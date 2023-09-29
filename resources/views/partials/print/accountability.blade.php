<!DOCTYPE html>
<html>

<head>
  <title>Lap. Pertanggung Jawaban</title>
  <style>
    body {
      font-size: 0.875rem
    }

    table.table,
    .table th,
    .table td {
      border: 1px solid black;
      border-collapse: collapse;
      padding: 5px;
      margin-bottom: 50px;
      font-size: 0.75rem;
    }

    .table th {
      font-weight: 600;
    }
  </style>
</head>

<body>
  <h4 style="margin:0px">LAPORAN PERTANGGUNG JAWABAN KAS-UMD</h4>
  <h4 style="margin:0px">{{ $header }}</h4>
  <h4 style="margin-top:0px">TANGGAL : {{ $report_date }}</h4>
  <table class="table" width="100%">
    <thead>
      <tr>
        @foreach (config('global.report.header.accountability') as $value)
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
        <td>Saldo Awal</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td style="text-align: right">{{ $balance }}</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      @if(!empty($expense))
      @foreach ($expense as $key => $value)
      <tr>
        <td>{{ ($key+1) }}</td>
        <td>{{ $value['reff_no'] }}</td>
        <td>{{ $value['reff_date'] }}</td>
        <td>{{ $value['ma_id'] }}</td>
        <td>{{ $value['description'] }}</td>
        <td style="text-align: right">{{ $value['amount'] }}</td>
        <td style="text-align: right">{{ $value['amount'] }}</td>
        <td style="text-align: right">{{ $value['amount'] }}</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      @endforeach
      @else
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>Tanggal {{ $report_date }} tidak ada realisasi</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      @endif
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td style="text-align: right; font-weight:600">{{ $total_expense }}</td>
        <td style="text-align: right; font-weight:600">{{ $total_expense }}</td>
        <td style="text-align: right; font-weight:600">{{ $total_expense }}</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
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
        <td>&nbsp;</td>
        <td>Saldo Akhir</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td style="text-align: right">{{ $balance }}</td>
        <td>&nbsp;</td>
      </tr>
    </tbody>
  </table>
  <table width="100%">
    <tr>
      <td width="33%" style="text-align: center">Mengetahui,</td>
      <td width="33%" style="text-align: center"></td>
      <td width="33%" style="text-align: center">Jakarta, {{ $report_date }}</td>
    </tr>
    <tr>
      <td width="33%" style="text-align: center">Wakil Dekan II</td>
      <td width="33%" style="text-align: center">Kasubag Umum Keuangan</td>
      <td width="33%" style="text-align: center">Kasir FTSP</td>
    </tr>
    <tr>
      <td width="33%" style="text-align: center">&nbsp;</td>
      <td width="33%" style="text-align: center">&nbsp;</td>
      <td width="33%" style="text-align: center">&nbsp;</td>
    </tr>
    <tr>
      <td width="33%" style="text-align: center">&nbsp;</td>
      <td width="33%" style="text-align: center">&nbsp;</td>
      <td width="33%" style="text-align: center">&nbsp;</td>
    </tr>
    <tr>
      <td width="33%" style="text-align: center">&nbsp;</td>
      <td width="33%" style="text-align: center">&nbsp;</td>
      <td width="33%" style="text-align: center">&nbsp;</td>
    </tr>
    <tr>
      <td width="33%" style="text-align: center; font-weight: 600">{{ $knowing }}</td>
      <td width="33%" style="text-align: center; font-weight: 600">{{ $knowing2 }}</td>
      <td width="33%" style="text-align: center; font-weight: 600">{{ $user }}</td>
    </tr>
  </table>
</body>

</html>