<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Resources\TenantResource\Pages;

use App\Filament\SuperAdmin\Resources\TenantResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTenant extends EditRecord
{
    protected static string $resource = TenantResource::class;

    /**
     * Match CreateTenant — let the edit-tenant form use the full
     * panel width.  Same 2/3 + 1/3 grid reads better wide.
     */
    public function getMaxContentWidth(): \Filament\Support\Enums\Width|string|null
    {
        return \Filament\Support\Enums\Width::Full;
    }

    /**
     * Surface Suspend / Reactivate / Impersonate at the top of the
     * edit page so the SA can lock or unlock a workspace without
     * having to go back to the list.  Same factory methods used by
     * the table row, defined once on the Resource — behaviour stays
     * consistent across both surfaces.
     */
    protected function getHeaderActions(): array
    {
        return [
            TenantResource::impersonateAction(),
            TenantResource::suspendAction(),
            TenantResource::reactivateAction(),
            DeleteAction::make(),
        ];
    }
}
