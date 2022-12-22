<!DOCTYPE html>
<html>

<head>
  <title>Bon Merah</title>
  <style>
    @page {
      margin: 0px;
    }

    .ma {
      position: absolute;
      font-size: 0.875rem;
      left: 11.25%;
      top: 30.71428571428571%;
    }

    .desc {
      position: absolute;
      font-size: 0.875rem;
      width: 47.5%;
      white-space: normal;
      left: 20.833333333333336%;
      top: 30.71428571428571%;
    }

    .note {
      position: absolute;
      font-size: 0.875rem;
      text-align: right;
      width: 47.5%;
      white-space: normal;
      left: 20.833333333333336%;
      top: 60.71428571428571%;
    }

    .amount {
      position: absolute;
      font-size: 0.875rem;
      text-align: right;
      width: 16.666666666666664%;
      white-space: normal;
      right: 11.25%;
      top: 60.71428571428571%;
    }

    .text-amount {
      position: absolute;
      font-size: 0.875rem;
      width: 47.5%;
      white-space: normal;
      left: 20.833333333333336%;
      top: 67.85714285714286%;
    }

    .total-amount {
      position: absolute;
      font-size: 0.875rem;
      width: 16.666666666666664%;
      text-align: right;
      white-space: normal;
      right: 11.25%;
      top: 67.85714285714286%;
    }

    .ttd-1 {
      position: absolute;
      font-size: 0.875rem;
      width: 12.916666666666668%;
      text-align: center;
      white-space: nowrap;
      left: 11.25%;
      bottom: 5%;
    }

    .ttd-2 {
      position: absolute;
      font-size: 0.875rem;
      width: 12.916666666666668%;
      text-align: center;
      white-space: nowrap;
      left: 32.916666666666664%;
      bottom: 5%;
    }

    .ttd-3 {
      position: absolute;
      font-size: 0.875rem;
      width: 12.916666666666668%;
      text-align: center;
      white-space: nowrap;
      left: 54.58333333333333%;
      bottom: 5%;
    }

    .ttd-4 {
      position: absolute;
      font-size: 0.875rem;
      width: 12.916666666666668%;
      text-align: center;
      white-space: nowrap;
      left: 78.33333333333333%;
      bottom: 5%;
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