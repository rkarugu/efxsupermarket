<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dispatch Summary</title>
  <style>
    body {
      font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
      text-align: center;
      color: #777;
    }

    body h1 {
      font-weight: 300;
      margin-bottom: 0px;
      padding-bottom: 0px;
      color: #000;
    }

    body h3 {
      font-weight: 300;
      margin-top: 10px;
      margin-bottom: 20px;
      font-style: italic;
      color: #555;
    }

    body a {
      color: #06f;
    }

    .invoice-box {
      margin: auto;
      font-size: 12px;
      line-height: 20px;
      font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
      color: #555;
    }

    .invoice-box table {
      width: 100%;
      line-height: inherit;
      text-align: left;
      border-collapse: collapse;
    }

    .invoice-box table td {
      padding: 3px;
      vertical-align: top;
    }

    .invoice-box table tr td:last-child {
      text-align: right;
    }

    .invoice-box table tr.top table td {
      padding-bottom: 20px;
    }

    .invoice-box table tr.top table td.title {
      font-size: 45px;
      line-height: 45px;
      color: #333;
    }

    .invoice-box table tr.information table td {
      padding-bottom: 40px;
    }

    .invoice-box table tr.heading td {
      border-bottom: 1px solid #ddd;
      font-weight: bold;
    }

    .invoice-box table tr.details td {
      padding-bottom: 20px;
    }

    .invoice-box table tr.item td {
      border-bottom: 1px solid #eee;
    }


    @media only screen and (max-width: 600px) {
      .invoice-box table tr.top table td {
        width: 100%;
        display: block;
        text-align: center;
      }

      .invoice-box table tr.information table td {
        width: 100%;
        display: block;
        text-align: center;
      }
    }
    .horizontal_dotted_line {
      text-align: left !important;
    }
  </style>
</head>
<body>

<div class="invoice-box">
  <table  style="text-align: center;">
    <tbody>
    <tr class="top">
      <th colspan="3">
        <h2>Delivery Loading Sheet</h2>
      </th>
    </tr>
    </tbody>
  </table>
  <br>
  <table>
    <tbody>
    <tr class="heading">
      <th style="text-align:left">Particulars</th>
      <th style="text-align:right">Total Quantity</th>
      <th style="text-align:right">Tonnage</th>
      <th style="text-align:right">Total Tonnage</th>
    </tr>
    @php
      $products = [
          ['title' => 'RICE BASMATI PARBOIL 25KG', 'quantity' => '5', 'gross_weight' => '25', 'cost' => '3500', 'price' => '3550'],
           ['title' => 'MENENGAI LEMON 10*1KG CTN','quantity' => '2', 'gross_weight' => '10', 'cost' => '2144', 'price' => '2550'],
           ['title' => 'FAMILA PURE WIMBI 20X1KG BALE', 'quantity' => '3', 'gross_weight' => '20', 'cost' => '2440', 'price' => '2900'],
           ['title' => 'MBUNI HOME BAKING FLOUR 12X2KG', 'quantity' => '6', 'gross_weight' => '24', 'cost' => '1300', 'price' => '1330'],
      ];
      $tonnage = 0;
      $total_cost=0;
      $total_price=0;
      $fuel_consumed = 20;
      $distance_covered = 100;
      $cost_per_liter = 188;
    @endphp
    @foreach ($products as $product)

      <tr class="item ">
        <td>{{@$product['title']}}</td>
        <td style="text-align:right">{{manageAmountFormat(@$product['quantity'])}}</td>
        <td style="text-align:right">{{manageAmountFormat(@$product['gross_weight'])}}</td>
        <td style="text-align:right">{{manageAmountFormat(@$product['quantity'] * @$product['gross_weight'])}}</td>
      </tr>
      @php
        $total_cost += $product['quantity'] * $product['cost'];
        $total_price += $product['quantity'] * $product['price'];
        $tonnage += $product['quantity'] * $product['gross_weight'];
      @endphp
    @endforeach
    <tr class="item ">
      <th style="text-align:right" colspan = "3">Tonnage: </th>
      <th style="text-align: right">{{manageAmountFormat($tonnage)}}</th>
    </tr>
    </tbody>
  </table>
  <hr>
  <table>
    <tbody>
    <tr>
      <th style="text-align:left">Total Tonnage: {{manageAmountFormat($tonnage)}}</th>
      <th style="text-align:right">Total Gross Profit: {{ manageAmountFormat( $total_price - $total_cost) }}</th>
    </tr>
    <tr>
      <th style="text-align:left" colspan="3">Fuel Consumed In Liters: {{ $fuel_consumed }} LTRS</th>
    </tr>
    <tr>
      <th style="text-align:left" colspan="3">Distance Covered: {{ $distance_covered }} KM</th>
    </tr>
    <tr>
      <th style="text-align:left" colspan="3">Cost Per Liter: {{ $cost_per_liter }} KES</th>
    </tr>
    <tr>
      <th style="text-align:left" colspan="3">Total Fuel Cost: {{ $fuel_consumed * $cost_per_liter }} KES</th>
    </tr>
    <tr>
      @if(($total_price - $total_cost) - ($fuel_consumed * $cost_per_liter) < 0)
        <th style="text-align:left; color: red" colspan="3">Trip Net Profit: {{ manageAmountFormat(($total_price - $total_cost) - ($fuel_consumed * $cost_per_liter)) }}</th>
      @else
        <th style="text-align:left; color: green" colspan="3">Trip Net Profit: {{ manageAmountFormat(($total_price - $total_cost) - ($fuel_consumed * $cost_per_liter)) }}</th>
      @endif

    </tr>
    </tbody>
  </table>

</div>
</body>
</html>
