<?php

declare(strict_types=1);

/*
|------------------------------------------------------------
| SuperAdmin ScriptSettings — Filament admin strings
|------------------------------------------------------------
| Accessed via __('filament/sa_script_settings.<key>').
| Buyers translate or adapt by editing this file or copying it
| to lang/<locale>/filament/sa_script_settings.php.
*/

return [

    // ----- Page title / navigation -----
    'title'                            => 'Script Settings',
    'navigation_label'                 => 'Script Settings',

    // ----- Reporting & localisation section -----
    'reporting_section_description'    => 'Defaults used by the Billing dashboard, pricing page, and anywhere a tenant has not picked its own value. Stored in the Spatie Laravel Settings database payload — survives .env rewrites and takes effect immediately on save (no cache clear required).',
    'reporting_currency_label'         => 'Reporting Currency',
    'reporting_currency_helper'        => 'Used for MRR / ARR / ARPU figures on the Billing dashboard.',
    'default_timezone_label'           => 'Default Timezone',
    'date_format_label'                => 'Date Format',
    'default_language_label'           => 'Default Language',
    'default_language_helper'          => 'Sets the default UI language for new visitors. Shipped: English, Arabic (RTL), Spanish, Hindi.',

    // Last-resort label when config('locales.supported') is empty —
    // we still need at least one Select option, so we fall back to
    // 'en' => __('locale_fallback_english_native'). Stays "English" in
    // the en locale itself, intentionally self-referential.
    'locale_fallback_english_native'   => 'English',

    // ----- Backups section -----
    'backups_section_description'      => 'Automatic nightly backup of the database and uploaded files. Backups are stored under storage/app/backups/.',
    'auto_nightly_backup_label'        => 'Enable nightly automatic backups',
    'auto_nightly_backup_helper'       => 'When enabled, a fresh backup is created every night at 02:00 UTC via the scheduled task runner (cron.php).',

    // ----- Public marketing landing section -----
    'landing_section_description'      => 'Which landing page visitors see at the root URL (/). Change takes effect after saving — no cache clear required.',
    'landing_template_label'           => 'Landing template',
    'landing_template_helper'          => 'Preview any variant with /?preview=1 while signed in as super-admin. Change takes effect immediately after saving — no cache clear required.',
    'landing_variant_light'            => 'Light — polished white marketing hero (default)',
    'landing_variant_warm'             => 'Warm — cream background, serif display, editorial magazine feel',
    'landing_variant_modern'           => 'Modern — dark theme, gradient purple/pink',
    'landing_variant_editorial'        => 'Editorial — dark monochrome, post-Linear bento',
    'landing_variant_classic'          => 'Classic — content-heavy original template',

    // ----- Outbound email / SMTP section -----
    'smtp_section_description'         => 'Script-wide mail defaults written to your .env file (config:clear is triggered on save). Precedence order at send time: (1) tenant\'s own SMTP from Email Settings → (2) these script-level .env values → (3) Laravel\'s hard-coded "log" driver fallback. Tenants who leave their page empty fall back to these defaults automatically.',
    'mailer_label'                     => 'Mailer',
    'mailer_helper'                    => 'Keep this on "Log" while you\'re still testing. Switch to SMTP once your credentials below work.',
    'mailer_option_smtp'               => 'SMTP',
    'mailer_option_sendmail'           => 'sendmail',
    'mailer_option_log'                => 'Log (dev only — no real email sent)',
    'mailer_option_array'              => 'Array (testing — discards mail)',
    'smtp_host_label'                  => 'SMTP Host',
    'smtp_host_placeholder'            => 'smtp.example.com',
    'smtp_port_label'                  => 'SMTP Port',
    'smtp_port_placeholder'            => '587 (STARTTLS), 465 (SSL), 25 (unencrypted)',
    'encryption_label'                 => 'Encryption',
    'encryption_option_tls'            => 'TLS (STARTTLS — recommended)',
    'encryption_option_ssl'            => 'SSL',
    'encryption_option_none'           => 'None',
    'smtp_username_label'              => 'SMTP Username',
    'smtp_password_label'              => 'SMTP Password',
    'smtp_password_placeholder'        => 'Leave unchanged to keep current',
    'from_address_label'               => 'From Address',
    'from_address_placeholder'         => 'noreply@yourdomain.com',
    'from_name_label'                  => 'From Name',
    'from_name_placeholder'            => 'LeadHub',

    // ----- Notifications -----
    'settings_saved_title'             => 'Script settings saved.',
    'settings_saved_body'              => 'Reporting + mail defaults updated. If you changed the mailer, send a test email to confirm it works.',
    'no_user_title'                    => 'No authenticated user.',
    'mailer_dry_warning_title'         => 'Current mailer is ":mailer" — no real email will be sent.',
    'mailer_dry_warning_body'          => 'Switch to SMTP and save before testing.',
    'test_email_sent_title'            => 'Test email sent to :recipient',
    'test_email_sent_body'             => 'Check the inbox (and spam) in the next minute.',
    'test_send_failed_title'           => 'Send failed: :error',
    'test_send_failed_body'            => 'Double-check host, port, encryption and credentials.',

    // ----- Test email body -----
    'test_email_body_intro'            => 'This is a test email from your :app Script Settings page.',
    'test_email_body_confirmation'     => 'If you received this, your SMTP credentials are working.',
    'test_email_body_sent_at'          => 'Sent at: :timestamp UTC',
    'test_email_subject'               => '[:app] SMTP test email',

    // ----- Header actions -----
    'action_send_test'                 => 'Send Test Email',
    'action_send_test_tooltip'         => 'Uses the values currently on the form — test first, save if it works.',
    'action_send_test_recipient_label' => 'Send test email to',
    'action_send_test_recipient_helper' => 'Any address — override to verify delivery to your production mailbox before committing credentials.',
    'action_send_test_modal_submit'    => 'Send test',
    'action_save_settings'             => 'Save Settings',

    // ----- Hero -----
    'page_hero_title'                  => 'Global defaults for your LeadHub deployment',
    'hero_eyebrow'                     => 'Script Owner Settings',
    'hero_subtitle'                    => 'Pick the reporting currency for your Billing dashboard, your default timezone, and the language every new workspace starts in.',

    // ----- Scheduler / cron explainer -----
    'cron_details_summary'             => 'What does the scheduler run?',
    'cron_desc'                        => 'Configure your remote scheduler to GET this URL every minute:',

    // ─── Cron section heading + intro ───
    'cron_section_title'               => 'Background job scheduler (cron)',
    'cron_section_desc_html'           => 'LeadHub runs several background jobs every minute — email sending, automations, scheduled reports, lead scoring, task reminders, IMAP inbox polling, subscription lifecycle checks, and nightly backups. All of them depend on a cron trigger hitting <code class="ss-chip">cron.php</code> or running <code class="ss-chip">artisan schedule:run</code> once per minute. Pick the option your server supports.',

    // ─── Scheduler details list (interval → description) ───
    'cron_list_every_5_min_label'      => 'Every 5 min',
    'cron_list_every_5_min_desc'       => 'IMAP inbox polling, task reminders',
    'cron_list_every_15_min_label'     => 'Every 15 min',
    'cron_list_every_15_min_desc'      => 'email sequence step dispatcher',
    'cron_list_every_hour_label'       => 'Every hour',
    'cron_list_every_hour_desc'        => 'no-activity automations, scheduled report delivery, saved-filter alerts',
    'cron_list_every_6_hours_label'    => 'Every 6 hours',
    'cron_list_every_6_hours_desc'     => 'subscription lifecycle (trial expirations, grace-period reminders)',
    'cron_list_daily_02_label'         => 'Daily at 02:00',
    'cron_list_daily_02_desc'          => 'tenant database backups (when enabled)',
    'cron_list_daily_09_label'         => 'Daily at 09:00',
    'cron_list_daily_09_desc'          => 'LeadHub update-check ping',
    'cron_list_daily_user_label'       => 'Daily at user-configured hour',
    'cron_list_daily_user_desc'        => 'notifications digest',

    // ─── Cron Option A: Shared hosting ───
    'cron_option_a_label'              => 'Shared hosting (cPanel / Plesk / DirectAdmin)',
    'cron_option_a_tag'                => 'Easiest',
    'cron_option_a_desc_html'          => 'In your hosting panel, open <em>Cron Jobs</em> and add a task that runs every minute:',
    'cron_option_a_hint_html'          => 'Some shared hosts block <code class="ss-chip-light">exec()</code> — use Option B instead if the above silently fails.',

    // ─── Cron Option B: URL-based cron ───
    'cron_option_b_label'              => 'URL-based cron (cron-job.org, EasyCron, UptimeRobot)',
    'cron_option_b_secret_hint_html'   => 'The <code class="ss-chip-light">secret=</code> param must match your <code class="ss-chip-light">CRON_SECRET</code> in <code class="ss-chip-light">.env</code> — this blocks anyone else from triggering your scheduler.',
    'cron_option_b_warn_html'          => '<strong>Heads up:</strong> <code class="ss-chip-warn">CRON_SECRET</code> is not set — your URL trigger is open to the internet. Add a random 32-char secret to <code class="ss-chip-warn">.env</code> and append <code class="ss-chip-warn">?secret=…</code> to the URL above.',

    // ─── Cron Option C: VPS / dedicated (native Laravel scheduler) ───
    'cron_option_c_label'              => 'VPS / dedicated (native Laravel scheduler)',
    'cron_option_c_tag'                => 'Recommended for VPS',
    'cron_option_c_desc_html'          => 'SSH in as your web user and run <code class="ss-chip-light">crontab -e</code>, then add:',
    'cron_option_c_hint_html'          => 'This bypasses <code class="ss-chip-light">cron.php</code> and uses Laravel\'s native scheduler — lowest overhead, best for VPS or dedicated servers with Redis queues.',

    // ─── Cron copy button + verify ───
    'cron_copy'                        => 'Copy',
    'cron_copied'                      => 'Copied',
    'cron_verify_text_html'            => '<strong>Verify it\'s working:</strong> wait 2 minutes after saving your cron, then check the "Queue &amp; Workers" page (inside any tenant workspace) — if jobs are flowing, the connection will show a recent timestamp. You can also run <code class="ss-chip-success">php artisan schedule:list</code> from SSH to see every registered task.',
];
