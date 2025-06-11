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
            line-height: 25px;
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
            /* background: #eee; */
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
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
                        <h4 style="text-align: left; margin:0">SALES SUMMARY REPORT</h4>
                    </th>
                    <th>
                        <h4 style="text-align: right; margin:0">{{ is_null($branch) ? '' :  (is_int($branch)? '' : "BRANCH: $branch->name") }}
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
        <table>
            <thead>
                <tr class="heading">
                    <td>DATE</td>
                    <td style="text-align:right">VATABLE SALES</td>
                    <td style="text-align:right">16% VAT</td>
                    <td style="text-align:right">ZERO RATED</td>
                    <td style="text-align:right">EXEMPT</td>
                    <td style="text-align:right">TOTAL SALES</td>
                  
                </tr>
            </thead>
            <tbody>
                <?php 
                $total_sales_all = $total_vat_16 = $total_vat_0 = $total_vat_exempt = $total_tax=0;
                    
                    ?>
                @foreach ($salesData as $data)
                <tr class="item">
                    <td>{{ \Carbon\Carbon::parse($data->sales_date)->toDateString() }}</td>
                    <td style="text-align:right">{{ manageAmountFormat($data->total_sale_16 - ($data->returns_16 ?? 0) - ($data->total_vat_amount_16) + ((16 * $data->returns_16) / 116) ) }}</td>
                    <td style="text-align:right">{{ manageAmountFormat($data->total_vat_amount_16 - ((16 * $data->returns_16) / 116)) }}</td>
                    <td style="text-align:right">{{ manageAmountFormat($data->total_sale_0  - ($data->returns_0 ?? 0)) }}</td>
                    <td style="text-align:right">{{ manageAmountFormat($data->total_sale_exempt - ($data->returns_exempt ?? 0)) }}</td>
                    <td style="text-align:right">{{ manageAmountFormat($data->total_sales - ($data->returns_16 ?? 0) - ($data->returns_0 ?? 0) - ($data->returns_exempt ?? 0)) }}</td>


                </tr>
                <?php
                    $total_tax += $data->total_vat_amount_16 - ((16 * $data->returns_16) / 116);  
                    $total_sales_all += $data->total_sales - ($data->returns_16 ?? 0) - ($data->returns_0 ?? 0) - ($data->returns_exempt ?? 0);
                    $total_vat_16 += ($data->total_sale_16 - ($data->returns_16 ?? 0)  - ($data->total_vat_amount_16) + ((16 * $data->returns_16) / 116));
                    $total_vat_0 += $data->total_sale_0 - ($data->returns_0 ?? 0);
                    $total_vat_exempt += $data->total_sale_exempt - ($data->returns_exempt ?? 0);
                 ?>
                    
                @endforeach  
                <tr style="    border-top: 2px dashed #cecece;">
                    <td colspan="6"></td>
                </tr>
                <tr>
                    <th>Total</th>
                    <th style="text-align:right">{{ manageAmountFormat($total_vat_16) }}</th>
                    <th style="text-align:right">{{ manageAmountFormat($total_tax) }}</th>
                    <th style="text-align:right">{{ manageAmountFormat($total_vat_0) }}</th>
                    <th style="text-align:right">{{ manageAmountFormat($total_vat_exempt) }}</th>
                    <th style="text-align:right">{{ manageAmountFormat($total_sales_all) }}</th>




                </tr>
                <tr style="border-top: 2px dashed #cecece;">
                    <td colspan="6"></td>
                </tr> 
            </tbody>
        </table>
        <br>
        <h4>Stock Take Sales Summary</h4>
        <table>
            <thead>
                <tr class="heading">
                    <td>DATE</td>
                    <td style="text-align:right">VATABLE SALES</td>
                    <td style="text-align:right">16% VAT</td>
                    <td style="text-align:right">ZERO RATED</td>
                    <td style="text-align:right">EXEMPT</td>
                    <td style="text-align:right">TOTAL SALES</td>
                  
                </tr>
            </thead>
            <tbody>
                <?php 
                $total_sales_all = $total_vat_16 = $total_vat_0 = $total_vat_exempt = $total_tax = 0;
                    
                    ?>
                @foreach ($stockSaleSummary as $data)
                <tr class="item">
                    <td>{{ \Carbon\Carbon::parse($data->sales_date)->toDateString() }}</td>
                    <td style="text-align:right">{{ manageAmountFormat($data->stock_sale_16 - ($data->stock_return_16 ?? 0) -  ($data->stock_sale_vat_16 ?? 0) + ($data->stock_return_vat_16 ?? 0) ) }}</td>
                    <td style="text-align:right">{{ manageAmountFormat(($data->stock_sale_vat_16 ?? 0) - ($data->stock_return_vat_16 ?? 0)) }}</td>
                    <td style="text-align:right">{{ manageAmountFormat(($data->sales_zero_rated ?? 0) - ($data->returns_zero_rated ?? 0)) }}</td>
                    <td style="text-align:right">{{ manageAmountFormat(($data->sales_exempt ?? 0) - ($data->returns_exempt ?? 0)) }}</td>
                    <td style="text-align:right">{{ manageAmountFormat(($data->total_sales ?? 0) - ($data->total_returns ?? 0)) }}</td>


                </tr>
                <?php
                    $total_sales_all += ($data->total_sales ?? 0) - ($data->total_returns ?? 0);
                    $total_tax += ($data->stock_sale_vat_16 ?? 0) - ($data->stock_return_vat_16 ?? 0);
                    $total_vat_16 += ($data->stock_sale_16 - ($data->stock_return_16 ?? 0) -  ($data->stock_sale_vat_16 ?? 0) + ($data->stock_return_vat_16 ?? 0) );
                    $total_vat_0 += (($data->sales_zero_rated ?? 0) - ($data->returns_zero_rated ?? 0));
                    $total_vat_exempt += (($data->sales_exempt ?? 0) - ($data->returns_exempt ?? 0));
                ?>
                    
                @endforeach  
                <tr style="    border-top: 2px dashed #cecece;">
                    <td colspan="6"></td>
                </tr>
                <tr>
                    <th>Total</th>
                    <th style="text-align:right">{{ manageAmountFormat($total_vat_16) }}</th>
                    <th style="text-align:right">{{ manageAmountFormat($total_tax) }}</th>
                    <th style="text-align:right">{{ manageAmountFormat($total_vat_0) }}</th>
                    <th style="text-align:right">{{ manageAmountFormat($total_vat_exempt) }}</th>
                    <th style="text-align:right">{{ manageAmountFormat($total_sales_all) }}</th>




                </tr>
                <tr style="border-top: 2px dashed #cecece;">
                    <td colspan="6"></td>
                </tr> 
            </tbody>
        </table>
      
          
   
    </div>
</body>

</html>
