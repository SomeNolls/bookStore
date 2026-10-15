@php
    $bannerProps = [
        'eyebrow' => 'Your neighbourhood bookstore, online',
        'title' => "Find the book you didn't know you needed.",
        'text' => 'Browse by genre, discover new arrivals, and order online — or reserve and pick up in store.',
        'primaryAction' => ['label' => 'Browse the catalogue', 'url' => url('/books')],
        'secondaryAction' => ['label' => 'See new arrivals', 'url' => '#'],
        'books' => \App\Models\Book::latest()->take(3)->get()->map(fn ($book) => [
            'title' => $book->title,
            'url' => url('/b/' . $book->id),
        ])->values(),
    ];
@endphp

<link href="{{ mix('css/banner.css') }}" rel="stylesheet">

<div id="site-banner" data-props="{{ json_encode($bannerProps) }}"></div>
<script src="{{ mix('js/banner.js') }}" defer></script>
