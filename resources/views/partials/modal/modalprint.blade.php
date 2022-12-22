<div class="modal fade" id="modalPrint">
  <div class="modal-dialog">
    <div class="modal-content">
      <form class="form-lazy-control" data-action="{{ $modal_action }}">
        <div class="modal-header p-2 justify-content-center">
          <h4 class="modal-title">Bertanda Tangan Dibawah ini</h4>
        </div>
        <div class="modal-body p-2">
          <div class="form-group row">
            <label class="col-form-label col-sm-4">Mengetahui</label>
            <div class="col-sm-8">
              <input type="text" name="knowing" class="form-control form-control-sm" required> 
            </div>
          </div>
          <div class="form-group row">
            <label class="col-form-label col-sm-4">Setujui</label>
            <div class="col-sm-8">
              <input type="text" name="approver" class="form-control form-control-sm" required> 
            </div>
          </div>
          <div class="form-group row">
            <label class="col-form-label col-sm-4">Yang Membayar</label>
            <div class="col-sm-8">
              <input type="text" name="sender" class="form-control form-control-sm" required> 
            </div>
          </div>
          <div class="form-group row">
            <label class="col-form-label col-sm-4">Yang Menerima</label>
            <div class="col-sm-8">
              <input type="text" name="reciever" class="form-control form-control-sm" required> 
            </div>
          </div>
        </div>
        <div class="modal-footer p-2 d-block">
          <div class="form-button text-enter">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-outline-success btn-sm">Cetak</button>
          </div>
          <div class="form-loading">
            <img src="{{ asset('img/loading.gif') }}" height="40">
          </div>
        </div>
      </form>
    </div>
  </div>
</div>