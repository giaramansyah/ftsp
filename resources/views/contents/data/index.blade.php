@extends('layouts/main')
@section('title', 'Data')
@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header">
      <div class="card-tools">
        @include('partials.button.add', ['action' => route('master.data.add')])
      </div>
    </div>
    <div class="card-body">
      <table class="table table-striped table-sm table-data" width="100%">
        <thead>
          <tr>
            <th>{{ __('No') }}</th>
            <th>{{ __('Tahun Ajaran') }}</th>
            <th>{{ __('No. M.A') }}</th>
            <th>{{ __('Keterangan') }}</th>
            <th>{{ __('PIC') }}</th>
            <th>{{ __('Unit') }}</th>
            <th>{{ __('Total Dana') }}</th>
            <th>{{ __('Updated') }}</th>
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
            ajax : "{{ route('master.data.list') }}",
            order: [],
            columns : [
              {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
              {data: 'years', name: 'years', orderable: true, searchable: true},
              {data: 'ma', name: 'ma', orderable: true, searchable: true},
              {data: 'description', name: 'description', orderable: true, searchable: true},
              {data: 'staff', name: 'staff', orderable: true, searchable: true},
              {data: 'division', name: 'division', orderable: true, searchable: true},
              {data: 'amount', name: 'amount', orderable: true, searchable: true, class: 'text-right text-bold'},
              {data: 'updated_at', name: 'updated_at', orderable: true, searchable: true},
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