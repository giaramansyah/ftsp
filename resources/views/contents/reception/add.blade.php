@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
  <div class="card">
    <form class="form-lazy-control" data-action="{{ $action }}" data-method="post">
      <div class="card-body">
        <input type="hidden" name="type" value="{{ isset($type) ? $type : '' }}">
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Tahun Akademik<code>*</code></label>
          <div class="col-sm-2">
            <select class="form-control form-control-sm select2" name="year" onchange="getData();getId();">
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
            <select class="form-control form-control-sm select2" name="division_id" onchange="getData();getId();">
              <option value="">-- Silakan Pilih --</option>
              @foreach ($divisionArr as $key => $value)
              <option value="{{ Secure::secure($value['id']) }}">{{
                $value['name'] }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">No. Kas<code>*</code></label>
          <div class="col-sm-2">
            <input type="text" class="form-control form-control-sm" name="reception_id" id="reception_id" readonly {{ isset($mandatory) &&
              $mandatory? 'required' : '' }}>
          </div>
          <label class="col-sm-2 offset-sm-1 col-form-label">Tgl. Penerimaan<code>*</code></label>
          <div class="col-sm-2">
            <input type="date" class="form-control form-control-sm" name="reception_date" {{ isset($mandatory) &&
              $mandatory? 'required' : '' }}>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Mata Anggaran<code>*</code></label>
          <div class="col-sm-2">
            <div class="form-check pt-2">
              <input class="form-check-input" type="checkbox" name="from_ma_id" value="1" onchange="getData()">
              <label class="form-check-label">Penerimaan UMD</label>
            </div>
          </div>
        </div>
        <div class="form-group row mb-0 form-ma-input">
          <div class="offset-sm-2 col-sm-2">
            <input type="hidden" name="expense_id">
            <input type="text" class="form-control form-control-sm" name="ma_id" readonly {{ isset($mandatory) &&
              $mandatory? 'required' : '' }}>
          </div>
          <div class="col-sm-2">
            <div class="form-check pt-2">
              <input class="form-check-input" type="checkbox" name="edt_ma_id" value="1" onchange="editMa()">
              <label class="form-check-label">Ubah No. M.A.</label>
            </div>
          </div>
        </div>
        <div class="form-group row form-ma-table">
          <div class="offset-sm-2 col-sm-10">
            <table class="table table-sm table-bordered" width="100%">
              <thead>
                <tr>
                  <th class="text-center">#</th>
                  <th class="text-center">No. M.A.</th>
                  <th class="text-center">Deskripsi</th>
                  <th class="text-center">Nominal</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Deskripsi<code>*</code></label>
          <div class="col-sm-7">
            <input type="text" class="form-control form-control-sm" name="description" maxlength="350" {{ isset($mandatory) &&
              $mandatory? 'required' : '' }}>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Sub Deskripsi</label>
          <div class="col-sm-7">
            <textarea class="form-control form-control-sm" name="sub_description" maxlength="350" rows="4"></textarea>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">A/N</label>
          <div class="col-sm-2">
            <select class="form-control form-control-sm select2" name="name">
              <option value="">-- Silakan Pilih --</option>
              @foreach ($employeeArr as $key => $value)
              <option value="{{ $value['id'] }}">{{
                $value['name'] }}</option>
              @endforeach
            </select>
          </div>
          <label class="col-sm-2 offset-sm-1 col-form-label form-pic-label">PIC<code>*</code></label>
          <div class="col-sm-2 form-pic-select">
            <select class="form-control form-control-sm select2" name="staff_id" {{ isset($mandatory) &&
              $mandatory? 'required' : '' }}>
              <option value="">-- Silakan Pilih --</option>
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Nilai Penerimaan<code>*</code></label>
          <div class="col-sm-2">
            <div class="input-group input-group-sm">
              <div class="input-group-prepend">
                <span class="input-group-text">Rp</span>
              </div>
              <input type="text" class="form-control form-control-sm text-right" maxlength="20" name="amount"
                onkeypress="preventAlpha(event)" onkeyup="numberFormat(this, true)" onblur="numberFormat(this, true);amountText(this.value, '#text_amount')" {{
                isset($mandatory) && $mandatory? 'required' : '' }}>
            </div>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Terbilang<code>*</code></label>
          <div class="col-sm-7">
            <input type="text" class="form-control form-control-sm" name="text_amount" id="text_amount" {{ isset($mandatory) &&
              $mandatory? 'required' : '' }} readonly>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <div class="form-button">
          <div class="row justify-content-center">
            <div class="col-sm-1">
              @include('partials.button.back', ['class' => 'btn-sm btn-block', 'action' =>
              route('transaction.reception.index')])
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

  $('select[name="year"]').trigger('change')
  $('select[name="division_id"]').trigger('change')
  $('input[name="from_ma_id"]').trigger('change')

  function getData() {
    var year = $('select[name="year"]').val();
    var division_id = $('select[name="division_id"]').val();
    var from_ma = $('input[name="from_ma_id"]').is(":checked");

    if(year != '' && division_id != '' && from_ma) {
      $('.table').dataTable().fnClearTable();
      $('.table').dataTable().fnDestroy();
      $('.form-ma-input').show()
      $('.form-ma-input').find('input[type="text"]').attr('required', true)
      $('.form-ma-table').show()
      $('.form-pic-label').show()
      $('.form-pic-select').show()
      $('.form-pic-select').find('select').attr('required', true)
      var data = {
        year : year,
        division_id : division_id
      };
      $('.table').DataTable(
        {
          responsive: true,
          autoWidth: true,
          processing: true,
          paging: false,
          info: false,
          ajax : {
            method : 'get',
            url : "{{ route('transaction.reception.data') }}",
            data: data
          },
          dom: 'rf',
          order: [],
          columns : [
            {data: 'input', name: 'input', orderable: false, searchable: false, class: "text-center"},
            {data: 'ma_id', name: 'ma_id', orderable: true, searchable: true},
            {data: 'description', name: 'description', orderable: true, searchable: true},
            {data: 'amount', name: 'amount', orderable: true, searchable: true, class: "text-right"},
          ],
          fnInitComplete : function() {
            $('.table').off().on('click', 'input[name="ma"]', function() {
              var data_id = $(this).val();
              var expense_id = $(this).data('id')
              var amount = $(this).data('amount')
              var ma = $(this).data('ma')
              var desc = $(this).data('desc')
              var subdesc = $(this).data('subdesc')
              var name = $(this).data('name')
              var pic = $(this).data('pic')
              var textamount = $(this).data('textamount')
              $('input[name="ma_id"]').val(ma)
              $('input[name="expense_id"]').val(expense_id)
              $('input[name="description"]').val(desc)
              $('input[name="sub_escription"]').val(subdesc)
              $('select[name="name"]').select2().val(name).trigger("change");
              $('input[name="amount"]').val(amount)
              $('input[name="text_amount"]').val(textamount)
              getPic(data_id, pic)
            })
          }
        }
      );
    } else {
      $('.form-ma-input').hide()
      $('.form-ma-input').find('input[type="text"]').attr('required', false)
      $('.form-ma-table').hide()
      $('.form-pic-label').hide()
      $('.form-pic-select').hide()
      $('.form-pic-select').find('select').attr('required', false)
    }
  }

  function getId() {
    var year = $('select[name="year"]').val();
    var division_id = $('select[name="division_id"]').val();

    if(year != '' && division_id != '') {
      var data = {
        year : year,
        division_id : division_id
      };

      $.ajax({
        method: 'get',
        url: "{{ route('transaction.reception.generate') }}",
        data: data,
        dataType: 'json',
        beforeSend: function() {
          $('#reception_id').val('');
        },
        success: function(response) {
          if(response.status) {
            $('#reception_id').val(response.data);
          }
        }
      });
    }

  }

  function getPic(data_id, pic = null) {
    data_id = data_id.split('|')
    $.ajax({
      method: 'get',
      url: "{{ route('transaction.reception.pic') }}",
      data: {data_id : data_id[0]},
      dataType: 'json',
      beforeSend: function() {
        $('select[name="staff_id"]').empty().trigger("change");
      },
      success: function(response) {
        $('select[name="staff_id"]').select2({data: response.data});
        if(pic != null) {
          $('select[name="staff_id"]').select2().val(pic).trigger("change");
        }
      }
    });
  }

  function editMa() {
    if($('input[name="edt_ma_id"]').is(':checked') && $('input[name="ma_id"]').val() != '') {
      $('input[name="ma_id"]').attr('readonly', false)
    } else {
      $('input[name="ma_id"]').attr('readonly', true)
    }
  }
</script>
@endsection