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
          <input type="hidden" name="status" value="{{ config('global.status.code.finished') }}">
          <div class="form-group row">
            <label class="col-form-label col-sm-4">Realisasi Dana<code>*</code></label>
            <div class="col-sm-8">
              <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                  <span class="input-group-text">Rp</span>
                </div>
                <input type="text" class="form-control form-control-sm text-right" maxlength="20" name="amount_approved"
                  onkeypress="preventAlpha(event)" onkeyup="numberFormat(this, true)"
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