
<?php
    $appName  = config('leadhub.branding.app_name', config('app.name', 'LeadHub'));
    $primary  = config('leadhub.branding.primary_color', '#4f46e5');
    $homeUrl  = url('/');
?>
<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title><?php echo $__env->yieldContent('code'); ?> &middot; <?php echo e($appName); ?></title>
    <link rel="stylesheet" href="<?php echo e(asset('css/views/errors/layout.css')); ?>">
</head>

<body style="--err-primary: <?php echo e($primary); ?>;">
    <main class="card" role="alert">
        <p class="code"><?php echo e(__('errors.error_code_prefix', ['code' => ''])); ?><?php echo $__env->yieldContent('code'); ?></p>
        <h1><?php echo $__env->yieldContent('heading'); ?></h1>
        <p class="lead"><?php echo $__env->yieldContent('message'); ?></p>
        <div class="actions">
            <a class="btn btn-primary" href="<?php echo e($homeUrl); ?>"><?php echo e(__('errors.back_to_home')); ?></a>
            <?php if (! empty(trim($__env->yieldContent('extra-action')))): ?>
                <?php echo $__env->yieldContent('extra-action'); ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <p class="brand"><?php echo e($appName); ?></p>
    </main>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\CRMTechtaru\resources\views/errors/layout.blade.php ENDPATH**/ ?>