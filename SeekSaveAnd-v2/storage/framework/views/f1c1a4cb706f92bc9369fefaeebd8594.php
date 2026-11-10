
<?php $__env->startSection('title', 'Edit post'); ?>
<?php $__env->startSection('content'); ?>
<main class="container narrow">
    <p><a href="<?php echo e(route('items.show', $item)); ?>">← Back to post</a></p>
    <h1>Edit post</h1>
    <?php echo $__env->make('items._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\STUDENT\Documents\SEEKSAVEAND\SeekSaveAnd\SeekSaveAnd-v2\resources\views/items/edit.blade.php ENDPATH**/ ?>