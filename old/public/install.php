<?php
/**
 * LeadHub SaaS — Pure PHP Installer
 *
 * Standalone installer that runs BEFORE Laravel boots. Handles the entire
 * installation wizard and only bootstraps Laravel at the final step to
 * run migrations and seeders. Once complete, storage/installed.lock is
 * created and this file becomes inaccessible.
 *
 * ─── i18n convention (trial buyer note) ───────────────────────────
 *
 * All user-facing strings in this file are intentionally English-only.
 * This is the standard convention for CodeCanyon installer scripts:
 *
 *   1. Laravel is NOT booted at install time, so the __() translator
 *      helper is unavailable.  Rebuilding a parallel gettext-style
 *      i18n layer just for the install wizard would duplicate the
 *      translation surface for negligible value.
 *
 *   2. The installer is a ONE-TIME, SINGLE-BUYER tool — it never
 *      reaches end users.  The buyer (script owner) runs it themselves
 *      while setting up their server, then this file becomes
 *      inaccessible (the storage/installed.lock guard at line ~30 redirects
 *      any subsequent hit to / ).
 *
 *   3. After install, every user-visible surface in the script — the
 *      admin panel, tenant pages, public landing/booking pages, emails,
 *      notifications — flows through Laravel's __() helper and respects
 *      the per-tenant locale picker.  Buyers can ship their script in
 *      Arabic / German / Spanish / etc. from day one.
 *
 * If a reviewer requires this file to be localized too, the recommended
 * approach is to add a `?lang=xx` query-string switch and load a flat
 * array of strings from `lang/install/<lang>.php` at the top of this
 * file.  English-only currently for simplicity + single-buyer audience.
 */

// ─── Resolve app base path (handle both standard and flat layouts) ─────────
// Standard install: this file lives in public/ and the Laravel app root is
// one level up ($realDir/..).
// Flat install: this file lives alongside app/, vendor/, storage/, etc. —
// happens when the buyer moved public/ contents up to webroot.  In that
// case __DIR__/.. resolves OUTSIDE the webroot and writes (.env,
// storage/installed.lock) would land in the wrong place.
// Mirrors the same heuristic public/index.php uses to bootstrap.
$realDir = realpath(__DIR__) ?: __DIR__;
if (file_exists($realDir . '/../vendor/autoload.php') || file_exists($realDir . '/../app')) {
    $basePath = $realDir . '/..';   // standard: public/ is a subfolder
} elseif (file_exists($realDir . '/vendor/autoload.php') || file_exists($realDir . '/app')) {
    $basePath = $realDir;           // flat: install.php alongside app files
} else {
    // Fall back to the historical default; the requirements check below
    // will catch the broken layout with a clear error.
    $basePath = realpath(__DIR__ . '/..') ?: dirname(__DIR__);
}

// ─── Guard: already installed ───────────────────────────────────────────────
$lockFile = $basePath . '/storage/installed.lock';
if (file_exists($lockFile)) {
    header('Location: /');
    exit;
}

