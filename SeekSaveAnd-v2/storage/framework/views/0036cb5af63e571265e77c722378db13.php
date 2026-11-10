
<?php $__env->startSection('title', $item->title); ?>
<?php $__env->startSection('content'); ?>
<main class="container narrow">
    <p><a href="<?php echo e(route('items.index')); ?>">← Back to all posts</a></p>

    <div class="detail-visual">
        <?php if($item->photoUrl()): ?>
            <img src="<?php echo e($item->photoUrl()); ?>" alt="<?php echo e($item->title); ?>">
        <?php else: ?>
            <?php echo e($item->icon()); ?>

        <?php endif; ?>
        <span class="chip <?php echo e($item->type); ?>"><?php echo e($item->isLost() ? 'Lost' : 'Found'); ?></span>
        <?php if($item->resolved): ?> <span class="chip resolved">Resolved</span> <?php endif; ?>
    </div>

    <div class="panel">
        <div class="row between">
            <h1 style="margin:0"><?php echo e($item->title); ?></h1>
            <form method="POST" action="<?php echo e(route('items.save', $item)); ?>">
                <?php echo csrf_field(); ?>
                <button class="heart <?php echo e($isSaved ? 'on' : ''); ?>" type="submit" aria-label="<?php echo e($isSaved ? 'Remove from saved' : 'Save this post'); ?>"><?php echo e($isSaved ? '♥' : '♡'); ?></button>
            </form>
        </div>
        <p class="muted" style="margin-top:4px"><?php echo e($item->location); ?> • <?php echo e($item->category); ?> • <?php echo e($item->created_at->diffForHumans()); ?></p>
        <p style="white-space:pre-wrap"><?php echo e($item->description); ?></p>
    </div>

    <div class="panel">
        <h3>Posted by</h3>
        <dl class="kv">
            <dt>Name</dt><dd><?php echo e($item->user->name); ?></dd>
            <?php if($item->user->programLabel() !== ''): ?> <dt>Program</dt><dd><?php echo e($item->user->programLabel()); ?></dd> <?php endif; ?>
            <?php if($item->user->student_number): ?> <dt>Student No.</dt><dd><?php echo e($item->user->student_number); ?></dd> <?php endif; ?>
            <dt>Email</dt><dd><?php echo e($item->user->email); ?></dd>
            <?php if($item->user->contact): ?> <dt>Contact</dt><dd><?php echo e($item->user->contact); ?></dd> <?php endif; ?>
        </dl>
    </div>

    <?php if($canManage): ?>
        <div class="panel">
            <p><strong><?php echo e($isOwner ? 'This is your post.' : 'Admin controls'); ?></strong> <?php echo e($isOwner ? 'Other students can message or claim it.' : ''); ?></p>
            <div class="row">
                <?php if($item->resolved): ?>
                    <span class="muted">Resolved <?php echo e($item->resolved_at?->diffForHumans()); ?>.</span>
                    <?php if(auth()->user()->isAdmin()): ?>
                        <form method="POST" action="<?php echo e(route('items.reopen', $item)); ?>"><?php echo csrf_field(); ?> <button class="btn ghost small inline" type="submit">Reopen</button></form>
                    <?php endif; ?>
                <?php else: ?>
                    <form method="POST" action="<?php echo e(route('items.resolve', $item)); ?>" onsubmit="return confirm('Mark this item as resolved?')">
                        <?php echo csrf_field(); ?> <button class="btn green small inline" type="submit">Mark Resolved</button>
                    </form>
                <?php endif; ?>
                <a class="btn ghost small inline" href="<?php echo e(route('items.edit', $item)); ?>">Edit</a>
                <form method="POST" action="<?php echo e(route('items.destroy', $item)); ?>" onsubmit="return confirm('Delete this post permanently?')">
                    <?php echo csrf_field(); ?> <button class="btn dark small inline" type="submit">Delete</button>
                </form>
            </div>
        </div>

        <?php if($claims->isNotEmpty()): ?>
            <div class="panel">
                <h3>Claims (<?php echo e($claims->count()); ?>)</h3>
                <?php $__currentLoopData = $claims; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $claim): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="claim">
                        <div class="row between">
                            <strong><?php echo e($claim->user->name); ?></strong>
                            <span class="badge <?php echo e($claim->status); ?>"><?php echo e(ucfirst($claim->status)); ?></span>
                        </div>
                        <div class="muted"><?php echo e($claim->user->programLabel()); ?> • <?php echo e($claim->user->student_number); ?> • <?php echo e($claim->user->contact); ?></div>
                        <p style="white-space:pre-wrap;margin:6px 0">“<?php echo e($claim->proof); ?>”</p>
                        <?php if($claim->status === 'pending' && ! $item->resolved): ?>
                            <div class="row">
                                <form method="POST" action="<?php echo e(route('claims.approve', $claim)); ?>" onsubmit="return confirm('Approve this claim and mark the item resolved?')">
                                    <?php echo csrf_field(); ?> <button class="btn green small inline" type="submit">Approve</button>
                                </form>
                                <form method="POST" action="<?php echo e(route('claims.reject', $claim)); ?>">
                                    <?php echo csrf_field(); ?> <button class="btn ghost small inline" type="submit">Reject</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if(! $isOwner): ?>
        <div class="panel">
            <?php if($item->resolved): ?>
                <p><strong>This item is resolved.</strong> Messaging and claims are closed.</p>
            <?php else: ?>
                <div class="row">
                    <a class="btn inline" href="<?php echo e(route('messages.open', $item)); ?>"><?php echo e($item->isLost() ? 'I found this – message poster' : 'Message poster'); ?></a>
                </div>

                <?php if($item->isFound()): ?>
                    <?php if($myClaim): ?>
                        <p style="margin-top:14px">Your claim: <span class="badge <?php echo e($myClaim->status); ?>"><?php echo e(ucfirst($myClaim->status)); ?></span></p>
                    <?php endif; ?>
                    <details style="margin-top:14px" <?php if($errors->has('proof')): ?> open <?php endif; ?>>
                        <summary class="btn ghost inline small" style="list-style:none"><?php echo e($myClaim ? 'Update claim' : 'Claim this item'); ?></summary>
                        <form method="POST" action="<?php echo e(route('claims.store', $item)); ?>" style="margin-top:12px">
                            <?php echo csrf_field(); ?>
                            <p class="muted">Describe proof of ownership. Chat stays attached to this post.</p>
                            <div class="field">
                                <textarea class="input" name="proof" placeholder="Color, marks, contents…"><?php echo e(old('proof', $myClaim?->proof)); ?></textarea>
                                <?php $__errorArgs = ['proof'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="error-text"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <button class="btn" type="submit">Submit claim</button>
                        </form>
                    </details>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="safety">For your safety, meet in a public, well-lit place to return items.</div>
</main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\STUDENT\Documents\SEEKSAVEAND\SeekSaveAnd\SeekSaveAnd-v2\resources\views/items/show.blade.php ENDPATH**/ ?>