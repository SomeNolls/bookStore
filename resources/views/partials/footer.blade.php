@php
    $authUser = auth()->user();
@endphp

@include('partials.react', ['component' => 'Footer', 'props' => [
    'siteName' => 'Book Store',
    'details' => ['[STORE ADDRESS]', '[OPENING HOURS]'],
    'columns' => [
        ['title' => 'Shop', 'links' => [
            ['label' => 'All books', 'url' => url('/books')],
            ['label' => 'Genres', 'url' => '#'],
            ['label' => 'New arrivals', 'url' => url('/') . '#new-arrivals'],
        ]],
        ['title' => 'Account', 'links' => $authUser ? [
            ['label' => 'My profile', 'url' => url('/profile/' . $authUser->id)],
        ] : [
            ['label' => 'Login', 'url' => route('login')],
            ['label' => 'Register', 'url' => route('register')],
        ]],
        ['title' => 'Store', 'links' => [
            ['label' => 'About us', 'url' => '#'],
            ['label' => 'Contact us', 'url' => '#'],
            ['label' => 'Privacy policy', 'url' => '#'],
        ]],
    ],
]])
