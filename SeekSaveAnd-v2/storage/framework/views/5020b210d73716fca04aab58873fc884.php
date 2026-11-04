<?php $__env->startSection('title', 'Sign in'); ?>
<?php $__env->startSection('content'); ?>
    <h1 class="auth-title">SeekSaveAnd</h1>
    <p class="auth-sub">MCC Lost &amp; Found Platform</p>

    <?php $__errorArgs = ['login'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="flash" style="padding:0"><div class="err"><?php echo e($message); ?></div></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

    <a class="btn ms-btn" href="<?php echo e(route('microsoft.redirect')); ?>">
        <svg width="20" height="20" viewBox="0 0 21 21" aria-hidden="true"><rect x="1" y="1" width="9" height="9" fill="#F25022"/><rect x="11" y="1" width="9" height="9" fill="#7FBA00"/><rect x="1" y="11" width="9" height="9" fill="#00A4EF"/><rect x="11" y="11" width="9" height="9" fill="#FFB900"/></svg>
        Sign in with your school Microsoft account
    </a>
    <p class="demo-note">Use the same school email and password you use for Microsoft 365 / Teams.</p>

    <?php if($devUsers->isNotEmpty()): ?>
        <div class="panel" style="margin-top:22px">
            <strong>Local testing only</strong>
            <p class="muted" style="margin:4px 0 10px">Sign in as a seeded user without Microsoft.</p>
            <?php $__currentLoopData = $devUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a class="btn ghost small" style="margin-bottom:8px" href="<?php echo e(route('dev.login', $u)); ?>">
                    <?php echo e($u->name); ?> <?php echo e($u->isAdmin() ? '(admin)' : ''); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\OneDrive\Desktop\SeekSaveAnd-v2-fixed\SeekSaveAnd-v2\resources\views/auth/login.blade.php ENDPATH**/ ?>