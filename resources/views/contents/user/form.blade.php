@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
  <div class="card">
    <form class="form-lazy-control" data-action="{{ $action }}" data-validate="username">
      <div class="card-body">
        <div class="alert hidden" role="alert"></div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Username<code>*</code></label>
          <div class="col-sm-3">
            <input type="text" class="form-control form-control-sm" maxlength="100" name="username" value="{{ isset($username) ? $username : '' }}"  {{ isset($username) ? 'readonly' : '' }} required>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Nama Lengkap<code>*</code></label>
          <div class="col-sm-3">
            <div class="row">
              <div class="col-sm-6">
                <input type="text" class="form-control form-control-sm" maxlength="50" name="first_name" value="{{ isset($first_name) ? $first_name : '' }}" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
              </div>
              <div class="col-sm-6">
                <input type="text" class="form-control form-control-sm" maxlength="50" name="last_name" value="{{ isset($last_name) ? $last_name : '' }}" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
              </div>
            </div>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Email<code>*</code></label>
          <div class="col-sm-3">
            <input type="email" class="form-control form-control-sm" maxlength="50" name="email" value="{{ isset($email) ? $email : '' }}" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">PIC<code>*</code></label>
          <div class="col-sm-3">
            <select class="form-control form-control-sm select2" name="staff_id" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
              <option value="">-- Silakan Pilih --</option>
              @foreach ($staffArr as $key => $value)
                <option value="{{ $value['id'] }}" {{ isset($staff_id) && $staff_id == $value['id'] ? 'selected' : '' }}>{{ $value['name'] }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row d-none form-division-select-1">
          <label class="col-sm-2 col-form-label">Unit<code>*</code></label>
          <div class="col-sm-3">
            <select class="form-control form-control-sm select2" name="division_id" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
              <option value="">-- Silakan Pilih --</option>
              @foreach ($divisionArrS1 as $key => $value)
                <option value="{{ $value['id'] }}" {{ isset($division_id) && $division_id == $value['id'] ? 'selected' : '' }}>{{ $value['name'] }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row d-none form-division-select-2">
          <label class="col-sm-2 col-form-label">Unit<code>*</code></label>
          <div class="col-sm-3">
            <select class="form-control form-control-sm select2" name="division_id" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
              <option value="">-- Silakan Pilih --</option>
              @foreach ($divisionArrS2 as $key => $value)
                <option value="{{ $value['id'] }}" {{ isset($division_id) && $division_id == $value['id'] ? 'selected' : '' }}>{{ $value['name'] }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row d-none form-division-input-f">
          <label class="col-sm-2 col-form-label">Unit<code>*</code></label>
          <div class="col-sm-3">
            <input type="hidden" name="division_id" value="1">
            <input type="text" class="form-control form-control-sm" maxlength="50" name="division" value="Fakultas" {{ isset($mandatory) && $mandatory? 'required' : '' }} readonly>
          </div>
        </div>
        <div class="form-group row d-none form-division-input-3">
          <label class="col-sm-2 col-form-label">Unit<code>*</code></label>
          <div class="col-sm-3">
            <input type="hidden" name="division_id" value="6">
            <input type="text" class="form-control form-control-sm" maxlength="50" name="division" value="Doktor Teknik Arsitektur" {{ isset($mandatory) && $mandatory? 'required' : '' }} readonly>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Grup Akses<code>*</code></label>
          <div class="col-sm-3">
            <select class="form-control form-control-sm select2" name="privilege_group_id" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
              <option value="">-- Silakan Pilih --</option>
              @foreach ($groupArr as $value)
                <option value="{{ $value['id'] }}" {{ isset($privilege_group_id) && $privilege_group_id == $value['id'] ? 'selected' : '' }}>{{ $value['name'] }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <div class="form-button">
          <div class="row justify-content-center">
            <div class="col-sm-1">
              @include('partials.button.back', ['class' => 'btn-sm btn-block', 'action' => route('settings.user.index')])
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

    $('select[name="staff_id"]').on('change', function(){
      if($(this).val() == "{{ config('global.staff.code.admin') }}" || $(this).val() == '') {
        $('.form-division-select-1').addClass('d-none');
        $('.form-division-select-2').addClass('d-none');
        $('.form-division-input-f').addClass('d-none');
        $('.form-division-input-3').addClass('d-none');
        $('.form-division-select-1').find('select').attr('disabled', true);
        $('.form-division-select-2').find('select').attr('disabled', true);
        $('.form-division-input-f').find('input').attr('disabled', true);
        $('.form-division-input-3').find('input').attr('disabled', true);
      } else if($(this).val() == "{{ config('global.staff.code.kaprodis1') }}") {
        $('.form-division-select-1').removeClass('d-none');
        $('.form-division-select-2').addClass('d-none');
        $('.form-division-input-f').addClass('d-none');
        $('.form-division-input-3').addClass('d-none');
        $('.form-division-select-1').find('select').attr('disabled', false);
        $('.form-division-select-2').find('select').attr('disabled', true);
        $('.form-division-input-f').find('input').attr('disabled', true);
        $('.form-division-input-3').find('input').attr('disabled', true);
      } else if($(this).val() == "{{ config('global.staff.code.kaprodis2') }}") {
        $('.form-division-select-1').addClass('d-none');
        $('.form-division-select-2').removeClass('d-none');
        $('.form-division-input-f').addClass('d-none');
        $('.form-division-input-3').addClass('d-none');
        $('.form-division-select-1').find('select').attr('disabled', true);
        $('.form-division-select-2').find('select').attr('disabled', false);
        $('.form-division-input-f').find('input').attr('disabled', true);
        $('.form-division-input-3').find('input').attr('disabled', true);
      } else if($(this).val() == "{{ config('global.staff.code.kaprodis3') }}") {
        $('.form-division-select-1').addClass('d-none');
        $('.form-division-select-2').addClass('d-none');
        $('.form-division-input-f').addClass('d-none');
        $('.form-division-input-3').removeClass('d-none');
        $('.form-division-select-1').find('select').attr('disabled', true);
        $('.form-division-select-2').find('select').attr('disabled', true);
        $('.form-division-input-f').find('input').attr('disabled', true);
        $('.form-division-input-3').find('input').attr('disabled', false);
      } else {
        $('.form-division-select-1').addClass('d-none');
        $('.form-division-select-2').addClass('d-none');
        $('.form-division-input-f').removeClass('d-none');
        $('.form-division-input-3').addClass('d-none');
        $('.form-division-select-1').find('select').attr('disabled', true);
        $('.form-division-select-2').find('select').attr('disabled', true);
        $('.form-division-input-f').find('input').attr('disabled', false);
        $('.form-division-input-3').find('input').attr('disabled', true);
      }
    });

    $('select[name="staff_id"]').trigger('change');
  </script>
@endsection