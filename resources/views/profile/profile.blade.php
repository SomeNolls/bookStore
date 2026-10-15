
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">


                <div class="row m-5 border-bottom border-1 border-dark pb-4">
                    <h3>My books:</h3>
                </div>

                <div class="row ">
                    <table class="m-5 mt-0 card-body">
                        <tr class="border border-dark border-1 col-6 ">
                            <td class="w-25">Title</td>
                            <td class="w-25">Rented date</td>
                            <td>Return date</td>
                            <td>Remaining time</td>
                            <td>Return book</td>
                        </tr>

                        @foreach($rentedBooks as $rentedBook)
                            @foreach($books as $book)
                                @if($rentedBook->book_id == $book->id)
                                    <tr class="border border-dark border-1 col-6 ">

                                        <td class="w-25">{{$book->title}}</td>
                                        <td class="w-25">{{$rentedBook->created_at}}</td>
                                        <td class="w-25">{{$rentedBook->returnDate}}</td>
                                        <td>{{ \Carbon\Carbon::parse($rentedBook->created_at)->diffForHumans($rentedBook->returnDate)}}</td>
                                        <td class="w-25"><a href="{{'/returnBook/'.$rentedBook['id']}}" class="btn btn-dark btn-primary">Return</a></td>

                                    </tr>
                                @endif
                            @endforeach
                        @endforeach
                    </table>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection
