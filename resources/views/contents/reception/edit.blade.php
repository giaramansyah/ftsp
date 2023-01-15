@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
  <div class="card">
    <form class="form-lazy-control" data-action="{{ $action }}" data-method="post">
      <div class="card-body">
        <input type="hidden" name="type" value="{{ isset($type) ? $type : '' }}">
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Tahun Akademik<code>*</code></label>
          <div class="col-sm-2">
            <input type="text" class="form-control form-control-sm"
              value="{{ isset($years) && $years ? $years : '' }}" readonly>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Unit<code>*</code></label>
          <div class="col-sm-2">
            <input type="hidden" name="division_id" value="{{ isset($division_id) && $division_id ? $division_id : '' }}">
            <input type="text" class="form-control form-control-sm"
              value="{{ isset($division) && $division ? $division : '' }}" readonly>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">No. Kas<code>*</code></label>
          <div class="col-sm-2">
            <input type="text" class="form-control form-control-sm" name="reception_id"
              value="{{ isset($reception_id) ? $reception_id : '' }}" readonly {{ isset($mandatory) &&
              $mandatory? 'required' : '' }}>
          </div>
          <label class="col-sm-2 offset-sm-1 col-form-label">Tgl. Penerimaan<code>*</code></label>
          <div class="col-sm-2">
            <input type="date" class="form-control form-control-sm" name="reception_date"
              value="{{ isset($reception_date) ? $reception_date : '' }}" {{ isset($mandatory) && $mandatory? 'required'
              : '' }}>
          </div>
        </div>
        @if($from_ma)
        <div class="form-group row mb-0">
          <label class="col-sm-2 col-form-label">Mata Anggaran<code>*</code></label>
          <div class="col-sm-2">
            <input type="hidden" name="data_id" value="{{ isset($data_id) ? $data_id : '' }}">
            <input type="text" class="form-control form-control-sm" name="ma_id"
              value="{{ isset($ma_id) ? $ma_id : '' }}" readonly {{ isset($mandatory) && $mandatory? 'required' : '' }}>
          </div>
          <div class="col-sm-2">
            <div class="form-check pt-2">
              <input class="form-check-input" type="checkbox" name="edt_ma_id" value="1" onchange="editMa()">
              <label class="form-check-label">Ubah No. M.A.</label>
            </div>
          </div>
        </div>
        <div class="form-group row">
          <div class="offset-sm-2 col-sm-10">
            <table class="table table-sm table-bordered" width="100%">
              <thead>
                <tr>
                  <th class="text-center">No. M.A.</th>
                  <th class="text-center">Deskripsi</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>{{ $data['ma_id'] }}</td>
                  <td>{{ $data['description'] }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        @endif
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Deskripsi<code>*</code></label>
          <div class="col-sm-7">
            <input type="text" class="form-control form-control-sm" name="description"
              value="{{ isset($description) && $description ? $description : '' }}" {{ isset($mandatory) &&
              $mandatory? 'required' : '' }}>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Sub Deskripsi</label>
          <div class="col-sm-7">
            <textarea class="form-control form-control-sm" name="sub_description"
              rows="4">{{ isset($sub_description) ? $sub_description : '' }}</textarea>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">A/N<code>*</code></label>
          <div class="col-sm-2">
            <select class="form-control form-control-sm select2" name="name" {{ isset($mandatory) &&
              $mandatory? 'required' : '' }}>
              <option value="">-- Silakan Pilih --</option>
              @foreach ($employeeArr as $key => $value)
              <option value="{{ $value['id'] }}" {{ isset($name) && $name == $value['id'] ? 'selected' : '' }}>{{
                $value['name'] }}</option>
              @endforeach
            </select>
          </div>
          @if($from_ma)
          <label class="col-sm-2 offset-sm-1 col-form-label">PIC<code>*</code></label>
          <div class="col-sm-2">
            <select class="form-control form-control-sm select2" name="staff_id" {{ isset($mandatory) &&
              $mandatory? 'required' : '' }}>
              <option value="">-- Silakan Pilih --</option>
              @foreach ($staffArr as $value)
              <option value="{{ $value['id'] }}" {{ isset($staff_id) && $staff_id==$value['id'] ? 'selected' : '' }}>{{
                $value['name'] }}</option>
              @endforeach
            </select>
          </div>
          @endif
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Nilai Penerimaan<code>*</code></label>
          <div class="col-sm-2">
            <div class="input-group input-group-sm">
              <div class="input-group-prepend">
                <span class="input-group-text">Rp</span>
              </div>
              <input type="text" class="form-control form-control-sm text-right" maxlength="20" name="amount"
                value="{{ isset($amount) && $amount ? $amount : '' }}" onkeypress="preventAlpha(event)"
                onkeyup="numberFormat(this, true)" onblur="numberFormat(this, true);amountText(this.value, '#text_amount')" {{ isset($mandatory) &&
                $mandatory? 'required' : '' }}>
            </div>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Terbilang<code>*</code></label>
          <div class="col-sm-7">
            <input type="text" class="form-control form-control-sm" name="text_amount"
              value="{{ isset($text_amount) && $text_amount ? $text_amount : '' }}" {{ isset($mandatory) &&
              $mandatory? 'required' : '' }} readonly>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <div class="form-button">
          <div class="row justify-content-center">
            <div class="col-sm-1">
              @include('partials.button.back', ['class' => 'btn-sm btn-block', 'action' =>
              route('transaction.reception.index')])
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
  
  function editMa() {
    if($('input[name="edt_ma_id"]').is(':checked') && $('input[name="ma_id"]').val() != '') {
      $('input[name="ma_id"]').attr('readonly', false)
    } else {
      $('input[name="ma_id"]').attr('readonly', true)
    }
  }
</script>
@endsection