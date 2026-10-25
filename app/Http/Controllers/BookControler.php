<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;


class BookControler extends Controller
{
    public function show()
    {
        $filters = [
            'search' => trim((string) request('search', '')),
            'genre' => (string) request('genre', ''),
            'min' => is_numeric(request('min')) ? request('min') : '',
            'max' => is_numeric(request('max')) ? request('max') : '',
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
