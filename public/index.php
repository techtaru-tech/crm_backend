<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Shared-Hosting Path Auto-Detection
|--------------------------------------------------------------------------
| Standard install: this file lives inside a "public/" subdirectory and
| the Laravel app root is one level up (__DIR__.'/..')
|
| Flat install (e.g. everything uploaded to public_html/ directly): this
| file lives alongside app/, vendor/, bootstrap/, etc. In that case
| __DIR__.'/../vendor/autoload.php' won't exist, so we fall back to
| __DIR__ as the app root.
|
*/
$realDir = realpath(__DIR__) ?: __DIR__;
if (file_exists($realDir . '/../vendor/autoload.php')) {
    $appBase = $realDir . '/..';   // standard: public/ is a subfolder
} else {
    $appBase = $realDir;           // flat: index.php alongside app files
}

/*
|--------------------------------------------------------------------------
| Installation Guard
|--------------------------------------------------------------------------
| If storage/installed.lock does not exist, the application has not been
| installed yet. Redirect to install.php — a standalone, pure-PHP
| installer that runs without Laravel.
|
*/
if (!file_exists($appBase . '/storage/installed.lock')) {
    // Check if .env + DB exist (manual deployment / migration) before redirecting
    $envPath = $appBase . '/.env';
    $autoDetected = false;
    if (file_exists($envPath)) {
        $envRaw = file_get_contents($envPath);
        $dbv = [];
        foreach (['DB_HOST','DB_PORT','DB_DATABASE','DB_USERNAME','DB_PASSWORD'] as $k) {
            if (preg_match('/^' . $k . '=(.*)$/m', $envRaw, $m)) {
                $dbv[$k] = trim($m[1], "\" '\t");
            }
        }
        if (!empty($dbv['DB_DATABASE']) && !empty($dbv['DB_HOST'])) {
            try {
                $dsn = 'mysql:host=' . ($dbv['DB_HOST'] ?? '127.0.0.1')
                     . ';port=' . ($dbv['DB_PORT'] ?? '3306')
                     . ';dbname=' . $dbv['DB_DATABASE'];
                $pdo = new PDO($dsn, $dbv['DB_USERNAME'] ?? '', $dbv['DB_PASSWORD'] ?? '', [
                    PDO::ATTR_TIMEOUT => 3, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);
                if ($pdo->query("SHOW TABLES LIKE 'users'")->rowCount() > 0) {
                    // Clear stale bootstrap cache that may reference removed dev packages
                    foreach (glob($appBase . '/bootstrap/cache/*.php') as $f) @unlink($f);
                    @file_put_contents($appBase . '/storage/installed.lock', date('Y-m-d H:i:s') . ' (auto-detected)');
                    $autoDetected = true;
                }
            } catch (Throwable $e) { /* DB not reachable — redirect to installer */ }
        }
    }
    if (!$autoDetected) {
        // Some web stacks (notably cpriego/valet-linux, but also any
        // server config with a Laravel-aware front-controller pattern)
        // route EVERY .php request through index.php instead of serving
        // the requested file directly. In that mode, redirecting
        // /install.php -> install.php just bounces back through here
        // and creates an infinite 302 loop.
        //
        // If the incoming request IS already /install.php, run the
        // installer inline rather than redirect - breaks the loop and
        // costs nothing on Apache/shared-hosting setups (where Apache
        // serves install.php directly and never reaches this branch).
        $reqUri = $_SERVER['REQUEST_URI'] ?? '';
        if (preg_match('#(^|/)install\.php(\?|$)#', $reqUri)
            && file_exists(__DIR__ . '/install.php')) {
            require __DIR__ . '/install.php';
            exit;
        }
        header('Location: install.php');
        exit;
    }
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $appBase . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
if (!file_exists($appBase . '/vendor/autoload.php')) {
    http_response_code(500);
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
       . '<title>LeadHub — Incomplete Upload</title>'
       . '<style>body{font-family:system-ui,-apple-system,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;background:#f8fafc;color:#0f172a}'
       . '.box{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:2.5rem;max-width:480px;text-align:center;box-shadow:0 4px 12px rgba(0,0,0,.06)}'
       . '.icon{width:48px;height:48px;margin:0 auto 1rem;background:#fef2f2;border-radius:50%;display:flex;align-items:center;justify-content:center}'
       . '.icon svg{width:24px;height:24px;stroke:#dc2626;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}'
       . 'h1{font-size:1.15rem;font-weight:700;margin:0 0 .5rem}p{color:#64748b;font-size:.85rem;line-height:1.6;margin:.5rem 0}'
       . 'strong{color:#0f172a}</style></head>'
       . '<body><div class="box">'
       . '<div class="icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>'
       . '<h1>Incomplete Upload</h1>'
       . '<p>The <strong>vendor/</strong> folder is missing. It looks like the upload was incomplete or the zip was not fully extracted.</p>'
       . '<p>Please re-upload <strong>all files</strong> from the LeadHub distribution zip, making sure the <strong>vendor/</strong> folder is included.</p>'
       . '</div></body></html>';
    exit;
}
require $appBase . '/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $appBase . '/bootstrap/app.php';

$app->handleRequest(Request::capture());
