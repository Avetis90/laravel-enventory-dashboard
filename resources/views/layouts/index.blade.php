<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'Welcome')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">    
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="{{ mix('js/app.js') }}"></script>
    @yield('headerscripts')
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    @include('includes.header')
    @include('includes.side-menu')
    <div class="content-wrapper">
        {{--<section class="content-header">--}}
        {{--<ol class="breadcrumb">--}}
        {{--<li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>--}}
        {{--<li class="active">Here</li>--}}
        {{--</ol>--}}
        {{--</section>--}}
        @if(Session::has('error') || Session::has('success') || Session::has('warning') || Session::has('message'))
            <section class="notification box-message">
                <div class="info-box">
                    <div class="info-box-icon {{Session::has('error') ? 'bg-red' : (Session::has('warning') ? 'bg-yellow' : (Session::has('success') ? 'bg-green': 'bg-aqua'))  }}">
                        <i class="fa fa-envelope-o"></i>
                    </div>
                    <div class="info-box-content info-box-content-center">
                        {{ Session::get('error') }}
                        {{ Session::get('success') }}
                        {{ Session::get('warning') }}
                        {{ Session::get('message')}}
                    </div>
                    <div class="action-container">
                        <div class="close">
                            <i class="fa fa-times" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </section>
        @endif
        <section class="content">
            @yield('content')
        </section>

    </div>
    @include('includes.footer')
</div>
</body>