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
      font-family: Cambria,Georgia,serif; 
      font-size: 11pt;
      left: 7mm;
      top: 54mm;
    }

    .desc {
      position: absolute;
      font-family: Cambria,Georgia,serif; 
      font-size: 11pt;
      width: 114mm;
      white-space: normal;
      left: 36mm;
      top: 53.5mm;
    }

    .note {
      position: absolute;
      font-family: Cambria,Georgia,serif; 
      font-size: 11pt;
      text-align: right;
      width: 114mm;
      white-space: normal;
      left: 36mm;
      top: 89mm;
    }

    .amount {
      position: absolute;
      font-family: Cambria,Georgia,serif; 
      font-size: 11pt;
      text-align: right;
      width: 40mm;
      white-space: normal;
      right: 2mm;
      top: 88.5mm;
    }

    .text-amount {
      position: absolute;
      font-family: Cambria,Georgia,serif; 
      font-size: 11pt;
      width: 114mm;
      white-space: normal;
      left: 36mm;
      top: 100mm;
    }

    .total-amount {
      position: absolute;
      font-family: Cambria,Georgia,serif; 
      font-size: 11pt;
      width: 40mm;
      text-align: right;
      white-space: normal;
      right: 2mm;
      top: 100.5mm;
    }

    .ttd-1 {
      position: absolute;
      font-family: Cambria,Georgia,serif; 
      font-size: 11pt;
      width: 31mm;
      white-space: nowrap;
      left: 7mm;
      top: 135mm;
    }

    .ttd-2 {
      position: absolute;
      font-family: Cambria,Georgia,serif; 
      font-size: 11pt;
      width: 31mm;
      white-space: nowrap;
      left: 61mm;
      top: 135mm;
    }

    .ttd-3 {
      position: absolute;
      font-family: Cambria,Georgia,serif; 
      font-size: 11pt;
      width: 31mm;
      white-space: nowrap;
      left: 122mm;
      top: 135mm;
    }

    .ttd-4 {
      position: absolute;
      font-family: Cambria,Georgia,serif; 
      font-size: 11pt;
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
      <strong>{{ $ma_id }}</strong>
    </div>
    <div class="desc">
      <strong>{{ $description }}<br>a/n. {{ $name }}</strong>
    </div>
    <div class="note">
      <strong>Sebesar :</strong>
    </div>
    <div class="amount">
      <strong>Rp. {{ $amount }}</strong>
    </div>
    <div class="text-amount">
      <strong>{{ $text_amount }}</strong>
    </div>
    <div class="total-amount">
      <strong>Rp. {{ $amount }}</strong>
    </div>
    <div class="ttd-1">
      <strong>{{ $knowing }}</strong>
    </div>
    <div class="ttd-2">
      <strong>{{ $approver }}</strong>
    </div>
    <div class="ttd-3">
      <strong>{{ $sender }}</strong>
    </div>
    <div class="ttd-4">
      <strong>{{ $reciever }}</strong>
    </div>
  </div>
</body>

</html>