<div class="modal fade" id="modalUpdate">
  <div class="modal-dialog">
    <div class="modal-content">
      <form class="form-lazy-control" data-action="{{ $modal_action }}" data-validate="max_amount">
        <div class="modal-header p-2 justify-content-center">
          <h4 class="modal-title">Update Status Data Surat</h4>
        </div>
        <div class="modal-body p-2">
          <input type="hidden" name="ma_id" value="{{ $ma_id }}">
          <input type="hidden" name="validate_max" id="validate_max" value="{{ $amount_requested }}">
          <div class="form-group row">
            <label class="col-sm-4 col-form-label">{{ __('Status Pengajuan') }}<code>*</code></label>
            <div class="col-sm-8">
              <select class="form-control form-control-sm" name="application_status" required>
                <option value="">-- Silakan Pilih --</option>
                @foreach ($applicationStatusArr as $key => $value)
                  <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="approve-container d-none">
            <div class="form-group row">
              <label class="col-sm-4 col-form-label">{{ __('Jenis Pencairan Dana') }}<code>*</code></label>
              <div class="col-sm-8">
                <select class="form-control form-control-sm" name="fund_type" required>
                  <option value="">-- Silakan Pilih --</option>
                  @foreach ($fundTypeArr as $key => $value)
                    <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-4">Realisasi Dana<code>*</code></label>
              <div class="col-sm-8">
                <div class="input-group input-group-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Rp</span>
                  </div>
                  <input type="text" class="form-control form-control-sm text-right" maxlength="20"
                    name="amount_approved" onkeypress="preventAlpha(event)" onkeyup="numberFormat(this, true)"
                    onblur="numberFormat(this, true);amountText(this.value, '#text_amount_approve')">
                </div>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Terbilang<code>*</code></label>
              <div class="col-sm-8">
                <input type="text" class="form-control form-control-sm" id="text_amount_approve" readonly>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Tanggal Pencairan<code>*</code></label>
              <div class="col-sm-8">
                <input type="date" class="form-control form-control-sm" name="disbursed_date" required>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label">Keterangan</label>
            <div class="col-sm-8">
              <textarea class="form-control form-control-sm" name="notes" rows="4"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer p-2 d-block">
          <div class="form-button text-enter">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-outline-success btn-sm">Lanjutkan</button>
          </div>
          <div class="form-loading">
            <img src="{{ asset('img/loading.gif') }}" height="40">
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
