@php
    $getLoggeduserProfile = getLoggeduserProfile();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Cash Sales</title>
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
        <table  style="text-align: center;">
            <tbody>
                <tr class="top">
                    <th colspan="2">
                        <h2 style="font-size:18px !important">{{getAllSettings()['COMPANY_NAME']}}</h2>
                    </th>
                </tr>
                <tr class="top">
                    <td colspan="2" style="    text-align: center;">{{getAllSettings()['ADDRESS_2']}}</td>
                </tr>
                <tr class="top">
                    <!-- <th  colspan="1" style="width: 50%">
                        VAT NO:
                    </th> -->
                    <th colspan="2" style="width: 100%;text-align:center">
                        VAT ANALYSIS SUMMARIZED BY VAT CODE
                    </th>
                 

                </tr>
                <tr class="top">
                    <th  colspan="1" style="width: 50%;text-align:left">From Date : {{request()->from ? date('d/m/Y',strtotime(request()->from)) : NULL}}</th>
                   <th  colspan="1" style="width: 50%;text-align:right">To : {{request()->to ? date('d/m/Y',strtotime(request()->to)) : NULL}}</th>
                </tr>

            <table >
                        <thead>
                        <tr>
                            <th style="width: 3%">#</th>
                            <th>Vat  Group</th>
                            <th>Customer Pin</th>
                            <th>Customer Name</th>
                            <th> Date</th>
                            <th> Cu Invoice Number</th>
                            <th> Tax Amount</th>
                            <th> Amount Exclusive of VAT</th>
                            
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($customer as $val)
                        @php

                        $rate=($val['vat_rate'] == 0 ? 0: ($val['vat_rate'] /100)); 
                         if($rate>0)
                         { $vat=($val['selling_price'])*($val['quantity']) * $rate ; 
                     }else
                     {
                      $vat=0; 

                     } 
                     @endphp 

                            <tr>
                                <th scope="row" style="width: 3%;">{{ $loop->index + 1 }}</th>
                                     <td>
                                             @if($val['tax_manager_id'] == 1) VAT 16% @elseif($val['tax_manager_id'] == 2) Zero Vat @elseif($val['tax_manager_id'] == 3) Vat Exempted @else N/A @endif
                                    </td>
                                      <td>{{$val['customer_pin']}}</td>
                                      <td>{{$val['name']}}</td>
                                      <td>{{$val['requisition_date']}}</td>
                                      <td>{{$val['name']}}</td>
                                     <td>{{number_format ($vat, 2)}}</td>
                                     <th>{{ number_format ((floatval($val['selling_price'])) * (floatval($val['quantity'])),2) }}</th>
                                     
                                
                            </tr>

                        @endforeach
                        </tbody>
                    </table>
    </div>   
</body>
</html>