@extends('layouts.auth')

@section('title', 'Reset password — ' . config('app.name', 'Book Store'))

@section('content')
    @include('partials.react', ['component' => 'ForgotPassword', 'props' => [
        'homeUrl' => url('/'),
        'action' => route('password.email'),
        'csrfToken' => csrf_token(),
        'loginUrl' => route('login'),
        'values' => ['email' => old('email')],
        'errors' => collect($errors->getMessages())->map(fn ($messages) => $messages[0]),
        'status' => session('status'),
    ]])
@endsection
