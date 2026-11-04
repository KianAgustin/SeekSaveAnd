@extends('layouts.app')
@section('title', $item->title)
@section('content')
<main class="container narrow">
    <p><a href="{{ route('items.index') }}">← Back to all posts</a></p>

    <div class="detail-visual">
        @if ($item->photoUrl())
            <img src="{{ $item->photoUrl() }}" alt="{{ $item->title }}">
        @else
            {{ $item->icon() }}
        @endif
        <span class="chip {{ $item->type }}">{{ $item->isLost() ? 'Lost' : 'Found' }}</span>
        @if ($item->resolved) <span class="chip resolved">Resolved</span> @endif
    </div>

    <div class="panel">
        <div class="row between">
            <h1 style="margin:0">{{ $item->title }}</h1>
            <form method="POST" action="{{ route('items.save', $item) }}">
                @csrf
                <button class="heart {{ $isSaved ? 'on' : '' }}" type="submit" aria-label="{{ $isSaved ? 'Remove from saved' : 'Save this post' }}">{{ $isSaved ? '♥' : '♡' }}</button>
            </form>
        </div>
        <p class="muted" style="margin-top:4px">{{ $item->location }} • {{ $item->category }} • {{ $item->created_at->diffForHumans() }}</p>
        <p style="white-space:pre-wrap">{{ $item->description }}</p>
    </div>

    <div class="panel">
        <h3>Posted by</h3>
        <dl class="kv">
            <dt>Name</dt><dd>{{ $item->user->name }}</dd>
            @if ($item->user->programLabel() !== '') <dt>Program</dt><dd>{{ $item->user->programLabel() }}</dd> @endif
            @if ($item->user->student_number) <dt>Student No.</dt><dd>{{ $item->user->student_number }}</dd> @endif
            <dt>Email</dt><dd>{{ $item->user->email }}</dd>
            @if ($item->user->contact) <dt>Contact</dt><dd>{{ $item->user->contact }}</dd> @endif
        </dl>
    </div>

    @if ($canManage)
        <div class="panel">
            <p><strong>{{ $isOwner ? 'This is your post.' : 'Admin controls' }}</strong> {{ $isOwner ? 'Other students can message or claim it.' : '' }}</p>
            <div class="row">
                @if ($item->resolved)
                    <span class="muted">Resolved {{ $item->resolved_at?->diffForHumans() }}.</span>
                    @if (auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('items.reopen', $item) }}">@csrf <button class="btn ghost small inline" type="submit">Reopen</button></form>
                    @endif
                @else
                    <form method="POST" action="{{ route('items.resolve', $item) }}" onsubmit="return confirm('Mark this item as resolved?')">
                        @csrf <button class="btn green small inline" type="submit">Mark Resolved</button>
                    </form>
                @endif
                <a class="btn ghost small inline" href="{{ route('items.edit', $item) }}">Edit</a>
                <form method="POST" action="{{ route('items.destroy', $item) }}" onsubmit="return confirm('Delete this post permanently?')">
                    @csrf <button class="btn dark small inline" type="submit">Delete</button>
                </form>
            </div>
        </div>

        @if ($claims->isNotEmpty())
            <div class="panel">
                <h3>Claims ({{ $claims->count() }})</h3>
                @foreach ($claims as $claim)
                    <div class="claim">
                        <div class="row between">
                            <strong>{{ $claim->user->name }}</strong>
                            <span class="badge {{ $claim->status }}">{{ ucfirst($claim->status) }}</span>
                        </div>
                        <div class="muted">{{ $claim->user->programLabel() }} • {{ $claim->user->student_number }} • {{ $claim->user->contact }}</div>
                        <p style="white-space:pre-wrap;margin:6px 0">“{{ $claim->proof }}”</p>
                        @if ($claim->status === 'pending' && ! $item->resolved)
                            <div class="row">
                                <form method="POST" action="{{ route('claims.approve', $claim) }}" onsubmit="return confirm('Approve this claim and mark the item resolved?')">
                                    @csrf <button class="btn green small inline" type="submit">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('claims.reject', $claim) }}">
                                    @csrf <button class="btn ghost small inline" type="submit">Reject</button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    @endif

    @if (! $isOwner)
        <div class="panel">
            @if ($item->resolved)
                <p><strong>This item is resolved.</strong> Messaging and claims are closed.</p>
            @else
                <div class="row">
                    <a class="btn inline" href="{{ route('messages.open', $item) }}">{{ $item->isLost() ? 'I found this – message poster' : 'Message poster' }}</a>
                </div>

                @if ($item->isFound())
                    @if ($myClaim)
                        <p style="margin-top:14px">Your claim: <span class="badge {{ $myClaim->status }}">{{ ucfirst($myClaim->status) }}</span></p>
                    @endif
                    <details style="margin-top:14px" @if ($errors->has('proof')) open @endif>
                        <summary class="btn ghost inline small" style="list-style:none">{{ $myClaim ? 'Update claim' : 'Claim this item' }}</summary>
                        <form method="POST" action="{{ route('claims.store', $item) }}" style="margin-top:12px">
                            @csrf
                            <p class="muted">Describe proof of ownership. Chat stays attached to this post.</p>
                            <div class="field">
                                <textarea class="input" name="proof" placeholder="Color, marks, contents…">{{ old('proof', $myClaim?->proof) }}</textarea>
                                @error('proof') <div class="error-text">{{ $message }}</div> @enderror
                            </div>
                            <button class="btn" type="submit">Submit claim</button>
                        </form>
                    </details>
                @endif
            @endif
        </div>
    @endif

    <div class="safety">For your safety, meet in a public, well-lit place to return items.</div>
</main>
@endsection
