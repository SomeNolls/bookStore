@extends('layouts.site')

@section('title', 'Admin panel — ' . config('app.name', 'Book Store'))

@section('content')
    @php($bookValues = fn ($book) => [
        'title' => $book->title,
        'author' => $book->author,
        'genre' => $book->genre,
        'description' => $book->description,
        'stock' => $book->stock,
        'price' => $book->price,
        'forRent' => $book->forRent,
        'rentPrice' => $book->rentPrice,
    ])

    @include('partials.react', ['component' => 'Admin', 'props' => [
        'tab' => $tab,
        'tabUrl' => route('admin', auth()->id()),
        'csrfToken' => csrf_token(),
        'status' => session('status'),
        'genres' => \App\Models\Book::genres(),
        'statuses' => \App\Models\Order::STATUSES,
        'books' => $books->map(fn ($book) => [
            'id' => $book->id,
            'title' => $book->title,
            'author' => $book->author,
            'genre' => $book->genre,
            'price' => (float) $book->price,
            'rentPrice' => (float) $book->rentPrice,
            'available' => (int) $book->number,
            'stock' => (int) $book->stock,
            'forRent' => (int) $book->forRent,
            'img' => $book->img ? asset('storage/' . $book->img) : null,
            'viewUrl' => url('/b/' . $book->id),
            'editUrl' => route('admin.books.edit', $book->id),
            'deleteUrl' => route('admin.books.destroy', $book->id),
        ]),
        // The add form and the edit form share one set of old input and errors; only one is ever on screen.
        'bookForm' => [
            'action' => $editing ? route('admin.books.update', $editing->id) : route('admin.books.store'),
            'editing' => $editing ? ['title' => $editing->title, 'img' => asset('storage/' . $editing->img)] : null,
            'values' => session()->getOldInput() ?: ($editing ? $bookValues($editing) : ['forRent' => 0, 'rentPrice' => 0]),
            'errors' => collect($errors->getMessages())->map(fn ($messages) => $messages[0]),
        ],
        'orders' => $orders->map(fn ($order) => [
            'number' => $order->id,
            'date' => $order->created_at->format('j M Y'),
            'customer' => trim($order->first_name . ' ' . $order->last_name),
            'email' => $order->email,
            'phone' => $order->phone,
            'count' => $order->items->sum('quantity'),
            'total' => (float) $order->total,
            'delivery' => (float) $order->delivery_fee,
            'status' => $order->status,
            'statusUrl' => route('admin.orders.update', $order->id),
            'deliveryMethod' => \App\Models\Order::DELIVERY_METHODS[$order->delivery_method] ?? $order->delivery_method,
            'address' => collect([$order->address, $order->city, $order->postcode])->filter()->implode(', '),
            'payment' => $order->paymentLabel(),
            'notes' => $order->notes,
            'items' => $order->items->map(fn ($item) => [
                'title' => $item->title,
                'quantity' => $item->quantity,
                'price' => (float) $item->price,
                'months' => $item->rental_months,
                'url' => $item->book_id ? url('/b/' . $item->book_id) : null,
            ]),
        ]),
        'rentals' => $rentals->map(fn ($rental) => [
            'id' => $rental->id,
            'title' => optional($rental->book)->title ?? 'Deleted book',
            'url' => $rental->book ? url('/b/' . $rental->book_id) : null,
            'customer' => optional($rental->user)->name ?? 'Deleted account',
            'email' => optional($rental->user)->email,
            'order' => $rental->order_id,
            'orderStatus' => $rental->order ? (\App\Models\Order::STATUSES[$rental->order->status] ?? $rental->order->status) : null,
            'months' => $rental->months,
            'rented' => $rental->created_at->format('j M Y'),
            'started' => $rental->hasStarted(),
            'due' => $rental->hasStarted() ? $rental->returnDate->format('j M Y') : null,
            'overdue' => $rental->hasStarted() && $rental->returnDate->isPast(),
            'timeLeft' => $rental->hasStarted() ? $rental->returnDate->diffForHumans() : null,
            'returnUrl' => route('admin.rentals.return', $rental->id),
        ]),
    ]])
@endsection
