<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    @stack('styles')

    <script src="{{asset('js/bootstrap.bundle.js')}}"></script>
    <script src="{{asset('js/toggleTheme.js')}}"></script>

    <title> {{config('app.company.name')}}@hasSection('title')
            | @yield('title')
        @endif</title>
</head>
<body>
@auth()
    <div class="d-flex">
        @include('partials.sidebar')
        <div class="main-content">
            @include('partials.navbar')
            <div class="page-content">
                @yield('content')
            </div>

        </div>
    </div>
@endauth
@guest()
    <div class="guest-content">
        @yield('content')
    </div>
@endguest

@include('partials.footer')
</body>
</html>
