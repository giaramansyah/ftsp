@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
  <div class="card">
    <form class="form-lazy-control" data-action="{{ $action }}" data-method="upload" data-validate="max_amount,image">
      <div class="card-body">
        <input type="hidden" name="type" value="{{ isset($type) ? $type : '' }}">
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Tahun Ajaran<code>*</code></label>
          <div class="col-sm-2">
            <input type="text" class="form-control form-control-sm"
              value="{{ isset($data['years']) && $data['years'] ? $data['years'] : '' }}" readonly>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Unit<code>*</code></label>
          <div class="col-sm-2">
            <input type="hidden" name="division_id" value="{{ isset($data['division_id']) && $data['division_id'] ? $data['division_id'] : '' }}">
            <input type="text" class="form-control form-control-sm"
              value="{{ isset($data['division']) && $data['division'] ? $data['division'] : '' }}" readonly>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">No. Kas<code>*</code></label>
          <div class="col-sm-2">
            <input type="text" class="form-control form-control-sm" name="expense_id"
              value="{{ isset($expense_id) ? $expense_id : '' }}" readonly {{ isset($mandatory) &&
              $mandatory? 'required' : '' }}>
          </div>
          <label class="col-sm-2 offset-sm-1 col-form-label">Tgl. Transaksi<code>*</code></label>
          <div class="col-sm-2">
            <input type="date" class="form-control form-control-sm" name="expense_date"
              value="{{ isset($expense_date) ? $expense_date : '' }}" {{ isset($mandatory) && $mandatory? 'required'
              : '' }}>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">No. Surat<code>*</code></label>
          <div class="col-sm-2">
            <input type="text" class="form-control form-control-sm" name="reff_no"
              value="{{ isset($reff_no) ? $reff_no : '' }}" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
          </div>
          <label class="col-sm-2 offset-sm-1 col-form-label">Tgl. Surat<code>*</code></label>
          <div class="col-sm-2">
            <input type="date" class="form-control form-control-sm" name="reff_date"
              value="{{ isset($reff_date) ? $reff_date : '' }}" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
          </div>
        </div>
        <div class="form-group row mb-0">
          <label class="col-sm-2 col-form-label">Mata Anggaran<code>*</code></label>
          <div class="col-sm-2">
            <input type="hidden" name="data_id" value="{{ isset($data_id) ? $data_id : '' }}">
            <input type="hidden" name="validate_max" id="validate_max"
              value="{{ isset($data['available']) ? $data['available'] : '' }}">
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
                  <th class="text-center">Total Dana</th>
                  <th class="text-center">Sisa Dana</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>{{ $data['ma_id'] }}</td>
                  <td>{{ $data['description'] }}</td>
                  <td>{{ $data['amount'] }}</td>
                  <td>{{ $data['remain'] }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
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
            <input type="text" class="form-control form-control-sm" name="name"
              value="{{ isset($name) && $name ? $name : '' }}" {{ isset($mandatory) && $mandatory? 'required' : '' }}>
          </div>
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
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Nilai Transaksi<code>*</code></label>
          <div class="col-sm-2">
            <div class="input-group input-group-sm">
              <div class="input-group-prepend">
                <span class="input-group-text">Rp</span>
              </div>
              <input type="text" class="form-control form-control-sm text-right" maxlength="20" name="amount"
                value="{{ isset($amount) && $amount ? $amount : '' }}" onkeypress="preventAlpha(event)"
                onkeyup="numberFormat(this, true)" onblur="numberFormat(this, true)" {{ isset($mandatory) &&
                $mandatory? 'required' : '' }}>
            </div>
          </div>
          <label class="col-sm-2 offset-sm-1 col-form-label">No. Rekening<code>*</code></label>
          <div class="col-sm-2">
            <input type="text" class="form-control form-control-sm" maxlength="20" name="account"
              value="{{ isset($account) && $account ? $account : '' }}" onkeypress="preventAlpha(event)"
              onkeyup="numberFormat(this, false)" onblur="numberFormat(this, false)" {{ isset($mandatory) &&
              $mandatory? 'required' : '' }}>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Terbilang<code>*</code></label>
          <div class="col-sm-7">
            <input type="text" class="form-control form-control-sm" name="text_amount"
              value="{{ isset($text_amount) && $text_amount ? $text_amount : '' }}" {{ isset($mandatory) &&
              $mandatory? 'required' : '' }}>
          </div>
        </div>
        @if($is_red)
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Tgl. Penyerahan<code>*</code></label>
          <div class="col-sm-2">
            <input type="date" class="form-control form-control-sm" name="apply_date"
              value="{{ isset($apply_date) && $apply_date ? $apply_date : '' }}" {{ isset($mandatory) &&
              $mandatory? 'required' : '' }}>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Upload LPJ</label>
          <div class="col-sm-7">
            @if(isset($image) && $image)
            <a href="{{ $download }}" rel="noopener noreferrer nofollow" target="_blank" title="Download File LPJ">{{
              $image }}</a>
            @endif
            <div class="input-group input-group-sm">
              <div class="custom-file">
                <input type="file" name="image" id="image" class="custom-file-input"
                  accept="application/pdf, image/jpg, image/png" {{ isset($mandatory) && $mandatory &&
                  !isset($image)? 'required' : '' }}>
                <label class="custom-file-label" for="file">Pilih File</label>
              </div>
            </div>
            <span class="font-italic small">*ekstensi file hanya .pdf, .jpg dan .png</span>
          </div>
        </div>
        @endif
      </div>
      <div class="card-footer">
        <div class="form-button">
          <div class="row justify-content-center">
            <div class="col-sm-1">
              @include('partials.button.back', ['class' => 'btn-sm btn-block', 'action' =>
              route('transaction.expense.index')])
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
  bsCustomFileInput.init();
  
  function editMa() {
    if($('input[name="edt_ma_id"]').is(':checked') && $('input[name="ma_id"]').val() != '') {
      $('input[name="ma_id"]').attr('readonly', false)
    } else {
      $('input[name="ma_id"]').attr('readonly', true)
    }
  }
</script>
@endsection