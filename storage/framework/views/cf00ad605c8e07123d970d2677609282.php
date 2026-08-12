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
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        
        <div class="lg:col-span-1 space-y-4">
            <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => ['heading' => __('filament/companies.section_company_info')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['heading' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('filament/companies.section_company_info'))]); ?>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide"><?php echo e(__('filament/companies.name')); ?></dt>
                        <dd class="mt-1 font-semibold text-gray-900 dark:text-white text-lg">
                            <?php echo e($company->name); ?>

                        </dd>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($company->domain): ?>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide"><?php echo e(__('filament/companies.domain')); ?></dt>
                        <dd class="mt-1"><a href="https://<?php echo e($company->domain); ?>" target="_blank" rel="noopener" class="text-primary-600 hover:underline"><?php echo e($company->domain); ?></a></dd>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($company->website): ?>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide"><?php echo e(__('filament/companies.website')); ?></dt>
                        
                        <dd class="mt-1">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(\App\Support\UrlSafety::isSafeExternalUrl($company->website)): ?>
                                <a href="<?php echo e($company->website); ?>" target="_blank" rel="noopener noreferrer" class="text-primary-600 hover:underline break-all"><?php echo e($company->website); ?></a>
                            <?php else: ?>
                                <span class="break-all"><?php echo e($company->website); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </dd>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($company->phone): ?>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide"><?php echo e(__('filament/companies.phone')); ?></dt>
                        <dd class="mt-1"><a href="tel:<?php echo e($company->phone); ?>" class="text-primary-600 hover:underline"><?php echo e($company->phone); ?></a></dd>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($company->industry): ?>
                    <?php
                        // Translator-first industry label so the badge respects tenant locale.
                        // Falls back to a humanised key for tenant-custom industries that
                        // aren't in the bundled `industry_*` lang keys.
                        $industryKey   = 'filament/companies.industry_' . $company->industry;
                        $industryTrans = __($industryKey);
                        $industryLabel = is_string($industryTrans) && $industryTrans !== $industryKey
                            ? $industryTrans
                            : ucfirst(str_replace('_', ' ', (string) $company->industry));
                    ?>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide"><?php echo e(__('filament/companies.industry')); ?></dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                                <?php echo e($industryLabel); ?>

                            </span>
                        </dd>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($company->size): ?>
                    <?php
                        // Translator-first size label so the badge respects tenant locale.
                        $sizeKey   = 'filament/companies.size_' . $company->size;
                        $sizeTrans = __($sizeKey);
                        $sizeLabel = is_string($sizeTrans) && $sizeTrans !== $sizeKey
                            ? $sizeTrans
                            : ucfirst((string) $company->size);
                    ?>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide"><?php echo e(__('filament/companies.size')); ?></dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                <?php echo e($sizeLabel); ?>

                            </span>
                        </dd>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($company->assignedUser): ?>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide"><?php echo e(__('filament/companies.account_owner')); ?></dt>
                        <dd class="mt-1"><?php echo e($company->assignedUser->name); ?></dd>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($company->address || $company->city || $company->country): ?>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide"><?php echo e(__('filament/companies.address')); ?></dt>
                        <dd class="mt-1 text-gray-700 dark:text-gray-300">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($company->address): ?><div><?php echo e($company->address); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php echo e(trim(($company->city ?? '') . ($company->country ? ', ' . $company->country : ''))); ?>

                        </dd>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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

            <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => ['heading' => __('filament/companies.section_deal_summary')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['heading' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('filament/companies.section_deal_summary'))]); ?>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide"><?php echo e(__('filament/companies.open_pipeline')); ?></dt>
                        <dd class="font-semibold text-indigo-600 dark:text-indigo-400">
                            <?php echo e(\App\Support\Currency::format($company->open_deals_value, \App\Support\Currency::default())); ?>

                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide"><?php echo e(__('filament/companies.won_deals')); ?></dt>
                        <dd class="font-semibold text-green-600 dark:text-green-400">
                            <?php echo e(\App\Support\Currency::format($company->won_deals_value, \App\Support\Currency::default())); ?>

                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide"><?php echo e(__('filament/companies.contacts')); ?></dt>
                        <dd class="font-semibold"><?php echo e($leads->count()); ?></dd>
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
        </div>

        
        <div class="lg:col-span-2 space-y-4" x-data="{ tab: 'contacts' }">
            <div class="flex gap-2 border-b border-gray-200 dark:border-gray-700">
                <button type="button" @click="tab='contacts'" :class="tab==='contacts' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-4 py-2 text-sm font-medium border-b-2 transition">
                    <?php echo e(__('filament/companies.tab_contacts_with_count', ['count' => $leads->count()])); ?>

                </button>
                <button type="button" @click="tab='notes'" :class="tab==='notes' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-4 py-2 text-sm font-medium border-b-2 transition">
                    <?php echo e(__('filament/companies.tab_notes')); ?>

                </button>
            </div>

            <div x-show="tab==='contacts'">
                <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => ['heading' => __('filament/companies.section_contacts')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['heading' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('filament/companies.section_contacts'))]); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($leads->isEmpty()): ?>
                        <p class="text-sm text-gray-500"><?php echo e(__('filament/companies.no_contacts')); ?></p>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left text-xs uppercase text-gray-500 border-b border-gray-200 dark:border-gray-700">
                                        <th class="py-2 pr-3"><?php echo e(__('filament/companies.col_name')); ?></th>
                                        <th class="py-2 pr-3"><?php echo e(__('filament/companies.col_email')); ?></th>
                                        <th class="py-2 pr-3"><?php echo e(__('filament/companies.col_status')); ?></th>
                                        <th class="py-2 pr-3 text-right"><?php echo e(__('filament/companies.col_deal_value')); ?></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900">
                                            <td class="py-2 pr-3">
                                                <a href="<?php echo e(route('filament.admin.resources.leads.view', $lead)); ?>" class="text-primary-600 hover:underline">
                                                    <?php echo e($lead->full_name ?: __('filament/companies.lead_no_name')); ?>

                                                </a>
                                            </td>
                                            <td class="py-2 pr-3 text-gray-600 dark:text-gray-400"><?php echo e($lead->email); ?></td>
                                            <td class="py-2 pr-3">
                                                
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                    <?php echo e(match($lead->status) {
                                                        \App\Enums\LeadStatus::New        => 'bg-yellow-100 text-yellow-800',
                                                        \App\Enums\LeadStatus::Contacted  => 'bg-blue-100 text-blue-800',
                                                        \App\Enums\LeadStatus::Qualified  => 'bg-green-100 text-green-800',
                                                        \App\Enums\LeadStatus::Won        => 'bg-emerald-100 text-emerald-800',
                                                        \App\Enums\LeadStatus::Lost       => 'bg-red-100 text-red-800',
                                                        default                           => 'bg-gray-100 text-gray-800',
                                                    }); ?>">
                                                    <?php echo e($lead->status?->label() ?? '—'); ?>

                                                </span>
                                            </td>
                                            <td class="py-2 pr-3 text-right font-medium">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->deal_value): ?>
                                                    <?php echo e(\App\Support\Currency::format((float) $lead->deal_value, $lead->deal_currency ?? \App\Support\Currency::default())); ?>

                                                <?php else: ?>
                                                    <span class="text-gray-400">—</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
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
            </div>

            <div x-show="tab==='notes'" x-cloak>
                <?php if (isset($component)) { $__componentOriginalee08b1367eba38734199cf7829b1d1e9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalee08b1367eba38734199cf7829b1d1e9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.section.index','data' => ['heading' => __('filament/companies.section_notes')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['heading' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('filament/companies.section_notes'))]); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($company->notes): ?>
                        <div class="whitespace-pre-wrap text-sm text-gray-700 dark:text-gray-300"><?php echo e($company->notes); ?></div>
                    <?php else: ?>
                        <p class="text-sm text-gray-500"><?php echo e(__('filament/companies.no_notes')); ?></p>
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
            </div>
        </div>
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
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/resources/companies/view.blade.php ENDPATH**/ ?>