@extends('layouts.app')
@section('title', 'Report an item')
@section('content')
<main class="container narrow">
    <h1>Report an Item</h1>
    @include('items._form')
</main>
@endsection
