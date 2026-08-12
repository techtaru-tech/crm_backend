<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Resources\CouponResource\Pages;

use App\Filament\SuperAdmin\Resources\CouponResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCoupons extends ListRecords
{
    protected static string $resource = CouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
