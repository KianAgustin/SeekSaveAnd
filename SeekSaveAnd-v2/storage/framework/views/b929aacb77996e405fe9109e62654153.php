<a class="card <?php echo e($item->resolved ? 'done' : ''); ?>" href="<?php echo e(route('items.show', $item)); ?>">
    <div class="card-visual">
        <?php if($item->photoUrl()): ?>
            <img src="<?php echo e($item->photoUrl()); ?>" alt="<?php echo e($item->title); ?>" loading="lazy">
        <?php else: ?>
            <?php echo e($item->icon()); ?>

        <?php endif; ?>
        <span class="chip <?php echo e($item->type); ?>"><?php echo e($item->isLost() ? 'Lost' : 'Found'); ?></span>
        <?php if($item->resolved): ?> <span class="chip resolved">Resolved</span> <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="card-title"><?php echo e($item->title); ?></div>
        <div class="card-meta"><?php echo e($item->location); ?></div>
        <div class="card-meta"><?php echo e($item->created_at->diffForHumans()); ?></div>
    </div>
</a>
<?php /**PATH C:\Users\STUDENT\Documents\SEEKSAVEAND\SeekSaveAnd\SeekSaveAnd-v2\resources\views/partials/item-card.blade.php ENDPATH**/ ?>