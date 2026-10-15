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
        @include('partials.banner', ['books' => $latestBooks])

        <main>
            @include('partials.react', ['component' => 'Genres', 'props' => [
                'genres' => collect(['All', 'Fiction', 'Classics', 'Fantasy', 'Mystery & Thriller', 'Science', 'History', 'Children'])
                    ->map(fn ($name) => [
                        'name' => $name,
                        'url' => $name === 'All' ? url('/books') : '#',
                    ]),
            ]])

            @include('partials.react', ['component' => 'NewArrivals', 'props' => [
                'viewAllUrl' => url('/books'),
                'books' => $latestBooks->map(fn ($book) => [
                    'title' => $book->title,
                    'description' => \Illuminate\Support\Str::limit($book->description, 80),
                    'img' => $book->img ? asset('storage/' . $book->img) : null,
                    'url' => url('/b/' . $book->id),
                    'available' => (int) $book->number,
                ]),
            ]])

            @include('partials.react', ['component' => 'Features', 'props' => [
                'items' => [
                    ['icon' => 'search', 'title' => 'Smart search & filters', 'text' => 'Filter by genre, author, price, language and availability.'],
                    ['icon' => 'star', 'title' => 'Reader reviews', 'text' => 'Ratings and reviews from registered readers on every book.'],
                    ['icon' => 'bag', 'title' => 'Reserve & pick up', 'text' => 'Order online and collect in store, with order tracking in your account.'],
                ],
            ]])

            @include('partials.react', ['component' => 'Newsletter', 'props' => [
                'title' => 'New books in your inbox',
                'text' => 'A short monthly email with new arrivals and staff picks.',
                'action' => '#',
                'csrfToken' => csrf_token(),
            ]])
        </main>

        @include('partials.footer')

        <div class="js-container"></div>

        <script src="{{ mix('js/welcome.js') }}"></script>
    </body>
</html>
