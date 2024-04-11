<div class="modal fade" id="modalPrintWhite">
  <div class="modal-dialog">
    <div class="modal-content">
      <form class="form-lazy-control" data-action="{{ $modal_action }}">
        <div class="modal-header p-2 justify-content-center">
          <h4 class="modal-title">Bertanda Tangan Dibawah ini</h4>
        </div>
        <div class="modal-body p-2">
          <input type="hidden" name="type" value="{{ config('global.type.code.white') }}">
          <div class="form-group row">
            <label class="col-form-label col-sm-4">Dibayar Oleh</label>
            <div class="col-sm-8">
              <select class="form-control form-control-sm select2" name="knowing">
                <option value="">-- Silakan Pilih --</option>
                @foreach ($employeeArr as $key => $value)
                <option value="{{ $value['id'] }}">{{
                  $value['name'] }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-form-label col-sm-4">Menyetujui</label>
            <div class="col-sm-8">
              <select class="form-control form-control-sm select2" name="approver">
                <option value="">-- Silakan Pilih --</option>
                @foreach ($employeeArr as $key => $value)
                <option value="{{ $value['id'] }}">{{
                  $value['name'] }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-form-label col-sm-4">Yang Menerima</label>
            <div class="col-sm-8">
              <select class="form-control form-control-sm select2" name="reciever">
                <option value="">-- Silakan Pilih --</option>
                @foreach ($employeeArr as $key => $value)
                <option value="{{ $value['id'] }}">{{
                  $value['name'] }}</option>
                @endforeach
              </select>
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