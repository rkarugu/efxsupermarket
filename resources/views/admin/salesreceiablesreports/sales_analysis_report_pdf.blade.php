<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report</title>
    <style>
        .horizontal_dotted_line {
            display: flex;
        }

        .horizontal_dotted_line:after {
            border-bottom: 2px dashed #b2b2b2;
            ;
            content: '';
            flex: 1;
        }

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
            /* margin-top: 10px; */
            margin-bottom: 20px;
            font-style: italic;
            color: #555;
        }

        body a {
            color: #06f;
        }

        .invoice-box {
            /* margin: auto; */
            font-size: 12px;
            line-height: 18px;
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
            padding: 0px !important;
            vertical-align: top;
        }

        .invoice-box table tr td:last-child {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            /* padding-bottom: 20px; */
            border-spacing: 0px !important;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 20px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            /* padding-bottom: 40px; */
        }

        .invoice-box table tr.heading td {
            /* background: #eee; */
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details  {
            /* padding-bottom: 20px; */
        }

        /* .invoice-box table tr.item td:last-child {
            border-bottom: 1px solid #eee;
        } */

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
    <?php $all_settings = getAllSettings(); ?>
    <div class="invoice-box">
        <table style="width: 100%; margin-bottom: 20px">
            <tbody>
                <tr>
                    <th colspan="2">
                        <h2 style="text-align: left; margin:0">{!! strtoupper($all_settings['COMPANY_NAME']) !!}</h2>
                    </th>
                </tr>
                <tr>
                    <th>
                        <h4 style="text-align: left; margin:0">SALES ANALYSIS</h4>
                    </th>
                    <th>
                        <h4 style="text-align: right; margin:0">{{ is_null($branch) ? '' : "BRANCH: $branch" }}
                        </h4>
                    </th>
                </tr>
                <tr>
                    <th>
                        <h4 style="text-align: left; margin:0">Input By: {{ $user->name }}</h4>
                    </th>
                    <th>
                        <h4 style="text-align: right; margin:0">DATE FROM:
                            {{ date('d-M-Y', strtotime(request()->date)) }} |
                            DATE TO {{ date('d-M-Y', strtotime(request()->todate)) }} | TIME: {{ date('H:i A') }}</h4>
                    </th>
                </tr>
            </tbody>
        </table>
        @php
            $grandTotalCostExcl = $grandTotalCostIncl = $grandTotalSaleExcl = $grandTotalSaleIncl = $grandTotalProfitExcl = $grandTotalProfitIncl = $index = $totalMargin =  0;
        @endphp
        @foreach ($categories as $category)
        <h4 style="text-align: left !important;">{{ $category->category_description }}</h4>

        <table>
            <thead>
                <tr class="heading">
                    <td>#</td>
                    {{-- <td>Code</td> --}}
                    <td >Title</td>
                    <td>Suppliers</td>
                    <td style="text-align:right">Quantity</td>
                    <td style="text-align:right">CostExcl</td>
                    <td style="text-align:right">CostIncl</td>
                    <td style="text-align:right">PriceExcl</td>
                    <td style="text-align:right">PriceIncl</td>
                    <td style="text-align:right">TotalCostExcl</td>
                    <td style="text-align:right">TotalCostIncl</td>
                    <td style="text-align:right">TotalSaleExcl</td>
                    <td style="text-align:right">TotalSaleIncl</td>
                    <td style="text-align:right">ProfitOnExcl</td>
                    <td style="text-align:right">ProfitOnIncl</td>
                    <td style="text-align:right">Margin</td>
                    {{-- <td style="text-align:right">Markup</td> --}}
                </tr>
            </thead>
            <tbody>
                @php
                $totalCostExcl = $totalCostIncl = $totalSaleExcl = $totalSaleIncl = $totalProfitExcl = $totalProfitIncl = 0;
            @endphp
              
                @foreach ($data as $item)
                    @if ($item->category_id == $category->id)
                  
                    <tr>
                        <th>{{$index+1}}</th>
                        {{-- <td style="text-align:left; margin-top: 0px !important; padding-top: 0px !important;">{{ $item->stock_id_code }}</td> --}}
                        <td style="text-align:left; margin-top: 0px !important; padding-top: 0px !important;">{{ $item->item_title }}</td>
                        <td style="text-align:left; margin-top: 0px !important; padding-top: 0px !important;">{{ $item->suppliers }}</td>
                        <td style="text-align:center; margin-top: 0px !important; padding-top: 0px !important;">{{ (int)$item->quantity }}</td>
                        <td style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;">{{ manageAmountFormat(($item->standard_cost * 100) /  (100 + $item->tax_value) ) }}</td>
                        <td style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;">{{ manageAmountFormat($item->standard_cost) }}</td>
                        <td style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;">{{ manageAmountFormat(($item->selling_price * 100) /  (100 + $item->tax_value) )  }}</td>
                        <td style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;">{{ manageAmountFormat($item->selling_price) }}</td>
                        <td style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;">{{ manageAmountFormat(($item->actual_standard_cost * 100) /  (100 + $item->tax_value) ) }}</td>
                        <td style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;">{{ manageAmountFormat($item->actual_standard_cost) }}</td>
                        <td style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;">{{ manageAmountFormat(($item->total_selling_price_with_vat  * 100) /  (100 + $item->tax_value) )  }}</td>
                        <td style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;">{{ manageAmountFormat($item->total_selling_price_with_vat) }}</td>
                        <td style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;">{{ manageAmountFormat((($item->total_selling_price_with_vat  * 100) /  (100 + $item->tax_value))-(($item->actual_standard_cost * 100) /  (100 + $item->tax_value))) }}</td>
                        <td style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;">{{ manageAmountFormat(($item->total_selling_price_with_vat)-($item->actual_standard_cost)) }}</td>
                        <td style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;">{{ manageAmountFormat(((($item->total_selling_price_with_vat)-($item->actual_standard_cost)) / ($item->actual_standard_cost != 0 ? $item->actual_standard_cost : 1) ) * 100) . "%"}}</td>
                        {{-- <td></td> --}}
                        @php
                            $index += 1;
                            $totalCostExcl += ($item->actual_standard_cost * 100) /  (100 + $item->tax_value);
                            $totalCostIncl += $item->actual_standard_cost;
                            $totalSaleExcl += ($item->total_selling_price_with_vat  * 100) /  (100 + $item->tax_value);
                            $totalSaleIncl += $item->total_selling_price_with_vat;
                            $totalProfitExcl += ((($item->total_selling_price_with_vat  * 100) /  (100 + $item->tax_value))-(($item->actual_standard_cost * 100) /  (100 + $item->tax_value)));
                            $totalProfitIncl += (($item->total_selling_price_with_vat)-($item->actual_standard_cost));
                            $totalMargin += (((($item->total_selling_price_with_vat)-($item->actual_standard_cost)) / ($item->actual_standard_cost != 0 ? $item->actual_standard_cost : 1) ) * 100);
                        @endphp

                    </tr>
                   
                    
                        
                    @endif
                @endforeach
                <tr>
                    <th colspan="8">TOTAL</th>
                    <th style="text-align: right">{{ manageAmountFormat($totalCostExcl)}}</th>
                    <th style="text-align: right">{{ manageAmountFormat($totalCostIncl) }}</th>
                    <th style="text-align: right">{{ manageAmountFormat($totalSaleExcl) }}</th>
                    <th style="text-align: right">{{manageAmountFormat($totalSaleIncl)}}</th>
                    <th style="text-align: right">{{manageAmountFormat($totalProfitExcl)}}</th>
                    <th style="text-align: right">{{manageAmountFormat($totalProfitIncl) }}</th>
                    <th style="text-align: right"></th>
                    <th style="text-align: right"></th>
                    @php
                        $grandTotalCostExcl += $totalCostExcl;
                        $grandTotalCostIncl += $totalCostIncl;
                        $grandTotalSaleExcl += $totalSaleExcl;
                        $grandTotalSaleIncl += $totalSaleIncl;
                        $grandTotalProfitExcl += $totalProfitExcl;
                        $grandTotalProfitIncl += $totalProfitIncl;
                    @endphp
                </tr>

            </tbody>
         
            
        </table>

            
        @endforeach
        {{-- totals of totals --}}
        <table>
            <thead>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>

            </thead>
            <tbody>
                <tr>
                    <th style="text-align: right"></th>
                    <th style="text-align: right"></th>
                    <th style="text-align: right"></th>
                    <th style="text-align: right"></th>
                    <th style="text-align: right"></th>
                    <th style="text-align: right"></th>
                    <th style="text-align: right"></th>

                    <th >GRAND TOTAL</th>
                    <th style="text-align: right">{{manageAmountFormat($grandTotalCostExcl)}}</th>
                    <th style="text-align: right">{{manageAmountFormat($grandTotalCostIncl)}}</th>
                    <th style="text-align: right">{{manageAmountFormat($grandTotalSaleExcl)}}</th>
                    <th style="text-align: right">{{manageAmountFormat($grandTotalSaleIncl)}}</th>
                    <th style="text-align: right">{{manageAmountFormat($grandTotalProfitExcl)}}</th>
                    <th style="text-align: right">{{manageAmountFormat($grandTotalProfitIncl)}}</th>
                    <th style="text-align: right"></th>
                    <th style="text-align: right">{{ manageAmountFormat($totalMargin / $itemCount) }}</th>
                </tr>

            </tbody>
        </table>
      
    </div>
</body>

</html>
