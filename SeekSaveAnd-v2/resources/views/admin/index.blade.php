@extends('layouts.app')
@section('title', 'Admin')
@section('content')
<main class="container">
    <h1>Admin dashboard</h1>
    <div class="subnav">
        <a href="{{ route('admin.index') }}" class="on">All posts</a>
        <a href="{{ route('admin.users') }}">Users</a>
    </div>

    <div class="stats">
        @foreach ($stats as $label => $n)
            <div class="stat"><div class="stat-n">{{ $n }}</div><div class="muted">{{ $label }}</div></div>
        @endforeach
    </div>

    <form method="GET" class="row" style="margin:16px 0">
        <input class="input" style="flex:1 1 220px;width:auto" type="search" name="q" value="{{ $q }}" placeholder="Search title, location, name or email">
        <select class="input" style="width:auto" name="type">
            <option value="">Lost &amp; Found</option>
            <option value="lost" @selected($type === 'lost')>Lost</option>
            <option value="found" @selected($type === 'found')>Found</option>
        </select>
        <select class="input" style="width:auto" name="status">
            <option value="">Any status</option>
            <option value="open" @selected($status === 'open')>Open</option>
            <option value="resolved" @selected($status === 'resolved')>Resolved</option>
        </select>
        <button class="btn inline" type="submit">Filter</button>
    </form>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Item</th><th>Type</th><th>Location</th><th>Posted by</th><th>Status</th><th>Claims</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse ($items as $item)
                <tr>
                    <td>
                        <div class="row" style="flex-wrap:nowrap">
                            <div class="thumb">@if ($item->photoUrl()) <img src="{{ $item->photoUrl() }}" alt=""> @else {{ $item->icon() }} @endif</div>
                            <a href="{{ route('items.show', $item) }}"><strong>{{ $item->title }}</strong></a>
                        </div>
                    </td>
                    <td><span class="badge {{ $item->isLost() ? 'rejected' : 'approved' }}">{{ ucfirst($item->type) }}</span></td>
                    <td>{{ $item->location }}</td>
                    <td>{{ $item->user->name }}<div class="muted">{{ $item->created_at->diffForHumans() }}</div></td>
                    <td>{{ $item->resolved ? 'Resolved' : 'Open' }}</td>
                    <td>{{ $item->pending_claims > 0 ? $item->pending_claims . ' pending' : '—' }}</td>
                    <td>
                        <div class="row" style="flex-wrap:nowrap">
                            @if ($item->resolved)
                                <form method="POST" action="{{ route('items.reopen', $item) }}">@csrf <button class="btn ghost small inline" type="submit">Reopen</button></form>
                            @else
                                <form method="POST" action="{{ route('items.resolve', $item) }}" onsubmit="return confirm('Mark as resolved?')">@csrf <button class="btn green small inline" type="submit">Resolve</button></form>
                            @endif
                            <a class="btn ghost small inline" href="{{ route('items.edit', $item) }}">Edit</a>
                            <form method="POST" action="{{ route('items.destroy', $item) }}" onsubmit="return confirm('Delete this post permanently?')">@csrf <button class="btn dark small inline" type="submit">Delete</button></form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty">No posts match these filters.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($items->hasPages())
        <div class="pager">
            @if ($items->onFirstPage()) <span>Previous</span> @else <a href="{{ $items->previousPageUrl() }}">Previous</a> @endif
            <span class="cur">{{ $items->currentPage() }} / {{ $items->lastPage() }}</span>
            @if ($items->hasMorePages()) <a href="{{ $items->nextPageUrl() }}">Next</a> @else <span>Next</span> @endif
        </div>
    @endif
</main>
@endsection
