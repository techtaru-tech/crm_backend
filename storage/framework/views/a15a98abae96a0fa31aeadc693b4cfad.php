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
    
    <link rel="stylesheet" href="<?php echo e(asset('css/filament/leads-view.css')); ?>">

    <div class="lh-lead lh-lead-grid">

        <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => ['heading' => __('filament/leads.section_contact_info')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['heading' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('filament/leads.section_contact_info'))]); ?>
                <dl>
                    <div>
                        <dt><?php echo e(__('filament/leads.view_name_label')); ?></dt>
                        <dd>
                            <span class="lh-lead-name"><?php echo e($lead->full_name ?: __('filament/leads.view_no_name')); ?></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->is_starred): ?>
                                <svg class="lh-lead-star" width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M9.05 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118L2.098 10.1c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.673z"/></svg>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </dd>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->email): ?>
                    <div>
                        <dt><?php echo e(__('filament/leads.view_email_label')); ?></dt>
                        <dd><a href="mailto:<?php echo e($lead->email); ?>" class="lh-lead-link"><?php echo e($lead->email); ?></a></dd>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->phone): ?>
                    <div>
                        <dt><?php echo e(__('filament/leads.view_phone_label')); ?></dt>
                        <dd><a href="tel:<?php echo e($lead->phone); ?>" class="lh-lead-link"><?php echo e($lead->phone); ?></a></dd>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div>
                        <dt><?php echo e(__('filament/leads.view_source_label')); ?></dt>
                        <dd>
                            <span class="lh-lead-pill source"><?php echo e($lead->source_label); ?></span>
                        </dd>
                    </div>
                    <div>
                        <dt><?php echo e(__('filament/leads.view_status_label')); ?></dt>
                        <dd>
                            <?php
                                // H7: status is now a LeadStatus enum cast — match cases
                                // directly so the vocabulary lives in one place (the enum).
                                $statusClass = match($lead->status) {
                                    \App\Enums\LeadStatus::New        => 'status-new',
                                    \App\Enums\LeadStatus::Contacted  => 'status-contacted',
                                    \App\Enums\LeadStatus::Qualified  => 'status-qualified',
                                    \App\Enums\LeadStatus::Won        => 'status-converted',
                                    \App\Enums\LeadStatus::Lost       => 'status-lost',
                                    default                           => 'status-default',
                                };
                            ?>
                            <span class="lh-lead-pill <?php echo e($statusClass); ?>"><?php echo e($lead->status?->label() ?? __('filament/leads.view_dash')); ?></span>
                        </dd>
                    </div>
                    <div>
                        <dt><?php echo e(__('filament/leads.view_lead_score_label')); ?></dt>
                        <dd>
                            <?php
                                $scoreClass = $lead->lead_score > 50 ? 'good' : ($lead->lead_score > 20 ? 'mid' : 'low');
                            ?>
                            <span class="lh-lead-score-wrap">
                                <span class="lh-lead-score <?php echo e($scoreClass); ?>"><?php echo e($lead->lead_score); ?></span>
                                <span class="lh-lead-score-unit"><?php echo e(__('filament/leads.view_pts_unit')); ?></span>
                            </span>
                        </dd>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($lead->applied_scoring_rules)): ?>
                            <details class="lh-lead-score-why">
                                <summary><?php echo e(__('filament/leads.view_why_this_score')); ?></summary>
                                <ul>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $lead->applied_scoring_rules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php $pts = (int) ($rule['points'] ?? 0); ?>
                                        <li>
                                            <span class="rule-name"><?php echo e($rule['name'] ?? __('filament/leads.view_default_rule_name')); ?></span>
                                            <span class="rule-pts <?php echo e($pts >= 0 ? 'pos' : 'neg'); ?>"><?php echo e($pts >= 0 ? '+' : ''); ?><?php echo e($pts); ?> <?php echo e(__('filament/leads.view_pts_suffix')); ?></span>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </ul>
                            </details>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->assignedUser): ?>
                    <div>
                        <dt><?php echo e(__('filament/leads.view_assigned_to_label')); ?></dt>
                        <dd><?php echo e($lead->assignedUser->name); ?></dd>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->pipelineStage): ?>
                    <div>
                        <dt><?php echo e(__('filament/leads.view_pipeline_stage_label')); ?></dt>
                        <dd>
                            <?php
                                // PipelineStage.color is admin-typed via Filament ColorPicker
                                // (client-side hex enforcement, server-side bypass-able via Livewire
                                // payload tamper). CSS-context interpolation needs server hex validation.
                                $safeStageColor = \App\Support\ColorSafety::safeHex($lead->pipelineStage->color, '#4f46e5');
                            ?>
                            <span class="lh-lead-pill" style="background-color: <?php echo e($safeStageColor); ?>22; color: <?php echo e($safeStageColor); ?>;">
                                <?php echo e($lead->pipelineStage->pipeline->name ?? ''); ?> / <?php echo e($lead->pipelineStage->name); ?>

                            </span>
                        </dd>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->tags->isNotEmpty()): ?>
                    <div>
                        <dt><?php echo e(__('filament/leads.view_tags_label')); ?></dt>
                        <dd>
                            <div class="lh-lead-tags">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $lead->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        // Tag.color is admin-typed (ColorPicker, Livewire-tamper-bypassable).
                                        $safeTagColor = \App\Support\ColorSafety::safeHex($tag->color, '#6b7280');
                                    ?>
                                    <span class="lh-lead-pill" style="background-color: <?php echo e($safeTagColor); ?>22; color: <?php echo e($safeTagColor); ?>;"><?php echo e($tag->name); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </dd>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->company): ?>
                    <div>
                        <dt><?php echo e(__('filament/leads.view_company_label')); ?></dt>
                        <dd class="lh-lead-bold"><?php echo e($lead->company); ?></dd>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->job_title): ?>
                    <div>
                        <dt><?php echo e(__('filament/leads.view_job_title_label')); ?></dt>
                        <dd><?php echo e($lead->job_title); ?></dd>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->industry || $lead->company_size): ?>
                    <div>
                        <dt><?php echo e(__('filament/leads.view_industry_size_label')); ?></dt>
                        <dd>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->industry): ?><span><?php echo e($lead->industry); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->industry && $lead->company_size): ?><span class="lh-lead-divider-dot">·</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->company_size): ?><span><?php echo e($lead->company_size); ?> <?php echo e(__('filament/leads.view_employees_suffix')); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </dd>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->country): ?>
                    <div>
                        <dt><?php echo e(__('filament/leads.view_country_label')); ?></dt>
                        <dd><?php echo e($lead->country); ?></dd>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->linkedin_url): ?>
                    <div>
                        <dt><?php echo e(__('filament/leads.view_linkedin_label')); ?></dt>
                        
                        <dd>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(\App\Support\UrlSafety::isSafeExternalUrl($lead->linkedin_url)): ?>
                                <a href="<?php echo e($lead->linkedin_url); ?>" target="_blank" rel="noopener noreferrer" class="lh-lead-link lh-lead-link-break"><?php echo e(__('filament/leads.view_linkedin_view_profile')); ?></a>
                            <?php else: ?>
                                <span class="lh-lead-link-break"><?php echo e($lead->linkedin_url); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </dd>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->enriched_at): ?>
                    <div>
                        <dt>
                            <span class="lh-lead-heading-row">
                                <svg class="lh-lead-enriched-svg" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 20 20"><path d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"/></svg>
                                <?php echo e(__('filament/leads.view_ai_enriched_label')); ?>

                            </span>
                        </dt>
                        <dd class="lh-lead-enriched-time"><?php echo e($lead->enriched_at->diffForHumans()); ?></dd>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div>
                        <dt><?php echo e(__('filament/leads.view_created_label')); ?></dt>
                        <dd><?php echo e($lead->created_at->diffForHumans()); ?></dd>
                    </div>
                </dl>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $attributes = $__attributesOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $component = $__componentOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__componentOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($nextActions)): ?>
            <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                 <?php $__env->slot('heading', null, []); ?> 
                    <span class="lh-lead-heading-row-lg">
                        <svg class="lh-lead-ai-svg" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 20 20"><path d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z"/></svg>
                        <?php echo e(__('filament/leads.section_ai_coaching')); ?>

                    </span>
                 <?php $__env->endSlot(); ?>
                <div class="lh-lead-section-stack">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $nextActions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $prio = $action['priority'] ?? 'low';
                        $iconPaths = [
                            'phone'   => '<path d="M2 3.5A1.5 1.5 0 013.5 2h1.148a1.5 1.5 0 011.465 1.175l.716 3.223a1.5 1.5 0 01-1.052 1.767l-.933.267c-.41.117-.643.555-.48.95a11.542 11.542 0 006.254 6.254c.395.163.833-.07.95-.48l.267-.933a1.5 1.5 0 011.767-1.052l3.223.716A1.5 1.5 0 0118 15.352V16.5a1.5 1.5 0 01-1.5 1.5H15c-1.149 0-2.263-.15-3.326-.43A13.022 13.022 0 012.43 8.326 13.019 13.019 0 012 5V3.5z"/>',
                            'email'   => '<path d="M3 4a2 2 0 00-2 2v1.161l8.441 4.221a1.25 1.25 0 001.118 0L19 7.162V6a2 2 0 00-2-2H3z"/><path d="M19 8.839l-7.77 3.885a2.75 2.75 0 01-2.46 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z"/>',
                            'task'    => '<path fill-rule="evenodd" d="M15.988 3.012A2.25 2.25 0 0118 5.25v6.5A2.25 2.25 0 0115.75 14H13.5V7A2.5 2.5 0 0011 4.5H8.128a2.252 2.252 0 011.884-1.488A2.25 2.25 0 0112.25 1h1.5a2.25 2.25 0 012.238 2.012zM11.5 3.25a.75.75 0 01.75-.75h1.5a.75.75 0 01.75.75v.25h-3v-.25z" clip-rule="evenodd"/><path d="M2 6a1 1 0 011-1h9a1 1 0 011 1v11a1 1 0 01-1 1H3a1 1 0 01-1-1V6z"/>',
                            'note'    => '<path fill-rule="evenodd" d="M10 2c-2.236 0-4.43.18-6.57.524C1.993 2.755 1 4.014 1 5.426v5.148c0 1.413.993 2.67 2.43 2.902 1.168.188 2.352.327 3.55.414.28.02.521.18.642.413l1.713 3.293a.75.75 0 001.33 0l1.713-3.293a.783.783 0 01.642-.413 41.102 41.102 0 003.55-.414c1.437-.231 2.43-1.49 2.43-2.902V5.426c0-1.413-.993-2.67-2.43-2.902A41.289 41.289 0 0010 2z" clip-rule="evenodd"/>',
                            'warning' => '<path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>',
                            'star'    => '<path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd"/>',
                        ];
                        $iconHtml = $iconPaths[$action['icon'] ?? 'note'] ?? $iconPaths['note'];
                    ?>
                    <div class="lh-lead-ai-card prio-<?php echo e($prio); ?>">
                        <svg class="lh-lead-ai-icon" width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><?php echo $iconHtml; ?></svg>
                        <div class="lh-lead-ai-body">
                            <div class="lh-lead-ai-row">
                                <p class="lh-lead-ai-title"><?php echo e($action['title']); ?></p>
                                <?php
                                    // M-I18N: priority labels (High/Medium/Low) must follow tenant locale.
                                    // Translator-first with English fallback so unknown values still render.
                                    $prioKey   = 'lead_next_actions.priority.' . $prio;
                                    $prioLabel = __($prioKey);
                                    if (! is_string($prioLabel) || $prioLabel === $prioKey) {
                                        $prioLabel = ucfirst($prio);
                                    }
                                ?>
                                <span class="lh-lead-ai-badge prio-<?php echo e($prio); ?>"><?php echo e($prioLabel); ?></span>
                            </div>
                            <p class="lh-lead-ai-reason"><?php echo e($action['reason']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $attributes = $__attributesOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $component = $__componentOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__componentOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php
                $inboxItems = collect();
                foreach (($emails ?? collect()) as $e) {
                    $inboxItems->push([
                        'kind'      => 'email',
                        'direction' => $e->direction,
                        'channel'   => 'email',
                        'subject'   => $e->subject,
                        'body'      => $e->body_text ?: strip_tags((string) $e->body_html),
                        'from'      => $e->from_address,
                        'status'    => $e->opened_at ? 'opened' : ($e->clicked_at ? 'clicked' : ($e->bounced_at ? 'bounced' : null)),
                        'created_at'=> $e->created_at,
                        'id'        => 'e' . $e->id,
                    ]);
                }
                foreach (($messages ?? collect()) as $m) {
                    $inboxItems->push([
                        'kind'      => 'message',
                        'direction' => $m->direction,
                        'channel'   => $m->channel,
                        'subject'   => null,
                        'body'      => $m->body ?: ($m->media_url ? __('filament/leads.view_media_attachment') : __('filament/leads.view_dash')),
                        'from'      => $m->from_identifier,
                        'status'    => $m->status,
                        'created_at'=> $m->created_at,
                        'id'        => 'm' . $m->id,
                    ]);
                }
                $inboxItems = $inboxItems->sortByDesc('created_at')->take(30)->values();
            ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inboxItems->isNotEmpty()): ?>
            <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                 <?php $__env->slot('heading', null, []); ?> 
                    <span class="lh-lead-heading-row-wide">
                        <svg class="lh-lead-inbox-svg" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M1 6a3 3 0 013-3h12a3 3 0 013 3v8a3 3 0 01-3 3H4a3 3 0 01-3-3V6zm4 1.5a2 2 0 100-4 2 2 0 000 4zm0 1.5a3.5 3.5 0 013.5 3.5c0 .28-.22.5-.5.5H2a.5.5 0 01-.5-.5A3.5 3.5 0 015 9zm8.75-4a.75.75 0 000 1.5h3.5a.75.75 0 000-1.5h-3.5zm0 3a.75.75 0 000 1.5h3.5a.75.75 0 000-1.5h-3.5zm0 3a.75.75 0 000 1.5h3.5a.75.75 0 000-1.5h-3.5z" clip-rule="evenodd"/></svg>
                        <?php echo e(__('filament/leads.section_conversations')); ?>

                        <span class="lh-lead-count-muted">(<?php echo e($inboxItems->count()); ?>)</span>
                    </span>
                 <?php $__env->endSlot(); ?>
                <div class="lh-lead-inbox">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $inboxItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $isInbound = $item['direction'] === 'inbound';
                            $statusClass = match($item['status']) {
                                'opened', 'read', 'delivered' => 'ok',
                                'clicked' => 'click',
                                'sent'    => 'sent',
                                'bounced', 'failed' => 'err',
                                default   => 'idle',
                            };
                        ?>
                        <details class="<?php echo e($isInbound ? 'in' : 'out'); ?>">
                            <summary>
                                <div class="lh-lead-inbox-left">
                                    <svg class="lh-lead-inbox-ch" width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M3 4a2 2 0 00-2 2v1.161l8.441 4.221a1.25 1.25 0 001.118 0L19 7.162V6a2 2 0 00-2-2H3z"/><path d="M19 8.839l-7.77 3.885a2.75 2.75 0 01-2.46 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z"/></svg>
                                    <span class="lh-lead-inbox-dir <?php echo e($isInbound ? 'in' : 'out'); ?>"><?php echo e($isInbound ? __('filament/leads.view_inbox_in') : __('filament/leads.view_inbox_out')); ?></span>
                                    <?php
                                        // Translator-first channel label so the chip respects tenant locale.
                                        // Falls back to ucfirst() for unknown legacy channels.
                                        $chKey   = 'filament/leads.channel_' . $item['channel'];
                                        $chTrans = __($chKey);
                                        $chLabel = is_string($chTrans) && $chTrans !== $chKey
                                            ? $chTrans
                                            : ucfirst((string) $item['channel']);
                                    ?>
                                    <span class="lh-lead-inbox-kind"><?php echo e($chLabel); ?></span>
                                    <span class="lh-lead-inbox-preview">
                                        <?php echo e($item['subject'] ? $item['subject'] . ' — ' : ''); ?><?php echo e(\Illuminate\Support\Str::limit($item['body'], 80)); ?>

                                    </span>
                                </div>
                                <span class="lh-lead-inbox-time"><?php echo e($item['created_at']?->diffForHumans()); ?></span>
                            </summary>
                            <div class="lh-lead-inbox-body">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item['subject']): ?><p class="subj"><?php echo e($item['subject']); ?></p><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <p class="wrap"><?php echo e($item['body']); ?></p>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item['status']): ?>
                                    <?php
                                        // Translator-first inbox status label so the pill respects tenant locale.
                                        $isKey   = 'filament/leads.inbox_status_' . $item['status'];
                                        $isTrans = __($isKey);
                                        $isLabel = is_string($isTrans) && $isTrans !== $isKey
                                            ? $isTrans
                                            : ucfirst((string) $item['status']);
                                    ?>
                                    <div class="lh-lead-inbox-status <?php echo e($statusClass); ?>"><?php echo e(__('filament/leads.view_inbox_status_prefix')); ?> <?php echo e($isLabel); ?></div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </details>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div>
                    <a href="/admin/conversations?lead=<?php echo e($lead->id); ?>" class="lh-lead-inbox-link">
                        <?php echo e(__('filament/leads.view_open_conversation_view')); ?>

                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.25 5.5a.75.75 0 00-.75.75v8.5c0 .414.336.75.75.75h8.5a.75.75 0 00.75-.75v-4a.75.75 0 011.5 0v4A2.25 2.25 0 0112.75 17h-8.5A2.25 2.25 0 012 14.75v-8.5A2.25 2.25 0 014.25 4h5a.75.75 0 010 1.5h-5z" clip-rule="evenodd"/><path fill-rule="evenodd" d="M6.194 12.753a.75.75 0 001.06.053L16.5 4.44v2.81a.75.75 0 001.5 0v-4.5a.75.75 0 00-.75-.75h-4.5a.75.75 0 000 1.5h2.553l-9.056 8.194a.75.75 0 00-.053 1.06z" clip-rule="evenodd"/></svg>
                    </a>
                </div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $attributes = $__attributesOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $component = $__componentOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__componentOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php
                $customFieldDefs = \App\Models\CustomFieldDefinition::where('tenant_id', $lead->tenant_id)
                    ->where('entity_type', 'lead')
                    ->orderBy('sort_order')
                    ->get();
                $customValues = (array) ($lead->custom_fields ?? []);
                $hasAnyCustomValue = collect($customFieldDefs)->contains(fn($f) => filled(data_get($customValues, $f->key)));
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($customFieldDefs->isNotEmpty() && $hasAnyCustomValue): ?>
            <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => ['heading' => __('filament/leads.section_custom_fields')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['heading' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('filament/leads.section_custom_fields'))]); ?>
                <dl>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $customFieldDefs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $val = data_get($customValues, $field->key); ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($val)): ?>
                            <div>
                                <dt><?php echo e($field->name); ?></dt>
                                <dd>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($field->field_type === 'multi_select' && is_array($val)): ?>
                                        <div class="lh-lead-tags">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $val; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="lh-lead-pill source"><?php echo e($v); ?></span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    <?php elseif($field->field_type === 'checkbox'): ?>
                                        <?php echo e($val ? __('filament/leads.view_custom_yes') : __('filament/leads.view_custom_no')); ?>

                                    <?php elseif($field->field_type === 'url' && \App\Support\UrlSafety::isSafeExternalUrl($val)): ?>
                                        
                                        <a href="<?php echo e($val); ?>" target="_blank" rel="noopener noreferrer" class="lh-lead-link lh-lead-link-break"><?php echo e($val); ?></a>
                                    <?php elseif($field->field_type === 'url'): ?>
                                        <span class="lh-lead-link-break"><?php echo e($val); ?></span>
                                    <?php elseif($field->field_type === 'date' && $val): ?>
                                        
                                        <?php echo e(\Carbon\Carbon::parse($val)->translatedFormat('M j, Y')); ?>

                                    <?php else: ?>
                                        <?php echo e(is_array($val) ? implode(', ', $val) : $val); ?>

                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </dd>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </dl>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $attributes = $__attributesOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $component = $__componentOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__componentOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php
                $enrollments = $lead->sequenceEnrollments()->with('sequence')->latest()->get();
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($enrollments->isNotEmpty()): ?>
            <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => ['heading' => __('filament/leads.section_email_sequences')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['heading' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('filament/leads.section_email_sequences'))]); ?>
                <div class="lh-lead-section-stack-only">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $enrollments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enrollment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $badgeKey = in_array($enrollment->status, ['active','completed','replied','unenrolled']) ? $enrollment->status : 'unenrolled';
                        ?>
                        <div class="lh-lead-row">
                            <div class="lh-lead-row-main">
                                <p class="lh-lead-row-title"><?php echo e($enrollment->sequence?->name ?? __('filament/leads.view_dash')); ?></p>
                                <p class="lh-lead-row-sub">
                                    <?php echo e(__('filament/leads.view_step_label')); ?> <?php echo e($enrollment->current_step); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($enrollment->next_send_at && $enrollment->status === 'active'): ?>
                                        <?php echo e(__('filament/leads.view_next_label', ['time' => $enrollment->next_send_at->diffForHumans()])); ?>

                                    <?php elseif($enrollment->completed_at): ?>
                                        <?php echo e(__('filament/leads.view_completed_label', ['time' => $enrollment->completed_at->diffForHumans()])); ?>

                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </p>
                            </div>
                            <?php
                                // Translator-first enrollment status so the badge respects tenant locale.
                                // Reuses the enrollment_status_* keys already defined in this lang file.
                                $enKey   = 'filament/leads.enrollment_status_' . $enrollment->status;
                                $enTrans = __($enKey);
                                $enLabel = is_string($enTrans) && $enTrans !== $enKey
                                    ? $enTrans
                                    : ucfirst((string) $enrollment->status);
                            ?>
                            <span class="lh-lead-seq-badge <?php echo e($badgeKey); ?>"><?php echo e($enLabel); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $attributes = $__attributesOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $component = $__componentOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__componentOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => ['heading' => __('filament/leads.section_attachments')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['heading' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('filament/leads.section_attachments'))]); ?>
                <div class="lh-lead-section-stack-tight">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $lead->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="lh-lead-row">
                            <div class="lh-lead-row-left">
                                <svg class="lh-lead-paperclip" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.57a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                                <div class="lh-lead-min-w-0">
                                    <p class="lh-lead-row-title"><?php echo e($attachment->original_name); ?></p>
                                    <p class="lh-lead-row-sub">
                                        <?php echo e($attachment->getHumanSize()); ?>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($attachment->uploader): ?> · <?php echo e($attachment->uploader->name); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        · <?php echo e($attachment->created_at->diffForHumans()); ?>

                                    </p>
                                </div>
                            </div>
                            <div class="lh-lead-row-actions">
                                <button wire:click="downloadAttachment(<?php echo e($attachment->id); ?>)" type="button" class="lh-lead-icon-btn" title="<?php echo e(__('filament/leads.view_attachment_download_title')); ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                                </button>
                                <button wire:click="deleteAttachment(<?php echo e($attachment->id); ?>)" wire:confirm="<?php echo e(__('filament/leads.confirm_delete_attachment')); ?>" type="button" class="lh-lead-icon-btn danger" title="<?php echo e(__('filament/leads.view_attachment_delete_title')); ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6h14z"/></svg>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="lh-lead-empty"><?php echo e(__('filament/leads.view_no_attachments_yet')); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <form wire:submit="uploadAttachments" class="lh-lead-upload">
                    <div class="lh-lead-section-stack-only">
                        <input type="file" wire:model="newAttachments" multiple/>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newAttachments.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="lh-lead-upload-error"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div wire:loading wire:target="newAttachments" class="lh-lead-upload-loading"><?php echo e(__('filament/leads.view_uploading')); ?></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($newAttachments): ?>
                            <button type="submit" class="lh-lead-btn-primary lh-lead-btn-width-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a.75.75 0 01-.75-.75V4.66L7.3 6.76a.75.75 0 11-1.1-1.02l3.25-3.5a.75.75 0 011.1 0l3.25 3.5a.75.75 0 01-1.1 1.02l-1.95-2.1v12.59A.75.75 0 0110 18z" clip-rule="evenodd"/></svg>
                                <?php echo e(__('filament/leads.view_save_attachments')); ?>

                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </form>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $attributes = $__attributesOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $component = $__componentOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__componentOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>

            
            <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => ['heading' => __('filament/leads.section_line_items')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['heading' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('filament/leads.section_line_items'))]); ?>
                <?php
                    $currency = $lead->deal_currency ?: 'USD';
                    $symbol   = match (strtoupper($currency)) {
                        'EUR' => '€', 'GBP' => '£', 'INR' => '₹', default => '$',
                    };
                    $itemsTotal = ($dealItems ?? collect())->sum('total');
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($dealItems ?? collect())->isEmpty()): ?>
                    <p class="lh-lead-empty"><?php echo e(__('filament/leads.view_no_line_items')); ?></p>
                <?php else: ?>
                    <div class="lh-lead-overflow-x">
                        <table class="lh-lead-table">
                            <thead>
                                <tr>
                                    <th><?php echo e(__('filament/leads.view_table_item')); ?></th>
                                    <th class="right"><?php echo e(__('filament/leads.view_table_qty')); ?></th>
                                    <th class="right"><?php echo e(__('filament/leads.view_table_unit_price')); ?></th>
                                    <th class="right"><?php echo e(__('filament/leads.view_table_discount')); ?></th>
                                    <th class="right"><?php echo e(__('filament/leads.view_table_total')); ?></th>
                                    <th class="lh-lead-table-th-action"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $dealItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <div class="lh-lead-table-name"><?php echo e($item->name); ?></div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->product && $item->product->sku): ?>
                                                <div class="lh-lead-table-sku"><?php echo e(__('filament/leads.view_table_sku_prefix')); ?> <?php echo e($item->product->sku); ?></div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="right"><?php echo e($item->quantity); ?></td>
                                        <td class="right"><?php echo e($symbol); ?><?php echo e(number_format((float) $item->unit_price, 2)); ?></td>
                                        <td class="right muted">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((float) $item->discount_percent > 0): ?>
                                                -<?php echo e(number_format((float) $item->discount_percent, 2)); ?>%
                                            <?php else: ?>
                                                <?php echo e(__('filament/leads.view_dash')); ?>

                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="right total"><?php echo e($symbol); ?><?php echo e(number_format((float) $item->total, 2)); ?></td>
                                        <td class="right">
                                            <button wire:click="removeDealItem(<?php echo e($item->id); ?>)" wire:confirm="<?php echo e(__('filament/leads.confirm_remove_line_item')); ?>" type="button" class="lh-lead-icon-btn danger lh-lead-icon-btn-sm" title="<?php echo e(__('filament/leads.view_remove_item_title')); ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="label"><?php echo e(__('filament/leads.view_table_total_label')); ?></td>
                                    <td class="grand"><?php echo e($symbol); ?><?php echo e(number_format((float) $itemsTotal, 2)); ?></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $attributes = $__attributesOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $component = $__componentOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__componentOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>

            
            <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => ['heading' => __('filament/leads.section_quotes_invoices')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['heading' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('filament/leads.section_quotes_invoices'))]); ?>
                <?php
                    $quotesCol   = $quotes   ?? collect();
                    $invoicesCol = $invoices ?? collect();
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($quotesCol->isEmpty() && $invoicesCol->isEmpty()): ?>
                    <p class="lh-lead-empty"><?php echo e(__('filament/leads.view_no_quotes_invoices')); ?></p>
                <?php else: ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($quotesCol->isNotEmpty()): ?>
                        <h4 class="lh-lead-h4"><?php echo e(__('filament/leads.view_quotes_heading')); ?></h4>
                        <ul class="lh-lead-qi-list">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $quotesCol; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $qColor = match($q->status) {
                                        'draft'     => '#f3f4f6;color:#374151',
                                        'sent'      => '#dbeafe;color:#1e3a8a',
                                        'accepted'  => '#dcfce7;color:#166534',
                                        'declined'  => '#fee2e2;color:#991b1b',
                                        'expired'   => '#fef9c3;color:#854d0e',
                                        'converted' => '#e0e7ff;color:#3730a3',
                                        default     => '#f3f4f6;color:#374151',
                                    };
                                ?>
                                <?php
                                    // Translator-first quote-status label so the sidebar badge
                                    // respects tenant locale. Falls back to ucfirst() on unknown
                                    // statuses for forward-compatibility with custom enums.
                                    $qStatusKey   = 'filament/leads.view_quote_status_' . (string) $q->status;
                                    $qStatusTrans = __($qStatusKey);
                                    $qStatusLabel = $qStatusTrans !== $qStatusKey
                                        ? (string) $qStatusTrans
                                        : ucfirst((string) $q->status);
                                ?>
                                <li>
                                    <a href="<?php echo e(\App\Filament\Resources\QuoteResource::getUrl('view', ['record' => $q->id])); ?>" class="lh-lead-qi-num"><?php echo e($q->quote_number); ?></a>
                                    <span class="lh-lead-qi-title"><?php echo e($q->title); ?></span>
                                    <span class="lh-lead-qi-badge" style="background:<?php echo e(explode(';', $qColor)[0]); ?>;<?php echo e(explode(';', $qColor)[1]); ?>"><?php echo e($qStatusLabel); ?></span>
                                    <span class="lh-lead-qi-amt"><?php echo e(number_format((float) $q->total, 2)); ?> <?php echo e($q->currency); ?></span>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </ul>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($invoicesCol->isNotEmpty()): ?>
                        <h4 class="lh-lead-h4"><?php echo e(__('filament/leads.view_invoices_heading')); ?></h4>
                        <ul class="lh-lead-qi-list">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $invoicesCol; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $iColor = match($inv->status) {
                                        'draft'     => '#f3f4f6;color:#374151',
                                        'sent'      => '#dbeafe;color:#1e3a8a',
                                        'partial'   => '#fef9c3;color:#854d0e',
                                        'overdue'   => '#fef9c3;color:#854d0e',
                                        'paid'      => '#dcfce7;color:#166534',
                                        'cancelled' => '#fee2e2;color:#991b1b',
                                        'refunded'  => '#fee2e2;color:#991b1b',
                                        default     => '#f3f4f6;color:#374151',
                                    };
                                ?>
                                <?php
                                    // Translator-first invoice-status label so the sidebar badge
                                    // respects tenant locale. Falls back to ucfirst() on unknown
                                    // statuses for forward-compatibility with custom enums.
                                    $invStatusKey   = 'filament/leads.view_invoice_status_' . (string) $inv->status;
                                    $invStatusTrans = __($invStatusKey);
                                    $invStatusLabel = $invStatusTrans !== $invStatusKey
                                        ? (string) $invStatusTrans
                                        : ucfirst((string) $inv->status);
                                ?>
                                <li>
                                    <a href="<?php echo e(\App\Filament\Resources\InvoiceResource::getUrl('view', ['record' => $inv->id])); ?>" class="lh-lead-qi-num"><?php echo e($inv->invoice_number); ?></a>
                                    
                                    <span class="lh-lead-qi-title"><?php echo e(__('filament/leads.view_invoice_due_label', ['date' => optional($inv->due_date)->translatedFormat('M j') ?? __('filament/leads.view_dash')])); ?></span>
                                    <span class="lh-lead-qi-badge" style="background:<?php echo e(explode(';', $iColor)[0]); ?>;<?php echo e(explode(';', $iColor)[1]); ?>"><?php echo e($invStatusLabel); ?></span>
                                    <span class="lh-lead-qi-amt"><?php echo e(number_format((float) $inv->total, 2)); ?> <?php echo e($inv->currency); ?></span>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </ul>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $attributes = $__attributesOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $component = $__componentOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__componentOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>

            
            <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => ['heading' => __('filament/leads.section_internal_notes')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['heading' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('filament/leads.section_internal_notes'))]); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $notes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="lh-lead-note">
                        <div class="lh-lead-note-head">
                            <span class="lh-lead-note-by"><?php echo e($note->user?->name ?? __('filament/leads.view_note_system')); ?></span>
                            <span class="lh-lead-note-time"><?php echo e($note->created_at->diffForHumans()); ?></span>
                        </div>
                        <p class="lh-lead-note-body">
                            <?php echo preg_replace('/@(\w+)/', '<span class="lh-lead-mention">@$1</span>', e($note->body)); ?>

                        </p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($note->mentions) && count($note->mentions) > 0): ?>
                            <?php
                                $mentionedUsers = \App\Models\User::whereIn('id', $note->mentions)->pluck('name');
                            ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mentionedUsers->isNotEmpty()): ?>
                                <div class="lh-lead-mention-row">
                                    <span class="lh-lead-mention-label"><?php echo e(__('filament/leads.view_note_mentioned_label')); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $mentionedUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mentionedName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="lh-lead-mention">{{ $mentionedName }}</span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="lh-lead-empty"><?php echo e(__('filament/leads.view_no_internal_notes')); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $attributes = $__attributesOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $component = $__componentOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__componentOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($calls) && $calls->isNotEmpty()): ?>
                <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => ['heading' => __('filament/leads.section_call_history')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['heading' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('filament/leads.section_call_history'))]); ?>
                    <ul class="lh-lead-call-list">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $calls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $call): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="lh-lead-call">
                                <div class="lh-lead-call-head">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($call->direction === 'outbound'): ?>
                                        <svg class="lh-lead-call-arrow out" width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.22 14.78a.75.75 0 001.06 0l7.22-7.22v5.69a.75.75 0 001.5 0v-7.5a.75.75 0 00-.75-.75h-7.5a.75.75 0 000 1.5h5.69l-7.22 7.22a.75.75 0 000 1.06z" clip-rule="evenodd"/></svg>
                                    <?php else: ?>
                                        <svg class="lh-lead-call-arrow in" width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M14.78 5.22a.75.75 0 00-1.06 0L6.5 12.44V6.75a.75.75 0 00-1.5 0v7.5c0 .414.336.75.75.75h7.5a.75.75 0 000-1.5H7.56l7.22-7.22a.75.75 0 000-1.06z" clip-rule="evenodd"/></svg>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <div class="lh-lead-min-w-0">
                                        <div class="lh-lead-call-agent">
                                            <?php echo e($call->user?->name ?? __('filament/leads.view_call_agent_default')); ?>

                                            <span class="lh-lead-call-dot">·</span>
                                            <span class="lh-lead-call-dur"><?php echo e($call->formatted_duration); ?></span>
                                        </div>
                                        <div class="lh-lead-call-meta">
                                            <?php echo e($call->created_at?->diffForHumans()); ?>

                                            <?php
                                                $callBadgeClass = match($call->status) {
                                                    'completed' => 'completed',
                                                    'failed', 'canceled' => 'failed',
                                                    'no-answer', 'busy' => 'no-answer',
                                                    default => 'default',
                                                };
                                                // Translator-first call-status label so the badge respects
                                                // tenant locale. Twilio hyphenates 'no-answer' but our lang
                                                // key has to be a valid PHP identifier — normalise here.
                                                $callStatusKey   = 'filament/leads.view_call_status_'
                                                    . str_replace('-', '_', (string) $call->status);
                                                $callStatusTrans = __($callStatusKey);
                                                $callStatusLabel = $callStatusTrans !== $callStatusKey
                                                    ? (string) $callStatusTrans
                                                    : ucfirst((string) $call->status);
                                            ?>
                                            <span class="lh-lead-call-status <?php echo e($callBadgeClass); ?>"><?php echo e($callStatusLabel); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($call->recording_url)): ?>
                                    <div class="lh-lead-call-recording">
                                        <audio controls preload="none" class="lh-lead-call-audio">
                                            <source src="<?php echo e($call->recording_url); ?>.mp3" type="audio/mpeg"/>
                                        </audio>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($call->ai_summary)): ?>
                                    <details class="lh-lead-call-summary">
                                        <summary class="lh-lead-call-summary-toggle"><?php echo e(__('filament/leads.view_call_ai_summary')); ?></summary>
                                        <div class="lh-lead-call-summary-body"><?php echo e($call->ai_summary); ?></div>
                                    </details>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </ul>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $attributes = $__attributesOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $component = $__componentOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__componentOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($pageViews) && $pageViews->isNotEmpty()): ?>
                <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => ['heading' => __('filament/leads.section_web_activity')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['heading' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('filament/leads.section_web_activity'))]); ?>
                    <ul class="lh-lead-web-list">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $pageViews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="lh-lead-web-item">
                                <span class="lh-lead-web-dot">
                                    <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .2.08.39.22.53l3 3a.75.75 0 001.06-1.06L10.75 9.69V5z" clip-rule="evenodd"/></svg>
                                </span>
                                <div class="lh-lead-flex-1-min">
                                    <p class="lh-lead-web-title"><?php echo e($pv->title ?: ($pv->path ?: $pv->url)); ?></p>
                                    <p class="lh-lead-web-url">
                                        
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(\App\Support\UrlSafety::isSafeExternalUrl($pv->url)): ?>
                                            <a href="<?php echo e($pv->url); ?>" target="_blank" rel="noopener noreferrer" class="lh-lead-web-link-inherit"><?php echo e($pv->path ?: $pv->url); ?></a>
                                        <?php else: ?>
                                            <span class="lh-lead-web-link-inherit"><?php echo e($pv->path ?: $pv->url); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pv->utm_source): ?> · <span class="lh-lead-web-utm"><?php echo e(__('filament/leads.view_utm_prefix')); ?> <?php echo e($pv->utm_source); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </p>
                                </div>
                                <span class="lh-lead-web-time"><?php echo e($pv->viewed_at?->diffForHumans()); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </ul>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $attributes = $__attributesOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $component = $__componentOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__componentOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => ['heading' => __('filament/leads.section_activity_timeline')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['heading' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('filament/leads.section_activity_timeline'))]); ?>
                <div>
                    <ul class="lh-lead-timeline">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <li>
                                <div class="lh-lead-tl-item">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$loop->last): ?>
                                        <span class="lh-lead-tl-line" aria-hidden="true"></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <div class="lh-lead-tl-row">
                                        <?php
                                            $dotClass = match($activity->type) {
                                                'email_sent'     => 'email',
                                                'call_logged'    => 'call',
                                                'note_added'     => 'note',
                                                'stage_moved'    => 'stage',
                                                'status_changed' => 'status',
                                                'tag_applied', 'tag_removed' => 'tag',
                                                'assigned'       => 'assigned',
                                                'score_changed'  => 'score',
                                                default          => 'default',
                                            };
                                            $iconSvgMap = [
                                                'email_sent'     => '<path d="M3 4a2 2 0 00-2 2v1.161l8.441 4.221a1.25 1.25 0 001.118 0L19 7.162V6a2 2 0 00-2-2H3z"/><path d="M19 8.839l-7.77 3.885a2.75 2.75 0 01-2.46 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z"/>',
                                                'call_logged'    => '<path d="M2 3.5A1.5 1.5 0 013.5 2h1.148a1.5 1.5 0 011.465 1.175l.716 3.223a1.5 1.5 0 01-1.052 1.767l-.933.267c-.41.117-.643.555-.48.95a11.542 11.542 0 006.254 6.254c.395.163.833-.07.95-.48l.267-.933a1.5 1.5 0 011.767-1.052l3.223.716A1.5 1.5 0 0118 15.352V16.5a1.5 1.5 0 01-1.5 1.5H15c-1.149 0-2.263-.15-3.326-.43A13.022 13.022 0 012.43 8.326 13.019 13.019 0 012 5V3.5z"/>',
                                                'note_added'     => '<path fill-rule="evenodd" d="M10 2c-2.236 0-4.43.18-6.57.524C1.993 2.755 1 4.014 1 5.426v5.148c0 1.413.993 2.67 2.43 2.902 1.168.188 2.352.327 3.55.414.28.02.521.18.642.413l1.713 3.293a.75.75 0 001.33 0l1.713-3.293a.783.783 0 01.642-.413 41.102 41.102 0 003.55-.414c1.437-.231 2.43-1.49 2.43-2.902V5.426c0-1.413-.993-2.67-2.43-2.902A41.289 41.289 0 0010 2z" clip-rule="evenodd"/>',
                                                'stage_moved'    => '<path fill-rule="evenodd" d="M15.97 2.47a.75.75 0 011.06 0l4.5 4.5a.75.75 0 010 1.06l-4.5 4.5a.75.75 0 11-1.06-1.06l3.22-3.22H7.5a.75.75 0 010-1.5h11.69l-3.22-3.22a.75.75 0 010-1.06zm-7.94 9a.75.75 0 010 1.06l-3.22 3.22H16.5a.75.75 0 010 1.5H4.81l3.22 3.22a.75.75 0 11-1.06 1.06l-4.5-4.5a.75.75 0 010-1.06l4.5-4.5a.75.75 0 011.06 0z" clip-rule="evenodd"/>',
                                                'status_changed' => '<path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0V5.36l-.31-.31A7 7 0 003.239 8.188a.75.75 0 101.448.389A5.5 5.5 0 0113.89 6.11l.311.31h-2.432a.75.75 0 000 1.5h4.243a.75.75 0 00.53-.219z" clip-rule="evenodd"/>',
                                                'tag_applied', 'tag_removed' => '<path fill-rule="evenodd" d="M5.5 3A2.5 2.5 0 003 5.5v2.879a2.5 2.5 0 00.732 1.767l6.5 6.5a2.5 2.5 0 003.536 0l2.878-2.878a2.5 2.5 0 000-3.536l-6.5-6.5A2.5 2.5 0 008.38 3H5.5zM6 7a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>',
                                                'assigned'       => '<path d="M11 5a3 3 0 11-6 0 3 3 0 016 0zM2.046 15.253c-.058.209-.087.429-.087.657 0 .552.448 1 1 1h6.032a4.5 4.5 0 01.012-6 6.453 6.453 0 00-6.957 4.343zM15 6a.75.75 0 01.75.75V8.5h1.75a.75.75 0 010 1.5h-1.75v1.75a.75.75 0 01-1.5 0V10h-1.75a.75.75 0 010-1.5h1.75V6.75A.75.75 0 0115 6z"/>',
                                                'score_changed'  => '<path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401z" clip-rule="evenodd"/>',
                                            ];
                                            $iconSvg = $iconSvgMap[$activity->type] ?? '<path d="M11.983 1.907a.75.75 0 00-1.292-.657l-8.5 9.5A.75.75 0 002.75 12h6.572l-1.305 6.093a.75.75 0 001.292.657l8.5-9.5A.75.75 0 0017.25 8h-6.572l1.305-6.093z"/>';
                                        ?>
                                        <span class="lh-lead-tl-dot <?php echo e($dotClass); ?>">
                                            <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><?php echo $iconSvg; ?></svg>
                                        </span>
                                        <div class="lh-lead-tl-body">
                                            <div>
                                                <?php
                                                    // Translate from metadata['i18n_key'] when present (new rows);
                                                    // fall back to the canonical English $activity->description
                                                    // for legacy rows or unknown keys.
                                                    $i18nKey    = $activity->metadata['i18n_key']    ?? null;
                                                    $i18nParams = $activity->metadata['i18n_params'] ?? [];
                                                    $translated = $i18nKey ? __($i18nKey, $i18nParams) : null;
                                                ?>
                                                <p class="lh-lead-tl-text">
                                                    <?php echo e(($translated && $translated !== $i18nKey) ? $translated : $activity->description); ?>

                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activity->user): ?>
                                                        <span class="lh-lead-tl-by"> <?php echo e(__('filament/leads.view_activity_by', ['name' => $activity->user->name])); ?></span>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </p>
                                            </div>
                                            <div class="lh-lead-tl-time"><?php echo e($activity->created_at->diffForHumans()); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <li class="lh-lead-tl-empty"><?php echo e(__('filament/leads.view_no_activity_yet')); ?></li>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </ul>
                </div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $attributes = $__attributesOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__attributesOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalee08b1367eba38734199cf7829b1d1e9)): ?>
<?php $component = $__componentOriginalee08b1367eba38734199cf7829b1d1e9; ?>
<?php unset($__componentOriginalee08b1367eba38734199cf7829b1d1e9); ?>
<?php endif; ?>
    </div>
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
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/resources/leads/view.blade.php ENDPATH**/ ?>