@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
  <div class="card">
    <form class="form-lazy-control" data-action="{{ $action }}">
      <div class="card-body">
        <div class="alert hidden" role="alert"></div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">NIK<code>*</code></label>
          <div class="col-sm-3">
            <input type="text" class="form-control form-control-sm" name="nik" value="{{ isset($nik) ? $nik : '' }}" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Unit<code>*</code></label>
          <div class="col-sm-3">
            <select class="form-control form-control-sm" name="unit_id" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
              <option value="">-- Silakan Pilih --</option>
              @foreach ($unitArr as $key => $value)
                <option value="{{ $value['id'] }}" {{ isset($unit_id) && $unit_id == $value['id'] ? 'selected' : '' }}>{{ $value['name'] }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Nama Lengkap<code>*</code></label>
          <div class="col-sm-3">
            <input type="text" class="form-control form-control-sm" name="name" value="{{ isset($name) ? $name : '' }}" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Nomor rekening<code>*</code></label>
          <div class="col-sm-3">
            <input type="text" class="form-control form-control-sm" name="account" value="{{ isset($account) ? $account : '' }}" onkeypress="preventAlpha(event)"
            onkeyup="numberFormat(this, false)" onblur="numberFormat(this, false)" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <div class="form-button">
          <div class="row justify-content-center">
            <div class="col-sm-1">
              @include('partials.button.back', ['class' => 'btn-sm btn-block', 'action' => route('master.employee.index')])
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
    bsCustomFileInput.init();
  </script>
@endsection