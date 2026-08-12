{{--
  Reusable date-range filter component for report pages.

  Props:
    - $dateRangeModel  (string)  wire:model key for the preset select, e.g. "dateRange"
    - $dateFromModel   (string)  wire:model key for the custom start date, e.g. "dateFrom"
    - $dateToModel     (string)  wire:model key for the custom end date, e.g. "dateTo"
    - $currentRange    (string)  current value of the preset select (passed in from the page)

  Usage:
    <x-report-date-range
        dateRangeModel="dateRange"
        dateFromModel="dateFrom"
        dateToModel="dateTo"
        :currentRange="$dateRange"
    />

  Flatpickr is vendored locally under public/vendor/ and loaded once globally
  (no third-party CDN is contacted at runtime). It is initialized on demand
  via Alpine when the custom range is active.
--}}
@props([
    'dateRangeModel' => 'dateRange',
    'dateFromModel'  => 'dateFrom',
    'dateToModel'    => 'dateTo',
    'currentRange'   => 'custom',
])

<div
    x-data="{
        open: @js($currentRange === 'custom'),
        fpFrom: null,
        fpTo: null,

        init() {
            this.$watch('open', val => {
                if (val) this.$nextTick(() => this.initFlatpickr());
            });
            if (this.open) this.$nextTick(() => this.initFlatpickr());
        },

        initFlatpickr() {
            if (!window.flatpickr) return;
            // Inject localized weekday / month names so the calendar
            // popup renders in the active app locale.  The vendored
            // flatpickr.min.js ships only the English `default` locale
            // pack — without these strings the popup labels every
            // language with English month / day names regardless of
            // the surrounding UI locale.
            const fpLocale = {
                weekdays: {
                    shorthand: @js(__('components.fp_weekdays_short')),
                    longhand:  @js(__('components.fp_weekdays_long')),
                },
                months: {
                    shorthand: @js(__('components.fp_months_short')),
                    longhand:  @js(__('components.fp_months_long')),
                },
                firstDayOfWeek: @js((int) __('components.fp_first_day_of_week')),
                rangeSeparator: ' — ',
            };
            const fromEl = this.$refs.fpFrom;
            const toEl   = this.$refs.fpTo;
            if (fromEl && !this.fpFrom) {
                this.fpFrom = flatpickr(fromEl, {
                    dateFormat: 'Y-m-d',
                    locale: fpLocale,
                    onChange: ([date]) => {
                        if (date) {
                            fromEl.dispatchEvent(new Event('input'));
                        }
                    }
                });
            }
            if (toEl && !this.fpTo) {
                this.fpTo = flatpickr(toEl, {
                    dateFormat: 'Y-m-d',
                    locale: fpLocale,
                    onChange: ([date]) => {
                        if (date) {
                            toEl.dispatchEvent(new Event('input'));
                        }
                    }
                });
            }
        }
    }"
    class="rdr-wrap"
>
    <link rel="stylesheet" href="{{ asset('css/views/components/report-date-range.css') }}">
    {{-- Flatpickr — vendored locally under public/vendor/ so no third-
         party CDN is contacted at runtime.  Only loaded once globally. --}}
    @once
        <link rel="stylesheet" href="{{ asset('vendor/flatpickr/flatpickr.min.css') }}">
        <script src="{{ asset('vendor/flatpickr/flatpickr.min.js') }}" defer
                x-init="$nextTick(() => { if (typeof flatpickr !== 'undefined') { $dispatch('flatpickr-ready') } })"></script>
    @endonce

    <div>
        <label class="rdr-label">{{ __('components.rdr_date_range_label') }}</label>
        <select
            wire:model.live="{{ $dateRangeModel }}"
            x-on:change="open = ($event.target.value === 'custom')"
            class="rdr-select"
        >
            <option value="7">{{ __('components.rdr_range_last_7_days') }}</option>
            <option value="30">{{ __('components.rdr_range_last_30_days') }}</option>
            <option value="this_month">{{ __('components.rdr_range_this_month') }}</option>
            <option value="last_month">{{ __('components.rdr_range_last_month') }}</option>
            <option value="custom">{{ __('components.rdr_range_custom') }}</option>
        </select>
    </div>

    <div x-show="open" x-cloak class="rdr-custom">
        <div>
            <label class="rdr-label">{{ __('components.rdr_from_label') }}</label>
            <input
                x-ref="fpFrom"
                type="text"
                wire:model.live="{{ $dateFromModel }}"
                placeholder="{{ __('components.rdr_date_placeholder') }}"
                class="rdr-input"
            >
        </div>
        <div>
            <label class="rdr-label">{{ __('components.rdr_to_label') }}</label>
            <input
                x-ref="fpTo"
                type="text"
                wire:model.live="{{ $dateToModel }}"
                placeholder="{{ __('components.rdr_date_placeholder') }}"
                class="rdr-input"
            >
        </div>
    </div>

    {{ $slot }}
</div>
