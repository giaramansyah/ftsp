@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-6">
      <div class="card">
        <div class="card-body box-profile">
          <h3 class="profile-username text-center mb-0">No. Surat</h3>
          <h3 class="profile-username text-center">{{ $reff_no }}</h3>
          <p class="text-muted text-center mb-0">No. Kas</p>
          <p class="text-muted text-center">{{ $expense_id }}</p>
          <p class="text-md badge {{ $is_red ? 'badge-danger' : 'badge-secondary' }}">{{
            $status_desc }}</p>
          <ul class="list-group list-group-unbordered mb-3">
            <li class="list-group-item">
              <b>Tgl. Transaksi</b>
              <p class="float-right mb-0">{{ $expense_date_format }}</p>
            </li>
            <li class="list-group-item">
              <b>Tgl. Surat</b>
              <p class="float-right mb-0">{{ $reff_date_format }}</p>
            </li>
            @if($ma_id != $data['ma_id'])
            <li class="list-group-item">
              <b>No. M.A. (Perubahan)</b>
              <p class="float-right mb-0">{{ $ma_id }}</p>
            </li>
            @endif
            <li class="list-group-item">
              <b>PIC</b>
              <p class="float-right mb-0">{{ $staff }}</p>
            </li>
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
              <b>No. Rekening</b>
              <p class="float-right mb-0">{{ $account }}</p>
            </li>
            <li class="list-group-item">
              <b>Jml. Transaksi</b>
              <p class="float-right mb-0 text-bold">Rp {{ $amount }}</p>
            </li>
            @if($is_red)
            <li class="list-group-item">
              <b>Tgl. Penyerahan</b>
              <p class="float-right mb-0">{{ $apply_date_format }}</p>
            </li>
            <li class="list-group-item">
              <b>Lap. Pertaggung Jawaban</b>
              <p class="float-right mb-0">
                @if($image_exist)
                  <a href="{{ $download }}" rel="noopener noreferrer nofollow" target="_blank"
                    title="Download File LPJ">{{ $image }}</a>
                @else
                  <i class="fas fa-exclamation-circle text-danger" title="File Not Found"></i><span class="text-muted">
                    {{ $image }}
                  </span>
                @endif
              </p>
            </li>
            @endif
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
              route('transaction.expense.index')))
            </div>
            @if($is_update)
            <div class="col-auto">
              @include('partials.button.edit', array('class' => 'btn-sm', 'action' => route('transaction.expense.edit',
              ['id'
              => $id])))
            </div>
            @if($status == config('global.status.code.finished'))
            <div class="col-auto">
              <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#modalPrintRed">
                <i class="fas fa-print"></i> Cetak Bon Merah
              </button>
            </div>
            @endif
            @if($type == config('global.type.code.white'))
            <div class="col-auto">
              <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#modalPrintWhite">
                <i class="fas fa-print"></i> Buat UMD
              </button>
            </div>
            @endif
            @endif
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6">
      <div class="card">
        <div class="card-body box-profile">
          <h3 class="profile-username text-center mb-0">No. M.A</h3>
          <h3 class="profile-username text-center">{{ $data['ma_id'] }}</h3>
          <p class="text-muted text-center mb-0">Tahun Akademik</p>
          <p class="text-muted text-center">{{ $data['years'] }}</p>
          @if($is_multiple == 1)
          <p class="text-md badge badge-info">Multi Anggaran</p>
          @endif
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
  </div>
</div>
@include('partials.modal.modalprintred', ['modal_action' => route('transaction.expense.print', ['id' => $id]),
'employeeArr' => $employeeArr])
@include('partials.modal.modalprintwhite', ['modal_action' => route('transaction.expense.print', ['id' => $id]),
'employeeArr' => $employeeArr])
@endsection
@section('push-js')
<script type="text/javascript">
  $('.select2').select2({theme: 'bootstrap4'});
</script>
@endsection