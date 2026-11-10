
<?php $__env->startSection('title', 'Admin – Users'); ?>
<?php $__env->startSection('content'); ?>
<main class="container">
    <h1>Admin dashboard</h1>
    <div class="subnav">
        <a href="<?php echo e(route('admin.index')); ?>">All posts</a>
        <a href="<?php echo e(route('admin.users')); ?>" class="on">Users</a>
    </div>

    <form method="GET" class="row" style="margin:16px 0">
        <input class="input" style="flex:1 1 220px;width:auto" type="search" name="q" value="<?php echo e($q); ?>" placeholder="Search name, email or student number">
        <button class="btn inline" type="submit">Search</button>
    </form>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Program</th><th>Student No.</th><th>Posts</th><th>Role</th><th></th></tr></thead>
            <tbody>
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><strong><?php echo e($u->name); ?></strong></td>
                    <td><?php echo e($u->email); ?></td>
                    <td><?php echo e($u->programLabel() ?: '—'); ?></td>
                    <td><?php echo e($u->student_number ?: '—'); ?></td>
                    <td><?php echo e($u->items_count); ?></td>
                    <td><span class="badge <?php echo e($u->isAdmin() ? 'pending' : 'approved'); ?>"><?php echo e($u->isAdmin() ? 'Admin' : 'Student'); ?></span></td>
                    <td>
                        <?php if($u->id !== auth()->id()): ?>
                            <form method="POST" action="<?php echo e(route('admin.users.role', $u)); ?>" onsubmit="return confirm('Change role for <?php echo e(e($u->name)); ?>?')">
                                <?php echo csrf_field(); ?> <button class="btn ghost small inline" type="submit"><?php echo e($u->isAdmin() ? 'Remove admin' : 'Make admin'); ?></button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    <?php if($users->hasPages()): ?>
        <div class="pager">
            <?php if($users->onFirstPage()): ?> <span>Previous</span> <?php else: ?> <a href="<?php echo e($users->previousPageUrl()); ?>">Previous</a> <?php endif; ?>
            <span class="cur"><?php echo e($users->currentPage()); ?> / <?php echo e($users->lastPage()); ?></span>
            <?php if($users->hasMorePages()): ?> <a href="<?php echo e($users->nextPageUrl()); ?>">Next</a> <?php else: ?> <span>Next</span> <?php endif; ?>
        </div>
    <?php endif; ?>
</main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\STUDENT\Documents\SEEKSAVEAND\SeekSaveAnd\SeekSaveAnd-v2\resources\views/admin/users.blade.php ENDPATH**/ ?>