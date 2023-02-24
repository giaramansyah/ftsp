<div class="row justify-content-center">
  @foreach ($result_note as $value)
  <div class="col-sm-3">
    <div class="info-box">
      <span class="info-box-icon {{ $value['class'] }} elevation-1">
        <i class="{{ $value['icon'] }}"></i>
      </span>
      <div class="info-box-content">
        <span class="info-box-text text-bold">{{ $value['title'] }}</span>
        <span class="info-box-text">{{ $value['label'] }}</span>
        <span class="info-box-number">
          @if ($value['is_prepend'])
          <small>{{ $value['prefix'] }}</small>
          @endif
          {{ $value['value'] }}
          @if ($value['is_append'])
          <small>{{ $value['prefix'] }}</small>
          @endif
        </span>
      </div>
    </div>
  </div>
  @endforeach
</div>
<div class="row">
  <div class="col-sm-12">
    <div class="card" id="note">
      <div class="card-header">
        <div class="form-group row justify-content-center">
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
          <div class="col-12 col-sm-12 p-2">
            <canvas class="chart-canvas" height="600px"></canvas>
          </div>
          <table class="table table-bordered table-sm" width="100%">
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
