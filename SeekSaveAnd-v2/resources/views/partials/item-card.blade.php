<a class="card {{ $item->resolved ? 'done' : '' }}" href="{{ route('items.show', $item) }}">
    <div class="card-visual">
        @if ($item->photoUrl())
            <img src="{{ $item->photoUrl() }}" alt="{{ $item->title }}" loading="lazy">
        @else
            {{ $item->icon() }}
        @endif
        <span class="chip {{ $item->type }}">{{ $item->isLost() ? 'Lost' : 'Found' }}</span>
        @if ($item->resolved) <span class="chip resolved">Resolved</span> @endif
    </div>
    <div class="card-body">
        <div class="card-title">{{ $item->title }}</div>
        <div class="card-meta">{{ $item->location }}</div>
        <div class="card-meta">{{ $item->created_at->diffForHumans() }}</div>
    </div>
</a>
