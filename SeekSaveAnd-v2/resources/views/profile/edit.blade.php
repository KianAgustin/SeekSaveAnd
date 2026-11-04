@extends('layouts.app')
@section('title', 'My details')
@section('content')
<main class="container narrow">
    <h1>{{ $user->profileComplete() ? 'Edit my details' : 'Complete your profile' }}</h1>
    <p class="muted">Signed in as <strong>{{ $user->name }}</strong> ({{ $user->email }}). Other students see these details on your posts so items can be returned.</p>

    <form method="POST" action="{{ route('profile.update') }}" novalidate>
        @csrf
        <div class="field">
            <label for="campus">Campus</label>
            <select class="input" id="campus" name="campus">
                <option value="">Campus</option>
                @foreach (config('seeksave.campuses') as $o) <option value="{{ $o }}" @selected(old('campus', $user->campus) === $o)>{{ $o }}</option> @endforeach
            </select>
            @error('campus') <div class="error-text">{{ $message }}</div> @enderror
        </div>
        <div class="field">
            <label for="program">Program</label>
            <select class="input" id="program" name="program">
                <option value="">Program</option>
                @foreach (config('seeksave.programs') as $o) <option value="{{ $o }}" @selected(old('program', $user->program) === $o)>{{ $o }}</option> @endforeach
            </select>
            @error('program') <div class="error-text">{{ $message }}</div> @enderror
        </div>
        <div class="field">
            <label for="year">Year</label>
            <select class="input" id="year" name="year">
                <option value="">Year</option>
                @foreach (config('seeksave.years') as $o) <option value="{{ $o }}" @selected(old('year', $user->year) === $o)>{{ $o }}</option> @endforeach
            </select>
            @error('year') <div class="error-text">{{ $message }}</div> @enderror
        </div>
        <div class="field">
            <label for="section">Section</label>
            <select class="input" id="section" name="section">
                <option value="">Section</option>
                @foreach (config('seeksave.sections') as $o) <option value="{{ $o }}" @selected(old('section', $user->section) === $o)>{{ $o }}</option> @endforeach
            </select>
            @error('section') <div class="error-text">{{ $message }}</div> @enderror
        </div>
        <div class="field">
            <label for="student_number">Student Number</label>
            <input class="input" id="student_number" type="text" name="student_number" value="{{ old('student_number', $user->student_number) }}" placeholder="e.g. 24-29-6767">
            @error('student_number') <div class="error-text">{{ $message }}</div> @enderror
        </div>
        <div class="field">
            <label for="contact">Contact Number</label>
            <input class="input" id="contact" type="tel" name="contact" value="{{ old('contact', $user->contact) }}" placeholder="09xx xxx xxxx">
            @error('contact') <div class="error-text">{{ $message }}</div> @enderror
        </div>
        <button class="btn" type="submit">Save details</button>
    </form>
</main>
@endsection
