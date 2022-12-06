@extends('layouts/main')
@section('title', 'Privigroup')
@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header">
      <div class="card-tools">
        @include('partials.button.add', ['action' => route('settings.privigroup.add')])
      </div>
    </div>
    <div class="card-body">
      <table class="table table-striped table-sm table-data" width="100%">
        <thead>
          <tr>
            <th>{{ __('No') }}</th>
            <th>{{ __('Group Name') }}</th>
            <th>{{ __('Description') }}</th>
            <th>{{ __('Updated') }}</th>
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
            ajax : "{{ route('settings.privigroup.list') }}",
            order: [],
            columns : [
              {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
              {data: 'name', name: 'name', orderable: true, searchable: true},
              {data: 'description', name: 'description', orderable: true, searchable: true},
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