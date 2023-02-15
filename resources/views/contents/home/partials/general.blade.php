<div class="row justify-content-center">
  @foreach ($result as $value)
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
  @foreach (['fakultas', 'mta', 'mts'] as $value)
  <div class="col-sm-4">
    <div class="card" id="{{ $value }}">
      <div class="card-body p-2">
        <div class="row px-2">
          <div class="col-12 col-sm-12 p-2">
            <h5 class="chart-title text-center mb-0"></h5>
          </div>
          <div class="col-12 col-sm-8 p-2">
            <canvas class="chart-canvas"></canvas>
          </div>
          <div class="col-12 col-sm-4 d-table p-2">
            <ul class="chart-legend d-table-cell align-middle"></ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endforeach
</div>
<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          Transaksi Pengeluaran UMD - Belum di Pertanggung Jawabkan
        </h3>
      </div>
      <div class="card-body">
        <table class="table table-striped table-sm table-data" width="100%">
          <thead>
            <tr>
              <th>No</th>
              <th>No. Kas</th>
              <th>No. Surat</th>
              <th>No. M.A.</th>
              <th>Tgl. Transaksi</th>
              <th>Tgl. Surat</th>
              <th>PIC</th>
              <th>Jml. Transaksi</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
