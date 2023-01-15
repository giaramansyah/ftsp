@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-body">
      <div class="form-group row justify-content-center">
        <label class="col-sm-2 col-form-label">{{ __('Tahun Akademik') }}<code>*</code></label>
        <div class="col-sm-2">
          <select class="form-control form-control-sm select2" name="division_id">
            <option value="">-- Silakan Pilih --</option>
            @foreach ($yearArr as $key => $value)
            <option value="{{ Secure::secure($value['id']) }}" {{ isset($year) && $year==$value['id'] ? 'selected' : ''
              }}>{{ $value['name'] }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="form-group">
        <h3 class="text-center mt-5">LAPORAN REKAPITULASI ANGGARAN FTSP TAHUN ANGGARAN {{ $yearDesc }}</h3>
        <table class="table table-striped table-bordered table-sm table-division" data-id="{{ Secure::secure($year) }}"
          width="100%">
          <thead>
            <tr class="text-center">
              <th>BAGIAN</th>
              <th>DANA AWAL</th>
              <th>PENYERAPAN</th>
              <th>REALISASI</th>
              <th>SISA DANA</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
          {{-- <tfoot>
            <tr>
              <th class="text-left">TOTAL</th>
              <th class="text-right"></th>
              <th class="text-right"></th>
              <th class="text-center"></th>
              <th class="text-right"></th>
            </tr>
          </tfoot> --}}
        </table>
      </div>
      <div class="form-group">
        <h3 class="text-center mt-5">REKAPITULASI ANGGARAN FAKULTAS TAHUN ANGGARAN {{ $yearDesc }}</h3>
        <table class="table table-striped table-bordered table-sm table-pic" data-id="{{ Secure::secure($year) }}"
          width="100%">
          <thead>
            <tr class="text-center">
              <th>PIC</th>
              <th>DANA AWAL</th>
              <th>PENYERAPAN</th>
              <th>REALISASI</th>
              <th>SISA DANA</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
          <tfoot>
            <tr>
              <th class="text-left">TOTAL</th>
              <th class="text-right"></th>
              <th class="text-right"></th>
              <th class="text-center"></th>
              <th class="text-right"></th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
@section('push-js')
<script type="text/javascript">
  $('.select2').select2({theme: 'bootstrap4'});

  $('.select2').on('change', function(){
    window.location.href = "{{ route('report.recapitulation.index') }}/"+$(this).val()
  })

  $('.table-division').each(function(){
    var table = $(this);
    var id = table.data('id')
    table.DataTable(
      {
        responsive: true,
        autoWidth: true,
        processing: true,
        paging: false,
        info: false,
        ajax : {
          method : 'GET',
          url : "{{ route('report.recapitulation.list.division') }}",
          data : {
            id : id
          }
        },
        dom: 'rf',
        order: [],
        columns : [
          {data: 'division_link', name: 'division_link', orderable: true, searchable: true, class: 'text-uppercase'},
          {data: 'amount', name: 'amount', orderable: true, searchable: true, class: 'text-right'},
          {data: 'used', name: 'used', orderable: true, searchable: true, class: 'text-right'},
          {data: 'percent', name: 'percent', orderable: true, searchable: true, class: 'text-center'},
          {data: 'remain', name: 'remain', orderable: true, searchable: true, class: 'text-right'},
        ],
        createdRow: function (row, data, index) {
          if(index > 2) {
            $(row).addClass('text-bold')
          }
        },
        // footerCallback: function (row, data, start, end, display) {
        //   var api = this.api();
          
        //   if(api.rows( { page: 'current' } ).any()) {
        //       var intVal = function (i) {
        //           return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
        //       };

        //       totalAmount = api.column(1).data().reduce(function (a, b) {
        //           return intVal(a) + intVal(b);
        //       }, 0);

        //       totalUsed = api.column(2).data().reduce(function (a, b) {
        //           return intVal(a) + intVal(b);
        //       }, 0);
  
        //       $(api.column(1).footer()).html(formatCurrency(totalAmount));
        //       $(api.column(2).footer()).html(formatCurrency(totalUsed));
        //       $(api.column(3).footer()).html((totalUsed/totalAmount*100).toFixed(2)+'%');
        //       $(api.column(4).footer()).html(formatCurrency(totalAmount-totalUsed));
        //       $(api.table().footer()).show()
        //     } else {
        //       $(api.table().footer()).hide()
        //     }
        // }
      }
    );
  });

  $('.table-pic').each(function(){
    var table = $(this);
    var id = table.data('id')
    table.DataTable(
      {
        responsive: true,
        autoWidth: true,
        processing: true,
        paging: false,
        info: false,
        ajax : {
          method : 'GET',
          url : "{{ route('report.recapitulation.list.pic') }}",
          data : {
            id : id
          }
        },
        dom: 'rf',
        order: [],
        columns : [
          {data: 'pic', name: 'pic', orderable: true, searchable: true, class: 'text-uppercase'},
          {data: 'amount', name: 'amount', orderable: true, searchable: true, class: 'text-right'},
          {data: 'used', name: 'used', orderable: true, searchable: true, class: 'text-right'},
          {data: 'percent', name: 'percent', orderable: true, searchable: true, class: 'text-center'},
          {data: 'remain', name: 'remain', orderable: true, searchable: true, class: 'text-right'},
        ],
        footerCallback: function (row, data, start, end, display) {
          var api = this.api();
          
          if(api.rows( { page: 'current' } ).any()) {
              var intVal = function (i) {
                  return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
              };

              totalAmount = api.column(1).data().reduce(function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0);

              totalUsed = api.column(2).data().reduce(function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0);
  
              $(api.column(1).footer()).html(formatCurrency(totalAmount));
              $(api.column(2).footer()).html(formatCurrency(totalUsed));
              $(api.column(3).footer()).html((totalUsed/totalAmount*100).toFixed(2)+'%');
              $(api.column(4).footer()).html(formatCurrency(totalAmount-totalUsed));
              $(api.table().footer()).show()
            } else {
              $(api.table().footer()).hide()
            }
        }
      }
    );
  });
</script>
@endsection