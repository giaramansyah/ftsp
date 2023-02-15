@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
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
  <div class="row">
    <div class="col-sm-12">
      <div class="card" id="note">
        <div class="card-body p-2">
          <div class="row px-2">
            <div class="col-12 col-sm-12 p-2">
              <canvas class="chart-canvas" height="600px"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('push-js')
<script type="text/javascript">
  getChart();
  getPending();

  function getPending() {
    $('.table-data').DataTable(
      {
        responsive: true,
        autoWidth: true,
        processing: true,
        ajax : {
          method : 'GET',
          url : "{{ route('home.pending') }}",
        },
        order: [],
        columns : [
          {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
          {data: 'expense', name: 'expense', orderable: true, searchable: true},
          {data: 'reff_no', name: 'reff_no', orderable: true, searchable: true},
          {data: 'ma_id', name: 'ma_id', orderable: true, searchable: true},
          {data: 'expense_date_format', name: 'expense_date_format', orderable: true, searchable: true},
          {data: 'reff_date_format', name: 'reff_date_format', orderable: true, searchable: true},
          {data: 'staff', name: 'staff', orderable: true, searchable: true},
          {data: 'amount', name: 'amount', orderable: true, searchable: true, class: 'text-right text-bold'},
        ]
      }
    );
  }

  function getChart() {
    $.ajaxSetup({
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
    });

    $.ajax({
      type: "GET",
      url: "{{ route('home.realization') }}",
      dataType: 'json',
      tryCount : 0,
      retryLimit : 3,
      success: function (response) {
        if(response.status) {
          $.each(response.data, function(key, value){
            drawPieChart(key, value)
          })
        }
      }
    });

    $.ajax({
      type: "GET",
      url: "{{ route('home.note') }}",
      dataType: 'json',
      tryCount : 0,
      retryLimit : 3,
      success: function (response) {
        if(response.status) {
          drawBarChart(response.data)
        }
      }
    });
  }

  function drawPieChart(key, data) {
    var canvas = $('#'+key).find('.chart-canvas');
    var legend = $('#'+key).find('.chart-legend');
    var title = $('#'+key).find('.chart-title');
    var options = {
      type: "pie",
      data: {
        labels: data.series,
        datasets: [
          {
            data: [data.real.value, data.pagu.value],
            backgroundColor: [
              data.real.color,
              data.pagu.color,
            ],
            borderColor: "#e6e6e6",
            borderWidth: 1,
            datalabels: {
              anchor: "center",
              backgroundColor: null,
              borderWidth: 0,
            },
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          },
          tooltip: {
            mode: "index",
            intersect: true,
            callbacks: {
              label: function (context) {
                var xLabel = context.label;
                var yLabel = context.dataset.data[context.dataIndex];
                return (
                  xLabel +
                  " : Rp " + formatCurrency(yLabel)
                );
              },
            },
          },
          datalabels: {
            display: true,
            color: "#f8f9fa",
            font: {
              size: 13,
              weight: "bold",
            },
            textShadowBlur: 10,
            textShadowColor: "#212529",
            formatter: function (value, context) {
              var total =  parseFloat(data.real.value) + parseFloat(data.pagu.value);
              return (
                ((parseFloat(value) / total) * 100).toFixed(
                    2
                ) + "%"
              );
            },
          },
        },
      },
    };

    var items = "";
    $.each(
        data.legend,
        function (index, value) {
            var percent = "";
            if (index > 0) {
              percent = " (" + value.percent + "%)";
            }
            items +=
                '<li><i class="fas fa-circle ' +
                value.color +
                '"></i> ' +
                value.text +
                "" +
                percent +
                "</li>";
                items +=
                '<li><i class="fas fa-circle text-white"></i> <strong>Rp ' +
                value.value +
                "</strong></li>";
        }
    );

    title.text(data.title);
    legend.html(items);
    new Chart(canvas, options);
  }

  function drawBarChart(data) {
    var canvas = $('#note').find('.chart-canvas');
    var options = {
      type: "bar",
      data: {
        labels: data.series,
        datasets: [
          {
            label: "Pengajuan",
            backgroundColor: "#28a745",
            borderColor: "#28a745",
            data: data.requested,
          },
          {
            label: "Realisasi",
            backgroundColor: "#00a2e9",
            borderColor: "#00a2e9",
            data: data.approved,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          title: {
            display: true,
            text: 'PERBANDINGAN PENGAJUAN DAN REALISASI MATA ANGGARAN',
            color: "#212529",
            font: {
              size: 20,
            },
          },
          tooltip: {
            mode: "index",
            intersect: true,
            callbacks: {
              label: function (context) {
                var xLabel = context.dataset.label;
                var yLabel = context.dataset.data[context.dataIndex];
                return (
                  xLabel +
                  " : Rp " + formatCurrency(yLabel)
                );
              },
            },
          },
        },
        scales: {
          xAxes: {
            position: "bottom",
            ticks: {
              color: "#212529",
              font: {
                size: 12,
              },
            },
          },
          yAxes: {
            position: "left",
            beginAtZero: true,
            title: {
              display: true,
              text: "Dalam Rupiah",
              color: "#212529",
            },
            ticks: {
              count: 20,
              precision: 0,
              color: "#212529",
              font: {
                size: 12,
              },
              callback: function (value, index, values) {
                return "Rp " + formatCurrency(value);
              },
            },
          },
        },
      },
    };

    new Chart(canvas, options);
  }
</script>
@endsection