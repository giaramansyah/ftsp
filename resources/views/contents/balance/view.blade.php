@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-4">
      <div class="card">
        <div class="card-body box-profile">
          <h3 class="profile-username text-center">{{ $division }}</h3>
          <p class="text-muted text-center">Rp {{ $amount }}</p>
          <ul class="list-group list-group-unbordered mb-3">
            <li class="list-group-item">
              <b>Tgl. Diubah</b>
              <p class="float-right mb-0">{{ $updated_by }} On {{ $updated_at }}</p>
            </li>
          </ul>
        </div>
        <div class="card-footer">
          <div class="row justify-content-center">
            <div class="col-auto">
              @include('partials.button.back', array('class' => 'btn-sm', 'action' => route('master.balance.index')))
            </div>
            @if($is_update)
            <div class="col-auto">
              @include('partials.button.edit', array('class' => 'btn-sm', 'action' => route('master.balance.edit', ['id'
              => $id])))
            </div>
            @endif
            @if($is_delete)
            <div class="col-auto">
              @include('partials.button.delete', array('class' => 'btn-sm', 'source' => 'database', 'action' =>
              route('master.balance.post',
              ['action' => config('global.action.form.delete'), 'id' => $id])))
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-8">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-sm-6">
              <h4 class="card-title mb-0 text-bold">{{ __('Mustasi') }}</h4>
            </div>
          </div>
        </div>
        <div class="card-body">
          <table class="table table-sm" width="100%">
            <thead>
              <tr>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Keterangan</th>
                <th class="text-center">Debit/Kredit</th>
                <th class="text-center">Jumlah</th>
                <th class="text-center">Saldo</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($histories as $key => $value)
              <tr>
                <td>{{ $value['created_at'] }}</td>
                <td>{{ $value['description'] }}</td>
                <td class="text-center">{{ $value['transaction'] }}</td>
                <td class="text-right">{{ $value['amount'] }}</td>
                <td class="text-right">{{ $value['balance'] }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@include('partials.modal.modaldelete')
@endsection
@section('push-js')
<script type="text/javascript">
  $('.table').DataTable({
      order: [[0, 'desc']],
  });
</script>
@endsection