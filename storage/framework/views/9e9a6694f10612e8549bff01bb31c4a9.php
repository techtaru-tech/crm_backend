<?php
    $stateColorMap = [
        'active'        => ['bg' => '#dcfce7', 'fg' => '#14532d', 'border' => '#bbf7d0'],
        'trial'         => ['bg' => '#fef3c7', 'fg' => '#78350f', 'border' => '#fde68a'],
        'trial_expired' => ['bg' => '#fee2e2', 'fg' => '#7f1d1d', 'border' => '#fecaca'],
        'expired'       => ['bg' => '#f3f4f6', 'fg' => '#374151', 'border' => '#e5e7eb'],
        'cancelled'     => ['bg' => '#f3f4f6', 'fg' => '#374151', 'border' => '#e5e7eb'],
        'suspended'     => ['bg' => '#fee2e2', 'fg' => '#7f1d1d', 'border' => '#fecaca'],
        'unknown'       => ['bg' => '#f3f4f6', 'fg' => '#374151', 'border' => '#e5e7eb'],
    ];
    $colors  = $stateColorMap[$state_key] ?? $stateColorMap['unknown'];
    $seatPct = $seat_max > 0 ? min(100, (int) round(($seat_used / $seat_max) * 100)) : 0;
?>

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
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $tenant): ?>
        <div class="bp-error-card">
            <?php echo e(__('filament/billing_portal.error_no_workspace')); ?>

        </div>
    <?php else: ?>
        
        <div class="bp-status-banner" style="background:<?php echo e($colors['bg']); ?>;border:1px solid <?php echo e($colors['border']); ?>;">
            <div class="bp-status-row">
                <div>
                    <div class="bp-status-pill-wrap">
                        <span class="bp-status-pill" style="color:<?php echo e($colors['fg']); ?>;border:1px solid <?php echo e($colors['border']); ?>;">
                            <?php echo e($state_label); ?>

                        </span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan): ?>
                            <?php
                                // Translator-first interval label so the price line
                                // and "/ month" suffix respect tenant locale instead
                                // of leaking the raw slug stored in config/plans.php.
                                // Falls through to the raw value for any tenant-custom
                                // interval not shipped as a lang key.
                                $bpIntervalSlug  = $plan['interval'] ?? 'month';
                                $bpIntervalKey   = 'filament/billing_portal.interval_' . $bpIntervalSlug;
                                $bpIntervalTrans = __($bpIntervalKey);
                                $bpIntervalLabel = (is_string($bpIntervalTrans) && $bpIntervalTrans !== $bpIntervalKey)
                                    ? $bpIntervalTrans
                                    : $bpIntervalSlug;
                            ?>
                            <strong class="bp-plan-headline"><?php echo e($plan['name'] ?? __('filament/billing_portal.section_current_plan')); ?></strong>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($plan['price'] ?? 0) > 0): ?>
                                <span class="bp-plan-price-line">
                                    <?php echo e(number_format((float) $plan['price'], 2)); ?> <?php echo e($plan['currency'] ?? 'USD'); ?>

                                    / <?php echo e($bpIntervalLabel); ?>

                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($next_event && $next_event_at): ?>
                        <div class="bp-next-event-line">
                            <?php echo e($next_event); ?>: <strong class="bp-next-event-strong"><?php echo e($next_event_at->translatedFormat('M j, Y')); ?></strong>
                            <span class="bp-next-event-rel">(<?php echo e($next_event_at->diffForHumans()); ?>)</span>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="bp-actions-wrap">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($state_key, ['trial', 'trial_expired', 'expired', 'cancelled'])): ?>
                        <a href="/admin/subscription-required" class="bp-cta-primary">
                            <?php echo e(__('filament/billing_portal.cta_choose_plan')); ?>

                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="bp-grid lh-billing-grid">
            
            <div class="bp-card">
                <h3 class="bp-section-title"><?php echo e(__('filament/billing_portal.section_current_plan')); ?></h3>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan): ?>
                    <div class="bp-plan-row">
                        <div>
                            <div class="bp-plan-name-large"><?php echo e($plan['name'] ?? '—'); ?></div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($plan['description'])): ?>
                                <p class="bp-plan-desc"><?php echo e($plan['description']); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="bp-price-block">
                            <div class="bp-price-large">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($plan['price'] ?? 0) > 0): ?>
                                    <?php echo e(number_format((float) $plan['price'], 2)); ?>

                                <?php else: ?>
                                    <?php echo e(__('filament/billing_portal.price_free')); ?>

                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($plan['price'] ?? 0) > 0): ?>
                                <?php
                                    // Same translator-first interval lookup as the hero strip.
                                    // Reused inside the LEFT-card price block where $bpIntervalLabel
                                    // from the hero scope isn't accessible (different @if branch).
                                    $bpCardIntervalSlug  = $plan['interval'] ?? 'month';
                                    $bpCardIntervalKey   = 'filament/billing_portal.interval_' . $bpCardIntervalSlug;
                                    $bpCardIntervalTrans = __($bpCardIntervalKey);
                                    $bpCardIntervalLabel = (is_string($bpCardIntervalTrans) && $bpCardIntervalTrans !== $bpCardIntervalKey)
                                        ? $bpCardIntervalTrans
                                        : __('filament/billing_portal.interval_month_fallback');
                                ?>
                                <div class="bp-price-period"><?php echo e($plan['currency'] ?? 'USD'); ?> / <?php echo e($bpCardIntervalLabel); ?></div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="bp-seat-card">
                        <div class="bp-seat-header">
                            <span><?php echo e(__('filament/billing_portal.seat_team_seats')); ?></span>
                            <span><strong class="bp-seat-header-strong"><?php echo e($seat_used); ?></strong> <?php echo e(__('filament/billing_portal.of_connector')); ?> <?php echo e($seat_max ?: '∞'); ?></span>
                        </div>
                        <div class="bp-seat-bar-track">
                            <div class="bp-seat-bar-fill" style="width:<?php echo e($seatPct); ?>%;background:<?php echo e($seatPct >= 90 ? '#ef4444' : ($seatPct >= 70 ? '#f59e0b' : '#10b981')); ?>;"></div>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($seat_max > 0 && $seat_used >= $seat_max): ?>
                            <p class="bp-seat-warning"><?php echo e(__('filament/billing_portal.seat_limit_reached')); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($plan['features'])): ?>
                        <h4 class="bp-features-title"><?php echo e(__('filament/billing_portal.features_whats_included')); ?></h4>
                        <ul class="bp-features-grid">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $plan['features']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $featureKey => $enabled): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($enabled): ?>
                                    <?php
                                        // Translator-first feature label so the "What's included"
                                        // bullet respects tenant locale. Tenant-custom features
                                        // fall back to a humanised key for legibility.
                                        $featKey   = 'filament/billing_portal.feature_' . $featureKey;
                                        $featTrans = __($featKey);
                                        $featLabel = is_string($featTrans) && $featTrans !== $featKey
                                            ? $featTrans
                                            : \Illuminate\Support\Str::title(str_replace('_', ' ', (string) $featureKey));
                                    ?>
                                    <li class="bp-feature-item">
                                        <span class="bp-feature-check">✓</span>
                                        <?php echo e($featLabel); ?>

                                    </li>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </ul>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php else: ?>
                    <p class="bp-empty"><?php echo e(__('filament/billing_portal.no_plan_information')); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="bp-card">
                <h3 class="bp-section-title"><?php echo e(__('filament/billing_portal.section_manage_subscription')); ?></h3>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($gateway_label): ?>
                    <div class="bp-gateway-info">
                        <?php echo e(__('filament/billing_portal.gateway_paying_via_prefix')); ?> <strong class="bp-gateway-info-strong"><?php echo e($gateway_label); ?></strong>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="bp-actions-col">
                    <a href="/admin/subscription-required" class="bp-action-link">
                        <span><?php echo e(__('filament/billing_portal.action_change_plan')); ?></span>
                        <span class="bp-action-arrow">→</span>
                    </a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($has_stripe_portal)): ?>
                        <a href="<?php echo e(route('billing.portal')); ?>" class="bp-action-link">
                            <span><?php echo e(__('filament/billing_portal.action_update_payment_method')); ?></span>
                            <span class="bp-action-arrow">→</span>
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($state_key, ['active', 'trial'])): ?>
                        <a href="/admin/billing/cancel" class="bp-action-link-danger">
                            <span><?php echo e(__('filament/billing_portal.action_cancel_subscription')); ?></span>
                            <span>→</span>
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <p class="bp-support-hint">
                    <?php echo e(__('filament/billing_portal.support_hint')); ?>

                </p>
            </div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($recent_events)): ?>
            <div class="bp-events-card">
                <h3 class="bp-section-title"><?php echo e(__('filament/billing_portal.section_recent_activity')); ?></h3>
                <div class="bp-actions-col">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $recent_events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $label = match ($event['action']) {
                                'subscription.activated'          => '✅ ' . __('filament/billing_portal.event_subscription_activated'),
                                'subscription.cancelled'          => '🛑 ' . __('filament/billing_portal.event_subscription_cancelled'),
                                'subscription.payment_failed'     => '⚠️ ' . __('filament/billing_portal.event_payment_failed'),
                                'subscription.plan_changed'       => '🔄 ' . __('filament/billing_portal.event_plan_changed'),
                                'subscription.notification_sent'  => '✉️ ' . __('filament/billing_portal.event_notification_sent'),
                                'tenant.suspended'                => '⏸ ' . __('filament/billing_portal.event_workspace_suspended'),
                                'tenant.reactivated'              => '▶ ' . __('filament/billing_portal.event_workspace_reactivated'),
                                'tenant.auto_suspended'           => '⏸ ' . __('filament/billing_portal.event_auto_suspended'),
                                default                           => $event['action'],
                            };
                        ?>
                        <div class="bp-event-row">
                            <span class="bp-event-label"><?php echo e($label); ?></span>
                            <span class="bp-event-time"><?php echo e($event['created_at']?->diffForHumans()); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($plans) && count($plans) > 1): ?>
            <div class="bp-plans-section">
                <div class="bp-plans-header">
                    <h3 class="bp-plans-section-title"><?php echo e(__('filament/billing_portal.section_available_plans')); ?></h3>
                    
                    <div id="lh-billing-toggle" role="tablist" class="bp-billing-toggle">
                        <button type="button" data-period="month" role="tab" aria-selected="true" class="bp-toggle-btn-active">
                            <?php echo e(__('filament/billing_portal.toggle_monthly')); ?>

                        </button>
                        <button type="button" data-period="year" role="tab" aria-selected="false" class="bp-toggle-btn-inactive">
                            <?php echo e(__('filament/billing_portal.toggle_annual')); ?> <span class="bp-save-badge"><?php echo e(__('filament/billing_portal.toggle_annual_save_badge')); ?></span>
                        </button>
                    </div>
                </div>
                <div class="bp-plans-grid">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $availablePlan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $isCurrent = $plan && ($plan['key'] ?? null) === $key;
                            $highlight = ! empty($availablePlan['highlight']);
                            $preview = collect($upgrade_previews ?? [])
                                ->firstWhere('plan_key', $key);
                            $monthlyPrice = (float) ($availablePlan['price'] ?? 0);
                            $annualPrice  = $availablePlan['annual_price'] !== null
                                ? (float) $availablePlan['annual_price']
                                : ($monthlyPrice > 0 ? round($monthlyPrice * 12, 2) : 0.0);
                            $annualDiscountPct = (int) ($availablePlan['annual_discount_percent'] ?? 0);
                        ?>
                        
                        <div class="bp-plan-card" style="border:2px solid <?php echo e($isCurrent ? '#4f46e5' : ($highlight ? '#a78bfa' : '#e5e7eb')); ?>;background:<?php echo e($isCurrent ? '#eef2ff' : '#ffffff'); ?>;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($highlight && ! $isCurrent): ?>
                                <span class="bp-plan-card-tag-recommended"><?php echo e(__('filament/billing_portal.plan_tag_recommended')); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isCurrent): ?>
                                <span class="bp-plan-card-tag-current"><?php echo e(__('filament/billing_portal.plan_tag_current')); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <div class="bp-plan-card-name"><?php echo e($availablePlan['name'] ?? $key); ?></div>
                            <div data-billing-period="month" class="bp-plan-card-price">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($monthlyPrice > 0): ?>
                                    <?php echo e(number_format($monthlyPrice, 2)); ?>

                                    <span class="bp-plan-card-price-suffix"><?php echo e($availablePlan['currency'] ?? 'USD'); ?><?php echo e(__('filament/billing_portal.price_suffix_per_month')); ?></span>
                                <?php else: ?>
                                    <span class="bp-plan-card-price-free"><?php echo e(__('filament/billing_portal.price_free')); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            
                            <div data-billing-period="year" class="bp-plan-card-price bp-period-hidden">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($annualPrice > 0): ?>
                                    <?php echo e(number_format($annualPrice, 2)); ?>

                                    <span class="bp-plan-card-price-suffix"><?php echo e($availablePlan['currency'] ?? 'USD'); ?><?php echo e(__('filament/billing_portal.price_suffix_per_year')); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($annualDiscountPct > 0): ?>
                                        <div class="bp-plan-card-save"><?php echo e(__('filament/billing_portal.plan_save_vs_monthly', ['pct' => $annualDiscountPct])); ?></div>
                                    <?php elseif($monthlyPrice > 0 && $annualPrice < $monthlyPrice * 12): ?>
                                        <?php $saving = (int) round((1 - ($annualPrice / ($monthlyPrice * 12))) * 100); ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($saving > 0): ?>
                                            <div class="bp-plan-card-save"><?php echo e(__('filament/billing_portal.plan_save_vs_monthly', ['pct' => $saving])); ?></div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php else: ?>
                                    <span class="bp-plan-card-price-free"><?php echo e(__('filament/billing_portal.price_free')); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($availablePlan['description'])): ?>
                                <p class="bp-plan-card-description"><?php echo e($availablePlan['description']); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $isCurrent && $preview): ?>
                                <div class="bp-preview-box">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($preview['direction'] === 'upgrade'): ?>
                                        <strong class="bp-preview-upgrade"><?php echo e(__('filament/billing_portal.preview_upgrade_strong')); ?></strong><br>
                                        <?php echo e(__('filament/billing_portal.preview_charge_now_label')); ?> <strong><?php echo e(number_format((float) $preview['charge'], 2)); ?> <?php echo e($preview['currency']); ?></strong><br>
                                        <?php echo e(__('filament/billing_portal.preview_credit_applied_label')); ?> <?php echo e(number_format((float) $preview['credit'], 2)); ?> <?php echo e($preview['currency']); ?>

                                        <span class="bp-preview-meta">(<?php echo e($preview['prorated_days'] === 1 ? __('filament/billing_portal.preview_prorated_days_one', ['count' => $preview['prorated_days']]) : __('filament/billing_portal.preview_prorated_days_other', ['count' => $preview['prorated_days']])); ?>)</span>
                                    <?php elseif($preview['direction'] === 'downgrade'): ?>
                                        <strong class="bp-preview-downgrade"><?php echo e(__('filament/billing_portal.preview_downgrade_strong')); ?></strong><br>
                                        <?php echo e(__('filament/billing_portal.preview_account_credit_label')); ?> <strong><?php echo e(number_format(abs((float) $preview['net']), 2)); ?> <?php echo e($preview['currency']); ?></strong><br>
                                        <span class="bp-preview-meta"><?php echo e(__('filament/billing_portal.preview_applied_next_invoice')); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $isCurrent): ?>
                                <a href="/admin/subscription-required?plan=<?php echo e($key); ?>"
                                   data-billing-cta="month"
                                   class="bp-switch-btn">
                                    <?php echo e(__('filament/billing_portal.plan_action_switch')); ?>

                                </a>
                                
                                <a href="/admin/subscription-required?plan=<?php echo e($key); ?>&interval=year"
                                   data-billing-cta="year"
                                   class="bp-switch-btn bp-period-hidden">
                                    <?php echo e(__('filament/billing_portal.plan_action_switch_annual')); ?>

                                </a>
                            <?php else: ?>
                                <div class="bp-active-label"><?php echo e(__('filament/billing_portal.plan_active_label')); ?></div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="bp-billing-details">
            <h3 class="bp-billing-details-title"><?php echo e(__('filament/billing_portal.section_billing_details')); ?></h3>
            <p class="bp-billing-details-hint"><?php echo e(__('filament/billing_portal.billing_details_hint')); ?></p>
            <form wire:submit="saveBillingDetails" class="bp-billing-form">
                <label class="bp-form-label bp-form-label-full">
                    <?php echo e(__('filament/billing_portal.form_business_name_label')); ?>

                    <input type="text" maxlength="200" wire:model.lazy="billingDetails.business_name" class="bp-form-input">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($billingDetailsErrors['business_name'])): ?>
                        <span class="bp-form-error"><?php echo e($billingDetailsErrors['business_name']); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>
                <label class="bp-form-label">
                    <?php echo e(__('filament/billing_portal.form_vat_number_label')); ?>

                    <input type="text" maxlength="30" wire:model.lazy="billingDetails.vat_number" class="bp-form-input bp-form-input-mono">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($billingDetailsErrors['vat_number'])): ?>
                        <span class="bp-form-error"><?php echo e($billingDetailsErrors['vat_number']); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>
                <label class="bp-form-label">
                    <?php echo e(__('filament/billing_portal.form_country_label')); ?>

                    <input type="text" maxlength="2" wire:model.lazy="billingDetails.billing_country" placeholder="<?php echo e(__('filament/billing_portal.form_country_placeholder')); ?>" class="bp-form-input bp-form-input-mono bp-form-input-uppercase">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($billingDetailsErrors['billing_country'])): ?>
                        <span class="bp-form-error"><?php echo e($billingDetailsErrors['billing_country']); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>
                <label class="bp-form-label bp-form-label-full">
                    <?php echo e(__('filament/billing_portal.form_billing_address_label')); ?>

                    <textarea rows="3" maxlength="2000" wire:model.lazy="billingDetails.billing_address" class="bp-form-input bp-form-input-textarea"></textarea>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($billingDetailsErrors['billing_address'])): ?>
                        <span class="bp-form-error"><?php echo e($billingDetailsErrors['billing_address']); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>
                <label class="bp-form-label bp-form-label-full">
                    <?php echo e(__('filament/billing_portal.form_billing_email_label')); ?>

                    <input type="email" maxlength="200" wire:model.lazy="billingDetails.billing_email" placeholder="<?php echo e(__('filament/billing_portal.form_billing_email_placeholder')); ?>" class="bp-form-input">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($billingDetailsErrors['billing_email'])): ?>
                        <span class="bp-form-error"><?php echo e($billingDetailsErrors['billing_email']); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>
                <div class="bp-form-submit-row">
                    <button type="submit" class="bp-form-submit"><?php echo e(__('filament/billing_portal.form_save_button')); ?></button>
                </div>
            </form>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <script src="<?php echo e(asset('js/views/filament/pages/billing/billing-portal.js')); ?>" defer></script>

    
    <link rel="stylesheet" href="<?php echo e(asset('css/views/filament/pages/billing/billing-portal.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/filament/billing-portal.css')); ?>">
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
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/pages/billing/billing-portal.blade.php ENDPATH**/ ?>