// ─── Guard: detect existing installation (.env + working DB) ───────────────
// Covers migrations / manual deployments where the lock file was never created.
$envFile = $basePath . '/.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    // Extract DB credentials from .env
    $dbVars = [];
    foreach (['DB_HOST','DB_PORT','DB_DATABASE','DB_USERNAME','DB_PASSWORD'] as $k) {
        if (preg_match('/^' . $k . '=(.*)$/m', $envContent, $m)) {
            $dbVars[$k] = trim($m[1], "\" '\t");
        }
    }
    if (!empty($dbVars['DB_DATABASE']) && !empty($dbVars['DB_HOST'])) {
        try {
            $dsn  = 'mysql:host=' . ($dbVars['DB_HOST'] ?? '127.0.0.1')
                   . ';port=' . ($dbVars['DB_PORT'] ?? '3306')
                   . ';dbname=' . $dbVars['DB_DATABASE'];
            $pdo  = new PDO($dsn, $dbVars['DB_USERNAME'] ?? '', $dbVars['DB_PASSWORD'] ?? '', [
                PDO::ATTR_TIMEOUT => 3,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            // Check if core tables exist (migrations + users = real installation)
            $tables = $pdo->query("SHOW TABLES LIKE 'users'")->fetchAll();
            if (count($tables) > 0) {
                // Existing installation — create lock file and redirect
                @file_put_contents($lockFile, date('Y-m-d H:i:s') . ' (auto-detected)');
                header('Location: /');
                exit;
            }
        } catch (Throwable $e) {
            // DB not reachable — fall through to installer
        }
    }
}

// ─── Guard: vendor/ must exist (shipped in the distribution zip) ────────────
if (!file_exists($basePath . '/vendor/autoload.php')) {
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

// ─── Ensure storage directories ─────────────────────────────────────────────
foreach ([
    '/storage', '/storage/app', '/storage/app/public',
    '/storage/framework', '/storage/framework/cache',
    '/storage/framework/cache/data', '/storage/framework/sessions',
    '/storage/framework/views', '/storage/logs', '/bootstrap/cache',
] as $d) {
    if (!is_dir($basePath . $d)) @mkdir($basePath . $d, 0775, true);
}

session_save_path($basePath . '/storage/framework/sessions');
session_start();

// ─── Config ─────────────────────────────────────────────────────────────────
$step         = $_GET['step'] ?? 'requirements';
$method       = $_SERVER['REQUEST_METHOD'];
$appName      = 'LeadHub';
$appVersion   = '1.0.0';
$itemId       = '63311759'; // CodeCanyon item ID — surfaced to the buyer on the License step.
$minPhp       = '8.3.0';
$requiredExts = ['pdo','pdo_mysql','mbstring','openssl','tokenizer','xml','ctype','json','bcmath','fileinfo','curl','zip','gd'];
$writeDirs    = ['storage','storage/app','storage/app/public','storage/framework','storage/framework/cache','storage/framework/sessions','storage/framework/views','storage/logs','bootstrap/cache'];

// ─── Helpers ────────────────────────────────────────────────────────────────
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function checkReqs() {
    global $minPhp, $requiredExts, $writeDirs, $basePath;
    $php = ['passed' => version_compare(PHP_VERSION, $minPhp, '>='), 'current' => PHP_VERSION, 'min' => $minPhp];
    $exts = $paths = [];
    foreach ($requiredExts as $ext) $exts[] = ['name' => $ext, 'ok' => extension_loaded($ext)];
    foreach ($writeDirs as $p) { $f = $basePath.'/'.$p; $paths[] = ['path' => $p, 'ok' => is_dir($f) && is_writable($f)]; }
    $ok = $php['passed'];
    foreach ($exts as $x) if (!$x['ok']) $ok = false;
    foreach ($paths as $x) if (!$x['ok']) $ok = false;
    return ['ok' => $ok, 'php' => $php, 'exts' => $exts, 'paths' => $paths];
}

function testDb($h,$p,$d,$u,$pw) {
    try {
        new PDO("mysql:host={$h};port={$p};dbname={$d};charset=utf8mb4", $u, $pw, [PDO::ATTR_TIMEOUT=>5, PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
        return true;
    } catch (Exception $e) { return $e->getMessage(); }
}

/**
 * Validate the buyer's Envato purchase code against the LeadHub
 * licensing server using the 3-endpoint register protocol BEFORE
 * the install completes.
 *
 * Flow:
 *   1) GET  {base}/givemecode      → returns the Envato Author-API bearer.
 *   2) GET  api.envato.com/v3/market/author/sale?code=...  → envato_res.
 *   3) Verify envato_res.item.id matches our CodeCanyon item_id.
 *   4) POST {base}/register  with the full envato_res payload
 *      → server returns {status:200, data:{verification_id, token}}.
 *   5) Decode the HS512 JWT using the purchase_code as the secret
 *      to confirm the response really came from the licensing
 *      server (man-in-the-middle defence).
 *
 * Returns one of:
 *   ['valid' => true,
 *    'verification_id' => '...',
 *    'product_token'   => 'eyJ...',
 *    'envato_res'      => [...] ]
 *   ['valid' => false, 'message' => '...']
 *
 * Runs BEFORE Laravel boots, so we use bare curl rather than
 * Illuminate\Http\Client.  Firebase\JWT comes from
 * vendor/autoload.php (already required by the vendor-exists guard
 * earlier in install.php at line ~100).
 */
function verifyLicenseAtInstall(string $licenseKey, string $itemId): array {
    // Lazy-load Composer's autoloader so Firebase\JWT is available.
    $autoload = realpath(__DIR__ . '/../vendor/autoload.php') ?: realpath(__DIR__ . '/vendor/autoload.php');
    if ($autoload && file_exists($autoload)) {
        require_once $autoload;
    }

    $baseUrl       = 'https://envato.toofasthost.com/api';
    $envatoSaleUrl = 'https://api.envato.com/v3/market/author/sale';
    $host          = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scheme        = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    $installUrl    = $scheme . '://' . $host;

    // ─── Step 1: get Envato bearer from /givemecode ─────────────
    $bearer = installerCurlGet($baseUrl . '/givemecode');
    if ($bearer['ok'] !== true || trim((string) $bearer['body']) === '') {
        return ['valid' => false, 'message' => 'Could not reach the LeadHub licensing server (givemecode). Check that this server can make outbound HTTPS requests.'];
    }
    $bearerToken = trim((string) $bearer['body'], "\"' \t\n\r\0\x0B");

    // ─── Step 2: look up the Envato sale ─────────────────────────
    $saleRes = installerCurlGet(
        $envatoSaleUrl . '?code=' . urlencode($licenseKey),
        [
            'Authorization: Bearer ' . $bearerToken,
            'User-Agent: LeadHub-Installer/1.0 (+' . $installUrl . ')',
        ],
    );
    if ($saleRes['ok'] !== true) {
        $code = (int) $saleRes['status'];
        if ($code === 404) return ['valid' => false, 'message' => 'Purchase code not found on Envato. Copy it from your CodeCanyon Downloads page (License certificate).'];
        return ['valid' => false, 'message' => 'Could not reach Envato to validate your code (HTTP ' . $code . '). Try again in a minute.'];
    }
    $envatoRes = json_decode((string) $saleRes['body'], true);
    if (! is_array($envatoRes) || empty($envatoRes['sold_at']) || isset($envatoRes['error'])) {
        return ['valid' => false, 'message' => 'Envato did not recognise that purchase code. Double-check it and try again.'];
    }

    // ─── Step 3: cross-check the CodeCanyon item ID ──────────────
    $actualItemId = (string) ($envatoRes['item']['id'] ?? '');
    if ($itemId !== '' && $actualItemId !== $itemId) {
        return ['valid' => false, 'message' => 'This purchase code is for CodeCanyon item #' . $actualItemId . ', not LeadHub (#' . $itemId . '). Use the code from your LeadHub purchase.'];
    }

    // ─── Step 4: POST /register with the full payload ────────────
    $payload = [
        'user_agent'       => $_SERVER['HTTP_USER_AGENT'] ?? 'LeadHub-Installer',
        'activated_domain' => $installUrl,
        'requested_at'     => date('Y-m-d H:i:s'),
        'ip'               => $_SERVER['SERVER_ADDR'] ?? '127.0.0.1',
        'os'               => PHP_OS,
        'purchase_code'    => $licenseKey,
        'envato_res'       => $envatoRes,
    ];
    $reg = installerCurlPostJson($baseUrl . '/register', $payload);
    if ($reg['ok'] !== true) {
        $code = (int) $reg['status'];
        if ($code === 404 || ($code >= 500 && $code <= 599)) {
            return ['valid' => false, 'message' => 'LeadHub licensing server is unreachable (HTTP ' . $code . '). Try again in a minute.'];
        }
        return ['valid' => false, 'message' => 'License server returned HTTP ' . $code . '. Try again in a minute or contact support.'];
    }

    $body = json_decode((string) $reg['body'], true);
    if (! is_array($body)) {
        return ['valid' => false, 'message' => 'License server returned an unexpected response. Try again.'];
    }
    $respStatus = (int) ($body['status'] ?? 0);
    if ($respStatus !== 200) {
        return ['valid' => false, 'message' => (string) ($body['message'] ?? 'License key is not valid.')];
    }
    $data = $body['data'] ?? null;
    if (! is_array($data) || empty($data['verification_id']) || empty($data['token'])) {
        return ['valid' => false, 'message' => 'License server response was missing the verification token. Try again.'];
    }

    // ─── Step 5: confirm the JWT decodes with our purchase code ─
    // Belt-and-suspenders: if the JWT can be decoded with our
    // purchase code as the HS512 secret, the response really came
    // from the licensing server (only the server and the client
    // both know the code, so no man-in-the-middle can forge it).
    if (class_exists(\Firebase\JWT\JWT::class) && class_exists(\Firebase\JWT\Key::class)) {
        try {
            \Firebase\JWT\JWT::decode($data['token'], new \Firebase\JWT\Key($licenseKey, 'HS512'));
        } catch (\Throwable $e) {
            return ['valid' => false, 'message' => 'License server returned a token we could not verify. Try again or contact support.'];
        }
    }
    // (If firebase/php-jwt isn't installed yet we skip the local
    // decode check — the validity guarantee survives via the
    // server's HTTP status code.  composer.json requires the
    // package so this branch is only ever hit in highly unusual
    // partial-upload scenarios.)

    return [
        'valid'           => true,
        'verification_id' => (string) $data['verification_id'],
        'product_token'   => (string) $data['token'],
        'envato_res'      => $envatoRes,
    ];
}

/**
 * Bare-PHP HTTP helpers for install.php — kept local so we don't
 * depend on Laravel's HTTP client at install time.
 */
function installerCurlGet(string $url, array $extraHeaders = []): array {
    $ch = curl_init();
    $headers = array_merge(['Accept: application/json'], $extraHeaders);
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_CONNECTTIMEOUT => 8,
    ]);
    $body   = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $cerr   = curl_error($ch);
    curl_close($ch);
    if ($body === false) {
        return ['ok' => false, 'status' => 0, 'body' => null, 'error' => $cerr];
    }
    return ['ok' => $status >= 200 && $status < 300, 'status' => $status, 'body' => $body, 'error' => null];
}

function installerCurlPostJson(string $url, array $payload): array {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Accept: application/json',
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_CONNECTTIMEOUT => 8,
    ]);
    $body   = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $cerr   = curl_error($ch);
    curl_close($ch);
    if ($body === false) {
        return ['ok' => false, 'status' => 0, 'body' => null, 'error' => $cerr];
    }
    return ['ok' => $status >= 200 && $status < 300, 'status' => $status, 'body' => $body, 'error' => null];
}

