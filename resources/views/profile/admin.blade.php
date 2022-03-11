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
                                <td class="w-25" style="border-right: 1px solid #1c1f23; padding-left: 6px">Title</td>
                                <td class="w-25" style="border-right: 1px solid #1c1f23; padding-left: 6px">Current stock</td>
                                <td class="w-25" style="border-right: 1px solid #1c1f23; padding-left: 6px">All stock</td>
                                <td style="border-right: 1px solid #1c1f23; padding-left: 6px">Delete book</td>
                                <td style=" padding-left: 6px">Edit book</td>
                            </tr>


                            @foreach($books as $book)

                                <tr class="border border-dark border-1 col-6 ">
                                    <td class="w-25" style="border-right: 1px solid #1c1f23; padding-left: 6px">{{$book->title}}</td>
                                    <td class="w-25" style="border-right: 1px solid #1c1f23; padding-left: 6px">{{$book->number}}</td>
                                    <td class="w-25" style="border-right: 1px solid #1c1f23; padding-left: 6px">{{$book->stock}}</td>
                                    <td style="border-right: 1px solid #1c1f23; padding-left: 6px"><a href="{{'/delete/'.$book['id']}}" class="btn btn-dark btn-primary">Delete</a></td>
                                    <td style="padding-left: 6px"><a href="{{'/edit/'.$book['id']}}" class="btn btn-dark btn-primary">Edit</a></td>
                                </tr>

                            @endforeach

                        </table>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
