@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
  <div class="card">
    <form class="form-lazy-control" data-action="{{ $action }}">
      <div class="card-body">
        <div class="alert hidden" role="alert"></div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Unit<code>*</code></label>
          <div class="col-sm-2">
            @if(!isset($id))
            <select class="form-control form-control-sm select2" name="division_id" {{ isset($mandatory) &&
              $mandatory? 'required' : '' }}>
              <option value="">-- Silakan Pilih --</option>
              @foreach ($divisionArr as $key => $value)
              <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
              @endforeach
            </select>
            @else
            <input type="text" class="form-control form-control-sm" name="division" value="{{ isset($division) ? $division : '' }}" readonly>
            @endif
          </div>
        </div>
        @if(isset($id))
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Transaksi<code>*</code></label>
          <div class="col-sm-2">
            <select class="form-control form-control-sm select2" name="transaction_id" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
              <option value="">-- Silakan Pilih --</option>
              @foreach ($transactionArr as $key => $value)
              <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
              @endforeach
            </select>
          </div>
        </div>
        @endif
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Nominal Saldo<code>*</code></label>
          <div class="col-sm-2">
            <div class="input-group input-group-sm">
              <div class="input-group-prepend">
                <span class="input-group-text">Rp</span>
              </div>
              <input type="text" class="form-control form-control-sm text-right" maxlength="20" name="amount"
                value="0" onkeypress="preventAlpha(event)"
                onkeyup="numberFormat(this, true)" onblur="numberFormat(this, true)" {{ isset($mandatory) &&
                $mandatory? 'required' : '' }}>
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <div class="form-button">
          <div class="row justify-content-center">
            <div class="col-sm-1">
              @include('partials.button.back', ['class' => 'btn-sm btn-block', 'action' =>
              route('master.balance.index')])
            </div>
            <div class="col-sm-1">
              @include('partials.button.submit')
            </div>
          </div>
        </div>
        <div class="form-loading">
          <img src="{{ asset('img/loading.gif') }}" height="40">
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
@section('push-js')
<script type="text/javascript">
  $('.select2').select2({theme: 'bootstrap4'});
</script>
@endsection