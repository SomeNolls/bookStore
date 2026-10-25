@extends('layouts.site')

@section('title', 'Checkout — ' . config('app.name', 'Book Store'))

@section('content')
    @php($authUser = auth()->user())

    @include('partials.react', ['component' => 'Checkout', 'props' => [
        'items' => $lines->map(fn ($line) => $line['book']->toCard() + ['quantity' => $line['quantity']]),
        'rentals' => $rentals->map(fn ($line) => $line['book']->toCard() + ['months' => $line['months']]),
        'subtotal' => $subtotal,
        'delivery' => \App\Support\Cart::deliveryFee($subtotal),
        'action' => route('checkout.store'),
        'csrfToken' => csrf_token(),
        'cartUrl' => route('cart'),
        // Prefill the name and email from the account; phone and delivery address are only ever entered here.
        'values' => session()->getOldInput() ?: ($authUser ? [
            'first_name' => $authUser->firstName(),
            'last_name' => $authUser->lastName(),
            'email' => $authUser->email,
        ] : []),
        'errors' => collect($errors->getMessages())->map(fn ($messages) => $messages[0]),
        // TODO: replace with the real shop details.
        'store' => ['address' => '[STORE ADDRESS]', 'hours' => '[OPENING HOURS]'],
    ]])
@endsection
