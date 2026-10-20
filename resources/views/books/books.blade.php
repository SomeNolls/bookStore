@extends('layouts.site')

@section('title', 'All books — ' . config('app.name', 'Book Store'))

@section('content')
    @include('partials.react', ['component' => 'Catalogue', 'props' => [
        'title' => $filters['genre'] ?: 'All books',
        'action' => url('/books'),
        'filters' => $filters,
        'genres' => $genres,
        'sorts' => [
            ['value' => 'newest', 'label' => 'Newest'],
            ['value' => 'price_asc', 'label' => 'Price: low to high'],
            ['value' => 'price_desc', 'label' => 'Price: high to low'],
            ['value' => 'title', 'label' => 'Title A–Z'],
        ],
        'total' => $books->total(),
        'books' => collect($books->items())->map(fn ($book) => $book->toCard() + ['genre' => $book->genre]),
        'pagination' => [
            'pages' => $books->lastPage() > 1 ? collect($books->getUrlRange(1, $books->lastPage()))
                ->map(fn ($url, $page) => ['number' => $page, 'url' => $url, 'active' => $page === $books->currentPage()])
                ->values() : [],
            'prevUrl' => $books->previousPageUrl(),
            'nextUrl' => $books->nextPageUrl(),
        ],
    ]])
@endsection
