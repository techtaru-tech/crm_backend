<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CustomFieldDefinition;
use App\Models\DealItem;
use App\Models\EmailSequence;
use App\Models\EmailSequenceEnrollment;
use App\Models\EmailSequenceStep;
use App\Models\FeatureRequest;
use App\Models\FeatureRequestVote;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\LandingPage;
use App\Models\LandingPageSection;
use App\Models\Lead;
use App\Models\LeadCall;
use App\Models\LeadEmail;
use App\Models\LeadMessage;
use App\Models\MeetingAvailabilityRule;
use App\Models\MeetingBooking;
use App\Models\MeetingType;
use App\Models\PageView;
use App\Models\Product;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\SavedFilter;
use App\Models\Tenant;
use App\Models\TrackingSnippet;
use App\Models\User;
use App\Models\UserDashboardPreference;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Layered demo data for the v2 entities (Phase 3–6 features).
 *
 * Runs AFTER DemoSeeder on the primary "acme-digital" tenant and
 * back-fills every v2 feature with realistic volume.
 *
 * Per-section idempotency: each seed method checks whether its target
 * table already has data for the tenant before inserting.  Running
 * this seeder twice is safe — each method no-ops if its section is
 * populated.  Drop a single table to re-seed just that section.
 */
class DemoV2Seeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::where('slug', 'acme-digital')->first();
        if (! $tenant) {
            $this->command?->warn('DemoV2Seeder: primary demo tenant ("acme-digital") not found. Run DemoSeeder first.');
            return;
        }

        // Bind the tenant into the container so the BelongsToTenant global
        // scope resolves to this tenant for every subsequent query. Without
        // this, CLI-seeded reads fail-closed to `WHERE 0=1` and every
        // seed method thinks the tenant has no data.
        app()->instance('current_tenant', $tenant);

        $users = User::where('tenant_id', $tenant->id)->get();
        $leads = Lead::where('tenant_id', $tenant->id)->get();
        if ($leads->isEmpty() || $users->isEmpty()) {
            $this->command?->warn('DemoV2Seeder: no leads/users in primary tenant — nothing to layer onto.');
            return;
        }

        $this->command?->info("DemoV2Seeder: seeding v2 entities for tenant '{$tenant->name}'");

        // Phase 0: back-fill lead financials that DemoSeeder didn't set.
        // won_at / lost_at / deal_value were added after DemoSeeder was
        // written, so without this step there are no "won" leads for
        // seedQuotesAndInvoices to process and no "active" leads with a
        // deal_value for seedDealItemsOnLeads.
        $leads = $this->backfillLeadFinancials($tenant, $leads);

        // Phase 1: base entities
        $this->seedProducts($tenant);
        $this->seedCompanies($tenant, $leads);
        $this->seedCustomFields($tenant, $leads);

        // Phase 2: lead-scoped timeline data
        $this->seedLeadEmails($tenant, $leads, $users);
        $this->seedLeadMessages($tenant, $leads, $users);
        $this->seedPageViews($tenant, $leads);
        $this->seedLeadCalls($tenant, $leads, $users);
        $this->seedDealItemsOnLeads($tenant, $leads);

        // Phase 3: scheduler + money
        $this->seedMeetings($tenant, $users, $leads);
        $this->seedQuotesAndInvoices($tenant, $leads, $users);
        $this->seedInvoicePayments($tenant);

        // Phase 4: marketing + automation
        $this->seedEmailSequence($tenant, $leads);
        $this->seedLandingPages($tenant);
        $this->seedTrackingSnippets($tenant);

        // Phase 5: workspace polish
        $this->seedFeatureRequests($tenant, $users);
        $this->seedFeatureRequestVotes($tenant, $users);
        $this->seedSavedFilters($users);
        $this->seedDashboardPreferences($users);
        $this->seedBusinessHours($tenant);
        $this->seedReferralLink($tenant);

        $this->command?->info('DemoV2Seeder: complete.');
    }

    protected function seedProducts(Tenant $tenant): void
    {
        if (Product::where('tenant_id', $tenant->id)->exists()) return;

        $rows = [
            ['name' => 'Starter Plan',     'sku' => 'PLAN-STARTER', 'price' =>  49, 'category' => 'Subscription'],
            ['name' => 'Growth Plan',      'sku' => 'PLAN-GROWTH',  'price' => 149, 'category' => 'Subscription'],
            ['name' => 'Pro Plan',         'sku' => 'PLAN-PRO',     'price' => 299, 'category' => 'Subscription'],
            ['name' => 'Onboarding',       'sku' => 'SVC-ONBOARD',  'price' => 500, 'category' => 'Service'],
            ['name' => 'Premium Support',  'sku' => 'SVC-SUPPORT',  'price' => 200, 'category' => 'Service'],
        ];
        foreach ($rows as $r) {
            Product::create(array_merge($r, [
                'tenant_id'  => $tenant->id,
                'currency'   => 'USD',
                'is_active'  => true,
                'description'=> 'Demo product — ' . $r['name'],
            ]));
        }
    }

    protected function seedCompanies(Tenant $tenant, $leads): void
    {
        if (Company::where('tenant_id', $tenant->id)->exists()) return;

        $names = ['Acme Corp', 'Contoso Ltd', 'Globex', 'Initech', 'Umbrella Co', 'Stark Industries', 'Wayne Enterprises', 'Pied Piper', 'Hooli', 'Massive Dynamic'];
        $industries = ['Technology', 'Marketing', 'Healthcare', 'Finance', 'Manufacturing', 'Retail'];
        $sizes = ['small', 'medium', 'large', 'enterprise'];

        $companies = collect();
        foreach ($names as $name) {
            $companies->push(Company::create([
                'tenant_id' => $tenant->id,
                'name'      => $name,
                'domain'    => strtolower(str_replace(' ', '', $name)) . '.com',
                'industry'  => $industries[array_rand($industries)],
                'size'      => $sizes[array_rand($sizes)],
                'website'   => 'https://' . strtolower(str_replace(' ', '', $name)) . '.com',
                'city'      => 'San Francisco',
                'country'   => 'USA',
            ]));
        }

        foreach ($leads as $lead) {
            if (mt_rand(1, 100) <= 40) {
                $lead->update(['company_id' => $companies->random()->id]);
            }
        }
    }

    protected function seedCustomFields(Tenant $tenant, $leads): void
    {
        if (CustomFieldDefinition::where('tenant_id', $tenant->id)->exists()) return;

        $fields = [
            ['name' => 'Budget Range',    'key' => 'budget_range',    'field_type' => 'select',        'options' => ['Under $10k', '$10k–$50k', '$50k–$250k', '$250k–$1M', '$1M+']],
            ['name' => 'Timeline',        'key' => 'timeline',        'field_type' => 'select',        'options' => ['ASAP', '1–3 months', '3–6 months', '6–12 months', 'Just exploring']],
            ['name' => 'Decision Makers', 'key' => 'decision_makers', 'field_type' => 'multi_select', 'options' => ['CEO', 'CTO', 'CFO', 'CMO', 'VP Sales', 'Procurement']],
            ['name' => 'Contract Value',  'key' => 'contract_value',  'field_type' => 'number',        'options' => null],
            ['name' => 'Is VIP',          'key' => 'is_vip',          'field_type' => 'checkbox',      'options' => null],
        ];

        foreach ($fields as $i => $f) {
            CustomFieldDefinition::create([
                'tenant_id'         => $tenant->id,
                'entity_type'       => 'lead',
                'name'              => $f['name'],
                'key'               => $f['key'],
                'field_type'        => $f['field_type'],
                'options'           => $f['options'],
                'required'          => false,
                'show_in_form'      => true,
                'show_in_table'     => $i < 2,
                'show_in_filters'   => $i < 2,
                'sort_order'        => $i,
            ]);
        }

        // Populate ~40% of leads with realistic values.
        foreach ($leads->random(max(1, (int) ($leads->count() * 0.4))) as $lead) {
            $cf = (array) ($lead->custom_fields ?? []);
            $cf['budget_range']    = $this->pick(['Under $10k', '$10k–$50k', '$50k–$250k', '$250k–$1M', '$1M+']);
            $cf['timeline']        = $this->pick(['ASAP', '1–3 months', '3–6 months', '6–12 months']);
            if (mt_rand(0, 1)) {
                $cf['decision_makers'] = array_slice(['CEO', 'CTO', 'CFO', 'CMO', 'VP Sales'], 0, mt_rand(1, 3));
            }
            if (mt_rand(0, 1)) {
                $cf['contract_value'] = mt_rand(5, 500) * 1000;
            }
            $cf['is_vip'] = mt_rand(0, 9) === 0; // ~10% VIPs
            $lead->update(['custom_fields' => $cf]);
        }
    }

    protected function seedLeadEmails(Tenant $tenant, $leads, $users): void
    {
        if (LeadEmail::where('tenant_id', $tenant->id)->exists()) return;

        $sampled = $leads->random(min(60, $leads->count()));
        foreach ($sampled as $lead) {
            $count = mt_rand(1, 10);
            for ($i = 0; $i < $count; $i++) {
                $direction = mt_rand(0, 1) ? 'outbound' : 'inbound';
                $sentAt    = $this->dateInPast(90);
                LeadEmail::create([
                    'tenant_id'    => $tenant->id,
                    'lead_id'      => $lead->id,
                    'user_id'      => $direction === 'outbound' ? $users->random()->id : null,
                    'direction'    => $direction,
                    'message_id'   => '<' . Str::random(24) . '@demo.acme.test>',
                    'subject'      => $this->pick(['Follow-up on our chat', 'Pricing question', 'Next steps', 'Thanks for your time', 'Checking in']),
                    'body_text'    => 'Hi ' . ($lead->first_name ?: 'there') . ",\n\nJust following up on our conversation. Let me know if you have any questions.\n\nBest,\n" . ($users->first()?->name ?? 'The team'),
                    'from_address' => $direction === 'outbound' ? ($users->first()?->email ?? 'team@demo.test') : ($lead->email ?: 'contact@demo.test'),
                    'to_addresses' => [$direction === 'outbound' ? ($lead->email ?: 'contact@demo.test') : 'team@demo.test'],
                    'sent_at'      => $sentAt,
                    'opened_at'    => mt_rand(0, 100) < 60 ? $sentAt->copy()->addMinutes(mt_rand(5, 300)) : null,
                    'clicked_at'   => mt_rand(0, 100) < 25 ? $sentAt->copy()->addMinutes(mt_rand(10, 400)) : null,
                    'created_at'   => $sentAt,
                    'updated_at'   => $sentAt,
                ]);
            }
        }
    }

    protected function seedLeadMessages(Tenant $tenant, $leads, $users): void
    {
        if (LeadMessage::where('tenant_id', $tenant->id)->exists()) return;

        $channels = ['whatsapp', 'sms', 'telegram'];
        $sampled  = $leads->random(min(35, $leads->count()));
        foreach ($sampled as $lead) {
            $count = mt_rand(1, 5);
            for ($i = 0; $i < $count; $i++) {
                $direction = mt_rand(0, 1) ? 'outbound' : 'inbound';
                $channel   = $channels[array_rand($channels)];
                $created   = $this->dateInPast(60);
                LeadMessage::create([
                    'tenant_id'       => $tenant->id,
                    'lead_id'         => $lead->id,
                    'user_id'         => $direction === 'outbound' ? $users->random()->id : null,
                    'channel'         => $channel,
                    'direction'       => $direction,
                    'from_identifier' => $direction === 'outbound' ? '+15551234567' : ($lead->phone ?: '+15559990000'),
                    'to_identifier'   => $direction === 'outbound' ? ($lead->phone ?: '+15559990000') : '+15551234567',
                    'body'            => $this->pick(['Hey! Following up on your interest.', 'Got time for a quick call?', 'Here are the details you asked for.', 'Thanks!', 'Yes, I\'m interested.']),
                    'status'          => $this->pick(['sent', 'delivered', 'read']),
                    'created_at'      => $created,
                    'updated_at'      => $created,
                ]);
            }
        }
    }

    protected function seedPageViews(Tenant $tenant, $leads): void
    {
        if (PageView::where('tenant_id', $tenant->id)->exists()) return;

        $paths = ['/', '/pricing', '/features', '/about', '/blog/getting-started', '/contact', '/demo', '/docs'];
        $sampled = $leads->random(min(100, $leads->count()));
        foreach ($sampled as $lead) {
            $count = mt_rand(1, 10);
            for ($i = 0; $i < $count; $i++) {
                $path = $paths[array_rand($paths)];
                $viewedAt = $this->dateInPast(30);
                PageView::create([
                    'tenant_id'     => $tenant->id,
                    'visitor_token' => Str::random(40),
                    'lead_id'       => $lead->id,
                    'url'           => 'https://acme.example' . $path,
                    'path'          => $path,
                    'title'         => ucfirst(trim(str_replace(['/', '-'], [' ', ' '], $path))) ?: 'Home',
                    'utm_source'    => $this->pick(['google', 'facebook', 'linkedin', 'direct', null]),
                    'utm_campaign'  => $this->pick(['spring-2026', 'product-launch', 'retargeting', null]),
                    'viewed_at'     => $viewedAt,
                    'duration_seconds' => mt_rand(5, 240),
                    'created_at'    => $viewedAt,
                    'updated_at'    => $viewedAt,
                ]);
            }
        }
    }

    protected function seedLeadCalls(Tenant $tenant, $leads, $users): void
    {
        if (LeadCall::where('tenant_id', $tenant->id)->exists()) return;

        $withPhone = $leads->filter(fn ($l) => $l->phone);
        if ($withPhone->isEmpty()) return;

        $sampled = $withPhone->random(min(25, $withPhone->count()));
        foreach ($sampled as $lead) {
            $count = mt_rand(1, 3);
            for ($i = 0; $i < $count; $i++) {
                $direction = mt_rand(0, 1) ? 'outbound' : 'inbound';
                $startedAt = $this->dateInPast(60);
                $duration  = mt_rand(30, 600);
                LeadCall::create([
                    'tenant_id'       => $tenant->id,
                    'lead_id'         => $lead->id,
                    'user_id'         => $users->random()->id,
                    'direction'       => $direction,
                    'from_number'     => $direction === 'outbound' ? '+15551234567' : $lead->phone,
                    'to_number'       => $direction === 'outbound' ? $lead->phone   : '+15551234567',
                    'status'          => $this->pick(['completed', 'completed', 'completed', 'no-answer', 'busy']),
                    'duration_seconds'=> $duration,
                    'started_at'      => $startedAt,
                    'ended_at'        => $startedAt->copy()->addSeconds($duration),
                    'ai_summary'      => 'Discussed pricing and next steps. Lead agreed to review the proposal by end of week.',
                    'created_at'      => $startedAt,
                    'updated_at'      => $startedAt,
                ]);
            }
        }
    }

    protected function seedDealItemsOnLeads(Tenant $tenant, $leads): void
    {
        if (DealItem::where('tenant_id', $tenant->id)->exists()) return;

        $products = Product::where('tenant_id', $tenant->id)->get();
        if ($products->isEmpty()) return;

        // Attach 1-3 line items to ~20 active (non-won, non-lost) leads with deal_value.
        $active = $leads->filter(fn ($l) => is_null($l->won_at) && is_null($l->lost_at) && $l->deal_value > 0)
                        ->take(20);

        foreach ($active as $lead) {
            foreach ($products->random(mt_rand(1, 3)) as $p) {
                $qty      = mt_rand(1, 3);
                $discount = $this->pick([0, 0, 0, 5, 10, 15]); // mostly no discount
                $total    = $p->price * $qty * (1 - $discount / 100);
                DealItem::create([
                    'tenant_id'        => $tenant->id,
                    'lead_id'          => $lead->id,
                    'product_id'       => $p->id,
                    'name'             => $p->name,
                    'unit_price'       => $p->price,
                    'quantity'         => $qty,
                    'discount_percent' => $discount,
                    'total'            => $total,
                ]);
            }
        }
    }

    protected function seedMeetings(Tenant $tenant, $users, $leads): void
    {
        if (MeetingType::where('tenant_id', $tenant->id)->exists()) return;

        $host = $users->first();
        if (! $host) return;

        foreach ([1, 2, 3, 4, 5] as $dow) {
            MeetingAvailabilityRule::create([
                'user_id'    => $host->id,
                'day_of_week'=> $dow,
                'start_time' => '09:00:00',
                'end_time'   => '17:00:00',
            ]);
        }

        $types = [
            ['name' => 'Discovery Call', 'slug' => 'discovery', 'duration_minutes' => 30, 'color' => '#4f46e5'],
            ['name' => 'Product Demo',   'slug' => 'demo',      'duration_minutes' => 45, 'color' => '#10b981'],
        ];
        $typeModels = [];
        foreach ($types as $t) {
            $typeModels[] = MeetingType::create(array_merge($t, [
                'tenant_id' => $tenant->id,
                'user_id'   => $host->id,
                'description' => 'Demo meeting type — pre-populated by the seeder.',
                'buffer_minutes' => 5,
                'advance_notice_hours' => 4,
                'future_days_max' => 60,
                'location_type' => 'google_meet',
                'is_active' => true,
            ]));
        }

        $leadsWithEmail = $leads->filter(fn ($l) => $l->email);
        if ($leadsWithEmail->isEmpty()) return;
        for ($i = 0; $i < 20; $i++) {
            $type  = $typeModels[array_rand($typeModels)];
            $lead  = $leadsWithEmail->random();
            $past  = mt_rand(0, 1);
            $start = $past
                ? Carbon::now()->subDays(mt_rand(1, 180))->setTime(mt_rand(9, 15), mt_rand(0, 3) * 15)
                : Carbon::now()->addDays(mt_rand(1, 30))->setTime(mt_rand(9, 15), mt_rand(0, 3) * 15);

            MeetingBooking::create([
                'tenant_id'       => $tenant->id,
                'meeting_type_id' => $type->id,
                'user_id'         => $host->id,
                'lead_id'         => $lead->id,
                'guest_name'      => $lead->full_name ?: 'Demo Guest',
                'guest_email'     => $lead->email ?: 'guest@demo.test',
                'guest_phone'     => $lead->phone,
                'starts_at'       => $start,
                'ends_at'         => $start->copy()->addMinutes($type->duration_minutes),
                'timezone'        => 'UTC',
                'status'          => $past ? ($this->pick(['completed', 'completed', 'no_show'])) : 'confirmed',
                'metadata'        => ['source' => 'demo_seed'],
            ]);
        }
    }

    protected function seedQuotesAndInvoices(Tenant $tenant, $leads, $users): void
    {
        if (Quote::where('tenant_id', $tenant->id)->exists()) return;

        $products = Product::where('tenant_id', $tenant->id)->get();
        if ($products->isEmpty()) return;

        $owner = $users->first();
        $wonLeads = $leads->filter(fn ($l) => $l->won_at !== null)->take(15);

        foreach ($wonLeads as $lead) {
            $subtotal = 0;
            $quote = Quote::create([
                'tenant_id'    => $tenant->id,
                'lead_id'      => $lead->id,
                'company_id'   => $lead->company_id,
                'created_by'   => $owner?->id,
                'status'       => 'accepted',
                'title'        => 'Proposal — ' . ($lead->full_name ?: 'Client'),
                'introduction' => 'Thank you for the opportunity. Proposal valid for 30 days.',
                'currency'     => 'USD',
                'valid_until'  => Carbon::now()->addDays(30),
                'sent_at'      => $lead->won_at?->copy()->subDays(5),
                'accepted_at'  => $lead->won_at,
                'signed_name'  => $lead->full_name ?: 'Client',
                'signed_ip'    => '203.0.113.' . mt_rand(1, 254),
                'signed_at'    => $lead->won_at,
            ]);

            $items = $products->random(mt_rand(1, 3));
            foreach ($items as $p) {
                $qty  = mt_rand(1, 3);
                $line = $p->price * $qty;
                $subtotal += $line;
                QuoteItem::create([
                    'tenant_id'  => $tenant->id,
                    'quote_id'   => $quote->id,
                    'product_id' => $p->id,
                    'name'       => $p->name,
                    'unit_price' => $p->price,
                    'quantity'   => $qty,
                    'discount_percent' => 0,
                    'total'      => $line,
                ]);
            }
            $quote->update(['subtotal' => $subtotal, 'total' => $subtotal]);

            $invoice = Invoice::create([
                'tenant_id'     => $tenant->id,
                'lead_id'       => $lead->id,
                'company_id'    => $lead->company_id,
                'quote_id'      => $quote->id,
                'created_by'    => $owner?->id,
                'status'        => 'paid',
                'currency'      => 'USD',
                'subtotal'      => $subtotal,
                'total'         => $subtotal,
                'amount_paid'   => $subtotal,
                'issued_date'   => $lead->won_at?->toDateString() ?? now()->toDateString(),
                'due_date'      => $lead->won_at?->copy()->addDays(30)->toDateString() ?? now()->addDays(30)->toDateString(),
                'sent_at'       => $lead->won_at,
                'paid_at'       => $lead->won_at?->copy()->addDays(mt_rand(1, 20)),
            ]);
            foreach ($items as $p) {
                InvoiceItem::create([
                    'tenant_id'  => $tenant->id,
                    'invoice_id' => $invoice->id,
                    'product_id' => $p->id,
                    'name'       => $p->name,
                    'unit_price' => $p->price,
                    'quantity'   => 1,
                    'discount_percent' => 0,
                    'total'      => $p->price,
                ]);
            }
        }
    }

    protected function seedInvoicePayments(Tenant $tenant): void
    {
        if (InvoicePayment::where('tenant_id', $tenant->id)->exists()) return;

        $paidInvoices = Invoice::where('tenant_id', $tenant->id)
            ->where('status', 'paid')
            ->get();

        foreach ($paidInvoices as $invoice) {
            InvoicePayment::create([
                'tenant_id'   => $tenant->id,
                'invoice_id'  => $invoice->id,
                'gateway'     => $this->pick(['stripe', 'stripe', 'stripe', 'paypal', 'manual']),
                'external_id' => 'ch_' . Str::random(24),
                'amount'      => $invoice->total,
                'currency'    => $invoice->currency,
                'status'      => 'succeeded',
                'meta'        => ['seeded' => true, 'method' => 'card'],
                'paid_at'     => $invoice->paid_at ?? $invoice->created_at,
            ]);
        }
    }

    protected function seedEmailSequence(Tenant $tenant, $leads): void
    {
        if (EmailSequence::where('tenant_id', $tenant->id)->exists()) return;

        $seq = EmailSequence::create([
            'tenant_id'     => $tenant->id,
            'name'          => 'New Lead Nurture',
            'description'   => '3-step drip for newly-captured leads.',
            'status'        => 'active',
            'stop_on_reply' => true,
            'stop_on_won'   => true,
        ]);

        $steps = [
            ['subject' => 'Welcome to our community, {first_name}', 'body_html' => '<p>Hi {first_name}, thanks for signing up!</p>'],
            ['subject' => 'Quick question, {first_name}?',          'body_html' => '<p>What brought you to us today?</p>'],
            ['subject' => 'A resource you might like',              'body_html' => '<p>Check out our onboarding guide.</p>'],
        ];
        foreach ($steps as $i => $s) {
            EmailSequenceStep::create([
                'tenant_id'   => $tenant->id,
                'sequence_id' => $seq->id,
                'sort_order'  => $i,
                'delay_days'  => $i === 0 ? 0 : ($i === 1 ? 2 : 5),
                'delay_hours' => 0,
                'subject'     => $s['subject'],
                'body_html'   => $s['body_html'],
            ]);
        }

        $enrolled = $leads->random(min(30, $leads->count()));
        foreach ($enrolled as $lead) {
            $enrolledAt = $this->dateInPast(30);
            $step = mt_rand(0, 3);
            EmailSequenceEnrollment::create([
                'tenant_id'    => $tenant->id,
                'sequence_id'  => $seq->id,
                'lead_id'      => $lead->id,
                'current_step' => $step,
                'status'       => $step >= 3 ? 'completed' : 'active',
                'enrolled_at'  => $enrolledAt,
                'next_send_at' => $step >= 3 ? null : Carbon::now()->addHours(mt_rand(1, 48)),
                'completed_at' => $step >= 3 ? $enrolledAt->copy()->addDays(7) : null,
            ]);
        }
    }

    protected function seedLandingPages(Tenant $tenant): void
    {
        if (LandingPage::where('tenant_id', $tenant->id)->exists()) return;

        // Pull the three professional templates from config so the demo
        // pages are production-grade (full hero, testimonials, pricing,
        // FAQ, CTA) rather than the toy three-section placeholders the
        // earlier seeder produced.
        $templates = config('landing_page_templates', []);

        $pages = [
            [
                'template_key' => 'saas',
                'slug'         => 'platform',
                'name'         => 'SaaS Platform',
                'title'        => 'Platform overview — everything you get with Acme',
                'meta'         => 'Close more deals with less effort. See every feature, every plan, every customer story.',
                'views'        => 2840,
                'conversions' => 217,
            ],
            [
                'template_key' => 'lead_capture',
                'slug'         => 'sales-playbook',
                'name'         => 'Sales Playbook (Ebook)',
                'title'        => 'Free guide: 47 tactics top performers use to close 2× more deals',
                'meta'         => 'Download the 2026 Sales Playbook — 42 pages, zero fluff.',
                'views'        => 1120,
                'conversions' => 298,
            ],
            [
                'template_key' => 'product_launch',
                'slug'         => 'v3-launch',
                'name'         => 'v3 Launch',
                'title'        => 'Meet v3 — faster, lighter, genuinely fun',
                'meta'         => 'The biggest release we\'ve ever shipped. Now live for all customers.',
                'views'        => 640,
                'conversions' => 82,
            ],
        ];

        foreach ($pages as $p) {
            $template = $templates[$p['template_key']] ?? null;
            if (! $template) continue;

            $page = LandingPage::create([
                'tenant_id'        => $tenant->id,
                'name'             => $p['name'],
                'slug'             => $p['slug'],
                'title'            => $p['title'],
                'meta_description' => $p['meta'],
                'status'           => 'published',
                'primary_color'    => '#4f46e5',
                'background_color' => '#ffffff',
                'font_family'      => 'Inter',
                'views_count'      => $p['views'],
                'conversions_count'=> $p['conversions'],
            ]);

            foreach ($template['sections'] as $i => $s) {
                LandingPageSection::create([
                    'landing_page_id' => $page->id,
                    'type'            => $s['type'],
                    'sort_order'      => $i,
                    'content'         => $s['content'],
                    'is_visible'      => true,
                ]);
            }
        }
    }

    protected function seedTrackingSnippets(Tenant $tenant): void
    {
        if (TrackingSnippet::where('tenant_id', $tenant->id)->exists()) return;

        TrackingSnippet::create([
            'tenant_id'        => $tenant->id,
            'name'             => 'Main Website',
            'allowed_domains'  => ['acme.example', 'www.acme.example'],
            'is_active'        => true,
            'views_count'      => 18420,
            'last_viewed_at'   => Carbon::now()->subMinutes(mt_rand(1, 90)),
        ]);
        TrackingSnippet::create([
            'tenant_id'        => $tenant->id,
            'name'             => 'Blog',
            'allowed_domains'  => ['blog.acme.example'],
            'is_active'        => true,
            'views_count'      => 6280,
            'last_viewed_at'   => Carbon::now()->subHours(mt_rand(1, 48)),
        ]);
    }

    protected function seedFeatureRequests(Tenant $tenant, $users): void
    {
        if (FeatureRequest::where('tenant_id', $tenant->id)->exists()) return;

        $requests = [
            ['title' => 'Add Slack integration for new leads',   'description' => 'When a new lead lands, post to a Slack channel.',      'votes' => 12, 'status' => 'planned'],
            ['title' => 'Mobile app for iOS',                     'description' => 'A native iOS app for the sales team.',                 'votes' => 28, 'status' => 'open'],
            ['title' => 'Custom dashboard widgets',              'description' => 'Let users build their own dashboard tiles.',           'votes' => 8,  'status' => 'open'],
            ['title' => 'Two-way Gmail sync',                    'description' => 'Sync sent + received emails automatically.',           'votes' => 22, 'status' => 'in_progress'],
            ['title' => 'Calendly replacement',                   'description' => 'Built-in meeting scheduler.',                          'votes' => 15, 'status' => 'shipped'],
            ['title' => 'Industry templates',                     'description' => 'Pre-configured pipelines for real estate, law, medical.', 'votes' => 19, 'status' => 'shipped'],
        ];
        foreach ($requests as $r) {
            FeatureRequest::create([
                'tenant_id'   => $tenant->id,
                'user_id'     => $users->random()->id,
                'title'       => $r['title'],
                'description' => $r['description'],
                'status'      => $r['status'],
                'votes_count' => $r['votes'],
            ]);
        }
    }

    protected function seedFeatureRequestVotes(Tenant $tenant, $users): void
    {
        if (FeatureRequestVote::query()
            ->whereIn('feature_request_id', FeatureRequest::where('tenant_id', $tenant->id)->pluck('id'))
            ->exists()) {
            return;
        }

        // Pool voters from all tenants — votes_count reflects community votes.
        $voterPool = User::where('is_super_admin', false)->pluck('id')->all();
        if (empty($voterPool)) return;

        foreach (FeatureRequest::where('tenant_id', $tenant->id)->get() as $fr) {
            $n = min($fr->votes_count, count($voterPool));
            $voters = array_rand(array_flip($voterPool), max(1, $n));
            $voters = is_array($voters) ? $voters : [$voters];
            foreach ($voters as $uid) {
                FeatureRequestVote::firstOrCreate([
                    'feature_request_id' => $fr->id,
                    'user_id'            => $uid,
                ]);
            }
        }
    }

    protected function seedSavedFilters($users): void
    {
        $owner = $users->first();
        if (! $owner) return;
        if (SavedFilter::where('user_id', $owner->id)->exists()) return;

        SavedFilter::create([
            'user_id'        => $owner->id,
            'resource'       => 'lead',
            'name'           => 'Hot Leads (score > 80)',
            'filters'        => ['score_min' => 80],
            'is_team_shared' => true,
        ]);
        SavedFilter::create([
            'user_id'        => $owner->id,
            'resource'       => 'lead',
            'name'           => 'New This Week',
            'filters'        => ['date_from' => Carbon::now()->subDays(7)->toDateString()],
            'is_team_shared' => true,
        ]);
        SavedFilter::create([
            'user_id'        => $owner->id,
            'resource'       => 'lead',
            'name'           => 'My Open Deals',
            'filters'        => ['assigned_user_id' => $owner->id],
            'is_team_shared' => false,
        ]);
    }

    protected function seedDashboardPreferences($users): void
    {
        if (UserDashboardPreference::whereIn('user_id', $users->pluck('id'))->exists()) return;

        // Role-aware defaults: agents see a focused layout, everyone else the full dashboard.
        $managerLayout = [
            'LeadsStatsOverview',
            'RevenueForecastWidget',
            'LeadsOverTimeChart',
            'LeadStatusChart',
            'LeadsBySourceChart',
            'PipelineDistributionChart',
            'LiveLeadFeed',
        ];
        $agentLayout = [
            'LeadsStatsOverview',
            'LiveLeadFeed',
        ];

        foreach ($users as $user) {
            $isAgent = method_exists($user, 'hasRole') && $user->hasRole('agent');
            UserDashboardPreference::create([
                'user_id'         => $user->id,
                'enabled_widgets' => $isAgent ? $agentLayout : $managerLayout,
                'widget_order'    => null,
            ]);
        }
    }

    protected function seedBusinessHours(Tenant $tenant): void
    {
        $settings = (array) ($tenant->settings ?? []);
        if (! empty($settings['business_hours'])) return;

        $settings['business_hours'] = [
            'timezone' => 'UTC',
            'days' => [
                0 => ['enabled' => false, 'start' => '09:00', 'end' => '17:00'], // Sun
                1 => ['enabled' => true,  'start' => '09:00', 'end' => '17:00'], // Mon
                2 => ['enabled' => true,  'start' => '09:00', 'end' => '17:00'], // Tue
                3 => ['enabled' => true,  'start' => '09:00', 'end' => '17:00'], // Wed
                4 => ['enabled' => true,  'start' => '09:00', 'end' => '17:00'], // Thu
                5 => ['enabled' => true,  'start' => '09:00', 'end' => '17:00'], // Fri
                6 => ['enabled' => false, 'start' => '09:00', 'end' => '17:00'], // Sat
            ],
        ];
        $tenant->update(['settings' => $settings]);
    }

    protected function seedReferralLink(Tenant $tenant): void
    {
        // Make pinnacle-realty referred by acme-digital to light up the
        // Referrals dashboard with at least one row.
        $pinnacle = Tenant::where('slug', 'pinnacle-realty')->first();
        if (! $pinnacle || $pinnacle->referred_by_tenant_id) {
            return;
        }
        $pinnacle->update(['referred_by_tenant_id' => $tenant->id]);
    }

    /**
     * Back-fill lead financial fields (deal_value, expected_close_date,
     * won_at/won_value, lost_at/lost_reason) that DemoSeeder doesn't set.
     *
     * Returns a refreshed collection so downstream steps work with the
     * populated fields.
     */
    protected function backfillLeadFinancials(Tenant $tenant, $leads)
    {
        // Idempotency: if any lead already has deal_value set, assume
        // we've already run this and skip.
        $alreadyDone = Lead::query()
            ->withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->whereNotNull('deal_value')
            ->exists();

        if ($alreadyDone) {
            $this->command?->info('  (lead financials already back-filled)');
            return $leads->fresh() ?? $leads;
        }

        $lostReasons = ['budget constraints', 'chose competitor', 'project cancelled', 'no longer a fit', 'unresponsive'];
        $updates = 0;

        foreach ($leads as $lead) {
            $roll = mt_rand(1, 100);
            $data = [];

            // ~60% of leads carry a deal value (1k–500k, log-biased toward smaller deals)
            if ($roll <= 60) {
                $tier = mt_rand(1, 100);
                $data['deal_value']   = $tier <= 50 ? mt_rand(1, 15) * 1000
                                     : ($tier <= 85 ? mt_rand(15, 75) * 1000
                                     :               mt_rand(75, 500) * 1000);
                $data['deal_currency'] = 'USD';
            }

            // ~30% of leads have an expected close date in the next 90 days
            if (mt_rand(1, 100) <= 30) {
                $data['expected_close_date'] = Carbon::now()->addDays(mt_rand(7, 90))->toDateString();
            }

            // ~15% won in the past 180 days
            if ($roll <= 15) {
                $wonAt = $this->dateInPast(180);
                $data['won_at']    = $wonAt;
                $data['won_value'] = $data['deal_value'] ?? mt_rand(5, 200) * 1000;
                $data['status']    = 'converted';
            }
            // ~20% lost in the past 180 days (after the won bucket)
            elseif ($roll <= 35) {
                $data['lost_at']     = $this->dateInPast(180);
                $data['lost_reason'] = $this->pick($lostReasons);
                $data['status']      = 'lost';
            }

            if (! empty($data)) {
                // updateQuietly so we don't fire observers / touch
                // updated_at (preserves the realistic created_at spread).
                $lead->forceFill($data)->saveQuietly();
                $updates++;
            }
        }

        $this->command?->info("  back-filled financials on {$updates} / " . $leads->count() . ' leads');

        // Return a fresh collection so the newly-persisted fields are
        // visible to downstream seed methods (fresh() re-hydrates).
        return Lead::query()
            ->withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->get();
    }

    /* ---------- Helpers ---------- */

    protected function dateInPast(int $maxDaysAgo): Carbon
    {
        $r = mt_rand(1, 100) / 100;
        $days = (int) round($maxDaysAgo * ($r ** 2));
        return Carbon::now()->subDays($days)->setTime(mt_rand(7, 19), mt_rand(0, 59), mt_rand(0, 59));
    }

    protected function pick(array $arr): mixed
    {
        return $arr[array_rand($arr)];
    }
}
