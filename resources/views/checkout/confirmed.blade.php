@extends('layouts.site')

@section('title', 'Order placed — ' . config('app.name', 'Book Store'))

@section('content')
    @include('partials.react', ['component' => 'OrderConfirmed', 'props' => [
        'order' => [
            'number' => $order->id,
            'email' => $order->email,
            'deliveryMethod' => $order->delivery_method,
            'items' => $order->items->map(fn ($item) => [
                'title' => $item->title,
                'quantity' => $item->quantity,
                'price' => (float) $item->price,
                'months' => $item->rental_months,
            ]),
            'subtotal' => (float) $order->subtotal,
            'delivery' => (float) $order->delivery_fee,
            'total' => (float) $order->total,
        ],
        'catalogueUrl' => url('/books'),
    ]])
@endsection
