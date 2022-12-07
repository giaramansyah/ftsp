@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
  <div class="card">
    <form class="form-lazy-control" data-action="{{ $action }}">
      <div class="card-body">
        <div class="alert hidden" role="alert"></div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Tahun Ajaran<code>*</code></label>
          <div class="col-sm-2">
            <select class="form-control form-control-sm select2" name="division_id">
              <option value="">-- Silakan Pilih --</option>
              @foreach ($yearArr as $key => $value)
              <option value="{{ $value['id'] }}" {{ isset($year) && $year==$value['id'] ? 'selected' : '' }}>{{
                $value['name'] }}</option>
              @endforeach
            </select>
          </div>
          <label class="col-sm-2 col-form-label">Pilih. M.A.<code>*</code></label>
          <div class="col-sm-4 select-ma">
            <select class="form-control form-control-sm select2" name="ma_id" {{ isset($mandatory) &&
              $mandatory? 'required' : '' }}>
              <option value="">-- Silakan Pilih --</option>
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Jenis Transaksi<code>*</code></label>
          <div class="col-sm-2 select-ma">
            <select class="form-control form-control-sm select2" name="type_id" {{ isset($mandatory) &&
              $mandatory? 'required' : '' }}>
              <option value="">-- Silakan Pilih --</option>
              @foreach ($typeArr as $key => $value)
              <option value="{{ $value['id'] }}" {{ isset($type_id) && $type_id==$value['id'] ? 'selected' : '' }}>{{
                $value['name'] }}</option>
              @endforeach
            </select>
          </div>
          <label class="col-sm-2 col-form-label">No. M.A.<code>*</code></label>
          <div class="col-sm-2">
            <input type="text" class="form-control form-control-sm" maxlength="20" name="change_ma_id"
              value="{{ isset($change_ma_id) ? $change_ma_id : '' }}" readonly>
          </div>
          <div class="col-auto">
            <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success mt-2">
              <input type="checkbox" class="custom-control-input" id="customSwitch-is_changed" name="is_changed"
                value="1" {{ isset($is_changed) && $is_changed=1 ? 'checked' : '' }}>
              <label class="custom-control-label" for="customSwitch-is_changed">Ubah No. M.A.</label>
            </div>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Tgl. Transaksi<code>*</code></label>
          <div class="col-sm-2">
            <input type="date" class="form-control form-control-sm" max="{{ date('Y-m-d') }}" name="date_offer"
              value="{{ isset($date_offer) ? $date_offer : '' }}" {{ isset($mandatory) && $mandatory? 'required' : ''
              }}>
          </div>
          <label class="col-sm-2 col-form-label">Deskripsi. M.A</label>
          <div class="col-sm-4">
            <input type="text" class="form-control form-control-sm" maxlength="100" name="ma_description"
              value="{{ isset($ma_description) ? $ma_description : '' }}" readonly>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">No. Kas</label>
          <div class="col-sm-2">
            <input type="text" class="form-control form-control-sm" maxlength="15" name="offer_no"
              value="{{ isset($offer_no) ? $offer_no : '' }}" readonly {{ isset($mandatory) && $mandatory? 'required'
              : '' }}>
          </div>
          <label class="col-sm-2 col-form-label t">Total Dana. M.A.</label>
          <div class="col-sm-2">
            <div class="input-group input-group-sm">
              <div class="input-group-prepend">
                <span class="input-group-text text-bold">Rp</span>
              </div>
              <input type="text" class="form-control form-control-sm text-right text-bold" maxlength="20"
                name="validate_amount" value="{{ isset($ma_amount) ? $ma_amount : '' }}" readonly>
            </div>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">No. Surat<code>*</code></label>
          <div class="col-sm-2">
            <input type="text" class="form-control form-control-sm" maxlength="15" name="letter_no"
              value="{{ isset($letter_no) ? $letter_no : '' }}" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
          </div>
          <label class="col-sm-2 col-form-label">PIC<code>*</code></label>
          <div class="col-sm-2">
            @if($is_multi)
            <select class="form-control form-control-sm select2" name="staff_id" {{ isset($mandatory) &&
              $mandatory? 'required' : '' }}>
              <option value="">-- Silakan Pilih --</option>
              {{-- @foreach ($staffArr as $key => $value)
              <option value="{{ $value['id'] }}" {{ isset($staff_id) && $staff_id==$value['id'] ? 'selected' : '' }}>{{
                $value['name'] }}</option>
              @endforeach --}}
            </select>
            @else
            <input type="hidden" name="staff_id" value="{{ isset($staff_id) ? $staff_id : '' }}" {{ isset($mandatory) &&
              $mandatory? 'required' : '' }}>
            <input type="text" class="form-control form-control-sm" name="staff"
              value="{{ isset($staff) ? $staff : '' }}" readonly {{ isset($mandatory) && $mandatory? 'required' : '' }}>
            @endif
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Tgl. Surat<code>*</code></label>
          <div class="col-sm-2">
            <input type="date" class="form-control form-control-sm" max="{{ date('Y-m-d') }}" name="date_letter"
              value="{{ isset($date_letter) ? $date_letter : '' }}" {{ isset($mandatory) && $mandatory? 'required' : ''
              }}>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">A/N<code>*</code></label>
          <div class="col-sm-2">
            <input type="text" class="form-control form-control-sm" maxlength="50" name="name"
              value="{{ isset($name) ? $name : '' }}" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
          </div>

        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Deskripsi<code>*</code></label>
          <div class="col-sm-6">
            <input type="text" class="form-control form-control-sm" maxlength="100" name="description"
              value="{{ isset($description) ? $description : '' }}" {{ isset($mandatory) && $mandatory? 'required' : ''
              }}>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Subdeskripsi</label>
          <div class="col-sm-6">
            <textarea class="form-control form-control-sm" name="description"
              rows="4">{{ isset($description) ? $description : '' }}</textarea>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Jumlah Pengajuan<code>*</code></label>
          <div class="col-sm-2">
            <div class="input-group input-group-sm">
              <div class="input-group-prepend">
                <span class="input-group-text">Rp</span>
              </div>
              <input type="text" class="form-control form-control-sm text-right" maxlength="20" name="amount"
                value="{{ isset($amount) ? $amount : '' }}" onkeypress="preventAlpha(event)"
                onkeyup="numberFormat(this, true)" onblur="numberFormat(this, true)" {{ isset($mandatory) &&
                $mandatory? 'required' : '' }}>
            </div>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">No. Rekening<code>*</code></label>
          <div class="col-sm-2">
            <input type="text" class="form-control form-control-sm" maxlength="20" name="account_number"
              value="{{ isset($account_number) ? $account_number : '' }}" onkeypress="preventAlpha(event)"
              onkeyup="numberFormat(this, false)" onblur="numberFormat(this,false)" {{ isset($mandatory) &&
              $mandatory? 'required' : '' }}>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Terbilang<code>*</code></label>
          <div class="col-sm-6">
            <input type="text" class="form-control form-control-sm" maxlength="150" name="text_amount"
              value="{{ isset($text_amount) ? $text_amount : '' }}" {{ isset($mandatory) && $mandatory? 'required' : ''
              }}>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Tgl. Penyerahan<code>*</code></label>
          <div class="col-sm-2">
            <input type="date" class="form-control form-control-sm" name="date_apply"
              value="{{ isset($date_apply) ? $date_apply : '' }}" {{ isset($mandatory) && $mandatory? 'required' : ''
              }}>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <div class="form-button">
          <div class="row justify-content-center">
            <div class="col-sm-1">
              @include('partials.button.back', ['class' => 'btn-sm btn-block', 'action' =>
              route('transaction.offer.index')])
            </div>
            <div class="col-sm-1">
              @include('partials.button.submit')
            </div>
            <div class="col-sm-1">
              @include('partials.button.reset', ['class' => 'btn-sm btn-block', 'action' =>
              route('transaction.offer.index')])
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