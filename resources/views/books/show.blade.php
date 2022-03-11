@extends('layouts.app')

@section('content')
    <div class="container bg-white">
        <div class="row justify-content-center">
            <div class="col-md-10  ">

                <div class="row pt-5">
                    <div class="col-6 ">

                        <img src="/storage/{{$book->img}}"  style="width: 80%">

                    </div>
                    <div class="col-6">
                        <div class="row border-bottom border-1 border-dark">
                            <h3>{{$book->title}}</h3>
                        </div>
                        <div class="row mt-2 p-4 pb-0">
                            @if($book->number == 0)
                               <h3>Out of stock!</h3>
                            @else
                                {{$book->number}} left
                            @endif



                        </div>

                        <!-- Rent form start -->
                        @if($book->number != 0)
                        <div class="row pt-4 ">
                            <form method="post" action="/rent" enctype="multipart/form-data">
                                <div class="border border-1 border-dark p-4">
                                    @csrf
                                    <div class="row">
                                        <h5>Rent for:</h5>
                                    </div>

                                    <!-- Radio buttons start -->

                                    <div class="row mb-3">
                                        <div class="row align-content-right">
                                            <div class="col-6 ">
                                                <input type="radio" id="1" name="returnDate" value="1">
                                                <label for="returnDate" class="col-form-label text-end"><h5>1 month</h5></label>
                                            </div>
                                        </div>

                                        <div class="row align-content-right">
                                            <div class="col-6">
                                                <input type="radio" id="2" name="returnDate" value="2">
                                                <label for="returnDate" class="col-form-label text-end"><h5>2 month</h5></label>
                                            </div>
                                        </div>

                                        <div class="row align-content-right">
                                            <div class="col-6">
                                                <input type="radio" id="3" name="returnDate" value="3">
                                                <label for="returnDate" class="col-form-label text-end"><h5>3 month</h5></label>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <input type="hidden" id="book_id" name="book_id" value="{{$book->id}}">

                                <!-- Radio buttons end -->

                                <div class="row mb-0 mt-4">
                                    <div class="col">
                                        <button type="submit" class="btn btn-primary " style="width: 40%">
                                            {{ __('Rent') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @endif

                        <!-- Rent form end -->

                    </div>
                    <div class="row justify-content-center pt-5 pb-5">
                        <div class="col-md-10">
                            <h4 class="border-bottom border-dark border-1">Description:</h4>
                            <p class=" p-4">{{$book->description}}</p>

                        </div>
                    </div>
                </div>


            </div>


        </div>
@endsection
