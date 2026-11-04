@extends('layouts.app')
@section('title', 'Home')
@section('content')
<section class="hero">
    <div class="hero-inner">
        <form method="GET" action="{{ route('items.index') }}" class="search-row">
            <input type="hidden" name="type" value="{{ $type }}">
            <input class="input grow" type="search" name="q" value="{{ $q }}" placeholder="Looking for…" aria-label="Search items">
            <select class="input loc" name="location" aria-label="Filter by location">
                <option value="">All locations</option>
                @foreach (config('seeksave.locations') as $loc)
                    <option value="{{ $loc }}" @selected($location === $loc)>{{ $loc }}</option>
                @endforeach
            </select>
            <button class="btn go" type="submit">Search</button>
        </form>
        @php $base = array_filter(['q' => $q, 'location' => $location]); @endphp
        <div class="tabs">
            <a href="{{ route('items.index', $base) }}" class="{{ ! $type ? 'on' : '' }}">All</a>
            <a href="{{ route('items.index', $base + ['type' => 'found']) }}" class="{{ $type === 'found' ? 'on' : '' }}">Found</a>
            <a href="{{ route('items.index', $base + ['type' => 'lost']) }}" class="{{ $type === 'lost' ? 'on' : '' }}">Lost</a>
        </div>
    </div>
</section>

<main class="container">
    @if ($items->isEmpty())
        <div class="empty">
            <p><strong>No posts match that search.</strong></p>
            <p>Try another keyword or location, or post the item yourself.</p>
        </div>
    @else
        <div class="grid">
            @foreach ($items as $item) @include('partials.item-card', ['item' => $item]) @endforeach
        </div>
        @if ($items->hasPages())
            <div class="pager">
                @if ($items->onFirstPage()) <span>Previous</span> @else <a href="{{ $items->previousPageUrl() }}">Previous</a> @endif
                <span class="cur">{{ $items->currentPage() }} / {{ $items->lastPage() }}</span>
                @if ($items->hasMorePages()) <a href="{{ $items->nextPageUrl() }}">Next</a> @else <span>Next</span> @endif
            </div>
        @endif
    @endif
</main>

<a class="btn fab" href="{{ route('items.create', ['type' => $type === 'found' ? 'found' : 'lost']) }}">+ Report an item</a>
@endsection
