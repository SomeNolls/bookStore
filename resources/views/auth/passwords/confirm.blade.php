@extends('layouts.auth')

@section('title', 'Confirm password — ' . config('app.name', 'Book Store'))

@section('content')
    @include('partials.react', ['component' => 'ConfirmPassword', 'props' => [
        'homeUrl' => url('/'),
        'action' => route('password.confirm'),
        'csrfToken' => csrf_token(),
        'forgotUrl' => Route::has('password.request') ? route('password.request') : null,
        'errors' => collect($errors->getMessages())->map(fn ($messages) => $messages[0]),
    ]])
@endsection
