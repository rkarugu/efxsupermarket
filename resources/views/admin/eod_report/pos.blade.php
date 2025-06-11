<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report</title>
    <style>
        @media print {
            @page {
                size: landscape
            }
        }

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
            font-weight: bold;
            /* margin-top: 10px; */
            margin-bottom: 0px;
            /* font-style: italic; */
            /* color: #555 */
            color: #000;
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
            line-height: 2px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            /* padding-bottom: 40px; */
        }

        .invoice-box table tr.heading td {
            /* background: #eee; */
            border-bottom: 1px solid #858383;
            font-weight: bold;
        }

        .invoice-box table tr.details {
            /* padding-bottom: 20px; */
        }

        /* .invoice-box table tr.item td:last-child {
            border-bottom: 1px solid #eee;
        } */

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        th {
            font-weight: bolder;
        }

        td {
            font-weight: bold;
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
                        <h4 style="text-align: left; margin:0">EOD REPORT</h4>
                    </th>
                    <th>
                        <h4 style="text-align: right; margin:0">{{ is_null($branch) ? 'BRANCH: ALL' : "BRANCH: $branch->name" }}
                        </h4>
                    </th>
                </tr>
                <tr>
                    <th>
                        <h5 style="text-align: left; margin:0">Input By: {{ $user->name }}</h5>
                    </th>
                    <th>
                        <h5 style="text-align: right; margin:0">DATE:
                            {{ date('d-M-Y', strtotime(request()->date)) }} | TIME: {{ date('H:i A') }}</h5>
                    </th>
                </tr>
            </tbody>
        </table>
        @if ($type == 'pos')
         
        {{--POS SALES--}}
        <table>
          
            <tbody>          
            @php
                $sum_INV = 0;
                $cash_sales = $cash_sales_returns = $invoices = $invoices_return = $petty_cash = $customer_receipt = $eazzy = $vooma = $mpesa = $totalNetCash = 0;
            @endphp
                <tr class="heading">
                    <td style="font-size: 11px">#</td>
                    <td style="font-size: 11px">CASHIER</td>
                    <td style="text-align:right;font-size: 11px">CS</td>
                    <td style="text-align:right;font-size: 11px">CSR</td>
                    <td style="text-align:right;font-size: 11px">INV</td>
                    <td style="text-align:right;font-size: 11px">CRN</td>
                    <td style="text-align:right;font-size: 11px">NET SALES</td>
                    {{-- <td style="text-align:right;font-size: 11px">EXP</td> --}}
                    <td style="text-align:right;font-size: 11px">SCP</td>
                    <td style="text-align:right;font-size: 11px">CRD</td>
                    <td style="text-align:right;font-size: 11px">CRC</td>
                    <td style="text-align:right;font-size: 11px">NETCASH</td>

                    <td style="text-align:right;font-size: 11px">Eazzy</td>
                    <td style="text-align:right;font-size: 11px">Vooma</td>
                    <td style="text-align:right;font-size: 11px">Mpesa</td>
                    <td style="text-align:right;font-size: 11px">CDM</td>

                    <td style="text-align:right;font-size: 11px">Cheque</td>
                    <td style="text-align:right;font-size: 11px">EC</td>

                </tr>

                <tr>
                    <td colspan="17" style="font-weight: bold; text-align: left; font-size: 12px;">Cash Sales</td>
                </tr>
        {{-- @if($posSalesExist) --}}

                @php
                   $pos_items_count = $total_exp = $invoice_sales
                    = $invoice_sales_returns = $cash_sales_inner = 
                    $expectedCash = $cash_sales_returns_inner = 
                    $cash_sale_vooma = $cash_sale_eazzy = $cash_sale_mpesa = 
                    $cash_sale_cheque  = $cash_sale_crc = $cash_sale_scp = $cash_sale_crd =
                    $cash_sale_cdm = $cash_sale_net_cash = 0;
                @endphp
                @foreach ($posSales as $posSale)
                    @php
                        $expectedCashValue = ($posSale->cash_sales ?? 0) - ($posSale->cash_returns ?? 0) - ($posSale->Eazzy ?? 0) - ($posSale->Vooma ?? 0) - ($posSale->Mpesa ?? 0) - ($posSale->CDM ?? 0) + (($posSale->CRC ?? 0) * -1) + ($posSale->CRD ?? 0) - ($posSale->SCP ?? 0);
                    @endphp
                    <tr style="border-bottom: 1px solid #ccc">
                        <td style="font-size: 9px">{{ $loop->index + 1 }}</td>
                        <td style="margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ ucwords(strtolower($posSale->cashier)) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($posSale->cash_sales) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($posSale->cash_returns) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($posSale->invoice_sales ?? 0) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($posSale->invoice_sales_returns ?? 0) }}</td>
                       
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat(($posSale->cash_sales ?? 0) - ($posSale->cash_returns ?? 0) + ($posSale->invoice_sales ?? 0) - ($posSale->invoice_sales_returns ?? 0)) }}</td>
                        {{-- <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat(($posSale->exp ?? 0)) }}</td> --}}
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat(($posSale->SCP ?? 0)) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat(($posSale->CRD ?? 0)) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat(($posSale->CRC ?? 0) * -1) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{-- {{ manageAmountFormat(($posSale->Eazzy ?? 0)+($posSale->Vooma ?? 0)+($posSale->Mpesa ?? 0) + ($expectedCashValue ?? 0) + (($posSale->CRC ?? 0) * -1) + ($posSale->CRD ?? 0) - ($posSale->SCP ?? 0) + ($posSale->CDM ?? 0)) }}</td> --}}
                            {{ manageAmountFormat(($posSale->Eazzy ?? 0)+($posSale->Vooma ?? 0)+($posSale->Mpesa ?? 0) + ($posSale->CDM ?? 0) + ($expectedCashValue ?? 0)) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($posSale->Eazzy ?? 0) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($posSale->Vooma ?? 0) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($posSale->Mpesa ?? 0) }}</td>
                            <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($posSale->CDM ?? 0) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($posSale->Cheque ?? 0) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($expectedCashValue) }}</td>
                       
           
                    </tr>
                        @php
                            $pos_items_count = $pos_items_count + 1;
                            $cash_sales = $cash_sales ?? 0.00;
                            $cash_sales_returns = $cash_sales_returns ?? 0.00;
                            $eazzy = $eazzy ?? 0.00;
                            $vooma = $vooma ?? 0.00;
                            $mpesa = $mpesa ?? 0.00;
                            $cheque = $cheque ?? 0.00;
                            $crc = $crc ?? 0.00;
                            $scp = $scp ?? 0.00;
                            $totalNetCash = $totalNetCash ?? 0.00;
                            
                            $cash_sales_inner += $posSale->cash_sales ?? 0.0;
                            $cash_sales_returns_inner += $posSale->cash_returns ?? 0.0;
                            $invoice_sales += $posSale->invoice_sales ?? 0.0;
                            $invoice_sales_returns += $posSale->invoice_sales_returns?? 0.0;
                            // $total_exp  += ($posSale->exp ?? 0);
                            $cash_sale_vooma += $posSale->Vooma ?? 0.0;
                            $cash_sale_mpesa += $posSale->Mpesa ?? 0.0;
                            $cash_sale_eazzy += $posSale->Eazzy ?? 0.0;
                            $cash_sale_crc  +=  ($posSale->CRC ?? 0.0) * -1;
                            $cash_sale_scp += $posSale->SCP ?? 0;
                            $cash_sale_crd += $posSale->CRD ?? 0;
                            $cash_sale_cdm += $posSale->CDM ?? 0;
                            $cash_sale_cheque += $posSale->Cheque ?? 0.0;
                            $cash_sale_net_cash += (($posSale->Eazzy ?? 0)+($posSale->Vooma ?? 0)+($posSale->Mpesa ?? 0) + ($posSale->CDM ?? 0) + ($expectedCashValue ?? 0) );
                            $cash_sales += $posSale->cash_sales ?? 0.0;
                            $cash_sales_returns += $posSale->cash_returns ?? 0.0;
                            $expectedCash += $expectedCashValue;
                            $eazzy += $posSale->Eazzy ?? 0.0;
                            $vooma += $posSale->Vooma ?? 0.0;
                            $mpesa += $posSale->Mpesa ?? 0.0;
                            $cheque += $posSale->cheque ?? 0.0;
                            $crc += ($posSale->CRC ?? 0.0) * -1;

                            $totalNetCash += ($posSale->Eazzy ?? 0.0) + ($posSale->Vooma ?? 0.0) + ($posSale->Mpesa ?? 0.0)  + ($posSale->CDM ?? 0) + ($expectedCashValue ?? 0) ;
                            
                        @endphp

                    
                @endforeach
              
                <tr style="border-top: 1px solid #000;border-bottom:1px solid #000;">
                    <td colspan="2" style="font-size: 11px;text-align:left;">Sub Totals</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sales_inner) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sales_returns_inner) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($invoice_sales) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($invoice_sales_returns) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sales - $cash_sales_returns + $invoice_sales - $invoice_sales_returns) }}</td>
                    {{-- <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($total_exp) }}</td> --}}
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sale_scp) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sale_crd) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sale_crc) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sale_net_cash) }}</td>

                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sale_eazzy ) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sale_vooma) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sale_mpesa ) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sale_cdm ) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sale_cheque) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($expectedCash) }}</td>

                </tr>
  
                <tr style="border-top: 2px solid #000;">
                    <td colspan="17"></td>
                </tr>
                <tr style="border-top: 1px solid #000;border-bottom:1px solid #000;">
                    <td colspan="2" style="font-size: 11px;text-align:left;">Returns - Chief Cashier</td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right">{{manageAmountFormat($tabletReturns)}}</td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right">{{ '('.manageAmountFormat($tabletReturns).')' }}</td>
                    {{-- <td style="font-size: 9px;text-align:right"></td> --}}
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right">{{ '('.manageAmountFormat($tabletReturns).')' }}</td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right">{{ '('.manageAmountFormat($tabletReturns).')' }}</td>

                </tr>
                {{-- <tr style="border-top: 1px solid #000;border-bottom:1px solid #000;">
                    <td colspan="2" style="font-size: 11px;text-align:left;">Pos Cash Payments</td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right">{{ '('.manageAmountFormat($posCashPayments).')' }}</td>

                </tr> --}}
                <tr style="border-top: 1px solid #000;border-bottom:1px solid #000;">
                    <td colspan="2" style="font-size: 11px;text-align:left;">Cash Banking</td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right"></td>
                    <td style="font-size: 9px;text-align:right">{{ '('.manageAmountFormat($cashBanking).')' }}</td>

                </tr>
                <tr style="border-top: 1px solid #000;border-bottom:1px solid #000;">
                    <td colspan="2" style="font-size: 11px;text-align:left;">Grand Totals</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sales_inner) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sales_returns_inner + $tabletReturns) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($invoice_sales) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($invoice_sales_returns) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sales - $cash_sales_returns + $invoice_sales - $invoice_sales_returns - $tabletReturns) }}</td>
                    {{-- <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($total_exp) }}</td> --}}
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sale_scp) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sale_crd) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sale_crc) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sale_net_cash - $tabletReturns) }}</td>

                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sale_eazzy ) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sale_vooma ) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sale_mpesa ) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sale_cdm ) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sale_cheque ) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($expectedCash - $tabletReturns - $cashBanking) }}</td>

                </tr>
                <tr style="border-top: 2px solid #000;">
                    <td colspan="17"></td>
                </tr>
            
               

            @endif
             
            </tbody>
        </table>

          {{-- CDM  --}}
          <table style="text-align: left;">
            <tbody>
                <tr class="top">
                    <th>
                        <h3>CDM</h3>
                    </th>
                </tr>

            </tbody>
        </table>
        <table>
            <thead>
                <tr class="heading" style="border-bottom: 1px solid #858383;">
                    <th style="font-size: 11px">#</th>
                    <th style="text-align: left;font-size: 11px">Dropped By</th>
                    <th style="text-align: left;font-size: 11px">Cashier</th>
                    <th style="text-align: left;font-size: 11px">Document No.</th>
                    <th style="text-align: left;font-size: 11px">Bank Receipt No.</th>
                    <th style="text-align: right;font-size: 11px">Dropped Amount</th>
                    <th style="text-align: right;font-size: 11px">Banked Amount</th>
                    <th style="text-align: right;font-size: 11px">Variance (EB)</th>

                </tr>
            </thead>
            <tbody>
                @php
                    $totalDropped = $totalBanked  = $totalVariance =  0; 
                @endphp
                @foreach ($cdmTransactions as $drop)
                <tr class="item">
                    <td style="font-size: 9px">{{ $loop->index + 1 }}</td>
                    <td style="font-size: 9px">{{ $drop->chiefcashier_name }}
                    <td style="font-size: 9px">{{ $drop->cashier_name }}
                    <td style="font-size: 9px">{{ $drop->reference }}
                    <td style="font-size: 9px">{{ $drop->bank_receipt_number  ??  '-' }}
                    <td style="font-size: 9px; text-align: right;">{{ manageAmountFormat($drop->amount ?? 0) }}
                    <td style="font-size: 9px; text-align: right;">{{ manageAmountFormat($drop->banked_amount ?? 0) }}
                    <td style="font-size: 9px; text-align: right;">{{ manageAmountFormat($drop->amount - $drop->banked_amount) }}
                    </td>
                </tr>
                @php
                    $totalDropped += $drop->amount;
                    $totalBanked += $drop->banked_amount;
                    $totalVariance += $drop->amount - $drop->banked_amount;
                
                @endphp
                
                    
                @endforeach
                <tr style="border-top: 1px solid #000;border-bottom:1px solid #000;">
                    <td style="font-size: 9px" colspan="5">Total</td>
                    <td style="font-size: 9px; text-align: right;">{{ manageAmountFormat($totalDropped) }}</td>
                    <td style="font-size: 9px; text-align: right;">{{ manageAmountFormat($totalBanked) }}</td>
                    <td style="font-size: 9px; text-align: right;">{{ manageAmountFormat($totalVariance) }}</td>
                </tr>
                <tr style="border-top: 1px solid #000;border-bottom:1px solid #000;">
                    <td style="font-size: 9px" colspan="7">Returns - Chief Cashier</td>
                    <td style="font-size: 9px; text-align: right;">{{ '('.manageAmountFormat($tabletReturns) .')'}}</td>
                </tr>
                <tr style="border-top: 1px solid #000;border-bottom:1px solid #000;">
                    <td style="font-size: 9px" colspan="7">Cash Banking</td>
                    <td style="font-size: 9px; text-align: right;">{{ '('.manageAmountFormat($cashBanking) .')'}}</td>
                </tr>
                {{-- <tr style="border-top: 1px solid #000;border-bottom:1px solid #000;">
                    <td style="font-size: 9px" colspan="7">Pos Cash Payments</td>
                    <td style="font-size: 9px; text-align: right;">{{ '('.manageAmountFormat($posCashPayments) .')'}}</td>
                </tr> --}}
                <tr style="border-top: 1px solid #000;border-bottom:1px solid #000;">
                    <td style="font-size: 9px" colspan="5">Grand Totals</td>
                    <td style="font-size: 9px; text-align: right;">{{ manageAmountFormat($totalDropped) }}</td>
                    <td style="font-size: 9px; text-align: right;">{{ manageAmountFormat($totalBanked) }}</td>
                    <td style="font-size: 9px; text-align: right;">{{ manageAmountFormat($totalVariance - $tabletReturns - $cashBanking) }}</td>
                </tr>
            </tbody>
        </table>

        

        {{-- TOTAL SALES SUMMARY --}}
        @php
            $sum_TOTAL = ($cash_sales_inner ?? 0) - ($cash_sales_returns_inner ?? 0) - ($tabletReturns ?? 0) + ($invoice_sales ?? 0) - ($invoice_sales_returns ?? 0);
        @endphp
    <table style="text-align: left;">
        <tbody>
            <tr class="top">
                <th colspan="9">
                    <h3>TOTAL SALES SUMMARY</h3>
                </th>
            </tr>

            <tr class="item">
                <th style="text-align:center" style="font-size: 11px"><span
                        style="border-bottom:1px solid #000;">Cash Sales</span></th>
                <th>-</th>
                <th style="text-align:center" style="font-size: 11px"><span
                        style="border-bottom:1px solid #000;">CSR</span></th>
                <th>+</th>
                <th style="text-align:center" style="font-size: 11px"><span
                        style="border-bottom:1px solid #000;">INV</span></th>
                <th>-</th>
                <th style="text-align:center" style="font-size: 11px"><span
                        style="border-bottom:1px solid #000;">CRN</span></th>
                <th>=</th>
                <th style="text-align:center" style="font-size: 11px"><span
                        style="border-bottom:1px solid #000;">TotalSales</span></th>
            </tr>

            <tr class="item">
                <th style="text-align:center;font-size:9px" colspan="1">
                    {{ manageAmountFormat($cash_sales_inner ?? 0) }}</th>
                <th></th>
                <th style="text-align:center;font-size:9px" colspan="1">{{ manageAmountFormat(($cash_sales_returns_inner ?? 0) + ($tabletReturns ?? 0) ) }}
                </th>
                <th></th>
                <th style="text-align:center;font-size:9px" colspan="1">{{ manageAmountFormat($invoice_sales ?? 0) }}
                </th>
                <th></th>
                <th style="text-align:center;font-size:9px" colspan="1">{{ manageAmountFormat($invoice_sales_returns ?? 0) }}
                </th>
                <th></th>
                <th style="text-align:center;font-size:9px" colspan="1">{{ manageAmountFormat($sum_TOTAL ?? 0) }}
                </th>
            </tr>

        </tbody>
    </table>


    {{-- CHEQUES  SECTION --}}

                 {{-- CHEQUES RECEIVED   --}}
               
                 <table style="text-align: left;">
                    <tbody>
                        <tr class="top">
                            <th>
                                <h3>CHEQUES RECEIVED</h3>
                            </th>
                        </tr>
    
                    </tbody>
                </table>
                <table>
                    <thead>
                        <tr class="heading" style="border-bottom: 1px solid #858383;">
                            <th style="font-size: 11px">#</th>
                            <th style="text-align: left;font-size: 11px">Customer</th>
                            <th style="text-align: left;font-size: 11px">Cheque Date</th>
                            <th style="text-align: left;font-size: 11px">Cheque No.</th>
                            <th style="text-align: left;font-size: 11px">Drawers Name</th>
                            <th style="text-align: left;font-size: 11px">Drawers Bank</th>
                            <th style="text-align: right;font-size: 11px">Amount</th>
    
                        </tr>
                    </thead>
                        <tbody>
                            @php
                                $total_cheques = 0;
                            @endphp
                            @foreach ($receivedCheques as $maturedCheque)
                                <tr class="item" style="border-bottom: 1px solid #ccc">
                                    <td style="font-size: 9px">{{ $loop->index +1 }}</td>
                                    {{-- <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->route_name)) }}</td> --}}
                                    <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->customer_name)) }}</td>
                                    <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->cheque_date)) }}</td>
                                    <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->cheque_no)) }}</td>
                                    <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->drawers_name)) }}</td>
                                    <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->drawers_bank)) }}</td>
                                    <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($maturedCheque->amount) }}</td>
                                </tr>
                                @php
                                    $total_cheques += $maturedCheque->amount;
                                @endphp
                                
                            @endforeach
                            <tr class="item" style="border-bottom: 1px solid #ccc">
                                <td style="font-size: 9px" colspan="6">Totals</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($total_cheques) }}</td>
                            </tr>
                        </tbody>
                
                   
                </table>
    
                  {{-- CHEQUES BANKED   --}}
                   
                <table style="text-align: left;">
                    <tbody>
                        <tr class="top">
                            <th>
                                <h3>CHEQUES BANKED</h3>
                            </th>
                        </tr>
    
                    </tbody>
                </table>
                <table>
                    <thead>
                        <tr class="heading" style="border-bottom: 1px solid #858383;">
                            <th style="font-size: 11px">#</th>
                            <th style="text-align: left;font-size: 11px">Customer</th>
                            <th style="text-align: left;font-size: 11px">Date Received</th>
                            <th style="text-align: left;font-size: 11px">Cheque Date</th>
                            <th style="text-align: left;font-size: 11px">Cheque No.</th>
                            <th style="text-align: left;font-size: 11px">Drawers Name</th>
                            <th style="text-align: left;font-size: 11px">Drawers Bank</th>
                            <th style="text-align: left;font-size: 11px">Bank Deposited</th>
                            <th style="text-align: left;font-size: 11px">Deposited By</th>
                            <th style="text-align: right;font-size: 11px">Amount</th>
    
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total_cheques = 0;
                        @endphp
                        @foreach ($bankedCheques as $maturedCheque)
                            <tr class="item" style="border-bottom: 1px solid #ccc">
                                <td style="font-size: 9px">{{ $loop->index +1 }}</td>
                                {{-- <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->route_name)) }}</td> --}}
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->customer_name)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->date_received)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->cheque_date)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->cheque_no)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->drawers_name)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->drawers_bank)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->bank)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->depositer)) }}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($maturedCheque->amount) }}</td>
                            </tr>
                            @php
                                $total_cheques += $maturedCheque->amount;
                            @endphp
                            
                        @endforeach
                        <tr class="item" style="border-bottom: 1px solid #ccc">
                            <td style="font-size: 9px" colspan="9">Totals</td>
                            <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($total_cheques) }}</td>
                        </tr>
                    </tbody>
                 
                   
                </table>

                 {{-- CHEQUES BANKED   --}}
                   
                 <table style="text-align: left;">
                    <tbody>
                        <tr class="top">
                            <th>
                                <h3>CHEQUES NOT BANKED</h3>
                            </th>
                        </tr>
    
                    </tbody>
                </table>
                <table>
                    <thead>
                        <tr class="heading" style="border-bottom: 1px solid #858383;">
                            <th style="font-size: 11px">#</th>
                            <th style="text-align: left;font-size: 11px">Customer</th>
                            <th style="text-align: left;font-size: 11px">Date Received</th>
                            <th style="text-align: left;font-size: 11px">Cheque Date</th>
                            <th style="text-align: left;font-size: 11px">Cheque No.</th>
                            <th style="text-align: left;font-size: 11px">Drawers Name</th>
                            <th style="text-align: left;font-size: 11px">Drawers Bank</th>
                            <th style="text-align: right;font-size: 11px">Amount</th>
    
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total_cheques = 0;
                        @endphp
                        @foreach ($unbankedCheques as $maturedCheque)
                            <tr class="item" style="border-bottom: 1px solid #ccc">
                                <td style="font-size: 9px">{{ $loop->index +1 }}</td>
                                {{-- <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->route_name)) }}</td> --}}
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->customer_name)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->date_received)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->cheque_date)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->cheque_no)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->drawers_name)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->drawers_bank)) }}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($maturedCheque->amount) }}</td>
                            </tr>
                            @php
                                $total_cheques += $maturedCheque->amount;
                            @endphp
                            
                        @endforeach
                        <tr class="item" style="border-bottom: 1px solid #ccc">
                            <td style="font-size: 9px" colspan="7">Totals</td>
                            <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($total_cheques) }}</td>
                        </tr>
                    </tbody>
                 
                   
                </table>




                 {{-- UNPAID CHEQUES   --}}
                   
                 <table style="text-align: left;">
                    <tbody>
                        <tr class="top">
                            <th>
                                <h3> UNPAID CHEQUES </h3>
                            </th>
                        </tr>
    
                    </tbody>
                </table>
                <table>
                    <thead>
                        <tr class="heading" style="border-bottom: 1px solid #858383;">
                            <th style="font-size: 11px">#</th>
                            <th style="text-align: left;font-size: 11px">Customer</th>
                            <th style="text-align: left;font-size: 11px">Cheque Date</th>
                            <th style="text-align: left;font-size: 11px">Cheque No.</th>
                            <th style="text-align: left;font-size: 11px">Drawers Name</th>
                            <th style="text-align: left;font-size: 11px">Drawers Bank</th>
                            <th style="text-align: left;font-size: 11px">Bank Deposited</th>
                            <th style="text-align: left;font-size: 11px">Deposited By</th>
                            <th style="text-align: right;font-size: 11px">Amount</th>
                            <th style="text-align: right;font-size: 11px">Penalty</th>

    
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total_cheques = $total_penalty = 0;
                        @endphp
                        @foreach ($unpaidCheques as $maturedCheque)
                            <tr class="item" style="border-bottom: 1px solid #ccc">
                                <td style="font-size: 9px">{{ $loop->index +1 }}</td>
                                {{-- <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->route_name)) }}</td> --}}
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->customer_name)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->cheque_date)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->cheque_no)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->drawers_name)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->drawers_bank)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->bank)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->depositer)) }}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($maturedCheque->amount) }}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($maturedCheque->bounce_penalty) }}</td>

                            </tr>
                            @php
                                $total_cheques += $maturedCheque->amount;
                                $total_penalty += $maturedCheque->bounce_penalty;
                            @endphp
                            
                        @endforeach
                        <tr class="item" style="border-bottom: 1px solid #ccc">
                            <td style="font-size: 9px" colspan="8">Totals</td>
                            <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($total_cheques) }}</td>
                            <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($total_penalty) }}</td>
                        </tr>
                    </tbody>
                   
                   
                </table>
    
                 {{-- CRC   --}}
                   
                 <table style="text-align: left;">
                    <tbody>
                        <tr class="top">
                            <th>
                                <h3> CRC</h3>
                            </th>
                        </tr>
    
                    </tbody>
                </table>
                <table>
                    <thead>
                        <tr class="heading" style="border-bottom: 1px solid #858383;">
                            <th style="font-size: 11px">#</th>
                            <th style="text-align: left;font-size: 11px">Customer</th>
                            <th style="text-align: left;font-size: 11px">Received By</th>
                            <th style="text-align: right;font-size: 11px">Amount</th>
    
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total_CRC = 0;
                        @endphp
                        @foreach ($crcRecords as $record)
                            <tr class="item" style="border-bottom: 1px solid #ccc">
                                <td style="font-size: 9px">{{ $loop->index +1 }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($record->customer)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($record->received_by)) }}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($record->amount) }}</td>

                            </tr>
                            @php
                                $total_CRC += $record->amount;
                            @endphp
                            
                        @endforeach
                        <tr class="item" style="border-bottom: 1px solid #ccc">
                            <td style="font-size: 9px" colspan="3">Totals</td>
                            <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($total_CRC) }}</td>
                        </tr>
                    </tbody>
                   
                </table>
    {{-- CHEQUES SECTION --}}

      {{-- SALE SUMMARY  --}}
      <table style="text-align: left;">
        <tbody>
            <tr class="top">
                <th>
                    <h3>SALES SUMMARY</h3>
                </th>
            </tr>

        </tbody>
    </table>
    <table>
        <thead>
            <tr class="heading" style="border-bottom: 1px solid #858383;">
                <th style="font-size: 11px">#</th>
                <th style="text-align: left;font-size: 11px">DATE</th>
                <th style="text-align: right;font-size: 11px">VATABLE SALES</th>
                <th style="text-align: right;font-size: 11px">16% TAX</th>
                <th style="text-align: right;font-size: 11px">ZERO RATED</th>
                <th style="text-align: right;font-size: 11px">EXEMPT</th>
                <th style="text-align: right;font-size: 11px">TOTAL SALES</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_sales_all = $total_vat_16 = $total_vat_0 = $total_vat_exempt = $total_tax = 0;
            
            ?>
            @foreach ($saleSummary as $data)
                <tr class="item">
                    <td style="font-size: 9px">{{ $loop->index + 1 }}</td>
                    <td style="font-size: 9px">{{ \Carbon\Carbon::parse($data->sales_date)->toDateString() }}
                    </td>
                    <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($data->total_sale_16 - ($data->returns_16 ?? 0) -($data->cash_return_16) - ($data->total_vat_amount_16) + ((16 * $data->returns_16) / 116) +  ((16 * $data->cash_return_16) / 116)) }}
                    </td>
                    <td style="text-align:right;font-size: 9px">
                        {{ manageAmountFormat($data->total_vat_amount_16 - ((16 * $data->returns_16) / 116) - ((16 * $data->cash_return_16) / 116)) }}</td> 
                    <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($data->total_sale_0 - ($data->returns_0 ?? 0) - ($data->cash_return_0 ?? 0)) }}
                    </td>
                    
                    <td style="text-align:right;font-size: 9px">
                        {{ manageAmountFormat($data->total_sale_exempt - ($data->returns_exempt ?? 0) - ($data->cash_return_exempt ?? 0)) }}
                    </td>
                  
                    <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($data->total_sales - ($data->returns_16 ?? 0) - ($data->cash_return_16 ?? 0) - ($data->returns_0 ?? 0) - ($data->cash_return_0 ?? 0) - ($data->returns_exempt ?? 0) - ($data->cash_return_exempt ?? 0)) }}</td>


                </tr>
                <?php
                $total_sales_all += ($data->total_sales - ($data->returns_16 ?? 0) - ($data->cash_return_16 ?? 0) - ($data->returns_0 ?? 0) - ($data->cash_return_0 ?? 0) - ($data->returns_exempt ?? 0) - ($data->cash_return_exempt ?? 0));
                $total_tax += $data->total_vat_amount_16 - ((16 * $data->returns_16) / 116) - ((16 * $data->cash_return_16) / 116);
                $total_vat_16 += ($data->total_sale_16  - ($data->returns_16 ?? 0) - ($data->cash_return_16 ?? 0)  - ($data->total_vat_amount_16) + ((16 * $data->returns_16) / 116)+ ((16 * $data->cash_return_16) / 116));
                $total_vat_0 += ($data->total_sale_0  - ($data->returns_0 ?? 0) - ($data->cash_return_0 ?? 0));
                $total_vat_exempt += ($data->total_sale_exempt  - ($data->returns_exempt ?? 0) - ($data->cash_return_exempt ?? 0));
                ?>
            @endforeach

            <tr style="border-bottom: 2px solid black; border-top: 2px solid black;">
                <th style="font-size: 11px" colspan="2">Total</th>
                <th style="text-align:right; font-size: 9px">{{ manageAmountFormat($total_vat_16) }}</th>
                <th style="text-align:right; font-size: 9px">{{ manageAmountFormat($total_tax) }}</th>
                <th style="text-align:right; font-size: 9px">{{ manageAmountFormat($total_vat_0) }}</th>
                <th style="text-align:right; font-size: 9px">{{ manageAmountFormat($total_vat_exempt) }}</th>
                <th style="text-align:right; font-size: 9px">{{ manageAmountFormat($total_sales_all) }}</th>

            </tr>

        </tbody>
    </table>

      {{-- EXPENSES  --}}
        <table style="text-align: left;">
            <tbody>
                <tr class="top">
                    <th>
                        <h3>EXPENSES</h3>
                    </th>
                </tr>

            </tbody>
        </table>
        @php
            $otherExpensesGrandTotal = 0;
        @endphp

        @foreach ($pettyCashRequestTypes as $pettyCashRequestType)
          
            @php
                $headers = [];
                $data = null;
                switch ($pettyCashRequestType->slug){
                    case 'parking-fees' :
                        $headers = [];
                        $data = [];
                        $total = 0;
                        foreach($pettyCashRequestTypesData as $item){
                            if($item->pettyCashRequest?->type == 'parking-fees'){
                                $data[] = [
                                    $item->route?->route_name,
                                    $item->payee_name,
                                    $item->payment_reason,
                                    $item->deliverySchedule?->vehicle?->license_plate_number,
                                    $item->employee?->name,
                                    manageAmountFormat($item->amount)
                                ];
                                $total += $item->amount;
                                $otherExpensesGrandTotal += $item->amount;

                            }
                        }
                        if (!empty($data)) {
                            $headers = ['Route', 'Payee', 'Payment Reason', 'Vehicle', 'Driver', 'Amount'];
                            $data [] = [
                                '',
                                'Total',
                                '',
                                '',
                                '',
                                manageAmountFormat($total)

                            ];
                        }  

                        break;
                    case 'driver-grn' :
                        $headers = [];
                        $data = [];
                        $total = 0;

                        foreach($pettyCashRequestTypesData as $item){
                            if($item->pettyCashRequest?->type == 'driver-grn'){
                                $data[] = [
                                    $item->grn_number ? $item->grn_number : $item->transfer?->transfer_no,
                                    $item->payee_name,
                                    $item->payment_reason,
                                    $item->grn_number ? $item->grn?->supplier?->name : $item->transfer?->fromStoreDetail?->location_name,
                                    manageAmountFormat($item->amount)
                                ];
                                $total += $item->amount;
                                $otherExpensesGrandTotal += $item->amount;
                            }
                        }
                        if (!empty($data)) {
                            $headers = ['Document No', 'Payee', 'Payment Reason', 'Source','Amount'];
                            $data [] = [
                                '',
                                'Total',
                                '',
                                '',
                                manageAmountFormat($total)

                            ];
                         
                        }
                      
                        break;
                    case 'staff-welfare' :
                        $headers = [];
                        $data = [];
                        $total = 0;

                        foreach($pettyCashRequestTypesData as $item){
                            if($item->pettyCashRequest?->type == 'staff-welfare'){
                                $data[] = [
                                    $item->payee_name,
                                    $item->payment_reason,
                                    manageAmountFormat($item->amount)
                                ];
                                $total += $item->amount;
                                $otherExpensesGrandTotal += $item->amount;
                            }
                        }
                      
                        if (!empty($data)) {
                            $headers = ['Payee', 'Payment Reason', 'Amount'];
                            $data [] = [
                                '',
                                'Total',
                                '',
                                '',
                                manageAmountFormat($total)
                            ];
                        }
                       
                        break;
                    case 'repairs-maintenance-buildings':
                        $headers = [];
                        $data = [];
                        $total = 0;

                        foreach($pettyCashRequestTypesData as $item){
                            if($item->pettyCashRequest?->type == 'repairs-maintenance-buildings'){
                                $data[] = [
                                    $item->payee_name,
                                    $item->payment_reason,
                                    manageAmountFormat($item->amount)
                                ];
                                $total += $item->amount;
                                $otherExpensesGrandTotal += $item->amount;
                            }
                        }
                        if (!empty($data)) {
                            $headers = ['Payee', 'Payment Reason', 'Amount'];
                            $data [] = [
                                '',
                                'Total',
                                manageAmountFormat($total)

                            ];
                        }
                       
                        break;
                    case 'repairs-maintenance-motor-vehicle':
                        $headers = [];
                        $data = [];
                        $total = 0;

                        foreach($pettyCashRequestTypesData as $item){
                            if($item->pettyCashRequest?->type == 'repairs-maintenance-motor-vehicle'){
                                $data[] = [
                                    $item->payee_name,
                                    $item->payment_reason,
                                    manageAmountFormat($item->amount)
                                ];
                                $total += $item->amount;
                                $otherExpensesGrandTotal += $item->amount;
                            }
                        }
                        if (!empty($data)) {
                            $headers = ['Payee', 'Payment Reason', 'Amount'];
                            $data [] = [
                                '',
                                'Total',
                                manageAmountFormat($total)

                            ];
                        }
                       
                        break;
                    default:
                        $headers = [];
                        $data = [];
                        break;
                }
            @endphp
            @if (!empty($headers))
                <table>
                    <tbody>
                        <tr >
                            <th colspan="16" style="font-weight: bold; text-align: left; font-size: 12px;">
                                <h4 >{{$pettyCashRequestType->name}}</h4>
                            </th>
                        </tr>
                    </tbody>
                </table>
                <table>
                    <thead>
                        <tr class="heading" style="border-bottom: 1px solid #858383;">
                            <th style="font-size: 11px">#</th>
                            @foreach ($headers as $header)
                            <th style="text-align: {{ $header == 'Amount' ? 'right' : 'left' }}; font-size: 11px">{{ $header }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                        <tr class="item" style="{{ $loop->last ? 'font-weight: bold; border-top: 2px solid #000;' : '' }}">
                            <td style="font-size: 9px">{{ $loop->last ? '' : $loop->index + 1 }}</td>
    
                                @foreach ($item as $value)
                                    <td style="font-size: 9px">{{ $value }}</td>
                                    
                                @endforeach
                            
                            </tr>
                                                        
                        @endforeach
                    </tbody>
    
                </table>
                
            @endif
               
           
            
        @endforeach
        <table>
            <tbody>
                <tr style="border-bottom: 2px solid black; border-top: 2px solid black;">
                    <th style="font-size: 11px; font-weight:bold;" colspan="2">Expenses Total</th>
                    <th style="text-align:right; font-size: 9px; font-weight:bold;">{{ manageAmountFormat($otherExpensesGrandTotal) }}</th>
                 
                </tr>
            </tbody>
        </table>
    

      {{-- SCP  --}}
        <table style="text-align: left;">
            <tbody>
                <tr class="top">
                    <th>
                        <h3>SUPPLIER CASH PAYMENTS</h3>
                    </th>
                </tr>

            </tbody>
        </table>
        <table>
            <thead>
                <tr class="heading" style="border-bottom: 1px solid #858383;">
                    <th style="font-size: 11px">#</th>
                    <th style="text-align: left;font-size: 11px">Petty Cash No</th>
                    <th style="text-align: left;font-size: 11px">Initiated By</th>
                    <th style="text-align: left;font-size: 11px">Payee</th>
                    <th style="text-align: left;font-size: 11px">Payment Reason</th>
                    <th style="text-align: right;font-size: 11px">Amount</th>
                    

                </tr>
            </thead>
            <tbody>
                @php
                    $totalScp =  0; 
                @endphp
                @foreach ($scpRecords as $record)
                <tr class="item">
                    <td style="font-size: 9px">{{ $loop->index + 1 }}</td>
                    <td style="font-size: 9px">{{ $record->petty_cash_no }}
                    <td style="font-size: 9px">{{ $record->created_by }}
                    <td style="font-size: 9px">{{ $record->payee_name }}
                    <td style="font-size: 9px">{{ $record->payment_reason }}
                    <td style="font-size: 9px; text-align: right;">{{ manageAmountFormat($record->amount ?? 0) }}</td>
                </tr>
                @php
                    $totalScp += $record->amount;
                @endphp
                @endforeach
                <tr style="border-top: 1px solid #000;border-bottom:1px solid #000;">
                    <td style="font-size: 9px" colspan="5">Total</td>
                    <td style="font-size: 9px; text-align: right;">{{ manageAmountFormat($totalScp) }}</td>
                    
                </tr>
            </tbody>
        </table>

        {{-- BANKING --}}


        {{-- Debtors --}}
    
        <table style="text-align: left;">
            <tbody>
                <tr class="top">
                    <th>
                        <h3>DEBTORS</h3>
                    </th>
                </tr>

            </tbody>
        </table>
        <table>
            <thead>
                <tr class="heading" style="border-bottom: 1px solid #858383;">
                    <th style="font-size: 11px">#</th>
                    <th style="text-align: left;font-size: 11px">Customer</th>
                    <th style="text-align: right;font-size: 11px">Balance B/F</th>
                    <th style="text-align: right;font-size: 11px">Debits</th>
                    <th style="text-align: right;font-size: 11px">Credits</th>
                    <th style="text-align: right;font-size: 11px">Last Trans </th>
                    <th style="text-align: right;font-size: 11px">PD Cheques</th>
                    <th style="text-align: right;font-size: 11px">Balance</th>

                </tr>
            </thead>
            <tbody>
                @php
                    $balanceBfTotal = 0;
                    $debitsTotal = 0;
                    $creditsTotal = 0;
                    $pdChqsTotal = 0;
                    $balanceTotal = 0;
                    $index = 1;

                @endphp
                @foreach ($debtors as $record)
                        <tr class="item" style="border-bottom: 1px solid #ccc">
                            <td style="font-size: 9px">{{ $index ++ }}</td>
                            <td style="font-size: 9px">{{ $record['customer']}}</td>
                            <td style="text-align:right; font-size: 9px">{{ number_format($record['balance_bf'], 2)  }}</td>
                            <td style="text-align:right; font-size: 9px"> {{ number_format($record['debits'], 2) }} </td>
                            <td style="text-align:right; font-size: 9px"> {{ number_format($record['credits'], 2) }} </td>
                            <td style="text-align:right; font-size: 9px">{{ $record['last_trans_time'] }}</td>
                            <td style="text-align:right; font-size: 9px"> {{ number_format($record['pd_cheques'], 2) }} </td>
                            <td style="text-align:right; font-size: 9px"> {{ number_format($record['balance'], 2) }} </td>
                        
                            @php
                            $balanceBfTotal += $record['balance_bf'];
                            $debitsTotal += $record['debits'];
                            $creditsTotal += $record['credits'];
                            $pdChqsTotal += $record['pd_cheques'];
                            $balanceTotal += $record['balance'];

                            @endphp
                        </tr>
                @endforeach
                <tr style="border-bottom: 2px solid black; border-top: 2px solid black;">
                    <th style="font-size: 11px" colspan="2">Total</th>
                    <th style="text-align:right;font-size: 9px">{{  number_format($balanceBfTotal, 2) }}</th>
                    <th style="text-align:right;font-size: 9px">{{  number_format($debitsTotal, 2) }}</th>
                    <th style="text-align:right;font-size: 9px">{{  number_format($creditsTotal, 2) }}</th>
                    <th></th>
                    <th style="text-align:right;font-size: 9px">{{  number_format($pdChqsTotal, 2) }}</th>
                    <th style="text-align:right;font-size: 9px">{{  number_format($balanceTotal, 2) }}</th>




                
                </tr>
            </tbody>
        </table>  
            
        {{-- @endif --}}
        
    </div>
</body>

</html>