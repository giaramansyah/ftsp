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
      color: rgba(0, 0, 0, 0);
      left: 7mm;
      top: 54mm;
    }

    .desc {
      position: absolute;
      font-family: Cambria,Georgia,serif; 
      font-size: 11pt;
      color: rgba(0, 0, 0, 0);
      width: 114mm;
      white-space: normal;
      left: 36mm;
      top: 53.5mm;
    }

    .note {
      position: absolute;
      font-family: Cambria,Georgia,serif; 
      font-size: 11pt;
      color: rgba(0, 0, 0, 0);
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
      color: rgba(0, 0, 0, 0);
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
      color: rgba(0, 0, 0, 0);
      width: 114mm;
      white-space: normal;
      left: 36mm;
      top: 100mm;
    }

    .total-amount {
      position: absolute;
      font-family: Cambria,Georgia,serif; 
      font-size: 11pt;
      color: rgba(0, 0, 0, 0);
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
      color: rgba(0, 0, 0, 0);
      width: 31mm;
      white-space: nowrap;
      left: 7mm;
      top: 135mm;
    }

    .ttd-2 {
      position: absolute;
      font-family: Cambria,Georgia,serif; 
      font-size: 11pt;
      color: rgba(0, 0, 0, 0);
      width: 31mm;
      white-space: nowrap;
      left: 61mm;
      top: 135mm;
    }

    .ttd-3 {
      position: absolute;
      font-family: Cambria,Georgia,serif; 
      font-size: 11pt;
      color: rgba(0, 0, 0, 0);
      width: 31mm;
      white-space: nowrap;
      left: 122mm;
      top: 135mm;
    }

    .ttd-4 {
      position: absolute;
      font-family: Cambria,Georgia,serif; 
      font-size: 11pt;
      color: rgba(0, 0, 0, 0);
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
      Rp. {{ $amount }}
    </div>
    <div class="text-amount">
      {{ $text_amount }}
    </div>
    <div class="total-amount">
      Rp. {{ $amount }}
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