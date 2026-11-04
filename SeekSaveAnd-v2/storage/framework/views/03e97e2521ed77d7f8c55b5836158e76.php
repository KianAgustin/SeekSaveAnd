<?php $__env->startSection('title', 'Chat'); ?>
<?php $__env->startSection('content'); ?>
<main class="container narrow" style="padding-bottom:30px">
    <p><a href="<?php echo e(route('messages.index')); ?>">← Messages</a></p>

    <div class="panel" style="margin-top:0">
        <div class="row between">
            <div>
                <strong><a href="<?php echo e(route('items.show', $conversation->item)); ?>"><?php echo e($conversation->item->icon()); ?> <?php echo e($conversation->item->title); ?></a></strong>
                <div class="muted">Chatting with <?php echo e($other->name); ?></div>
            </div>
            <?php if(auth()->id() === $conversation->item->user_id && ! $conversation->item->resolved): ?>
                <form method="POST" action="<?php echo e(route('items.resolve', $conversation->item)); ?>" onsubmit="return confirm('Mark this item as resolved?')">
                    <?php echo csrf_field(); ?> <button class="btn green small inline" type="submit">Mark Resolved</button>
                </form>
            <?php elseif($conversation->item->resolved): ?>
                <span class="badge approved">Resolved</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="safety">For your safety, meet in a public, well-lit place to return items.</div>
    <p class="muted" style="text-align:center">This chat is only about “<?php echo e($conversation->item->title); ?>”.</p>

    <div class="chat-box" id="chat">
        <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bubble <?php echo e($m->sender_id === auth()->id() ? 'me' : 'them'); ?>" data-id="<?php echo e($m->id); ?>">
                <span class="txt"><?php echo e($m->body); ?></span>
                <small><?php echo e($m->created_at->timezone(config('app.timezone'))->format('g:i A')); ?></small>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <form class="chat-form" id="chatForm" method="POST" action="<?php echo e(route('messages.send', $conversation)); ?>">
        <?php echo csrf_field(); ?>
        <input class="input" type="text" name="body" id="body" placeholder="Message about this item..." maxlength="1000" autocomplete="off" required>
        <button class="btn" type="submit">Send</button>
    </form>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    var chat = document.getElementById('chat');
    var form = document.getElementById('chatForm');
    var input = document.getElementById('body');
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var pollUrl = <?php echo json_encode(route('messages.poll', $conversation), 512) ?>;
    var lastId = 0;
    chat.querySelectorAll('.bubble').forEach(function (b) { lastId = Math.max(lastId, +b.dataset.id); });

    function add(m) {
        if (chat.querySelector('[data-id="' + m.id + '"]')) return;
        var d = document.createElement('div');
        d.className = 'bubble ' + (m.mine ? 'me' : 'them');
        d.dataset.id = m.id;
        var t = document.createElement('span'); t.className = 'txt'; t.textContent = m.body;
        var s = document.createElement('small'); s.textContent = m.time;
        d.appendChild(t); d.appendChild(s); chat.appendChild(d);
        lastId = Math.max(lastId, m.id);
        window.scrollTo(0, document.body.scrollHeight);
    }
    function poll() {
        fetch(pollUrl + '?after=' + lastId, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
            .then(function (r) { return r.ok ? r.json() : { messages: [] }; })
            .then(function (j) { j.messages.forEach(add); })
            .catch(function () {});
    }
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var text = input.value.trim();
        if (!text) return;
        input.value = '';
        fetch(form.action, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            credentials: 'same-origin',
            body: JSON.stringify({ body: text })
        }).then(poll).catch(function () { input.value = text; });
    });
    setInterval(poll, 4000);
    window.scrollTo(0, document.body.scrollHeight);
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\OneDrive\Desktop\SeekSaveAnd-v2-fixed\SeekSaveAnd-v2\resources\views/messages/show.blade.php ENDPATH**/ ?>