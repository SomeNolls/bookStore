<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\BookControler;
use \App\Http\Controllers\ProfileController;
use \App\Http\Controllers\CartController;
use \App\Http\Controllers\CheckoutController;
use \App\Http\Controllers\AdminController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome', [
        'latestBooks' => \App\Models\Book::latest()->take(5)->get(),
    ]);
});

Auth::routes();

Route::post('/returnBook/{id}', [ProfileController::class, 'returnBook'])->name('rentals.return');
Route::get('/profile/{user}', [ProfileController::class, 'index'])->name('profile.show');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

Route::get('/admin/{user}', [AdminController::class, 'index'])->name('admin');
Route::get('/edit/{book}', [AdminController::class, 'edit'])->name('admin.books.edit');
Route::put('/admin/books/{book}', [AdminController::class, 'update'])->name('admin.books.update');
Route::delete('/admin/books/{book}', [AdminController::class, 'destroy'])->name('admin.books.destroy');
Route::patch('/admin/orders/{order}', [AdminController::class, 'updateOrder'])->name('admin.orders.update');
Route::post('/admin/rentals/{rental}/return', [AdminController::class, 'returnRental'])->name('admin.rentals.return');


// Renting goes through the cart and checkout like buying.
Route::post('/rent', [CartController::class, 'storeRental'])->middleware('auth')->name('cart.rent');

Route::get('/books', [BookControler::class, 'show']);
// Books are added from the admin panel's "Add a book" tab.
Route::get('/b/create', fn () => redirect()->route('admin', ['user' => auth()->id() ?? 0, 'tab' => 'add']));
Route::get('/b/{book}', [BookControler::class, 'showBook']);
Route::post('/b', [AdminController::class, 'store'])->name('admin.books.store');

Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{book}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{book}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::delete('/cart/rentals/{book}', [CartController::class, 'destroyRental'])->name('cart.rentals.destroy');

Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/confirmed', [CheckoutController::class, 'confirmed'])->name('checkout.confirmed');
