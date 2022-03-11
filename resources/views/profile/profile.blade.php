
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
                            <td class="w-25" style="border-right: 1px solid #1c1f23; padding-left: 6px">Title</td>
                            <td class="w-25" style="border-right: 1px solid #1c1f23; padding-left: 6px">Rented date</td>
                            <td style="border-right: 1px solid #1c1f23; padding-left: 6px">Return date</td>
                            <td style="border-right: 1px solid #1c1f23; padding-left: 6px;  width: 10%">Remaining time</td>
                            <td style=" padding-left: 6px">Return book</td>
                        </tr>

                        @foreach($rentedBooks as $rentedBook)
                            @foreach($books as $book)
                                @if($rentedBook->book_id == $book->id)
                                    <tr class="border border-dark border-1 col-6 ">

                                        <td class="w-25" style="border-right: 1px solid #1c1f23; padding-left: 6px">{{$book->title}}</td>
                                        <td class="w-25" style="border-right: 1px solid #1c1f23; padding-left: 6px">{{$rentedBook->created_at}}</td>
                                        <td class="w-25" style="border-right: 1px solid #1c1f23; padding-left: 6px">{{$rentedBook->returnDate}}</td>
                                        <td style="border-right: 1px solid #1c1f23; padding-left: 6px; width: 10%">{{ \Carbon\Carbon::parse($rentedBook->created_at)->diffForHumans($rentedBook->returnDate)}}</td>
                                        <td class="w-25" style="padding-left: 6px"><a href="{{'/returnBook/'.$rentedBook['id']}}" class="btn btn-dark btn-primary">Return</a></td>

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
