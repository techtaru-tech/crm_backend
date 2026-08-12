
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'dateRangeModel' => 'dateRange',
    'dateFromModel'  => 'dateFrom',
    'dateToModel'    => 'dateTo',
    'currentRange'   => 'custom',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'dateRangeModel' => 'dateRange',
    'dateFromModel'  => 'dateFrom',
    'dateToModel'    => 'dateTo',
    'currentRange'   => 'custom',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div
    x-data="{
        open: <?php echo \Illuminate\Support\Js::from($currentRange === 'custom')->toHtml() ?>,
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
                    shorthand: <?php echo \Illuminate\Support\Js::from(__('components.fp_weekdays_short'))->toHtml() ?>,
                    longhand:  <?php echo \Illuminate\Support\Js::from(__('components.fp_weekdays_long'))->toHtml() ?>,
                },
                months: {
                    shorthand: <?php echo \Illuminate\Support\Js::from(__('components.fp_months_short'))->toHtml() ?>,
                    longhand:  <?php echo \Illuminate\Support\Js::from(__('components.fp_months_long'))->toHtml() ?>,
                },
                firstDayOfWeek: <?php echo \Illuminate\Support\Js::from((int) __('components.fp_first_day_of_week'))->toHtml() ?>,
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
    <link rel="stylesheet" href="<?php echo e(asset('css/views/components/report-date-range.css')); ?>">
    
    <?php if (! $__env->hasRenderedOnce('b7811ddc-11ef-4ae2-a91b-e7234d244689')): $__env->markAsRenderedOnce('b7811ddc-11ef-4ae2-a91b-e7234d244689'); ?>
        <link rel="stylesheet" href="<?php echo e(asset('vendor/flatpickr/flatpickr.min.css')); ?>">
        <script src="<?php echo e(asset('vendor/flatpickr/flatpickr.min.js')); ?>" defer
                x-init="$nextTick(() => { if (typeof flatpickr !== 'undefined') { $dispatch('flatpickr-ready') } })"></script>
    <?php endif; ?>

    <div>
        <label class="rdr-label"><?php echo e(__('components.rdr_date_range_label')); ?></label>
        <select
            wire:model.live="<?php echo e($dateRangeModel); ?>"
            x-on:change="open = ($event.target.value === 'custom')"
            class="rdr-select"
        >
            <option value="7"><?php echo e(__('components.rdr_range_last_7_days')); ?></option>
            <option value="30"><?php echo e(__('components.rdr_range_last_30_days')); ?></option>
            <option value="this_month"><?php echo e(__('components.rdr_range_this_month')); ?></option>
            <option value="last_month"><?php echo e(__('components.rdr_range_last_month')); ?></option>
            <option value="custom"><?php echo e(__('components.rdr_range_custom')); ?></option>
        </select>
    </div>

    <div x-show="open" x-cloak class="rdr-custom">
        <div>
            <label class="rdr-label"><?php echo e(__('components.rdr_from_label')); ?></label>
            <input
                x-ref="fpFrom"
                type="text"
                wire:model.live="<?php echo e($dateFromModel); ?>"
                placeholder="<?php echo e(__('components.rdr_date_placeholder')); ?>"
                class="rdr-input"
            >
        </div>
        <div>
            <label class="rdr-label"><?php echo e(__('components.rdr_to_label')); ?></label>
            <input
                x-ref="fpTo"
                type="text"
                wire:model.live="<?php echo e($dateToModel); ?>"
                placeholder="<?php echo e(__('components.rdr_date_placeholder')); ?>"
                class="rdr-input"
            >
        </div>
    </div>

    <?php echo e($slot); ?>

</div>
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/components/report-date-range.blade.php ENDPATH**/ ?>