@extends('layouts.app')
@section('title', 'Messages')
@section('content')
<main class="container narrow">
    <h1>Messages</h1>
    @if ($conversations->isEmpty())
        <div class="empty">
            <p><strong>No conversations yet.</strong></p>
            <p>Open a posted item and tap “Message poster”.</p>
        </div>
    @else
        <div class="list">
            @foreach ($conversations as $c)
                @php $other = $c->otherParty(auth()->user()); @endphp
                <a href="{{ route('messages.show', $c) }}">
                    <div class="avatar">{{ $c->item->icon() }}</div>
                    <div style="min-width:0">
                        <div><strong>{{ $c->item->title }}</strong> <span class="muted">with {{ $other->name }}</span> @if ($c->unread_count > 0)<span class="count">{{ $c->unread_count }} new</span>@endif</div>
                        <div class="muted" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                            {{ $c->latestMessage?->body ?? 'No messages yet' }}
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</main>
@endsection
