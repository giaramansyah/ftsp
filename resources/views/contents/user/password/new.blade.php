<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} | New Password</title>
    <link rel="shortcut icon" href="{{ asset('img/AdminLTELogo.png') }}" />
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
  </head>
  <body class="hold-transition login-page text-sm">
    <div class="login-box">
      <div class="card card-outline card-primary">
        <div class="card-header text-center">
          <h1 class="h1">{{ __('New Password') }}</h1>
        </div>
        <div class="card-body">
          <p class="login-box-msg">{{ __('Create new password for login') }}</p>
          <form class="form-lazy-control" data-action="{{ $action }}" data-validate="password">
            <div class="alert hidden" role="alert"></div>
            <div class="form-group">
              <label>{{ __('Password') }}</label>
              <input type="password" name="new_password" id="new_password" class="form-control" placeholder="{{ __('Password') }}" required>
            </div>
            <div class="form-group">
              <label>{{ __('Confirm Password') }}</label>
              <input type="password" name="confirm_password" class="form-control" placeholder="{{ __('Re-Type Password') }}" required>
            </div>
            <div class="form-button">
              @include('partials.button.submit')
            </div>
            <div class="form-loading">
              <img src="{{ asset('img/loading.gif') }}" height="40">
            </div>
          </form>
        </div>
      </div>
    </div>
    <script type="text/javascript" src="{{asset('js/app.js')}}"></script>
  </body>
</html>