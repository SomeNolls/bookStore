@extends('layouts.site')

@section('title', 'Your cart — ' . config('app.name', 'Book Store'))

@section('content')
    @include('partials.react', ['component' => 'Cart', 'props' => [
        'items' => $lines->map(fn ($line) => $line['book']->toCard() + [
            'id' => $line['book']->id,
            'quantity' => $line['quantity'],
            'updateUrl' => route('cart.update', $line['book']),
            'removeUrl' => route('cart.destroy', $line['book']),
        ]),
        'rentals' => $rentals->map(fn ($line) => $line['book']->toCard() + [
            'id' => $line['book']->id,
            'months' => $line['months'],
            'removeUrl' => route('cart.rentals.destroy', $line['book']),
        ]),
        'subtotal' => $subtotal,
        'delivery' => \App\Support\Cart::deliveryFee($subtotal),
        'freeDeliveryFrom' => \App\Support\Cart::FREE_DELIVERY_FROM,
        'notice' => session('notice'),
        'csrfToken' => csrf_token(),
        'catalogueUrl' => url('/books'),
        'checkoutUrl' => route('checkout'),
    ]])
@endsection
