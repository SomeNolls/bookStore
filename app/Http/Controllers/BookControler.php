<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\App;
use Intervention\Image\Facades\Image;


class BookControler extends Controller
{
    public function createBook()
    {
        return view('books/createBook');
    }

    public function store()
    {
        $data = request()->validate([
            'title' => 'required',
            'description' => 'required',
            'number' => 'required',
            'img' => ['required', 'image']
        ]);

        $imagePath = request('img')->store('uploads', 'public');

        $image = Image::make(public_path("storage/{$imagePath}"))->fit(230, 230);
        $image->save();

        \App\Models\Book::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'number' => $data['number'],
            'stock' => $data['number'],
            'img' =>$imagePath
        ]);

        return redirect('/admin/'.auth()->user()->id);
    }

    public function show()
    {
        $data = Book::orderBy('created_at', 'desc')->get();
        return view('books/books', [ 'books'=>$data]);
    }

    public function showBook(\App\Models\Book $book)
    {
        return view('books/show', compact('book'));
    }


}
