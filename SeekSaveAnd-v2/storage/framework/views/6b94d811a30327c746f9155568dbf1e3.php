
<?php $__env->startSection('title', 'Admin'); ?>
<?php $__env->startSection('content'); ?>
<main class="container">
    <h1>Admin dashboard</h1>
    <div class="subnav">
        <a href="<?php echo e(route('admin.index')); ?>" class="on">All posts</a>
        <a href="<?php echo e(route('admin.users')); ?>">Users</a>
    </div>

    <div class="stats">
        <?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="stat"><div class="stat-n"><?php echo e($n); ?></div><div class="muted"><?php echo e($label); ?></div></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <form method="GET" class="row" style="margin:16px 0">
        <input class="input" style="flex:1 1 220px;width:auto" type="search" name="q" value="<?php echo e($q); ?>" placeholder="Search title, location, name or email">
        <select class="input" style="width:auto" name="type">
            <option value="">Lost &amp; Found</option>
            <option value="lost" <?php if($type === 'lost'): echo 'selected'; endif; ?>>Lost</option>
            <option value="found" <?php if($type === 'found'): echo 'selected'; endif; ?>>Found</option>
        </select>
        <select class="input" style="width:auto" name="status">
            <option value="">Any status</option>
            <option value="open" <?php if($status === 'open'): echo 'selected'; endif; ?>>Open</option>
            <option value="resolved" <?php if($status === 'resolved'): echo 'selected'; endif; ?>>Resolved</option>
        </select>
        <button class="btn inline" type="submit">Filter</button>
    </form>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Item</th><th>Type</th><th>Location</th><th>Posted by</th><th>Status</th><th>Claims</th><th>Actions</th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <div class="row" style="flex-wrap:nowrap">
                            <div class="thumb"><?php if($item->photoUrl()): ?> <img src="<?php echo e($item->photoUrl()); ?>" alt=""> <?php else: ?> <?php echo e($item->icon()); ?> <?php endif; ?></div>
                            <a href="<?php echo e(route('items.show', $item)); ?>"><strong><?php echo e($item->title); ?></strong></a>
                        </div>
                    </td>
                    <td><span class="badge <?php echo e($item->isLost() ? 'rejected' : 'approved'); ?>"><?php echo e(ucfirst($item->type)); ?></span></td>
                    <td><?php echo e($item->location); ?></td>
                    <td><?php echo e($item->user->name); ?><div class="muted"><?php echo e($item->created_at->diffForHumans()); ?></div></td>
                    <td><?php echo e($item->resolved ? 'Resolved' : 'Open'); ?></td>
                    <td><?php echo e($item->pending_claims > 0 ? $item->pending_claims . ' pending' : '—'); ?></td>
                    <td>
                        <div class="row" style="flex-wrap:nowrap">
                            <?php if($item->resolved): ?>
                                <form method="POST" action="<?php echo e(route('items.reopen', $item)); ?>"><?php echo csrf_field(); ?> <button class="btn ghost small inline" type="submit">Reopen</button></form>
                            <?php else: ?>
                                <form method="POST" action="<?php echo e(route('items.resolve', $item)); ?>" onsubmit="return confirm('Mark as resolved?')"><?php echo csrf_field(); ?> <button class="btn green small inline" type="submit">Resolve</button></form>
                            <?php endif; ?>
                            <a class="btn ghost small inline" href="<?php echo e(route('items.edit', $item)); ?>">Edit</a>
                            <form method="POST" action="<?php echo e(route('items.destroy', $item)); ?>" onsubmit="return confirm('Delete this post permanently?')"><?php echo csrf_field(); ?> <button class="btn dark small inline" type="submit">Delete</button></form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="empty">No posts match these filters.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if($items->hasPages()): ?>
        <div class="pager">
            <?php if($items->onFirstPage()): ?> <span>Previous</span> <?php else: ?> <a href="<?php echo e($items->previousPageUrl()); ?>">Previous</a> <?php endif; ?>
            <span class="cur"><?php echo e($items->currentPage()); ?> / <?php echo e($items->lastPage()); ?></span>
            <?php if($items->hasMorePages()): ?> <a href="<?php echo e($items->nextPageUrl()); ?>">Next</a> <?php else: ?> <span>Next</span> <?php endif; ?>
        </div>
    <?php endif; ?>
</main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\STUDENT\Documents\SEEKSAVEAND\SeekSaveAnd\SeekSaveAnd-v2\resources\views/admin/index.blade.php ENDPATH**/ ?>