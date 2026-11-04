@extends('layouts.app')
@section('title', 'Edit post')
@section('content')
<main class="container narrow">
    <p><a href="{{ route('items.show', $item) }}">← Back to post</a></p>
    <h1>Edit post</h1>
    @include('items._form')
</main>
@endsection
