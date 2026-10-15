<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <script src="{{ asset('js/app.js') }}" defer></script>
    </head>
    <body>

        @include('partials.header')


        <!-- -->

        <div class="container ">
            <div class="row justify-content-center">
                <div class="col-md-10 bg-white">
                    <div class="row justify-content-center">
                        <div class="col-md-8" >
                            <br><br>
                            <img src="/images/Logo.png" ref="logo">
                            <nav class="navbar navbar-expand-md  navbar-light  shadow-sm welcome_links" >
                                <ul class="navbar-nav" >
                                    <li >
                                        <a href="{{url('/books')}}" class="nav-link" ><h2>Books</h2></a>
                                    </li>
                                    <li >
                                        <a href="#" class="nav-link "><h2>About us</h2></a>
                                    </li>
                                    <li >
                                        <a href="#" class="nav-link "><h2>Contacts</h2></a>
                                    </li>
                                    @auth
                                        <li >
                                            <a class="nav-link" href="/profile/{{Auth::user()->id }}">
                                               <h2>Profile</h2></a>
                                        </li>
                                    @endauth
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="js-container"></div>

        <script src="{{ mix('js/welcome.js') }}"></script>
    </body>
</html>
