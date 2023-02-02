<!DOCTYPE html>
<html>
  <head>
    <title>Lap. Harian</title>
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
    <h4 style="margin:0px">LAPORAN HARIAN KAS-UMD ({{ $year }})</h4>
    <h4 style="margin:0px">{{ $header }}</h4>
    <h4 style="margin-top:0px">TANGGAL : {{ $report_date }}</h4>
    <table class="table" width="100%">
      <thead>
        <tr>
          <th>NO</th>
          <th>TGl. TRANSAKSI</th>
          <th>DESKRIPSI</th>
          <th>KREDIT (RP)</th>
          <th>DEBET (RP)</th>
          <th>NOMINAL (RP)</th>
          <th>NO. REKENING</th>
          <th>A/N</th>
        </tr>
      </thead>
      <tbody>
        @if(!empty($data))
          @foreach ($data as $key => $value)
          <tr>
            <td>{{ ($key+1) }}</td>
            <td>{{ $value['date'] }}</td>
            <td>{{ $value['description'] }}</td>
            <td style="text-align: right">{{ $value['credit'] }}</td>
            <td style="text-align: right">{{ $value['debet'] }}</td>
            <td style="text-align: right">{{ $value['amount'] }}</td>
            <td>{{ $value['account'] }}</td>
            <td>{{ $value['name'] }}</td>
          </tr>
          @endforeach
        @else
          <tr>
            <td colspan="8">Tanggal {{ $report_date }} tidak ada laporan</td>
          </tr>
        @endif
      </tbody>
      <tfoot>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>JUMLAH</td>
          <td>{{ $total_credit }}</td>
          <td>{{ $total_debet }}</td>
          <td>{{ $total_amount }}</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </tfoot>
    </table>
    <table width="100%">
      <tr>
        <td width="50%" style="text-align: center">Mengetahui,</td>
        <td width="50%" style="text-align: center">Setuju,</td>
        <td width="50%" style="text-align: center">setuju,</td>
        <td width="50%" style="text-align: center">Jakarta, {{ $report_date }}</td>
      </tr>
      <tr>
        <td width="50%" style="text-align: center">Wakil Dekan II</td>
        <td width="50%" style="text-align: center">Kabag TU</td>
        <td width="50%" style="text-align: center">Kasubag Umum & Keuangan</td>
        <td width="50%" style="text-align: center">Kasir FTSP</td>
      </tr>
      <tr>
        <td width="50%" style="text-align: center">&nbsp;</td>
        <td width="50%" style="text-align: center">&nbsp;</td>
        <td width="50%" style="text-align: center">&nbsp;</td>
        <td width="50%" style="text-align: center">&nbsp;</td>
      </tr>
      <tr>
        <td width="50%" style="text-align: center">&nbsp;</td>
        <td width="50%" style="text-align: center">&nbsp;</td>
        <td width="50%" style="text-align: center">&nbsp;</td>
        <td width="50%" style="text-align: center">&nbsp;</td>
      </tr>
      <tr>
        <td width="50%" style="text-align: center">&nbsp;</td>
        <td width="50%" style="text-align: center">&nbsp;</td>
        <td width="50%" style="text-align: center">&nbsp;</td>
        <td width="50%" style="text-align: center">&nbsp;</td>
      </tr>
      <tr>
        <td width="50%" style="text-align: center; font-weight: 600">{{ $knowing }}</td>
        <td width="50%" style="text-align: center; font-weight: 600">{{ $approve1 }}</td>
        <td width="50%" style="text-align: center; font-weight: 600">{{ $approve2 }}</td>
        <td width="50%" style="text-align: center; font-weight: 600">{{ $user }}</td>
      </tr>
    </table>
  </body>
</html>