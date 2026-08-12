<!DOCTYPE html>

<html lang="<?php echo e(app()->getLocale()); ?>">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo e($subject ?? $appName ?? __('mail.layout_default_title')); ?></title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;font-size:15px;color:#374151;">

<div style="display:none;overflow:hidden;line-height:1px;opacity:0;max-height:0;max-width:0;color:transparent;">
    <?php echo e($preheader ?? ($subject ?? __('mail.layout_preheader_fallback', ['app' => $appName ?? __('mail.layout_default_title')]))); ?>

</div>
<div style="width:100%;background:#f3f4f6;padding:40px 0;">
    <div style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.06);">
        
        <div style="padding:28px 32px 24px;background:<?php echo e($headerBackground ?? ($primaryColor ?? '#4f46e5')); ?>;text-align:center;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($logoUrl)): ?>
                <img src="<?php echo e($logoUrl); ?>" alt="<?php echo e($appName ?? __('mail.layout_default_title')); ?>" style="height:40px;max-width:220px;object-fit:contain;display:block;margin:0 auto;" />
            <?php else: ?>
                <table cellpadding="0" cellspacing="0" border="0" align="center" style="margin:0 auto;">
                    <tr>
                        <td valign="middle" style="padding-right:10px;">
                            <img src="<?php echo e(url('/favicon.svg')); ?>" alt="" width="36" height="36" style="display:block;width:36px;height:36px;" />
                        </td>
                        <td valign="middle">
                            <span style="display:block;color:#ffffff;font-size:24px;font-weight:700;letter-spacing:-0.015em;line-height:1;"><?php echo e($appName ?? __('mail.layout_default_title')); ?></span>
                        </td>
                    </tr>
                </table>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($headerTagline)): ?>
                <p style="margin:10px 0 0 0;color:rgba(255,255,255,.85);font-size:13px;font-weight:500;"><?php echo e($headerTagline); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        
        <div style="padding:36px 32px;">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
        
        <div style="padding:24px 32px;background:<?php echo e($footerBackground ?? '#f9fafb'); ?>;border-top:1px solid #e5e7eb;text-align:center;font-size:12px;color:<?php echo e($footerTextColor ?? '#9ca3af'); ?>;line-height:1.6;">
            <p style="margin:0 0 6px;color:<?php echo e($footerTextColor ?? '#6b7280'); ?>;font-weight:500;"><?php echo e($appName ?? __('mail.layout_default_title')); ?></p>
            <?php echo nl2br(e($footerText ?: __('mail.layout_footer_default', ['app' => $appName ?? __('mail.layout_default_title')]))); ?>

        </div>
    </div>
</div>
</body>
</html>
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/emails/layout.blade.php ENDPATH**/ ?>