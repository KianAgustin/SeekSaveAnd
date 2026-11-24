@extends('layouts.app')
@section('title', 'Guided cases')
@section('content')
<main class="container">
    <h1>Guided cases</h1>
    <div class="subnav">
        <a href="{{ route('admin.index') }}">All posts</a>
        <a href="{{ route('admin.guided') }}" class="on">Guided chats</a>
        <a href="{{ route('admin.users') }}">Users</a>
    </div>

    <p class="muted">Conversations where the poster or claimer requested admin guidance. Set a meetup schedule from inside each chat.</p>

    @if ($conversations->isEmpty())
        <div class="empty">No open guided conversations right now.</div>
    @else
        <div class="list">
            @foreach ($conversations as $c)
                @php
                    $poster = $c->item->user;
                    $claimer = $c->user;
                @endphp
                <a href="{{ route('messages.show', $c) }}">
                    <div class="avatar">
                        @if ($claimer->avatarUrl())
                            <img src="{{ $claimer->avatarUrl() }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:50%">
                        @else
                            {{ $claimer->initials() }}
                        @endif
                    </div>
                    <div style="min-width:0;flex:1">
                        <div>
                            <strong>{{ $c->item->category }}</strong>
                            <span class="badge pending">Guided</span>
                            @if ($c->meetup_status)
                                <span class="badge approved">{{ ucfirst($c->meetup_status) }}</span>
                            @endif
                        </div>
                        <div class="muted">
                            {{ ucfirst($c->item->type) }} • {{ $c->item->location }}
                            • Poster: {{ $poster->name }} • Claimer: {{ $claimer->name }}
                        </div>
                        <div class="muted" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                            {{ $c->latestMessage?->body ?? 'No messages yet' }}
                        </div>
                        @if ($c->hasMeetup())
                            <div class="muted" style="margin-top:2px">
                                📅 {{ $c->meetup_at?->timezone(config('app.timezone'))->format('M j, g:i A') }}
                                @if ($c->meetup_location) @ {{ $c->meetup_location }} @endif
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        @if ($conversations->hasPages())
            <div class="pager">
                @if ($conversations->onFirstPage()) <span>Previous</span> @else <a href="{{ $conversations->previousPageUrl() }}">Previous</a> @endif
                <span class="cur">{{ $conversations->currentPage() }} / {{ $conversations->lastPage() }}</span>
                @if ($conversations->hasMorePages()) <a href="{{ $conversations->nextPageUrl() }}">Next</a> @else <span>Next</span> @endif
            </div>
        @endif
    @endif
</main>
@endsection
