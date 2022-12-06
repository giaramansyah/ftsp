@extends('layouts/main')
@section('title', 'Detail Data')
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-4">
      <div class="card">
        <div class="card-body box-profile">
          <h3 class="profile-username text-center mb-0">No. M.A</h3>
          <h3 class="profile-username text-center">{{ $ma_id }}</h3>
          <p class="text-muted text-center mb-0">Tahun Ajaran</p>
          <p class="text-muted text-center">{{ $years }}</p>
          <ul class="list-group list-group-unbordered mb-3">
            <li class="list-group-item">
              <b>{{ __('Deskripsi') }}</b>
              <p class="float-right mb-0">{{ $description }}</p>
            </li>
            <li class="list-group-item">
              <b>{{ __('PIC') }}</b>
              <p class="float-right mb-0">{{ $staff }}</p>
            </li>
            <li class="list-group-item">
              <b>{{ __('Unit') }}</b>
              <p class="float-right mb-0">{{ $division }}</p>
            </li>
            <li class="list-group-item">
              <b>{{ __('Total Dana') }}</b>
              <p class="float-right mb-0">Rp {{ $amount }}</p>
            </li>
          </ul>
          <p class="float-right mb-0 small text-muted">last updated {{ $updated_at }}</p>
        </div>
        <div class="card-footer">
          <div class="row justify-content-center">
            <div class="col-auto">
              @include('partials.button.back', array('class' => 'btn-sm', 'action' => route('master.data.index')))
            </div>
            <div class="col-auto">
              @include('partials.button.edit', array('class' => 'btn-sm', 'action' => route('master.data.edit', ['id' => $id])))
            </div>
            <div class="col-auto">
              @include('partials.button.delete', array('class' => 'btn-sm', 'action' => route('master.data.post', ['action' => config('global.action.form.delete'), 'id' => $id])))
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-8">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-sm-6">
              <h4 class="card-title mb-0 text-bold">{{ __('History') }}</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection