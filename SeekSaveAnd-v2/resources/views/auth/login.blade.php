@extends('layouts.guest')
@section('title', 'Sign in')
@section('content')
    <h1 class="auth-title">SeekSaveAnd</h1>
    <p class="auth-sub">MCC Lost &amp; Found Platform</p>

    @error('login') <div class="flash" style="padding:0"><div class="err">{{ $message }}</div></div> @enderror

    <a class="btn ms-btn" href="{{ route('microsoft.redirect') }}">
        <svg width="20" height="20" viewBox="0 0 21 21" aria-hidden="true"><rect x="1" y="1" width="9" height="9" fill="#F25022"/><rect x="11" y="1" width="9" height="9" fill="#7FBA00"/><rect x="1" y="11" width="9" height="9" fill="#00A4EF"/><rect x="11" y="11" width="9" height="9" fill="#FFB900"/></svg>
        Sign in with your school Microsoft account
    </a>
    <p class="demo-note">Use the same school email and password you use for Microsoft 365 / Teams.</p>

    @if ($devUsers->isNotEmpty())
        <div class="panel" style="margin-top:22px">
            <strong>Local testing only</strong>
            <p class="muted" style="margin:4px 0 10px">Sign in as a seeded user without Microsoft.</p>
            @foreach ($devUsers as $u)
                <a class="btn ghost small" style="margin-bottom:8px" href="{{ route('dev.login', $u) }}">
                    {{ $u->name }} {{ $u->isAdmin() ? '(admin)' : '' }}
                </a>
            @endforeach
        </div>
    @endif
@endsection
