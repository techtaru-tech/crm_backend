# Changelog

All notable improvements to LeadHub are listed here, newest first.

## 2.0 — Unreleased (in development)

LeadHub 2.0 is a major AI-native upgrade built around three flagship additions:

- LeadBot — an embeddable AI website chatbot. Drop one snippet on your site and it greets visitors, answers questions about your business, qualifies them, and captures the lead straight into your pipeline. A per-workspace daily message cap keeps AI cost predictable.
- AI SDR — an autonomous follow-up agent that works each new lead like a junior sales rep: it sends personalised email touches on a cadence you set, respects your business hours and a per-day send ceiling, stops the instant a human (the lead or one of your reps) replies, honours STOP / unsubscribe, and hands a qualified lead to a real rep with a written summary.
- Visual Flow Builder — design automations on a drag-and-drop canvas. Wire a trigger to a chain of actions and conditions as visual nodes instead of a form, then save and run them on the existing automation engine (no engine change, fully backward compatible).

All three run on the single OpenAI key you set as platform owner — your workspaces get AI features with no per-workspace key setup, and built-in daily caps protect that key from runaway cost. With no AI key configured, the AI features degrade gracefully instead of erroring.

## 1.0.7 — 2026-06-17

- Marketplace: installing a shared template (automation, form, pipeline, landing page or email sequence) into your workspace now works reliably — some installs previously failed to save.
- Inbound lead API: posting the same email address again now returns the existing lead instead of silently creating a duplicate (affects Zapier, Make, n8n and custom webhook sources).
- Data privacy (GDPR): the scheduled account-erasure job now runs reliably, and a workspace inside its deletion cool-off window is routed to the correct screen instead of a generic "subscription required" page.
- Maintenance: the audit-log retention cleanup now actually removes entries older than the configured window, keeping the database lean on busy installs.
- Marketplace: now ships with free, ready-to-install starter templates (a welcome automation, a contact form, a B2B sales pipeline and a 3-day onboarding email sequence) so it is useful from day one instead of empty.
- Data export: your GDPR export size cap (LH_EXPORT_MAX_BYTES) is now honoured even after running `php artisan config:cache` — previously a cached config silently reverted it to the 2 GB default.
- Billing settings: saving the Super Admin "Billing & Trial" settings no longer errors out — the default trial length, auto-suspend window and affiliate commission now save reliably.
- Kanban: clicking a lead card now opens the lead instead of showing a 404.
- Lead export: exporting your leads to a spreadsheet now works — it previously errored out before the file was produced.
- Landing page: social links and SEO fields set in the Super Admin landing-page editor now save and appear on the public site even when a link is typed without `https://` (it is added for you); a single mistyped link no longer silently blocks the whole page from saving.
- Updates: the Super Admin "Updates" page no longer shows "Error while loading page" when the update-history file can't be read (which a just-applied update can cause) — it loads cleanly and logs the underlying cause instead.
- Team: a workspace admin can now change any member's role — including promoting to or demoting from Admin — straight from the "Role" column on the Team page. The workspace owner always stays an admin, and managers can only move members between Manager and Member.
- Invoicing: set a default tax / VAT rate once under Settings → General → Invoicing and it pre-fills the Tax Rate on every new invoice and quote (still editable per document).
- Invoices & quotes: your workspace logo now appears at the top of the invoice and quote PDFs (it falls back to your workspace name when no logo is uploaded).
- Branding: the Apple touch icon (the home-screen icon on iPhones/iPads) now uses your uploaded favicon instead of the bundled default.
- Security: the workspace owner can no longer be removed, suspended, or demoted by anyone — the account that administers the workspace always keeps access — and a team invite can no longer be tampered with to grant the Admin role.
- Security: operator-entered links (landing-page social icons and the SEO share image) and the invoice/quote logo now reject unsafe or malformed values instead of rendering them.
- Reliability: saving the Super Admin "Billing & Trial" settings no longer errors when a numeric field is left blank.
- Updates: applying an update on locked-down shared hosting no longer fails with "Call to undefined function …exec()" — when the host disables the `exec`/`shell_exec` functions, the automatic pre-update backup now falls back to a pure-PHP database dump instead of aborting the whole update.
- Invoices, quotes & deals: the currency picker now offers all 21 supported currencies (including KES, NGN, ZAR and more) and pre-selects your workspace currency instead of always defaulting to USD.
- Branding: your uploaded favicon now also appears on the workspace login page and throughout the admin pages — previously only the public landing page reflected it.
- Leads: you can now add your own custom lead sources (walk-in, exhibition, referral, …) right from the lead form's Source field; they're remembered for next time and show up in the "Leads by Source" dashboard report.
- Team: a new "Roles & Permissions" page lets a workspace admin choose exactly what the Manager and Member roles can access, instead of those capabilities being fixed (the Admin role always keeps full access).

