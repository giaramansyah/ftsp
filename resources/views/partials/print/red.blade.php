<!DOCTYPE html>
<html>

<head>
  <title>Bon Merah</title>
  <style>
    @page {
      size: 21cm 29.7cm;
      margin: 0cm;
    }

    .ma {
      position: absolute;
      font-size: 0.875rem;
      left: 16.5mm;
      top: 54mm;
    }

    .desc {
      position: absolute;
      font-size: 0.875rem;
      width: 114mm;
      white-space: normal;
      left: 45mm;
      top: 54mm;
    }

    .note {
      position: absolute;
      font-size: 0.875rem;
      text-align: right;
      width: 114mm;
      white-space: normal;
      left: 45mm;
      top: 92mm;
    }

    .amount {
      position: absolute;
      font-size: 0.875rem;
      text-align: right;
      width: 40mm;
      white-space: normal;
      right: 16.5mm;
      top: 92mm;
    }

    .text-amount {
      position: absolute;
      font-size: 0.875rem;
      width: 114mm;
      white-space: normal;
      left: 45mm;
      top: 107mm;
    }

    .total-amount {
      position: absolute;
      font-size: 0.875rem;
      width: 40mm;
      text-align: right;
      white-space: normal;
      right: 16.5mm;
      top: 107mm;
    }

    .ttd-1 {
      position: absolute;
      font-size: 0.875rem;
      width: 31mm;
      text-align: center;
      white-space: nowrap;
      left: 16.5mm;
      top: 148mm;
    }

    .ttd-2 {
      position: absolute;
      font-size: 0.875rem;
      width: 31mm;
      text-align: center;
      white-space: nowrap;
      left: 70mm;
      top: 148mm;
    }

    .ttd-3 {
      position: absolute;
      font-size: 0.875rem;
      width: 31mm;
      text-align: center;
      white-space: nowrap;
      left: 122mm;
      top: 148mm;
    }

    .ttd-4 {
      position: absolute;
      font-size: 0.875rem;
      width: 31mm;
      text-align: center;
      white-space: nowrap;
      left: 175mm;
      top: 148mm;
    }
  </style>
</head>

<body>
  <div width="100%">
    <div class="ma">
      {{ $ma_id }}
    </div>
    <div class="desc">
      {{ $description }}<br>a/n. {{ $name }}
    </div>
    <div class="note">
      Sebesar :
    </div>
    <div class="amount">
      <strong>Rp. </strong>{{ $amount }}
    </div>
    <div class="text-amount">
      {{ $text_amount }}
    </div>
    <div class="total-amount">
      {{ $amount }}
    </div>
    <div class="ttd-1">
      {{ $knowing }}
    </div>
    <div class="ttd-2">
      {{ $approver }}
    </div>
    <div class="ttd-3">
      {{ $sender }}
    </div>
    <div class="ttd-4">
      {{ $reciever }}
    </div>
  </div>
</body>

</html>