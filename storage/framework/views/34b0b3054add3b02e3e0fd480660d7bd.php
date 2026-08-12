
<link rel="stylesheet" href="<?php echo e(asset('css/views/filament/super-admin/login-disambiguation.css')); ?>">

<div class="sa-disambig-banner">
    <div class="sa-disambig-banner-title"><?php echo e(__('filament/sa_login.disambig_title')); ?></div>
    <?php echo __('filament/sa_login.disambig_body_html', [
        'tenant_login_link' => '<a href="/admin/login" class="sa-disambig-banner-link">'
            . e(__('filament/sa_login.disambig_tenant_login_label'))
            . '</a>',
    ]); ?>

</div>
<?php /**PATH C:\xampp\htdocs\CRMTechtaru\resources\views/filament/super-admin/login-disambiguation.blade.php ENDPATH**/ ?>