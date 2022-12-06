@extends('layouts/main')
@section('title', 'Privilege')
@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header">
      <div class="card-tools">
        @include('partials.button.add', ['action' => route('settings.privilege.add')])
      </div>
    </div>
    <div class="card-body">
      <table class="table table-striped table-sm table-data" width="100%">
        <thead>
          <tr>
            <th>{{ __('No') }}</th>
            <th>{{ __('Code') }}</th>
            <th>{{ __('Menu') }}</th>
            <th>{{ __('Modules') }}</th>
            <th>{{ __('Description') }}</th>
            <th>{{ __('Action') }}</th>
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
    $('.table-data').DataTable(
        {
            responsive: true,
            autoWidth: true,
            processing: true,
            ajax : "{{ route('settings.privilege.list') }}",
            order: [],
            columns : [
              {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
              {data: 'code', name: 'code', orderable: true, searchable: true},
              {data: 'menu_label', name: 'menu_label', orderable: true, searchable: true},
              {data: 'modules_name', name: 'modules_name', orderable: true, searchable: true},
              {data: 'desc', name: 'desc', orderable: true, searchable: true},
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