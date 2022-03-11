<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\RentedBooks;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($user)
    {
        $user = User::findOrFail($user);
        $rentedBooks = RentedBooks::orderBy('created_at', 'desc')->get();
        $books = Book::orderBy('created_at', 'desc')->get();



        if ($user == Auth::user())
        {
            return view('profile/profile', ['user' => $user,'rentedBooks' =>$rentedBooks, 'books'=>$books]);
        }

        $user = User::find(auth()->user()->id);
        return view('profile/profile', ['user' => $user,'rentedBooks' =>$rentedBooks, 'books'=>$books]);


    }
    public function returnBook($id)
    {

        $data = RentedBooks::find($id);

        Book::find($data['book_id'])->increment('number');

        $data->delete();

        return redirect("/profile/".auth()->user()->id);
    }

}
