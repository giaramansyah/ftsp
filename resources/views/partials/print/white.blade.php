<!DOCTYPE html>
<html>

<head>
  <title>Bon Putih</title>
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
      top: 76.2mm;
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
      top: 76.2mm;
    }

    .text-amount {
      position: absolute;
      font-family: Cambria,Georgia,serif; 
      font-size: 11pt;
      color: rgba(0, 0, 0, 1);
      width: 114mm;
      white-space: normal;
      left: 36mm;
      top: 93.1mm;
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
      top: 93mm;
    }

    .ttd-1 {
      position: absolute;
      font-family: Cambria,Georgia,serif; 
      font-size: 11pt;
      color: rgba(0, 0, 0, 1);
      width: 31mm;
      white-space: nowrap;
      left: 98mm;
      top: 127mm;
    }

    .ttd-2 {
      position: absolute;
      font-family: Cambria,Georgia,serif; 
      font-size: 11pt;
      color: rgba(0, 0, 0, 1);
      width: 31mm;
      white-space: nowrap;
      left: 132.8mm;
      top: 127mm;
    }

    .ttd-3 {
      position: absolute;
      font-family: Cambria,Georgia,serif; 
      font-size: 11pt;
      color: rgba(0, 0, 0, 1);
      width: 31mm;
      text-align: right;
      white-space: nowrap;
      right: 2mm;
      top: 127mm;
    }
  </style>
</head>

<body>
  <div width="100%">
    <div class="ma">
      <b>{{ $ma_id }}</b>
    </div>
    <div class="desc">
      <b>{{ $description }}<br>a/n. {{ $name }}</b>
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
      <b>{{ $reciever }}</b>
    </div>
  </div>
</body>

</html>