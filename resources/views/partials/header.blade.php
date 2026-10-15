@php
    $authUser = auth()->user();

    $headerProps = [
        'homeUrl' => url('/'),
        'siteName' => 'Book Store',
        'links' => [
            ['label' => 'Books', 'url' => url('/books')],
            ['label' => 'Genres', 'url' => '#'],
            ['label' => 'New Arrivals', 'url' => url('/') . '#new-arrivals'],
            ['label' => 'About Us', 'url' => '/about-us'],
            ['label' => 'Contact Us', 'url' => '/contact-us'],
        ],
        'currentPath' => '/' . ltrim(request()->path(), '/'),
        'searchUrl' => url('/books'),
        'search' => (string) request('search', ''),
        'cartUrl' => '#',
        'cartCount' => 0,
        'loginUrl' => Route::has('login') ? route('login') : null,
        'registerUrl' => Route::has('register') ? route('register') : null,
        'logoutUrl' => route('logout'),
        'csrfToken' => csrf_token(),
        'user' => $authUser ? [
            'name' => $authUser->name,
            'profileUrl' => url('/profile/' . $authUser->id),
        ] : null,
        // Only sent to admins, so the button never renders for anyone else.
        'adminUrl' => ($authUser && $authUser->admin == 1) ? url('/admin/' . $authUser->id) : null,
    ];
@endphp

@include('partials.react', ['component' => 'Header', 'props' => $headerProps])
