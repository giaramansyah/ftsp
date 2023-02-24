@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
  @if($is_note)
  @include('contents.home.partials.note')
  @endif
  @if($is_general)
  @include('contents.home.partials.general')
  @endif
</div>
@endsection
@section('push-js')
@if($is_note)
<script type="text/javascript">
  $('select[name=year]').on('change', function(){
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
      data: {id:id},
      dataType: 'json',
      tryCount : 0,
      retryLimit : 3,
      success: function (response) {
        if(response.status) {
          drawBarChart(response.data);
          drawColumnTable(response.data);
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
        datasets: [
          {
            label: "Dana RAB",
            backgroundColor: "#28a745",
            borderColor: "#28a745",
            data: data.requested,
          },
          {
            label: "Dana Pengajuan",
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
            text: 'SERAPAN RAB FTSP USULAN TA ' + data.year,
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
              footer: function(contexts) {
                let index = 0;
                contexts.forEach(function(context) {
                  index = context.dataIndex;
                });
                return "Persentase : " + data.percentage[index] + "%";
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

  function drawColumnTable(data) {
    $.each(data, function(index, value){
      if(index == 'year') {
        return;
      }

      var row = $('<tr class="text-bold">');
      if(index == 'series') {
        var col = '<td width="5%">&nbsp;</td>';
      } else if(index == 'requested') {
        var col = '<td width="10%"><i class="fas fa-square" style="color:#28a745"></i> DANA RAB</td>';
      } else if(index == 'approved') {
        var col = '<td width="10%"><i class="fas fa-square" style="color:#00a2e9"></i> DANA USULAN</td>';
      } else if(index == 'percentage') {
        var col = '<td width="10%"><i class="fas fa-square" style="color:#6c757d"></i> PERSENTASE</td>';
      } 

      $.each(value, function(key, val) {
        if(index == 'series') {
          col += '<td width="10%" class="text-center">'+val+'</td>';
        } else if(index == 'percentage') {
          col += '<td width="10%" class="text-center">' + val + '%</td>';
        } else {
          col += '<td width="10%" class="text-center">' + formatCurrency(val) + '</td>';
        }
      })
      row.append(col);
      $('#note').find('table tbody').append(row);
    })
  }
</script>
@endif
@if($is_general)
<script type="text/javascript">
  getPieChart();
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
</script>
@endif
@endsection