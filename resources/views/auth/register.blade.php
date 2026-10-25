@extends('layouts.auth')

@section('title', 'Create an account — ' . config('app.name', 'Book Store'))

@section('content')
    @include('partials.react', ['component' => 'Register', 'props' => [
        'homeUrl' => url('/'),
        'action' => route('register'),
        'csrfToken' => csrf_token(),
        'loginUrl' => route('login'),
        'values' => session()->getOldInput(),
        'errors' => collect($errors->getMessages())->map(fn ($messages) => $messages[0]),
    ]])
@endsection
