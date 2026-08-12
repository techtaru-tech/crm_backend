<?php $__env->startSection('content'); ?>
<h2 style="margin: 0 0 16px 0; font-size: 20px; font-weight: 600; color: #111827;">
    <?php echo e($headline); ?>

</h2>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<p style="margin: 0 0 12px 0; font-size: 15px; color: #374151; line-height: 1.6;">
    <?php echo $line; ?>

</p>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<div style="text-align: center; margin: 32px 0;">
    <a href="<?php echo e($actionUrl); ?>"
       style="display:inline-block;background-color:<?php echo e($primaryColor); ?>;color:#ffffff !important;padding:14px 28px;border-radius:6px;text-decoration:none;font-size:15px;font-weight:600;">
        <span style="color:#ffffff;"><?php echo e($actionLabel); ?></span>
    </a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('emails.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/emails/lead-notification.blade.php ENDPATH**/ ?>