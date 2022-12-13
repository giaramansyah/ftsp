<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>{{ config("app.name") }} | @yield('title')</title>
  <link rel="shortcut icon" href="{{ asset('img/AdminLTELogo.png') }}" />
  <link rel="stylesheet" href="{{asset('css/app.css')}}">
  @yield('push-css')
  <script type="text/javascript">
    window.history.forward();
    function noBack() {
      window.history.forward();
    }
  </script>
</head>

<body class="layout-fixed sidebar-mini text-sm">
  <div class="wrapper">
    @include('layouts.nav-top')
    @include('layouts.nav-side')
    <div class="content-wrapper">
      <div class="content-header">
        @include('layouts.header')
      </div>
      <section class="content">
        @yield('content')
      </section>
    </div>
    {{-- @include('layouts.footer') --}}
  </div>
  <script type="text/javascript" src="{{asset('js/app.js')}}"></script>
  @yield('push-js')
</body>

</html>