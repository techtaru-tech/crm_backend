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
    <link rel="stylesheet" href="<?php echo e(asset('css/filament/kanban-board.css')); ?>">

    
    <div class="kb-pills">
        <label class="kb-pill-label"><?php echo e(__('filament/kanban_board.pipeline_label')); ?></label>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $pipelines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pipeline): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <button
                wire:click="changePipeline(<?php echo e($pipeline->id); ?>)"
                class="kb-pill <?php echo e($selectedPipelineId == $pipeline->id ? 'kb-pill-active' : 'kb-pill-inactive'); ?>">
                <?php echo e($pipeline->name); ?>

            </button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($stages)): ?>
        <div class="kb-empty">
            <h3 class="kb-empty-title"><?php echo e(__('filament/kanban_board.no_pipeline_configured')); ?></h3>
            <p class="kb-empty-body">
                <a href="<?php echo e(route('filament.admin.resources.pipelines.create')); ?>" class="kb-empty-link"><?php echo e(__('filament/kanban_board.create_a_pipeline')); ?></a> <?php echo e(__('filament/kanban_board.to_get_started')); ?>

            </p>
        </div>
    <?php else: ?>
        
        <div
            class="kb-board"
            x-data="{
                tenantId: <?php echo \Illuminate\Support\Js::from((string) auth()->user()?->tenant_id)->toHtml() ?>,
                toast: null,
                toastTimer: null,

                showToast(message) {
                    this.toast = message;
                    clearTimeout(this.toastTimer);
                    this.toastTimer = setTimeout(() => { this.toast = null; }, 4000);
                },

                initSortable() {
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').get('stages').forEach(stage => {
                        const el = document.getElementById('stage-' + stage.id);
                        if (!el || el._sortable) return;
                        el._sortable = Sortable.create(el, {
                            group: 'leads',
                            animation: 150,
                            ghostClass: 'opacity-40',
                            onEnd: (evt) => {
                                const leadId = parseInt(evt.item.dataset.leadId);
                                const newStageId = parseInt(evt.to.dataset.stageId);
                                if (leadId && newStageId) {
                                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('moveCard', leadId, newStageId);
                                }
                            }
                        });
                    });

                    // Unassigned column — pull-only source.  Cards can be
                    // dragged OUT into any stage (which assigns them), but
                    // put:false means you cannot drop a card back IN (we
                    // don't support un-assigning via drag).  Drops into a
                    // stage fire moveCard with that stage's id; the empty
                    // data-stage-id on this column means a stray drop here
                    // parses to NaN and is a no-op.
                    const unassignedEl = document.getElementById('stage-unassigned');
                    if (unassignedEl && !unassignedEl._sortable) {
                        unassignedEl._sortable = Sortable.create(unassignedEl, {
                            group: { name: 'leads', pull: true, put: false },
                            animation: 150,
                            ghostClass: 'opacity-40',
                            onEnd: (evt) => {
                                const leadId = parseInt(evt.item.dataset.leadId);
                                const newStageId = parseInt(evt.to.dataset.stageId);
                                if (leadId && newStageId) {
                                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('moveCard', leadId, newStageId);
                                }
                            }
                        });
                    }
                },

                initEcho() {
                    if (typeof window.Echo === 'undefined') return;
                    window.Echo.private('tenant.' + this.tenantId + '.leads')
                        .listen('.lead.stage_changed', (e) => {
                            window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('loadBoard');
                            const tpl = <?php echo \Illuminate\Support\Js::from(__('filament/kanban_board.toast_lead_moved'))->toHtml() ?>;
                            const defaultName = <?php echo \Illuminate\Support\Js::from(__('filament/kanban_board.default_lead_name'))->toHtml() ?>;
                            const defaultStage = <?php echo \Illuminate\Support\Js::from(__('filament/kanban_board.default_stage_name'))->toHtml() ?>;
                            this.showToast(
                                tpl
                                    .replace(':lead_name', e.lead_name || defaultName)
                                    .replace(':to_stage', e.to_stage || defaultStage)
                            );
                        })
                        .listen('.lead.received', () => {
                            window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('loadBoard');
                        });
                }
            }"
            x-init="$nextTick(() => { initSortable(); initEcho(); })"
        >
            
            <div
                x-show="toast"
                x-transition
                class="kb-toast kb-toast-hidden"
                x-text="toast"
            ></div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unassignedTotal > 0): ?>
                <div class="kb-col kb-col-unassigned">
                    <div class="kb-header">
                        <div class="kb-header-left">
                            
                            <div class="kb-dot" style="background-color: #9ca3af"></div>
                            <h3 class="kb-stage-name"><?php echo e(__('filament/kanban_board.unassigned_label')); ?></h3>
                        </div>
                        <span class="kb-count"><?php echo e(count($unassignedLeads)); ?></span>
                    </div>
                    <div class="kb-totals">
                        <?php echo e($unassignedTotal); ?> <?php echo e(__('filament/kanban_board.leads_suffix')); ?> · <span class="kb-total-value"><?php echo e(__('filament/kanban_board.unassigned_hint')); ?></span>
                    </div>

                    <div
                        id="stage-unassigned"
                        data-stage-id=""
                        class="kb-stage"
                    >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $unassignedLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo $__env->make('filament.pages.partials.kanban-card', ['lead' => $lead], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $stages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="kb-col">
                    
                    <div class="kb-header">
                        <div class="kb-header-left">
                            
                            <div class="kb-dot" style="background-color: <?php echo e($stage['color']); ?>"></div>
                            <h3 class="kb-stage-name"><?php echo e($stage['name']); ?></h3>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stage['is_won']): ?>
                                <span class="kb-badge kb-badge-won"><?php echo e(__('filament/kanban_board.badge_won')); ?></span>
                            <?php elseif($stage['is_lost']): ?>
                                <span class="kb-badge kb-badge-lost"><?php echo e(__('filament/kanban_board.badge_lost')); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <span class="kb-count"><?php echo e(count($leadsByStage[$stage['id']] ?? [])); ?></span>
                    </div>
                    <?php $totals = $stageTotals[$stage['id']] ?? ['count' => 0, 'total_value' => 0]; ?>
                    <div class="kb-totals">
                        <?php echo e($totals['count']); ?> <?php echo e(__('filament/kanban_board.leads_suffix')); ?> · <span class="kb-total-value"><?php echo e(\App\Support\Currency::format($totals['total_value'], \App\Support\Currency::default())); ?></span>
                    </div>

                    
                    <div
                        id="stage-<?php echo e($stage['id']); ?>"
                        data-stage-id="<?php echo e($stage['id']); ?>"
                        class="kb-stage"
                    >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $leadsByStage[$stage['id']] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo $__env->make('filament.pages.partials.kanban-card', ['lead' => $lead], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <?php $__env->startPush('scripts'); ?>
            <script src="<?php echo e(asset('vendor/sortablejs/Sortable.min.js')); ?>"></script>
        <?php $__env->stopPush(); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/pages/kanban-board.blade.php ENDPATH**/ ?>