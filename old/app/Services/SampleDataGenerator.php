<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Company;
use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\Tag;
use App\Models\Tenant;
use Carbon\Carbon;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Populates a tenant with realistic-looking demo data.  Single biggest
 * "wow moment" lever for fresh installs and demos — empty CRMs feel
 * dead even when fully featured.
 *
 * Generates:
 *   - 12 industry-flavoured tags
 *   - 8 companies (each with 1-3 leads)
 *   - 50 leads with names, emails, phones, sources, statuses
 *   - Random-distributed pipeline placement
 *
 * Idempotent on re-run only via the `tag` flag — by default each
 * call adds NEW data.  Pass clearExisting=true to wipe sample-tagged
 * data first.
 */
class SampleDataGenerator
{
    public const SAMPLE_TAG_NAME = 'Sample data';

    /**
     * @return array<string, int>  counts per entity
     */
    public function generate(Tenant $tenant, bool $clearExisting = false): array
    {
        $faker = FakerFactory::create('en_US');

        return DB::transaction(function () use ($tenant, $faker, $clearExisting) {
            if ($clearExisting) {
                $this->clearSampleData($tenant);
            }

            $counts = [
                'tags'      => 0,
                'companies' => 0,
                'leads'     => 0,
            ];

            // 1. Tags
            $tagNames = ['Hot', 'Warm', 'Cold', 'Referral', 'Demo Booked', 'Decision Maker', 'Tire Kicker', 'Past Customer', 'VIP', 'Newsletter Sub', 'Webinar Attendee', self::SAMPLE_TAG_NAME];
            $tagColors = ['#dc2626', '#f59e0b', '#3b82f6', '#10b981', '#8b5cf6', '#ec4899', '#6b7280', '#0891b2', '#d4af37', '#0ea5e9', '#7c3aed', '#a3a3a3'];
            foreach ($tagNames as $i => $name) {
                $tag = $this->safeCreate(Tag::class, [
                    'tenant_id' => $tenant->id,
                    'name'      => $name,
                    'color'     => $tagColors[$i] ?? '#6b7280',
                ]);
                if ($tag) $counts['tags']++;
            }

            // 2. Companies
            $industries = ['SaaS', 'E-commerce', 'Real Estate', 'Healthcare', 'Marketing', 'Manufacturing', 'Finance', 'Education'];
            $createdCompanies = [];
            for ($i = 0; $i < 8; $i++) {
                $company = $this->safeCreate(Company::class, [
                    'tenant_id' => $tenant->id,
                    'name'      => $faker->company(),
                    'website'   => 'https://' . Str::slug($faker->company(), '') . '.com',
                    'industry'  => $industries[array_rand($industries)],
                    'size'      => array_rand(array_flip(['1-10', '11-50', '51-200', '201-500', '500+'])),
                    'phone'     => $faker->phoneNumber(),
                ]);
                if ($company) {
                    $createdCompanies[] = $company;
                    $counts['companies']++;
                }
            }

            // 3. Leads
            $defaultPipeline = Pipeline::query()
                ->where('tenant_id', $tenant->id)
                ->orderBy('id')
                ->first();
            $stageIds = $defaultPipeline
                ? PipelineStage::query()
                    ->where('tenant_id', $tenant->id)
                    ->where('pipeline_id', $defaultPipeline->id)
                    ->pluck('id')->all()
                : [];

            $sources = ['Form', 'Cold Outbound', 'Referral', 'Paid Ads', 'Inbound Email', 'Event', 'Social'];
            $statuses = ['new', 'contacted', 'qualified', 'proposal', 'won', 'lost'];

            for ($i = 0; $i < 50; $i++) {
                $first = $faker->firstName();
                $last  = $faker->lastName();
                $company = $createdCompanies[array_rand($createdCompanies) ?? 0] ?? null;

                $lead = $this->safeCreate(Lead::class, [
                    'tenant_id'    => $tenant->id,
                    'first_name'   => $first,
                    'last_name'    => $last,
                    'email'        => strtolower("{$first}.{$last}@" . $faker->safeEmailDomain()),
                    'phone'        => $faker->phoneNumber(),
                    'source'       => $sources[array_rand($sources)],
                    'status'       => $statuses[array_rand($statuses)],
                    'company_id'   => $company?->id,
                    'pipeline_id'  => $defaultPipeline?->id,
                    'stage_id'     => $stageIds ? $stageIds[array_rand($stageIds)] : null,
                    'value'        => $faker->numberBetween(500, 25000),
                    'created_at'   => Carbon::now()->subDays($faker->numberBetween(0, 90)),
                ]);
                if ($lead) $counts['leads']++;
            }

            return $counts;
        });
    }

    public function clearSampleData(Tenant $tenant): int
    {
        try {
            $tag = Tag::query()
                ->where('tenant_id', $tenant->id)
                ->where('name', self::SAMPLE_TAG_NAME)
                ->first();
            if (! $tag) return 0;

            $deleted = 0;
            if (\Schema::hasTable('lead_tag')) {
                $leadIds = DB::table('lead_tag')->where('tag_id', $tag->id)->pluck('lead_id');
                $deleted = Lead::query()
                    ->withoutGlobalScope('tenant')
                    ->whereIn('id', $leadIds)
                    ->where('tenant_id', $tenant->id)
                    ->delete();
            }

            $tag->delete();
            return $deleted;
        } catch (\Throwable $e) {
            Log::warning('SampleDataGenerator clear failed', [
                'tenant_id' => $tenant->id,
                'error'     => $e->getMessage(),
            ]);
            return 0;
        }
    }

    /**
     * Wraps Eloquent::create in a try/catch so a single bad row
     * (schema drift, custom field constraint, etc.) doesn't kill
     * the whole population pass.
     */
    protected function safeCreate(string $model, array $attrs): mixed
    {
        try {
            return $model::create($attrs);
        } catch (\Throwable $e) {
            Log::debug('SampleDataGenerator: create failed', [
                'model' => $model,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
