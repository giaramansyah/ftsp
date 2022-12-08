@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header">
      @if($is_create)
      <div class="card-tools">
        @include('partials.button.create', ['color' => 'btn-outline-secondary', 'name' => 'Bon Putih', 'action' =>
        route('transaction.expense.add', ['type' => config('global.type.code.white')])])
        @include('partials.button.create', ['color' => 'btn-outline-danger', 'name' => 'Bon Merah', 'action' =>
        route('transaction.expense.add', ['type' => config('global.type.code.red')])])
      </div>
      @endif
    </div>
    <div class="card-body">
      <table class="table table-striped table-sm table-data" width="100%">
        <thead>
          <tr>
            <th>No</th>
            <th>Unit</th>
            <th>Saldo</th>
            <th>Tgl. Diubah</th>
            <th>Opsi</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
</div>
@include('partials.modal.modaldelete')
@endsection
@section('push-js')
<script type="text/javascript">
  // $('.table-data').DataTable(
  //       {
  //           responsive: true,
  //           autoWidth: true,
  //           processing: true,
  //           ajax : "{{ route('transaction.expense.list') }}",
  //           order: [],
  //           columns : [
  //             {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
  //             {data: 'balance', name: 'balance', orderable: true, searchable: true},
  //             {data: 'amount', name: 'amount', orderable: true, searchable: true},
  //             {data: 'updated_at', name: 'updated_at', orderable: true, searchable: true},
  //             {data: 'action', name: 'action', orderable: false, searchable: false},
  //           ],
  //           fnInitComplete : function() {
  //             $('.table-data').on('click', 'button[data-button="button-action"]', function() {
  //               $.fn.ButtonAction.call($(this))
  //             })
  //           }
  //       }
  //   );
</script>
@endsection