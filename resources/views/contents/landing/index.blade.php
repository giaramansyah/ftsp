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
  <body class="hold-transition login-page text-sm bg-login">
    <div class="login-box">
      <div class="row h-100">
        <div class="col-sm-7 text-center">
          <img class="img-block" src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }}" height="64">
          <h6 class="mb-4">UNIVERSITAS TRISAKTI</h6>
          <h6 class="title-login font-weight-light">MONITORING KEUANGAN</h6>
          <h6 class="subtitle-login font-weight-bold">FTSP USAKTI</h6>
        </div>
        <div class="col-sm-5">
          <div class="card card-login mb-3">
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
                      <button type="submit" name="auth" value="1" class="btn btn-dark btn-block">{{ __('Login') }}</button>
                    </div>
                    <div class="form-loading">
                      <img src="{{ asset('img/loading.gif') }}" height="40">
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <p class="text-center font-weight-light">&copy;2022mokuftsp</p>
        </div>
      </div>
    </div>
    <script type="text/javascript" src="{{asset('js/app.js')}}"></script>
  </body>
</html>