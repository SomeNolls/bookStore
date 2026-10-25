<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Order;
use App\Models\RentedBooks;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RentalTest extends TestCase
{
    use RefreshDatabase;

    private function user($admin = false)
    {
        $user = User::create([
            'name' => 'Ana Petrova',
            'email' => ($admin ? 'admin' : 'reader') . '@example.com',
            'password' => Hash::make('secret123'),
        ]);
        $user->forceFill(['admin' => $admin ? '1' : '0'])->save();

        return $user;
    }

    private function book($attributes = [])
    {
        return Book::create($attributes + [
            'title' => 'Dune',
            'description' => 'Sand.',
            'img' => 'uploads/dune.jpg',
            'number' => 2,
            'stock' => 2,
            'price' => 15,
            'rentPrice' => 3,
            'forRent' => 1,
        ]);
    }

    private function checkout()
    {
        return $this->post('/checkout', [
            'first_name' => 'Ana',
            'last_name' => 'Petrova',
            'email' => 'ana@example.com',
            'phone' => '0888123456',
            'delivery_method' => 'pickup',
            'card_name' => 'Ana Petrova',
            'card_number' => '7111 1111 1111 1111',
            'card_expiry' => '12/' . now()->addYear()->format('y'),
            'card_cvc' => '123',
            'terms' => '1',
        ]);
    }

    public function test_renting_needs_a_signed_in_user()
    {
        $book = $this->book();

        $this->post('/rent', ['book_id' => $book->id, 'months' => 1])->assertRedirect('/login');
        $this->assertNull(session('cart_rentals'));
    }

    public function test_rented_book_goes_through_checkout_without_a_return_date()
    {
        $reader = $this->user();
        $book = $this->book();

        $this->actingAs($reader)->post('/rent', ['book_id' => $book->id, 'months' => 2])->assertRedirect('/cart');
        $this->assertSame([$book->id => 2], session('cart_rentals'));
        // 3$ a month for 2 months.
        $this->get('/cart')->assertOk()->assertViewHas('rentals')->assertViewHas('subtotal', 6.0);

        $this->checkout()->assertRedirect('/checkout/confirmed');

        $order = Order::with('items')->first();
        $rental = RentedBooks::first();
        $this->assertEquals(6, $order->total);
        $this->assertEquals(3, $order->items->first()->price);
        $this->assertSame(2, $order->items->first()->rental_months);
        $this->assertSame([$order->id, $reader->id, 2], [$rental->order_id, $rental->user_id, $rental->months]);
        $this->assertNull($rental->returnDate);
        $this->assertSame([0, 2], [$book->fresh()->forRent, $book->fresh()->number]);

        // It can't be returned before it has started.
        $this->post('/returnBook/' . $rental->id)->assertNotFound();
        $this->get('/profile/' . $reader->id . '?tab=rentals')->assertOk();
    }

    public function test_completing_the_order_sets_the_return_date()
    {
        $admin = $this->user(true);
        $reader = $this->user();
        $book = $this->book();

        $this->actingAs($reader)->post('/rent', ['book_id' => $book->id, 'months' => 3]);
        $this->checkout();
        $order = Order::first();

        $this->actingAs($admin)->patch('/admin/orders/' . $order->id, ['status' => 'delivered']);
        $this->assertNull(RentedBooks::first()->returnDate);

        $this->actingAs($admin)->patch('/admin/orders/' . $order->id, ['status' => 'completed']);
        $this->assertSame(now()->addMonths(3)->toDateString(), RentedBooks::first()->returnDate->toDateString());

        $this->actingAs($admin)->get('/admin/' . $admin->id . '?tab=rentals')->assertOk()->assertViewHas('tab', 'rentals');
    }

    public function test_cancelling_the_order_frees_the_copy_for_rent()
    {
        $admin = $this->user(true);
        $reader = $this->user();
        $book = $this->book();

        $this->actingAs($reader)->post('/rent', ['book_id' => $book->id, 'months' => 1]);
        $this->checkout();

        $this->actingAs($admin)->patch('/admin/orders/' . Order::first()->id, ['status' => 'cancelled']);

        $this->assertSame(0, RentedBooks::count());
        $this->assertSame(1, $book->fresh()->forRent);
    }

    public function test_admin_can_mark_a_rental_returned()
    {
        $admin = $this->user(true);
        $book = $this->book(['forRent' => 0]);
        $rental = RentedBooks::create(['user_id' => $admin->id, 'book_id' => $book->id, 'returnDate' => now()->addMonth()]);

        $this->actingAs($admin)->post('/admin/rentals/' . $rental->id . '/return')
            ->assertRedirect(route('admin', ['user' => $admin->id, 'tab' => 'rentals']));

        $this->assertNull($rental->fresh());
        $this->assertSame(1, $book->fresh()->forRent);
    }

    public function test_books_with_no_copy_to_rent_cannot_be_rented()
    {
        $reader = $this->user();
        $book = $this->book(['forRent' => 0]);

        $this->actingAs($reader)->post('/rent', ['book_id' => $book->id, 'months' => 1]);

        $this->assertSame([], session('cart_rentals', []));
    }
}
