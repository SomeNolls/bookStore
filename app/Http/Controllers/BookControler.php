<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
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
            'author' => 'required',
            'genre' => 'required',
            'description' => 'required',
            'stock' => ['required', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'forRent' => ['required', 'integer', 'min:0'],
            'eBook' => ['required', 'boolean'],
            'img' => ['required', 'image']
        ]);

        $imagePath = request('img')->store('uploads', 'public');

        $image = Image::make(Storage::disk('public')->path($imagePath));
        $image->save();

        \App\Models\Book::create([
            'title' => $data['title'],
            'author' => $data['author'],
            'genre' => $data['genre'],
            'description' => $data['description'],
            'number' => $data['stock'],
            'stock' => $data['stock'],
            'price' => $data['price'],
            'forRent' => $data['forRent'],
            'eBook' => $data['eBook'],
            'img' =>$imagePath
        ]);

        return redirect('/admin/'.auth()->user()->id);
    }

    public function show()
    {
        $data = Book::orderBy('created_at', 'desc')
            ->when(request('search'), function ($query, $search) {
                $query->where('title', 'like', '%'.$search.'%');
            })
            ->get();
        return view('books/books', [ 'books'=>$data]);
    }

    public function showBook(\App\Models\Book $book)
    {
        return view('books/show', compact('book'));
    }


}
