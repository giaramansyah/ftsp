@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
  <div class="card">
    <form class="form-lazy-control" data-action="{{ $action }}" data-method="upload" data-validate="max_amount,image">
      <div class="card-body">
        <input type="hidden" name="type" value="{{ isset($type) ? $type : '' }}">
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Tahun Ajaran<code>*</code></label>
          <div class="col-sm-2">
            <select class="form-control form-control-sm select2" name="year" onchange="getData()">
              <option value="">-- Silakan Pilih --</option>
              @foreach ($yearArr as $key => $value)
              <option value="{{ Secure::secure($value['id']) }}" {{ isset($year) && $year==$value['id'] ? 'selected' : '' }}>{{
                $value['name'] }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Unit<code>*</code></label>
          <div class="col-sm-2">
            <select class="form-control form-control-sm select2" name="division_id" onchange="getData()">
              <option value="">-- Silakan Pilih --</option>
              @foreach ($divisionArr as $key => $value)
              <option value="{{ Secure::secure($value['id']) }}" {{ isset($year) && $year==$value['id'] ? 'selected' : '' }}>{{
                $value['name'] }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">No. Kas<code>*</code></label>
          <div class="col-sm-2">
            <input type="text" class="form-control form-control-sm" name="expense_id" value="{{ isset($expense_id) ? $expense_id : '' }}" readonly {{ isset($mandatory) && $mandatory? 'required' : '' }}>
          </div>
          <label class="col-sm-2 offset-sm-1 col-form-label">Tgl. Transaksi<code>*</code></label>
          <div class="col-sm-2">
            <input type="date" class="form-control form-control-sm" name="expense_date" value="{{ isset($expense_date) ? $expense_date : '' }}" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">No. Surat<code>*</code></label>
          <div class="col-sm-2">
            <input type="text" class="form-control form-control-sm" name="reff_no" value="{{ isset($reff_no) ? $reff_no : '' }}" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
          </div>
          <label class="col-sm-2 offset-sm-1 col-form-label">Tgl. Surat<code>*</code></label>
          <div class="col-sm-2">
            <input type="date" class="form-control form-control-sm" name="reff_date" value="{{ isset($reff_date) ? $reff_date : '' }}" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Deskripsi<code>*</code></label>
          <div class="col-sm-7">
            <input type="text" class="form-control form-control-sm" name="description" value="{{ isset($description) ? $description : '' }}" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Sub Deskripsi</label>
          <div class="col-sm-7">
            <textarea class="form-control form-control-sm" name="sub_description" rows="4">{{ isset($description) ? $description : '' }}</textarea>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Mata Anggaran<code>*</code></label>
          <input type="hidden" name="data_id">
          <input type="hidden" name="validate_max" id="validate_max">
          <div class="col-sm-7">
            <table class="table table-sm table-bordered" width="100%">
              <thead>
                <tr>
                  <th class="text-center">#</th>
                  <th class="text-center">No. M.A.</th>
                  <th class="text-center">Deskripsi</th>
                  <th class="text-center">Total Dana</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">A/N<code>*</code></label>
          <div class="col-sm-2">
            <input type="text" class="form-control form-control-sm" name="name" value="{{ isset($name) ? $name : '' }}" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
          </div>
          <label class="col-sm-2 offset-sm-1 col-form-label">PIC<code>*</code></label>
          <div class="col-sm-2">
            <select class="form-control form-control-sm select2" name="staff_id" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
              <option value="">-- Silakan Pilih --</option>
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Nilai Transaksi<code>*</code></label>
          <div class="col-sm-2">
            <div class="input-group input-group-sm">
              <div class="input-group-prepend">
                <span class="input-group-text">Rp</span>
              </div>
              <input type="text" class="form-control form-control-sm text-right" maxlength="20" name="amount"
                value="{{ isset($amount) ? $amount : '' }}" onkeypress="preventAlpha(event)"
                onkeyup="numberFormat(this, true)" onblur="numberFormat(this, true)" {{ isset($mandatory) &&
                $mandatory? 'required' : '' }}>
            </div>
          </div>
          <label class="col-sm-2 offset-sm-1 col-form-label">No. Rekening<code>*</code></label>
          <div class="col-sm-2">
            <input type="text" class="form-control form-control-sm" maxlength="20" name="account"
                value="{{ isset($account) ? $account : '' }}" onkeypress="preventAlpha(event)"
                onkeyup="numberFormat(this, false)" onblur="numberFormat(this, false)" {{ isset($mandatory) &&
                $mandatory? 'required' : '' }}>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Terbilang<code>*</code></label>
          <div class="col-sm-7">
            <input type="text" class="form-control form-control-sm" name="text_amount" value="{{ isset($text_amount) ? $text_amount : '' }}" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
          </div>
        </div>
        @if($is_red)
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Tgl. Penyerahan<code>*</code></label>
          <div class="col-sm-2">
            <input type="date" class="form-control form-control-sm" name="apply_date" value="{{ isset($apply_date) ? $apply_date : '' }}" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Upload LPJ</label>
          <div class="col-sm-7">
            <div class="input-group input-group-sm">
              <div class="custom-file">
                <input type="file" name="image" id="image" class="custom-file-input"
                  accept="application/pdf, image/jpg, image/png" {{ isset($mandatory) &&
                    $mandatory? 'required' : '' }}>
                <label class="custom-file-label" for="file">Pilih File</label>
              </div>
            </div>
            <span class="font-italic small">*ekstensi file hanya .pdf, .jpg dan .png</span>
          </div>
        </div>
        @endif
      </div>
      <div class="card-footer">
        <div class="form-button">
          <div class="row justify-content-center">
            <div class="col-sm-1">
              @include('partials.button.back', ['class' => 'btn-sm btn-block', 'action' =>
              route('transaction.expense.index')])
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
  $('.table').DataTable({dom: 'rf'});

  function getData() {
    var year = $('select[name="year"]').val();
    var division_id = $('select[name="division_id"]').val();

    if(year != '' && division_id != '') {
      $('.table').dataTable().fnClearTable();
      $('.table').dataTable().fnDestroy();
      $('.table').DataTable(
        {
          responsive: true,
          autoWidth: true,
          processing: true,
          paging: false,
          info: false,
          ajax : {
            method : 'get',
            url : "{{ route('transaction.expense.data') }}",
            data: {
              year : year,
              division_id : division_id
            }
          },
          dom: 'rf',
          order: [],
          columns : [
            {data: 'input', name: 'input', orderable: false, searchable: false, class: "text-center"},
            {data: 'ma_id', name: 'ma_id', orderable: true, searchable: true},
            {data: 'description', name: 'description', orderable: true, searchable: true},
            {data: 'remain', name: 'remain', orderable: true, searchable: true, class: "text-right"},
          ],
          fnInitComplete : function() {
            $('.table').on('click', 'input[name="ma"]', function() {
              var data_id = $(this).val();
              var amount = $(this).data('amount')
              $('input[name="data_id"]').val(data_id)
              $('input[name="validate_max"]').val(amount)
              getPic(data_id)
            })
          }
        }
      );
    }

    function getPic(data_id) {
      $.ajax({
        method: 'get',
        url: "{{ route('transaction.expense.pic') }}",
        data: {data_id : data_id},
        dataType: 'json',
        beforeSend: function() {
          $('select[name="staff_id"]').select2({data: [{id: '', text: '-- Silakan Pilih --'}]});
        },
        success: function(response) {
          $('select[name="staff_id"]').select2({data: response.data});
        }
      });
    }
  }
</script>
@endsection