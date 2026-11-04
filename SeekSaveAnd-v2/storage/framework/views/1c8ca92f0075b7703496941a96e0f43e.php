<?php $__env->startSection('title', 'Home'); ?>
<?php $__env->startSection('content'); ?>
<section class="hero">
    <div class="hero-inner">
        <form method="GET" action="<?php echo e(route('items.index')); ?>" class="search-row">
            <input type="hidden" name="type" value="<?php echo e($type); ?>">
            <input class="input grow" type="search" name="q" value="<?php echo e($q); ?>" placeholder="Looking for…" aria-label="Search items">
            <select class="input loc" name="location" aria-label="Filter by location">
                <option value="">All locations</option>
                <?php $__currentLoopData = config('seeksave.locations'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($loc); ?>" <?php if($location === $loc): echo 'selected'; endif; ?>><?php echo e($loc); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <button class="btn go" type="submit">Search</button>
        </form>
        <?php $base = array_filter(['q' => $q, 'location' => $location]); ?>
        <div class="tabs">
            <a href="<?php echo e(route('items.index', $base)); ?>" class="<?php echo e(! $type ? 'on' : ''); ?>">All</a>
            <a href="<?php echo e(route('items.index', $base + ['type' => 'found'])); ?>" class="<?php echo e($type === 'found' ? 'on' : ''); ?>">Found</a>
            <a href="<?php echo e(route('items.index', $base + ['type' => 'lost'])); ?>" class="<?php echo e($type === 'lost' ? 'on' : ''); ?>">Lost</a>
        </div>
    </div>
</section>

<main class="container">
    <?php if($items->isEmpty()): ?>
        <div class="empty">
            <p><strong>No posts match that search.</strong></p>
            <p>Try another keyword or location, or post the item yourself.</p>
        </div>
    <?php else: ?>
        <div class="grid">
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <?php echo $__env->make('partials.item-card', ['item' => $item], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if($items->hasPages()): ?>
            <div class="pager">
                <?php if($items->onFirstPage()): ?> <span>Previous</span> <?php else: ?> <a href="<?php echo e($items->previousPageUrl()); ?>">Previous</a> <?php endif; ?>
                <span class="cur"><?php echo e($items->currentPage()); ?> / <?php echo e($items->lastPage()); ?></span>
                <?php if($items->hasMorePages()): ?> <a href="<?php echo e($items->nextPageUrl()); ?>">Next</a> <?php else: ?> <span>Next</span> <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</main>

<a class="btn fab" href="<?php echo e(route('items.create', ['type' => $type === 'found' ? 'found' : 'lost'])); ?>">+ Report an item</a>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\OneDrive\Desktop\SeekSaveAnd-v2-fixed\SeekSaveAnd-v2\resources\views/items/index.blade.php ENDPATH**/ ?>