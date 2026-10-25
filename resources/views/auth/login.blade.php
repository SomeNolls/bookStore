@extends('layouts.auth')

@section('title', 'Sign in — ' . config('app.name', 'Book Store'))

@section('content')
    @include('partials.react', ['component' => 'Login', 'props' => [
        'homeUrl' => url('/'),
        'action' => route('login'),
        'csrfToken' => csrf_token(),
        'registerUrl' => route('register'),
        'forgotUrl' => Route::has('password.request') ? route('password.request') : null,
        'values' => ['email' => old('email'), 'remember' => (bool) old('remember')],
        'errors' => collect($errors->getMessages())->map(fn ($messages) => $messages[0]),
        'status' => session('status'),
    ]])
@endsection
