<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{!!$title!!}</title>
    <style>
        
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            text-align: center;
            color: #777;
            margin: 0;
    padding: 0;
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
            font-size: 11px;
            line-height: 20px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }
        .invoice-box *{
            font-size: 12px;
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

        .invoice-box table tr.item:last-child {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
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
    </style>
</head>
<body>

    <div class="invoice-box">

        <table width="100">
            <tr >
                <td colspan="14" align="left" style="text-align: left !important"><strong>Item Route Performance Report</strong>
             </td>
            </tr>
              <tr>
               <td colspan="14" align="left" style="text-align: left !important"><strong>{{ $supplierexcel }}</strong></td>
            </tr>
              <tr>
                <td colspan="14" align="left" style="text-align: left !important"><strong>Date Range: {{$from}} to {{ $to}} </strong></td>
            </tr>
        </table> 

        <table>
        <tbody>
                <tr class="heading">
                    <td style="text-align: center !important"><strong>ITEM CODE</strong></td>
                    <td style="text-align: center !important"><strong>ITEM NAM</strong>E</td>
                    @foreach ($customers as $customer)
                    <td style="text-align: center !important"><strong>{{ $customer }}</strong></td>
                     @endforeach
                     <td style="text-align: center !important"><strong>Total</strong></td>
                </tr>
                
                    @foreach($data as $element)
                   
                        <tr>
                            <td>{{ $element->stock_id_code }}</td>
                            <td>{{ $element->title }}</td>
                            @php
                                $sum_net_qty = 0;
                            @endphp
                            @foreach($customers as $index => $customer)
                                @php
                                    $mother_qty_key = 'mother_qty_' . $index;
                                    $mother_returns_key = 'mother_returns_' . $index;
                                    $qty_key = 'qty_' . $index;
                                    $returns_key = 'returns_' . $index;
                                    $mother_qty = $element->$mother_qty_key ?? 0;
                                    $mother_returns = $element->$mother_returns_key?? 0;
                                    $qty = $element->$qty_key ?? 0;
                                    $returns = $element->$returns_key ?? 0;
                                    $conversion_factor = $element->conversion_factor ?? 0;
                                    if($conversion_factor != null){
                                        $net_qty = (($qty-$returns) / $conversion_factor) + ($mother_qty - $mother_returns);


                                    }else{
                                        $net_qty =  ($mother_qty - $mother_returns);
                                    }
                                    $sum_net_qty += $net_qty;
                                @endphp
                                <td>{{ number_format($net_qty, 1) }}</td>
                            @endforeach
                            <td><strong>{{ number_format($sum_net_qty, 1) }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
                {{-- <tfoot>
                    <tr>
                        <th colspan="2">Grand Total</th>
                        @php
                            $grand_total = 0;
                            $customers_qty_arr = array_fill(0, count($customers), 0);
                            foreach ($data as $element) {
                                foreach ($customers as $index => $customer) {
                                    $qty = $element['qty_' . $index] ?? 0;
                                    $returns = $element['returns_' . $index] ?? 0;
                                    $net_qty = max(0, $qty - $returns);
                                    $grand_total += $net_qty;
                                    $customers_qty_arr[$index] += $net_qty;
                                }
                            }
                        @endphp
                        <th><strong>{{ $grand_total }}</strong></th>
                        @foreach($customers_qty_arr as $qty)
                            <th>{{ $qty }}</th>
                        @endforeach
                    </tr>
                </tfoot> --}}
              
            
                
        </table>
     
    </div>   
</body>
</html>