
<?php $__env->startSection('title', 'Messages'); ?>
<?php $__env->startSection('content'); ?>
<main class="container narrow">
    <h1>Messages</h1>
    <?php if($conversations->isEmpty()): ?>
        <div class="empty">
            <p><strong>No conversations yet.</strong></p>
            <p>Open a posted item and tap “Message poster”.</p>
        </div>
    <?php else: ?>
        <div class="list">
            <?php $__currentLoopData = $conversations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $other = $c->otherParty(auth()->user()); ?>
                <a href="<?php echo e(route('messages.show', $c)); ?>">
                    <div class="avatar"><?php echo e($c->item->icon()); ?></div>
                    <div style="min-width:0">
                        <div><strong><?php echo e($c->item->title); ?></strong> <span class="muted">with <?php echo e($other->name); ?></span> <?php if($c->unread_count > 0): ?><span class="count"><?php echo e($c->unread_count); ?> new</span><?php endif; ?></div>
                        <div class="muted" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                            <?php echo e($c->latestMessage?->body ?? 'No messages yet'); ?>

                        </div>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\STUDENT\Documents\SEEKSAVEAND\SeekSaveAnd\SeekSaveAnd-v2\resources\views/messages/index.blade.php ENDPATH**/ ?>