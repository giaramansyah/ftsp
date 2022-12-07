@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-body">
      <table class="table table-striped table-sm table-data" width="100%">
        <thead>
          <tr>
            <th>{{ __('Timestamps') }}</th>
            <th>{{ __('Username') }}</th>
            <th>{{ __('Activity') }}</th>
            <th>{{ __('Description') }}</th>
            <th>{{ __('IP Address') }}</th>
            <th>{{ __('Agent/Browser') }}</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
@section('push-js')
  <script type="text/javascript">
    $('.table-data').DataTable(
        {
            responsive: true,
            autoWidth: true,
            processing: true,
            ajax : "{{ route('logs.activity.list') }}",
            order: [],
            columns : [
              {data: 'updated_at', name: 'updated_at', orderable: false, searchable: false},
              {data: 'username', name: 'username', orderable: true, searchable: true},
              {data: 'privilege', name: 'privilege', orderable: true, searchable: true},
              {data: 'description', name: 'description', orderable: true, searchable: true},
              {data: 'ip_address', name: 'ip_address', orderable: true, searchable: true},
              {data: 'agent', name: 'agent', orderable: false, searchable: false},
            ],
        }
    );
  </script>
@endsection