<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Support\Cart;
use Illuminate\Validation\Rule;

class CartController extends Controller
{
    public function index()
    {
        $before = Cart::count();
        $lines = Cart::lines();
        $rentals = Cart::rentalLines();

        if (Cart::count() < $before) {
            session()->now('notice', 'Some books in your cart sold out or have fewer copies left, so we updated your cart.');
        }

        return view('cart/index', [
            'lines' => $lines,
            'rentals' => $rentals,
            'subtotal' => Cart::subtotal($lines, $rentals),
        ]);
    }

    public function store()
    {
        $data = request()->validate([
            'book_id' => ['required', 'integer', 'exists:books,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        Cart::add(Book::findOrFail($data['book_id']), $data['quantity'] ?? 1);

        return redirect()->route('cart');
    }

    public function update(Book $book)
    {
        $data = request()->validate([
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        Cart::set($book, $data['quantity']);

        return redirect()->route('cart');
    }

    public function destroy(Book $book)
    {
        Cart::remove($book);

        return redirect()->route('cart');
    }

    public function storeRental()
    {
        $data = request()->validate([
            'book_id' => ['required', 'integer', 'exists:books,id'],
            'months' => ['required', Rule::in(Cart::RENTAL_MONTHS)],
        ]);

        Cart::addRental(Book::findOrFail($data['book_id']), $data['months']);

        return redirect()->route('cart');
    }

    public function destroyRental(Book $book)
    {
        Cart::removeRental($book);

        return redirect()->route('cart');
    }
}
