@include('partials.react', ['component' => 'Banner', 'props' => [
    'eyebrow' => 'Your neighbourhood bookstore, online',
    'title' => "Find the book you didn't know you needed.",
    'text' => 'Browse by genre, discover new arrivals, and order online — or reserve and pick up in store.',
    'primaryAction' => ['label' => 'Browse the catalogue', 'url' => url('/books')],
    'secondaryAction' => ['label' => 'See new arrivals', 'url' => url('/') . '#new-arrivals'],
    'books' => $books->take(3)->map(fn ($book) => [
        'title' => $book->title,
        'url' => url('/b/' . $book->id),
    ])->values(),
]])
