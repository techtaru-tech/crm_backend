<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Concerns\PageRequiresPermission;

use App\Models\AuditLog;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class AuditLogPage extends Page implements HasTable
{
    use PageRequiresPermission;

    protected static string $requiredPermission = 'settings.view';

    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-clipboard-document-list';
    protected static string|\UnitEnum|null $navigationGroup = 'Reports';
    protected static ?int    $navigationSort  = 9;
    protected string $view = 'filament.pages.reports.audit-log-page';

    public static function getNavigationLabel(): string
    {
        return __('filament/audit_log.navigation_label');
    }

    public function getTitle(): string
    {
        return __('filament/audit_log.title');
    }

    protected function getTableQuery(): Builder
    {
        $tenantId = auth()->user()?->tenant_id;
        return AuditLog::where('tenant_id', $tenantId)->latest('created_at');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('created_at')
                ->label(__('filament/audit_log.col_time'))
                ->formatStateUsing(fn ($state) => $state?->translatedFormat('M j, Y H:i:s'))
                ->sortable()
                ->searchable(false),

            TextColumn::make('user_name')
                ->label(__('filament/audit_log.col_user'))
                ->default(__('filament/audit_log.col_user_default'))
                ->searchable(),

            TextColumn::make('action')
                ->label(__('filament/audit_log.col_action'))
                ->badge()
                ->colors([
                    'success' => fn($state) => in_array($state, ['created', 'login']),
                    'warning' => fn($state) => in_array($state, ['updated']),
                    'danger'  => fn($state) => in_array($state, ['deleted', 'logout']),
                    'gray'    => fn($state) => true,
                ])
                ->formatStateUsing(fn (?string $state): string => match ($state) {
                    'created' => __('filament/audit_log.action_created'),
                    'login'   => __('filament/audit_log.action_login'),
                    'updated' => __('filament/audit_log.action_updated'),
                    'deleted' => __('filament/audit_log.action_deleted'),
                    'logout'  => __('filament/audit_log.action_logout'),
                    default   => (string) $state,
                }),

            TextColumn::make('auditable_type')
                ->label(__('filament/audit_log.col_model'))
                ->formatStateUsing(fn($state) => $state ? class_basename($state) : '—')
                ->searchable(false),

            TextColumn::make('auditable_id')
                ->label(__('filament/audit_log.col_record'))
                ->formatStateUsing(function ($state, $record) {
                    if (! $state || ! $record->auditable_type) {
                        return '—';
                    }
                    return '#' . $state;
                })
                ->url(function ($record) {
                    if (! $record->auditable_id || ! $record->auditable_type) {
                        return null;
                    }
                    $map = [
                        'App\\Models\\Lead'       => fn($id) => '/admin/leads/' . $id,
                        'App\\Models\\Form'       => fn($id) => '/admin/forms/' . $id,
                        'App\\Models\\Automation' => fn($id) => '/admin/automations/' . $id,
                        'App\\Models\\User'       => fn($id) => '/admin/users/' . $id,
                    ];
                    $resolver = $map[$record->auditable_type] ?? null;
                    return $resolver ? $resolver($record->auditable_id) : null;
                })
                ->openUrlInNewTab()
                ->color('primary'),

            TextColumn::make('ip_address')
                ->label(__('filament/audit_log.col_ip'))
                ->searchable(),

            TextColumn::make('url')
                ->label(__('filament/audit_log.col_url'))
                ->limit(50)
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    protected function getTableFilters(): array
    {
        $tenantId = auth()->user()?->tenant_id;
        $users    = User::where('tenant_id', $tenantId)->orderBy('name')->pluck('name', 'id');

        return [
            SelectFilter::make('user_id')
                ->label(__('filament/audit_log.filter_user'))
                ->options($users)
                ->query(fn(Builder $query, array $data) =>
                    $data['value'] ? $query->where('user_id', $data['value']) : $query
                ),

            SelectFilter::make('action')
                ->label(__('filament/audit_log.filter_action'))
                ->options([
                    'created' => __('filament/audit_log.action_created'),
                    'updated' => __('filament/audit_log.action_updated'),
                    'deleted' => __('filament/audit_log.action_deleted'),
                    'login'   => __('filament/audit_log.action_login'),
                    'logout'  => __('filament/audit_log.action_logout'),
                ]),

            Filter::make('date_range')
                ->label(__('filament/audit_log.filter_date_range'))
                ->form([
                    DatePicker::make('from')->label(__('filament/audit_log.filter_from')),
                    DatePicker::make('to')->label(__('filament/audit_log.filter_to')),
                ])
                ->query(function (Builder $query, array $data) {
                    if ($data['from']) {
                        $query->whereDate('created_at', '>=', $data['from']);
                    }
                    if ($data['to']) {
                        $query->whereDate('created_at', '<=', $data['to']);
                    }
                    return $query;
                }),
        ];
    }

    protected function getTableSearchPlaceholder(): string
    {
        return __('filament/audit_log.search_placeholder');
    }
}
