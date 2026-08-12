@extends('marketing.layout')

@section('title', 'Documentation — ' . $appName)
@section('description', 'Quick-start guides and how-tos for getting the most out of ' . $appName . '.')

@push('head')
<style>
    .docs-wrap{max-width:1100px;margin:0 auto;padding:60px 24px 80px;display:grid;grid-template-columns:260px 1fr;gap:48px}
    .docs-nav{position:sticky;top:90px;align-self:flex-start;max-height:calc(100vh - 110px);overflow-y:auto}
    .docs-nav h4{font-size:11px;text-transform:uppercase;letter-spacing:.6px;color:#6b7280;font-weight:700;margin:20px 0 8px;padding:0 12px}
    .docs-nav h4:first-child{margin-top:0}
    .docs-nav a{display:block;padding:7px 12px;font-size:13px;color:#4b5563;border-radius:6px;margin-bottom:2px}
    .docs-nav a:hover{background:#f3f4f6;color:#111827}
    .docs-nav a.active{background:#eef2ff;color:#4f46e5;font-weight:600}
    .docs-main h1{font-size:36px;font-weight:800;color:#0f172a;margin-bottom:12px;letter-spacing:-.01em}
    .docs-main p.lead{font-size:17px;color:#6b7280;margin-bottom:32px;max-width:680px}
    .docs-main h2{font-size:22px;font-weight:700;color:#0f172a;margin:48px 0 12px;padding-top:20px;border-top:1px solid #f1f5f9}
    .docs-main h2:first-of-type{border-top:0;padding-top:0;margin-top:0}
    .docs-main h3{font-size:16px;font-weight:700;color:#111827;margin:24px 0 8px}
    .docs-main p{font-size:15px;color:#374151;line-height:1.7;margin-bottom:14px}
    .docs-main ul,.docs-main ol{padding-left:22px;margin-bottom:14px;color:#374151;font-size:15px;line-height:1.7}
    .docs-main li{margin-bottom:6px}
    .docs-main code{background:#f1f5f9;color:#4f46e5;padding:2px 6px;border-radius:4px;font-size:13px;font-family:ui-monospace,monospace}
    .docs-main pre{background:#0f172a;color:#e2e8f0;padding:18px;border-radius:8px;overflow-x:auto;font-size:13px;line-height:1.6;margin-bottom:14px}
    .docs-main pre code{background:transparent;color:inherit;padding:0}
    .docs-main .callout{background:#eef2ff;border-left:3px solid #4f46e5;padding:14px 18px;border-radius:6px;margin:16px 0;color:#312e81;font-size:14px}
    .docs-main .callout strong{color:#4f46e5}
    .docs-main table{width:100%;border-collapse:collapse;margin:16px 0;font-size:14px}
    .docs-main th,.docs-main td{border:1px solid #e5e7eb;padding:10px 14px;text-align:left}
    .docs-main th{background:#f9fafb;font-weight:600;color:#374151}
    @media(max-width:860px){.docs-wrap{grid-template-columns:1fr;gap:24px}.docs-nav{position:static;max-height:none}}
</style>
@endpush

@section('content')
<div class="docs-wrap">
    <aside class="docs-nav">
        <h4>Getting Started</h4>
        <a href="#quickstart">Quick start</a>
        <a href="#tenant-setup">First-tenant setup</a>
        <a href="#team">Inviting your team</a>

        <h4>Capturing Leads</h4>
        <a href="#lead-sources">Lead sources</a>
        <a href="#forms">Web forms</a>
        <a href="#widget">Embed widget</a>
        <a href="#tracking">Visitor tracking</a>

        <h4>Working Leads</h4>
        <a href="#pipelines">Pipelines</a>
        <a href="#scoring">Lead scoring</a>
        <a href="#companies">Companies</a>
        <a href="#custom-fields">Custom fields</a>
        <a href="#deals">Deal values</a>

        <h4>Communication</h4>
        <a href="#emails">Email threads</a>
        <a href="#sequences">Email sequences</a>
        <a href="#messaging">WhatsApp / SMS</a>

        <h4>Automate</h4>
        <a href="#automations">Automations</a>
        <a href="#templates">Templates</a>
        <a href="#integrations">Integrations</a>

        <h4>API & Webhooks</h4>
        <a href="#api">REST API</a>
        <a href="#webhooks">Outbound webhooks</a>
    </aside>

    <main class="docs-main">
        <h1>{{ $appName }} documentation</h1>
        <p class="lead">
            Everything you need to install, configure, and get your first lead converted.
            Budget 15 minutes for quick-start, 60 minutes to have the whole stack automated.
        </p>

        <h2 id="quickstart">Quick start</h2>
        <p>After installation, sign in as the super admin, create a tenant workspace, and walk through the onboarding wizard.
        The <strong>Getting Started Checklist</strong> on the dashboard guides you through the six moves that turn an empty shell into a working lead engine.</p>
        <div class="callout"><strong>Tip:</strong> if you just want to see the product with sample data, run <code>php artisan db:seed --class=DemoSeeder</code> on a fresh install — it creates a demo tenant, users, leads, pipelines, automations, and forms.</div>

        <h2 id="tenant-setup">First-tenant setup</h2>
        <p>A <strong>tenant</strong> is a workspace — think "one company using LeadHub." The super admin creates tenants, and each tenant has its own leads, pipelines, forms, and users. Visit <code>/super-admin/tenants</code> to create one.</p>
        <ol>
            <li>Fill in the workspace name, slug, owner email.</li>
            <li>Pick a plan (Trial is fine to start).</li>
            <li>Set seat limit (how many users this tenant can invite).</li>
            <li>Save — the owner receives a welcome email.</li>
        </ol>

        <h2 id="team">Inviting your team</h2>
        <p>As a tenant admin, visit <strong>Settings → Team Members</strong> and click <em>Invite</em>. Enter their email and pick a role:</p>
        <table>
            <tr><th>Role</th><th>Can do</th></tr>
            <tr><td>Agent</td><td>Work assigned leads, log activities, send emails</td></tr>
            <tr><td>Manager</td><td>Everything an agent does + team reports + reassign leads</td></tr>
            <tr><td>Admin</td><td>Full workspace access: pipelines, automations, integrations, billing</td></tr>
        </table>

        <h2 id="lead-sources">Lead sources</h2>
        <p>LeadHub ingests leads from 18 sources out of the box: Meta Lead Ads, Instagram, TikTok, LinkedIn, Google Ads, WhatsApp, Telegram, Viber, Twitter, Snapchat, Pinterest, YouTube, Microsoft Ads, Typeform, JotForm, Calendly, IMAP email, and your own web forms.</p>
        <p>Visit <strong>Integrations → Lead Sources</strong> → <em>Connect</em>. Each source asks for different credentials — Meta wants OAuth, Typeform wants an API key, IMAP wants host/port/login. Once connected, new leads start flowing automatically.</p>

        <h2 id="forms">Web forms</h2>
        <p>Build a form in <strong>Leads → Forms</strong>. Choose fields, styling, multi-step flow, reCAPTCHA protection, and the pipeline + stage to drop submissions into. Each form gets a public URL at <code>/forms/&#123;tenant&#125;/&#123;slug&#125;</code> and an embed snippet for your site.</p>

        <h2 id="widget">Embed widget</h2>
        <p>Need a floating "Get a quote" button on your website? Visit <strong>Tools → Lead Capture Widgets</strong>, customize the colors and position, and paste the <code>&lt;script&gt;</code> tag on your site. Widgets capture leads without leaving the visitor's current page.</p>

        <h2 id="tracking">Visitor tracking</h2>
        <p>Create a tracker in <strong>Tools → Web Tracking</strong>, paste the snippet on your site, and <strong>LeadHub records every page view</strong>. When a visitor submits a form or you call <code>LeadHubTracker.identify(email)</code>, their historical page views retroactively link to that lead — so you can see which pages they browsed before converting.</p>
        <pre><code>&lt;script src="https://your-site.com/tracking/UUID/track.js" async&gt;&lt;/script&gt;
&lt;script&gt;
  // Call this after a user logs in or fills a form:
  LeadHubTracker.identify('user@example.com');
&lt;/script&gt;</code></pre>

        <h2 id="pipelines">Pipelines</h2>
        <p>A pipeline is your sales process: <em>New → Contacted → Qualified → Proposal → Won / Lost</em>. Create one in <strong>Leads → Pipelines</strong>, drag to reorder stages, set <code>win_probability</code> on each stage for weighted forecasts. The Kanban board visualizes leads moving across stages.</p>

        <h2 id="scoring">Lead scoring</h2>
        <p>Score leads automatically. In <strong>Settings → Lead Scoring Rules</strong>, add rules like "source equals Meta = +10 points" or "company_size greater than 50 = +20 points". Scores recalculate on every update.</p>

        <h2 id="companies">Companies</h2>
        <p>B2B sales is account-based. LeadHub auto-creates a <strong>Company</strong> when a lead comes in with a corporate email — <code>jane@acme.com</code> creates an Acme Corp record. Visit <strong>Leads → Companies</strong> to see all contacts grouped by account, with rolled-up pipeline values.</p>

        <h2 id="custom-fields">Custom fields</h2>
        <p>Every business is different. Define your own fields in <strong>Settings → Custom Fields</strong>: text, number, date, dropdown, multi-select, checkbox. Choose whether they show in the lead form, the lead table, or the filters. Values store in the <code>custom_fields</code> JSON column.</p>

        <h2 id="deals">Deal values &amp; revenue</h2>
        <p>Add a <code>deal_value</code> and <code>expected_close_date</code> to any lead. The Kanban board shows <strong>pipeline value per stage</strong>, the dashboard shows weighted forecast + won this month, and the revenue report compares periods.</p>
        <div class="callout">Use <strong>Line Items</strong> on the lead view to attach products from your catalog — deal_value auto-calculates from qty × price − discount.</div>

        <h2 id="emails">Email threads</h2>
        <p>Send an email from a lead's page and the reply lands back in the thread — not as a brand-new lead. LeadHub matches by <code>In-Reply-To</code> and <code>References</code> headers, falling back to the <code>From:</code> address. Every outbound email also includes an open-tracking pixel and click-tracking redirector.</p>

        <h2 id="sequences">Email sequences</h2>
        <p>Build multi-step drip campaigns in <strong>Leads → Email Sequences</strong>. Each step has a delay (days + hours), a subject, and a body. Enroll one lead or enroll 500 at once via bulk action. Sequences <strong>auto-unenroll</strong> when the lead replies or when the deal is marked won.</p>

        <h2 id="messaging">WhatsApp / SMS / Telegram</h2>
        <p>Two-way messaging. Connect your WhatsApp Business API, Twilio, or Telegram bot in integrations, then use the <strong>Conversations</strong> page to chat with leads directly. Inbound messages create/update leads; outbound go out via the connected provider.</p>

        <h2 id="automations">Automations</h2>
        <p>Visit <strong>Leads → Automations → + New</strong>. Pick a trigger (lead created, stage changed, score threshold, no activity, tag added, form submitted, manual). Add steps: conditions, delays, or actions. Actions include send email, assign lead, add tag, move stage, notify users, send Slack message, send SMS, fire webhook, create task.</p>

        <h2 id="templates">Automation templates</h2>
        <p>Not sure where to start? Click <strong>Browse Templates</strong> on the automations list and install one of 8 pre-built workflows: Welcome New Lead, Round-Robin Assignment, Hot Lead Alert, Follow-up Reminder, Stage-Change Notification, Re-engage Cold Leads, Auto-Tag VIPs, Slack Alert on New Lead. Installed as <em>disabled</em> — review the steps and toggle on.</p>

        <h2 id="integrations">Integrations</h2>
        <p>Push leads to 39 external tools: HubSpot, Salesforce, Pipedrive, Zoho, Mailchimp, ActiveCampaign, Klaviyo, Slack, Microsoft Teams, Google Sheets, Notion, Airtable, Zapier, Make, N8n, and more. Visit <strong>Settings → Integrations</strong> → <em>+ New</em>, pick the type, fill credentials, and set field mapping.</p>

        <h2 id="api">REST API</h2>
        <p>Full REST API at <code>/api/v1</code>. Create an API key in <strong>Settings → API Keys</strong> with scoped permissions (<code>read:leads</code>, <code>write:leads</code>, <code>read:pipelines</code>, etc.). Pass as <code>Authorization: Bearer &lt;key&gt;</code>. Interactive docs at <code>/api/docs</code>.</p>
        <pre><code>curl -X POST https://your-app.com/api/v1/leads \
  -H "Authorization: Bearer lh_live_..." \
  -H "Content-Type: application/json" \
  -d '{"first_name":"Jane","email":"jane@acme.com","source":"web_form"}'</code></pre>

        <h2 id="webhooks">Outbound webhooks</h2>
        <p>Fire a POST to your own URL when things happen. Events: <code>lead.created</code>, <code>lead.updated</code>, <code>lead.stage_changed</code>, <code>form.submitted</code>, <code>automation.triggered</code>. Filter by source, status, stage, tags. HMAC-SHA256 signature in <code>X-LeadHub-Signature</code> header.</p>
    </main>
</div>
@endsection
