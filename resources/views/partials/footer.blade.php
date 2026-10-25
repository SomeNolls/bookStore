@php
    $authUser = auth()->user();
    // Opens a tab of the account page; guests are sent to sign in first.
    $accountTab = fn ($tab) => $authUser ? route('profile.show', ['user' => $authUser->id, 'tab' => $tab]) : route('login');
@endphp

@include('partials.react', ['component' => 'Footer', 'props' => [
    'siteName' => 'Book Store',
    'year' => now()->year,
    'columns' => [
        ['title' => 'Shopping', 'links' => [
            ['label' => 'How renting works', 'url' => url('/') . '#how'],
            ['label' => 'Delivery and payment', 'url' => '#'],
            ['label' => 'Terms and conditions', 'url' => '#'],
        ]],
        ['title' => 'The store', 'links' => [
            ['label' => 'About us', 'url' => '/about-us'],
            ['label' => 'Contacts', 'url' => '/contact-us'],
            ['label' => 'Opening hours', 'url' => '/contact-us'],
        ]],
        ['title' => 'Customers', 'links' => [
            ['label' => 'My account', 'url' => $accountTab('profile')],
            ['label' => 'Order status', 'url' => $accountTab('orders')],
        ]],
    ],
    // TODO: replace with the shop's real profiles.
    'social' => [
        ['label' => 'Instagram', 'icon' => 'instagram', 'url' => '#'],
        ['label' => 'Facebook', 'icon' => 'facebook', 'url' => '#'],
        ['label' => 'YouTube', 'icon' => 'youtube', 'url' => '#'],
    ],
    'legal' => [
        ['label' => 'Cookies', 'url' => '#'],
        ['label' => 'Privacy', 'url' => '#'],
        ['label' => 'Accessibility', 'url' => '#'],
    ],
]])
