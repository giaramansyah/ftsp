<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} | Login</title>
    <link rel="shortcut icon" href="favicon.ico" />
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
  </head>
  <body class="hold-transition login-page text-sm">
    <div class="login-box">
      <div class="card card-outline card-primary">
        <div class="card-header text-center">
          <h1 class="h1">{{ config('app.name') }}</h1>
        </div>
        <div class="card-body">
          <p class="login-box-msg">{{ __('Sign in to start your session') }}</p>
          <form class="form-lazy-control" data-action="{{ route('auth') }}">
            <div class="alert hidden" role="alert"></div>
            <div class="input-group mb-3">
              <input type="text" name="username" class="form-control" placeholder="{{ __('Username') }}" required>
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-envelope"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input type="password" name="password" class="form-control" placeholder="{{ __('Password') }}" required>
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-sm-6">
                <div class="icheck-primary">
                  <input type="checkbox" id="remember" name="remember" value="1">
                  <label class="mb-0" for="remember">{{ __('Remember Me') }}</label>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-button">
                  <button type="submit" name="auth" value="1" class="btn btn-primary btn-block">{{ __('Login') }}</button>
                </div>
                <div class="form-loading">
                  <img src="{{ asset('img/loading.gif') }}" height="40">
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <script type="text/javascript" src="{{asset('js/app.js')}}"></script>
  </body>
</html>