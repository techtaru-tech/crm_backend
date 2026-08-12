# LeadHub SaaS CRM — Technical Documentation

**Project:** CRMTechtaru (LeadHub)  
**Stack:** Laravel 11 + Filament 3 + Livewire 3 + MySQL  
**Architecture:** Multi-tenant SaaS  
**Environment:** XAMPP / PHP 8.3

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Architecture Overview](#2-architecture-overview)
3. [Directory Structure](#3-directory-structure)
4. [Database Architecture](#4-database-architecture)
5. [Multi-Tenancy](#5-multi-tenancy)
6. [Authentication & Authorization](#6-authentication--authorization)
7. [Filament Admin Panels](#7-filament-admin-panels)
8. [Routing](#8-routing)
9. [Models](#9-models)
10. [Middleware Stack](#10-middleware-stack)
11. [Settings System](#11-settings-system)
12. [Services](#12-services)
13. [Public Embeds](#13-public-embeds)
14. [Billing & Payments](#14-billing--payments)
15. [Integrations & Webhooks](#15-integrations--webhooks)
16. [Email & Notifications](#16-email--notifications)
17. [GDPR Compliance](#17-gdpr-compliance)
18. [Security](#18-security)
19. [Roles & Permissions](#19-roles--permissions)
20. [Environment Configuration](#20-environment-configuration)
21. [Local Development Setup](#21-local-development-setup)

---

## 1. Project Overview

LeadHub is a **white-label, multi-tenant SaaS CRM** platform built on Laravel and Filament. It allows a platform owner (Super Admin) to host multiple independent workspaces (tenants), each with their own leads, pipelines, team, branding, and billing subscription.

### Core Capabilities

| Capability | Description |
|---|---|
| Lead Management | Capture, score, enrich, deduplicate, and pipeline-manage leads |
| Multi-Tenant | Isolated workspaces per customer with custom domain/branding |
| Automation | Trigger-based workflows (email, webhook, field update, etc.) |
| Integrations | OAuth connections to CRM/marketing tools (HubSpot, Pipedrive, etc.) |
| Billing | Multi-gateway subscriptions (Stripe, PayPal, Razorpay, Paystack) |
| Public Embeds | Cross-origin lead forms, chatbot, tracking widgets |
| White-Label | Custom logo, colors, domain per tenant or platform-wide |
| GDPR | Right to erase (Art. 17) + data export (Art. 20) |
| AI SDR | AI-driven outreach enrollment and messaging |
| Meeting Booking | Calendly-style public booking pages per user |

---

## 2. Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                        Apache / XAMPP                           │
│                     (php83, port 8080)                          │
└──────────────────────────┬──────────────────────────────────────┘
                           │
                    public/index.php
                           │
┌──────────────────────────▼──────────────────────────────────────┐
│                      Laravel Kernel                             │
│                                                                 │
│  Middleware Pipeline:                                           │
│  TrustProxies → SecurityHeaders → SetLocale →                  │
│  ResolveTenant → EnforceTenantScope →                          │
│  EnforceLicense → [Auth Middleware]                             │
└────┬───────────────────────┬────────────────────────────────────┘
     │                       │
     ▼                       ▼
┌─────────────┐      ┌───────────────────┐
│ Super Admin │      │   Tenant Admin    │
│   Panel     │      │     Panel         │
│ /super-admin│      │    /admin         │
│             │      │                   │
│ Rose theme  │      │  Indigo theme     │
│ SA users    │      │  Tenant staff     │
└─────────────┘      └───────────────────┘
     │                       │
     └──────────┬────────────┘
                │
        ┌───────▼────────┐
        │   MySQL DB      │
        │  techtaru_crm  │
        └────────────────┘
```

### Technology Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 11 |
| Admin UI | Filament 3 + Livewire 3 |
| Database | MySQL 8 |
| Auth | Laravel Auth + Filament + Spatie Permission |
| 2FA | TOTP (App-based, Filament built-in) |
| Settings | Spatie Laravel-Settings |
| File Storage | Local (configurable to S3) |
| Queue | Sync (configurable to Redis/database) |
| Caching | File (configurable to Redis) |
| PHP Version | 8.3 (Apache: php83, CLI: php_82_backup) |

---

## 3. Directory Structure

```
CRMTechtaru/
├── app/
│   ├── Console/Commands/          # Artisan commands (backup, license, cron)
│   ├── Filament/
│   │   ├── Pages/                 # Tenant admin pages (50+)
│   │   ├── Resources/             # Tenant admin resources (30+)
│   │   ├── Widgets/               # Dashboard widgets
│   │   └── SuperAdmin/
│   │       ├── Pages/             # SA pages (13)
│   │       └── Resources/         # SA resources (7)
│   ├── Http/
│   │   ├── Controllers/           # Web controllers (30)
│   │   └── Middleware/            # Middleware (21)
│   ├── Models/                    # Eloquent models (96)
│   ├── Providers/Filament/        # Panel providers
│   ├── Services/                  # Domain services (37)
│   └── Settings/                  # Spatie settings classes (9)
├── config/                        # Laravel config files
├── database/
│   ├── migrations/                # 160+ migration files
│   └── seeders/                   # Role/permission/demo seeders
├── lang/                          # Translations (en, ar, es, hi)
├── public/                        # Web root (index.php, assets)
├── resources/views/               # Blade templates
├── routes/
│   └── web.php                    # All HTTP routes
├── storage/
│   ├── installed.lock             # Presence = app installed
│   └── logs/laravel.log
└── .env                           # Environment configuration
```

---

## 4. Database Architecture

### Schema Overview

The database `techtaru_crm` contains **160+ tables** across these domains:

#### Core Tenant & User Tables

| Table | Purpose |
|---|---|
| `tenants` | Workspace/customer records |
| `users` | All users (SA + tenant staff) |
| `invitations` | Pending team invitations |
| `user_sessions` | Active session tracking |
| `login_attempts` | Brute-force detection |
| `roles`, `permissions`, `model_has_roles` | Spatie permission tables |

#### Lead Management

| Table | Purpose |
|---|---|
| `leads` | Core lead/contact records |
| `lead_activities` | Timeline of interactions |
| `lead_notes` | Free-text notes |
| `lead_emails` | Email history per lead |
| `lead_messages` | Messaging history |
| `lead_calls` | Call logs |
| `lead_tasks` | To-do items per lead |
| `lead_attachments` | Uploaded files |
| `lead_duplicates` | Detected duplicate pairs |
| `lead_pipeline_stages` | Lead → pipeline stage join |
| `lead_imports` | Bulk import records |

#### Pipeline & Automation

| Table | Purpose |
|---|---|
| `pipelines` | Sales pipelines |
| `automations` | Automation workflow definitions |
| `automation_steps` | Steps within a workflow |
| `automation_runs` | Execution history per lead |
| `email_sequences` | Drip campaign definitions |
| `email_sequence_steps` | Steps in a drip campaign |
| `email_sequence_enrollments` | Lead enrollment tracking |

#### Forms & Embeds

| Table | Purpose |
|---|---|
| `forms` | Lead capture form definitions |
| `form_fields` | Fields within a form |
| `form_submissions` | Submitted form data |
| `lead_capture_widgets` | Embed widget config |
| `tracking_snippets` | Web visitor tracking config |
| `chatbot_configs` | LeadBot chatbot config |
| `chat_conversations` | Chatbot session data |
| `chat_messages` | Individual chat messages |

#### Billing

| Table | Purpose |
|---|---|
| `plans` | Subscription plans |
| `coupons` | Discount codes |
| `invoices` | Invoice records |
| `invoice_items` | Line items |
| `invoice_payments` | Payment records |
| `quotes` | Sales quotes |
| `recurring_invoices` | Recurring invoice schedules |
| `tenant_billing_receipts` | Billing event receipts |
| `processed_billing_events` | Idempotency table |

#### Integrations

| Table | Purpose |
|---|---|
| `integrations` | OAuth integration connections |
| `integration_sync_logs` | Sync history |
| `lead_source_connections` | Inbound source configs |
| `outbound_webhooks` | Webhook endpoint configs |
| `webhook_logs` | Delivery history |
| `calendar_connections` | Google/Outlook calendar OAuth |

---

## 5. Multi-Tenancy

### Isolation Model

Every tenant-scoped model carries a `tenant_id` foreign key. The `ResolveTenant` middleware resolves the current tenant from the request and binds it to the service container:

```php
app('current_tenant') // → Tenant model instance
```

The `EnforceTenantScope` middleware then applies a global Eloquent scope so all queries automatically filter by `tenant_id`.

### Tenant Lifecycle

```
Registration → TenantOnboardingService → OnboardingWizard → Active Workspace
     │                                                              │
     └──► Plan assigned ──► Billing subscription created ──────────┘
```

### Key Middleware (in order)

```
ResolveTenant           → resolves Tenant from domain
EnforceTenantScope      → auto-scopes DB queries
InjectTenantBranding    → loads branding into views
RedirectToOnboarding    → new tenants → wizard
EnforceSubscription     → blocks expired subscriptions
```

### Custom Domain Support

Tenants can configure a custom domain stored in the `tenant_domains` table. `ResolveTenant` matches the incoming `Host` header against this table.

---

## 6. Authentication & Authorization

### User Types

| Type | Role | Panel Access |
|---|---|---|
| Platform owner | `super_admin` | `/super-admin` |
| Workspace owner | `admin` | `/admin` |
| Elevated staff | `manager` | `/admin` |
| Standard staff | `member` | `/admin` |

### Super Admin Identity

The `is_super_admin` column and the Spatie `super_admin` role are **kept in sync** at all times. Always use the model helpers — never check the column or role directly:

```php
$user->isSuperAdmin()          // canonical check (column OR role)
$user->promoteToSuperAdmin()   // syncs both column and role
$user->demoteFromSuperAdmin()  // syncs both column and role
```

### Two-Factor Authentication (2FA)

- Provider: Filament built-in `AppAuthentication` (TOTP)
- Recovery codes supported
- SA-wide `enforce_2fa` flag in `SecuritySettings`
- `Enforce2Fa` middleware redirects unenrolled users to setup page

### Session Management

Active sessions are tracked in `user_sessions`. Suspending a user calls `invalidateSessions()` which wipes all their DB session records immediately.

### Panel Access Gate

```php
// app/Models/User.php
public function canAccessPanel(Panel $panel): bool
{
    if ($panel->getId() === 'super-admin') {
        return $this->isSuperAdmin();
    }
    return true; // all authenticated users can access tenant panel
}
```

---

## 7. Filament Admin Panels

### Super Admin Panel (`/super-admin`)

| Config | Value |
|---|---|
| Path | `/super-admin` |
| Color | Rose |
| Login | `SuperAdminLogin` (custom, with reCAPTCHA v3) |
| Auth Guard | `web` |

**Resources (7):**

| Resource | Purpose |
|---|---|
| `TenantResource` | Create/edit/delete workspaces |
| `UserResource` | Global user management |
| `PlanResource` | Billing plan tiers |
| `CouponResource` | Discount codes |
| `LocaleResource` | Translation language packs |
| `StaticPageResource` | Legal/marketing pages |
| `AffiliateReferralResource` | Affiliate program |

**Pages (13):**

| Page | Purpose |
|---|---|
| `Dashboard` | SA overview and metrics |
| `BrandingPage` | Platform-wide logo/colors/favicon |
| `EmailBrandingPage` | Email header/footer styling |
| `LandingPageEditor` | Public landing page builder |
| `BillingSettingsPage` | Payment gateway configuration |
| `ScriptSettings` | Env var editor, script-level config |
| `SystemHealth` | Server health checks |
| `Updates` | Software update checker |
| `Backups` | System backup management |
| `Modules` | Feature flags / optional modules |
| `RecaptchaSettingsPage` | reCAPTCHA v3 keys |

---

### Tenant Admin Panel (`/admin`)

| Config | Value |
|---|---|
| Path | `/admin` |
| Color | Indigo |
| Login | `WhiteLabelLogin` (custom, with reCAPTCHA v3) |
| 2FA | TOTP via `AppAuthentication` |
| Auth Guard | `web` |

**Navigation Groups (15):**
Leads · Pipeline · Inbox · Forms · Automations · Integrations · Reports · Brand & Domain · Communications · Team & Access · Advanced · Account · Settings · Sales · Templates

**Key Resources (30+):**

| Resource | Purpose |
|---|---|
| `LeadResource` | Core lead CRUD with relation managers |
| `PipelineResource` | Deal stages management |
| `FormResource` | Lead capture form builder |
| `AutomationResource` | Visual workflow builder |
| `IntegrationResource` | OAuth CRM/marketing connections |
| `EmailSequenceResource` | Drip campaign builder |
| `EmailTemplateResource` | Reusable email blocks |
| `InvoiceResource` | Invoice management with PDF |
| `QuoteResource` | Sales quote builder |
| `AiSdrAgentResource` | AI outreach configuration |
| `ChatbotConfigResource` | Embedded chatbot setup |
| `MeetingTypeResource` | Booking page setup |
| `CompanyResource` | Account-level records |
| `ProductResource` | Product/service catalog |
| `OutboundWebhookResource` | Webhook endpoint config |

**Dashboard Widgets:**

- `GettingStartedChecklist` — onboarding progress
- `LeadsStatsOverview` — key metrics
- `RevenueForecastWidget` — pipeline value
- `LeadsOverTimeChart` — volume trend
- `LeadsBySourceChart` — source attribution
- `PipelineDistributionChart` — funnel view
- `LiveLeadFeed` — real-time new leads

---

## 8. Routing

All routes are defined in `routes/web.php`. Key route groups:

### Public Routes (no auth)

```
GET  /                          Marketing landing page
GET  /pricing                   Pricing page
GET  /register                  Tenant self-service registration
GET  /pages/{slug}              Static legal/GDPR pages
GET  /forms/{tenant}/{slug}     Embeddable lead capture form
POST /forms/{tenant}/{slug}     Form submission
GET  /widget/{uuid}/loader.js   Widget embed script
GET  /chatbot/{uuid}/loader.js  Chatbot embed script
GET  /book/{user}/{slug}        Public meeting booking
GET  /{workspace}/{slug}        Public landing page (tenant)
GET  /quote/{token}             Public quote view
GET  /invoice/{token}           Public invoice view
```

### Authenticated Tenant Routes

```
POST /admin/push/subscribe                    PWA push notification subscription
GET  /admin/reports/export/csv                CSV report download
GET  /admin/data-export/download/{token}      GDPR export
GET  /billing/checkout/{gateway}/{plan}       Payment checkout
POST /billing/webhook/{gateway}               Billing webhook receiver
```

### Super Admin Routes

```
GET  /super-admin/impersonate/{tenant}    Impersonate a workspace
GET  /admin/password-setup/{token}        First-time SA password setup
```

### Webhook & Integration Routes

```
GET|POST /webhook/{tenant}/{source}/{token}   Inbound lead webhook
GET|POST /oauth/{source}/{connectionId}        OAuth callback
POST     /voice/call/initiate                  Click-to-call
```

### Tracking & Analytics

```
GET  /tracking/{uuid}/track.js    Visitor tracking script
POST /tracking/{uuid}/hit         Page hit recording
GET  /emails/track/open/{token}   Email open pixel
GET  /emails/track/click/{token}  Email link click
```

---

## 9. Models

### Key Model Relationships

```
Tenant ──< User (team members)
Tenant ──< Lead
Tenant ──< Pipeline ──< PipelineStage ──< Lead
Tenant ──< Form ──< FormField
           Form ──< FormSubmission ──< FormSubmissionValue
Tenant ──< Automation ──< AutomationStep
           Automation ──< AutomationRun
Tenant ──< EmailSequence ──< EmailSequenceStep
           EmailSequence ──< EmailSequenceEnrollment (per Lead)
Lead   ──< LeadEmail, LeadMessage, LeadCall, LeadNote
Lead   ──< LeadTask, LeadActivity, LeadAttachment
Lead   ──< AiSdrEnrollment ──< AiSdrMessage
User   ──< MeetingType ──< MeetingBooking
Tenant ──< Invoice ──< InvoiceItem
           Invoice ──< InvoicePayment
Tenant ──< Integration ──< IntegrationSyncLog
```

### Model Traits

| Trait | Purpose |
|---|---|
| `BelongsToTenant` | Auto-injects tenant_id on create, applies tenant scope |
| `HasFactory` | Test factory support |
| `SoftDeletes` | Soft-delete on Lead and other core entities |
| `HasLocalePreference` | Per-user notification locale |

---

## 10. Middleware Stack

### Full Middleware Reference

| Middleware | Location | Purpose |
|---|---|---|
| `TrustProxies` | Global | CDN/reverse proxy header handling |
| `SecurityHeaders` | Global | X-Frame-Options, CSP, etc. |
| `SetLocale` | Global | Sets locale from `user.language` |
| `ResolveTenant` | Panel | Resolves Tenant from Host header or route param |
| `EnforceTenantScope` | Panel | Global Eloquent query scope by tenant_id |
| `InjectTenantBranding` | Panel | Loads branding into view context |
| `EnforceLicense` | Panel | Blocks UI when license missing/expired (14-day grace) |
| `EnforceSuperAdminIpAllowlist` | SA Panel | Optional IP allowlist (`SA_IP_ALLOWLIST` env) |
| `RequireSuperAdmin` | SA Panel | Gates SA panel: `isSuperAdmin()` must be true |
| `RedirectSuperAdminFromTenantPanel` | Admin Panel | Prevents SA from using tenant panel |
| `EnforceSubscription` | Admin Panel | Blocks when subscription expired/cancelled |
| `EnforceNotSuspended` | Auth | Blocks suspended users |
| `Enforce2Fa` | Auth | Redirects to 2FA setup when required |
| `CheckSeatLimit` | Admin | Validates seat count before adding users |
| `EnforceSecuritySettings` | Admin | Password policy, IP blocks |
| `TrackUserSession` | Admin | Records user logins |
| `RedirectToOnboarding` | Admin | New tenants → onboarding wizard |
| `CheckImpersonation` | SA | Resolves impersonation session |
| `PortalAuth` | Portal | Magic-link auth for customer portal |
| `ApiKeyAuthentication` | API | Validates X-API-Key header |

---

## 11. Settings System

Uses **Spatie Laravel-Settings** (persisted to database as JSON).

| Class | Key Settings |
|---|---|
| `BrandingSettings` | app name, logo/favicon URLs, primary/accent colors, email header/footer |
| `BillingSettings` | enabled gateways, trial days, lifecycle cadence, reminder schedule, affiliate commission |
| `SecuritySettings` | password policy, enforce_2fa, IP allowlist, brute-force lockout config |
| `GeneralSettings` | default currency, default language |
| `LicenseSettings` | purchase code, last_valid_at, grace window expiry |
| `RecaptchaSettings` | v3 site key/secret, toggle per form (login/register/etc) |
| `UpdateSettings` | last check timestamp, available version info |
| `LandingContent` | hero/features/pricing section text, nav/footer links, stats |

### Reading Settings

```php
$branding = app(BrandingSettings::class);
echo $branding->logo_url;
```

### Tenant Settings

Tenant-specific settings (SMTP, branding overrides) are stored via `TenantSettingsRepository` keyed by tenant ID.

---

## 12. Services

### Core Domain Services

| Service | Purpose |
|---|---|
| `LeadIngestionService` | Parse inbound lead data, deduplication check |
| `LeadScoringService` | Calculate score from rules table |
| `LeadMergeService` | Merge two duplicate lead records |
| `LeadDuplicateDetector` | Find potential duplicates by email/phone |
| `LeadActivityService` | Log timeline events for a lead |
| `AutomationTemplateService` | Manage and apply automation templates |
| `IntegrationDispatcher` | Route integration events to the right handler |

### Billing Services

| Service | Purpose |
|---|---|
| `LicenseService` | Validate purchase code; enforce 14-day grace |
| `TenantBillingStateService` | State machine: trial → active → overdue → cancelled |
| `SubscriptionEventService` | Process gateway webhooks (created/updated/cancelled) |
| `CouponService` | Validate and apply discount codes |
| `TaxCalculator` | VAT/tax calculation on invoices |
| `ProrationCalculator` | Prorate charges when changing plans |

### Admin/Platform Services

| Service | Purpose |
|---|---|
| `TenantOnboardingService` | New workspace setup steps |
| `TenantDataExporter` | GDPR Article 20 data export (ZIP) |
| `TenantErasureService` | GDPR Article 17 right-to-erase |
| `BackupService` | Encrypted DB + file backups |
| `UpdaterService` | Check and apply software updates |
| `EnvEditor` | Safe `.env` variable read/write |
| `ModuleManagerService` | Toggle optional feature modules |

### Communication Services

| Service | Purpose |
|---|---|
| `BrandedEmailRenderer` | Renders email with tenant/SA branding |
| `EmailDomainVerificationService` | DKIM/SPF setup for custom email domains |
| `AiEmailComposerService` | AI-powered email drafting |
| `InvitationService` | Generate and send team invitations |
| `RecaptchaService` | Verify reCAPTCHA v3 tokens |

---

## 13. Public Embeds

Three types of cross-origin embeds (all CORS-exempt, token-validated):

### Lead Capture Widget

```
GET  /widget/{uuid}/loader.js      Embed script
POST /widget/{uuid}/submit         Form submit endpoint
GET  /widget/{uuid}/preview        Preview (no submit)
```

### LeadBot Chatbot

```
GET     /chatbot/{uuid}/loader.js  Embed script
POST    /chatbot/{uuid}/chat       Chat message endpoint
OPTIONS /chatbot/{uuid}/preflight  CORS preflight
GET     /chatbot/{uuid}/preview    Preview
```

### Web Visitor Tracking

```
GET  /tracking/{uuid}/track.js    Tracking script (injected via <script>)
POST /tracking/{uuid}/hit         Page view recording
POST /tracking/{uuid}/identify    Lead identification
```

All three are throttled separately (rate limits apply per UUID/IP) and require no user session.

---

## 14. Billing & Payments

### Supported Gateways

| Gateway | Checkout | Webhooks | Portal |
|---|---|---|---|
| Stripe | Yes | Yes | Customer Portal |
| PayPal | Yes | Yes | — |
| Razorpay | Yes | Yes | — |
| Paystack | Yes | Yes | — |
| Manual | Yes | — | — |

### Billing Flow

```
/billing/checkout/{gateway}/{plan}
         │
         ▼
   Gateway Checkout
         │
         ▼ (success redirect or webhook)
POST /billing/webhook/{gateway}
         │
         ▼
SubscriptionEventService.handle()
         │
         ▼
TenantBillingStateService.transition()
         │
         ├── created   → activate tenant, assign plan
         ├── updated   → update seats/limits
         ├── paused    → restrict access
         └── cancelled → downgrade/deactivate
```

### Idempotency

All billing webhook events are recorded in `processed_billing_events` before processing. Duplicate gateway events (common with Stripe) are silently ignored.

### Invoice / Quote

- Quotes can be accepted/declined via a public signed URL (`/quote/{token}`)
- Accepted quotes can be converted to Invoices
- Invoices support PDF export and online payment links
- Recurring invoices auto-generate on a configured schedule

---

## 15. Integrations & Webhooks

### Inbound Lead Webhooks

```
POST /webhook/{tenant}/{source}/{token}
```

- Supports Zapier, Make.com, and any HTTP source
- Rate limited: 120 requests/minute
- Payload mapped to Lead fields via `LeadIngestionService`

### OAuth Integrations

The `Integration` model stores OAuth tokens and field mappings for:

- HubSpot, Pipedrive, Salesforce (CRM sync)
- Mailchimp, ActiveCampaign (marketing)
- Google Calendar, Outlook Calendar (meeting sync)

OAuth flow:

```
/integrations/oauth/{type}         → redirect to provider
/oauth/{source}/{connectionId}     → callback, store token
```

### Outbound Webhooks

Configured via `OutboundWebhookResource`. When automation triggers an outbound webhook step, `OutboundWebhookDeliveryJob` sends a signed POST to the configured URL. Delivery history stored in `webhook_logs`.

---

## 16. Email & Notifications

### Email Rendering

All outbound emails are rendered via `BrandedEmailRenderer` which applies:

1. Tenant branding (logo, primary color, footer)
2. SA-global branding fallback
3. Bundled default template

### Notification Types

| Type | Delivery |
|---|---|
| Team invitations | Email |
| Lead assignment | In-app + Email |
| Automation triggers | Email (via sequence steps) |
| Billing events | Email |
| Scheduled reports | Email (PDF/CSV attachment) |
| Push notifications | PWA service worker |

### Password Reset

Uses a synchronous `Mail::send` (not queued) for shared-hosting compatibility where queues may not run.

### Notification Preferences

Users control per-type email/push preferences via `notification_preferences` table. Digest mode available (batch multiple notifications into one email).

---

## 17. GDPR Compliance

### Right to Erasure (Article 17)

Initiated from tenant admin → Data Privacy page. `TenantErasureService` handles full workspace deletion:

- Deletes all leads, attachments, activity history
- Removes user records and sessions
- Schedules tenant record deletion after confirmation window

### Data Export (Article 20)

```
GET /admin/data-export/download/{token}
```

- Generates a ZIP file containing all tenant data in CSV/JSON
- Signed URL with 24-hour TTL
- Scoped to current tenant (cannot cross-tenant)

### Consent Tracking

- Form submissions store consent timestamp
- Lead records carry GDPR flags (consent given, deletion requested)
- `deletion_requested_at` on tenants table for workspace deletion workflow

---

## 18. Security

### License Enforcement

`EnforceLicense` middleware blocks panel access when:

- `LEADHUB_LICENSE_KEY` is missing from `.env`
- License verification failed AND grace window (default 14 days) has expired

Always allows access to:

- `/license-required` page
- License settings pages
- Logout routes
- Livewire internal endpoints

### IP Allowlist (Super Admin)

Set `SA_IP_ALLOWLIST=192.168.1.1,10.0.0.0/8` in `.env` to restrict SA panel access to specific IPs.

### reCAPTCHA v3

Applied to login, registration, and form submission endpoints. Keys configured in `RecaptchaSettings`. Each endpoint has a separate action name (`login`, `sa_login`, `register`, `form_submit`).

### Brute-Force Protection

`login_attempts` table tracks failed logins. `SecuritySettings` configures:

- Max attempts before lockout
- Lockout duration
- Block by IP or by email

### Security Headers

`SecurityHeaders` middleware adds:

- `X-Frame-Options: SAMEORIGIN`
- `X-Content-Type-Options: nosniff`
- `Content-Security-Policy` (exemptions for widget/chatbot iframes)
- `Referrer-Policy`

---

## 19. Roles & Permissions

### Role Hierarchy

```
super_admin   Platform owner — all permissions, SA panel access
    │
    admin     Tenant owner — all permissions within workspace
        │
        manager   Staff — leads/pipeline/forms/automations + limited team
            │
            member    Staff — view/create/edit leads + limited read access
```

### Permission Matrix

| Permission | super_admin | admin | manager | member |
|---|---|---|---|---|
| leads.view | ✓ | ✓ | ✓ | ✓ |
| leads.create | ✓ | ✓ | ✓ | ✓ |
| leads.edit | ✓ | ✓ | ✓ | ✓ |
| leads.delete | ✓ | ✓ | ✓ | — |
| leads.export | ✓ | ✓ | ✓ | — |
| leads.import | ✓ | ✓ | ✓ | — |
| leads.assign | ✓ | ✓ | ✓ | ✓ |
| pipeline.view | ✓ | ✓ | ✓ | ✓ |
| pipeline.manage | ✓ | ✓ | ✓ | — |
| forms.view | ✓ | ✓ | ✓ | ✓ |
| forms.create/edit/delete | ✓ | ✓ | ✓ | — |
| automations.view | ✓ | ✓ | ✓ | ✓ |
| automations.create/edit/delete | ✓ | ✓ | ✓ | — |
| integrations.view | ✓ | ✓ | ✓ | ✓ |
| integrations.manage | ✓ | ✓ | ✓ | — |
| reports.view | ✓ | ✓ | ✓ | ✓ |
| reports.export | ✓ | ✓ | ✓ | — |
| team.view | ✓ | ✓ | ✓ | — |
| team.invite | ✓ | ✓ | ✓ | — |
| team.manage | ✓ | ✓ | — | — |
| settings.view | ✓ | ✓ | ✓ | — |
| settings.manage | ✓ | ✓ | — | — |
| api_keys.manage | ✓ | ✓ | — | — |
| webhooks.manage | ✓ | ✓ | — | — |

---

## 20. Environment Configuration

Key `.env` variables:

```env
# Application
APP_NAME="LeadHub"
APP_ENV=local                          # local | production
APP_KEY=base64:...                     # Generated encryption key
APP_DEBUG=true                         # false in production
APP_URL=http://localhost:8080          # Must match actual URL for Livewire

# License
LEADHUB_APP_NAME="LeadHub"
LEADHUB_LICENSE_KEY=your-key-here     # Purchase code from CodeCanyon
LEADHUB_DEMO_MODE=false               # true bypasses license check

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=techtaru_crm
DB_USERNAME=root
DB_PASSWORD=

# Session / Cache / Queue
SESSION_DRIVER=file                    # file | database | redis
CACHE_STORE=file                       # file | redis
QUEUE_CONNECTION=sync                  # sync | database | redis

# Mail
MAIL_MAILER=log                        # log | smtp | mailgun | ses
MAIL_FROM_ADDRESS="noreply@localhost"
MAIL_FROM_NAME="LeadHub"

# Security (optional)
SA_IP_ALLOWLIST=                       # Comma-separated IPs/CIDRs for SA panel

# CRON
CRON_SECRET=...                        # Secret for cron.php endpoint
```

---

## 21. Local Development Setup

### Prerequisites

- XAMPP with Apache (php83) and MySQL running
- PHP 8.3 CLI at `C:\xampp\php83\php.exe` (for Artisan commands)

### URLs

| Purpose | URL |
|---|---|
| App (port 8080 vhost) | `http://localhost:8080` |
| Super Admin Login | `http://localhost:8080/super-admin/login` |
| Tenant Admin Login | `http://localhost:8080/admin/login` |
| phpMyAdmin | `http://localhost/phpmyadmin` |

### Default Credentials

| Field | Value |
|---|---|
| Email | `info@techtaru.com` |
| Password | `Admin@1234` |
| Role | `super_admin` |

### Running Artisan Commands

```bash
C:\xampp\php83\php.exe artisan migrate
C:\xampp\php83\php.exe artisan db:seed
C:\xampp\php83\php.exe artisan config:clear
C:\xampp\php83\php.exe artisan cache:clear
C:\xampp\php83\php.exe artisan view:clear
C:\xampp\php83\php.exe artisan route:list
```

### Installation Check

The file `storage/installed.lock` must exist for the app to skip the installer. If missing, the app redirects all requests to `public/install.php`.

### Cron Jobs

```
# Via curl endpoint
* * * * * curl -s http://localhost:8080/cron.php?secret=YOUR_CRON_SECRET

# Via Artisan
* * * * * C:\xampp\php83\php.exe C:\xampp\htdocs\CRMTechtaru\artisan schedule:run
```

---

*Documentation generated: 2026-06-29*  
*Project: LeadHub SaaS CRM — CRMTechtaru*
