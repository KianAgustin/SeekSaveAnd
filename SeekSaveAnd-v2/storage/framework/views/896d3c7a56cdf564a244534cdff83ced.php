<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'SeekSaveAnd'); ?> – MCC Lost &amp; Found</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('css/seeksave.css')); ?>">
</head>
<body>
<?php if(auth()->guard()->check()): ?>
<?php $unread = \App\Models\Message::unreadCountFor(auth()->user()); ?>
<header class="topbar">
    <div class="topbar-inner">
        <a class="brand" href="<?php echo e(route('items.index')); ?>">
            <?php echo $__env->make('partials.logo', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> SeekSaveAnd
        </a>
        <nav class="nav">
            <a href="<?php echo e(route('items.index')); ?>" class="<?php echo e(request()->routeIs('items.index') ? 'active' : ''); ?>">Home</a>
            <a href="<?php echo e(route('messages.index')); ?>" class="<?php echo e(request()->routeIs('messages.*') ? 'active' : ''); ?>">Messages <?php if($unread > 0): ?><span class="count"><?php echo e($unread); ?></span><?php endif; ?></a>
            <a href="<?php echo e(route('dashboard')); ?>" class="<?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">Profile</a>
            <?php if(auth()->user()->isAdmin()): ?>
                <a href="<?php echo e(route('admin.index')); ?>" class="<?php echo e(request()->routeIs('admin.*') ? 'active' : ''); ?>">Admin</a>
            <?php endif; ?>
            <form method="POST" action="<?php echo e(route('logout')); ?>" style="display:inline">
                <?php echo csrf_field(); ?>
                <button type="submit">Log out</button>
            </form>
        </nav>
    </div>
</header>
<?php endif; ?>

<div class="flash">
    <?php if(session('status')): ?> <div class="ok"><?php echo e(session('status')); ?></div> <?php endif; ?>
    <?php if($errors->has('chat')): ?> <div class="err"><?php echo e($errors->first('chat')); ?></div> <?php endif; ?>
</div>

<?php echo $__env->yieldContent('content'); ?>

<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\Admin\OneDrive\Desktop\SeekSaveAnd-v2-fixed\SeekSaveAnd-v2\resources\views/layouts/app.blade.php ENDPATH**/ ?>