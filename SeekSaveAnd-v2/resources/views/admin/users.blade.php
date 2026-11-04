@extends('layouts.app')
@section('title', 'Admin – Users')
@section('content')
<main class="container">
    <h1>Admin dashboard</h1>
    <div class="subnav">
        <a href="{{ route('admin.index') }}">All posts</a>
        <a href="{{ route('admin.users') }}" class="on">Users</a>
    </div>

    <form method="GET" class="row" style="margin:16px 0">
        <input class="input" style="flex:1 1 220px;width:auto" type="search" name="q" value="{{ $q }}" placeholder="Search name, email or student number">
        <button class="btn inline" type="submit">Search</button>
    </form>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Program</th><th>Student No.</th><th>Posts</th><th>Role</th><th></th></tr></thead>
            <tbody>
            @foreach ($users as $u)
                <tr>
                    <td><strong>{{ $u->name }}</strong></td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->programLabel() ?: '—' }}</td>
                    <td>{{ $u->student_number ?: '—' }}</td>
                    <td>{{ $u->items_count }}</td>
                    <td><span class="badge {{ $u->isAdmin() ? 'pending' : 'approved' }}">{{ $u->isAdmin() ? 'Admin' : 'Student' }}</span></td>
                    <td>
                        @if ($u->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.role', $u) }}" onsubmit="return confirm('Change role for {{ e($u->name) }}?')">
                                @csrf <button class="btn ghost small inline" type="submit">{{ $u->isAdmin() ? 'Remove admin' : 'Make admin' }}</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @if ($users->hasPages())
        <div class="pager">
            @if ($users->onFirstPage()) <span>Previous</span> @else <a href="{{ $users->previousPageUrl() }}">Previous</a> @endif
            <span class="cur">{{ $users->currentPage() }} / {{ $users->lastPage() }}</span>
            @if ($users->hasMorePages()) <a href="{{ $users->nextPageUrl() }}">Next</a> @else <span>Next</span> @endif
        </div>
    @endif
</main>
@endsection
