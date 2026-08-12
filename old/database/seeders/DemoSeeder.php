<?php

/**
 * =============================================================================
 * LeadHub Demo Seeder
 * =============================================================================
 *
 * Creates comprehensive demo data for every area of the LeadHub application.
 * Designed to populate dashboards, charts, reports, and all tables with
 * realistic-looking data.
 *
 * HOW TO RUN (since CLI has PHP 8.2 but app requires PHP 8.4):
 *
 *   Option A — Create a temporary route in routes/web.php:
 *
 *       Route::get('/run-demo-seed', function () {
 *           Artisan::call('db:seed', ['--class' => 'DemoSeeder']);
 *           return 'Demo data seeded!';
 *       });
 *
 *   Option B — Via Tinker in the browser (if available):
 *
 *       Artisan::call('db:seed', ['--class' => 'DemoSeeder']);
 *
 *   Option C — Add to DatabaseSeeder::run() and re-run migrate:fresh --seed
 *              from an environment with PHP 8.4.
 *
 * IDEMPOTENCY: The seeder checks for existing demo tenants by slug. If found,
 * it skips execution to avoid duplicate data.
 *
 * =============================================================================
 */

namespace Database\Seeders;

use App\Models\ApiKey;
use App\Models\AuditLog;
use App\Models\Automation;
use App\Models\AutomationRun;
use App\Models\AutomationStep;
use App\Models\EmailTemplate;
use App\Models\Form;
use App\Models\FormField;
use App\Models\FormSubmission;
use App\Models\FormSubmissionValue;
use App\Models\Integration;
use App\Models\IntegrationSyncLog;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadCaptureWidget;
use App\Models\LeadDuplicate;
use App\Models\LeadImport;
use App\Models\LeadNote;
use App\Models\LeadScoringRule;
use App\Models\LeadSourceConnection;
use App\Models\LeadTask;
use App\Models\NotificationPreference;
use App\Models\OutboundWebhook;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\ScheduledReport;
use App\Models\Tag;
use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Models\User;
use App\Models\WebhookDelivery;
use App\Models\WebhookLog;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DemoSeeder extends Seeder
{
    // -------------------------------------------------------------------------
    // Date helpers
    // -------------------------------------------------------------------------
    public Carbon $now;

    private function randomPastDate(int $maxDaysAgo = 90): Carbon
    {
        return $this->now->copy()->subDays(rand(1, $maxDaysAgo))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
    }

    private function randomRecentDate(int $maxDaysAgo = 30): Carbon
    {
        return $this->now->copy()->subDays(rand(0, $maxDaysAgo))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
    }

    // -------------------------------------------------------------------------
    // Data pools
    // -------------------------------------------------------------------------
    private array $firstNames = [
        'James', 'Mary', 'Robert', 'Patricia', 'John', 'Jennifer', 'Michael', 'Linda',
        'David', 'Elizabeth', 'William', 'Barbara', 'Richard', 'Susan', 'Joseph', 'Jessica',
        'Thomas', 'Sarah', 'Christopher', 'Karen', 'Charles', 'Lisa', 'Daniel', 'Nancy',
        'Matthew', 'Betty', 'Anthony', 'Margaret', 'Mark', 'Sandra', 'Donald', 'Ashley',
        'Steven', 'Kimberly', 'Paul', 'Emily', 'Andrew', 'Donna', 'Joshua', 'Michelle',
        'Kenneth', 'Carol', 'Kevin', 'Amanda', 'Brian', 'Dorothy', 'George', 'Melissa',
        'Timothy', 'Deborah', 'Ronald', 'Stephanie', 'Edward', 'Rebecca', 'Jason', 'Sharon',
        'Jeffrey', 'Laura', 'Ryan', 'Cynthia', 'Jacob', 'Kathleen', 'Gary', 'Amy',
        'Nicholas', 'Angela', 'Eric', 'Shirley', 'Jonathan', 'Anna', 'Stephen', 'Brenda',
        'Larry', 'Pamela', 'Justin', 'Emma', 'Scott', 'Nicole', 'Brandon', 'Helen',
        'Benjamin', 'Samantha', 'Samuel', 'Katherine', 'Raymond', 'Christine', 'Gregory', 'Debra',
        'Frank', 'Rachel', 'Alexander', 'Carolyn', 'Patrick', 'Janet', 'Jack', 'Catherine',
        'Sofia', 'Olivia', 'Noah', 'Liam', 'Ethan', 'Ava', 'Mason', 'Isabella',
        'Lucas', 'Mia', 'Oliver', 'Charlotte', 'Elijah', 'Amelia', 'Aiden', 'Harper',
    ];

    private array $lastNames = [
        'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis',
        'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson',
        'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin', 'Lee', 'Perez', 'Thompson',
        'White', 'Harris', 'Sanchez', 'Clark', 'Ramirez', 'Lewis', 'Robinson', 'Walker',
        'Young', 'Allen', 'King', 'Wright', 'Scott', 'Torres', 'Nguyen', 'Hill',
        'Flores', 'Green', 'Adams', 'Nelson', 'Baker', 'Hall', 'Rivera', 'Campbell',
        'Mitchell', 'Carter', 'Roberts', 'Gomez', 'Phillips', 'Evans', 'Turner', 'Diaz',
        'Parker', 'Cruz', 'Edwards', 'Collins', 'Reyes', 'Stewart', 'Morris', 'Morales',
        'Murphy', 'Cook', 'Rogers', 'Gutierrez', 'Ortiz', 'Morgan', 'Cooper', 'Peterson',
        'Bailey', 'Reed', 'Kelly', 'Howard', 'Ramos', 'Kim', 'Cox', 'Ward',
        'Richardson', 'Watson', 'Brooks', 'Chavez', 'Wood', 'James', 'Bennett', 'Gray',
        'Mendoza', 'Ruiz', 'Hughes', 'Price', 'Alvarez', 'Castillo', 'Sanders', 'Patel',
        'Myers', 'Long', 'Ross', 'Foster', 'Jimenez', 'Powell', 'Jenkins', 'Perry',
    ];

    private array $companies = [
        'Acme Corp', 'Globex Industries', 'Initech Solutions', 'Umbrella Corp', 'Stark Industries',
        'Wayne Enterprises', 'Cyberdyne Systems', 'Soylent Corp', 'Massive Dynamic', 'Hooli',
        'Pied Piper', 'Dunder Mifflin', 'Sterling Cooper', 'Wonka Industries', 'Oscorp',
        'LexCorp', 'Weyland-Yutani', 'Tyrell Corp', 'Aperture Science', 'Black Mesa',
        'Vandelay Industries', 'Prestige Worldwide', 'InGen', 'OsCorp Technologies', 'Stark Tech',
        'BlueStar Aviation', 'Zenith Media', 'Pinnacle Consulting', 'Nexus Digital', 'Vertex Software',
        'Summit Healthcare', 'Atlas Logistics', 'Prism Analytics', 'Beacon Financial', 'Forge Manufacturing',
        'Catalyst Marketing', 'Evergreen Education', 'Titan Construction', 'Meridian Energy', 'Horizon Labs',
        'Quantum Dynamics', 'Silverline Insurance', 'CloudBridge IT', 'NovaTech', 'BrightPath HR',
        'Redwood Capital', 'Echo Ventures', 'Pulse Digital', 'Sapphire Solutions', 'Granite Partners',
    ];

    private array $jobTitles = [
        'CEO', 'CTO', 'CFO', 'COO', 'VP of Marketing', 'VP of Sales', 'VP of Engineering',
        'Marketing Manager', 'Sales Manager', 'Product Manager', 'Project Manager',
        'Software Engineer', 'DevOps Engineer', 'Data Analyst', 'Business Analyst',
        'HR Director', 'Operations Manager', 'Account Executive', 'Consultant',
        'Director of Operations', 'Head of Growth', 'Marketing Coordinator',
        'Sales Representative', 'Customer Success Manager', 'IT Manager',
        'Founder', 'Co-Founder', 'Managing Director', 'Partner', 'Freelancer',
    ];

    private array $industries = [
        'Technology', 'Healthcare', 'Finance', 'Education', 'Manufacturing',
        'Retail', 'Real Estate', 'Construction', 'Marketing', 'Legal',
        'Consulting', 'Logistics', 'Energy', 'Media', 'Hospitality',
        'Insurance', 'Automotive', 'Telecommunications', 'Agriculture', 'SaaS',
    ];

    private array $companySizes = [
        '1-10', '11-50', '51-200', '201-500', '501-1000', '1001-5000', '5000+',
    ];

    private array $countries = [
        'United States', 'United Kingdom', 'Canada', 'Germany', 'France',
        'Australia', 'Netherlands', 'Spain', 'Italy', 'Sweden',
        'Switzerland', 'Belgium', 'Greece', 'Portugal', 'Ireland',
        'Norway', 'Denmark', 'Finland', 'Austria', 'Poland',
    ];

    private array $sources = [
        'meta', 'instagram', 'tiktok', 'linkedin', 'whatsapp',
        'google_ads', 'youtube', 'email', 'typeform', 'web_form',
        'manual', 'calendly', 'jotform', 'twitter',
    ];

    private array $statuses = ['new', 'contacted', 'qualified', 'lost', 'won'];

    // Weighted status distribution for realism
    private function randomStatus(): string
    {
        $weights = ['new' => 30, 'contacted' => 25, 'qualified' => 20, 'lost' => 12, 'won' => 13];
        $rand = rand(1, 100);
        $cumulative = 0;
        foreach ($weights as $status => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return $status;
            }
        }
        return 'new';
    }

    private function randomSource(): string
    {
        // Weight popular sources more
        $weighted = array_merge(
            array_fill(0, 4, 'meta'),
            array_fill(0, 3, 'google_ads'),
            array_fill(0, 3, 'linkedin'),
            array_fill(0, 2, 'web_form'),
            array_fill(0, 2, 'instagram'),
            array_fill(0, 2, 'manual'),
            array_fill(0, 1, 'tiktok'),
            array_fill(0, 1, 'youtube'),
            array_fill(0, 1, 'email'),
            array_fill(0, 1, 'typeform'),
            array_fill(0, 1, 'whatsapp'),
            array_fill(0, 1, 'calendly'),
            array_fill(0, 1, 'jotform'),
            array_fill(0, 1, 'twitter'),
        );
        return $weighted[array_rand($weighted)];
    }

    private function pick(array $arr): mixed
    {
        return $arr[array_rand($arr)];
    }

    private function randomPhone(): string
    {
        return '+1' . rand(200, 999) . rand(100, 999) . rand(1000, 9999);
    }

    private function randomEmail(string $first, string $last, string $company): string
    {
        $domain = Str::slug(str_replace([' ', "'"], ['', ''], $company), '') . '.com';
        $sep = $this->pick(['.', '_', '']);
        return strtolower($first) . $sep . strtolower($last) . '@' . $domain;
    }

    // =========================================================================
    // MAIN RUN
    // =========================================================================
    /**
     * Seed demo data into an existing tenant.
     * Usage: (new DemoSeeder)->seedExistingTenant($tenant, $users);
     */
    public function seedExistingTenant(Tenant $tenant, array $users, int $leadCount = 250): void
    {
        $this->now = Carbon::now();
        $this->ensureRolesExist();

        $adminUser = $users[0];

        [$pipelines, $stagesByPipeline] = $this->seedPipelines($tenant);
        $tags = $this->seedTags($tenant);
        $sourceConnections = $this->seedSourceConnections($tenant);
        $leads = $this->seedLeads($tenant, $users, $pipelines, $stagesByPipeline, $leadCount);
        $this->seedLeadTags($leads, $tags);
        $this->seedLeadActivities($tenant, $leads, $users);
        $this->seedLeadNotes($tenant, $leads, $users);
        $this->seedLeadTasks($tenant, $leads, $users);
        $this->seedForms($tenant, $pipelines, $stagesByPipeline, $leads);
        $this->seedAutomations($tenant, $leads, $tags, $pipelines, $stagesByPipeline);
        $this->seedEmailTemplates($tenant);
        $this->seedLeadScoringRules($tenant);
        $this->seedApiKeys($tenant);
        $this->seedOutboundWebhooks($tenant, $leads);
        $this->seedIntegrations($tenant, $leads);
        $this->seedWebhookLogs($tenant, $sourceConnections);
        $this->seedNotificationPreferences($users);
        $this->seedScheduledReports($tenant, $adminUser);
        $this->seedLeadImports($tenant, $adminUser);
        $this->seedLeadDuplicates($tenant, $leads);
        $this->seedLeadCaptureWidgets($tenant, $pipelines, $stagesByPipeline);
        $this->seedAuditLogs($tenant, $users, $leads);
        $this->seedTenantSettings($tenant);
        $this->seedTenantDomains($tenant);
    }

    public function run(): void
    {
        $this->now = Carbon::now();

        // Idempotency check
        if (Tenant::where('slug', 'acme-digital')->exists()) {
            $this->command?->warn('Demo data already exists (tenant "acme-digital" found). Skipping.');
            return;
        }

        $this->command?->info('Seeding demo data...');

        // Ensure roles exist (DatabaseSeeder should have run first)
        $this->ensureRolesExist();

        // Create tenants and users
        [$tenants, $usersByTenant] = $this->seedTenantsAndUsers();

        // Per-tenant data
        foreach ($tenants as $tenant) {
            $users = $usersByTenant[$tenant->id];
            $adminUser = $users[0];
            $allUserIds = collect($users)->pluck('id')->toArray();

            $this->command?->info("  Seeding data for tenant: {$tenant->name}");

            // Pipelines & stages
            [$pipelines, $stagesByPipeline] = $this->seedPipelines($tenant);

            // Tags
            $tags = $this->seedTags($tenant);

            // Source connections
            $sourceConnections = $this->seedSourceConnections($tenant);

            // Leads (200+ for main tenant, fewer for others)
            $leadCount = $tenant->slug === 'acme-digital' ? 250 : 80;
            $leads = $this->seedLeads($tenant, $users, $pipelines, $stagesByPipeline, $leadCount);

            // Attach tags to leads
            $this->seedLeadTags($leads, $tags);

            // Lead activities
            $this->seedLeadActivities($tenant, $leads, $users);

            // Lead notes
            $this->seedLeadNotes($tenant, $leads, $users);

            // Lead tasks
            $this->seedLeadTasks($tenant, $leads, $users);

            // Forms & submissions
            $this->seedForms($tenant, $pipelines, $stagesByPipeline, $leads);

            // Automations
            $this->seedAutomations($tenant, $leads, $tags, $pipelines, $stagesByPipeline);

            // Email templates
            $this->seedEmailTemplates($tenant);

            // Lead scoring rules
            $this->seedLeadScoringRules($tenant);

            // API keys
            $this->seedApiKeys($tenant);

            // Outbound webhooks & deliveries
            $this->seedOutboundWebhooks($tenant, $leads);

            // Integrations & sync logs
            $this->seedIntegrations($tenant, $leads);

            // Webhook logs (inbound)
            $this->seedWebhookLogs($tenant, $sourceConnections);

            // Notification preferences
            $this->seedNotificationPreferences($users);

            // Scheduled reports
            $this->seedScheduledReports($tenant, $adminUser);

            // Lead imports
            $this->seedLeadImports($tenant, $adminUser);

            // Lead duplicates
            $this->seedLeadDuplicates($tenant, $leads);

            // Lead capture widgets
            $this->seedLeadCaptureWidgets($tenant, $pipelines, $stagesByPipeline);

            // Audit logs
            $this->seedAuditLogs($tenant, $users, $leads);

            // Tenant settings & branding
            $this->seedTenantSettings($tenant);

            // Tenant domains
            $this->seedTenantDomains($tenant);
        }

        $this->command?->info('Demo data seeded successfully!');
    }

    // =========================================================================
    // ROLES
    // =========================================================================
    private function ensureRolesExist(): void
    {
        foreach (['super_admin', 'admin', 'manager', 'agent', 'viewer'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }

    // =========================================================================
    // TENANTS & USERS
    // =========================================================================
    private function seedTenantsAndUsers(): array
    {
        $tenantDefs = [
            [
                'name' => 'Acme Digital Agency',
                'slug' => 'acme-digital',
                'plan' => 'professional',
                'subscription_status' => 'active',
                'max_seats' => 10,
                'users' => [
                    ['name' => 'Alex Thompson', 'email' => 'alex@acme-digital.com', 'role' => 'admin'],
                    ['name' => 'Sarah Chen', 'email' => 'sarah@acme-digital.com', 'role' => 'manager'],
                    ['name' => 'Marcus Johnson', 'email' => 'marcus@acme-digital.com', 'role' => 'agent'],
                    ['name' => 'Elena Rodriguez', 'email' => 'elena@acme-digital.com', 'role' => 'agent'],
                    ['name' => 'David Park', 'email' => 'david@acme-digital.com', 'role' => 'agent'],
                ],
            ],
            [
                'name' => 'Pinnacle Real Estate Group',
                'slug' => 'pinnacle-realty',
                'plan' => 'business',
                'subscription_status' => 'active',
                'max_seats' => 8,
                'users' => [
                    ['name' => 'Rachel Foster', 'email' => 'rachel@pinnacle-realty.com', 'role' => 'admin'],
                    ['name' => 'Tom Bradley', 'email' => 'tom@pinnacle-realty.com', 'role' => 'manager'],
                    ['name' => 'Mia Santos', 'email' => 'mia@pinnacle-realty.com', 'role' => 'agent'],
                    ['name' => 'Jake Wilson', 'email' => 'jake@pinnacle-realty.com', 'role' => 'agent'],
                ],
            ],
            [
                'name' => 'NovaTech Solutions',
                'slug' => 'novatech',
                'plan' => 'trial',
                'subscription_status' => 'trial',
                'max_seats' => 5,
                'users' => [
                    ['name' => 'Chris Anderson', 'email' => 'chris@novatech-solutions.com', 'role' => 'admin'],
                    ['name' => 'Lisa Morgan', 'email' => 'lisa@novatech-solutions.com', 'role' => 'agent'],
                    ['name' => 'Kevin Patel', 'email' => 'kevin@novatech-solutions.com', 'role' => 'agent'],
                ],
            ],
        ];

        $tenants = [];
        $usersByTenant = [];

        foreach ($tenantDefs as $def) {
            // Create owner first (first user)
            $ownerDef = $def['users'][0];
            $owner = User::create([
                'name' => $ownerDef['name'],
                'email' => $ownerDef['email'],
                'password' => Hash::make('password'),
                'timezone' => 'America/New_York',
                'email_verified_at' => $this->now->copy()->subDays(60),
                'is_super_admin' => false,
            ]);

            // Demo seeder bypasses TenantProvisioningObserver (which
            // auto-seeds default email templates into every newly-
            // created tenant). The demo refresh has its own template
            // fixtures and shouldn't get the starter set duplicated
            // on every refresh cycle. seedFor() is idempotent so a
            // collision wouldn't be DB-fatal, but skipping the
            // observer keeps the demo's seeded state predictable.
            $tenant = Tenant::withoutEvents(fn () => Tenant::create([
                'name' => $def['name'],
                'slug' => $def['slug'],
                'owner_id' => $owner->id,
                'max_seats' => $def['max_seats'],
                'seat_count' => count($def['users']),
                'plan' => $def['plan'],
                'subscription_status' => $def['subscription_status'],
                'trial_ends_at' => $def['subscription_status'] === 'trial'
                    ? $this->now->copy()->addDays(14)
                    : null,
                'subscription_ends_at' => $def['subscription_status'] === 'active'
                    ? $this->now->copy()->addYear()
                    : null,
                'active' => true,
            ]));

            // Update owner's tenant_id
            $owner->update(['tenant_id' => $tenant->id]);
            $owner->assignRole($ownerDef['role']);

            // Attach owner to tenant pivot
            $tenant->users()->attach($owner->id, ['role' => $ownerDef['role']]);

            $users = [$owner];

            // Create remaining users
            foreach (array_slice($def['users'], 1) as $userDef) {
                $user = User::create([
                    'name' => $userDef['name'],
                    'email' => $userDef['email'],
                    'password' => Hash::make('password'),
                    'tenant_id' => $tenant->id,
                    'timezone' => $this->pick(['America/New_York', 'America/Chicago', 'America/Los_Angeles', 'Europe/London', 'Europe/Berlin']),
                    'email_verified_at' => $this->now->copy()->subDays(rand(10, 50)),
                    'is_super_admin' => false,
                ]);
                $user->assignRole($userDef['role']);
                $tenant->users()->attach($user->id, ['role' => $userDef['role']]);
                $users[] = $user;
            }

            $tenants[] = $tenant;
            $usersByTenant[$tenant->id] = $users;
        }

        return [$tenants, $usersByTenant];
    }

    // =========================================================================
    // PIPELINES & STAGES
    // =========================================================================
    public function seedPipelines(Tenant $tenant): array
    {
        $pipelineDefs = [
            [
                'name' => 'Sales Pipeline',
                'description' => 'Main sales pipeline for tracking leads from initial contact to close.',
                'is_default' => true,
                'stages' => [
                    ['name' => 'New Lead', 'color' => '#6366f1', 'sort_order' => 0, 'is_won' => false, 'is_lost' => false],
                    ['name' => 'Contacted', 'color' => '#3b82f6', 'sort_order' => 1, 'is_won' => false, 'is_lost' => false],
                    ['name' => 'Qualified', 'color' => '#f59e0b', 'sort_order' => 2, 'is_won' => false, 'is_lost' => false],
                    ['name' => 'Proposal Sent', 'color' => '#8b5cf6', 'sort_order' => 3, 'is_won' => false, 'is_lost' => false],
                    ['name' => 'Negotiation', 'color' => '#ec4899', 'sort_order' => 4, 'is_won' => false, 'is_lost' => false],
                    ['name' => 'Won', 'color' => '#22c55e', 'sort_order' => 5, 'is_won' => true, 'is_lost' => false],
                    ['name' => 'Lost', 'color' => '#ef4444', 'sort_order' => 6, 'is_won' => false, 'is_lost' => true],
                ],
            ],
            [
                'name' => 'Onboarding',
                'description' => 'Pipeline for new customer onboarding process.',
                'is_default' => false,
                'stages' => [
                    ['name' => 'Welcome', 'color' => '#6366f1', 'sort_order' => 0, 'is_won' => false, 'is_lost' => false],
                    ['name' => 'Setup Call', 'color' => '#3b82f6', 'sort_order' => 1, 'is_won' => false, 'is_lost' => false],
                    ['name' => 'Configuration', 'color' => '#f59e0b', 'sort_order' => 2, 'is_won' => false, 'is_lost' => false],
                    ['name' => 'Training', 'color' => '#8b5cf6', 'sort_order' => 3, 'is_won' => false, 'is_lost' => false],
                    ['name' => 'Active', 'color' => '#22c55e', 'sort_order' => 4, 'is_won' => true, 'is_lost' => false],
                ],
            ],
            [
                'name' => 'Partnership Pipeline',
                'description' => 'Tracking potential partnership opportunities.',
                'is_default' => false,
                'stages' => [
                    ['name' => 'Inquiry', 'color' => '#6366f1', 'sort_order' => 0, 'is_won' => false, 'is_lost' => false],
                    ['name' => 'Evaluation', 'color' => '#f59e0b', 'sort_order' => 1, 'is_won' => false, 'is_lost' => false],
                    ['name' => 'Terms Discussion', 'color' => '#8b5cf6', 'sort_order' => 2, 'is_won' => false, 'is_lost' => false],
                    ['name' => 'Agreement', 'color' => '#22c55e', 'sort_order' => 3, 'is_won' => true, 'is_lost' => false],
                    ['name' => 'Declined', 'color' => '#ef4444', 'sort_order' => 4, 'is_won' => false, 'is_lost' => true],
                ],
            ],
        ];

        $pipelines = [];
        $stagesByPipeline = [];

        foreach ($pipelineDefs as $pDef) {
            $pipeline = Pipeline::create([
                'tenant_id' => $tenant->id,
                'name' => $pDef['name'],
                'description' => $pDef['description'],
                'is_default' => $pDef['is_default'],
            ]);
            $pipelines[] = $pipeline;

            $stages = [];
            foreach ($pDef['stages'] as $sDef) {
                $stages[] = PipelineStage::create(array_merge($sDef, [
                    'pipeline_id' => $pipeline->id,
                    'tenant_id' => $tenant->id,
                ]));
            }
            $stagesByPipeline[$pipeline->id] = $stages;
        }

        return [$pipelines, $stagesByPipeline];
    }

    // =========================================================================
    // TAGS
    // =========================================================================
    public function seedTags(Tenant $tenant): array
    {
        $tagDefs = [
            ['name' => 'Hot Lead', 'color' => '#ef4444'],
            ['name' => 'Cold Lead', 'color' => '#3b82f6'],
            ['name' => 'Warm Lead', 'color' => '#f59e0b'],
            ['name' => 'VIP', 'color' => '#8b5cf6'],
            ['name' => 'Follow-up', 'color' => '#ec4899'],
            ['name' => 'Nurture', 'color' => '#14b8a6'],
            ['name' => 'High Budget', 'color' => '#22c55e'],
            ['name' => 'Enterprise', 'color' => '#6366f1'],
            ['name' => 'SMB', 'color' => '#a855f7'],
            ['name' => 'Referral', 'color' => '#06b6d4'],
            ['name' => 'Re-engaged', 'color' => '#84cc16'],
            ['name' => 'Decision Maker', 'color' => '#f97316'],
            ['name' => 'Urgent', 'color' => '#dc2626'],
            ['name' => 'Demo Requested', 'color' => '#2563eb'],
            ['name' => 'Price Sensitive', 'color' => '#d97706'],
        ];

        $tags = [];
        foreach ($tagDefs as $def) {
            $tags[] = Tag::create(array_merge($def, ['tenant_id' => $tenant->id]));
        }
        return $tags;
    }

    // =========================================================================
    // SOURCE CONNECTIONS
    // =========================================================================
    public function seedSourceConnections(Tenant $tenant): array
    {
        $connections = [];
        $defs = [
            ['source' => 'meta', 'name' => 'Facebook Lead Ads - Main Campaign', 'status' => 'connected'],
            ['source' => 'google_ads', 'name' => 'Google Ads Lead Forms', 'status' => 'connected'],
            ['source' => 'linkedin', 'name' => 'LinkedIn Lead Gen', 'status' => 'connected'],
            ['source' => 'instagram', 'name' => 'Instagram Leads', 'status' => 'connected'],
            ['source' => 'typeform', 'name' => 'Typeform Survey', 'status' => 'connected'],
            ['source' => 'web_form', 'name' => 'Website Contact Form', 'status' => 'connected'],
        ];

        foreach ($defs as $def) {
            $connections[] = LeadSourceConnection::create([
                'tenant_id' => $tenant->id,
                'source' => $def['source'],
                'name' => $def['name'],
                'webhook_token' => Str::random(48),
                'status' => $def['status'],
                'active' => true,
                'last_received_at' => $this->randomRecentDate(7),
                'settings' => ['auto_assign' => true],
            ]);
        }

        return $connections;
    }

    // =========================================================================
    // LEADS
    // =========================================================================
    public function seedLeads(Tenant $tenant, array $users, array $pipelines, array $stagesByPipeline, int $count): array
    {
        $leads = [];
        $userIds = collect($users)->pluck('id')->toArray();
        $mainPipeline = $pipelines[0]; // Sales Pipeline
        $mainStages = $stagesByPipeline[$mainPipeline->id];

        for ($i = 0; $i < $count; $i++) {
            $firstName = $this->pick($this->firstNames);
            $lastName = $this->pick($this->lastNames);
            $company = $this->pick($this->companies);
            $status = $this->randomStatus();
            $source = $this->randomSource();
            $createdAt = $this->randomPastDate(90);

            // Assign to pipeline stage based on status
            $pipeline = $mainPipeline;
            $stage = null;

            // Sometimes put in secondary pipelines
            if ($i % 15 === 0 && count($pipelines) > 1) {
                $pipeline = $pipelines[1]; // Onboarding
                $pStages = $stagesByPipeline[$pipeline->id];
                $stage = $this->pick($pStages);
            } elseif ($i % 25 === 0 && count($pipelines) > 2) {
                $pipeline = $pipelines[2]; // Partnership
                $pStages = $stagesByPipeline[$pipeline->id];
                $stage = $this->pick($pStages);
            } else {
                // Map status to stage in main pipeline
                $stage = match ($status) {
                    'new' => $mainStages[0],            // New Lead
                    'contacted' => $this->pick([$mainStages[1], $mainStages[2]]), // Contacted or Qualified
                    'qualified' => $this->pick([$mainStages[2], $mainStages[3], $mainStages[4]]), // Qualified/Proposal/Negotiation
                    'won' => $mainStages[5],            // Won
                    'lost' => $mainStages[6],           // Lost
                    default => $mainStages[0],
                };
            }

            $hasPhone = rand(1, 100) <= 70;
            $isAssigned = rand(1, 100) <= 75;
            $assignedUserId = $isAssigned ? $this->pick($userIds) : null;
            $assignedUser = $assignedUserId ? collect($users)->firstWhere('id', $assignedUserId) : null;

            $contactedAt = in_array($status, ['contacted', 'qualified', 'won'])
                ? $createdAt->copy()->addHours(rand(1, 72))
                : null;

            $lead = Lead::create([
                'tenant_id' => $tenant->id,
                'source' => $source,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $this->randomEmail($firstName, $lastName, $company),
                'phone' => $hasPhone ? $this->randomPhone() : null,
                'company' => $company,
                'job_title' => $this->pick($this->jobTitles),
                'country' => $this->pick($this->countries),
                'industry' => $this->pick($this->industries),
                'company_size' => $this->pick($this->companySizes),
                'status' => $status,
                'lead_score' => $this->generateLeadScore($status),
                'is_starred' => rand(1, 100) <= 10,
                'is_duplicate' => false,
                'pipeline_id' => $pipeline->id,
                'pipeline_stage_id' => $stage->id,
                'stage_entered_at' => $createdAt->copy()->addHours(rand(0, 48)),
                'assigned_user_id' => $assignedUserId,
                'assigned_to' => $assignedUser?->name,
                'contacted_at' => $contactedAt,
                'custom_fields' => rand(1, 100) <= 30 ? ['utm_source' => $this->pick(['google', 'facebook', 'linkedin', 'direct']), 'utm_campaign' => $this->pick(['spring_2026', 'launch_promo', 'retargeting', 'brand_awareness'])] : null,
                'raw_data' => null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt->copy()->addHours(rand(0, 48)),
            ]);

            $leads[] = $lead;
        }

        return $leads;
    }

    private function generateLeadScore(string $status): int
    {
        return match ($status) {
            'won' => rand(70, 100),
            'qualified' => rand(50, 85),
            'contacted' => rand(25, 60),
            'new' => rand(5, 40),
            'lost' => rand(0, 30),
            default => rand(0, 50),
        };
    }

    // =========================================================================
    // LEAD-TAG PIVOT
    // =========================================================================
    public function seedLeadTags(array $leads, array $tags): void
    {
        $tagIds = collect($tags)->pluck('id')->toArray();
        $inserts = [];

        foreach ($leads as $lead) {
            // 60% of leads get tags
            if (rand(1, 100) > 60) {
                continue;
            }
            $numTags = rand(1, 3);
            $selectedTagIds = (array) array_rand(array_flip($tagIds), min($numTags, count($tagIds)));

            foreach ($selectedTagIds as $tagId) {
                $inserts[] = ['lead_id' => $lead->id, 'tag_id' => $tagId];
            }
        }

        // Bulk insert in chunks
        foreach (array_chunk($inserts, 500) as $chunk) {
            DB::table('lead_tag')->insert($chunk);
        }
    }

    // =========================================================================
    // LEAD ACTIVITIES
    // =========================================================================
    public function seedLeadActivities(Tenant $tenant, array $leads, array $users): void
    {
        $activityTypes = [
            'created' => 'Lead was created',
            'status_changed' => 'Lead status changed to {status}',
            'stage_moved' => 'Lead moved to {stage} stage',
            'email_sent' => 'Email sent to lead',
            'call_logged' => 'Phone call logged ({duration} minutes)',
            'note_added' => 'Note added to lead',
            'tag_applied' => 'Tag "{tag}" applied',
            'score_changed' => 'Lead score updated to {score}',
            'assigned' => 'Lead assigned to {user}',
        ];

        $userIds = collect($users)->pluck('id')->toArray();
        $inserts = [];

        foreach ($leads as $lead) {
            $numActivities = rand(1, 6);
            for ($i = 0; $i < $numActivities; $i++) {
                $type = $this->pick(array_keys($activityTypes));
                $description = match ($type) {
                    'status_changed' => 'Lead status changed to ' . $lead->status,
                    'stage_moved' => 'Lead moved to next stage',
                    'call_logged' => 'Phone call logged (' . rand(5, 45) . ' minutes)',
                    'tag_applied' => 'Tag "' . $this->pick(['Hot Lead', 'Follow-up', 'VIP', 'Warm Lead']) . '" applied',
                    'score_changed' => 'Lead score updated to ' . $lead->lead_score,
                    'assigned' => 'Lead assigned to ' . $this->pick($users)->name,
                    'email_sent' => 'Email sent to lead',
                    'note_added' => 'Note added to lead',
                    default => $activityTypes[$type],
                };

                $activityDate = $lead->created_at->copy()->addHours(rand(0, 720));
                if ($activityDate->isAfter($this->now)) {
                    $activityDate = $this->now->copy()->subHours(rand(1, 24));
                }

                $inserts[] = [
                    'lead_id' => $lead->id,
                    'tenant_id' => $tenant->id,
                    'user_id' => $this->pick($userIds),
                    'type' => $type,
                    'description' => $description,
                    'metadata' => json_encode($type === 'call_logged' ? ['duration' => rand(5, 45), 'outcome' => $this->pick(['interested', 'callback', 'not_interested', 'voicemail'])] : null),
                    'created_at' => $activityDate,
                ];
            }
        }

        foreach (array_chunk($inserts, 500) as $chunk) {
            DB::table('lead_activities')->insert($chunk);
        }
    }

    // =========================================================================
    // LEAD NOTES
    // =========================================================================
    public function seedLeadNotes(Tenant $tenant, array $leads, array $users): void
    {
        $noteTemplates = [
            'Spoke with {name} on the phone. They are interested in our {product} solution and want a follow-up demo next week.',
            'Left a voicemail. Will try again tomorrow afternoon.',
            'Sent pricing proposal via email. Awaiting response.',
            'Met at the {event} conference. Very promising lead with budget approval expected next quarter.',
            '{name} mentioned they are evaluating 2-3 other vendors. Need to emphasize our unique value proposition.',
            'Follow-up call scheduled for {date}. They want to include their CTO in the next meeting.',
            'Budget approved. Moving forward with contract discussion.',
            'Not a good fit at this time. They need a simpler solution. Will re-engage in 6 months.',
            'Referred by {referrer}. Already familiar with our platform from a previous role.',
            'Requested a custom integration with their existing CRM. Checking with engineering team on feasibility.',
            'Decision maker is on vacation until end of month. Will reconnect then.',
            'Very responsive to our email campaign. Clicked through on 3 of our last 5 emails.',
            'Attended our webinar on lead generation best practices. Followed up with personalized outreach.',
            'Competitor deal fell through. They are now seriously considering us.',
            'Needs GDPR-compliant solution. Confirmed our platform meets their requirements.',
        ];

        $userIds = collect($users)->pluck('id')->toArray();
        $inserts = [];

        foreach ($leads as $lead) {
            if (rand(1, 100) > 50) {
                continue; // 50% of leads get notes
            }
            $numNotes = rand(1, 3);
            for ($j = 0; $j < $numNotes; $j++) {
                $template = $this->pick($noteTemplates);
                $body = str_replace(
                    ['{name}', '{product}', '{event}', '{date}', '{referrer}'],
                    [
                        $lead->first_name,
                        $this->pick(['CRM', 'analytics', 'automation', 'lead capture']),
                        $this->pick(['SaaS Connect', 'TechCrunch', 'MarTech', 'Web Summit']),
                        $this->now->copy()->addDays(rand(1, 14))->format('M j'),
                        $this->pick($this->firstNames) . ' ' . $this->pick($this->lastNames),
                    ],
                    $template
                );

                $noteDate = $lead->created_at->copy()->addHours(rand(1, 500));
                if ($noteDate->isAfter($this->now)) {
                    $noteDate = $this->now->copy()->subHours(rand(1, 48));
                }

                $inserts[] = [
                    'lead_id' => $lead->id,
                    'tenant_id' => $tenant->id,
                    'user_id' => $this->pick($userIds),
                    'body' => $body,
                    'mentions' => null,
                    'created_at' => $noteDate,
                    'updated_at' => $noteDate,
                    'deleted_at' => null,
                ];
            }
        }

        foreach (array_chunk($inserts, 500) as $chunk) {
            DB::table('lead_notes')->insert($chunk);
        }
    }

    // =========================================================================
    // LEAD TASKS
    // =========================================================================
    public function seedLeadTasks(Tenant $tenant, array $leads, array $users): void
    {
        $taskTemplates = [
            'Follow up with phone call',
            'Send pricing proposal',
            'Schedule demo meeting',
            'Review contract terms',
            'Send welcome email',
            'Check in after trial period',
            'Prepare case study for prospect',
            'Send introduction to account manager',
            'Review lead qualification criteria',
            'Schedule onboarding call',
            'Send product comparison document',
            'Update CRM notes',
        ];

        $userIds = collect($users)->pluck('id')->toArray();
        $inserts = [];

        // Add tasks to ~40% of leads
        foreach ($leads as $lead) {
            if (rand(1, 100) > 40) {
                continue;
            }
            $numTasks = rand(1, 3);
            for ($j = 0; $j < $numTasks; $j++) {
                $dueAt = $this->now->copy()->addDays(rand(-10, 14))->setHour(rand(9, 17));
                $completed = $dueAt->isPast() ? (rand(1, 100) <= 70) : false;

                $inserts[] = [
                    'lead_id' => $lead->id,
                    'tenant_id' => $tenant->id,
                    'assigned_user_id' => $this->pick($userIds),
                    'title' => $this->pick($taskTemplates),
                    'description' => rand(1, 100) <= 40 ? 'Priority task - please complete as soon as possible.' : null,
                    'due_at' => $dueAt,
                    'completed' => $completed,
                    'completed_at' => $completed ? $dueAt->copy()->subHours(rand(1, 24)) : null,
                    'created_at' => $dueAt->copy()->subDays(rand(1, 7)),
                    'updated_at' => $this->now,
                ];
            }
        }

        foreach (array_chunk($inserts, 500) as $chunk) {
            DB::table('lead_tasks')->insert($chunk);
        }
    }

    // =========================================================================
    // FORMS & SUBMISSIONS
    // =========================================================================
    public function seedForms(Tenant $tenant, array $pipelines, array $stagesByPipeline, array $leads): void
    {
        $formDefs = [
            [
                'name' => 'Contact Us',
                'slug' => 'contact-us',
                'title' => 'Get in Touch',
                'description' => 'Fill out the form below and we\'ll get back to you within 24 hours.',
                'submit_label' => 'Send Message',
                'thank_you_message' => 'Thank you for contacting us! We\'ll be in touch shortly.',
                'fields' => [
                    ['type' => 'text', 'label' => 'First Name', 'field_key' => 'first_name', 'required' => true, 'sort_order' => 0],
                    ['type' => 'text', 'label' => 'Last Name', 'field_key' => 'last_name', 'required' => true, 'sort_order' => 1],
                    ['type' => 'email', 'label' => 'Email Address', 'field_key' => 'email', 'required' => true, 'sort_order' => 2],
                    ['type' => 'phone', 'label' => 'Phone Number', 'field_key' => 'phone', 'required' => false, 'sort_order' => 3],
                    ['type' => 'textarea', 'label' => 'Message', 'field_key' => 'message', 'required' => false, 'sort_order' => 4],
                    ['type' => 'gdpr', 'label' => 'I agree to the privacy policy', 'field_key' => 'gdpr_consent', 'required' => true, 'sort_order' => 5],
                ],
            ],
            [
                'name' => 'Request a Demo',
                'slug' => 'request-demo',
                'title' => 'Book a Free Demo',
                'description' => 'See our platform in action with a personalized demo.',
                'submit_label' => 'Book Demo',
                'thank_you_message' => 'Your demo has been booked! Check your email for confirmation.',
                'fields' => [
                    ['type' => 'text', 'label' => 'Full Name', 'field_key' => 'full_name', 'required' => true, 'sort_order' => 0],
                    ['type' => 'email', 'label' => 'Work Email', 'field_key' => 'email', 'required' => true, 'sort_order' => 1],
                    ['type' => 'text', 'label' => 'Company Name', 'field_key' => 'company', 'required' => true, 'sort_order' => 2],
                    ['type' => 'select', 'label' => 'Company Size', 'field_key' => 'company_size', 'required' => true, 'sort_order' => 3, 'options' => ['1-10', '11-50', '51-200', '201-500', '500+']],
                    ['type' => 'phone', 'label' => 'Phone Number', 'field_key' => 'phone', 'required' => false, 'sort_order' => 4],
                ],
            ],
            [
                'name' => 'Newsletter Signup',
                'slug' => 'newsletter',
                'title' => 'Stay Updated',
                'description' => 'Get the latest insights on lead generation and CRM best practices.',
                'submit_label' => 'Subscribe',
                'thank_you_message' => 'You\'re subscribed! Watch your inbox for our next newsletter.',
                'fields' => [
                    ['type' => 'email', 'label' => 'Email Address', 'field_key' => 'email', 'required' => true, 'sort_order' => 0],
                    ['type' => 'text', 'label' => 'First Name', 'field_key' => 'first_name', 'required' => false, 'sort_order' => 1],
                ],
            ],
            [
                'name' => 'Free Consultation',
                'slug' => 'free-consultation',
                'title' => 'Book Your Free Consultation',
                'description' => 'Our experts will analyze your current lead pipeline and suggest improvements.',
                'submit_label' => 'Get My Consultation',
                'thank_you_message' => 'We\'ll reach out within 1 business day to schedule your consultation.',
                'fields' => [
                    ['type' => 'text', 'label' => 'First Name', 'field_key' => 'first_name', 'required' => true, 'sort_order' => 0],
                    ['type' => 'text', 'label' => 'Last Name', 'field_key' => 'last_name', 'required' => true, 'sort_order' => 1],
                    ['type' => 'email', 'label' => 'Email', 'field_key' => 'email', 'required' => true, 'sort_order' => 2],
                    ['type' => 'phone', 'label' => 'Phone', 'field_key' => 'phone', 'required' => true, 'sort_order' => 3],
                    ['type' => 'text', 'label' => 'Company', 'field_key' => 'company', 'required' => true, 'sort_order' => 4],
                    ['type' => 'select', 'label' => 'What is your biggest challenge?', 'field_key' => 'challenge', 'required' => false, 'sort_order' => 5, 'options' => ['Lead quality', 'Lead volume', 'Conversion rate', 'Follow-up speed', 'Reporting']],
                    ['type' => 'textarea', 'label' => 'Additional Details', 'field_key' => 'details', 'required' => false, 'sort_order' => 6],
                ],
            ],
        ];

        $mainPipeline = $pipelines[0];
        $mainFirstStage = $stagesByPipeline[$mainPipeline->id][0];

        foreach ($formDefs as $fDef) {
            $form = Form::create([
                'tenant_id' => $tenant->id,
                'name' => $fDef['name'],
                'slug' => $fDef['slug'],
                'title' => $fDef['title'],
                'description' => $fDef['description'],
                'submit_label' => $fDef['submit_label'],
                'thank_you_message' => $fDef['thank_you_message'],
                'pipeline_id' => $mainPipeline->id,
                'pipeline_stage_id' => $mainFirstStage->id,
                'active' => true,
                'multi_step' => false,
                'recaptcha_enabled' => false,
            ]);

            $fields = [];
            foreach ($fDef['fields'] as $fieldDef) {
                $fields[] = FormField::create([
                    'form_id' => $form->id,
                    'type' => $fieldDef['type'],
                    'label' => $fieldDef['label'],
                    'field_key' => $fieldDef['field_key'],
                    'required' => $fieldDef['required'],
                    'sort_order' => $fieldDef['sort_order'],
                    'step_number' => 1,
                    'options' => $fieldDef['options'] ?? null,
                    'locked' => in_array($fieldDef['field_key'], ['email', 'first_name', 'last_name']),
                ]);
            }

            // Create submissions (8-20 per form)
            $numSubmissions = rand(8, 20);
            $availableLeads = array_slice($leads, rand(0, max(0, count($leads) - $numSubmissions)), $numSubmissions);

            foreach ($availableLeads as $lead) {
                $submittedAt = $this->randomPastDate(60);
                $submission = FormSubmission::create([
                    'form_id' => $form->id,
                    'tenant_id' => $tenant->id,
                    'lead_id' => $lead->id,
                    'ip_address' => rand(10, 200) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(1, 254),
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'is_spam' => rand(1, 100) <= 3,
                    'consented_at' => $submittedAt,
                    'consent_text' => 'I agree to the privacy policy and terms of service.',
                    'completed_step' => 1,
                    'created_at' => $submittedAt,
                    'updated_at' => $submittedAt,
                ]);

                // Create values for each field
                foreach ($fields as $field) {
                    $value = match ($field->field_key) {
                        'first_name', 'full_name' => $lead->first_name,
                        'last_name' => $lead->last_name,
                        'email' => $lead->email,
                        'phone' => $lead->phone ?? $this->randomPhone(),
                        'company' => $lead->company,
                        'message', 'details' => 'I am interested in learning more about your services.',
                        'company_size' => $this->pick($this->companySizes),
                        'challenge' => $this->pick(['Lead quality', 'Lead volume', 'Conversion rate']),
                        'gdpr_consent' => '1',
                        default => null,
                    };

                    if ($value !== null) {
                        FormSubmissionValue::create([
                            'form_submission_id' => $submission->id,
                            'form_field_id' => $field->id,
                            'value' => $value,
                        ]);
                    }
                }
            }
        }
    }

    // =========================================================================
    // AUTOMATIONS
    // =========================================================================
    public function seedAutomations(Tenant $tenant, array $leads, array $tags, array $pipelines, array $stagesByPipeline): void
    {
        $automationDefs = [
            [
                'name' => 'Welcome New Leads',
                'description' => 'Automatically send a welcome email when a new lead is received.',
                'trigger_type' => 'lead_created',
                'trigger_config' => [],
                'enabled' => true,
                'steps' => [
                    ['type' => 'action', 'config' => ['action' => 'send_email', 'template' => 'welcome'], 'sort_order' => 0],
                    ['type' => 'delay', 'config' => ['delay_hours' => 24], 'sort_order' => 1],
                    ['type' => 'action', 'config' => ['action' => 'notify_users', 'message' => 'New lead needs follow-up'], 'sort_order' => 2],
                ],
            ],
            [
                'name' => 'Hot Lead Alert',
                'description' => 'Notify sales team immediately when a high-score lead comes in.',
                'trigger_type' => 'lead_score_threshold',
                'trigger_config' => ['threshold' => 70, 'direction' => 'above'],
                'enabled' => true,
                'steps' => [
                    ['type' => 'action', 'config' => ['action' => 'add_tag', 'tag' => 'Hot Lead'], 'sort_order' => 0],
                    ['type' => 'action', 'config' => ['action' => 'notify_users', 'message' => 'Hot lead detected! Score above 70.'], 'sort_order' => 1],
                    ['type' => 'action', 'config' => ['action' => 'create_task', 'title' => 'Call hot lead within 1 hour'], 'sort_order' => 2],
                ],
            ],
            [
                'name' => 'Stage Change Follow-up',
                'description' => 'When a lead moves to Proposal Sent, create a follow-up task.',
                'trigger_type' => 'lead_stage_changed',
                'trigger_config' => ['to_stage' => 'Proposal Sent'],
                'enabled' => true,
                'steps' => [
                    ['type' => 'condition', 'config' => ['condition' => 'score_gt', 'value' => 50], 'sort_order' => 0],
                    ['type' => 'action', 'config' => ['action' => 'create_task', 'title' => 'Follow up on proposal in 3 days'], 'sort_order' => 1],
                    ['type' => 'action', 'config' => ['action' => 'send_email', 'template' => 'proposal_sent'], 'sort_order' => 2],
                ],
            ],
            [
                'name' => 'Re-engage Cold Leads',
                'description' => 'Automatically tag and email leads with no activity in 14 days.',
                'trigger_type' => 'no_activity',
                'trigger_config' => ['days' => 14],
                'enabled' => true,
                'steps' => [
                    ['type' => 'action', 'config' => ['action' => 'add_tag', 'tag' => 'Re-engaged'], 'sort_order' => 0],
                    ['type' => 'action', 'config' => ['action' => 'send_email', 'template' => 're_engagement'], 'sort_order' => 1],
                ],
            ],
            [
                'name' => 'Assign to Round Robin',
                'description' => 'Auto-assign new leads from Facebook to the sales team in round-robin.',
                'trigger_type' => 'lead_created',
                'trigger_config' => ['source' => 'meta'],
                'enabled' => false,
                'steps' => [
                    ['type' => 'condition', 'config' => ['condition' => 'source_is', 'value' => 'meta'], 'sort_order' => 0],
                    ['type' => 'action', 'config' => ['action' => 'assign_lead', 'method' => 'round_robin'], 'sort_order' => 1],
                    ['type' => 'action', 'config' => ['action' => 'notify_users', 'message' => 'New Facebook lead assigned to you'], 'sort_order' => 2],
                ],
            ],
        ];

        foreach ($automationDefs as $aDef) {
            $automation = Automation::create([
                'tenant_id' => $tenant->id,
                'name' => $aDef['name'],
                'description' => $aDef['description'],
                'trigger_type' => $aDef['trigger_type'],
                'trigger_config' => $aDef['trigger_config'],
                'enabled' => $aDef['enabled'],
            ]);

            foreach ($aDef['steps'] as $step) {
                AutomationStep::create([
                    'automation_id' => $automation->id,
                    'type' => $step['type'],
                    'config' => $step['config'],
                    'sort_order' => $step['sort_order'],
                ]);
            }

            // Create some runs for enabled automations
            if ($aDef['enabled']) {
                $runCount = rand(10, 30);
                $selectedLeads = array_slice($leads, 0, $runCount);

                $runInserts = [];
                foreach ($selectedLeads as $lead) {
                    $startedAt = $this->randomPastDate(60);
                    $status = $this->pick(['success', 'success', 'success', 'success', 'partial', 'failed']);
                    $finishedAt = $status !== 'pending' ? $startedAt->copy()->addSeconds(rand(1, 120)) : null;

                    $runInserts[] = [
                        'automation_id' => $automation->id,
                        'lead_id' => $lead->id,
                        'started_at' => $startedAt,
                        'finished_at' => $finishedAt,
                        'status' => $status,
                        'log' => json_encode($status === 'failed'
                            ? [['step' => 1, 'error' => 'Email delivery failed: mailbox full']]
                            : [['step' => 1, 'result' => 'ok'], ['step' => 2, 'result' => 'ok']]),
                        'created_at' => $startedAt,
                        'updated_at' => $finishedAt ?? $startedAt,
                    ];
                }

                foreach (array_chunk($runInserts, 500) as $chunk) {
                    DB::table('automation_runs')->insert($chunk);
                }
            }
        }
    }

    // =========================================================================
    // EMAIL TEMPLATES
    // =========================================================================
    public function seedEmailTemplates(Tenant $tenant): void
    {
        $templates = [
            [
                'name' => 'Welcome Email',
                'subject' => 'Welcome, {{lead.first_name}}! We\'re glad to have you',
                'body_html' => '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;"><h2>Welcome, {{lead.first_name}}!</h2><p>Thank you for your interest in our services. We\'re excited to help you achieve your goals.</p><p>One of our team members will reach out to you shortly to discuss how we can best assist you.</p><p>In the meantime, feel free to explore our resources:</p><ul><li>Product Overview</li><li>Case Studies</li><li>Pricing Plans</li></ul><p>Best regards,<br>The Team</p></div>',
                'body_text' => "Welcome, {{lead.first_name}}!\n\nThank you for your interest in our services. We're excited to help you achieve your goals.\n\nOne of our team members will reach out to you shortly.\n\nBest regards,\nThe Team",
            ],
            [
                'name' => 'Follow-up After Demo',
                'subject' => '{{lead.first_name}}, great speaking with you today!',
                'body_html' => '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;"><h2>Great meeting, {{lead.first_name}}!</h2><p>Thank you for taking the time to see our demo today. I hope it gave you a clear picture of how our platform can help {{lead.company}} grow.</p><p>Here are the next steps we discussed:</p><ol><li>Review the proposal we\'ll send this week</li><li>Share with your team</li><li>Schedule a follow-up call</li></ol><p>Please don\'t hesitate to reach out if you have any questions.</p><p>Looking forward to working together!</p></div>',
                'body_text' => "Great meeting, {{lead.first_name}}!\n\nThank you for taking the time to see our demo today.\n\nNext steps:\n1. Review the proposal\n2. Share with your team\n3. Schedule a follow-up call\n\nLooking forward to working together!",
            ],
            [
                'name' => 'Proposal Sent',
                'subject' => 'Your custom proposal is ready, {{lead.first_name}}',
                'body_html' => '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;"><h2>Your Proposal is Ready</h2><p>Hi {{lead.first_name}},</p><p>As discussed, I\'ve attached a custom proposal tailored for {{lead.company}}.</p><p>Key highlights:</p><ul><li>Custom pricing based on your needs</li><li>Dedicated onboarding support</li><li>Priority customer service</li></ul><p>I\'d love to walk through the details with you. Would next week work for a call?</p><p>Best,<br>Your Account Manager</p></div>',
                'body_text' => "Hi {{lead.first_name}},\n\nYour custom proposal for {{lead.company}} is ready.\n\nKey highlights:\n- Custom pricing\n- Dedicated onboarding\n- Priority support\n\nWould next week work for a call?\n\nBest,\nYour Account Manager",
            ],
            [
                'name' => 'Re-engagement Email',
                'subject' => 'We miss you, {{lead.first_name}}! Here\'s what\'s new',
                'body_html' => '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;"><h2>It\'s been a while, {{lead.first_name}}!</h2><p>We noticed it\'s been some time since we last connected. A lot has changed since then:</p><ul><li>New automation features</li><li>Enhanced reporting dashboard</li><li>Improved lead scoring algorithm</li></ul><p>Would you like to schedule a quick 15-minute catch-up to see what\'s new?</p><p>Best regards,<br>The Team</p></div>',
                'body_text' => "Hi {{lead.first_name}}!\n\nIt's been a while since we last connected. Here's what's new:\n- New automation features\n- Enhanced reporting\n- Improved lead scoring\n\nWould you like a 15-minute catch-up?\n\nBest regards,\nThe Team",
            ],
            [
                'name' => 'Thank You - Won Deal',
                'subject' => 'Welcome aboard, {{lead.first_name}}! Let\'s get started',
                'body_html' => '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;"><h2>Welcome aboard, {{lead.first_name}}! 🎉</h2><p>We\'re thrilled to have {{lead.company}} join our growing family of successful businesses.</p><p>Your onboarding process starts now:</p><ol><li>Account setup (today)</li><li>Kickoff call (this week)</li><li>Team training (next week)</li><li>Go live!</li></ol><p>Your dedicated success manager will reach out within 24 hours.</p><p>Here\'s to a great partnership!</p></div>',
                'body_text' => "Welcome aboard, {{lead.first_name}}!\n\nWe're thrilled to have {{lead.company}} join us.\n\nOnboarding steps:\n1. Account setup\n2. Kickoff call\n3. Team training\n4. Go live!\n\nYour success manager will reach out within 24 hours.\n\nHere's to a great partnership!",
            ],
            [
                'name' => 'Appointment Reminder',
                'subject' => 'Reminder: Your appointment is coming up, {{lead.first_name}}',
                'body_html' => '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;"><h2>Appointment Reminder</h2><p>Hi {{lead.first_name}},</p><p>Just a friendly reminder that your appointment is coming up. Please make sure to have any questions ready so we can make the most of our time together.</p><p>If you need to reschedule, please let us know at least 24 hours in advance.</p><p>See you soon!</p></div>',
                'body_text' => "Hi {{lead.first_name}},\n\nJust a friendly reminder that your appointment is coming up.\n\nPlease have any questions ready.\n\nSee you soon!",
            ],
            [
                'name' => 'Feedback Request',
                'subject' => '{{lead.first_name}}, we\'d love your feedback',
                'body_html' => '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;"><h2>How are we doing?</h2><p>Hi {{lead.first_name}},</p><p>Your opinion matters to us! We\'d love to hear about your experience so far.</p><p>Could you take 2 minutes to share your thoughts? Your feedback helps us improve our service for everyone.</p><p>Thank you for being a valued partner!</p></div>',
                'body_text' => "Hi {{lead.first_name}},\n\nYour opinion matters to us! Could you take 2 minutes to share your thoughts?\n\nThank you for being a valued partner!",
            ],
        ];

        foreach ($templates as $t) {
            EmailTemplate::create(array_merge($t, ['tenant_id' => $tenant->id]));
        }
    }

    // =========================================================================
    // LEAD SCORING RULES
    // =========================================================================
    public function seedLeadScoringRules(Tenant $tenant): void
    {
        $rules = [
            ['name' => 'Has email', 'field' => 'email', 'operator' => 'present', 'value' => null, 'points' => 10, 'sort_order' => 0],
            ['name' => 'Has phone', 'field' => 'phone', 'operator' => 'present', 'value' => null, 'points' => 10, 'sort_order' => 1],
            ['name' => 'Has company', 'field' => 'company', 'operator' => 'present', 'value' => null, 'points' => 5, 'sort_order' => 2],
            ['name' => 'Enterprise company', 'field' => 'company_size', 'operator' => 'equals', 'value' => '501-1000', 'points' => 15, 'sort_order' => 3],
            ['name' => 'Large enterprise', 'field' => 'company_size', 'operator' => 'equals', 'value' => '5000+', 'points' => 20, 'sort_order' => 4],
            ['name' => 'From LinkedIn', 'field' => 'source', 'operator' => 'equals', 'value' => 'linkedin', 'points' => 15, 'sort_order' => 5],
            ['name' => 'From Google Ads', 'field' => 'source', 'operator' => 'equals', 'value' => 'google_ads', 'points' => 12, 'sort_order' => 6],
            ['name' => 'Has LinkedIn URL', 'field' => 'linkedin_url', 'operator' => 'present', 'value' => null, 'points' => 8, 'sort_order' => 7],
            ['name' => 'Technology industry', 'field' => 'industry', 'operator' => 'equals', 'value' => 'Technology', 'points' => 10, 'sort_order' => 8],
            ['name' => 'Has job title', 'field' => 'job_title', 'operator' => 'present', 'value' => null, 'points' => 5, 'sort_order' => 9],
            ['name' => 'C-Level executive', 'field' => 'job_title', 'operator' => 'contains', 'value' => 'CEO', 'points' => 20, 'sort_order' => 10],
            ['name' => 'VP or Director', 'field' => 'job_title', 'operator' => 'contains', 'value' => 'VP', 'points' => 15, 'sort_order' => 11],
        ];

        foreach ($rules as $rule) {
            LeadScoringRule::create(array_merge($rule, [
                'tenant_id' => $tenant->id,
                'active' => true,
            ]));
        }
    }

    // =========================================================================
    // API KEYS
    // =========================================================================
    public function seedApiKeys(Tenant $tenant): void
    {
        $keys = [
            [
                'name' => 'Production API Key',
                'scopes' => ['read:leads', 'write:leads', 'read:pipelines', 'read:tags'],
                'rate_limit_per_hour' => 1000,
                'last_used_at' => $this->randomRecentDate(3),
            ],
            [
                'name' => 'Integration API Key',
                'scopes' => ['read:leads', 'write:leads', 'read:forms', 'write:forms'],
                'rate_limit_per_hour' => 500,
                'last_used_at' => $this->randomRecentDate(7),
            ],
        ];

        foreach ($keys as $keyDef) {
            $generated = ApiKey::generateKey();
            ApiKey::create([
                'tenant_id' => $tenant->id,
                'name' => $keyDef['name'],
                'key_prefix' => $generated['prefix'],
                'key_hash' => $generated['hash'],
                'scopes' => $keyDef['scopes'],
                'rate_limit_per_hour' => $keyDef['rate_limit_per_hour'],
                'last_used_at' => $keyDef['last_used_at'],
                'expires_at' => $this->now->copy()->addYear(),
            ]);
        }
    }

    // =========================================================================
    // OUTBOUND WEBHOOKS & DELIVERIES
    // =========================================================================
    public function seedOutboundWebhooks(Tenant $tenant, array $leads): void
    {
        $webhookDefs = [
            [
                'name' => 'CRM Sync Webhook',
                'url' => 'https://crm.example.com/webhooks/leadhub',
                'events' => ['lead.created', 'lead.updated', 'lead.stage_changed'],
                'filters' => null,
            ],
            [
                'name' => 'Slack Notification',
                'url' => 'https://hooks.slack.com/services/T00000000/B00000000/XXXXXXXXXXXXXXXXXXXXXXXX',
                'events' => ['lead.created', 'form.submitted'],
                'filters' => ['status' => ['new']],
            ],
        ];

        foreach ($webhookDefs as $wDef) {
            $webhook = OutboundWebhook::create([
                'tenant_id' => $tenant->id,
                'name' => $wDef['name'],
                'url' => $wDef['url'],
                'events' => $wDef['events'],
                'filters' => $wDef['filters'],
                'secret' => Str::random(40),
                'enabled' => true,
            ]);

            // Create deliveries
            $deliveryCount = rand(15, 30);
            $inserts = [];
            for ($i = 0; $i < $deliveryCount; $i++) {
                $event = $this->pick($wDef['events']);
                $lead = $this->pick($leads);
                $status = $this->pick(['success', 'success', 'success', 'success', 'failed']);
                $createdAt = $this->randomPastDate(30);

                $inserts[] = [
                    'webhook_id' => $webhook->id,
                    'tenant_id' => $tenant->id,
                    'event' => $event,
                    'payload' => json_encode(['lead_id' => $lead->id, 'event' => $event, 'data' => ['first_name' => $lead->first_name, 'email' => $lead->email]]),
                    'response_code' => $status === 'success' ? 200 : $this->pick([500, 502, 503, 408, 0]),
                    'response_body' => $status === 'success' ? '{"ok":true}' : '{"error":"internal server error"}',
                    'latency_ms' => $status === 'success' ? rand(50, 500) : rand(1000, 30000),
                    'status' => $status,
                    'attempts' => $status === 'success' ? 1 : rand(1, 5),
                    'next_retry_at' => $status === 'failed' ? $this->now->copy()->addMinutes(rand(5, 60)) : null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }

            foreach (array_chunk($inserts, 500) as $chunk) {
                DB::table('webhook_deliveries')->insert($chunk);
            }
        }
    }

    // =========================================================================
    // INTEGRATIONS & SYNC LOGS
    // =========================================================================
    public function seedIntegrations(Tenant $tenant, array $leads): void
    {
        $integrationDefs = [
            [
                'type' => 'google_sheets',
                'name' => 'Google Sheets Export',
                'status' => 'connected',
                'enabled' => true,
                'field_mapping' => ['first_name' => 'A', 'last_name' => 'B', 'email' => 'C', 'phone' => 'D', 'company' => 'E', 'status' => 'F'],
            ],
            [
                'type' => 'slack',
                'name' => 'Slack Notifications',
                'status' => 'connected',
                'enabled' => true,
                'field_mapping' => null,
            ],
            [
                'type' => 'mailchimp',
                'name' => 'Mailchimp Sync',
                'status' => 'disconnected',
                'enabled' => false,
                'field_mapping' => ['email' => 'EMAIL', 'first_name' => 'FNAME', 'last_name' => 'LNAME'],
            ],
        ];

        foreach ($integrationDefs as $iDef) {
            $integration = Integration::create([
                'tenant_id' => $tenant->id,
                'type' => $iDef['type'],
                'name' => $iDef['name'],
                'status' => $iDef['status'],
                'enabled' => $iDef['enabled'],
                'field_mapping' => $iDef['field_mapping'],
                'last_synced_at' => $iDef['enabled'] ? $this->randomRecentDate(3) : null,
            ]);

            // Sync logs for enabled integrations
            if ($iDef['enabled']) {
                $logCount = rand(15, 40);
                $inserts = [];
                for ($j = 0; $j < $logCount; $j++) {
                    $lead = $this->pick($leads);
                    $status = $this->pick(['success', 'success', 'success', 'success', 'failed']);
                    $createdAt = $this->randomPastDate(30);

                    $inserts[] = [
                        'integration_id' => $integration->id,
                        'lead_id' => $lead->id,
                        'event' => $this->pick(['lead_created', 'lead_updated']),
                        'payload' => json_encode(['lead_id' => $lead->id, 'email' => $lead->email]),
                        'response' => json_encode($status === 'success' ? ['status' => 'ok'] : ['error' => 'Connection timeout']),
                        'status' => $status,
                        'attempts' => $status === 'success' ? 1 : rand(1, 3),
                        'retried_at' => $status === 'failed' ? $createdAt->copy()->addMinutes(15) : null,
                        'error_message' => $status === 'failed' ? 'Connection timeout after 30s' : null,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ];
                }

                foreach (array_chunk($inserts, 500) as $chunk) {
                    DB::table('integration_sync_logs')->insert($chunk);
                }
            }
        }
    }

    // =========================================================================
    // WEBHOOK LOGS (INBOUND)
    // =========================================================================
    public function seedWebhookLogs(Tenant $tenant, array $sourceConnections): void
    {
        $inserts = [];
        foreach ($sourceConnections as $conn) {
            $logCount = rand(10, 25);
            for ($i = 0; $i < $logCount; $i++) {
                $status = $this->pick(['success', 'success', 'success', 'success', 'failed', 'pending']);
                $createdAt = $this->randomPastDate(45);

                $inserts[] = [
                    'tenant_id' => $tenant->id,
                    'source_connection_id' => $conn->id,
                    'source' => $conn->source,
                    'status' => $status,
                    'headers' => json_encode(['Content-Type' => 'application/json', 'X-Webhook-Source' => $conn->source]),
                    'payload' => json_encode(['first_name' => $this->pick($this->firstNames), 'last_name' => $this->pick($this->lastNames), 'email' => strtolower($this->pick($this->firstNames)) . '@example.com']),
                    'error_message' => $status === 'failed' ? 'Invalid payload format' : null,
                    'leads_created' => $status === 'success' ? 1 : 0,
                    'ip_address' => rand(10, 200) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(1, 254),
                    'processed_at' => $status !== 'pending' ? $createdAt->copy()->addSeconds(rand(1, 10)) : null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }
        }

        foreach (array_chunk($inserts, 500) as $chunk) {
            DB::table('webhook_logs')->insert($chunk);
        }
    }

    // =========================================================================
    // NOTIFICATION PREFERENCES
    // =========================================================================
    public function seedNotificationPreferences(array $users): void
    {
        $types = array_keys(NotificationPreference::TYPES);
        $channels = NotificationPreference::CHANNELS;

        $inserts = [];
        foreach ($users as $user) {
            foreach ($types as $type) {
                foreach ($channels as $channel) {
                    $inserts[] = [
                        'user_id' => $user->id,
                        'notification_type' => $type,
                        'channel' => $channel,
                        'enabled' => rand(1, 100) <= 80,
                        'email_frequency' => $channel === 'email' ? $this->pick(['immediate', 'immediate', 'hourly', 'off']) : 'immediate',
                        'created_at' => $this->now,
                        'updated_at' => $this->now,
                    ];
                }
            }
        }

        foreach (array_chunk($inserts, 500) as $chunk) {
            DB::table('notification_preferences')->insert($chunk);
        }
    }

    // =========================================================================
    // SCHEDULED REPORTS
    // =========================================================================
    public function seedScheduledReports(Tenant $tenant, User $adminUser): void
    {
        $reportDefs = [
            [
                'name' => 'Weekly Dashboard Summary',
                'report_type' => 'dashboard_stats',
                'frequency' => 'weekly',
                'format' => 'csv',
                'recipient_emails' => [$adminUser->email],
                'is_active' => true,
            ],
            [
                'name' => 'Monthly Leads by Source',
                'report_type' => 'leads_by_source',
                'frequency' => 'monthly',
                'format' => 'csv',
                'recipient_emails' => [$adminUser->email],
                'is_active' => true,
            ],
            [
                'name' => 'Daily Agent Performance',
                'report_type' => 'agent_performance',
                'frequency' => 'daily',
                'format' => 'csv',
                'recipient_emails' => [$adminUser->email],
                'is_active' => true,
            ],
        ];

        foreach ($reportDefs as $rDef) {
            $report = new ScheduledReport(array_merge($rDef, ['tenant_id' => $tenant->id]));
            $report->last_sent_at = $this->randomRecentDate(7);
            $report->next_due_at = $report->calculateNextDue();
            $report->save();
        }
    }

    // =========================================================================
    // LEAD IMPORTS
    // =========================================================================
    public function seedLeadImports(Tenant $tenant, User $adminUser): void
    {
        $imports = [
            [
                'file_path' => 'imports/leads_q1_2026.csv',
                'original_filename' => 'leads_q1_2026.csv',
                'status' => 'completed',
                'total_rows' => 150,
                'imported_count' => 142,
                'duplicate_count' => 6,
                'error_count' => 2,
                'column_mapping' => ['A' => 'first_name', 'B' => 'last_name', 'C' => 'email', 'D' => 'phone', 'E' => 'company'],
                'errors' => [
                    ['row' => 47, 'error' => 'Invalid email format'],
                    ['row' => 103, 'error' => 'Missing required field: email'],
                ],
            ],
            [
                'file_path' => 'imports/event_leads_march.csv',
                'original_filename' => 'event_leads_march_2026.csv',
                'status' => 'completed',
                'total_rows' => 75,
                'imported_count' => 73,
                'duplicate_count' => 2,
                'error_count' => 0,
                'column_mapping' => ['A' => 'first_name', 'B' => 'last_name', 'C' => 'email', 'D' => 'company', 'E' => 'job_title'],
                'errors' => null,
            ],
        ];

        foreach ($imports as $imp) {
            LeadImport::create(array_merge($imp, [
                'tenant_id' => $tenant->id,
                'user_id' => $adminUser->id,
                'created_at' => $this->randomPastDate(45),
            ]));
        }
    }

    // =========================================================================
    // LEAD DUPLICATES
    // =========================================================================
    public function seedLeadDuplicates(Tenant $tenant, array $leads): void
    {
        if (count($leads) < 20) {
            return;
        }

        $inserts = [];
        $usedPairs = [];
        $dupCount = min(10, intval(count($leads) * 0.04));

        for ($i = 0; $i < $dupCount; $i++) {
            $origIdx = rand(0, count($leads) - 2);
            $dupIdx = rand($origIdx + 1, count($leads) - 1);
            $pairKey = $leads[$origIdx]->id . '-' . $leads[$dupIdx]->id;

            if (in_array($pairKey, $usedPairs)) {
                continue;
            }
            $usedPairs[] = $pairKey;

            $inserts[] = [
                'tenant_id' => $tenant->id,
                'original_lead_id' => $leads[$origIdx]->id,
                'duplicate_lead_id' => $leads[$dupIdx]->id,
                'match_field' => $this->pick(['email', 'phone', 'phone_normalized']),
                'attempted_data' => json_encode([
                    'first_name' => $leads[$dupIdx]->first_name,
                    'last_name' => $leads[$dupIdx]->last_name,
                    'email' => $leads[$dupIdx]->email,
                ]),
                'created_at' => $this->randomPastDate(30),
                'updated_at' => $this->now,
            ];
        }

        if (!empty($inserts)) {
            DB::table('lead_duplicates')->insert($inserts);
        }
    }

    // =========================================================================
    // LEAD CAPTURE WIDGETS
    // =========================================================================
    public function seedLeadCaptureWidgets(Tenant $tenant, array $pipelines, array $stagesByPipeline): void
    {
        $mainPipeline = $pipelines[0];
        $firstStage = $stagesByPipeline[$mainPipeline->id][0];

        $widgets = [
            [
                'name' => 'Homepage Widget',
                'headline' => 'Get a Free Quote',
                'subheadline' => 'Tell us about your project and we\'ll get back to you within 24 hours.',
                'button_text' => 'Request Quote',
                'success_message' => 'Thanks! We\'ll be in touch soon.',
                'primary_color' => '#3b82f6',
                'position' => 'bottom-right',
                'show_phone' => true,
                'show_company' => true,
                'show_message' => true,
                'leads_captured' => rand(25, 80),
            ],
            [
                'name' => 'Blog Sidebar Widget',
                'headline' => 'Have Questions?',
                'subheadline' => 'Our team is here to help.',
                'button_text' => 'Contact Us',
                'success_message' => 'Message sent! We\'ll respond shortly.',
                'primary_color' => '#8b5cf6',
                'position' => 'bottom-left',
                'show_phone' => false,
                'show_company' => false,
                'show_message' => true,
                'leads_captured' => rand(10, 35),
            ],
        ];

        foreach ($widgets as $wDef) {
            LeadCaptureWidget::create(array_merge($wDef, [
                'uuid' => Str::uuid()->toString(),
                'tenant_id' => $tenant->id,
                'pipeline_id' => $mainPipeline->id,
                'pipeline_stage_id' => $firstStage->id,
                'text_color' => '#ffffff',
                'require_phone' => false,
                'require_message' => false,
                'is_active' => true,
                'allowed_domains' => ['*'],
            ]));
        }
    }

    // =========================================================================
    // AUDIT LOGS
    // =========================================================================
    public function seedAuditLogs(Tenant $tenant, array $users, array $leads): void
    {
        $actions = [
            ['action' => 'login', 'auditable_type' => 'App\\Models\\User'],
            ['action' => 'created', 'auditable_type' => 'App\\Models\\Lead'],
            ['action' => 'updated', 'auditable_type' => 'App\\Models\\Lead'],
            ['action' => 'deleted', 'auditable_type' => 'App\\Models\\Lead'],
            ['action' => 'created', 'auditable_type' => 'App\\Models\\Pipeline'],
            ['action' => 'updated', 'auditable_type' => 'App\\Models\\Automation'],
            ['action' => 'created', 'auditable_type' => 'App\\Models\\Form'],
            ['action' => 'updated', 'auditable_type' => 'App\\Models\\Tenant'],
            ['action' => 'created', 'auditable_type' => 'App\\Models\\Tag'],
            ['action' => 'export', 'auditable_type' => 'App\\Models\\Lead'],
        ];

        $inserts = [];
        $logCount = 100;

        for ($i = 0; $i < $logCount; $i++) {
            $actionDef = $this->pick($actions);
            $user = $this->pick($users);
            $lead = $this->pick($leads);
            $createdAt = $this->randomPastDate(60);

            $auditableId = match ($actionDef['auditable_type']) {
                'App\\Models\\User' => $user->id,
                'App\\Models\\Lead' => $lead->id,
                'App\\Models\\Tenant' => $tenant->id,
                default => rand(1, 10),
            };

            $inserts[] = [
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'action' => $actionDef['action'],
                'auditable_type' => $actionDef['auditable_type'],
                'auditable_id' => $auditableId,
                'old_values' => $actionDef['action'] === 'updated' ? json_encode(['status' => 'new']) : null,
                'new_values' => $actionDef['action'] === 'updated' ? json_encode(['status' => 'contacted']) : ($actionDef['action'] === 'created' ? json_encode(['id' => $auditableId]) : null),
                'ip_address' => '192.168.1.' . rand(1, 254),
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'url' => 'https://' . $tenant->slug . '.leadhub.app/admin/' . strtolower(class_basename($actionDef['auditable_type'])) . 's',
                'tags' => $this->pick([null, 'security', 'data', 'settings']),
                'created_at' => $createdAt,
            ];
        }

        foreach (array_chunk($inserts, 500) as $chunk) {
            DB::table('audit_logs')->insert($chunk);
        }
    }

    // =========================================================================
    // TENANT SETTINGS & BRANDING
    // =========================================================================
    public function seedTenantSettings(Tenant $tenant): void
    {
        $tenant->update([
            'settings' => [
                'timezone' => 'America/New_York',
                'date_format' => 'M d, Y',
                'time_format' => '12h',
                'default_lead_status' => 'new',
                'auto_assign_leads' => true,
                'duplicate_detection' => true,
                'duplicate_fields' => ['email', 'phone'],
                'lead_notifications' => true,
                'email_from_name' => $tenant->name,
                'email_from_address' => 'noreply@' . $tenant->slug . '.com',
                'scoring_enabled' => true,
                'currency' => 'USD',
                'language' => 'en',
            ],
            'branding' => [
                'app_name' => $tenant->name,
                'primary_color' => $this->pick(['#3b82f6', '#6366f1', '#8b5cf6', '#0ea5e9']),
                'logo_url' => null,
                'favicon_url' => null,
            ],
        ]);
    }

    // =========================================================================
    // TENANT DOMAINS
    // =========================================================================
    public function seedTenantDomains(Tenant $tenant): void
    {
        TenantDomain::create([
            'tenant_id' => $tenant->id,
            'domain' => $tenant->slug . '.leadhub.app',
            'verified_at' => $this->now->copy()->subDays(30),
            'verification_token' => Str::random(64),
        ]);
    }
}
