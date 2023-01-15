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
    <h4 style="margin:0px">LAPORAN HARIAN KAS-UMD</h4>
    <h4 style="margin:0px">{{ $header }}</h4>
    <h4 style="margin-top:0px">TANGGAL : {{ $report_date }}</h4>
    <table class="table" width="100%">
      <thead>
        <tr>
          <th>NO</th>
          <th>TGl. TRANSAKSI</th>
          <th>DESKRIPSI</th>
          <th>NOMINAL (RP)</th>
          <th>NO. REKENING</th>
          <th>A/N</th>
        </tr>
      </thead>
      <tbody>
        @if(!empty($expense))
          @foreach ($expense as $key => $value)
          <tr>
            <td>{{ ($key+1) }}</td>
            <td>{{ $value['expense_date'] }}</td>
            <td>{{ $value['description'] }}</td>
            <td style="text-align: right">{{ $value['amount'] }}</td>
            <td>{{ $value['account'] }}</td>
            <td>{{ $value['name'] }}</td>
          </tr>
          @endforeach
        @else
          <tr>
            <td colspan="6">Tanggal {{ $report_date }} tidak ada laporan</td>
          </tr>
        @endif
      </tbody>
    </table>
  </body>
</html>