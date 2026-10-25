<?php

namespace App\Support;

use App\Models\Book;

/**
 * The shopping cart, kept in the session as [book id => quantity] for books to buy
 * and [book id => months] for books to rent (one copy each).
 */
class Cart
{
    private const SESSION_KEY = 'cart';

    private const RENTALS_KEY = 'cart_rentals';

    public const RENTAL_MONTHS = [1, 2, 3];

    public const FREE_DELIVERY_FROM = 40;

    public const DELIVERY_FEE = 4.5;

    public static function quantities()
    {
        return session(self::SESSION_KEY, []);
    }

    public static function rentalMonths()
    {
        return session(self::RENTALS_KEY, []);
    }

    public static function count()
    {
        return array_sum(self::quantities()) + count(self::rentalMonths());
    }

    public static function isEmpty()
    {
        return self::count() === 0;
    }

    public static function add(Book $book, int $quantity = 1)
    {
        $current = self::quantities()[$book->id] ?? 0;

        self::set($book, $current + $quantity);
    }

    /**
     * Sets a book's quantity, capped at the copies in stock; 0 or less removes it.
     */
    public static function set(Book $book, int $quantity)
    {
        $quantities = self::quantities();
        $quantity = min($quantity, (int) $book->number);

        if ($quantity > 0) {
            $quantities[$book->id] = $quantity;
        } else {
            unset($quantities[$book->id]);
        }

        session([self::SESSION_KEY => $quantities]);
    }

    public static function remove(Book $book)
    {
        self::set($book, 0);
    }

    /**
     * Puts one copy of the book in the cart to rent, if a copy is free to rent.
     */
    public static function addRental(Book $book, int $months)
    {
        if ($book->forRent > 0) {
            session([self::RENTALS_KEY => [$book->id => $months] + self::rentalMonths()]);
        }
    }

    public static function removeRental(Book $book)
    {
        $months = self::rentalMonths();
        unset($months[$book->id]);

        session([self::RENTALS_KEY => $months]);
    }

    public static function clear()
    {
        session()->forget([self::SESSION_KEY, self::RENTALS_KEY]);
    }

    /**
     * The cart's books with their quantities. Books that were deleted or sold out are
     * dropped and quantities above the stock are lowered, and the session is updated to match.
     *
     * @return \Illuminate\Support\Collection<int, array{book: Book, quantity: int}>
     */
    public static function lines()
    {
        $quantities = self::quantities();
        $books = Book::whereIn('id', array_keys($quantities))->get()->keyBy('id');

        $lines = collect($quantities)
            ->map(fn ($quantity, $id) => [
                'book' => $books->get($id),
                'quantity' => $books->has($id) ? min($quantity, (int) $books[$id]->number) : 0,
            ])
            ->filter(fn ($line) => $line['quantity'] > 0)
            ->values();

        session([self::SESSION_KEY => $lines->mapWithKeys(fn ($line) => [$line['book']->id => $line['quantity']])->all()]);

        return $lines;
    }

    /**
     * The books in the cart to rent with their months. Books that were deleted or have no copy
     * left to rent are dropped, and the session is updated to match.
     *
     * @return \Illuminate\Support\Collection<int, array{book: Book, months: int}>
     */
    public static function rentalLines()
    {
        $months = self::rentalMonths();
        $books = Book::whereIn('id', array_keys($months))->where('forRent', '>', 0)->get()->keyBy('id');

        $lines = collect($months)
            ->filter(fn ($length, $id) => $books->has($id))
            ->map(fn ($length, $id) => ['book' => $books[$id], 'months' => $length])
            ->values();

        session([self::RENTALS_KEY => $lines->mapWithKeys(fn ($line) => [$line['book']->id => $line['months']])->all()]);

        return $lines;
    }

    /**
     * Bought books at their price, plus rented books at their monthly rent price for each month.
     */
    public static function subtotal($lines, $rentalLines = null)
    {
        $bought = $lines->sum(fn ($line) => $line['book']->price * $line['quantity']);
        $rented = collect($rentalLines)->sum(fn ($line) => $line['book']->rentPrice * $line['months']);

        return round($bought + $rented, 2);
    }

    /**
     * Pick-up is always free; delivery is free from FREE_DELIVERY_FROM.
     */
    public static function deliveryFee($subtotal, $method = 'courier')
    {
        return $method === 'pickup' || $subtotal >= self::FREE_DELIVERY_FROM ? 0 : self::DELIVERY_FEE;
    }
}
