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
      left: 8mm;
      top: 54mm;
    }

    .desc {
      position: absolute;
      font-size: 0.875rem;
      width: 114mm;
      white-space: normal;
      left: 36mm;
      top: 53.5mm;
    }

    .note {
      position: absolute;
      font-size: 0.875rem;
      text-align: right;
      width: 114mm;
      white-space: normal;
      left: 36mm;
      top: 89mm;
    }

    .amount {
      position: absolute;
      font-size: 0.875rem;
      text-align: right;
      width: 40mm;
      white-space: normal;
      right: 2mm;
      top: 88.5mm;
    }

    .text-amount {
      position: absolute;
      font-size: 0.875rem;
      width: 114mm;
      white-space: normal;
      left: 36mm;
      top: 100mm;
    }

    .total-amount {
      position: absolute;
      font-size: 0.875rem;
      width: 40mm;
      text-align: right;
      white-space: normal;
      right: 2mm;
      top: 100.5mm;
    }

    .ttd-1 {
      position: absolute;
      font-size: 0.875rem;
      width: 31mm;
      white-space: nowrap;
      left: 7mm;
      top: 135mm;
    }

    .ttd-2 {
      position: absolute;
      font-size: 0.875rem;
      width: 31mm;
      white-space: nowrap;
      left: 61mm;
      top: 135mm;
    }

    .ttd-3 {
      position: absolute;
      font-size: 0.875rem;
      width: 31mm;
      white-space: nowrap;
      left: 122mm;
      top: 135mm;
    }

    .ttd-4 {
      position: absolute;
      font-size: 0.875rem;
      width: 31mm;
      text-align: right;
      white-space: nowrap;
      right: 2mm;
      top: 135mm;
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
      <strong>Rp. </strong>{{ $amount }}
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