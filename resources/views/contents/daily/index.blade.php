@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
  <div class="card">
    <form class="form-lazy-control" data-action="{{ $action }}">
      <div class="card-body">
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Tahun Akademik<code>*</code></label>
          <div class="col-sm-2">
            <select class="form-control form-control-sm select2" name="year" {{ isset($mandatory)
              && $mandatory? 'required' : '' }}>
              <option value="">-- Silakan Pilih --</option>
              @foreach ($yearArr as $key => $value)
              <option value="{{ Secure::secure($value['id']) }}">{{
                $value['name'] }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Unit<code>*</code></label>
          <div class="col-sm-2">
            <select class="form-control form-control-sm select2" name="division_id" {{
              isset($mandatory) && $mandatory? 'required' : '' }}>
              <option value="">-- Silakan Pilih --</option>
              @foreach ($divisionArr as $key => $value)
              <option value="{{ Secure::secure($value['id']) }}">{{
                $value['name'] }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Tgl. Transaksi<code>*</code></label>
          <div class="col-sm-2">
            <input type="date" class="form-control form-control-sm" name="daily_date"
              value="{{ date('Y-m-d') }}" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <div class="form-button">
          <div class="row justify-content-center">
            <div class="col-auto">
              <button type="button" class="btn btn-outline-info btn-sm btn-block dropdown-toggle"
                data-toggle="dropdown">
                <i class="fas fa-print"></i> Cetak Laporan
              </button>
              <div class="dropdown-menu" role="menu">
                <button type="submit" class="dropdown-item" name="ext"
                  value="{{ Secure::secure('xlsx') }}">
                  <i class="fas fa-excel"></i> Excel
                </button>
                <button type="submit" class="dropdown-item" name="ext"
                value="{{ Secure::secure('pdf') }}">
                  <i class="fas fa-pdf"></i> PDF
                </button>
              </div>
            </div>
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