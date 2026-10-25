<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Order;
use App\Models\RentedBooks;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class AdminController extends Controller
{
    public const TABS = ['books', 'add', 'orders', 'rentals'];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        // Everyone else is sent to their own account page.
        $this->middleware(function ($request, $next) {
            if (auth()->user()->admin != 1) {
                return redirect()->route('profile.show', auth()->id());
            }

            return $next($request);
        });
    }

    /**
     * The admin panel; the {user} in the URL is only kept for old links.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($user)
    {
        return $this->panel(in_array(request('tab'), self::TABS) ? request('tab') : 'books');
    }

    public function edit(Book $book)
    {
        return $this->panel('books', $book);
    }

    public function store()
    {
        $data = $this->validateBook(true);

        Book::create([
            'title' => $data['title'],
            'author' => $data['author'],
            'genre' => $data['genre'],
            'description' => $data['description'],
            'number' => $data['stock'],
            'stock' => $data['stock'],
            'price' => $data['price'],
            'forRent' => $data['forRent'],
            'rentPrice' => $data['rentPrice'],
            'img' => $this->storeImage(),
        ]);

        return redirect()->route('admin', ['user' => auth()->id(), 'tab' => 'books'])
            ->with('status', "\"{$data['title']}\" is added.");
    }

    public function update(Book $book)
    {
        $data = $this->validateBook(false);

        // Stock is every copy the shop owns; number is the copies not sold or rented out.
        $book->number = max(0, $book->number + $data['stock'] - $book->stock);
        $book->stock = $data['stock'];

        if (request()->hasFile('img')) {
            Storage::disk('public')->delete($book->img);
            $book->img = $this->storeImage();
        }

        $book->fill([
            'title' => $data['title'],
            'author' => $data['author'],
            'genre' => $data['genre'],
            'description' => $data['description'],
            'price' => $data['price'],
            'forRent' => $data['forRent'],
            'rentPrice' => $data['rentPrice'],
        ])->save();

        return redirect()->route('admin', ['user' => auth()->id(), 'tab' => 'books'])
            ->with('status', "\"{$book->title}\" is saved.");
    }

    public function destroy(Book $book)
    {
        Storage::disk('public')->delete($book->img);
        $book->delete();

        return redirect()->route('admin', ['user' => auth()->id(), 'tab' => 'books'])
            ->with('status', "\"{$book->title}\" is deleted.");
    }

    public function updateOrder(Order $order)
    {
        $data = request()->validate([
            'status' => ['required', Rule::in(array_keys(Order::STATUSES))],
        ]);

        $order->changeStatus($data['status']);

        return redirect()->route('admin', ['user' => auth()->id(), 'tab' => 'orders'])
            ->with('status', "Order #{$order->id} is now " . strtolower(Order::STATUSES[$order->status]) . '.');
    }

    public function returnRental(RentedBooks $rental)
    {
        optional($rental->book)->increment('forRent');
        $rental->delete();

        return redirect()->route('admin', ['user' => auth()->id(), 'tab' => 'rentals'])
            ->with('status', 'The rental is marked as returned.');
    }

    private function panel($tab, $editing = null)
    {
        return view('profile/admin', [
            'tab' => $tab,
            'editing' => $editing,
            'books' => Book::orderBy('created_at', 'desc')->get(),
            'orders' => Order::with('items')->latest()->get(),
            'rentals' => RentedBooks::with(['book', 'user', 'order'])->latest()->get(),
        ]);
    }

    private function validateBook($isNew)
    {
        return request()->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'genre' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'stock' => ['required', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'forRent' => ['required', 'integer', 'min:0'],
            'rentPrice' => ['required', 'numeric', 'min:0'],
            'img' => [$isNew ? 'required' : 'nullable', 'image'],
        ]);
    }

    private function storeImage()
    {
        $imagePath = request('img')->store('uploads', 'public');

        Image::make(Storage::disk('public')->path($imagePath))->save();

        return $imagePath;
    }
}
