<?php
    // Resolve translated values (English is the fallback).
    $pTitle   = $page->t('title');
    $pExcerpt = $page->t('excerpt');
    $pMeta    = $page->t('meta_description');
    $pBody    = $page->t('content_html');

    // Hardening: $pBody is SuperAdmin-authored TinyMCE HTML
    // rendered on a public-internet page (no auth gate, every visitor
    // sees it).  Trust boundary is "SA-only", BUT a stored-XSS via a
    // compromised SA account would reach every site visitor.  Apply the
    // same multi-layer regex sanitizer that landing/sections/html.blade
    // uses so an SA-pasted <iframe src=javascript:>/<svg onload=>/etc.
    // is defanged even if HTMLPurifier isn't a dependency yet.
    if (is_string($pBody) && $pBody !== '') {
        for ($pass = 0; $pass < 2; $pass++) {
            $pBody = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $pBody);
            $pBody = preg_replace('#<script\b[^>]*/?>#is', '', $pBody);
        }
        $dangerousTags = '(?:iframe|frame|frameset|object|embed|applet|base|meta|form|input|button|svg|math|template|portal|xmp|plaintext|noembed|noscript)';
        for ($pass = 0; $pass < 2; $pass++) {
            $pBody = preg_replace('#<' . $dangerousTags . '\b[^>]*>.*?</' . $dangerousTags . '>#is', '', $pBody);
            $pBody = preg_replace('#<' . $dangerousTags . '\b[^>]*/?>#is', '', $pBody);
        }
        $pBody = preg_replace('#\son[a-z]+\s*=\s*"[^"]*"#i', '', $pBody);
        $pBody = preg_replace("#\son[a-z]+\s*=\s*'[^']*'#i", '', $pBody);
        $pBody = preg_replace('#\son[a-z]+\s*=\s*[^\s>]+#i', '', $pBody);
        $pBody = preg_replace(
            '#\s+(href|src|action|formaction|background|poster|cite|longdesc|srcset|data|manifest|ping|archive)\s*=\s*("|\')?\s*(?:javascript|vbscript|data|file)\s*:#i',
            ' data-blocked-uri-$1=$2',
            $pBody
        );
    }
?>

<?php $__env->startSection('title', $pTitle . ' — ' . $appName); ?>
<?php $__env->startSection('description', $pMeta ?: ($pExcerpt ?: $pTitle)); ?>

<?php $__env->startPush('head'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/views/public/static-page/show.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<article class="sp-shell">
    <header class="sp-header">
        <span class="sp-eyebrow"><?php echo e($appName); ?></span>
        <h1 class="sp-title"><?php echo e($pTitle); ?></h1>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pExcerpt !== ''): ?>
            <p class="sp-excerpt"><?php echo e($pExcerpt); ?></p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </header>

    <div class="sp-body">
        <?php echo $pBody; ?>

    </div>

    <p class="sp-updated"><?php echo e(__('marketing.public_static_last_updated')); ?> <?php echo e($page->updated_at?->translatedFormat('M j, Y')); ?></p>
</article>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('marketing.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/public/static-page/show.blade.php ENDPATH**/ ?>