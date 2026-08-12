
<link rel="stylesheet" href="<?php echo e(asset('css/views/marketing/partials/cookie-consent.css')); ?>">
<div id="lh-cookie-banner" class="lh-cookie-banner is-hidden" role="dialog" aria-live="polite" aria-label="<?php echo e(__('marketing.cookie_dialog_aria')); ?>">
    <p>
        <?php echo e(__('marketing.cookie_message')); ?>

        <a href="/pages/privacy" rel="noopener"><?php echo e(__('marketing.cookie_privacy_link')); ?></a>.
    </p>
    <div class="actions">
        <button type="button" class="reject" data-cookie-action="reject"><?php echo e(__('marketing.cookie_reject')); ?></button>
        <button type="button" class="accept" data-cookie-action="accept"><?php echo e(__('marketing.cookie_accept')); ?></button>
    </div>
</div>
<script src="<?php echo e(asset('js/views/marketing/cookie-consent.js')); ?>" defer></script>
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/marketing/partials/cookie-consent.blade.php ENDPATH**/ ?>