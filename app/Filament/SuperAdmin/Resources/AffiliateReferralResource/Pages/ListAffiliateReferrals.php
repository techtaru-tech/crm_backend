<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Resources\AffiliateReferralResource\Pages;

use App\Filament\SuperAdmin\Resources\AffiliateReferralResource;
use Filament\Resources\Pages\ListRecords;

/**
 * Affiliate-commission list page.
 *
 * No header "Create" action: commission rows are booked
 * automatically by the payment gateways when a referred tenant
 * pays — the operator only approves / pays / reverses them, which
 * happens via the per-row and bulk actions defined on the resource.
 */
class ListAffiliateReferrals extends ListRecords
{
    protected static string $resource = AffiliateReferralResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