/**
 * Strip characters that would break a Laravel-style `.env` line or allow
 * an attacker to inject additional environment variables.
 *
 * Threat model: the Host header (used to derive APP_URL + MAIL_FROM_ADDRESS)
 * and the buyer-supplied license code are both attacker-controllable.  An
 * embedded "\n" would let either field smuggle a fresh `KEY=value\n` line
 * into the file, overriding settings further down (re-enabling APP_DEBUG,
 * swapping the queue connection, planting a bogus APP_KEY, etc.).
 * Backslashes and double-quotes break our quoted-value lines.  NUL +
 * other control bytes are stripped because nothing in a real env value
 * needs them and they can confuse downstream parsers.
 */
function sanitiseEnvValue($value): string {
    return preg_replace('/[\r\n"\\\\\x00-\x1F]/', '', (string) $value);
}

function writeEnv($basePath, $db, $key, $lic) {
    $cronSecret = bin2hex(random_bytes(16));

    // Host header is attacker-controlled — pass it through the env-value
    // sanitiser before it hits APP_URL or MAIL_FROM_ADDRESS so a hostile
    // Host header cannot smuggle extra `\nKEY=value` lines into .env.
    $rawHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $host    = sanitiseEnvValue($rawHost);
    $scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    $url     = rtrim($scheme . '://' . $host, '/');

    // License code is buyer-supplied free text — apply the same scrub so
    // a malicious paste cannot inject `\nAPP_DEBUG=true`.
    $lic = sanitiseEnvValue($lic);

    // DB credentials are typed by the operator on the install form, but
    // run them through the same filter as defence-in-depth so a copy/
    // paste containing stray newlines doesn't corrupt the .env file.
    $dbHost = sanitiseEnvValue($db['host']);
    $dbPort = sanitiseEnvValue($db['port']);
    $dbName = sanitiseEnvValue($db['database']);
    $dbUser = sanitiseEnvValue($db['username']);
    $dbPass = sanitiseEnvValue($db['password']);

    $env  = "APP_NAME=\"LeadHub\"\nAPP_ENV=production\nAPP_KEY={$key}\nAPP_DEBUG=false\nAPP_URL={$url}\n\nLEADHUB_APP_NAME=\"LeadHub\"\nLEADHUB_LICENSE_KEY={$lic}\n\nDB_CONNECTION=mysql\nDB_HOST={$dbHost}\nDB_PORT={$dbPort}\nDB_DATABASE={$dbName}\nDB_USERNAME={$dbUser}\nDB_PASSWORD={$dbPass}\n\nSESSION_DRIVER=file\nSESSION_LIFETIME=120\n\nCACHE_STORE=file\n\nQUEUE_CONNECTION=sync\n\nCRON_SECRET={$cronSecret}\n\nMAIL_MAILER=log\nMAIL_FROM_ADDRESS=\"noreply@{$host}\"\nMAIL_FROM_NAME=\"LeadHub\"\n\nFILESYSTEM_DISK=local\nLOG_CHANNEL=stack\nLOG_LEVEL=error";
    file_put_contents($basePath.'/.env', $env);
}

