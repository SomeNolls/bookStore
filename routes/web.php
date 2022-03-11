<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\BookControler;
use \App\Http\Controllers\RentController;
use \App\Http\Controllers\ProfileController;
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
    return view('welcome');
});

Auth::routes();

Route::get('/returnBook/{id}', [ProfileController::class, 'returnBook']);
Route::get('/profile/{user}', [ProfileController::class, 'index'])->name('profile.show');

Route::post('/edit', [\App\Http\Controllers\AdminController::class, 'update']);
Route::get('/admin/{user}', [\App\Http\Controllers\AdminController::class, 'index']);
Route::get('/delete/{user}', [\App\Http\Controllers\AdminController::class, 'delete']);
Route::get('/edit/{user}', [\App\Http\Controllers\AdminController::class, 'edit']);


Route::post('/rent', [RentController::class, 'store']);

Route::get('/books', [BookControler::class, 'show']);
Route::get('/b/create', [BookControler::class, 'createBook']);
Route::get('/b/{book}', [BookControler::class, 'showBook']);
Route::post('/b', [BookControler::class, 'store']);
