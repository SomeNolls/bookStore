<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    /**
     * Genres offered in the genre lists, before any extra ones found in the books table.
     */
    public const GENRES = ['Fiction', 'Classics', 'Fantasy', 'Mystery & Thriller', 'Science', 'History', 'Children'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    public function rentedBy()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Every genre to offer as a filter: the default list plus any other genre a book uses.
     */
    public static function genres()
    {
        return collect(self::GENRES)
            ->merge(self::whereNotNull('genre')->distinct()->pluck('genre'))
            ->unique()
            ->values();
    }

    /**
     * The props a BookCard (resources/js/components/BookList.jsx) needs.
     */
    public function toCard()
    {
        return [
            'title' => $this->title,
            'author' => $this->author,
            'price' => (float) $this->price,
            'img' => $this->img ? asset('storage/' . $this->img) : null,
            'url' => url('/b/' . $this->id),
            'available' => (int) $this->number,
        ];
    }
}