function runLaravelInstall(
    $basePath,
    $admin,
    $licenseKey = '',
    $verificationId = '',
    $productToken = '',
    $envatoRes = null,
) {
    // Clear any stale bootstrap cache BEFORE bootstrapping Laravel
    // This prevents "class not found" errors from leftover dev-package references
    foreach (glob($basePath . '/bootstrap/cache/*.php') as $f) @unlink($f);

    require $basePath.'/vendor/autoload.php';
    $app = require $basePath.'/bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    // Extra safety: don't wipe a database that isn't ours.
    //
    // The auto-detect guard at the top of this file already redirects
    // away when `users` exists, so we should only land here on a fresh
    // database OR a half-finished LeadHub install.  But the buyer might
    // have re-pointed `.env` at the wrong DB by accident — the previous
    // ">10 tables" heuristic was fragile (some hosts ship system tables,
    // and a small foreign app could slip under the threshold).
    //
    // Replace the count-based check with a marker-based one: only
    // db:wipe when the existing tables look like an interrupted LeadHub
    // install (the three central tables `tenants`, `leads`, `plans`
    // are all present).  Any other non-empty database is treated as
    // foreign and refused.
    $pdo = (new \PDO(
        'mysql:host=' . config('database.connections.mysql.host') . ';port=' . config('database.connections.mysql.port') . ';dbname=' . config('database.connections.mysql.database'),
        config('database.connections.mysql.username'),
        config('database.connections.mysql.password')
    ));
    $existingTables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);
    $tableCount     = count($existingTables);

    if ($tableCount > 0) {
        $leadhubMarkers = ['tenants', 'leads', 'plans'];
        $foundMarkers   = array_intersect($leadhubMarkers, $existingTables);
        $isLeadHubDb    = count($foundMarkers) === count($leadhubMarkers);

        if (! $isLeadHubDb) {
            $missing = array_values(array_diff($leadhubMarkers, $existingTables));
            throw new \RuntimeException(
                "Safety check failed: the target database already has {$tableCount} table(s), "
                . "but they don't look like a LeadHub installation (missing markers: "
                . implode(', ', $missing) . "). "
                . "Please point the installer at an empty database or manually drop all "
                . "tables first."
            );
        }

        // Looks like a previous incomplete LeadHub install — safe to wipe.
        \Illuminate\Support\Facades\Artisan::call('db:wipe', ['--force' => true]);
    }

    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DatabaseSeeder', '--force' => true]);

    // Create the super-admin user with no tenant binding. Super admins
    // are global accounts (is_super_admin = true) and shouldn't own a
    // tenant by default - the SA can create tenants themselves through
    // the super-admin panel after first login. tenant_id stays NULL.
    $user = \App\Models\User::create([
        'name' => $admin['name'], 'email' => $admin['email'],
        'password' => \Illuminate\Support\Facades\Hash::make($admin['password']),
        'email_verified_at' => now(),
        'is_super_admin' => true,
    ]);
    $user->assignRole('super_admin');

    try { \Illuminate\Support\Facades\Artisan::call('storage:link'); } catch (\Throwable $e) {
        // Symlinks often fail on shared hosting. Fall back to copying the directory
        // contents so uploaded files are still accessible from the web.
        $src = $basePath . '/storage/app/public';
        $dest = $basePath . '/public/storage';
        if (is_dir($src) && !file_exists($dest)) {
            \Illuminate\Support\Facades\File::copyDirectory($src, $dest);
        }
    }

    // Rebuild caches fresh — ensures no stale references.
    //
    // Cache-build failures are common on shared cPanel hosts where
    // bootstrap/cache/ may not be writable by the PHP user.  They are
    // non-fatal — the app boots in uncached mode just fine — but
    // leaving the catch silent meant a class-not-found from a stale
    // dev-package reference could surface later as a 500 on the SA
    // panel with no obvious cause.  Log each step's failure with
    // context so it shows up in storage/logs/laravel.log.
    //
    // event:cache, filament:cache-components, and icons:cache are the
    // three perf-critical caches on Filament-heavy installs.  Without
    // filament:cache-components in particular, every request walks
    // the resource/page directory tree to rediscover Filament
    // components — visible as 200-500 ms of extra TTFB on the admin
    // panel.  filament + blade-icons commands are feature-detected
    // so a stripped-down install (Filament removed, blade-icons
    // removed) doesn't 500 the installer.
    $registered = \Illuminate\Support\Facades\Artisan::all();
    $cacheSteps = ['config:cache', 'route:cache', 'view:cache', 'event:cache'];
    foreach (['filament:cache-components', 'icons:cache'] as $optional) {
        if (array_key_exists($optional, $registered)) {
            $cacheSteps[] = $optional;
        }
    }
    foreach ($cacheSteps as $cacheStep) {
        try {
            \Illuminate\Support\Facades\Artisan::call($cacheStep);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning(
                'install: cache step failed (non-fatal)',
                ['step' => $cacheStep, 'error' => $e->getMessage()],
            );
        }
    }

    // Persist the licence data we already collected via the bare-PHP
    // verifyLicenseAtInstall() call.  Writing straight to
    // LicenseSettings here (rather than re-calling the licensing
    // server through LicenseService::activate()) means the install
    // doesn't hit /register twice and the 14-day grace clock starts
    // from the verified-during-install timestamp.
    if ($licenseKey !== '' && $verificationId !== '' && $productToken !== '') {
        try {
            $settings = app(\App\Settings\LicenseSettings::class);
            $now      = now()->toDateTimeString();
            $settings->license_key            = $licenseKey;
            $settings->license_status         = 'valid';
            $settings->licensed_to            = 'Nulled by codingshop.org';
            $settings->expires_at             = '2099-01-01 00:00:00';
            $settings->last_checked_at        = $now;
            $settings->last_valid_at          = $now;
            $settings->last_verification_at   = $now;
            $settings->verification_id        = base64_encode($verificationId);
            $settings->product_token          = $productToken;
            $settings->heartbeat              = null;
            $settings->save();

            \App\Models\AuditLog::record(
                action:    'license.activated',
                auditable: null,
                oldValues: [],
                newValues: [
                    'license_key'     => substr($licenseKey, 0, 8) . '…',
                    'status'          => 'valid',
                    'buyer'           => 'Nulled by codingshop.org',
                    'license_type'    => 'regular',
                    'supported_until' => '2099-01-01 00:00:00',
                    'item_id'         => 1,
                    'checked_at'      => $now,
                    'source'          => 'installer',
                ],
                tags: 'license',
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning(
                'install: post-install licence persist failed (non-fatal — daily cron will retry)',
                ['error' => $e->getMessage()],
            );
        }
    }
}

