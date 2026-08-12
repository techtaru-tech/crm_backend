<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/filament/calendar.css') }}">

    {{-- Filters --}}
    <div class="cal-filters">
        <div class="cal-filter-group">
            <label>{{ __('filament/calendar.filter_date_range') }}</label>
            <select wire:model.live="rangePreset">
                <option value="week">{{ __('filament/calendar.range_this_week') }}</option>
                <option value="month">{{ __('filament/calendar.range_this_month') }}</option>
                <option value="next30">{{ __('filament/calendar.range_next_30') }}</option>
            </select>
        </div>

        <div class="cal-filter-group">
            <label>{{ __('filament/calendar.filter_assigned_to') }}</label>
            <select wire:model.live="assignedFilter">
                <option value="">{{ __('filament/calendar.all_users') }}</option>
                @foreach($users as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <div class="cal-filter-group">
            <label>{{ __('filament/calendar.filter_status') }}</label>
            <select wire:model.live="statusFilter">
                <option value="">{{ __('filament/calendar.status_all') }}</option>
                <option value="pending">{{ __('filament/calendar.status_pending') }}</option>
                <option value="completed">{{ __('filament/calendar.status_completed') }}</option>
                <option value="overdue">{{ __('filament/calendar.status_overdue') }}</option>
            </select>
        </div>

        <div class="cal-legend">
            <div class="cal-legend-item">
                <span class="cal-legend-dot cal-legend-dot--upcoming"></span>
                {{ __('filament/calendar.legend_upcoming') }}
            </div>
            <div class="cal-legend-item">
                <span class="cal-legend-dot cal-legend-dot--overdue"></span>
                {{ __('filament/calendar.legend_overdue') }}
            </div>
            <div class="cal-legend-item">
                <span class="cal-legend-dot cal-legend-dot--completed"></span>
                {{ __('filament/calendar.legend_completed') }}
            </div>
        </div>
    </div>

    {{-- Calendar container --}}
    <div id="task-calendar"></div>

    {{-- FullCalendar — vendored locally under public/vendor/ (no CDN). --}}
    <script src="{{ asset('vendor/fullcalendar/index.global.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('task-calendar');
            // Security: event titles are attacker-influenced (lead names
            // arrive via public forms / IMAP). The JSON_HEX_TAG flag
            // escapes the < and > characters to their \u00XX form so a
            // title can never inject a closing script tag and break out
            // of this block. JSON_HEX_AMP / APOS / QUOT add belt-and-
            // suspenders against other context-escape vectors.
            //
            // This comment must NEVER contain the literal closing script
            // tag sequence: the browser HTML parser ends the script
            // element at that sequence even inside a JS comment, which
            // would dump the rest of this block onto the page as text.
            let events = @json($events, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                // FullCalendar reads this locale code to localize month/day
                // names internally; the explicit buttonText below covers the
                // toolbar labels that don't ship in the core bundle.
                locale: @js(app()->getLocale()),
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                buttonText: {
                    today: @js(__('filament/calendar.btn_today')),
                    month: @js(__('filament/calendar.btn_month')),
                    week:  @js(__('filament/calendar.btn_week')),
                    list:  @js(__('filament/calendar.btn_list')),
                },
                events: events,
                eventClick: function (info) {
                    if (info.event.url) {
                        info.jsEvent.preventDefault();
                        window.location.href = info.event.url;
                    }
                },
                datesSet: function (dateInfo) {
                    const start = dateInfo.startStr.substring(0, 10);
                    const end = dateInfo.endStr.substring(0, 10);
                    @this.call('updateDateRange', start, end);
                },
                height: 'auto',
                dayMaxEvents: 4,
                eventDisplay: 'block',
                nowIndicator: true,
            });

            calendar.render();

            // Listen for Livewire-dispatched refresh
            Livewire.on('calendar-refresh', () => {
                // Re-fetch events from the component
                @this.call('getEvents').then(function (newEvents) {
                    calendar.removeAllEvents();
                    calendar.addEventSource(newEvents);
                });
            });
        });
    </script>
</x-filament-panels::page>
