<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Help Center FAQ articles — title + body translation strings
|--------------------------------------------------------------------------
|
| The 18 canonical FAQ articles rendered at /admin/help.  Buyers
| translate or adapt the copy by editing this file, or by copying
| it to lang/<locale>/help_center_articles.php and translating
| each value.
|
| Keys are stable slugs so translation files can stay in sync as
| article ordering changes.  Tags + categories use a sibling file
| structure for the same translation discipline.
|
| Tags are not translated by default — they're used as search-index
| keywords and filter chips, and operators frequently want them to
| match English for SEO / muscle memory.  Buyers who want translated
| tags can copy a tags slot into their target-locale file.
|
*/

return [

    // ─── Page chrome ────────────────────────────────────────────────
    'page_title'        => 'Help & FAQ',
    'nav_label'         => 'Help & FAQ',
    'heading'           => 'How can we help?',
    'subheading'        => "Searchable answers to the questions every workspace asks in the first week. If yours isn't here, contact support.",

    // ─── Categories (rendered as section headers) ───────────────────
    'category' => [
        'getting_started'   => 'Getting started',
        'forms'             => 'Forms',
        'pipelines'         => 'Pipelines',
        'automations'       => 'Automations',
        'email'             => 'Email',
        'billing'           => 'Billing',
        'privacy'           => 'Privacy & Data',
        'team'              => 'Team',
        'general'           => 'General',
    ],

    // ─── Articles ───────────────────────────────────────────────────
    // Each article: title + body (plain text, newlines preserved
    // by nl2br at the view layer).  Tags remain English by default.
    'articles' => [

        'add_first_lead' => [
            'title' => 'How do I add my first lead?',
            'body'  => 'Go to Leads in the sidebar, click "New Lead" and fill in at minimum first name + email. The system auto-creates a company link if a company with that domain already exists.',
        ],

        'import_csv' => [
            'title' => 'How do I import existing leads from a spreadsheet?',
            'body'  => 'Leads → "Import" button (top right). Upload a CSV — the system maps columns automatically. Common fields recognised: first_name, last_name, email, phone, company, source, status. Custom fields can be mapped manually.',
        ],

        'branding_setup' => [
            'title' => 'Where do I set up my brand colors and logo?',
            'body'  => 'Settings → Branding. Upload a logo (we recommend 600x200 PNG with transparent background) and set primary + secondary colors. Changes apply to login pages, emails, and landing pages within seconds.',
        ],

        'embed_form' => [
            'title' => 'How do I embed a form on my website?',
            'body'  => 'Forms → click any form → "Embed" tab. Copy the iframe snippet (works anywhere) or the JavaScript snippet (loads inline, better UX). Submissions create new leads automatically.',
        ],

        'prefill_form' => [
            'title' => 'Can I prefill form fields from URL parameters?',
            'body'  => "Yes. Append ?email=foo@bar.com&first_name=Jane to your form's public URL. The matching fields are prefilled. Useful for personalised email campaigns.",
        ],

        'pipeline_stages' => [
            'title' => 'How do I customise pipeline stages?',
            'body'  => "Settings → Pipelines → select a pipeline → edit stages. Drag to reorder. Set \"win probability %\" so the dashboard's weighted forecast lines up with reality.",
        ],

        'pipeline_per_team' => [
            'title' => 'Can I have different pipelines for different teams?',
            'body'  => "Yes — each team can have its own pipeline. Set \"default\" on the pipeline most leads should land in. Members can switch by changing the lead's pipeline_id from the lead detail view.",
        ],

        'automation_intro' => [
            'title' => 'What is an automation?',
            'body'  => 'A trigger + a list of actions that fire when the trigger condition is met. Examples: "When a new lead is created → assign round-robin → notify Slack → send welcome email". Build them at Automations → New.',
        ],

        'automation_manual' => [
            'title' => 'How do I trigger an automation manually?',
            'body'  => "Open any lead → \"Run automation\" action → pick from the list. Useful for testing or for one-off campaigns where the trigger condition isn't a perfect match.",
        ],

        'smtp_connect' => [
            'title' => 'How do I connect my own SMTP server?',
            'body'  => 'Settings → Email Settings. Enter SMTP host, port, username, password. Click "Send test email". Once verified, all transactional emails (invitations, password resets, lifecycle reminders) route through your server.',
        ],

        'email_deliverability' => [
            'title' => "Why aren't my emails being delivered?",
            'body'  => "Three things: (1) check Settings → Email Settings → \"Send test email\" works, (2) verify your domain has SPF + DKIM records, (3) check the lead's email is real (we don't bounce-check before send). If all three pass and emails still bounce, your sending IP may be on a blacklist.",
        ],

        'payment_method' => [
            'title' => 'How do I update my payment method?',
            'body'  => "Account → Billing → \"Manage subscription\" → \"Update payment method\". You'll be redirected to your payment provider's secure portal. Card details never touch our servers.",
        ],

        'download_invoices' => [
            'title' => 'How do I download my past invoices?',
            'body'  => 'Account → Billing. Past invoices are listed under "Recent activity". Each one downloads as a PDF.',
        ],

        'cancel_subscription' => [
            'title' => 'How do I cancel my subscription?',
            'body'  => "Account → Billing → \"Cancel subscription\". You'll keep access until the end of your current billing period. Your data is preserved for 30 days after cancellation in case you change your mind.",
        ],

        'export_data' => [
            'title' => 'How do I export all my data?',
            'body'  => 'Account → Privacy & Data → "Export my data". A ZIP is built in the background containing every record in JSON format. Download link is valid 48 hours.',
        ],

        'delete_workspace' => [
            'title' => 'How do I delete my workspace permanently?',
            'body'  => 'Account → Privacy & Data → "Delete my workspace". Deletion is scheduled 30 days out — you can cancel any time before then. After 30 days, all records are permanently removed.',
        ],

        'invite_member' => [
            'title' => 'How do I invite a team member?',
            'body'  => 'Settings → Team & Seats → "Invite member". Enter email + role (member or manager). They get a setup link valid for 24 hours.',
        ],

        'member_vs_manager' => [
            'title' => "What's the difference between member and manager roles?",
            'body'  => "Member: can work with leads (create, edit, assign), can't change settings or invite team. Manager: everything member can do PLUS invite/remove members and edit pipelines. Admin: workspace owner — every permission, plus can delete the workspace.",
        ],

    ],

];
