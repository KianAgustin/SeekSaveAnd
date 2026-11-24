@extends('layouts.app')
@section('title', 'Chat')
@section('content')
<main class="container narrow" style="padding-bottom:30px">
    <p><a href="{{ route('messages.index') }}">← Messages</a></p>

    <div class="panel" style="margin-top:0">
        <div class="row between">
            <div>
                <strong><a href="{{ route('items.show', $conversation->item) }}">{{ $conversation->item->icon() }} {{ $conversation->item->category }}</a></strong>
                <div class="muted">
                    Chatting with {{ $other->name }}
                    @if ($conversation->guided)
                        <span class="badge pending">Guided by admin</span>
                    @endif
                </div>
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

    @if ($conversation->guided && $conversation->hasMeetup())
        <div class="panel meetup-box">
            <strong>📅 Meetup schedule</strong>
            <div class="muted" style="margin-top:4px">
                {{ $conversation->meetup_at?->timezone(config('app.timezone'))->format('M j, Y g:i A') }}
                @if ($conversation->meetup_location) • {{ $conversation->meetup_location }} @endif
                @if ($conversation->meetup_status) • <span class="badge pending">{{ ucfirst($conversation->meetup_status) }}</span> @endif
            </div>
            @if ($conversation->meetup_notes)
                <p style="margin:6px 0 0;white-space:pre-wrap">{{ $conversation->meetup_notes }}</p>
            @endif
        </div>
    @endif

    @if ($conversation->guided && auth()->user()->isAdmin())
        <div class="panel">
            <strong>Admin: set meetup schedule</strong>
            <form method="POST" action="{{ route('admin.schedule', $conversation) }}" style="margin-top:10px">
                @csrf
                <div class="field">
                    <label for="meetup_at">Date &amp; time</label>
                    <input class="input" type="datetime-local" id="meetup_at" name="meetup_at"
                           value="{{ old('meetup_at', $conversation->meetup_at?->format('Y-m-d\TH:i')) }}" required>
                </div>
                <div class="field">
                    <label for="meetup_location">Location</label>
                    <input class="input" type="text" id="meetup_location" name="meetup_location"
                           value="{{ old('meetup_location', $conversation->meetup_location ?? 'Student Affairs Office') }}"
                           placeholder="e.g. Student Affairs Office" maxlength="120" required>
                </div>
                <div class="field">
                    <label for="meetup_notes">Notes (optional)</label>
                    <textarea class="input" id="meetup_notes" name="meetup_notes" maxlength="500"
                              placeholder="Bring ID, item will be at front desk…">{{ old('meetup_notes', $conversation->meetup_notes) }}</textarea>
                </div>
                <div class="field">
                    <label for="meetup_status">Status</label>
                    <select class="input" id="meetup_status" name="meetup_status">
                        @foreach (['proposed' => 'Proposed', 'confirmed' => 'Confirmed', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('meetup_status', $conversation->meetup_status ?? 'proposed') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn small inline" type="submit">Save schedule</button>
            </form>
        </div>
    @endif

    @if ($claim && (auth()->user()->isAdmin() || auth()->id() === $conversation->item->user_id))
        <div class="panel">
            <strong>Proof submitted by {{ $claim->user->name }}</strong>
            <p style="white-space:pre-wrap;margin:6px 0">“{{ $claim->proof }}”</p>
            @if ($claim->proofPhotoUrl())
                <img class="form-photo chat-img" src="{{ $claim->proofPhotoUrl() }}" alt="Proof" data-full="{{ $claim->proofPhotoUrl() }}">
            @endif
        </div>
    @endif

    <div class="safety">For your safety, meet in a public, well-lit place to return items.</div>
    <p class="muted" style="text-align:center">This chat is only about this {{ $conversation->item->type }} item ({{ $conversation->item->category }}).</p>

    <div class="chat-box" id="chat">
        @foreach ($messages as $m)
            <div class="bubble {{ $m->sender_id === auth()->id() ? 'me' : 'them' }}" data-id="{{ $m->id }}">
                @if ($m->photoUrl())
                    <img class="chat-img" src="{{ $m->photoUrl() }}" alt="Shared photo" data-full="{{ $m->photoUrl() }}">
                @endif
                @if ($m->body && $m->body !== '📷 Photo')
                    <span class="txt">{{ $m->body }}</span>
                @endif
                <small>{{ $m->created_at->timezone(config('app.timezone'))->format('g:i A') }}</small>
            </div>
        @endforeach
    </div>

    <form class="chat-form" id="chatForm" method="POST" action="{{ route('messages.send', $conversation) }}" enctype="multipart/form-data">
        @csrf
        <label class="chat-attach" title="Attach photo">
            📷
            <input type="file" name="photo" id="photo" accept="image/png,image/jpeg,image/webp" hidden>
        </label>
        <input class="input" type="text" name="body" id="body" placeholder="Message about this item..." maxlength="1000" autocomplete="off">
        <button class="btn" type="submit">Send</button>
    </form>
    <div id="photo-preview" class="photo-preview" hidden>
        <img id="photo-preview-img" alt="Preview">
        <button type="button" id="photo-preview-clear" class="btn ghost small inline">Remove</button>
    </div>
</main>

{{-- Lightbox --}}
<div id="lightbox" class="lightbox" hidden>
    <button type="button" class="lightbox-close" aria-label="Close">&times;</button>
    <img id="lightbox-img" src="" alt="">
</div>
@endsection

@push('scripts')
<script>
(function () {
    var chat = document.getElementById('chat');
    var form = document.getElementById('chatForm');
    var input = document.getElementById('body');
    var photoInput = document.getElementById('photo');
    var preview = document.getElementById('photo-preview');
    var previewImg = document.getElementById('photo-preview-img');
    var previewClear = document.getElementById('photo-preview-clear');
    var csrf = document.querySelector('meta[name="csrf-token"]').content;
    var pollUrl = @json(route('messages.poll', $conversation));
    var lastId = 0;
    chat.querySelectorAll('.bubble').forEach(function (b) { lastId = Math.max(lastId, +b.dataset.id); });

    function openLightbox(src) {
        var lb = document.getElementById('lightbox');
        var img = document.getElementById('lightbox-img');
        img.src = src;
        lb.hidden = false;
        document.body.style.overflow = 'hidden';
    }
    function closeLightbox() {
        var lb = document.getElementById('lightbox');
        lb.hidden = true;
        document.getElementById('lightbox-img').src = '';
        document.body.style.overflow = '';
    }
    document.getElementById('lightbox').addEventListener('click', function (e) {
        if (e.target.id === 'lightbox' || e.target.classList.contains('lightbox-close')) closeLightbox();
    });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeLightbox(); });

    function bindImgClicks(root) {
        (root || document).querySelectorAll('.chat-img').forEach(function (img) {
            if (img.dataset.bound) return;
            img.dataset.bound = '1';
            img.addEventListener('click', function () {
                openLightbox(img.getAttribute('data-full') || img.src);
            });
        });
    }
    bindImgClicks();

    function add(m) {
        if (chat.querySelector('[data-id="' + m.id + '"]')) return;
        var d = document.createElement('div');
        d.className = 'bubble ' + (m.mine ? 'me' : 'them');
        d.dataset.id = m.id;
        if (m.photo_url) {
            var img = document.createElement('img');
            img.className = 'chat-img';
            img.src = m.photo_url;
            img.setAttribute('data-full', m.photo_url);
            img.alt = 'Shared photo';
            d.appendChild(img);
        }
        if (m.body && m.body !== '📷 Photo') {
            var t = document.createElement('span'); t.className = 'txt'; t.textContent = m.body;
            d.appendChild(t);
        }
        var s = document.createElement('small'); s.textContent = m.time;
        d.appendChild(s); chat.appendChild(d);
        lastId = Math.max(lastId, m.id);
        bindImgClicks(d);
        window.scrollTo(0, document.body.scrollHeight);
    }

    function poll() {
        fetch(pollUrl + '?after=' + lastId, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
            .then(function (r) { return r.ok ? r.json() : { messages: [] }; })
            .then(function (j) { j.messages.forEach(add); })
            .catch(function () {});
    }

    photoInput.addEventListener('change', function () {
        if (this.files[0]) {
            previewImg.src = URL.createObjectURL(this.files[0]);
            preview.hidden = false;
        } else {
            preview.hidden = true;
        }
    });
    previewClear.addEventListener('click', function () {
        photoInput.value = '';
        previewImg.src = '';
        preview.hidden = true;
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var text = input.value.trim();
        var hasPhoto = photoInput.files && photoInput.files[0];
        if (!text && !hasPhoto) return;

        var fd = new FormData(form);
        input.value = '';
        photoInput.value = '';
        preview.hidden = true;
        previewImg.src = '';

        fetch(form.action, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            credentials: 'same-origin',
            body: fd
        })
        .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
        .then(function (res) {
            if (res.ok && res.j.id) add(res.j);
            else poll();
        })
        .catch(function () { poll(); });
    });

    setInterval(poll, 4000);
    window.scrollTo(0, document.body.scrollHeight);
})();
</script>
@endpush
