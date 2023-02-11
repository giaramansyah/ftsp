@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-6">
      <div class="card">
        <div class="card-body box-profile">
          <h3 class="profile-username text-center mb-0">RAB</h3>
          <p class="text-muted text-center mb-0">Tahun Anggaran</p>
          <p class="text-muted text-center">{{ $years }}</p>
          <ul class="list-group list-group-unbordered mb-3">
            <li class="list-group-item">
              <b>Unit</b>
              <p class="float-right mb-0">{{ $division }}</p>
            </li>
            <li class="list-group-item">
              <b>PIC</b>
              <p class="float-right mb-0">{{ $staff }}</p>
            </li>
            <li class="list-group-item">
              <b>No. M.A.</b>
              <p class="float-right mb-0">{{ $ma_id }}</p>
            </li>
            <li class="list-group-item">
              <b>Program</b>
              <p class="float-right mb-0">{{ $program }}</p>
            </li>
            <li class="list-group-item">
              <b>Dana</b>
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
              route('master.note.index')))
            </div>
            @if($is_update)
            <div class="col-auto">
              @include('partials.button.edit', array('class' => 'btn-sm', 'action' => route('master.note.edit',
              ['id'
              => $id])))
            </div>
            @if($status == config('global.status.code.unfinished'))
            <div class="col-auto">
              <button type="button" class="btn btn-outline-success btn-sm" data-toggle="modal"
                data-target="#modalUpdate">
                <i class="fas fa-pen-to-square"></i> Update Status
              </button>
            </div>
            @endif
            @endif
            @if($is_delete)
            <div class="col-auto">
              @include('partials.button.delete', array('class' => 'btn-sm', 'source' => 'database', 'action' =>
              route('master.note.post', ['action' => config('global.action.form.delete'), 'id' => $id])))
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6">
      <div class="card">
        <div class="card-body box-profile">
          <h3 class="profile-username text-center mb-0">Pengajuan</h3>
          <p class="text-muted text-center mb-0">No. Surat</p>
          <p class="text-muted text-center">{{ $note_reff }}</p>
          <ul class="list-group list-group-unbordered mb-3">
            <li class="list-group-item">
              <b>{{ __('Tgl. Surat') }}</b>
              <p class="float-right mb-0">{{ $note_date_format }}</p>
            </li>
            <li class="list-group-item">
              <b>{{ __('Tgl. Upload Surat') }}</b>
              <p class="float-right mb-0">{{ $note_upload_format }}</p>
            </li>
            <li class="list-group-item">
              <b>{{ __('Perihal') }}</b>
              <p class="float-right mb-0">{{ $regarding }}</p>
            </li>
            <li class="list-group-item">
              <b>{{ __('Link URL') }}</b>
              <p class="float-right mb-0"><a href="{{ $link_url }}" target="_blank">{{ $link_url }}</a></p>
            </li>
            <li class="list-group-item">
              <b>{{ __('Pengajuan Dana') }}</b>
              <p class="float-right mb-0 text-bold">Rp {{ $amount_requested }}</p>
            </li>
            <li class="list-group-item">
              <b>{{ __('Realisasi Dana') }}</b>
              <p class="float-right mb-0 text-bold">{{ $amount_approved == '0' ? '-' : 'Rp '.$amount_approved }}</p>
            </li>
            <li class="list-group-item">
              <b>{{ __('Selisih Dana') }}</b>
              <p class="float-right mb-0 text-bold">{{ $amount_difference == '-' ? '-' : 'Rp '.$amount_difference }}</p>
            </li>
            <li class="list-group-item">
              <b>{{ __('Sisa Dana') }}</b>
              <p class="float-right mb-0 text-bold">{{ $ammount_remain == '-' ? '-' : 'Rp '.$ammount_remain }}</p>
            </li>
            <li class="list-group-item">
              <b>{{ __('Status') }}</b>
              <p
                class="float-right mb-0 text-bold badge {{ $status == config('global.status.code.unfinished') ? 'badge-danger' : 'badge-success' }}">
                {{ $status_desc }}</p>
            </li>
          </ul>
        </div>
        <div class="card-footer">
        </div>
      </div>
    </div>
  </div>
</div>
@include('partials.modal.modaldelete')
@include('partials.modal.modalupdate')
@endsection
@section('push-js')
<script type="text/javascript">
  $('.select2').select2({theme: 'bootstrap4'});
</script>
@endsection