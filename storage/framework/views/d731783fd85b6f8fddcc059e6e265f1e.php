<?php $__env->startSection('content'); ?>
<p style="margin:0 0 16px 0;font-size:16px;color:#374151;"><?php echo e(__('mail.password_reset_greeting', ['name' => $userName])); ?></p>

<p style="margin:0 0 16px 0;font-size:16px;color:#374151;">
    <?php echo __('mail.password_reset_intro', ['app' => '<strong>' . e($appName) . '</strong>']); ?>

</p>

<div style="text-align:center;margin:32px 0;">
    <a href="<?php echo e($resetUrl); ?>"
       style="display:inline-block;background-color:<?php echo e($primaryColor); ?>;color:#ffffff !important;padding:14px 28px;border-radius:6px;text-decoration:none;font-size:16px;font-weight:600;">
        <span style="color:#ffffff;"><?php echo e(__('mail.password_reset_button')); ?></span>
    </a>
</div>

<p style="margin:0 0 8px 0;font-size:14px;color:#6b7280;">
    <?php echo e(__('mail.password_reset_expires', ['minutes' => $expiresMinutes])); ?>

</p>
<p style="margin:12px 0 0 0;font-size:13px;color:#9ca3af;word-break:break-all;">
    <?php echo e(__('mail.password_reset_fallback')); ?><br>
    <?php echo e($resetUrl); ?>

</p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('emails.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/emails/password-reset.blade.php ENDPATH**/ ?>