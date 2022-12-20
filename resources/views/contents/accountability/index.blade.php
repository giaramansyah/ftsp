@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
  <div class="card">
    <form class="form-lazy-control" data-action="{{ $action }}">
      <div class="card-body">
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Tahun Ajaran<code>*</code></label>
          <div class="col-sm-2">
            <select class="form-control form-control-sm select2" name="year" onchange="getData()" {{ isset($mandatory) &&
              $mandatory? 'required' : '' }}>
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
            <select class="form-control form-control-sm select2" name="division_id" onchange="getData()" {{ isset($mandatory) &&
              $mandatory? 'required' : '' }}>
              <option value="">-- Silakan Pilih --</option>
              @foreach ($divisionArr as $key => $value)
              <option value="{{ Secure::secure($value['id']) }}">{{
                $value['name'] }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Tgl. Laporan<code>*</code></label>
          <div class="col-sm-2">
            <input type="date" class="form-control form-control-sm" name="accountability_date" value="{{ date('Y-m-d') }}" {{ isset($mandatory) &&
              $mandatory? 'required' : '' }}>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Penerimaan<code>*</code></label>
          <div class="col-sm-10">
            <table class="table table-sm table-bordered table-reception" width="100%">
              <thead>
                <tr>
                  <th class="text-center">#</th>
                  <th class="text-center">No. Kas</th>
                  <th class="text-center">Tgl. Penerimaan</th>
                  <th class="text-center">No. M.A.</th>
                  <th class="text-center">Deskripsi</th>
                  <th class="text-center">A/N</th>
                  <th class="text-center">Jumlah</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Pengeluaran<code>*</code></label>
          <div class="col-sm-10">
            <table class="table table-sm table-bordered table-expense" width="100%">
              <thead>
                <tr>
                  <th class="text-center">#</th>
                  <th class="text-center">No. Kas</th>
                  <th class="text-center">Tgl. Transaksi</th>
                  <th class="text-center">Deskripsi</th>
                  <th class="text-center">A/N</th>
                  <th class="text-center">Jumlah</th>
                  <th class="text-center">Status</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Mengetahui<code>*</code></label>
          <div class="col-sm-2">
            <input type="text" class="form-control form-control-sm" name="knowing" {{ isset($mandatory) &&
              $mandatory? 'required' : '' }}>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <div class="form-button">
          <div class="row justify-content-center">
            <div class="col-auto">
              <button type="submit" class="btn btn-outline-dark btn-sm btn-block" name="report_type" value="{{ config('global.report.code.accountability_fakultas') }}">
                <i class="fas fa-print"></i> Cetak Laporan (Fakultas)
              </button>
            </div>
            <div class="col-auto">
              <button type="submit" class="btn btn-outline-primary btn-sm btn-block" name="report_type" value="{{ config('global.report.code.accountability') }}">
                <i class="fas fa-print"></i> Cetak Laporan (Universitas)
              </button>
            </div>
            <div class="col-auto">
              <button type="submit" class="btn btn-outline-secondary btn-sm btn-block" name="report_type" value="{{ config('global.report.code.accountability_umd') }}">
                <i class="fas fa-print"></i> Cetak Laporan (UMD)
              </button>
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
  $('.table-reception').DataTable({dom: 'rf'});
  $('.table-expense').DataTable({dom: 'rf'})

  $('select[name="year"]').trigger('change')
  $('select[name="division_id"]').trigger('change')

  function getData() {
    var year = $('select[name="year"]').val();
    var division_id = $('select[name="division_id"]').val();

    if(year != '' && division_id != '') {
      var data = {
        year : year,
        division_id : division_id
      };
      $('.table-reception').dataTable().fnClearTable();
      $('.table-reception').dataTable().fnDestroy();
      $('.table-reception').DataTable(
        {
          responsive: true,
          autoWidth: true,
          processing: true,
          paging: false,
          info: false,
          ajax : {
            method : 'get',
            url : "{{ route('report.accountability.reception') }}",
            data: data
          },
          dom: 'rf',
          order: [],
          columns : [
            {data: 'input', name: 'input', orderable: false, searchable: false, class: "text-center"},
            {data: 'reception_id', name: 'reception_id', orderable: true, searchable: true},
            {data: 'reception_date_format', name: 'reception_date_format', orderable: true, searchable: true},
            {data: 'ma_id', name: 'ma_id', orderable: true, searchable: true},
            {data: 'description', name: 'description', orderable: true, searchable: true},
            {data: 'name', name: 'name', orderable: true, searchable: true},
            {data: 'amount', name: 'amount', orderable: true, searchable: true, class: "text-right"},
          ],
        }
      );
      $('.table-expense').dataTable().fnClearTable();
      $('.table-expense').dataTable().fnDestroy();
      $('.table-expense').DataTable(
        {
          responsive: true,
          autoWidth: true,
          processing: true,
          paging: false,
          info: false,
          ajax : {
            method : 'get',
            url : "{{ route('report.accountability.expense') }}",
            data: data
          },
          dom: 'rf',
          order: [],
          columns : [
            {data: 'input', name: 'input', orderable: false, searchable: false, class: "text-center"},
            {data: 'expense_id', name: 'expense_id', orderable: true, searchable: true},
            {data: 'expense_date_format', name: 'expense_date_format', orderable: true, searchable: true},
            {data: 'description', name: 'description', orderable: true, searchable: true},
            {data: 'name', name: 'name', orderable: true, searchable: true},
            {data: 'amount', name: 'amount', orderable: true, searchable: true, class: "text-right"},
            {data: 'status_desc', name: 'status_desc', orderable: true, searchable: true},
          ],
        }
      );
    }
  }
</script>
@endsection