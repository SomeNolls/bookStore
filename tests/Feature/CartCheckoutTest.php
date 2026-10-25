<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function book($attributes = [])
    {
        return Book::create($attributes + [
            'title' => 'Frankenstein',
            'author' => 'Mary Shelley',
            'description' => 'A classic.',
            'img' => 'uploads/cover.jpg',
            'number' => 3,
            'stock' => 3,
            'price' => 12.90,
        ]);
    }

    private function checkoutData($overrides = [])
    {
        return $overrides + [
            'first_name' => 'Ana',
            'last_name' => 'Petrova',
            'email' => 'ana@example.com',
            'phone' => '0888123456',
            'delivery_method' => 'courier',
            'address' => '1 Main St',
            'city' => 'Sofia',
            'postcode' => '1000',
            'card_name' => 'Ana Petrova',
            'card_number' => '7111 1111 1111 1111',
            'card_expiry' => '12/' . now()->addYear()->format('y'),
            'card_cvc' => '123',
            'terms' => '1',
        ];
    }

    public function test_cart_pages_render()
    {
        $book = $this->book();

        $this->get('/cart')->assertOk();
        $this->get('/checkout')->assertRedirect('/cart');

        $this->post('/cart', ['book_id' => $book->id])->assertRedirect('/cart');

        $this->get('/cart')->assertOk()->assertSee('data-react="Cart"', false);
        $this->get('/checkout')->assertOk()->assertSee('data-react="Checkout"', false);
        $this->get('/b/' . $book->id)->assertOk();
    }

    public function test_quantity_is_capped_at_stock_and_can_be_removed()
    {
        $book = $this->book();

        $this->post('/cart', ['book_id' => $book->id, 'quantity' => 10]);
        $this->assertSame([$book->id => 3], session('cart'));

        $this->patch('/cart/' . $book->id, ['quantity' => 2]);
        $this->assertSame([$book->id => 2], session('cart'));

        $this->delete('/cart/' . $book->id);
        $this->assertSame([], session('cart'));
    }

    public function test_placing_an_order_saves_it_and_takes_the_stock()
    {
        $book = $this->book();
        $this->post('/cart', ['book_id' => $book->id, 'quantity' => 2]);

        $this->post('/checkout', $this->checkoutData())->assertRedirect('/checkout/confirmed');

        $order = Order::with('items')->first();
        $this->assertEquals(25.80, $order->subtotal);
        $this->assertEquals(4.50, $order->delivery_fee);
        $this->assertEquals(30.30, $order->total);
        $this->assertSame(2, $order->items->first()->quantity);
        $this->assertSame(1, $book->fresh()->number);
        $this->assertNull(session('cart'));

        $this->get('/checkout/confirmed')->assertOk()->assertSee('data-react="OrderConfirmed"', false);
    }

    public function test_pickup_needs_no_address_and_is_free()
    {
        $book = $this->book();
        $this->post('/cart', ['book_id' => $book->id]);

        $this->post('/checkout', $this->checkoutData([
            'delivery_method' => 'pickup',
            'address' => '',
            'city' => '',
            'postcode' => '',
        ]))->assertRedirect('/checkout/confirmed');

        $this->assertEquals(0, Order::first()->delivery_fee);
    }

    public function test_checkout_validates_address_and_terms()
    {
        $book = $this->book();
        $this->post('/cart', ['book_id' => $book->id]);

        $this->post('/checkout', $this->checkoutData(['address' => '', 'terms' => null]))
            ->assertSessionHasErrors(['address', 'terms']);

        $this->assertSame(0, Order::count());
    }

    public function test_order_is_paid_by_card_and_keeps_only_the_last_four_digits()
    {
        $book = $this->book();
        $this->post('/cart', ['book_id' => $book->id]);

        $this->post('/checkout', $this->checkoutData(['phone' => '0888123 456']))->assertRedirect('/checkout/confirmed');

        $order = Order::first();
        $this->assertSame(['card_online', '1111', '0888 123 456'], [$order->payment_method, $order->card_last4, $order->phone]);
        $this->assertStringNotContainsString('7111111111111111', json_encode($order->getAttributes()));
    }

    public function test_any_cvc_works_with_the_test_card_but_other_cards_are_declined()
    {
        $book = $this->book();
        $this->post('/cart', ['book_id' => $book->id]);

        $this->post('/checkout', $this->checkoutData(['card_number' => '4242 4242 4242 4242']))
            ->assertSessionHasErrors('card_number')
            ->assertSessionMissing('_old_input.card_number')
            ->assertSessionMissing('_old_input.card_cvc');
        $this->assertSame(0, Order::count());
        $this->assertSame(3, $book->fresh()->number);

        $this->post('/checkout', $this->checkoutData(['card_cvc' => '987']))->assertRedirect('/checkout/confirmed');
    }

    public function test_checkout_validates_bulgarian_details_and_the_card()
    {
        $book = $this->book();
        $this->post('/cart', ['book_id' => $book->id]);

        $this->post('/checkout', $this->checkoutData([
            'first_name' => 'Ana1',
            'phone' => '0888 123 45',
            'postcode' => '10a0',
            'card_number' => '7111 1111',
            'card_expiry' => '01/20',
            'card_cvc' => '12',
        ]))->assertSessionHasErrors(['first_name', 'phone', 'postcode', 'card_number', 'card_expiry', 'card_cvc']);

        // Letters can't sneak into the phone, even past the mask.
        $this->post('/checkout', $this->checkoutData(['phone' => '0888 123 45a']))->assertSessionHasErrors('phone');

        // Cyrillic names are fine.
        $this->post('/checkout', $this->checkoutData(['first_name' => 'Ана', 'city' => 'София']))->assertSessionHasNoErrors();
        $this->assertSame(1, Order::count());
    }

    public function test_order_fails_when_stock_ran_out_meanwhile()
    {
        $book = $this->book();
        $this->post('/cart', ['book_id' => $book->id, 'quantity' => 2]);
        $book->update(['number' => 1]);

        $this->post('/checkout', $this->checkoutData())->assertSessionHasErrors('cart');

        $this->assertSame(0, Order::count());
        $this->assertSame(1, $book->fresh()->number);
    }
}
