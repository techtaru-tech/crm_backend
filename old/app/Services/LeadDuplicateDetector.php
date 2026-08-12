<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadDuplicate;
use Illuminate\Support\Facades\DB;

class LeadDuplicateDetector
{
    public function findExisting(int $tenantId, ?string $email, ?string $phone, ?int $excludeLeadId = null): ?Lead
    {
        if (! $email && ! $phone) {
            return null;
        }

        $normalizedPhone = Lead::normalizePhone($phone);

        $query = Lead::where('tenant_id', $tenantId)
            ->where(function ($q) use ($email, $normalizedPhone) {
                if ($email) {
                    $q->orWhere('email', strtolower(trim($email)));
                }
                if ($normalizedPhone) {
                    $q->orWhere('phone_normalized', $normalizedPhone);
                }
            });

        if ($excludeLeadId) {
            $query->where('id', '!=', $excludeLeadId);
        }

        return $query->oldest()->first();
    }

    public function matchField(?string $existingEmail, ?string $existingPhone, ?string $newEmail, ?string $newPhone): string
    {
        if ($existingEmail && $newEmail && strtolower($existingEmail) === strtolower($newEmail)) {
            return 'email';
        }

        return 'phone';
    }

    public function linkDuplicate(Lead $original, Lead $duplicate): void
    {
        $matchField = $this->matchField(
            $original->email,
            $original->phone,
            $duplicate->email,
            $duplicate->phone
        );

        DB::transaction(function () use ($original, $duplicate, $matchField) {
            LeadDuplicate::firstOrCreate([
                'original_lead_id'  => $original->id,
                'duplicate_lead_id' => $duplicate->id,
            ], [
                'tenant_id'   => $original->tenant_id,
                'match_field' => $matchField,
            ]);

            $duplicate->update(['is_duplicate' => true]);
        });
    }

    public function detectAndLink(Lead $newLead): bool
    {
        $existing = $this->findExisting(
            $newLead->tenant_id,
            $newLead->email,
            $newLead->phone,
            $newLead->id
        );

        if (! $existing) {
            return false;
        }

        $this->linkDuplicate($existing, $newLead);

        return true;
    }
}
