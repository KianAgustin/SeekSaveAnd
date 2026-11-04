@extends('layouts.app')
@section('title', 'Chat')
@section('content')
<main class="container narrow" style="padding-bottom:30px">
    <p><a href="{{ route('messages.index') }}">← Messages</a></p>

    <div class="panel" style="margin-top:0">
        <div class="row between">
            <div>
                <strong><a href="{{ route('items.show', $conversation->item) }}">{{ $conversation->item->icon() }} {{ $conversation->item->title }}</a></strong>
                <div class="muted">Chatting with {{ $other->name }}</div>
            </div>
            @if (auth()->id() === $conversation->item->user_id && ! $conversation->item->resolved)
                <form method="POST" action="{{ route('items.resolve', $conversation->item) }}" onsubmit="return confirm('Mark this item as resolved?')">
                    @csrf <button class="btn green small inline" type="submit">Mark Resolved</button>
                </form>
            @elseif ($conversation->item->resolved)
                <span class="badge approved">Resolved</span>
            @endif
        </div>
    </div>

    <div class="safety">For your safety, meet in a public, well-lit place to return items.</div>
    <p class="muted" style="text-align:center">This chat is only about “{{ $conversation->item->title }}”.</p>

    <div class="chat-box" id="chat">
        @foreach ($messages as $m)
            <div class="bubble {{ $m->sender_id === auth()->id() ? 'me' : 'them' }}" data-id="{{ $m->id }}">
                <span class="txt">{{ $m->body }}</span>
                <small>{{ $m->created_at->timezone(config('app.timezone'))->format('g:i A') }}</small>
            </div>
        @endforeach
    </div>

    <form class="chat-form" id="chatForm" method="POST" action="{{ route('messages.send', $conversation) }}">
        @csrf
        <input class="input" type="text" name="body" id="body" placeholder="Message about this item..." maxlength="1000" autocomplete="off" required>
        <button class="btn" type="submit">Send</button>
    </form>
</main>
@endsection

@push('scripts')
<script>
(function () {
    var chat = document.getElementById('chat');
    var form = document.getElementById('chatForm');
    var input = document.getElementById('body');
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var pollUrl = @json(route('messages.poll', $conversation));
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
@endpush
