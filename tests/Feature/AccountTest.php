<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Order;
use App\Models\RentedBooks;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    private function user($attributes = [])
    {
        return User::create($attributes + [
            'name' => 'Ana Petrova',
            'email' => 'ana@example.com',
            'password' => Hash::make('secret123'),
        ]);
    }

    private function book()
    {
        return Book::create([
            'title' => 'Dune',
            'description' => 'Sand.',
            'img' => 'uploads/dune.jpg',
            'number' => 2,
            'stock' => 2,
            'price' => 15,
        ]);
    }

    public function test_auth_pages_render_with_react()
    {
        $this->get('/login')->assertOk()->assertSee('data-react="Login"', false);
        $this->get('/register')->assertOk()->assertSee('data-react="Register"', false);
    }

    public function test_user_can_sign_in()
    {
        $user = $this->user();

        $this->post('/login', ['email' => 'ana@example.com', 'password' => 'secret123'])->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_register_requires_a_number_in_the_password_and_the_terms()
    {
        $this->post('/register', [
            'name' => 'Ana Petrova',
            'email' => 'new@example.com',
            'password' => 'nonumbers',
            'password_confirmation' => 'nonumbers',
        ])->assertSessionHasErrors(['password', 'terms']);

        $this->post('/register', [
            'name' => ' Ana Petrova ',
            'email' => 'new@example.com',
            'password' => 'reader2026',
            'password_confirmation' => 'reader2026',
            'terms' => '1',
        ])->assertRedirect('/');

        $this->assertSame('Ana Petrova', User::where('email', 'new@example.com')->value('name'));
    }

    public function test_account_page_shows_only_my_orders_and_rentals()
    {
        $me = $this->user();
        $other = $this->user(['email' => 'other@example.com']);
        $book = $this->book();

        foreach ([$me, $other] as $owner) {
            Order::create([
                'user_id' => $owner->id, 'first_name' => 'A', 'last_name' => 'B', 'email' => $owner->email,
                'phone' => '1', 'delivery_method' => 'pickup', 'payment_method' => 'cash',
                'subtotal' => 10, 'delivery_fee' => 0, 'total' => 10,
            ]);
            RentedBooks::create(['user_id' => $owner->id, 'book_id' => $book->id, 'returnDate' => now()->addMonth()]);
        }

        $response = $this->actingAs($me)->get('/profile/' . $me->id . '?tab=rentals')->assertOk();

        $this->assertSame([$me->id], $response->viewData('orders')->pluck('user_id')->all());
        $this->assertSame([$me->id], $response->viewData('rentals')->pluck('user_id')->all());
        $this->assertSame('rentals', $response->viewData('tab'));
    }

    public function test_profile_changes_only_the_email()
    {
        $user = $this->user();

        $this->actingAs($user)->put('/profile', [
            'first_name' => 'Maria',
            'last_name' => 'Ivanova',
            'email' => 'ana.i@example.com',
        ])->assertRedirect('/profile/' . $user->id . '?tab=profile');

        $user->refresh();
        $this->assertSame('Ana Petrova', $user->name);
        $this->assertSame('ana.i@example.com', $user->email);
    }

    public function test_email_must_stay_unique()
    {
        $user = $this->user();
        $this->user(['email' => 'taken@example.com']);

        $this->actingAs($user)->put('/profile', ['email' => 'taken@example.com'])
            ->assertSessionHasErrors('email');
    }

    public function test_password_change_needs_the_current_password()
    {
        $user = $this->user();

        $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'wrong',
            'password' => 'newpass123',
            'password_confirmation' => 'newpass123',
        ])->assertSessionHasErrors('current_password');

        $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'secret123',
            'password' => 'newpass123',
            'password_confirmation' => 'newpass123',
        ])->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('newpass123', $user->fresh()->password));
    }

    public function test_cannot_return_someone_elses_rental()
    {
        $me = $this->user();
        $other = $this->user(['email' => 'other@example.com']);
        $book = $this->book();
        $rental = RentedBooks::create(['user_id' => $other->id, 'book_id' => $book->id, 'returnDate' => now()->addMonth()]);

        $this->actingAs($me)->post('/returnBook/' . $rental->id)->assertNotFound();
        $this->assertNotNull($rental->fresh());

        $this->actingAs($other)->post('/returnBook/' . $rental->id);
        $this->assertNull($rental->fresh());
        $this->assertSame(1, $book->fresh()->forRent);
    }

    public function test_checkout_prefills_only_the_name_and_email()
    {
        $user = $this->user();
        $book = $this->book();

        $this->actingAs($user)->post('/cart', ['book_id' => $book->id]);

        $this->get('/checkout')->assertOk()->assertSee($user->email);
        $this->assertFalse(Schema::hasColumn('users', 'phone'));
        $this->assertFalse(Schema::hasColumn('users', 'address'));
    }
}
