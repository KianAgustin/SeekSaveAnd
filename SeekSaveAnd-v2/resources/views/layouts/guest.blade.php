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
<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-logo">@include('partials.logo')</div>
        @if (session('status')) <div class="flash" style="padding:0"><div class="ok">{{ session('status') }}</div></div> @endif
        @yield('content')
    </div>
</div>
@stack('scripts')
</body>
</html>
