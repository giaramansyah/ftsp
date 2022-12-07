@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
  <div class="card">
    <form class="form-lazy-control" data-action="{{ $action }}">
      <div class="card-body">
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">{{ __('Tahun Ajaran') }}<code>*</code></label>
          <div class="col-sm-2">
            <select class="form-control form-control-sm select2" name="year" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
              <option value="">-- Silakan Pilih --</option>
              @foreach ($yearArr as $key => $value)
                <option value="{{ $value['id'] }}" {{ isset($year) && $year == $value['id'] ? 'selected' : '' }}>{{ $value['name'] }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">{{ __('No. M.A.') }}<code>*</code></label>
          <div class="col-sm-2">
            <input type="text" class="form-control form-control-sm" maxlength="20" name="ma_id" value="{{ isset($ma_id) ? $ma_id : '' }}" readonly {{ isset($mandatory) && $mandatory? 'required' : '' }}>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">{{ __('Deskripsi') }}<code>*</code></label>
          <div class="col-sm-6">
            <input type="text" class="form-control form-control-sm" maxlength="100" name="description" value="{{ isset($description) ? $description : '' }}" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">{{ __('Total Dana') }}<code>*</code></label>
          <div class="col-sm-2">
            <div class="input-group input-group-sm">
              <div class="input-group-prepend">
                <span class="input-group-text text-bold">Rp</span>
              </div>
              <input type="text" class="form-control form-control-sm text-right text-bold" maxlength="20"
                name="amount" value="{{ isset($amount) ? $amount : '' }}" readonly>
            </div>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">{{ __('Unit') }}<code>*</code></label>
          <div class="col-sm-2">
            <select class="form-control form-control-sm select2" name="division_id" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
              <option value="">-- Silakan Pilih --</option>
              @foreach ($divisionArr as $key => $value)
                <option value="{{ $value['id'] }}" {{ isset($division_id) && $division_id == $value['id'] ? 'selected' : '' }}>{{ $value['name'] }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row form-staff-select">
          <label class="col-sm-2 col-form-label">{{ __('PIC') }}<code>*</code></label>
          <div class="col-sm-2">
            <table class="table table-sm" width="100%">
              <tbody>
                @foreach ($staffArr as $key => $value)
                  @if(!in_array($value['id'], [config('global.staff.code.kaprodis1'), config('global.staff.code.kaprodis2')]))
                    <tr>
                      <td>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" name="staff_id" value="{{ $value['id'] }}" {{ in_array($value['id'], $staff_id) ? 'checked' : '' }} {{ isset($mandatory) && $mandatory && $key == 1 ? 'required' : '' }}>
                          <label class="form-check-label">{{ $value['name'] }}</label>
                        </div>
                      </td>
                    </tr>
                  @endif
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <div class="form-group row form-staff-input-1">
          <label class="col-sm-2 col-form-label">{{ __('PIC') }}<code>*</code></label>
          <div class="col-sm-2">
            <input type="hidden" name="staff_id" value="{{ config('global.staff.code.kaprodis1') }}">
            <input type="text" class="form-control form-control-sm" name="staff" value="{{ config('global.staff.desc.kaprodis1') }}" readonly {{ isset($mandatory) && $mandatory && $key == 0 ? 'required' : '' }}>
          </div>
        </div>
        <div class="form-group row form-staff-input-2">
          <label class="col-sm-2 col-form-label">{{ __('PIC') }}<code>*</code></label>
          <div class="col-sm-2">
            <input type="hidden" name="staff_id" value="{{ config('global.staff.code.kaprodis2') }}">
            <input type="text" class="form-control form-control-sm" name="staff" value="{{ config('global.staff.desc.kaprodis2') }}" readonly {{ isset($mandatory) && $mandatory && $key == 0 ? 'required' : '' }}>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <div class="form-button">
          <div class="row justify-content-center">
            <div class="col-sm-1">
              @include('partials.button.back', ['class' => 'btn-sm btn-block', 'action' =>
              route('master.data.index')])
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

    $('select[name="division_id"]').on('change', function(){
      if($(this).val() == "{{ config('global.division.code.fakultas') }}") {
        $('.form-staff-select').removeClass('d-none');
        $('.form-staff-input-1').addClass('d-none');
        $('.form-staff-input-2').addClass('d-none');
        $('.form-staff-select').find('input').attr('disabled', false);
        $('.form-staff-input-1').find('input').attr('disabled', true);
        $('.form-staff-input-2').find('input').attr('disabled', true);
      } else if($(this).val() == "{{ config('global.division.code.arsitektur') }}" || $(this).val() == "{{ config('global.division.code.sipil') }}") {
        $('.form-staff-select').addClass('d-none');
        $('.form-staff-input-1').removeClass('d-none');
        $('.form-staff-input-2').addClass('d-none');
        $('.form-staff-select').find('input').attr('disabled', true);
        $('.form-staff-input-1').find('input').attr('disabled', false);
        $('.form-staff-input-2').find('input').attr('disabled', true);
      } else if($(this).val() == "{{ config('global.division.code.mta') }}" || $(this).val() == "{{ config('global.division.code.mts') }}") {
        $('.form-staff-select').addClass('d-none');
        $('.form-staff-input-1').addClass('d-none');
        $('.form-staff-input-2').removeClass('d-none');
        $('.form-staff-select').find('input').attr('disabled', true);
        $('.form-staff-input-1').find('input').attr('disabled', true);
        $('.form-staff-input-2').find('input').attr('disabled', false);
      } else {
        $('.form-staff-select').addClass('d-none');
        $('.form-staff-input-1').addClass('d-none');
        $('.form-staff-input-2').addClass('d-none');
        $('.form-staff-select').find('input').attr('disabled', true);
        $('.form-staff-input-1').find('input').attr('disabled', true);
        $('.form-staff-input-2').find('input').attr('disabled', true);
      }
    })

    $('select[name="division_id"]').trigger('change')
  </script>
@endsection