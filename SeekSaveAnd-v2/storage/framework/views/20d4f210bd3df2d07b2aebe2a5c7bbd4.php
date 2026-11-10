
<?php $__env->startSection('title', 'My profile'); ?>
<?php $__env->startSection('content'); ?>
<main class="container narrow">
    <div class="panel" style="margin-top:0">
        <h1 style="margin-bottom:2px"><?php echo e($user->name); ?></h1>
        <p class="muted" style="margin-top:0"><?php echo e($user->email); ?></p>
        <dl class="kv">
            <?php if($user->campus): ?> <dt>Campus</dt><dd><?php echo e($user->campus); ?></dd> <?php endif; ?>
            <?php if($user->programLabel() !== ''): ?> <dt>Program</dt><dd><?php echo e($user->programLabel()); ?></dd> <?php endif; ?>
            <?php if($user->student_number): ?> <dt>Student No.</dt><dd><?php echo e($user->student_number); ?></dd> <?php endif; ?>
            <?php if($user->contact): ?> <dt>Contact</dt><dd><?php echo e($user->contact); ?></dd> <?php endif; ?>
        </dl>
        <div class="row" style="margin-top:14px">
            <a class="btn ghost inline small" href="<?php echo e(route('profile.edit')); ?>">Edit my details</a>
            <?php if($user->isAdmin()): ?> <a class="btn inline small" href="<?php echo e(route('admin.index')); ?>">Admin dashboard</a> <?php endif; ?>
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?> <button class="btn ghost inline small" type="submit">Log out</button>
            </form>
        </div>
    </div>

    <h2 style="margin-top:24px">My Items</h2>
    <?php if($myItems->isEmpty()): ?>
        <div class="empty">You have not posted anything yet.</div>
    <?php else: ?>
        <div class="list">
            <?php $__currentLoopData = $myItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="display:flex;gap:12px;padding:14px 16px;border-top:1px solid #F3E7E0;align-items:center;flex-wrap:wrap">
                    <div class="avatar"><?php echo e($item->icon()); ?></div>
                    <div style="flex:1;min-width:160px">
                        <a href="<?php echo e(route('items.show', $item)); ?>"><strong><?php echo e($item->title); ?></strong></a>
                        <div class="muted"><?php echo e($item->isLost() ? 'LOST' : 'FOUND'); ?> • <?php echo e($item->location); ?></div>
                    </div>
                    <?php if($item->resolved): ?>
                        <span class="badge approved">Resolved</span>
                    <?php else: ?>
                        <form method="POST" action="<?php echo e(route('items.resolve', $item)); ?>" onsubmit="return confirm('Mark this item as resolved?')">
                            <?php echo csrf_field(); ?> <button class="btn green small inline" type="submit">Mark Resolved</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <h2 style="margin-top:24px">Saved posts</h2>
    <?php if($savedItems->isEmpty()): ?>
        <div class="empty">Tap the heart on a post to save it here.</div>
    <?php else: ?>
        <div class="grid">
            <?php $__currentLoopData = $savedItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php echo $__env->make('partials.item-card', ['item' => $item], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\STUDENT\Documents\SEEKSAVEAND\SeekSaveAnd\SeekSaveAnd-v2\resources\views/dashboard/index.blade.php ENDPATH**/ ?>