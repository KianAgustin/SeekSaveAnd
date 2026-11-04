
<form method="POST" enctype="multipart/form-data" novalidate
      action="<?php echo e($item ? route('items.update', $item) : route('items.store')); ?>">
    <?php echo csrf_field(); ?>
    <div class="field">
        <label>Listing Type</label>
        <div class="row">
            <label class="btn ghost inline small" style="cursor:pointer"><input type="radio" name="type" value="lost" <?php if(old('type', $type) === 'lost'): echo 'checked'; endif; ?>> I Lost It</label>
            <label class="btn ghost inline small" style="cursor:pointer"><input type="radio" name="type" value="found" <?php if(old('type', $type) === 'found'): echo 'checked'; endif; ?>> I Found It</label>
        </div>
        <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error-text"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="field">
        <label for="title">Title</label>
        <input class="input" id="title" type="text" name="title" value="<?php echo e(old('title', $item?->title)); ?>" placeholder="Title e.g. iPhone 14 Pro" maxlength="100">
        <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error-text"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="field">
        <label for="category">Category</label>
        <select class="input" id="category" name="category">
            <option value="">Category</option>
            <?php $__currentLoopData = config('seeksave.categories'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($c); ?>" <?php if(old('category', $item?->category) === $c): echo 'selected'; endif; ?>><?php echo e($c); ?></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error-text"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="field">
        <label for="location">Location</label>
        <select class="input" id="location" name="location">
            <option value="">Choose a campus spot</option>
            <?php $__currentLoopData = config('seeksave.locations'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($l); ?>" <?php if(old('location', $item?->location) === $l): echo 'selected'; endif; ?>><?php echo e($l); ?></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error-text"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="field">
        <label for="description">Description</label>
        <textarea class="input" id="description" name="description" placeholder="Color, marks, extra details..." maxlength="1000"><?php echo e(old('description', $item && $item->description !== 'No extra details provided.' ? $item->description : '')); ?></textarea>
        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error-text"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="field">
        <label for="photo">Photo (optional, JPG/PNG/WEBP up to 8 MB)</label>
        <?php if($item && $item->photoUrl()): ?>
            <img class="form-photo" src="<?php echo e($item->photoUrl()); ?>" alt="Current photo">
            <label class="check"><input type="checkbox" name="remove_photo" value="1"> <span>Remove current photo</span></label>
        <?php endif; ?>
        <input class="input" id="photo" type="file" name="photo" accept="image/png,image/jpeg,image/webp" capture="environment">
        <img class="form-photo" id="preview" alt="" hidden>
        <?php $__errorArgs = ['photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error-text"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <button class="btn" type="submit"><?php echo e($item ? 'Save changes' : 'Post item'); ?></button>
</form>

<?php $__env->startPush('scripts'); ?>
<script>
document.getElementById('photo').addEventListener('change', function () {
    var p = document.getElementById('preview'), f = this.files[0];
    if (!f) { p.hidden = true; return; }
    p.src = URL.createObjectURL(f); p.hidden = false;
});
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\Users\Admin\OneDrive\Desktop\SeekSaveAnd-v2-fixed\SeekSaveAnd-v2\resources\views/items/_form.blade.php ENDPATH**/ ?>