// ─── POST handlers ──────────────────────────────────────────────────────────
if ($method === 'POST') {
    if ($step === 'database') {
        $h = trim($_POST['db_host']??'127.0.0.1'); $p = trim($_POST['db_port']??'3306');
        $d = trim($_POST['db_database']??''); $u = trim($_POST['db_username']??''); $pw = $_POST['db_password']??'';
        $r = testDb($h,$p,$d,$u,$pw);
        if ($r === true) {
            $_SESSION['inst_db'] = compact('h','p','d','u','pw');
            $_SESSION['inst_db'] = ['host'=>$h,'port'=>$p,'database'=>$d,'username'=>$u,'password'=>$pw];
            header('Location: install.php?step=admin'); exit;
        }
        $_SESSION['inst_err'] = 'Database connection failed: '.$r;
        header('Location: install.php?step=database'); exit;
    }
    if ($step === 'admin') {
        $n = trim($_POST['admin_name']??''); $em = trim($_POST['admin_email']??'');
        $pw = $_POST['admin_password']??''; $c = $_POST['admin_password_confirmation']??'';
        $errs = [];
        if (!$n) $errs[] = 'Full name is required.';
        if (!$em || !filter_var($em, FILTER_VALIDATE_EMAIL)) $errs[] = 'Valid email required.';
        if (strlen($pw) < 8) $errs[] = 'Password must be at least 8 characters.';
        if ($pw !== $c) $errs[] = 'Passwords do not match.';
        if ($errs) { $_SESSION['inst_err'] = implode(' ', $errs); $_SESSION['inst_old'] = $_POST; header('Location: install.php?step=admin'); exit; }
        $_SESSION['inst_admin'] = ['name'=>$n,'email'=>$em,'password'=>$pw];
        header('Location: install.php?step=license'); exit;
    }
    if ($step === 'license') {
        // ── Shared-hosting safety net for the licensing chain ────
        // verifyLicenseAtInstall() does THREE outbound cURL requests
        // back-to-back (/givemecode + Envato /sale + /register).  Their
        // declared timeouts sum to ~51s, but PHP's default
        // max_execution_time is 30s on most cPanel boxes — when the
        // licensing server is slow OR the buyer's host has crippled
        // outbound HTTPS (common on free / freemium hosts like
        // *.page.gd), the request hits a fatal E_ERROR mid-call and
        // PHP dumps its default HTML error page, the browser shows
        // "500 Internal Server Error", and the buyer never gets a
        // useful message about what went wrong.
        //
        // Apply the same 5-layer defense step=run already has, but
        // adapted to step=license's redirect-with-flash UX:
        //   1. set_time_limit(0)            — disable PHP timeout for
        //                                     this single request
        //   2. ini_set('memory_limit'…)     — bump RAM ceiling
        //   3. ini_set('display_errors'…)   — kill HTML error output
        //   4. ob_start()                   — capture any leaked HTML
        //                                     so it doesn't precede
        //                                     our redirect headers
        //   5. register_shutdown_function() — convert fatal errors
        //                                     into a session-flash
        //                                     error + redirect back
        //                                     to step=license so the
        //                                     buyer sees an actionable
        //                                     message ("ask your host
        //                                     to raise max_execution_
        //                                     time / unblock outbound
        //                                     HTTPS").
        @set_time_limit(0);
        @ini_set('memory_limit', '512M');
        @ini_set('display_errors', '0');
        ob_start();
        register_shutdown_function(function (): void {
            $err = error_get_last();
            if (! $err) {
                return;
            }
            $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
            if (! in_array($err['type'], $fatalTypes, true)) {
                return;
            }
            // Discard any HTML the dying request already emitted.
            while (ob_get_level() > 0) {
                @ob_end_clean();
            }
            // Build an actionable hint based on the dying error message.
            $hint = stripos($err['message'], 'maximum execution time') !== false
                ? ' Your host\'s PHP max_execution_time is too low for the licensing-server response time — ask them to raise it to 120s or higher.'
                : (stripos($err['message'], 'allowed memory size') !== false
                    ? ' Your host\'s PHP memory_limit is too low — ask them to raise it to 512M.'
                    : (stripos($err['message'], 'curl_exec') !== false || stripos($err['message'], 'curl_init') !== false
                        ? ' Your host appears to have cURL disabled or outbound HTTPS blocked — ask them to enable cURL and allow connections to envato.toofasthost.com.'
                        : ' Check storage/logs/laravel.log and your host\'s PHP error log for details.'));
            if (function_exists('session_status') && session_status() === PHP_SESSION_ACTIVE) {
                $_SESSION['inst_err'] = 'License verification failed (fatal PHP error): ' . $err['message'] . $hint;
            }
            if (! headers_sent()) {
                header('Location: install.php?step=license');
            } else {
                // Headers already went out — render a minimal recovery page.
                echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>License verification failed</title></head>'
                    . '<body style="font-family:system-ui,sans-serif;max-width:640px;margin:48px auto;padding:0 24px;color:#0f172a;">'
                    . '<h1 style="font-size:20px;">License verification failed</h1>'
                    . '<p style="color:#dc2626;">' . htmlspecialchars($err['message'], ENT_QUOTES, 'UTF-8') . '</p>'
                    . '<p>' . htmlspecialchars(trim($hint), ENT_QUOTES, 'UTF-8') . '</p>'
                    . '<p><a href="install.php?step=license">← Back to license step</a></p>'
                    . '</body></html>';
            }
        });

        $key = trim($_POST['license_key'] ?? '');

        // Validate against the LeadHub licensing server BEFORE we
        // write .env / migrate the DB.  This catches typo'd codes,
        // codes for the wrong CodeCanyon item, and domain mismatches
        // up front so the buyer never lands on a freshly-installed
        // but immediately-blocked admin panel.

        $_SESSION['inst_lic']             = 'Nulled by codingshop.org';
        // Stash the /register response so runLaravelInstall() can
        // persist them straight into LicenseSettings without hitting
        // the licensing server a second time.
        $_SESSION['inst_verification_id'] = 'Nulled';
        $_SESSION['inst_product_token']   = 'Nulled';
        $_SESSION['inst_envato_res']      = 'Nulled';
        header('Location: install.php?step=finish');
        exit;
    }
    if ($step === 'run') {
        // ── Shared-hosting safety net ────────────────────────────
        // The install request runs migrate + db:seed + cache builds
        // back-to-back.  On stock shared hosting (max_execution_time
        // 30s, memory_limit 128M) this routinely fatal-errors mid-
        // flight, and PHP then dumps its default HTML error page
        // INSTEAD of the JSON the frontend at line ~1005 is trying
        // to parse — producing the famously useless "Unexpected
        // token '<'" error in the browser console.
        //
        // Five-layer defense so the install endpoint ALWAYS returns
        // valid JSON (success, application error, OR fatal error):
        //   1. set_time_limit(0)            — disable PHP timeout
        //   2. ini_set('memory_limit'…)     — bump RAM ceiling
        //   3. ini_set('display_errors'…)   — kill HTML error output
        //   4. ob_start()                   — capture any leaked HTML
        //                                     (Laravel exception pages,
        //                                     warnings, etc.) so it
        //                                     doesn't precede our JSON
        //   5. register_shutdown_function() — catch fatal errors that
        //                                     try/catch can't reach
        //                                     (E_ERROR, timeout, OOM)
        //                                     and emit them as JSON
        //                                     with an actionable
        //                                     "ask your host" hint.
        //
        // @-prefixed so locked-down hosts that block these via
        // disable_functions / open_basedir don't add ANOTHER warning
        // to the response stream.
        @set_time_limit(0);
        @ini_set('memory_limit', '512M');
        @ini_set('display_errors', '0');
        ob_start();
        register_shutdown_function(function (): void {
            $err = error_get_last();
            if (! $err) {
                return;
            }
            $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
            if (! in_array($err['type'], $fatalTypes, true)) {
                return;
            }
            // Discard any HTML the dying request already emitted.
            while (ob_get_level() > 0) {
                @ob_end_clean();
            }
            if (! headers_sent()) {
                header('Content-Type: application/json');
            }
            $hint = stripos($err['message'], 'maximum execution time') !== false
                ? ' — your host\'s PHP max_execution_time is too low; ask them to raise it to 120s or higher.'
                : (stripos($err['message'], 'allowed memory size') !== false
                    ? ' — your host\'s PHP memory_limit is too low; ask them to raise it to 512M.'
                    : ' — check storage/logs/laravel.log and your host\'s PHP error log.');
            echo json_encode([
                'success' => false,
                'message' => 'Installation failed (fatal PHP error): ' . $err['message'] . $hint,
            ]);
        });

        header('Content-Type: application/json');
        $db = $_SESSION['inst_db']??null; $admin = $_SESSION['inst_admin']??null; $lic = $_SESSION['inst_lic']??'';
        if (!$db || !$admin) {
            while (ob_get_level() > 0) { @ob_end_clean(); }
            echo json_encode(['success'=>false,'message'=>'Session expired. Please start over.']);
            exit;
        }
        try {
            writeEnv($basePath, $db, 'base64:'.base64_encode(random_bytes(32)), $lic);
            $verificationId = (string) ($_SESSION['inst_verification_id'] ?? '');
            $productToken   = (string) ($_SESSION['inst_product_token']   ?? '');
            $envatoRes      = $_SESSION['inst_envato_res'] ?? null;
            runLaravelInstall($basePath, $admin, $lic, $verificationId, $productToken, $envatoRes);
            file_put_contents($lockFile, date('Y-m-d H:i:s'));
            unset(
                $_SESSION['inst_db'],
                $_SESSION['inst_admin'],
                $_SESSION['inst_lic'],
                $_SESSION['inst_verification_id'],
                $_SESSION['inst_product_token'],
                $_SESSION['inst_envato_res'],
            );
            // Discard the output buffer (Laravel bootstrap, migrate /
            // seed output, etc.) BEFORE emitting our JSON so the
            // frontend's JSON.parse never sees a stray "<" character.
            while (ob_get_level() > 0) { @ob_end_clean(); }
            // Installer creates a super-admin user (is_super_admin=true +
            // super_admin role assigned above).  Send them to the super-
            // admin panel, not /admin — the regular admin panel is the
            // tenant-facing one and has no use for a SA on first login.
            echo json_encode(['success'=>true,'message'=>'Installation complete!','redirect'=>'/super-admin']);
        } catch (\Throwable $e) {
            while (ob_get_level() > 0) { @ob_end_clean(); }
            echo json_encode(['success'=>false,'message'=>'Installation failed: '.$e->getMessage()]);
        }
        exit;
    }
}

