<?php
/**
 * LeadHub SaaS — Root-level installer shim.
 *
 * Buyers on shared hosting often upload the whole ZIP into public_html/
 * with their Apache/nginx web root pointed at THAT directory (instead
 * of public_html/public/).  In that "naive upload" layout,
 * `https://yourdomain.com/install.php` needs to resolve to *this* file
 * — and we just delegate to the real installer in public/install.php.
 *
 * Three layouts handled:
 *
 *   1. NAIVE UPLOAD (most common on cPanel / shared hosting):
 *        public_html/             ← web root
 *        ├── install.php          ← THIS file
 *        ├── public/
 *        │   └── install.php      ← real installer (delegated to)
 *        ├── app/                 ← exposed to the web (security risk
 *        │                          warned about in the docs)
 *        └── vendor/
 *
 *   2. PROPER (web root = public/):
 *        Apache serves public/install.php directly — this shim is
 *        never reached.  Both /install.php (Apache) and the index.php
 *        redirect chain on /install (no extension) work fine.
 *
 *   3. FLAT UPLOAD (buyer moved public/ contents up, app/ outside webroot):
 *        public_html/             ← web root
 *        ├── install.php          ← THIS file = the real installer
 *        │                          (no public/ subdirectory anymore)
 *        └── (app/, vendor/, storage/ live one directory above)
 *
 * In layout #1 we delegate to public/install.php.  In layout #3 we
 * surface a clear "open /public/install.php" message rather than
 * try to run a guessed-path install — the original public/install.php
 * was authored for layout #1 (its $basePath = __DIR__ . '/..') and
 * including it from a flat install would resolve $basePath OUTSIDE
 * the webroot.  Better to tell the buyer exactly which URL to open.
 */

$realDir = realpath(__DIR__) ?: __DIR__;

// Layout #1 — public/install.php is alongside us.  Delegate.
if (file_exists($realDir . '/public/install.php')) {
    require $realDir . '/public/install.php';
    return;
}

// Layout #3 — flat install.  vendor/ etc. lives one directory above
// our webroot.  The original public/install.php's basePath logic
// would resolve incorrectly if we included it from here, so emit
// a friendly redirect-style message instead.
if (file_exists($realDir . '/../vendor/autoload.php')) {
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    $host = htmlspecialchars((string) ($_SERVER['HTTP_HOST'] ?? 'yourdomain.com'), ENT_QUOTES, 'UTF-8');
    echo '<!doctype html><meta charset="utf-8"><title>Installer location</title>'
       . '<style>body{font:14px/1.5 system-ui,Segoe UI,sans-serif;max-width:640px;'
       . 'margin:48px auto;color:#111;padding:0 16px}code{background:#f3f4f6;'
       . 'padding:2px 6px;border-radius:4px;font-size:13px}</style>'
       . '<h1>Almost there.</h1>'
       . '<p>Your hosting appears to be in a <strong>flat install</strong> '
       . 'layout — application files live one directory above this webroot.</p>'
       . '<p>Open the installer at:</p>'
       . "<p><code>https://{$host}/public/install.php</code></p>"
       . '<p>If that 404s, your <code>public/</code> folder may not have been '
       . 'uploaded — re-extract the ZIP and try again.</p>';
    exit;
}

// Neither layout matches — buyer hasn't fully unzipped or uploaded
// only this file by accident.
http_response_code(500);
header('Content-Type: text/html; charset=utf-8');
echo '<!doctype html><meta charset="utf-8"><title>Installer not found</title>'
   . '<style>body{font:14px/1.5 system-ui,Segoe UI,sans-serif;max-width:640px;'
   . 'margin:48px auto;color:#111;padding:0 16px}code{background:#f3f4f6;'
   . 'padding:2px 6px;border-radius:4px;font-size:13px}</style>'
   . '<h1>Installer not found.</h1>'
   . '<p>Could not locate <code>public/install.php</code> next to this file, '
   . 'and no Laravel app appears to be present at this directory level.</p>'
   . '<p>Most likely cause: the ZIP was not fully extracted, or only this '
   . 'shim file was uploaded.  Re-extract the full ZIP into your web root '
   . 'and refresh this page.</p>';
exit;
