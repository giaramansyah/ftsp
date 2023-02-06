@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-6">
      <div class="card">
        <div class="card-body box-profile">
          <h3 class="profile-username text-center mb-0">No. Kas</h3>
          <h3 class="profile-username text-center">{{ $reception_id }}</h3>
          <p class="text-muted text-center mb-0">Tahun Akademik</p>
          <p class="text-muted text-center">{{ $years }}</p>
          <ul class="list-group list-group-unbordered mb-3">
            <li class="list-group-item">
              <b>Tgl. Penerimaan</b>
              <p class="float-right mb-0">{{ $reception_date_format }}</p>
            </li>
            <li class="list-group-item">
              <b>Unit</b>
              <p class="float-right mb-0">{{ $division }}</p>
            </li>
            @if($from_ma)
            <li class="list-group-item">
              <b>No. M.A.</b>
              <p class="float-right mb-0">{{ $ma_id }}</p>
            </li>
            <li class="list-group-item">
              <b>PIC</b>
              <p class="float-right mb-0">{{ $staff }}</p>
            </li>
            @endif
            <li class="list-group-item">
              <b>Deskripsi</b>
              <p class="float-right mb-0">{{ $description }}</p>
            </li>
            <li class="list-group-item">
              <b>Sub Deskripsi</b>
              <p class="float-right mb-0">{{ $sub_description }}</p>
            </li>
            <li class="list-group-item">
              <b>Atas Nama</b>
              <p class="float-right mb-0">{{ $name_desc }}</p>
            </li>
            <li class="list-group-item">
              <b>Jml. Penerimaan</b>
              <p class="float-right mb-0 text-bold">Rp {{ $amount }}</p>
            </li>
            <li class="list-group-item">
              <b>Tgl. Diubah</b>
              <p class="float-right mb-0">{{ $updated_by }} On {{ $updated_at }}</p>
            </li>
          </ul>
        </div>
        <div class="card-footer">
          <div class="row justify-content-center">
            <div class="col-auto">
              @include('partials.button.back', array('class' => 'btn-sm', 'action' =>
              route('transaction.reception.index')))
            </div>
            @if($is_update)
            <div class="col-auto">
              @include('partials.button.edit', array('class' => 'btn-sm', 'action' => route('transaction.reception.edit',
              ['id'
              => $id])))
            </div>
            <div class="col-auto">
              <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#modalPrintGreen">
                <i class="fas fa-print"></i> Cetak Bon
              </button>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
    @if($from_ma)
    <div class="col-sm-6">
      <div class="card">
        <div class="card-body box-profile">
          <h3 class="profile-username text-center mb-0">No. M.A</h3>
          <h3 class="profile-username text-center">{{ $data['ma_id'] }}</h3>
          <p class="text-muted text-center mb-0">Tahun Akademik</p>
          <p class="text-muted text-center">{{ $data['years'] }}</p>
          <ul class="list-group list-group-unbordered mb-3">
            <li class="list-group-item">
              <b>{{ __('Deskripsi') }}</b>
              <p class="float-right mb-0">{!! $data['description'] !!}</p>
            </li>
            <li class="list-group-item">
              <b>{{ __('PIC') }}</b>
              <p class="float-right mb-0">{{ $data['staff'] }}</p>
            </li>
            <li class="list-group-item">
              <b>{{ __('Unit') }}</b>
              <p class="float-right mb-0">{{ $data['division'] }}</p>
            </li>
            <li class="list-group-item">
              <b>{{ __('Total Dana') }}</b>
              <p class="float-right mb-0 text-bold">Rp {{ $data['amount'] }}</p>
            </li>
          </ul>
        </div>
      </div>
    </div>
    @endif
  </div>
</div>
@include('partials.modal.modalprintgreen', ['modal_action' => route('transaction.reception.print', ['id' => $id]),
'employeeArr' => $employeeArr])
@endsection
@section('push-js')
<script type="text/javascript">
  $('.select2').select2({theme: 'bootstrap4'});
</script>
@endsection