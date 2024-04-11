<div class="row">
  <div class="col-sm-12">
    <div class="card" id="note">
      <div class="card-header">
        <div class="form-group row justify-content-center mb-0">
          <label class="col-sm-2 col-form-label">{{ __('Tahun Akademik') }}<code>*</code></label>
          <div class="col-sm-2">
            <select class="form-control form-control-sm select2" name="year">
              <option value="">-- Silakan Pilih --</option>
              @foreach ($yearArr as $key => $value)
              <option value="{{ Secure::secure($value['id']) }}" {{ date('Y') == $value['id'] ? 'selected' : ''
                }}>{{ $value['name'] }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
      <div class="card-body p-2">
        <div class="row px-2">
          <div class="col-12 col-sm-12 p-2" id="canvas-container">
            <canvas class="chart-canvas" height="600px"></canvas>
          </div>
          <div class="col-12 col-sm-12 p-2 table-responsive">
            <table class="table table-bordered table-sm" width="100%">
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
