<?php

return [

    /*
    |--------------------------------------------------------------------------
    | LeadHub Application Version
    |--------------------------------------------------------------------------
    */
    'version' => env('LEADHUB_VERSION', '1.0.7'),

    /*
    |--------------------------------------------------------------------------
    | REST API Version
    |--------------------------------------------------------------------------
    | The current API version prefix used by every documented endpoint.
    | Lives at `/api/<api_version>/...` (e.g. `/api/v1/leads`).
    |
    | The same value is rendered by ApiDocsController into the Swagger UI
    | "Servers" base URL and into the "Base URL & Versioning" banner at
    | the top of the /api/docs page, so when a v2 ships the operator
    | only needs to bump LEADHUB_API_VERSION=v2 in .env (no code edits)
    | and the docs auto-update.
    */
    'api_version' => env('LEADHUB_API_VERSION', 'v1'),

    /*
    |--------------------------------------------------------------------------
    | Marketing Landing Variant
    |--------------------------------------------------------------------------
    | Which public landing-page template to render at /.
    |
    |   'light'     — polished white/light marketing template (default)
    |   'modern'    — dark-themed gradient Linear/Cursor-style alternative
    |   'editorial' — ultra-minimal 2026 post-Linear, monochrome bento grid
    |   'classic'   — original content-heavy landing template
    |
    | Configurable from Script Settings; stored in .env via EnvEditor.
    */
    'landing_variant' => env('LEADHUB_LANDING_VARIANT', 'light'),

    /*
    |--------------------------------------------------------------------------
    | License Configuration
    |--------------------------------------------------------------------------
    | The licensing server uses a 3-endpoint protocol mirrored from
    | themesic's existing Perfex modules:
    |
    |   POST {base_url}/register   First-time activation.  Client
    |                              forwards the Envato sale lookup
    |                              (envato_res), receives a JWT
    |                              + verification_id back.
    |   POST {base_url}/validate   Periodic re-check.  Client sends
    |                              {verification_id, item_id,
    |                              activated_domain} and the JWT in
    |                              the Authorization header.  Server
    |                              returns {valid: true|false}.
    |   GET  {base_url}/givemecode Returns the Envato Author-API
    |                              bearer token so clients don't ship
    |                              the personal token themselves.
    |
    | item_id          CodeCanyon item ID for this product. Used to
    |                  reject purchase codes for other items.
    | grace_days       Window after the last successful /validate
    |                  during which the panel keeps working when the
    |                  server starts saying invalid.
    | heartbeat_hours  Window after a 5xx/404 from register or
    |                  validate during which the panel keeps working
    |                  on the heartbeat fallback (the sample uses
    |                  168 = 7 days).
    | check_interval_fallback_seconds
    |                  Used when the JWT does not include a
    |                  check_interval claim or the JWT decode fails;
    |                  default 24h matches the daily cron cadence.
    */
    'license' => [
        'key'         => env('LEADHUB_LICENSE_KEY', ''),
        'item_id'     => env('LEADHUB_LICENSE_ITEM_ID', '63311759'),
        'base_url'    => rtrim(env('LEADHUB_LICENSE_BASE_URL', 'https://envato.toofasthost.com/api'), '/'),
        'envato_sale_url' => env('LEADHUB_ENVATO_SALE_URL', 'https://api.envato.com/v3/market/author/sale'),
        'grace_days'  => (int) env('LEADHUB_LICENSE_GRACE_DAYS', 14),
        'heartbeat_hours' => (int) env('LEADHUB_LICENSE_HEARTBEAT_HOURS', 168),
        'check_interval_fallback_seconds' => (int) env('LEADHUB_LICENSE_CHECK_INTERVAL_FALLBACK', 86400),
    ],

    /*
    |--------------------------------------------------------------------------
    | Live-Demo Lockdown
    |--------------------------------------------------------------------------
    | Set LEADHUB_DEMO_MODE=true on the public live demo (theleadhub.app)
    | so casual visitors cannot destroy demo data, read or replace
    | gateway/SMTP/API credentials, send real outbound communications,
    | apply updates, run backups, or impersonate other accounts.
    |
    | Buyers running their own copy leave it OFF (the default) and see
    | no behavioural difference - the App\Support\DemoMode::guard()
    | call sites all short-circuit when this is false.
    |
    | demo_purchase_url is the destination of the "Get LeadHub" CTA
    | button on the demo-blocked toast.
    */
    'demo_mode'         => env('LEADHUB_DEMO_MODE', false),
    'demo_purchase_url' => env('LEADHUB_DEMO_PURCHASE_URL', 'https://1.envato.market/themesic'),

    /*
    |--------------------------------------------------------------------------
    | Update Server
    |--------------------------------------------------------------------------
    */
    'updates' => [
        'server_url' => env('LEADHUB_UPDATE_SERVER', 'https://updates.themesic.com/api/latest'),
    ],

    /*
    |--------------------------------------------------------------------------
    | White-label Defaults
    |--------------------------------------------------------------------------
    | These are fallback values used before per-tenant branding is configured.
    */
    'branding' => [
        'app_name'      => env('LEADHUB_APP_NAME', 'LeadHub'),
        'primary_color' => env('LEADHUB_PRIMARY_COLOR', '#4f46e5'),
        'accent_color'  => env('LEADHUB_ACCENT_COLOR', '#06b6d4'),
        'logo_url'      => env('LEADHUB_LOGO_URL', null),
        'favicon_url'   => env('LEADHUB_FAVICON_URL', null),
        'footer_text'   => env('LEADHUB_FOOTER_TEXT', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Installer
    |--------------------------------------------------------------------------
    */
    'installer' => [
        'lock_file' => storage_path('installed.lock'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Lead Sources
    |--------------------------------------------------------------------------
    */
    'sources' => [
        'facebook'            => 'Facebook Lead Ads',
        'instagram'           => 'Instagram',
        'tiktok'              => 'TikTok',
        'linkedin'            => 'LinkedIn',
        'whatsapp'            => 'WhatsApp',
        'viber'               => 'Viber',
        'telegram'            => 'Telegram',
        'twitter'             => 'Twitter / X',
        'snapchat'            => 'Snapchat',
        'pinterest'           => 'Pinterest',
        'google_ads'          => 'Google Ads',
        'youtube'             => 'YouTube',
        'microsoft_ads'       => 'Microsoft Advertising',
        'email'               => 'Email (IMAP)',
        'typeform'            => 'Typeform',
        'jotform'             => 'JotForm',
        'calendly'            => 'Calendly',
        'web_form'            => 'LeadHub Web Form',
    ],

    /*
    |--------------------------------------------------------------------------
    | Billing (multi-gateway: Stripe / PayPal / Paystack / Razorpay / Manual)
    |--------------------------------------------------------------------------
    | Driver selection lives on each Plan row + tenant-level
    | BillingSettings, not a global default.  This block only carries
    | the on/off toggle and the operator's billing-contact email.
    */
    'billing' => [
        'enabled'       => env('BILLING_ENABLED', false),
        'contact_email' => env('BILLING_CONTACT_EMAIL', 'sales@leadhub.io'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Lead Visibility
    |--------------------------------------------------------------------------
    | managers_see_unassigned_pool: when a team manager's row-level scope is
    | applied, also show leads that no team owns yet (assigned_team_id IS NULL).
    | Keeps freshly-imported / freshly-captured leads visible to the people who
    | distribute them. Set false for a strict "only my team's leads" policy.
    */
    'leads' => [
        'managers_see_unassigned_pool' => env('LEADHUB_MANAGERS_SEE_UNASSIGNED_POOL', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Self-service Registration
    |--------------------------------------------------------------------------
    | When enabled, the /register route is live and visitors can create a
    | workspace themselves. Disable it for self-hosted single-tenant installs.
    */
    'registration' => [
        'enabled' => env('LEADHUB_REGISTRATION_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Primary Host
    |--------------------------------------------------------------------------
    | The canonical marketing/app domain. Used to tell apart tenant
    | subdomains/custom domains from the root app itself.
    */
    'domain' => env('LEADHUB_DOMAIN', parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST)),

    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    */
    /*
    |--------------------------------------------------------------------------
    | Backups
    |--------------------------------------------------------------------------
    */
    'backups' => [
        'auto_nightly' => env('LEADHUB_AUTO_BACKUP', false),
    ],

    'security' => [
        'enforce_2fa'           => env('SECURITY_ENFORCE_2FA', false),
        'max_login_attempts'    => env('SECURITY_MAX_LOGIN_ATTEMPTS', 5),
        'lockout_duration'      => env('SECURITY_LOCKOUT_MINUTES', 15),
        'session_lifetime'      => env('SESSION_LIFETIME', 120),
    ],

    /*
    |--------------------------------------------------------------------------
    | VAPID Keys for Browser Push Notifications
    |--------------------------------------------------------------------------
    | Generate at https://vapidkeys.com or via Settings > Notifications
    | in the admin panel. No terminal commands needed.
    */
    'vapid' => [
        'public_key'  => env('VAPID_PUBLIC_KEY', ''),
        'private_key' => env('VAPID_PRIVATE_KEY', ''),
        'subject'     => env('VAPID_SUBJECT', 'mailto:admin@leadhub.app'),
    ],

    /*
    |--------------------------------------------------------------------------
    | OneSignal Push Notifications
    |--------------------------------------------------------------------------
    | Sign up at https://onesignal.com and create a Web Push app.
    | Set the App ID below and the REST API Key for server-side sends.
    */
    'onesignal' => [
        'app_id'       => env('ONESIGNAL_APP_ID', ''),
        'rest_api_key' => env('ONESIGNAL_REST_API_KEY', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Retention windows (audit logs, login attempts, billing events)
    |--------------------------------------------------------------------------
    | The leadhub:purge-audit-logs scheduled command deletes rows
    | older than these windows nightly.  Defaults:
    |   - audit logs:               180 days (GDPR / SOC2 compromise)
    |   - login attempts:            30 days (forensic only)
    |   - processed billing events:  90 days (gateways stop retrying
    |                                         long before this)
    */
    'audit_log_retention_days'      => (int) env('LH_AUDIT_RETENTION_DAYS', 180),
    'login_attempt_retention_days'  => (int) env('LH_LOGIN_ATTEMPT_RETENTION_DAYS', 30),
    'billing_event_retention_days'  => (int) env('LH_BILLING_EVENT_RETENTION_DAYS', 90),

    /*
    |--------------------------------------------------------------------------
    | Public API
    |--------------------------------------------------------------------------
    | Tenant-level rate cap is computed as multiplier × largest per-key
    | limit unless the tenant has settings.api.rate_limit_per_hour set
    | explicitly.  Default 5x covers prod/staging/CI fan-out without
    | letting one tenant burn the whole node's capacity.
    */
    'api' => [
        'tenant_rate_limit_multiplier' => (int) env('LH_API_TENANT_RL_MULTIPLIER', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | GDPR Data Export
    |--------------------------------------------------------------------------
    | Hard cap (bytes) on the binary uploads bundled into a tenant data-export
    | ZIP, so a runaway/malicious tenant can't fill the host disk. Read via
    | config() — NOT env() directly — so the cap still applies after
    | `php artisan config:cache` (env() outside config files returns null when
    | the config is cached). Default 2 GB.
    */
    'export_max_bytes' => (int) env('LH_EXPORT_MAX_BYTES', 2 * 1024 * 1024 * 1024),

    /*
    |--------------------------------------------------------------------------
    | AI SDR — per-tenant daily spend ceiling
    |--------------------------------------------------------------------------
    | Max AI decisions the autonomous follow-up agent may make for ONE tenant
    | per calendar day. Every decision is one call against the platform owner's
    | (super-admin) OpenAI key, so this stops a single tenant mass-enrolling and
    | running up unbounded cost on the shared key. Counted from the
    | ai_sdr_messages audit trail; read via config() so it survives
    | `config:cache`. Set 0 to disable the ceiling.
    */
    'ai_sdr' => [
        'tenant_daily_cap' => (int) env('LH_AI_SDR_TENANT_DAILY_CAP', 200),
    ],

    /*
    |--------------------------------------------------------------------------
    | LeadBot — daily assistant-reply ceiling (per bot)
    |--------------------------------------------------------------------------
    | Same shared-key protection as AI SDR, for the embeddable website chatbot.
    | A tenant cap of 0 / blank falls back to `default` (NOT "unlimited"), and a
    | tenant can never raise its own cap above `max`, so the super-admin OpenAI
    | key always keeps a ceiling. Read via config() so it survives config:cache.
    */
    'chatbot' => [
        'daily_cap_default' => (int) env('LH_CHATBOT_DAILY_CAP_DEFAULT', 500),
        'daily_cap_max'     => (int) env('LH_CHATBOT_DAILY_CAP_MAX', 5000),
    ],

];
