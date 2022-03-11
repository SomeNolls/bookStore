@extends('layouts.app')

@section('content')
    <div class="container ">
        <div class="row justify-content-center">
            <div class="col-md-10 bg-white ">

                <div class="row m-5">
                    <h3>Books:</h3>
                </div>

                <div class="row m-2 mb-5">
                    @foreach($books as $book)
                        <div class="col-3 pb-4">
                            <a href="b/{{$book->id}}">
                                <img src="/storage/{{$book['img']}}" class="w-100">
                            </a>
                        </div>

                    @endforeach

                </div>
        </div>


    </div>
@endsection
