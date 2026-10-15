@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">


                    <div class="row m-5 border-bottom border-1 border-dark pb-4">
                        <div class="col-6">
                            <h3>Database:</h3>
                        </div>

                        <div class="align-content-right col-6">
                            <a class="  btn btn-primary" href="/b/create">Add book</a>
                        </div>
                    </div>



                    <div class="row ">
                        <table class="m-5 mt-0 card-body">
                            <tr class="border border-dark border-1 col-6 ">
                                <td class="w-25">Title</td>
                                <td class="w-25">Current stock</td>
                                <td class="w-25">All stock</td>
                                <td>Delete book</td>
                                <td>Edit book</td>
                            </tr>


                            @foreach($books as $book)

                                <tr class="border border-dark border-1 col-6 ">
                                    <td class="w-25">{{$book->title}}</td>
                                    <td class="w-25">{{$book->number}}</td>
                                    <td class="w-25">{{$book->stock}}</td>
                                    <td><a href="{{'/delete/'.$book['id']}}" class="btn btn-dark btn-primary">Delete</a></td>
                                    <td><a href="{{'/edit/'.$book['id']}}" class="btn btn-dark btn-primary">Edit</a></td>
                                </tr>

                            @endforeach

                        </table>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
