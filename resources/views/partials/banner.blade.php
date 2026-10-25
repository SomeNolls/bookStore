@include('partials.react', ['component' => 'Banner', 'props' => [
    'title' => "Rent the books you love, return them when you're done.",
    'text' => 'Browse by genre, discover new arrivals, and get books delivered straight to your door.',
    'image' => asset('images/bannerImg.jpg'),
    'imageAlt' => 'A stack of books on a wooden table',
    'primaryAction' => ['label' => 'Browse the catalogue', 'url' => url('/books')],
    'secondaryAction' => ['label' => 'How it works', 'url' => '#how'],
]])
