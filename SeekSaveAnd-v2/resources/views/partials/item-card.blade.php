<a class="card {{ $item->resolved ? 'done' : '' }}" href="{{ route('items.show', $item) }}">
    <div class="card-visual card-visual--poster">
        @if ($item->user && $item->user->avatarUrl())
            <img src="{{ $item->user->avatarUrl() }}" alt="{{ $item->user->name }}" loading="lazy">
        @else
            <span class="poster-initials">{{ $item->user?->initials() ?? '?' }}</span>
        @endif
        <span class="chip {{ $item->type }}">{{ $item->isLost() ? 'SEEKING' : 'SAVING' }}</span>
        @if ($item->resolved) <span class="chip resolved">SAVED</span> @endif
    </div>
    <div class="card-body">
        <div class="card-title">{{ $item->category }}</div>
        <div class="card-meta">{{ ucfirst($item->type) }} • {{ $item->location }}</div>
        <div class="card-meta">{{ $item->created_at->diffForHumans() }}</div>
    </div>
</a>
