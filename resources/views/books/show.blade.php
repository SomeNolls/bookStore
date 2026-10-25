@extends('layouts.site')

@section('title', $book->title . ' — ' . config('app.name', 'Book Store'))

@section('content')
    @include('partials.react', ['component' => 'BookDetail', 'props' => [
        'book' => $book->toCard() + [
            'genre' => $book->genre,
            'description' => $book->description,
            'added' => optional($book->created_at)->format('j M Y'),
            'forRent' => (int) $book->forRent,
        ],
        'catalogueUrl' => url('/books'),
        'genreUrl' => $book->genre ? url('/books') . '?' . http_build_query(['genre' => $book->genre]) : null,
        'authorUrl' => $book->author ? url('/books') . '?' . http_build_query(['search' => $book->author]) : null,
        'cart' => [
            'action' => route('cart.store'),
            'csrfToken' => csrf_token(),
            'bookId' => $book->id,
        ],
        // Renting needs a signed-in user, since rentals are kept on the account.
        'rent' => auth()->check() ? [
            'action' => route('cart.rent'),
            'months' => \App\Support\Cart::RENTAL_MONTHS,
            'csrfToken' => csrf_token(),
            'bookId' => $book->id,
        ] : null,
        'loginUrl' => route('login'),
    ]])

    @if($similar->isNotEmpty())
        @include('partials.react', ['component' => 'BookList', 'props' => [
            'title' => 'You might also like',
            'books' => $similar->map->toCard(),
        ]])
    @endif
@endsection
