@extends('layouts.site')

@section('title', 'My account — ' . config('app.name', 'Book Store'))

@section('content')
    @include('partials.react', ['component' => 'Account', 'props' => [
        'firstName' => $user->firstName(),
        'tab' => $tab,
        'tabUrl' => route('profile.show', $user->id),
        'csrfToken' => csrf_token(),
        'logoutUrl' => route('logout'),
        'catalogueUrl' => url('/books'),
        'status' => session('status'),
        'orders' => $orders->map(fn ($order) => [
            'number' => $order->id,
            'date' => $order->created_at->format('j M Y'),
            'count' => $order->items->sum('quantity'),
            'total' => (float) $order->total,
            'subtotal' => (float) $order->subtotal,
            'delivery' => (float) $order->delivery_fee,
            'status' => $order->status,
            'statusLabel' => \App\Models\Order::STATUSES[$order->status] ?? ucfirst($order->status),
            'deliveryMethod' => \App\Models\Order::DELIVERY_METHODS[$order->delivery_method] ?? $order->delivery_method,
            'address' => collect([$order->address, $order->city, $order->postcode])->filter()->implode(', '),
            'payment' => $order->paymentLabel(),
            'items' => $order->items->map(fn ($item) => [
                'title' => $item->title,
                'quantity' => $item->quantity,
                'price' => (float) $item->price,
                'months' => $item->rental_months,
                'url' => $item->book_id ? url('/b/' . $item->book_id) : null,
            ]),
        ]),
        'rentals' => $rentals->filter->book->map(fn ($rental) => [
            'id' => $rental->id,
            'title' => $rental->book->title,
            'url' => url('/b/' . $rental->book_id),
            'rented' => $rental->created_at->format('j M Y'),
            'order' => $rental->order_id,
            'months' => $rental->months,
            // Rentals from an order start when it is completed.
            'started' => $rental->hasStarted(),
            'due' => $rental->hasStarted() ? $rental->returnDate->format('j M Y') : null,
            'overdue' => $rental->hasStarted() && $rental->returnDate->isPast(),
            'timeLeft' => $rental->hasStarted() ? $rental->returnDate->diffForHumans() : null,
            'returnUrl' => route('rentals.return', $rental->id),
        ])->values(),
        'profile' => [
            'action' => route('profile.update'),
            'passwordAction' => route('profile.password'),
            'values' => session()->getOldInput() ?: [
                'email' => $user->email,
            ],
            'errors' => collect($errors->getMessages())->map(fn ($messages) => $messages[0]),
        ],
    ]])
@endsection
