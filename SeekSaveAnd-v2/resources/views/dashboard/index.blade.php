@extends('layouts.app')
@section('title', 'My profile')
@section('content')
<main class="container narrow">
    <div class="panel" style="margin-top:0">
        <h1 style="margin-bottom:2px">{{ $user->name }}</h1>
        <p class="muted" style="margin-top:0">{{ $user->email }}</p>
        <dl class="kv">
            @if ($user->campus) <dt>Campus</dt><dd>{{ $user->campus }}</dd> @endif
            @if ($user->programLabel() !== '') <dt>Program</dt><dd>{{ $user->programLabel() }}</dd> @endif
            @if ($user->student_number) <dt>Student No.</dt><dd>{{ $user->student_number }}</dd> @endif
            @if ($user->contact) <dt>Contact</dt><dd>{{ $user->contact }}</dd> @endif
        </dl>
        <div class="row" style="margin-top:14px">
            <a class="btn ghost inline small" href="{{ route('profile.edit') }}">Edit my details</a>
            @if ($user->isAdmin()) <a class="btn inline small" href="{{ route('admin.index') }}">Admin dashboard</a> @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf <button class="btn ghost inline small" type="submit">Log out</button>
            </form>
        </div>
    </div>

    <h2 style="margin-top:24px">My Items</h2>
    @if ($myItems->isEmpty())
        <div class="empty">You have not posted anything yet.</div>
    @else
        <div class="list">
            @foreach ($myItems as $item)
                <div style="display:flex;gap:12px;padding:14px 16px;border-top:1px solid #F3E7E0;align-items:center;flex-wrap:wrap">
                    <div class="avatar">{{ $item->icon() }}</div>
                    <div style="flex:1;min-width:160px">
                        <a href="{{ route('items.show', $item) }}"><strong>{{ $item->title }}</strong></a>
                        <div class="muted">{{ $item->isLost() ? 'LOST' : 'FOUND' }} • {{ $item->location }}</div>
                    </div>
                    @if ($item->resolved)
                        <span class="badge approved">Resolved</span>
                    @else
                        <form method="POST" action="{{ route('items.resolve', $item) }}" onsubmit="return confirm('Mark this item as resolved?')">
                            @csrf <button class="btn green small inline" type="submit">Mark Resolved</button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <h2 style="margin-top:24px">Saved posts</h2>
    @if ($savedItems->isEmpty())
        <div class="empty">Tap the heart on a post to save it here.</div>
    @else
        <div class="grid">
            @foreach ($savedItems as $item) @include('partials.item-card', ['item' => $item]) @endforeach
        </div>
    @endif
</main>
@endsection
