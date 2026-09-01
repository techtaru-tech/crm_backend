#!/usr/bin/env bash
#
# Techtaru CRM — stable public dev tunnel.
#
#   ./devtunnel.sh start    # boot app + tunnel (idempotent)
#   ./devtunnel.sh stop
#   ./devtunnel.sh status
#   ./devtunnel.sh logs
#
# The public URL is fixed for the lifetime of the tunnel object named
# TUNNEL_ID below — restarting the app, the tunnel, or the Mac does NOT
# change it.  It only changes if the tunnel is deleted and recreated
# (dev tunnels are dropped after 30 days with zero traffic; every use
# resets that clock).
#
set -euo pipefail

cd "$(dirname "$0")"

TUNNEL_ID="techtaru-crm"
PORT=8010
PUBLIC_URL="https://v3r4qj1k-8010.asse.devtunnels.ms"

RUN_DIR="storage/app/devtunnel"
mkdir -p "$RUN_DIR"
SERVE_LOG="$RUN_DIR/serve.log"
TUNNEL_LOG="$RUN_DIR/tunnel.log"

serve_running()  { pgrep -f "artisan serve .*--port=$PORT" >/dev/null 2>&1; }
tunnel_running() { pgrep -f "devtunnel host $TUNNEL_ID" >/dev/null 2>&1; }

start() {
    # The tunnel object must exist; recreate it only if it was reaped.
    if ! devtunnel show "$TUNNEL_ID" >/dev/null 2>&1; then
        echo "!! Tunnel '$TUNNEL_ID' no longer exists — recreating."
        echo "!! NOTE: a recreated tunnel gets a NEW public hostname."
        devtunnel create "$TUNNEL_ID" --allow-anonymous \
            --description "Techtaru CRM - stable local dev tunnel"
        devtunnel port create "$TUNNEL_ID" -p "$PORT" --protocol http
    fi

    if serve_running; then
        echo "app    : already running on :$PORT"
    else
        nohup php artisan serve --host=127.0.0.1 --port="$PORT" >"$SERVE_LOG" 2>&1 &
        echo "app    : started on :$PORT"
    fi

    if tunnel_running; then
        echo "tunnel : already hosting $TUNNEL_ID"
    else
        nohup devtunnel host "$TUNNEL_ID" >"$TUNNEL_LOG" 2>&1 &
        echo "tunnel : hosting $TUNNEL_ID"
    fi

    sleep 4
    echo
    echo "Public URL: $PUBLIC_URL"
    echo "Admin     : $PUBLIC_URL/admin"
}

stop() {
    pkill -f "devtunnel host $TUNNEL_ID"      2>/dev/null && echo "tunnel : stopped" || echo "tunnel : not running"
    pkill -f "artisan serve .*--port=$PORT"   2>/dev/null && echo "app    : stopped" || echo "app    : not running"
}

status() {
    serve_running  && echo "app    : running on :$PORT" || echo "app    : stopped"
    tunnel_running && echo "tunnel : hosting $TUNNEL_ID" || echo "tunnel : stopped"
    echo "url    : $PUBLIC_URL"
    devtunnel show "$TUNNEL_ID" 2>/dev/null | grep -E "Tunnel ID|Expiration" || true
}

case "${1:-start}" in
    start)  start ;;
    stop)   stop ;;
    restart) stop; sleep 2; start ;;
    status) status ;;
    logs)   tail -f "$TUNNEL_LOG" "$SERVE_LOG" ;;
    *)      echo "usage: $0 {start|stop|restart|status|logs}"; exit 1 ;;
esac
