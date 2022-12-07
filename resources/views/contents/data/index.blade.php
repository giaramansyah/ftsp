@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header">
      @if($is_create)
      <div class="card-tools">
        @include('partials.button.upload', ['action' => route('master.data.add')])
      </div>
      @endif
    </div>
    <div class="card-body">
      <div class="form-group row justify-content-center">
        <label class="col-sm-2 col-form-label">{{ __('Tahun Ajaran') }}<code>*</code></label>
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
                <th>No. M.A</th>
                <th>Keterangan</th>
                <th>PIC</th>
                <th>Total Dana</th>
                <th>Dana Terpakai</th>
                <th>Sisa Dana</th>
                <th>Realisasi</th>
                <th>Updated</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
              <tr>
                <th></th>
                <th></th>
                <th></th>
                <th class="text-right">Total </th>
                <th class="text-right"></th>
                <th class="text-right"></th>
                <th class="text-right"></th>
                <th class="text-center"></th>
                <th></th>
              </tr>
            </tfoot>
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
          {data: 'used', name: 'used', orderable: true, searchable: true, class: 'text-right text-bold'},
          {data: 'remain', name: 'remain', orderable: true, searchable: true, class: 'text-right text-bold'},
          {data: 'percent', name: 'percent', orderable: true, searchable: true, class: 'text-center text-bold'},
          {data: 'updated_at', name: 'updated_at', orderable: true, searchable: true},
        ],
        footerCallback: function (row, data, start, end, display) {
          var api = this.api();
          
          if(api.rows( { page: 'current' } ).any()) {
              var intVal = function (i) {
                  return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
              };

              totalAmount = api.column(4).data().reduce(function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0);

              totalUsed = api.column(5).data().reduce(function (a, b) {
                  return intVal(a) + intVal(b);
              }, 0);
  
              $(api.column(4).footer()).html(formatCurrency(totalAmount));
              $(api.column(5).footer()).html(formatCurrency(totalUsed));
              $(api.column(6).footer()).html(formatCurrency(totalAmount-totalUsed));
              $(api.column(7).footer()).html((totalUsed/totalAmount*100).toFixed(2)+'%');
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