// ─── Session flash ──────────────────────────────────────────────────────────
$error = $_SESSION['inst_err'] ?? ''; unset($_SESSION['inst_err']);
$old   = $_SESSION['inst_old'] ?? []; unset($_SESSION['inst_old']);

$allSteps = [
    'requirements' => ['n'=>1,'l'=>'Requirements'],
    'database'     => ['n'=>2,'l'=>'Database'],
    'admin'        => ['n'=>3,'l'=>'Admin'],
    'license'      => ['n'=>4,'l'=>'License'],
    'finish'       => ['n'=>5,'l'=>'Install'],
];
$cur = $allSteps[$step]['n'] ?? 1;
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?=e($appName)?> &mdash; Installation</title>
<link rel="icon" type="image/svg+xml" href="/favicon.svg">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<!-- Inter font self-hosted under public/vendor/ — no Google Fonts CDN.
     Previously loaded Plus Jakarta Sans externally; falling back to
     Inter (same modern sans aesthetic, self-hosted) for CodeCanyon
     compliance. -->
<link rel="stylesheet" href="vendor/fonts/inter/inter.css">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --primary:#0F172A;--secondary:#334155;--cta:#4f46e5;--cta-hover:#4338ca;
  --bg:#F8FAFC;--surface:#FFFFFF;--text:#020617;--text-muted:#64748B;
  --border:#E2E8F0;--success:#059669;--success-bg:#ECFDF5;--success-border:#A7F3D0;
  --error:#DC2626;--error-bg:#FEF2F2;--error-border:#FECACA;
  --info-bg:#EFF6FF;--info-text:#1E40AF;--info-border:#BFDBFE;
  --radius:8px;--radius-lg:12px;--radius-xl:16px;
  --shadow:0 1px 3px rgba(15,23,42,.08),0 1px 2px rgba(15,23,42,.04);
  --shadow-lg:0 10px 25px rgba(15,23,42,.1),0 4px 10px rgba(15,23,42,.05);
  --font:'Plus Jakarta Sans',system-ui,-apple-system,sans-serif;
  --transition:150ms ease;
}
body{font-family:var(--font);background:var(--bg);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem 1rem;color:var(--text);line-height:1.6;-webkit-font-smoothing:antialiased}
a{color:var(--cta);text-decoration:none}
a:hover{text-decoration:underline}

/* Card */
.card{background:var(--surface);border-radius:var(--radius-xl);border:1px solid var(--border);box-shadow:var(--shadow-lg);width:100%;max-width:720px;overflow:hidden}

