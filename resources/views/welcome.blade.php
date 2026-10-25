<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Book Store') }}</title>

        <script src="{{ asset('js/app.js') }}" defer></script>
        @include('partials.assets')
    </head>
    <body>

        @include('partials.header')
        @include('partials.banner')

        <main>
            @include('partials.react', ['component' => 'Features', 'props' => [
                'items' => [
                    ['icon' => 'book', 'title' => 'A large catalogue', 'text' => 'books in Bulgarian and English'],
                    ['icon' => 'tag', 'title' => 'Fair prices', 'text' => 'clear and always up to date'],
                    ['icon' => 'truck', 'title' => 'Delivered to your door', 'text' => 'and returned the same way'],
                    ['icon' => 'calendar', 'title' => 'Track your rentals', 'text' => 'every due date in your account'],
                ],
            ]])

            @include('partials.react', ['component' => 'Genres', 'props' => [
                'genres' => collect(['All', ...\App\Models\Book::GENRES])
                    ->map(fn ($name) => [
                        'name' => $name,
                        'url' => $name === 'All' ? url('/books') : url('/books') . '?' . http_build_query(['genre' => $name]),
                    ]),
            ]])

            @include('partials.react', ['component' => 'BookList', 'props' => [
                'title' => 'New arrivals',
                'id' => 'new-arrivals',
                'viewAllUrl' => url('/books'),
                'books' => $latestBooks->map->toCard(),
            ]])

            @php($rentalMonths = \App\Support\Cart::RENTAL_MONTHS)
            @include('partials.react', ['component' => 'HowItWorks', 'props' => [
                'title' => 'How to rent books',
                'intro' => "Renting is simple: choose a book and a rental period, have it delivered to your door, and send it back when you're done. If anything goes wrong with a rental, our team is here to help by email, phone or the contact form.",
                'steps' => [
                    ['icon' => 'search', 'title' => 'Rent', 'text' => 'Search by title or author and choose a rental period ('
                        . implode(', ', array_slice($rentalMonths, 0, -1)) . ' or ' . last($rentalMonths)
                        . " months). We'll deliver it to your door."],
                    ['icon' => 'book', 'title' => 'Read', 'text' => 'Treat the book with care. Someone else will read it after you, so please no writing, highlighting or folded pages.'],
                    ['icon' => 'calendar', 'title' => 'Return', 'text' => 'Send it back by courier before the due date. You can see every due date in your account.'],
                ],
            ]])
        </main>

        @include('partials.footer')
    </body>
</html>
