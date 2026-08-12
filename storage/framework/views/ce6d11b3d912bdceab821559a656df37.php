<?php if (isset($component)) { $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/filament/calendar.css')); ?>">

    
    <div class="cal-filters">
        <div class="cal-filter-group">
            <label><?php echo e(__('filament/calendar.filter_date_range')); ?></label>
            <select wire:model.live="rangePreset">
                <option value="week"><?php echo e(__('filament/calendar.range_this_week')); ?></option>
                <option value="month"><?php echo e(__('filament/calendar.range_this_month')); ?></option>
                <option value="next30"><?php echo e(__('filament/calendar.range_next_30')); ?></option>
            </select>
        </div>

        <div class="cal-filter-group">
            <label><?php echo e(__('filament/calendar.filter_assigned_to')); ?></label>
            <select wire:model.live="assignedFilter">
                <option value=""><?php echo e(__('filament/calendar.all_users')); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($id); ?>"><?php echo e($name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
        </div>

        <div class="cal-filter-group">
            <label><?php echo e(__('filament/calendar.filter_status')); ?></label>
            <select wire:model.live="statusFilter">
                <option value=""><?php echo e(__('filament/calendar.status_all')); ?></option>
                <option value="pending"><?php echo e(__('filament/calendar.status_pending')); ?></option>
                <option value="completed"><?php echo e(__('filament/calendar.status_completed')); ?></option>
                <option value="overdue"><?php echo e(__('filament/calendar.status_overdue')); ?></option>
            </select>
        </div>

        <div class="cal-legend">
            <div class="cal-legend-item">
                <span class="cal-legend-dot cal-legend-dot--upcoming"></span>
                <?php echo e(__('filament/calendar.legend_upcoming')); ?>

            </div>
            <div class="cal-legend-item">
                <span class="cal-legend-dot cal-legend-dot--overdue"></span>
                <?php echo e(__('filament/calendar.legend_overdue')); ?>

            </div>
            <div class="cal-legend-item">
                <span class="cal-legend-dot cal-legend-dot--completed"></span>
                <?php echo e(__('filament/calendar.legend_completed')); ?>

            </div>
        </div>
    </div>

    
    <div id="task-calendar"></div>

    
    <script src="<?php echo e(asset('vendor/fullcalendar/index.global.min.js')); ?>"></script>

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
            let events = <?php echo json_encode($events, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT, 512) ?>;

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                // FullCalendar reads this locale code to localize month/day
                // names internally; the explicit buttonText below covers the
                // toolbar labels that don't ship in the core bundle.
                locale: <?php echo \Illuminate\Support\Js::from(app()->getLocale())->toHtml() ?>,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                buttonText: {
                    today: <?php echo \Illuminate\Support\Js::from(__('filament/calendar.btn_today'))->toHtml() ?>,
                    month: <?php echo \Illuminate\Support\Js::from(__('filament/calendar.btn_month'))->toHtml() ?>,
                    week:  <?php echo \Illuminate\Support\Js::from(__('filament/calendar.btn_week'))->toHtml() ?>,
                    list:  <?php echo \Illuminate\Support\Js::from(__('filament/calendar.btn_list'))->toHtml() ?>,
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
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('updateDateRange', start, end);
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
                window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('getEvents').then(function (newEvents) {
                    calendar.removeAllEvents();
                    calendar.addEventSource(newEvents);
                });
            });
        });
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/pages/calendar.blade.php ENDPATH**/ ?>