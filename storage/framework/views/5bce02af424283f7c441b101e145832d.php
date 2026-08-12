<?php
    $oneSignalAppId  = config('leadhub.onesignal.app_id', '');
    // Pre-build the OneSignal config array OUTSIDE the @json() directive.
    // Blade's @json paren-balancer mishandles `?->` chained against `()`
    // method calls inside the array literal, truncating the array early
    // and producing an unclosed `[` in the compiled view. Building the
    // array in a @php block first and passing the resulting variable
    // to @json sidesteps that compiler quirk.
    $oneSignalConfig = $oneSignalAppId ? [
        'appId'    => $oneSignalAppId,
        'userId'   => auth()->id(),
        'tenantId' => auth()->user()?->tenant_id,
        'i18n'     => [
            'banner_enable_message' => __('filament/push_sw.banner_enable_message'),
            'banner_allow_btn'      => __('filament/push_sw.banner_allow_btn'),
        ],
    ] : null;
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($oneSignalAppId): ?>

<link rel="stylesheet" href="<?php echo e(asset('css/views/filament/push-sw.css')); ?>">
<script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>

<script type="application/json" id="onesignal-config">
<?php echo json_encode($oneSignalConfig, 15, 512) ?>
</script>
<script src="<?php echo e(asset('js/views/filament/push-sw.js')); ?>" defer></script>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/push-sw.blade.php ENDPATH**/ ?>