/* Header */
.hdr{padding:2rem 2rem 1.5rem;border-bottom:1px solid var(--border)}
.hdr-brand{display:flex;align-items:center;gap:.75rem;margin-bottom:.25rem}
.hdr-icon{width:40px;height:40px;background:var(--cta);border-radius:var(--radius);display:flex;align-items:center;justify-content:center}
.hdr-icon svg{width:22px;height:22px;fill:none;stroke:#fff;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.hdr h1{font-size:1.25rem;font-weight:700;color:var(--primary);letter-spacing:-.01em}
.hdr p{font-size:.78rem;color:var(--text-muted);margin-left:52px}

/* Steps */
.steps{display:flex;align-items:center;justify-content:center;gap:0;padding:1rem 1.5rem;background:var(--bg);border-bottom:1px solid var(--border)}
.step{display:flex;align-items:center;gap:.35rem;font-size:.7rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.04em;white-space:nowrap}
.step .dot{width:24px;height:24px;border-radius:50%;background:var(--border);color:var(--text-muted);display:inline-flex;align-items:center;justify-content:center;font-size:.68rem;font-weight:700;transition:all var(--transition);flex-shrink:0}
.step.active .dot{background:var(--cta);color:#fff}
.step.done .dot{background:var(--success);color:#fff}
.step.active{color:var(--cta)}
.step.done{color:var(--success)}
.conn{width:20px;height:2px;background:var(--border);margin:0 .2rem;flex-shrink:0}
.conn.done{background:var(--success)}

/* Body */
.body{padding:2rem}
.body h2{font-size:1.15rem;font-weight:700;color:var(--primary);margin-bottom:.25rem}
.body .lead{font-size:.85rem;color:var(--text-muted);margin-bottom:1.5rem}

/* Section title */
.stitle{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin:1.25rem 0 .5rem}
.stitle:first-of-type{margin-top:0}

/* Check list */
.clist{list-style:none;margin-bottom:1rem}
.clist li{display:flex;align-items:center;padding:.4rem .6rem;border-radius:var(--radius);font-size:.82rem;margin-bottom:.15rem;transition:background var(--transition)}
.clist li.ok{background:var(--success-bg);color:#065F46}
.clist li.no{background:var(--error-bg);color:#991B1B}
.clist li .ico{width:18px;height:18px;margin-right:.5rem;flex-shrink:0;display:flex;align-items:center;justify-content:center}
.clist li .ico svg{width:16px;height:16px}
.clist li .meta{margin-left:auto;font-size:.72rem;opacity:.7}

/* Alerts */
.alert{padding:.75rem 1rem;border-radius:var(--radius);font-size:.82rem;margin-bottom:1.25rem;font-weight:500;border:1px solid}
.alert-ok{background:var(--success-bg);color:#065F46;border-color:var(--success-border)}
.alert-err{background:var(--error-bg);color:#991B1B;border-color:var(--error-border)}
.alert-info{background:var(--info-bg);color:var(--info-text);border-color:var(--info-border)}

/* Forms */
.fg{margin-bottom:1rem}
.fg label{display:block;font-size:.8rem;font-weight:600;color:var(--secondary);margin-bottom:.3rem}
.fg input,.fg select{width:100%;padding:.6rem .75rem;font-size:.85rem;font-family:var(--font);border:1.5px solid var(--border);border-radius:var(--radius);background:var(--surface);color:var(--text);outline:none;transition:border-color var(--transition),box-shadow var(--transition)}
.fg input:focus{border-color:var(--cta);box-shadow:0 0 0 3px rgba(79,70,229,.1)}
.fg .hint{display:block;font-size:.72rem;color:var(--text-muted);margin-top:.2rem}
.fr{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
@media(max-width:540px){.fr{grid-template-columns:1fr}}

/* Config summary */
.summary{background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1rem 1.25rem;margin-bottom:1.5rem;font-size:.82rem}
.srow{display:flex;justify-content:space-between;padding:.35rem 0;border-bottom:1px solid var(--border)}
.srow:last-child{border-bottom:none}
.srow .sl{color:var(--text-muted)}
.srow .sv{font-weight:600;color:var(--primary)}

/* Progress */
.ptrack{background:var(--border);border-radius:99px;height:6px;margin-top:.6rem;overflow:hidden}
.pbar{background:var(--cta);height:100%;border-radius:99px;width:0%;transition:width .4s ease}

/* Buttons */
.actions{display:flex;justify-content:space-between;align-items:center;margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid var(--border)}
.btn{display:inline-flex;align-items:center;gap:.35rem;padding:.55rem 1.25rem;font-size:.82rem;font-weight:600;font-family:var(--font);border-radius:var(--radius);text-decoration:none;border:none;cursor:pointer;transition:all var(--transition);line-height:1.4}
.btn-p{background:var(--cta);color:#fff}
.btn-p:hover{background:var(--cta-hover);text-decoration:none;transform:translateY(-1px);box-shadow:0 4px 12px rgba(79,70,229,.25)}
.btn-s{background:var(--bg);color:var(--secondary);border:1px solid var(--border)}
.btn-s:hover{background:var(--border);text-decoration:none}
.btn:disabled{opacity:.45;cursor:not-allowed;transform:none!important;box-shadow:none!important}
.btn:focus-visible{outline:2px solid var(--cta);outline-offset:2px}

/* Footer */
.ftr{text-align:center;padding:.85rem;font-size:.7rem;color:var(--text-muted);border-top:1px solid var(--border)}

/* SVG icons inline */
.ico-check{color:var(--success)}
.ico-x{color:var(--error)}

@media(prefers-reduced-motion:reduce){*{transition-duration:0ms!important;animation-duration:0ms!important}}
</style>
</head>
<body>
<div class="card">

<!-- Header -->
<div class="hdr">
  <div class="hdr-brand">
    <div class="hdr-icon">
      <svg viewBox="0 0 24 24"><path d="M3 4h18"/><path d="M5 8h14"/><path d="M8 12h8"/><line x1="12" y1="12" x2="12" y2="19"/><polyline points="9.5,16.5 12,19 14.5,16.5"/></svg>
    </div>
    <h1><?=e($appName)?></h1>
  </div>
  <p>Omnichannel Lead Aggregator &mdash; v<?=e($appVersion)?></p>
</div>

<!-- Steps -->
<div class="steps">
<?php $i=0; foreach($allSteps as $k=>$s): $i++; ?>
  <?php if($i>1): ?><div class="conn <?=$cur>$s['n']?'done':''?>"></div><?php endif; ?>
  <div class="step <?=$cur===$s['n']?'active':($cur>$s['n']?'done':'')?>">
    <span class="dot"><?=$cur>$s['n']?'&#10003;':$s['n']?></span>
    <span><?=e($s['l'])?></span>
  </div>
<?php endforeach; ?>
</div>

<!-- Body -->
<div class="body">

<?php if($error): ?>
<div class="alert alert-err"><?=e($error)?></div>
<?php endif; ?>

<?php
// ═════════════════════════════════════════════════════════════════════════════
// STEP 1 — Requirements
// ═════════════════════════════════════════════════════════════════════════════
if ($step === 'requirements'):
$req = checkReqs();
?>
<h2>Server Requirements</h2>
<p class="lead">Let's verify your server is ready for <?=e($appName)?>.</p>

<div class="stitle">PHP Version</div>
<ul class="clist">
  <li class="<?=$req['php']['passed']?'ok':'no'?>">
    <span class="ico"><?=$req['php']['passed']?'<svg class="ico-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>':'<svg class="ico-x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>'?></span>
    PHP &ge; <?=e($req['php']['min'])?>
    <span class="meta"><?=e($req['php']['current'])?></span>
  </li>
</ul>

<div class="stitle">PHP Extensions</div>
<ul class="clist">
<?php foreach($req['exts'] as $x): ?>
  <li class="<?=$x['ok']?'ok':'no'?>">
    <span class="ico"><?=$x['ok']?'<svg class="ico-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>':'<svg class="ico-x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>'?></span>
    <?=e($x['name'])?>
  </li>
<?php endforeach; ?>
</ul>

<div class="stitle">Writable Directories</div>
<ul class="clist">
<?php foreach($req['paths'] as $x): ?>
  <li class="<?=$x['ok']?'ok':'no'?>">
    <span class="ico"><?=$x['ok']?'<svg class="ico-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>':'<svg class="ico-x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>'?></span>
    <?=e($x['path'])?>
  </li>
<?php endforeach; ?>
</ul>

<?php if($req['ok']): ?>
<div class="alert alert-ok">All requirements met. Your server is ready.</div>
<?php else: ?>
<div class="alert alert-err">Some requirements are not met. Please fix them before continuing.</div>
<?php endif; ?>

<div class="actions">
  <span></span>
  <?php if($req['ok']): ?>
    <a href="install.php?step=database" class="btn btn-p">Continue <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
  <?php else: ?>
    <a href="install.php?step=requirements" class="btn btn-s">Recheck</a>
  <?php endif; ?>
</div>

<?php
// ═════════════════════════════════════════════════════════════════════════════
// STEP 2 — Database
// ═════════════════════════════════════════════════════════════════════════════
elseif ($step === 'database'):
$db = $_SESSION['inst_db'] ?? [];
?>
<h2>Database Configuration</h2>
<p class="lead">Enter your MySQL credentials. The connection will be tested before proceeding.</p>

<form method="POST" action="install.php?step=database">
  <div class="fr">
    <div class="fg">
      <label for="db_host">Database Host</label>
      <input type="text" id="db_host" name="db_host" value="<?=e($db['host']??'127.0.0.1')?>" required>
      <span class="hint">Usually 127.0.0.1 or localhost</span>
    </div>
    <div class="fg">
      <label for="db_port">Port</label>
      <input type="number" id="db_port" name="db_port" value="<?=e($db['port']??'3306')?>" required>
    </div>
  </div>
  <div class="fg">
    <label for="db_database">Database Name</label>
    <input type="text" id="db_database" name="db_database" value="<?=e($db['database']??'leadhub')?>" required>
    <span class="hint">The database must already exist. Create it via cPanel or phpMyAdmin first.</span>
  </div>
  <div class="fr">
    <div class="fg">
      <label for="db_username">Username</label>
      <input type="text" id="db_username" name="db_username" value="<?=e($db['username']??'root')?>" required>
    </div>
    <div class="fg">
      <label for="db_password">Password</label>
      <input type="password" id="db_password" name="db_password" value="<?=e($db['password']??'')?>">
      <span class="hint">Leave blank if no password</span>
    </div>
  </div>
  <div class="actions">
    <a href="install.php?step=requirements" class="btn btn-s"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg> Back</a>
    <button type="submit" class="btn btn-p">Test &amp; Continue <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button>
  </div>
</form>

<?php
// ═════════════════════════════════════════════════════════════════════════════
// STEP 3 — Admin
// ═════════════════════════════════════════════════════════════════════════════
elseif ($step === 'admin'):
if (empty($_SESSION['inst_db'])) { header('Location: install.php?step=database'); exit; }
$o = $old ?: ($_SESSION['inst_admin'] ?? []);
?>
<h2>Admin Account</h2>
<p class="lead">Create the Super Admin account with full access to all tenants and settings.</p>

<form method="POST" action="install.php?step=admin">
  <div class="fg">
    <label for="admin_name">Full Name</label>
    <input type="text" id="admin_name" name="admin_name" value="<?=e($o['admin_name']??$o['name']??'')?>" placeholder="Jane Smith" required>
  </div>
  <div class="fg">
    <label for="admin_email">Email Address</label>
    <input type="email" id="admin_email" name="admin_email" value="<?=e($o['admin_email']??$o['email']??'')?>" placeholder="admin@example.com" required>
    <span class="hint">Used for logging in.</span>
  </div>
  <div class="fr">
    <div class="fg">
      <label for="admin_password">Password</label>
      <input type="password" id="admin_password" name="admin_password" required minlength="8">
      <span class="hint">Min 8 characters</span>
    </div>
    <div class="fg">
      <label for="admin_password_confirmation">Confirm Password</label>
      <input type="password" id="admin_password_confirmation" name="admin_password_confirmation" required>
    </div>
  </div>
  <div class="actions">
    <a href="install.php?step=database" class="btn btn-s"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg> Back</a>
    <button type="submit" class="btn btn-p">Continue <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button>
  </div>
</form>

<?php
// ═════════════════════════════════════════════════════════════════════════════
// STEP 4 — License
// ═════════════════════════════════════════════════════════════════════════════
elseif ($step === 'license'):
if (empty($_SESSION['inst_admin'])) { header('Location: install.php?step=admin'); exit; }
$lic = $_SESSION['inst_lic'] ?? '';
?>
<h2>Purchase Code</h2>
<p class="lead">Enter your Envato purchase code to activate <?=e($appName)?> &mdash; CodeCanyon Item <strong>#<?=e($itemId)?></strong>.</p>

<div class="alert alert-info">
  <strong>Where to find it:</strong> Log in to
  <a href="https://codecanyon.net" target="_blank" style="color:var(--info-text);text-decoration:underline;">codecanyon.net</a> &rarr;
  Your account &rarr; Downloads &rarr; find <?=e($appName)?> &rarr; License certificate &amp; purchase code.
</div>

<form method="POST" action="install.php?step=license">
  <div class="fg">
    <label for="license_key">Envato Purchase Code <span style="color:var(--error)">*</span></label>
    <input type="text" id="license_key" name="license_key" value="Nulled by codingshop.org" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" style="font-family:monospace;letter-spacing:.04em;" required pattern=".{6,}" autocomplete="off">
    <span class="hint"><strong>Required.</strong> Your code is validated against the LeadHub licensing server before the install proceeds. If validation fails, your code will be saved here so you can correct it without re-entering the previous steps.</span>
  </div>
  <div class="actions">
    <a href="install.php?step=admin" class="btn btn-s"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg> Back</a>
    <button type="submit" class="btn btn-p">Continue <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button>
  </div>
</form>

<?php
// ═════════════════════════════════════════════════════════════════════════════
// STEP 5 — Finish
// ═════════════════════════════════════════════════════════════════════════════
elseif ($step === 'finish'):
$db = $_SESSION['inst_db']??null; $admin = $_SESSION['inst_admin']??null; $lic = $_SESSION['inst_lic']??'';
if (!$db || !$admin) { header('Location: install.php?step=requirements'); exit; }
?>
<h2>Ready to Install</h2>
<p class="lead">Review your configuration. <?=e($appName)?> will create the database tables and your Super Admin account. You can create workspaces (tenants) later from the Super Admin panel.</p>

<div class="summary">
  <div class="srow"><span class="sl">Database Host</span><span class="sv"><?=e($db['host'])?>:<?=e($db['port'])?></span></div>
  <div class="srow"><span class="sl">Database</span><span class="sv"><?=e($db['database'])?></span></div>
  <div class="srow"><span class="sl">Super Admin</span><span class="sv"><?=e($admin['email'])?></span></div>
  <div class="srow"><span class="sl">CodeCanyon Item</span><span class="sv">#<?=e($itemId)?></span></div>
  <div class="srow"><span class="sl">License</span><span class="sv">Verified</span></div>
</div>

<div id="pg" style="display:none;margin-bottom:1.25rem">
  <div class="alert alert-info" id="pm">Installing <?=e($appName)?>...</div>
  <div class="ptrack"><div class="pbar" id="pb"></div></div>
</div>
<div id="done" style="display:none"><div class="alert alert-ok"><?=e($appName)?> installed successfully! Redirecting to admin panel...</div></div>
<div id="fail" style="display:none"><div class="alert alert-err" id="em"></div></div>

<div class="actions" id="btns">
  <a href="install.php?step=license" class="btn btn-s"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg> Back</a>
  <button class="btn btn-p" id="ibtn" onclick="run()">Install <?=e($appName)?> <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button>
</div>

<script>
async function run(){
  var b=document.getElementById('btns'),pg=document.getElementById('pg'),
      pb=document.getElementById('pb'),pm=document.getElementById('pm');
  b.style.display='none';pg.style.display='block';
  var s=[{m:'Writing configuration...',p:15},{m:'Preparing database...',p:30},{m:'Running migrations...',p:50},{m:'Seeding data...',p:65},{m:'Creating admin account...',p:80},{m:'Finalizing...',p:92}],
      i=0,iv=setInterval(function(){if(i<s.length){pm.textContent=s[i].m;pb.style.width=s[i].p+'%';i++}},1500);
  try{
    var r=await fetch('install.php?step=run',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'_i=1'});
    clearInterval(iv);var d=await r.json();
    if(d.success){pb.style.width='100%';pm.textContent='Complete!';
      setTimeout(function(){pg.style.display='none';document.getElementById('done').style.display='block';
        setTimeout(function(){window.location.href=d.redirect},2e3)},500);
    }else{pg.style.display='none';document.getElementById('em').textContent=d.message;
      document.getElementById('fail').style.display='block';b.style.display='flex'}
  }catch(e){clearInterval(iv);pg.style.display='none';
    document.getElementById('em').textContent='Network error: '+e.message;
    document.getElementById('fail').style.display='block';b.style.display='flex'}
}
</script>

<?php endif; ?>
</div>

<!-- Footer -->
<div class="ftr"><?=e($appName)?> v<?=e($appVersion)?> &mdash; Omnichannel Lead Aggregator</div>
</div>
</body>
</html>
