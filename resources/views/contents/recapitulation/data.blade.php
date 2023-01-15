@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header">
      <div class="card-tools">
        @include('partials.button.back', ['class' => 'btn-sm', 'action' => route('report.recapitulation.division', ['id' => Secure::pack(['year' => $year, 'division_id' => $division_id])])])
      </div>
    </div>
    <div class="card-body">
      <div class="form-group">
        <h3 class="text-center">DETAIL PENGAJUAN KEGIATAN</h3>
        <h4 class="text-center mt-2">
          {{ $ma_id }} {{ $description }}
        </h4>
        <h4 class="text-center mt-2 text-info">
          Anggaran {{ $amount }} | Pengajuan {{ $used }} | Realisasi {{ $percent }} | Sisa Dana {{ $remain }}
        </h4>
        <table class="table table-striped table-bordered table-sm table-data" data-id="{{ $id }}"
          width="100%">
          <thead>
            <tr class="text-center">
              <th>NO</th>
              <th>NO. SURAT</th>
              <th>KEGIATAN</th>
              <th>TGL. PENGAJUAN</th>
              <th>PENGAJUAN</th>
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
    </div>
  </div>
</div>
@endsection
@section('push-js')
<script type="text/javascript">
  $('.table-data').each(function(){
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
          url : "{{ route('report.recapitulation.data.detail') }}",
          data : {
            id : id
          }
        },
        dom: 'rf',
        order: [],
        columns : [
          {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, class: 'text-center'},
          {data: 'reff_no', name: 'reff_no', orderable: true, searchable: true},
          {data: 'description', name: 'description', orderable: true, searchable: true},
          {data: 'reff_date_format', name: 'reff_date_format', orderable: true, searchable: true, class: 'text-center'},
          {data: 'amount', name: 'amount', orderable: true, searchable: true, class: 'text-right'},
        ],
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
</script>
@endsection