<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Order;
use App\Support\Cart;
use App\Support\Payment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function show()
    {
        $lines = Cart::lines();
        $rentals = Cart::rentalLines();

        if ($lines->isEmpty() && $rentals->isEmpty()) {
            return redirect()->route('cart');
        }

        return view('checkout/show', [
            'lines' => $lines,
            'rentals' => $rentals,
            'subtotal' => Cart::subtotal($lines, $rentals),
        ]);
    }

    // The same Bulgarian formats are checked as you type in resources/js/components/checkoutValidation.js.
    private const NAME = '/^\p{L}[\p{L}\s.\'-]*$/u';

    private const POSTCODE = '/^[1-9]\d{3}$/';

    public function store()
    {
        // Spaces are only formatting.
        request()->merge([
            'phone' => preg_replace('/\s/', '', (string) request('phone')),
            'card_number' => Payment::digits(request('card_number')),
        ]);

        $data = request()->validate([
            'first_name' => ['required', 'string', 'max:255', 'regex:' . self::NAME],
            'last_name' => ['required', 'string', 'max:255', 'regex:' . self::NAME],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'digits:10'],
            'delivery_method' => ['required', Rule::in(array_keys(Order::DELIVERY_METHODS))],
            'address' => ['required_unless:delivery_method,pickup', 'nullable', 'string', 'min:3', 'max:255'],
            'city' => ['required_unless:delivery_method,pickup', 'nullable', 'string', 'max:255', 'regex:' . self::NAME],
            'postcode' => ['required_unless:delivery_method,pickup', 'nullable', 'regex:' . self::POSTCODE],
            'card_name' => ['required', 'string', 'max:255', 'regex:' . self::NAME],
            'card_number' => ['required', 'digits:16'],
            'card_expiry' => ['required', 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/', function ($attribute, $value, $fail) {
                [$month, $year] = explode('/', $value);

                // A card works until the end of its expiry month.
                if (now()->startOfMonth()->gt(Carbon::create(2000 + (int) $year, (int) $month, 1))) {
                    $fail('This card has expired.');
                }
            }],
            'card_cvc' => ['required', 'regex:/^\d{3,4}$/'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'terms' => ['accepted'],
        ], [
            'first_name.regex' => 'Use letters only.',
            'last_name.regex' => 'Use letters only.',
            'phone.digits' => 'The phone number has 10 digits, e.g. 0888 123 456.',
            'address.required_unless' => 'Enter the address to deliver to.',
            'city.required_unless' => 'Enter the city.',
            'city.regex' => 'Use letters only.',
            'postcode.required_unless' => 'Enter the postcode.',
            'postcode.regex' => 'Bulgarian postcodes have 4 digits, e.g. 1000.',
            'card_name.required' => 'Enter the name on the card.',
            'card_name.regex' => 'Use letters only.',
            'card_number.required' => 'Enter the card number.',
            'card_number.digits' => 'The card number has 16 digits.',
            'card_expiry.required' => 'Enter the expiry date.',
            'card_expiry.regex' => 'Use MM/YY, e.g. 08/28.',
            'card_cvc.required' => 'Enter the CVC.',
            'card_cvc.regex' => 'The CVC is the 3 or 4 digits on the back of the card.',
            'terms.accepted' => 'Please agree to the terms and conditions.',
        ]);

        // Saved as 0888 123 456, however it was spaced.
        $data['phone'] = implode(' ', [substr($data['phone'], 0, 4), substr($data['phone'], 4, 3), substr($data['phone'], 7)]);

        if ($data['delivery_method'] === 'pickup') {
            $data['address'] = $data['city'] = $data['postcode'] = null;
        }

        $card = $data['card_number'];
        $data = collect($data)->except(['card_name', 'card_number', 'card_expiry', 'card_cvc'])->all() + [
            'payment_method' => 'card_online',
            'card_last4' => substr($card, -4),
        ];

        $quantities = Cart::quantities();
        $rentals = Cart::rentalMonths();

        if (Cart::isEmpty()) {
            return redirect()->route('cart');
        }

        // Rentals are kept on the account, so they need a signed-in user.
        if ($rentals && !auth()->check()) {
            throw ValidationException::withMessages(['cart' => 'Please sign in to rent books.']);
        }

        $order = DB::transaction(function () use ($data, $quantities, $rentals, $card) {
            // Lock the rows so two shoppers can't buy or rent the last copy at once.
            $books = Book::whereIn('id', array_keys($quantities + $rentals))->lockForUpdate()->get()->keyBy('id');

            $missing = collect($quantities)->contains(fn ($quantity, $id) => !$books->has($id) || $books[$id]->number < $quantity)
                || collect($rentals)->contains(fn ($months, $id) => !$books->has($id) || $books[$id]->forRent < 1);

            if ($missing) {
                throw ValidationException::withMessages([
                    'cart' => 'Some books in your cart are no longer available in that quantity. Please check your cart.',
                ]);
            }

            $subtotal = round(
                collect($quantities)->map(fn ($quantity, $id) => $books[$id]->price * $quantity)->sum()
                + collect($rentals)->map(fn ($months, $id) => $books[$id]->rentPrice * $months)->sum(),
                2
            );
            $deliveryFee = Cart::deliveryFee($subtotal, $data['delivery_method']);

            // Charged after the stock check and before anything is saved, so a declined card leaves no order behind.
            if (!Payment::charge($card, $subtotal + $deliveryFee)) {
                throw ValidationException::withMessages(['card_number' => 'Your card was declined. Please check the details or try another card.']);
            }

            $order = Order::create(collect($data)->except('terms')->all() + [
                'user_id' => auth()->id(),
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $subtotal + $deliveryFee,
            ]);

            foreach ($quantities as $id => $quantity) {
                $order->items()->create([
                    'book_id' => $id,
                    'title' => $books[$id]->title,
                    'price' => $books[$id]->price,
                    'quantity' => $quantity,
                ]);

                $books[$id]->decrement('number', $quantity);
            }

            // A rental's price is per month; its return date is set once the order is completed (see Order::changeStatus).
            foreach ($rentals as $id => $months) {
                $order->items()->create([
                    'book_id' => $id,
                    'title' => $books[$id]->title,
                    'price' => $books[$id]->rentPrice,
                    'quantity' => 1,
                    'rental_months' => $months,
                ]);

                $order->rentals()->create([
                    'user_id' => auth()->id(),
                    'book_id' => $id,
                    'months' => $months,
                ]);

                $books[$id]->decrement('forRent');
            }

            return $order;
        });

        Cart::clear();
        session(['last_order_id' => $order->id]);

        return redirect()->route('checkout.confirmed');
    }

    public function confirmed()
    {
        // Only the shopper who just placed the order can see it.
        $order = Order::with('items')->findOrFail(session('last_order_id'));

        return view('checkout/confirmed', compact('order'));
    }
}
