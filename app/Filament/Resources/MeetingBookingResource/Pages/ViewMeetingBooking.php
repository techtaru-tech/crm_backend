<?php

namespace App\Filament\Resources\MeetingBookingResource\Pages;

use App\Filament\Resources\MeetingBookingResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;

class ViewMeetingBooking extends ViewRecord
{
    protected static string $resource = MeetingBookingResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('sections.guest'))->schema([
                TextEntry::make('guest_name')
                    ->label(__('filament/meeting_bookings.field_guest_name_label')),
                TextEntry::make('guest_email')
                    ->label(__('filament/meeting_bookings.field_guest_email_label'))
                    ->copyable(),
                TextEntry::make('guest_phone')
                    ->label(__('filament/meeting_bookings.field_guest_phone_label'))
                    ->default('—'),
            ])->columns(3),
            Section::make(__('sections.meeting'))->schema([
                TextEntry::make('meetingType.name')->label(__('filament/meeting_bookings.type')),
                TextEntry::make('user.name')->label(__('filament/meeting_bookings.host')),
                TextEntry::make('starts_at')
                    ->label(__('filament/meeting_bookings.starts'))
                    ->formatStateUsing(fn ($state) => $state?->translatedFormat('M j, Y g:i A')),
                TextEntry::make('ends_at')
                    ->label(__('filament/meeting_bookings.field_ends_at_label'))
                    ->formatStateUsing(fn ($state) => $state?->translatedFormat('M j, Y g:i A')),
                TextEntry::make('timezone')
                    ->label(__('filament/meeting_bookings.field_timezone_label')),
                TextEntry::make('status')
                    ->label(__('filament/meeting_bookings.field_status_label'))
                    ->badge(),
                TextEntry::make('meeting_url')
                    ->label(__('filament/meeting_bookings.field_meeting_url_label'))
                    ->default('—'),
                TextEntry::make('notes')
                    ->label(__('filament/meeting_bookings.field_notes_label'))
                    ->default('—')
                    ->columnSpanFull(),
            ])->columns(2),
            Section::make(__('sections.cancellation'))
                ->visible(fn($record) => $record->status === 'cancelled')
                ->schema([
                    TextEntry::make('cancelled_at')
                        ->label(__('filament/meeting_bookings.field_cancelled_at_label'))
                        ->formatStateUsing(fn ($state) => $state?->translatedFormat('M j, Y g:i A')),
                    TextEntry::make('cancellation_reason')
                        ->label(__('filament/meeting_bookings.field_cancellation_reason_label'))
                        ->default('—'),
                ])->columns(2),
        ]);
    }
}
