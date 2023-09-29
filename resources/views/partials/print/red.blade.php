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
      color: rgba(0, 0, 0, 1);
      font-weight: 600;
      left: 7mm;
      top: 54mm;
    }

    .desc {
      position: absolute;
      font-family: Cambria,Georgia,serif; 
      font-size: 11pt;
      color: rgba(0, 0, 0, 1);
      font-weight: 600;
      width: 114mm;
      white-space: normal;
      left: 36mm;
      top: 53.5mm;
    }

    .note {
      position: absolute;
      font-family: Cambria,Georgia,serif; 
      font-size: 11pt;
      color: rgba(0, 0, 0, 1);
      font-weight: 600;
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
      color: rgba(0, 0, 0, 1);
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
      color: rgba(0, 0, 0, 1);
      width: 114mm;
      white-space: normal;
      left: 36mm;
      top: 100mm;
    }

    .total-amount {
      position: absolute;
      font-family: Cambria,Georgia,serif; 
      font-size: 11pt;
      color: rgba(0, 0, 0, 1);
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
      color: rgba(0, 0, 0, 1);
      width: 31mm;
      white-space: nowrap;
      left: 7mm;
      top: 135mm;
    }

    .ttd-2 {
      position: absolute;
      font-family: Cambria,Georgia,serif; 
      font-size: 11pt;
      color: rgba(0, 0, 0, 1);
      width: 31mm;
      white-space: nowrap;
      left: 61mm;
      top: 135mm;
    }

    .ttd-3 {
      position: absolute;
      font-family: Cambria,Georgia,serif; 
      font-size: 11pt;
      color: rgba(0, 0, 0, 1);
      width: 31mm;
      white-space: nowrap;
      left: 122mm;
      top: 135mm;
    }

    .ttd-4 {
      position: absolute;
      font-family: Cambria,Georgia,serif; 
      font-size: 11pt;
      color: rgba(0, 0, 0, 1);
      width: 31mm;
      text-align: right;
      white-space: nowrap;
      right: 0mm;
      top: 135mm;
    }
  </style>
</head>

<body>
  <div width="100%">
    <div class="ma">
      <b>{{ $ma_id }}</b>
    </div>
    <div class="desc">
      <b>{{ $description }}<br>a/n. {{ $name_desc }}</b>
    </div>
    <div class="note">
      <b>Sebesar :</b>
    </div>
    <div class="amount">
      <b>Rp. {{ $amount }}</b>
    </div>
    <div class="text-amount">
      <b>{{ $text_amount }}</b>
    </div>
    <div class="total-amount">
      <b>Rp. {{ $amount }}</b>
    </div>
    <div class="ttd-1">
      <b>{{ $knowing }}</b>
    </div>
    <div class="ttd-2">
      <b>{{ $approver }}</b>
    </div>
    <div class="ttd-3">
      <b>{{ $sender }}</b>
    </div>
    <div class="ttd-4">
      <b>{{ $reciever }}</b>
    </div>
  </div>
</body>

</html>