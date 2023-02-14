@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header">
      <div class="card-tools">
        @include('partials.button.print', ['class' => '', 'label' => 'Export Excel', 'action' => route('master.note.export', ['year' => Secure::secure($year)])])
        @if($is_create)
        @include('partials.button.add', ['action' => route('master.note.add')])
        @endif
      </div>
    </div>
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
      <ul class="nav nav-tabs justify-content-center mb-2" role="tablist">
        @foreach ($divisionArr as $key => $value)
        <li class="nav-item">
          <a class="nav-link {{ $key == 0 ? 'active' : '' }}" data-toggle="pill" href="#tab-content-{{ $value['id']}}"
            role="tab">
            {{ $value['name'] }}
          </a>
        </li>
        @endforeach
      </ul>
      <div class="tab-content">
        @foreach ($divisionArr as $key => $value)
        <div class="tab-pane fade {{ $key == 0 ? 'show active' : '' }}" id="tab-content-{{ $value['id'] }}"
          role="tabpanel">
          <table id="table{{ $value['id'] }}" class="table table-striped table-sm table-data"
            data-id="{{ Secure::pack(array('year' => $year, 'id' => $value['id'])) }}" width="100%">
            <thead>
              <tr>
                <th>No</th>
                <th>PIC</th>
                <th>No. MA</th>
                <th>Program</th>
                <th>Dana RAB</th>
                <th>No. Surat</th>
                <th>Tgl. Surat</th>
                <th>Pengajuan</th>
                <th>Realisasi</th>
                <th>Status</th>
                <th>Updated</th>
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
    window.location.href = "{{ route('master.note.index') }}/"+$(this).val()
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
          url : "{{ route('master.note.list') }}",
          data : {
            id : id
          }
        },
        order: [],
        columns : [
          {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
          {data: 'staff_export', name: 'staff_export', orderable: true, searchable: true},
          {data: 'ma', name: 'ma', orderable: true, searchable: true},
          {data: 'program', name: 'program', orderable: true, searchable: true, class: 'text-wrap'},
          {data: 'amount', name: 'amount', orderable: true, searchable: true, class: 'text-right text-bold'},
          {data: 'note_reff', name: 'program', orderable: true, searchable: true},
          {data: 'note_date', name: 'program', orderable: true, searchable: true},
          {data: 'amount_requested', name: 'amount_requested', orderable: true, searchable: true, class: 'text-right text-bold'},
          {data: 'approved', name: 'approved', orderable: true, searchable: true, class: 'text-right text-bold'},
          {data: 'status_desc', name: 'status_desc', orderable: true, searchable: true},
          {data: 'updated_at', name: 'updated_at', orderable: true, searchable: true},
        ]
      }
    );
  });
  
</script>
@endsection