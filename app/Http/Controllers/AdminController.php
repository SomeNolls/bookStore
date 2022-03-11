<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\RentedBooks;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class AdminController extends Controller
{
    public function index($user)
    {
        $user = User::findOrFail($user);
        $books = Book::orderBy('created_at', 'desc')->get();
        $rentedBooks = RentedBooks::orderBy('created_at', 'desc')->get();
        if ($user == Auth::user() && $user->admin == 1)
        {
            return view('profile/admin', ['user' => $user, 'books'=>$books]);
        }

        $user = User::find(auth()->user()->id);
        return view('profile/profile', ['user' => $user,'rentedBooks' =>$rentedBooks, 'books'=>$books]);


    }
    public function delete($id)
    {

        $data = Book::find($id);
        $data->delete();

        return redirect("/admin/".auth()->user()->id);
    }

    public function edit($id)
    {

       $data = Book::find($id);
       return view('books/editBook',['data' => $data]);
    }

    public function update(Request $req)
    {

        $data = Book::find($req->id);

        $data->title = $req->title;
        $data->description = $req->description;
        if ($req->img != null)
        {
            $imagePath = request('img')->store('uploads', 'public');

            $image = Image::make(public_path("storage/{$imagePath}"))->fit(230, 230);
            $image->save();
            $data->img = $imagePath;
        }


        $difference = $req->number - $data->stock;

        $data->number += $difference;
        $data->stock = $req->number;

        $data->save();

        $books = Book::orderBy('created_at', 'desc')->get();
        return view('profile/admin', ['user' => auth()->user(), 'books'=>$books]);


    }
}
