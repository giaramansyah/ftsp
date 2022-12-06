@extends('layouts/main')
@section('title', 'Privilege')
@section('content')
<div class="container-fluid">
  <div class="card">
    <form class="form-lazy-control" data-action="{{ $action }}">
      <div class="card-body">
        <div class="alert hidden" role="alert"></div>
        <div class="form-group row">
          <label class="col-sm-1 col-form-label">{{ __('Code') }}<code>*</code></label>
          <div class="col-sm-1">
            <input type="text" class="form-control form-control-sm text-uppercase" maxlength="4" name="code" value="{{ isset($code) ? $code : '' }}" {{ isset($code) ? 'readonly' : '' }} required>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-1 col-form-label">{{ __('Menu') }}<code>*</code></label>
          <div class="col-sm-2">
            <select class="form-control form-control-sm select2" name="menu_id" required>
              <option value="">-- {{ __('Please Select') }} --</option>
              @foreach ($menuArr as $value)
                <optgroup label="{{ __($value['label']) }}">
                  @foreach ($value['menu'] as $val)
                    <option value="{{ $val['id'] }}" {{ isset($menu_id) && $menu_id == $val['id'] ? 'selected' : '' }}>{{ __($val['label']) }}</option>
                  @endforeach
                </optgroup>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-1 col-form-label">{{ __('Modules') }}<code>*</code></label>
          <div class="col-sm-2">
            <select class="form-control form-control-sm select2" name="modules" required>
              <option value="">-- {{ __('Please Select') }} --</option>
              @foreach ($modulesArr as $key => $value)
                <option value="{{ $key }}" {{ isset($modules) && $modules == $key ? 'selected' : '' }}>{{ __($value) }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-1 col-form-label">{{ __('Description') }}</label>
          <div class="col-sm-6">
            <input type="text" class="form-control form-control-sm" maxlength="100" name="desc" value="{{ isset($desc) ? $desc : '' }}">
          </div>
        </div>
      </div>
      <div class="card-footer">
        <div class="form-button">
          <div class="row justify-content-center">
            <div class="col-sm-1">
              @include('partials.button.back', ['class' => 'btn-sm', 'action' => route('settings.privilege.index')])
            </div>
            <div class="col-sm-1">
              @include('partials.button.submit')
            </div>
          </div>
        </div>
        <div class="form-loading">
          <img src="{{ asset('img/loading.gif') }}" height="40">
        </div>
      </div>
    </form> 
  </div>
</div>
@endsection
@section('push-js')
  <script type="text/javascript">
    $('.select2').select2({theme: 'bootstrap4'});
  </script>
@endsection