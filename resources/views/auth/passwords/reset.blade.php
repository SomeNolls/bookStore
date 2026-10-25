@extends('layouts.auth')

@section('title', 'Choose a new password — ' . config('app.name', 'Book Store'))

@section('content')
    @include('partials.react', ['component' => 'ResetPassword', 'props' => [
        'homeUrl' => url('/'),
        'action' => route('password.update'),
        'csrfToken' => csrf_token(),
        'token' => $token,
        'loginUrl' => route('login'),
        'values' => ['email' => $email ?? old('email')],
        'errors' => collect($errors->getMessages())->map(fn ($messages) => $messages[0]),
    ]])
@endsection
