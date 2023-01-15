<!DOCTYPE html>
<html>
  <head>
    <title>Lap. Pertanggung Jawaban</title>
    <style>
      body {
        font-size: 0.875rem
      }

      table.table, .table th, .table td {
        border: 1px solid black;
        border-collapse: collapse;
        padding : 5px;
        margin-bottom: 50px;
        font-size: 0.75rem;
      }

      .table th {
        font-weight: 600;
      }
    </style>
  </head>
  <body>
    <h4 style="margin:0px;text-align: center;">LAPORAN UMD YANG BELUM DI PERTANGGUNG JAWABKAN</h4>
    <h4 style="margin:0px;text-align: center;">TA {{ $year }}</h4>
    <h4 style="margin-top:0px;text-align: center;">{{ $header }}</h4>
    <table class="table" width="100%">
      <thead>
        <tr>
          @foreach (config('global.report.header.accountability_umd') as $value)
          <th>{{ $value }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        @if(!empty($expense))
        @foreach($expense as $key => $value)
        <tr>
          <td style="text-align: center;">{{ ($key+1) }}</td>
          <td style="text-align: center;">{{ ($value['reff_date']) }}</td>
          <td style="text-align: center;">{{ ($value['ma_id']) }}</td>
          <td style="text-align: left;">{{ ($value['description']) }}</td>
          <td style="text-align: left;">{{ ($value['name']) }}</td>
          <td style="text-align: right;">{{ ($value['amount']) }}</td>
        </tr>
        @endforeach
        @else
        <tr>
          <td style="text-align: center;" colspan="6">Tidak Ada</td>
        </tr>
        @endif
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td style="text-align: center;">JUMLAH</td>
          <td>&nbsp;</td>
          <td style="text-align: right;">{{ $total_expense }}</td>
        </tr>
      </tbody>
    </table>
    <small>CATATAN : Dimohon segera Laporan Keuangan diserahkan ke Wadek II FTSP paling lambat tgl. 30 Desember 2022</small>
    <table style="margin-top:50px" width="100%">
      <tr>
        <td style="text-align: center">Jakarta, {{ $report_date }}</td>
      </tr>
      <tr>
        <td style="text-align: center">Wakil Dekan II</td>
      </tr>
      <tr>
        <td style="text-align: center">&nbsp;</td>
      </tr>
      <tr>
        <td style="text-align: center">&nbsp;</td>
      </tr>
      <tr>
        <td style="text-align: center">&nbsp;</td>
      </tr>
      <tr>
        <td style="text-align: center; font-weight: 600">{{ $knowing }}</td>
      </tr>
    </table>
  </body>
</html>