#!/usr/bin/env bash
# Extract ONE tenant's data out of the multi-tenant CRM database.
#
#   ./extract-shreeram.sh <source-db> <tenant-id> <output.sql>
#
# Produces a single .sql file holding the full schema plus only that
# tenant's rows, ready to load into a fresh single-tenant database.
# Read-only against the source — it never writes to it.
set -euo pipefail

SRC="${1:?source database}"
TID="${2:?tenant id}"
OUT="${3:?output file}"
MY="mysql -u root -h 127.0.0.1"
DUMP="mysqldump -u root -h 127.0.0.1 --single-transaction --no-tablespaces --skip-add-locks --skip-comments"

# Rows that belong to nobody and regenerate themselves.  Carrying a
# stale session or a queued job into a fresh install causes confusing
# failures on day one.
EPHEMERAL="cache cache_locks jobs job_batches failed_jobs sessions
           password_reset_tokens login_attempts processed_billing_events"

# Reference data every install needs in full — roles and permissions
# are NOT tenant-scoped, so filtering them would leave the tenant with
# users whose roles point at nothing.
GLOBAL="migrations permissions roles role_has_permissions plans locales
        static_pages coupons shared_templates"

# Child tables with no tenant_id of their own: each is reachable only
# through a parent that does have one.  Filtering these by their parent
# is the step that, skipped, leaves dangling foreign keys in the new
# database — the failure is silent until someone opens the record.
declare -a CHILD=(
  "automation_runs|automation_id IN (SELECT id FROM automations WHERE tenant_id=$TID)"
  "automation_steps|automation_id IN (SELECT id FROM automations WHERE tenant_id=$TID)"
  "chat_messages|chat_conversation_id IN (SELECT id FROM chat_conversations WHERE tenant_id=$TID)"
  "form_fields|form_id IN (SELECT id FROM forms WHERE tenant_id=$TID)"
  "form_submission_values|form_submission_id IN (SELECT id FROM form_submissions WHERE tenant_id=$TID)"
  "integration_sync_logs|integration_id IN (SELECT id FROM integrations WHERE tenant_id=$TID)"
  "landing_page_sections|landing_page_id IN (SELECT id FROM landing_pages WHERE tenant_id=$TID)"
  "lead_tag|lead_id IN (SELECT id FROM leads WHERE tenant_id=$TID)"
  "portal_access_tokens|lead_id IN (SELECT id FROM leads WHERE tenant_id=$TID)"
  "portal_sessions|lead_id IN (SELECT id FROM leads WHERE tenant_id=$TID)"
  "feature_request_votes|user_id IN (SELECT id FROM users WHERE tenant_id=$TID)"
  "meeting_availability_rules|user_id IN (SELECT id FROM users WHERE tenant_id=$TID)"
  "notification_digests|user_id IN (SELECT id FROM users WHERE tenant_id=$TID)"
  "notification_preferences|user_id IN (SELECT id FROM users WHERE tenant_id=$TID)"
  "push_subscriptions|user_id IN (SELECT id FROM users WHERE tenant_id=$TID)"
  "saved_filters|user_id IN (SELECT id FROM users WHERE tenant_id=$TID)"
  "user_dashboard_preferences|user_id IN (SELECT id FROM users WHERE tenant_id=$TID)"
  "user_sessions|user_id IN (SELECT id FROM users WHERE tenant_id=$TID)"
  # Spatie stores role assignments polymorphically, so these filter on
  # the morph pair rather than a foreign key.
  "model_has_roles|model_id IN (SELECT id FROM users WHERE tenant_id=$TID)"
  "model_has_permissions|model_id IN (SELECT id FROM users WHERE tenant_id=$TID)"
  "notifications|notifiable_id IN (SELECT id FROM users WHERE tenant_id=$TID)"
  "affiliate_referrals|referrer_tenant_id=$TID OR referred_tenant_id=$TID"
  "tenants|id=$TID"
)

ALL=$($MY -N -e "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA='$SRC' AND TABLE_TYPE='BASE TABLE' ORDER BY TABLE_NAME")

# The lists below are written across several lines for readability, so
# normalise whitespace before matching — otherwise a name sitting at
# the end of a line is followed by a newline, never a space, and is
# silently reported as unclassified.
has() { echo " $2 " | tr -s "[:space:]" " " | grep -q " $1 "; }
# Trailing `return 0` matters: without it the function exits non-zero
# when a table has no child rule, and `set -e` kills the run on the
# very first unmatched table.
child_where() { for e in "${CHILD[@]}"; do [ "${e%%|*}" = "$1" ] && { echo "${e#*|}"; return 0; }; done; return 0; }

echo "-- Single-tenant extract: tenant $TID from $SRC" > "$OUT"
echo "-- Generated $(date)" >> "$OUT"
echo "SET FOREIGN_KEY_CHECKS=0;" >> "$OUT"

echo "== schema =="
$DUMP --no-data "$SRC" >> "$OUT"

echo "== data =="
for T in $ALL; do
  if has "$T" "$EPHEMERAL"; then
    printf "  %-32s skipped (ephemeral)\n" "$T"; continue
  fi
  if has "$T" "$GLOBAL"; then
    $DUMP --no-create-info "$SRC" "$T" >> "$OUT"
    printf "  %-32s ALL (global)\n" "$T"; continue
  fi
  W=$(child_where "$T")
  if [ -z "$W" ]; then
    if $MY -N -e "SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='$SRC' AND TABLE_NAME='$T' AND COLUMN_NAME='tenant_id'" | grep -q 1; then
      W="tenant_id=$TID"
    else
      printf "  %-32s !! UNCLASSIFIED — check manually\n" "$T"; continue
    fi
  fi
  N=$($MY -N -e "SELECT COUNT(*) FROM \`$SRC\`.\`$T\` WHERE $W" 2>/dev/null || echo 0)
  $DUMP --no-create-info --where="$W" "$SRC" "$T" >> "$OUT"
  printf "  %-32s %s rows\n" "$T" "$N"
done

echo "SET FOREIGN_KEY_CHECKS=1;" >> "$OUT"
echo
echo "Written: $OUT  ($(du -h "$OUT" | cut -f1))"
