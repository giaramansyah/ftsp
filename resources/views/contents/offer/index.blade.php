@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header">
      @if($is_create)
        <div class="card-tools">
          @include('partials.button.add', ['action' => route('master.data.add')])
        </div>
      @endif
    </div>
    <div class="card-body">
      <div class="form-group row justify-content-center">
        <label class="col-sm-2 col-form-label">{{ __('Tahun Akademik') }}<code>*</code></label>
        <div class="col-sm-2">
          <select class="form-control form-control-sm select2" name="division_id">
            <option value="">-- Silakan Pilih --</option>
            @foreach ($yearArr as $key => $value)
            <option value="{{ Secure::secure($value['id']) }}" {{ isset($year) && $year == $value['id'] ? 'selected' : '' }}>{{ $value['name'] }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <ul class="nav nav-tabs justify-content-center mb-2" role="tablist">
        @foreach ($divisionArr as $key => $value)
          <li class="nav-item">
            <a class="nav-link {{ $key == 0 ? 'active' : '' }}" data-toggle="pill" href="#tab-content-{{ $value['id']}}" role="tab">
              {{ $value['name'] }}
            </a>
          </li>
        @endforeach
      </ul>
      <div class="tab-content">
        @foreach ($divisionArr as $key => $value)
        <div class="tab-pane fade {{ $key == 0 ? 'show active' : '' }}" id="tab-content-{{ $value['id'] }}" role="tabpanel">
          <table id="table{{ $value['id'] }}" class="table table-striped table-sm table-data" data-id="{{ Secure::pack(array('year' => $year, 'id' => $value['id'])) }}" width="100%">
            <thead>
              <tr>
                <th>{{ __('No') }}</th>
                <th>{{ __('No. M.A') }}</th>
                <th>{{ __('Keterangan') }}</th>
                <th>{{ __('PIC') }}</th>
                <th>{{ __('Total Dana') }}</th>
                <th>{{ __('Updated') }}</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@include('partials.modal.modaldelete')
@endsection
@section('push-js')
<script type="text/javascript">
  $('.select2').select2({theme: 'bootstrap4'});

  $('.select2').on('change', function(){
    window.location.href = "{{ route('master.data.index') }}/"+$(this).val()
  })

  $('.table-data').each(function(){
    var table = $(this);
    var id = table.data('id')
    table.DataTable(
      {
        responsive: true,
        autoWidth: true,
        processing: true,
        ajax : {
          method : 'GET',
          url : "{{ route('master.data.list') }}",
          data : {
            id : id
          }
        },
        order: [],
        columns : [
          {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
          {data: 'ma', name: 'ma', orderable: true, searchable: true},
          {data: 'description', name: 'description', orderable: true, searchable: true},
          {data: 'staff', name: 'staff', orderable: true, searchable: true},
          {data: 'amount', name: 'amount', orderable: true, searchable: true, class: 'text-right text-bold'},
          {data: 'updated_at', name: 'updated_at', orderable: true, searchable: true},
        ]
      }
    );
  });
  
</script>
@endsection