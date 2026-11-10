
<?php $__env->startSection('title', 'My details'); ?>
<?php $__env->startSection('content'); ?>
<main class="container narrow">
    <h1><?php echo e($user->profileComplete() ? 'Edit my details' : 'Complete your profile'); ?></h1>
    <p class="muted">Signed in as <strong><?php echo e($user->name); ?></strong> (<?php echo e($user->email); ?>). Other students see these details on your posts so items can be returned.</p>

    <form method="POST" action="<?php echo e(route('profile.update')); ?>" novalidate>
        <?php echo csrf_field(); ?>
        <div class="field">
            <label for="campus">Campus</label>
            <select class="input" id="campus" name="campus">
                <option value="">Campus</option>
                <?php $__currentLoopData = config('seeksave.campuses'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($o); ?>" <?php if(old('campus', $user->campus) === $o): echo 'selected'; endif; ?>><?php echo e($o); ?></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['campus'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error-text"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="field">
            <label for="program">Program</label>
            <select class="input" id="program" name="program">
                <option value="">Program</option>
                <?php $__currentLoopData = config('seeksave.programs'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($o); ?>" <?php if(old('program', $user->program) === $o): echo 'selected'; endif; ?>><?php echo e($o); ?></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['program'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error-text"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="field">
            <label for="year">Year</label>
            <select class="input" id="year" name="year">
                <option value="">Year</option>
                <?php $__currentLoopData = config('seeksave.years'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($o); ?>" <?php if(old('year', $user->year) === $o): echo 'selected'; endif; ?>><?php echo e($o); ?></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error-text"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="field">
            <label for="section">Section</label>
            <select class="input" id="section" name="section">
                <option value="">Section</option>
                <?php $__currentLoopData = config('seeksave.sections'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($o); ?>" <?php if(old('section', $user->section) === $o): echo 'selected'; endif; ?>><?php echo e($o); ?></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['section'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error-text"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="field">
            <label for="student_number">Student Number</label>
            <input class="input" id="student_number" type="text" name="student_number" value="<?php echo e(old('student_number', $user->student_number)); ?>" placeholder="e.g. 24-29-6767">
            <?php $__errorArgs = ['student_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error-text"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="field">
            <label for="contact">Contact Number</label>
            <input class="input" id="contact" type="tel" name="contact" value="<?php echo e(old('contact', $user->contact)); ?>" placeholder="09xx xxx xxxx">
            <?php $__errorArgs = ['contact'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error-text"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <button class="btn" type="submit">Save details</button>
    </form>
</main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\STUDENT\Documents\SEEKSAVEAND\SeekSaveAnd\SeekSaveAnd-v2\resources\views/profile/edit.blade.php ENDPATH**/ ?>