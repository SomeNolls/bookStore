<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private function user($admin = true)
    {
        $user = User::create([
            'name' => 'Ana Petrova',
            'email' => ($admin ? 'admin' : 'reader') . '@example.com',
            'password' => Hash::make('secret123'),
        ]);
        $user->forceFill(['admin' => $admin ? '1' : '0'])->save();

        return $user;
    }

    private function book()
    {
        return Book::create([
            'title' => 'Dune',
            'author' => 'Frank Herbert',
            'genre' => 'Fiction',
            'description' => 'Sand.',
            'img' => 'uploads/dune.jpg',
            'number' => 3,
            'stock' => 5,
            'price' => 15,
        ]);
    }

    private function bookData($overrides = [])
    {
        return $overrides + [
            'title' => 'Emma',
            'author' => 'Jane Austen',
            'genre' => 'Classics',
            'description' => 'Matchmaking.',
            'stock' => 4,
            'price' => 9.5,
            'forRent' => 1,
            'rentPrice' => 2.5,
        ];
    }

    private function order()
    {
        return Order::create([
            'first_name' => 'A', 'last_name' => 'B', 'email' => 'a@example.com',
            'phone' => '1', 'delivery_method' => 'pickup', 'payment_method' => 'cash',
            'subtotal' => 10, 'delivery_fee' => 0, 'total' => 10,
        ]);
    }

    public function test_panel_renders_each_tab_for_admins()
    {
        $admin = $this->user();
        $this->book();
        $this->order();

        $this->actingAs($admin)->get('/admin/' . $admin->id)->assertOk()
            ->assertSee('data-react="Admin"', false)
            ->assertViewHas('tab', 'books');
        $this->actingAs($admin)->get('/admin/' . $admin->id . '?tab=orders')->assertViewHas('tab', 'orders');
        $this->actingAs($admin)->get('/admin/' . $admin->id . '?tab=nope')->assertViewHas('tab', 'books');
    }

    public function test_non_admins_are_kept_out()
    {
        $reader = $this->user(false);
        $book = $this->book();

        $this->get('/admin/1')->assertRedirect('/login');
        $this->actingAs($reader)->get('/admin/' . $reader->id)->assertRedirect(route('profile.show', $reader->id));
        $this->actingAs($reader)->delete('/admin/books/' . $book->id);
        $this->actingAs($reader)->post('/b', $this->bookData(['img' => UploadedFile::fake()->image('emma.jpg')]));

        $this->assertDatabaseHas('books', ['id' => $book->id]);
        $this->assertDatabaseMissing('books', ['title' => 'Emma']);
    }

    public function test_admin_can_add_a_book()
    {
        Storage::fake('public');
        $admin = $this->user();

        $this->actingAs($admin)->post('/b', $this->bookData(['img' => UploadedFile::fake()->image('emma.jpg')]))
            ->assertRedirect(route('admin', ['user' => $admin->id, 'tab' => 'books']));

        $book = Book::where('title', 'Emma')->firstOrFail();
        $this->assertSame([4, 4], [(int) $book->number, (int) $book->stock]);
        $this->assertEquals(2.5, $book->rentPrice);
        Storage::disk('public')->assertExists($book->img);

        $this->actingAs($admin)->post('/b', $this->bookData(['title' => '']))->assertSessionHasErrors(['title', 'img']);
    }

    public function test_admin_can_edit_a_book_and_stock_follows()
    {
        $admin = $this->user();
        $book = $this->book();

        $this->actingAs($admin)->get('/edit/' . $book->id)->assertOk()->assertViewHas('editing');

        // 2 of the 5 copies are out, so raising the stock to 7 leaves 5 available.
        $this->actingAs($admin)->put('/admin/books/' . $book->id, $this->bookData(['title' => 'Dune Messiah', 'stock' => 7]))
            ->assertSessionHasNoErrors();

        $book->refresh();
        $this->assertSame('Dune Messiah', $book->title);
        $this->assertSame('uploads/dune.jpg', $book->img);
        $this->assertSame([5, 7], [(int) $book->number, (int) $book->stock]);
    }

    public function test_admin_can_delete_a_book()
    {
        $admin = $this->user();
        $book = $this->book();

        $this->actingAs($admin)->delete('/admin/books/' . $book->id)->assertRedirect();

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    public function test_admin_can_change_an_order_status()
    {
        $admin = $this->user();
        $order = $this->order();
        $this->assertSame('pending', $order->fresh()->status);

        $this->actingAs($admin)->patch('/admin/orders/' . $order->id, ['status' => 'shipped'])
            ->assertRedirect(route('admin', ['user' => $admin->id, 'tab' => 'orders']));
        $this->assertSame('shipped', $order->fresh()->status);

        $this->actingAs($admin)->patch('/admin/orders/' . $order->id, ['status' => 'lost'])->assertSessionHasErrors('status');
        $this->assertSame('shipped', $order->fresh()->status);
    }
}
