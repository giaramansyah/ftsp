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
@section('push-js')
<script type="text/javascript">
  getBarChart();

  function getBarChart() {
    $.ajaxSetup({
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
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