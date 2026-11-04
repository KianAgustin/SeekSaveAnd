<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SeekSaveAnd') – MCC Lost &amp; Found</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/seeksave.css') }}">
</head>
<body>
@auth
@php $unread = \App\Models\Message::unreadCountFor(auth()->user()); @endphp
<header class="topbar">
    <div class="topbar-inner">
        <a class="brand" href="{{ route('items.index') }}">
            @include('partials.logo') SeekSaveAnd
        </a>
        <nav class="nav">
            <a href="{{ route('items.index') }}" class="{{ request()->routeIs('items.index') ? 'active' : '' }}">Home</a>
            <a href="{{ route('messages.index') }}" class="{{ request()->routeIs('messages.*') ? 'active' : '' }}">Messages @if ($unread > 0)<span class="count">{{ $unread }}</span>@endif</a>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Profile</a>
            @if (auth()->user()->isAdmin())
                <a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">Admin</a>
            @endif
            <form method="POST" action="{{ route('logout') }}" style="display:inline">
                @csrf
                <button type="submit">Log out</button>
            </form>
        </nav>
    </div>
</header>
@endauth

<div class="flash">
    @if (session('status')) <div class="ok">{{ session('status') }}</div> @endif
    @if ($errors->has('chat')) <div class="err">{{ $errors->first('chat') }}</div> @endif
</div>

@yield('content')

@stack('scripts')
</body>
</html>
