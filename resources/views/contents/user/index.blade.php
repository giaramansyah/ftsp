@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header">
      @if($is_create)
      <div class="card-tools">
        @include('partials.button.add', ['action' => route('settings.user.add')])
      </div>
      @endif
    </div>
    <div class="card-body">
      <table class="table table-striped table-sm table-data" width="100%">
        <thead>
          <tr>
            <th>No</th>
            <th>Akun</th>
            <th>Email</th>
            <th>Akun Terhubung</th>
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
@include('partials.modal.modalforcelogout')
@endsection
@section('push-js')
<script type="text/javascript">
  $('.table-data').DataTable(
        {
            responsive: true,
            autoWidth: true,
            processing: true,
            ajax : "{{ route('settings.user.list') }}",
            order: [],
            columns : [
              {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
              {data: 'user_fullname', name: 'user_fullname', orderable: true, searchable: true},
              {data: 'email', name: 'email', orderable: true, searchable: true},
              {data: 'login', name: 'login', orderable: true, searchable: true},
              {data: 'updated_at', name: 'updated_at', orderable: true, searchable: true},
              {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            fnInitComplete : function() {
              $('.table-data').on('click', 'button[data-button="button-action"]', function() {
                $.fn.ButtonAction.call($(this))
              })
            }
        }
    );
</script>
@endsection