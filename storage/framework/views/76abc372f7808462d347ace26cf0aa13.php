
<link rel="stylesheet" href="<?php echo e(asset('css/views/filament/resources/forms/live-preview.css')); ?>">

<div class="lh-form-live-preview w-full rounded-xl overflow-hidden border border-gray-200 shadow-sm bg-white">
    <div class="bg-gray-100 px-4 py-2 flex items-center gap-2 border-b border-gray-200">
        <span class="w-3 h-3 rounded-full bg-red-400 inline-block"></span>
        <span class="w-3 h-3 rounded-full bg-yellow-400 inline-block"></span>
        <span class="w-3 h-3 rounded-full bg-green-400 inline-block"></span>
        <span class="ml-2 text-xs text-gray-500 truncate"><?php echo e($record->public_url); ?></span>
        <a href="<?php echo e($record->public_url); ?>" target="_blank" rel="noopener noreferrer" class="ml-auto text-xs text-primary-600 underline">
            <?php echo e(__('filament/forms.live_preview_open_in_new_tab')); ?>

        </a>
    </div>
    <iframe
        src="<?php echo e($record->public_url); ?>"
        class="w-full lh-form-live-preview__iframe"
        loading="lazy"
        title="<?php echo e(__('filament/forms.live_preview_iframe_title')); ?>"
    ></iframe>
</div>
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/resources/forms/live-preview.blade.php ENDPATH**/ ?>