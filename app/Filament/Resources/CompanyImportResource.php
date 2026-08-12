<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRolePermissions;
use App\Filament\Resources\CompanyImportResource\Pages;
use App\Models\CompanyImport;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class CompanyImportResource extends Resource
{
    use HasRolePermissions;
    // Companies share the 'leads' permission prefix (same as CompanyResource),
    // so anyone who can manage leads/companies can import companies.
    protected static string $permissionPrefix = 'leads';
    protected static ?string $model = CompanyImport::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static string|UnitEnum|null $navigationGroup = 'Leads';
    protected static ?int $navigationSort = 6;

    public static function getNavigationLabel(): string
    {
        return __('filament/company_imports.nav_label');
    }

    public static function getModelLabel(): string
    {
        return __('filament/company_imports.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament/company_imports.plural_model_label');
    }

    public static function getNavigationBadge(): ?string
    {
        // Wrapped so a missing company_imports table (new code copied by an
        // update before `migrate` ran) degrades to "no badge" instead of
        // 500ing the whole panel. See App\Support\NavBadge.
        return \App\Support\NavBadge::safe(function (): ?string {
            $tenantId = auth()->user()?->tenant_id;
            if (! $tenantId) {
                return null;
            }
            $count = static::getEloquentQuery()->where('status', 'processing')->count();

            return $count > 0 ? (string) $count : null;
        });
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        // The upload → map → confirm wizard lives in the Create page.
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('original_filename')
                    ->label(__('filament/company_imports.file'))
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('filament/company_imports.status'))
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'completed'  => 'success',
                        'processing' => 'warning',
                        'failed'     => 'danger',
                        default      => 'gray',
                    }),
                TextColumn::make('total_rows')->label(__('filament/company_imports.total')),
                TextColumn::make('imported_count')->label(__('filament/company_imports.imported'))->color('success'),
                TextColumn::make('duplicate_count')->label(__('filament/company_imports.dupes'))->color('warning'),
                TextColumn::make('error_count')->label(__('filament/company_imports.errors'))->color('danger'),
                TextColumn::make('user.name')->label(__('filament/company_imports.imported_by')),
                TextColumn::make('created_at')->label(__('filament/company_imports.created_at'))->dateTime()->sortable()->since(),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('8s');
    }

    public static function getEloquentQuery(): Builder
    {
        $tenantId = \App\Support\TenantContext::currentId();

        return parent::getEloquentQuery()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->with(['user']);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCompanyImports::route('/'),
            'create' => Pages\CreateCompanyImport::route('/create'),
        ];
    }
}
