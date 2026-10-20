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
        $filters = [
            'search' => trim((string) request('search', '')),
            'genre' => (string) request('genre', ''),
            'min' => is_numeric(request('min')) ? request('min') : '',
            'max' => is_numeric(request('max')) ? request('max') : '',
            'inStock' => request()->boolean('inStock'),
            'sort' => array_key_exists(request('sort'), self::SORTS) ? request('sort') : 'newest',
        ];

        [$sortColumn, $sortDirection] = self::SORTS[$filters['sort']];

        $books = Book::query()
            ->when($filters['search'], function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', '%'.$search.'%')
                        ->orWhere('author', 'like', '%'.$search.'%');
                });
            })
            ->when($filters['genre'], fn ($query, $genre) => $query->where('genre', $genre))
            ->when($filters['min'] !== '', fn ($query) => $query->where('price', '>=', $filters['min']))
            ->when($filters['max'] !== '', fn ($query) => $query->where('price', '<=', $filters['max']))
            ->when($filters['inStock'], fn ($query) => $query->where('number', '>', 0))
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(12)
            ->withQueryString();

        return view('books/books', [
            'books' => $books,
            'filters' => $filters,
            'genres' => Book::genres(),
        ]);
    }

    public function showBook(\App\Models\Book $book)
    {
        $similar = Book::where('id', '!=', $book->id)
            ->when($book->genre, fn ($query, $genre) => $query->where('genre', $genre))
            ->latest()
            ->take(4)
            ->get();

        return view('books/show', compact('book', 'similar'));
    }

    // Catalogue sort options: key => [column, direction]
    private const SORTS = [
        'newest' => ['created_at', 'desc'],
        'price_asc' => ['price', 'asc'],
        'price_desc' => ['price', 'desc'],
        'title' => ['title', 'asc'],
    ];


}
