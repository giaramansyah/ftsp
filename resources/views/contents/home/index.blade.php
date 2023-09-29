@extends('layouts/main')
@section('title', $header)
@section('content')
  <div class="container-fluid">
    @if ($is_note)
      @include('contents.home.partials.note')
    @endif
    @if ($is_general)
      @include('contents.home.partials.general')
    @endif
  </div>
@endsection
@section('push-js')
  @if ($is_note)
    <script type="text/javascript">
      $('select[name=year]').on('change', function() {
        var id = $(this).val();
        getBarChart(id);
      })

      $('select[name=year]').trigger('change')

      function getBarChart(id) {
        $.ajaxSetup({
          headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
          },
        });

        $.ajax({
          type: "GET",
          url: "{{ route('home.note') }}",
          data: {
            id: id
          },
          dataType: 'json',
          tryCount: 0,
          retryLimit: 3,
          success: function(response) {
            if (response.status) {
              drawBarChart(response.data);
              drawColumnTable(response.data);
              drawInfoBox(response.data.status);
            }
          }
        });
      }

      function drawBarChart(data) {
        var container = $('#note').find('#canvas-container');
        container.html('<canvas class="chart-canvas" height="600px"></canvas>')

        var canvas = $('#note').find('.chart-canvas');
        var options = {
          type: "bar",
          data: {
            labels: data.series,
            datasets: [{
                label: "DANA RAB",
                backgroundColor: "#ffc107",
                borderColor: "#ffc107",
                data: data.amount,
              },
              {
                label: "DANA USULAN",
                backgroundColor: "#17a2b8",
                borderColor: "#17a2b8",
                data: data.requested,
              },
              {
                label: "DANA REALISASI",
                backgroundColor: "#28a745",
                borderColor: "#28a745",
                data: data.approved,
              },
              {
                label: "DANA ON PROCESS",
                backgroundColor: "#ff851b",
                borderColor: "#ff851b",
                data: data.process,
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              title: {
                display: true,
                text: 'SERAPAN RAB FTSP TA ' + data.year,
                color: "#212529",
                font: {
                  size: 20,
                },
              },
              tooltip: {
                mode: "index",
                intersect: true,
                callbacks: {
                  label: function(context) {
                    var xLabel = context.dataset.label;
                    var yLabel = context.dataset.data[context.dataIndex];
                    return (
                      xLabel +
                      " : Rp " + formatCurrency(yLabel)
                    );
                  },
                  footer: function(contexts) {
                    let index = 0;
                    contexts.forEach(function(context) {
                      index = context.dataIndex;
                    });
                    return "% Pengajuan : " + data.percent_request[index] + "%\n" +
                      "% Realisasi : " + data.percent_approve[index] + "%\n" +
                      "% On Progress : " + data.percent_progress[index] + "%\n" +
                      "% On Process : " + data.percent_process[index] + "%\n";
                  }
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
                  callback: function(value, index, values) {
                    return "Rp " + formatCurrency(value);
                  },
                },
              },
            },
          },
        };

        new Chart(canvas, options);
      }

      function drawColumnTable(data) {
        $('#note').find('table tbody').html('');

        $.each(data, function(index, value) {
          if (index == 'year' || index == 'status') {
            return;
          }

          var row = $('<tr>');
          if (index == 'series') {
            var col = '<td width="5%">&nbsp;</td>';
          } else if (index == 'amount') {
            var col = '<td width="10%"><i class="fas fa-square" style="color:#ffc107"></i> DANA RAB</td>';
          } else if (index == 'requested') {
            var col = '<td width="10%"><i class="fas fa-square" style="color:#17a2b8"></i> DANA USULAN</td>';
          } else if (index == 'approved') {
            var col = '<td width="10%"><i class="fas fa-square" style="color:#28a745"></i> DANA REALISASI</td>';
          } else if (index == 'process') {
            var col = '<td width="10%"><i class="fas fa-square" style="color:#ff851b"></i> DANA ON PROCESS</td>';
          } else if (index == 'percent_request') {
            var col = '<td width="10%"><i class="fas fa-square" style="color:#836200"></i> % PENGAJUAN</td>';
          } else if (index == 'percent_approve') {
            var col = '<td width="10%"><i class="fas fa-square" style="color:#0b515c"></i> % REALISASI</td>';
          } else if (index == 'percent_progress') {
            var col = '<td width="10%"><i class="fas fa-square" style="color:#145322"></i> % ON PROGRESS</td>';
          } else if (index == 'percent_process') {
            var col = '<td width="10%"><i class="fas fa-square" style="color:#8d4100"></i> % ON PROCESS</td>';
          } else if (index == 'finished') {
            var col = '<td width="10%"><i class="fas fa-square" style="color:#007bff"></i> STATUS SELESAI</td>';
          } else if (index == 'unfinished') {
            var col = '<td width="10%"><i class="fas fa-square" style="color:#dc3545"></i> STATUS BELUM SELESAI</td>';
          }

          $.each(value, function(key, val) {
            if (index == 'series') {
              col += '<td width="10%" class="text-center">' + val + '</td>';
            } else if (index == 'percent_request' || index == 'percent_approve' || index == 'percent_progress' ||
              index == 'percent_process') {
              col += '<td width="10%" class="text-center">' + val + '%</td>';
            } else if (index == 'finished' || index == 'unfinished') {
              col += '<td width="10%" class="text-center">' + val + '</td>';
            } else {
              col += '<td width="10%" class="text-center">' + formatCurrency(val) + '</td>';
            }
          })
          row.append(col);
          $('#note').find('table tbody').append(row);
        })
      }

      function drawInfoBox(data) {
        $.each(data, function(index, value) {
          var box = $('#' + index).find('.info-box');
          box.find('.info-box-icon').addClass(value.class);
          box.find('.info-box-icon i').addClass(value.icon);
          box.find('.info-box-title').text(value.title);
          box.find('.info-box-label').text(value.label);
          if (value.is_prepend) {
            box.find('.info-box-number').text(value.prefix + ' ' + value.value)
          }
          if (value.is_append) {
            box.find('.info-box-number').text(value.value + ' ' + value.prefix)
          }
        })
      }
    </script>
  @endif
  @if ($is_general)
    <script type="text/javascript">
      $('select[name=year2]').on('change', function() {
        var id = $(this).val();
        location.href = "{{ route('home') }}" + "/" + id;
      })

      getPieChart();
      getPending();

      function getPending() {
        $('.table-data').DataTable({
          responsive: true,
          autoWidth: true,
          processing: true,
          ajax: {
            method: 'GET',
            url: "{{ route('home.pending') }}"
          },
          order: [],
          columns: [{
              data: 'DT_RowIndex',
              name: 'DT_RowIndex',
              orderable: false,
              searchable: false
            },
            {
              data: 'expense',
              name: 'expense',
              orderable: true,
              searchable: true
            },
            {
              data: 'reff_no',
              name: 'reff_no',
              orderable: true,
              searchable: true
            },
            {
              data: 'ma_id',
              name: 'ma_id',
              orderable: true,
              searchable: true
            },
            {
              data: 'expense_date_format',
              name: 'expense_date_format',
              orderable: true,
              searchable: true
            },
            {
              data: 'reff_date_format',
              name: 'reff_date_format',
              orderable: true,
              searchable: true
            },
            {
              data: 'staff',
              name: 'staff',
              orderable: true,
              searchable: true
            },
            {
              data: 'amount',
              name: 'amount',
              orderable: true,
              searchable: true,
              class: 'text-right text-bold'
            },
          ]
        });
      }

      function getPieChart() {
        $.ajaxSetup({
          headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
          },
        });

        $.ajax({
          type: "GET",
          url: "{{ route('home.realization') }}",
          dataType: 'json',
          tryCount: 0,
          retryLimit: 3,
          success: function(response) {
            if (response.status) {
              $.each(response.data, function(key, value) {
                drawPieChart(key, value)
              })
            }
          }
        });
      }

      function drawPieChart(key, data) {
        var canvas = $('#' + key).find('.chart-canvas');
        var legend = $('#' + key).find('.chart-legend');
        var title = $('#' + key).find('.chart-title');
        var options = {
          type: "pie",
          data: {
            labels: data.series,
            datasets: [{
              data: [data.pagu.value, data.real.value],
              backgroundColor: [
                data.pagu.color,
                data.real.color
              ],
              borderColor: "#e6e6e6",
              borderWidth: 1,
              datalabels: {
                anchor: "center",
                backgroundColor: null,
                borderWidth: 0,
              },
            }, ],
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
                  label: function(context) {
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
                formatter: function(value, context) {
                  var total = parseFloat(data.real.value) + parseFloat(data.pagu.value);
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
          function(index, value) {
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
    </script>
  @endif
@endsection