## 1.0.6 — 2026-06-15

- Recurring invoices / dues: schedule a member's monthly or annual charge once and LeadHub raises the invoice automatically and reminds you as each due date arrives - ideal for memberships, retainers and association dues.
- Import: map your CSV/Excel columns to your own custom fields when importing Leads or Companies, not just the built-in fields.
- Invoices: online "Pay Now" card payments now settle automatically via the payment webhook (previously a paid invoice could stay marked unpaid until recorded by hand).
- Payment Gateway settings: the page no longer shows a blank error when database updates are pending - it tells you to run migrations instead, and the "enable a gateway" setup step now ticks correctly.
- Updates: in-app updates apply more reliably and surface newly added features immediately.
- Custom domains: temporarily removed while we rework host-based serving (a CNAME alone can't be served without per-domain web-server setup).

## 1.0.5 — 2026-06-10

- Companies: bulk-import companies from a CSV or Excel file with column mapping and duplicate detection - the same guided wizard you already use for leads.
- Landing page: a new SEO tab (meta title, description, keywords, social share image) plus Facebook, Instagram and YouTube footer links in the Super Admin editor.
- Team: workspace admins can now change a member's role (Manager or Member) straight from the Team page.
- Two-Factor Authentication: the tenant 2FA setup page now loads its QR code reliably.
- Email: replies from a lead are now logged on the customer timeline and keep the conversation threaded.
- Lead sources: the webhook URL now appears in the copy-and-paste panel when editing a source, and saving a connection no longer fails on some MySQL/MariaDB setups.

## 1.0.4 — 2026-06-04

- In-app update checks now connect to the official LeadHub update service, so "Update check" reliably reports when a new version is available.

## 1.0.3 — 2026-06-04

- Kanban: dragging a lead into a different stage now saves reliably.
- Lead-capture widget: the embed and preview snippet now loads correctly on every site.
- Forms: the landing-page contact form and embedded forms now submit reliably for your visitors.
- Branding: uploaded logos and favicons now display on every host, including shared hosting without a storage symlink.

## 1.0.2 — 2026-06-03

- Kanban: a new "Unassigned" column surfaces leads that aren't in a pipeline stage yet — drag one onto any stage to assign it.
- Email automation: load any saved email template straight into a sequence step or the lead "Send Email" composer.
- System Health: one-click "Finalize update" applies pending updates and rebuilds every cache — no console or SSH required.
- System Health: one-click "Apply pending migrations" for shared-hosting installs.
- Landing page: edit your homepage hero statistics (numbers and labels) from the Super Admin Landing-Page Editor.
- Branding: upload a custom logo and favicon that now apply across the entire site — both the admin panels and the public pages.
- Uploads: import and file-upload size limit raised to 300 MB.
- API: a dedicated documentation page with a clear Base URL & versioning guide, plus integration-friendly lead endpoints for Zapier, Make, and Pabbly.

## 1.0.1 — 2026-05-26

- Maintenance release.
