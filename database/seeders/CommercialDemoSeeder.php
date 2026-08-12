<?php

namespace Database\Seeders;

use App\Models\EmailSequence;
use App\Models\EmailSequenceStep;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\MeetingType;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\Product;
use App\Models\Quote;
use App\Models\Tenant;
use App\Models\TenantBillingReceipt;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds commercial/demo records (Products, Meeting Types, Email
 * Sequences, Quotes, Invoices) for every existing tenant that is
 * missing them, so dropdowns / previews are never empty for CodeCanyon
 * buyers evaluating the product.
 *
 * Idempotent — uses updateOrCreate / existence checks on stable keys,
 * so re-running this seeder on an installed system does not duplicate
 * records.
 *
 *    php artisan db:seed --class=CommercialDemoSeeder
 *
 * IMPORTANT: every query bypasses the `tenant` global scope. When
 * seeding from CLI there is no authenticated user, so BelongsToTenant
 * fails closed (whereRaw 0=1) and updateOrCreate's initial SELECT
 * always returns empty → triggers an INSERT that collides with the
 * DB-level unique index. We filter by explicit tenant_id in every
 * query below, which is safe because we're iterating tenants one at
 * a time in a controlled server-side loop.
 */
class CommercialDemoSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::query()->withoutGlobalScope('tenant')->get();

        foreach ($tenants as $tenant) {
            $this->seedProducts($tenant);
            $this->seedMeetingTypes($tenant);
            $this->seedEmailSequences($tenant);
            $this->seedQuotesAndInvoices($tenant);
            $this->seedBillingReceipts($tenant);
        }
    }

    private function seedProducts(Tenant $tenant): void
    {
        $defaults = [
            ['name' => 'Starter Setup Package',   'sku' => 'PKG-STARTER',  'price' => 499.00,  'category' => 'Services'],
            ['name' => 'Professional Onboarding', 'sku' => 'PKG-PRO',      'price' => 1499.00, 'category' => 'Services'],
            ['name' => 'Monthly Retainer — Base', 'sku' => 'RET-BASE',     'price' => 299.00,  'category' => 'Subscription'],
            ['name' => 'Monthly Retainer — Plus', 'sku' => 'RET-PLUS',     'price' => 799.00,  'category' => 'Subscription'],
            ['name' => 'Hourly Consulting',       'sku' => 'CON-HOUR',     'price' => 150.00,  'category' => 'Services'],
            ['name' => 'Custom Integration',      'sku' => 'DEV-INT',      'price' => 2500.00, 'category' => 'Development'],
        ];

        foreach ($defaults as $p) {
            $existing = Product::withoutGlobalScope('tenant')
                ->where('tenant_id', $tenant->id)
                ->where('sku', $p['sku'])
                ->first();

            $attributes = [
                'name'        => $p['name'],
                'description' => $p['name'] . ' — included in the default catalogue.',
                'price'       => $p['price'],
                'currency'    => 'USD',
                'category'    => $p['category'],
                'is_active'   => true,
            ];

            if ($existing) {
                $existing->update($attributes);
            } else {
                Product::create(array_merge($attributes, [
                    'tenant_id' => $tenant->id,
                    'sku'       => $p['sku'],
                ]));
            }
        }
    }

    private function seedMeetingTypes(Tenant $tenant): void
    {
        $owner = User::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->orderBy('id')
            ->first();
        if (! $owner) return;

        $defaults = [
            ['name' => 'Discovery Call',  'slug' => 'discovery',  'duration_minutes' => 30, 'color' => '#6366f1'],
            ['name' => 'Product Demo',    'slug' => 'demo',       'duration_minutes' => 45, 'color' => '#22c55e'],
            ['name' => 'Strategy Session','slug' => 'strategy',   'duration_minutes' => 60, 'color' => '#f59e0b'],
        ];

        foreach ($defaults as $m) {
            $existing = MeetingType::withoutGlobalScope('tenant')
                ->where('tenant_id', $tenant->id)
                ->where('slug', $m['slug'])
                ->first();

            $attributes = [
                'user_id'              => $owner->id,
                'name'                 => $m['name'],
                'description'          => $m['name'] . ' — default meeting template.',
                'duration_minutes'     => $m['duration_minutes'],
                'buffer_minutes'       => 10,
                'advance_notice_hours' => 2,
                'future_days_max'      => 30,
                'color'                => $m['color'],
                'location_type'        => 'video',
                'location_details'     => 'Video link sent automatically',
                'is_active'            => true,
            ];

            if ($existing) {
                $existing->update($attributes);
            } else {
                MeetingType::create(array_merge($attributes, [
                    'tenant_id' => $tenant->id,
                    'slug'      => $m['slug'],
                ]));
            }
        }
    }

    private function seedEmailSequences(Tenant $tenant): void
    {
        $templates = [
            [
                'name'        => 'Welcome Sequence',
                'description' => '3-step welcome email series for new leads. Sends immediately, day 3, and day 7.',
                'steps'       => [
                    ['delay_days' => 0, 'subject' => 'Welcome to the team',              'body' => "<p>Hi {first_name},</p><p>Thanks for reaching out — we're excited to get started together. Here's what happens next…</p>"],
                    ['delay_days' => 3, 'subject' => 'A quick check-in',                 'body' => "<p>Hi {first_name},</p><p>Just following up on our last message — any questions so far?</p>"],
                    ['delay_days' => 7, 'subject' => 'Resources that might help',        'body' => "<p>Hi {first_name},</p><p>Here are a few links our other customers have found useful when getting started.</p>"],
                ],
            ],
            [
                'name'        => 'Re-engagement',
                'description' => '4-step sequence to win back cold leads. Triggered when a lead has had no activity for 30 days.',
                'steps'       => [
                    ['delay_days' => 0,  'subject' => 'Have we missed you?',             'body' => "<p>Hi {first_name},</p><p>It's been a while — just wanted to check in.</p>"],
                    ['delay_days' => 5,  'subject' => 'Still interested?',               'body' => "<p>Hi {first_name},</p><p>Let me know if now's a better time to chat.</p>"],
                    ['delay_days' => 12, 'subject' => 'Last chance — special offer',     'body' => "<p>Hi {first_name},</p><p>We're offering 20% off this month only.</p>"],
                    ['delay_days' => 21, 'subject' => 'Should we close the loop?',       'body' => "<p>Hi {first_name},</p><p>Let me know if we should put your file aside for now.</p>"],
                ],
            ],
            [
                'name'        => 'Post-Demo Follow-up',
                'description' => 'Automated follow-up after a product demo. Thanks the lead, reinforces value, asks for a decision.',
                'steps'       => [
                    ['delay_days' => 0, 'subject' => 'Thanks for the demo — recap',       'body' => "<p>Hi {first_name},</p><p>Thanks for the demo today. As promised, here's a summary of what we covered.</p>"],
                    ['delay_days' => 2, 'subject' => 'Any questions?',                    'body' => "<p>Hi {first_name},</p><p>Hope you had a chance to share internally — happy to answer any follow-up questions.</p>"],
                    ['delay_days' => 7, 'subject' => 'Ready to move forward?',            'body' => "<p>Hi {first_name},</p><p>If you're ready, I can send over a proposal today.</p>"],
                ],
            ],
        ];

        foreach ($templates as $tpl) {
            $existing = EmailSequence::withoutGlobalScope('tenant')
                ->where('tenant_id', $tenant->id)
                ->where('name', $tpl['name'])
                ->first();

            $attributes = [
                'description'   => $tpl['description'],
                'status'        => 'active',
                'stop_on_reply' => true,
                'stop_on_won'   => true,
            ];

            if ($existing) {
                $existing->update($attributes);
                $seq = $existing;
            } else {
                $seq = EmailSequence::create(array_merge($attributes, [
                    'tenant_id' => $tenant->id,
                    'name'      => $tpl['name'],
                ]));
            }

            // Only add steps on FIRST seed — scope-bypassed count so
            // the check isn't defeated by the same no-auth issue.
            $stepCount = EmailSequenceStep::withoutGlobalScope('tenant')
                ->where('sequence_id', $seq->id)
                ->count();

            if ($stepCount === 0) {
                foreach ($tpl['steps'] as $idx => $step) {
                    EmailSequenceStep::create([
                        'sequence_id' => $seq->id,
                        'tenant_id'   => $tenant->id,
                        'sort_order'  => $idx + 1,
                        'delay_days'  => $step['delay_days'],
                        'subject'     => $step['subject'],
                        'body_html'   => $step['body'],
                    ]);
                }
            }
        }
    }

    private function seedQuotesAndInvoices(Tenant $tenant): void
    {
        $leads = Lead::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->take(5)->get();
        if ($leads->isEmpty()) return;

        $owner = User::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)
            ->orderBy('id')->first();
        $ownerId = $owner?->id;

        // Skip if quotes/invoices already exist for this tenant — the
        // operator may have added real data and we don't want to mix
        // demo rows with their production data.
        $hasQuotes = Quote::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)->exists();
        $hasInvoices = Invoice::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenant->id)->exists();
        if ($hasQuotes || $hasInvoices) {
            return;
        }

        $statuses = ['draft', 'sent', 'accepted', 'sent', 'accepted'];

        foreach ($leads as $idx => $lead) {
            $subtotal = [1200, 2400, 3500, 900, 4800][$idx] ?? 1500;
            $taxRate  = 20.0;
            $taxAmt   = round($subtotal * $taxRate / 100, 2);
            $total    = $subtotal + $taxAmt;

            $status = $statuses[$idx] ?? 'draft';
            $quote = Quote::create([
                'tenant_id'       => $tenant->id,
                'lead_id'         => $lead->id,
                'company_id'      => $lead->company_id,
                'created_by'      => $ownerId,
                'quote_number'    => 'Q-' . $tenant->id . '-' . str_pad((string) ($idx + 1), 4, '0', STR_PAD_LEFT),
                'status'          => $status,
                'public_token'    => Str::random(48),
                'title'           => 'Proposal — ' . ($lead->company ?: $lead->full_name),
                'introduction'    => 'Thank you for considering us. Below is the proposed scope and pricing.',
                'terms'           => 'Valid for 30 days. Payment due net 15 from invoice date.',
                'currency'        => 'USD',
                'subtotal'        => $subtotal,
                'tax_rate'        => $taxRate,
                'tax_amount'      => $taxAmt,
                'discount_amount' => 0,
                'total'           => $total,
                'valid_until'     => now()->addDays(30),
                'sent_at'         => in_array($status, ['sent', 'accepted']) ? now()->subDays(5) : null,
                'accepted_at'     => $status === 'accepted' ? now()->subDays(2) : null,
            ]);

            // Convert every second "accepted" quote into an invoice so
            // the Invoices page also has realistic demo data.
            if ($status === 'accepted' && $idx % 2 === 0) {
                Invoice::create([
                    'tenant_id'       => $tenant->id,
                    'lead_id'         => $lead->id,
                    'company_id'      => $lead->company_id,
                    'quote_id'        => $quote->id,
                    'created_by'      => $ownerId,
                    'invoice_number'  => 'INV-' . $tenant->id . '-' . str_pad((string) ($idx + 1), 4, '0', STR_PAD_LEFT),
                    'status'          => $idx === 0 ? 'paid' : 'sent',
                    'public_token'    => Str::random(48),
                    'currency'        => 'USD',
                    'subtotal'        => $subtotal,
                    'tax_rate'        => $taxRate,
                    'tax_amount'      => $taxAmt,
                    'discount_amount' => 0,
                    'total'           => $total,
                    'amount_paid'     => $idx === 0 ? $total : 0,
                    'issued_date'     => now()->subDays(1),
                    'due_date'        => now()->addDays(14),
                    'sent_at'         => now()->subHours(6),
                    'paid_at'         => $idx === 0 ? now()->subHours(3) : null,
                ]);
            }
        }
    }

    /**
     * Seed 12 months of synthetic SaaS-side billing receipts for the
     * tenant.  Drives the SA Dashboard's "Revenue collected (last 12
     * months)" chart and "Recent payments" feed so they aren't empty
     * for buyers evaluating the platform.
     *
     * Trial tenants are skipped — they wouldn't have any payments yet.
     * Idempotent on the (tenant, gateway, external_id) tuple so the
     * seeder can re-run without duplicating rows.
     */
    private function seedBillingReceipts(Tenant $tenant): void
    {
        // Trials have no real payments — skip them.
        if (($tenant->subscription_status ?? null) === 'trial') {
            return;
        }

        // Map plan-key → typical monthly price.  Anything we don't
        // recognize falls back to a generic mid-tier amount so the
        // chart still has a non-zero data point.
        $price = match ($tenant->plan ?? '') {
            'business', 'enterprise' => 199.00,
            'professional', 'pro'    => 79.00,
            'starter'                => 29.00,
            default                  => 49.00,
        };

        $gateways = ['stripe', 'paypal', 'razorpay', 'paystack', 'manual'];

        for ($i = 0; $i < 12; $i++) {
            $issuedAt = now()->startOfMonth()->subMonths($i)
                ->addDays(rand(0, 27))
                ->setTime(rand(8, 18), rand(0, 59));

            $externalId = sprintf(
                'demo_evt_%d_%s',
                $tenant->id,
                $issuedAt->format('Y_m')
            );

            // Idempotency guard — re-running the seeder must not
            // duplicate rows.  The migration also has a unique index
            // on (tenant_id, gateway, external_id) but checking here
            // avoids the wasted INSERT-then-error round-trip.
            $exists = TenantBillingReceipt::query()
                ->where('tenant_id', $tenant->id)
                ->where('external_id', $externalId)
                ->exists();

            if ($exists) {
                continue;
            }

            $gateway = $gateways[array_rand($gateways)];

            $receipt = TenantBillingReceipt::create([
                'tenant_id'      => $tenant->id,
                'gateway'        => $gateway,
                'external_id'    => $externalId,
                'plan_key'       => $tenant->plan ?? 'starter',
                'amount'         => $price,
                'currency'       => 'USD',
                'receipt_number' => 'LH-' . $issuedAt->format('Y') . '-PENDING-' . Str::random(8),
                'issued_at'      => $issuedAt,
                'metadata'       => [
                    'source' => 'demo_seeder',
                    'month'  => $issuedAt->format('Y-m'),
                ],
            ]);

            // Mint the final sequential receipt_number now that we
            // know the auto-increment id — same shape the live
            // gateway-driven flow produces.
            $receipt->receipt_number = 'LH-' . $issuedAt->format('Y') . '-' . str_pad(
                (string) $receipt->id,
                6,
                '0',
                STR_PAD_LEFT
            );
            $receipt->save();
        }
    }
}
