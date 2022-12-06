@extends('layouts/main')
@section('title', 'Edit Data')
@section('content')
<div class="container-fluid">
  <div class="card">
    <form class="form-lazy-control" data-action="{{ $action }}">
      <div class="card-body">
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">{{ __('Tahun Ajaran') }}<code>*</code></label>
          <div class="col-sm-3">
            <select class="form-control form-control-sm select2" name="year" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
              <option value="">-- {{ __('Please Select') }} --</option>
              @foreach ($yearArr as $key => $value)
                <option value="{{ $value['id'] }}" {{ isset($year) && $year == $value['id'] ? 'selected' : '' }}>{{ $value['name'] }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">{{ __('No. M.A.') }}<code>*</code></label>
          <div class="col-sm-2">
            <input type="text" class="form-control form-control-sm" maxlength="100" name="ma_id" value="{{ isset($ma_id) ? $ma_id : '' }}" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">{{ __('Deskripsi') }}<code>*</code></label>
          <div class="col-sm-10">
            <input type="text" class="form-control form-control-sm" maxlength="100" name="description" value="{{ isset($description) ? $description : '' }}" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">{{ __('Unit') }}<code>*</code></label>
          <div class="col-sm-3">
            <select class="form-control form-control-sm select2" name="division_id" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
              <option value="">-- {{ __('Please Select') }} --</option>
              @foreach ($divisionArr as $key => $value)
                <option value="{{ $value['id'] }}" {{ isset($division_id) && $division_id == $value['id'] ? 'selected' : '' }}>{{ $value['name'] }}</option>
              @endforeach
            </select>
          </div>
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