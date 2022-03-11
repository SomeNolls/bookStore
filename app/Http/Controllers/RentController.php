<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\RentedBooks;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
Use \Carbon\Carbon;

class RentController extends Controller
{
    public function store()
    {


        $data = request()->validate([
            'returnDate' => 'required',
            'book_id' => 'required'
        ]);

        $user_id = auth()->user()->id;

        Book::find($data['book_id'])->decrement('number');

        $returnDate = Carbon::now();
        $returnDate->addMonths($data['returnDate']);


        RentedBooks::create([
            'returnDate' => $returnDate,
            'user_id' => $user_id,
            'book_id' => $data['book_id']

        ]);



        return redirect('/books');
    }


}
