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
                        <h4 style="text-align: left; margin:0">TILL SUMMARY REPORT</h4>
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
                        <h5 style="text-align: right; margin:0">DATE FROM:
                            {{ date('d-M-Y', strtotime(request()->date)) }} |
                            DATE TO {{ date('d-M-Y', strtotime(request()->todate)) }} | TIME: {{ date('H:i A') }}</h5>
                    </th>
                </tr>
            </tbody>
        </table>
        <table>
          
            <tbody>
        @php
            $countRoutesData =$data->filter(function($item) {
                return ($item->vcs != 0) || ((($item->Eazzy ?? 0.0) + ($item->Mpesa ?? 0.0) + ($item->Vooma ?? 0.0)) != 0);
            })->count();
        @endphp
        @if ($countRoutesData > 0)
        <tr class="heading">
            <td style="font-size: 11px">#</td>
            <td style="font-size: 11px">ROUTE</td>
            <td style="text-align:right;font-size: 11px">CS</td>
            <td style="text-align:right;font-size: 11px">CSR</td>
            <td style="text-align:right;font-size: 11px">VCS</td>
            <td style="text-align:right;font-size: 11px">VCR</td>
            <td style="text-align:right;font-size: 11px">INV</td>
            <td style="text-align:right;font-size: 11px">SALES</td>
            <td style="text-align:right;font-size: 11px">EXP</td>
            <td style="text-align:right;font-size: 11px">EC</td>
            <td style="text-align:right;font-size: 11px">Eazzy</td>
            <td style="text-align:right;font-size: 11px">Vooma</td>
            <td style="text-align:right;font-size: 11px">Mpesa</td>
            <td style="text-align:right;font-size: 11px">NET</td>
        </tr>
        
                <tr>
                    <td colspan="15" style="font-weight: bold; text-align: left; font-size: 12px;">Order Taking</td>
                </tr>
                @php
                    $order_taking_netcash = 0;
                    $delivery_netcash = 0;
                    $totalOrderTakingNetCash = 0;
                    $totalDeliveryNetCash = 0;

                    $order_taking_cash_sales = 0;
                    $order_taking_cash_sales_returns = 0;
                    $order_taking_invoices = 0;
                    $order_taking_invoices_return = 0;
                    $order_taking_petty_cash = 0;
                    $order_taking_customer_receipt = 0;
                    $order_taking_eazzy = 0;
                    $order_taking_vooma = 0;
                    $order_taking_mpesa = 0;

                    $delivery_cash_sales = 0;
                    $delivery_cash_sales_returns = 0;
                    $delivery_invoices = 0;
                    $delivery_invoices_return = 0;
                    $delivery_petty_cash = 0;
                    $delivery_customer_receipt = 0;
                    $delivery_eazzy = 0;
                    $delivery_vooma = 0;
                    $delivery_mpesa = 0;

                    $sum_INV = 0;
                    $order_taking_sum_INV = 0;
                    $delivery_sum_INV = 0;

                    $cash_sales = $cash_sales_returns = $invoices = $invoices_return = $petty_cash = $customer_receipt = $eazzy = $vooma = $mpesa = $totalNetCash = 0;
                    $data = $data->filter(function ($item) {
                        return $item->cs != 0 ||
                            $item->csr != 0 ||
                            $item->vcs != 0 ||
                            $item->returns != 0 ||
                            $item->vcr != 0 ||
                            $item->inv_backend != 0 ||
                            $item->petty_cash != 0 ||
                            $item->Eazzy != 0 ||
                            $item->Vooma != 0 ||
                            $item->Mpesa != 0;
                    });
                @endphp
                @foreach ($data->filter(function ($item) {
        return $item->vcs != 0;
    }) as $order_taking_item)
                    @php
                        $total =
                            ($order_taking_item->cs ?? 0.0) +
                            ($order_taking_item->vcs ?? 0.0) -
                            ($order_taking_item->csr ?? 0.0) -
                            ($order_taking_item->returns ?? 0.0);
                    @endphp

                    <tr>
                        <td style="font-size: 9px">{{ $loop->index + 1 }}</td>
                        <td style="margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ ucwords(strtolower($order_taking_item->name)) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($order_taking_item->cs) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($order_taking_item->csr) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($order_taking_item->vcs) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($order_taking_item->returns) }}</td>
                        @php
                            $total = 0;
                            $order_taking_sum_INV += $order_taking_item->inv_backend - $order_taking_item->inv_backend_return;
                            $sum_INV += $order_taking_item->inv_backend - $order_taking_item->inv_backend_return;
                            $total =
                                ($order_taking_item->cs ?? 0.0) +
                                ($order_taking_item->vcs ?? 0.0) -
                                ($order_taking_item->csr ?? 0.0) -
                                ($order_taking_item->returns ?? 0.0);
                        @endphp
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($order_taking_item->inv_backend - $order_taking_item->inv_backend_return) }}
                        </td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($total) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($order_taking_item->petty_cash) }}</td>
                            <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat(0) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($order_taking_item->Eazzy) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($order_taking_item->Vooma) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($order_taking_item->Mpesa) }}</td>
                        @php
                            $order_taking_netcash = 0;
                            $order_taking_netcash =
                                ($order_taking_item->Eazzy ?? 0.0) +
                                ($order_taking_item->Mpesa ?? 0.0) +
                                ($order_taking_item->Vooma ?? 0.0);
                            $cash_sales += $order_taking_item->cs ?? 0.0;
                            $order_taking_cash_sales += $order_taking_item->cs ?? 0.0;
                            $cash_sales_returns += $order_taking_item->csr ?? 0.0;
                            $order_taking_cash_sales_returns += $order_taking_item->csr ?? 0.0;
                            $invoices += $order_taking_item->vcs ?? 0.0;
                            $order_taking_invoices += $order_taking_item->vcs ?? 0.0;
                            $order_taking_invoices += $order_taking_item->vcs ?? 0.0;
                            $invoices_return += $order_taking_item->returns ?? 0.0;
                            $order_taking_invoices_return += $order_taking_item->returns ?? 0.0;
                            $petty_cash += $order_taking_item->petty_cash ?? 0.0;
                            $order_taking_petty_cash += $order_taking_item->petty_cash ?? 0.0;
                            $customer_receipt += abs($order_taking_item->customer_receipt ?? 0.0);
                            $order_taking_customer_receipt += abs($order_taking_item->customer_receipt ?? 0.0);
                            $eazzy += $order_taking_item->Eazzy ?? 0.0;
                            $order_taking_eazzy += $order_taking_item->Eazzy ?? 0.0;
                            $vooma += $order_taking_item->Vooma ?? 0.0;
                            $order_taking_vooma += $order_taking_item->Vooma ?? 0.0;
                            $mpesa += $order_taking_item->Mpesa ?? 0.0;
                            $order_taking_mpesa += $order_taking_item->Mpesa ?? 0.0;
                            $totalOrderTakingNetCash += $order_taking_netcash ?? 0.0;
                            $totalNetCash += $order_taking_netcash ?? 0.0;
                        @endphp
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($order_taking_netcash) }}</td>
                    </tr>
                @endforeach


                <tr style="border-top: 1px solid #000;border-bottom:1px solid #000;">
                    <td colspan="2" style="font-size: 11px;text-align:left;">Totals</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($order_taking_cash_sales) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($order_taking_cash_sales_returns) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($invoices) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($order_taking_invoices_return) }}</td>
                    @php
                        $order_taking_total1 = 0;
                        $order_taking_total1 =
                            ($order_taking_cash_sales ?? 0.0) +
                            ($invoices ?? 0.0) -
                            ($order_taking_cash_sales_returns ?? 0.0) -
                            ($order_taking_invoices_return ?? 0.0);
                    @endphp
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($order_taking_sum_INV) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($order_taking_total1) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($order_taking_petty_cash) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat(0) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($order_taking_eazzy) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($order_taking_vooma) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($order_taking_mpesa) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($totalOrderTakingNetCash) }}</td>
                </tr>
                    <tr class="heading">
                        <td style="font-size: 11px">#</td>
                        <td style="font-size: 11px">ROUTE</td>
                        <td style="text-align:right;font-size: 11px">CS</td>
                        <td style="text-align:right;font-size: 11px">CSR</td>
                        <td style="text-align:right;font-size: 11px">VCS</td>
                        <td style="text-align:right;font-size: 11px">VCR</td>
                        <td style="text-align:right;font-size: 11px">INV</td>
                        <td style="text-align:right;font-size: 11px">SALES</td>
                        <td style="text-align:right;font-size: 11px">EXP</td>
                        <td style="text-align:right;font-size: 11px">EC</td>
                        <td style="text-align:right;font-size: 11px">Eazzy</td>
                        <td style="text-align:right;font-size: 11px">Vooma</td>
                        <td style="text-align:right;font-size: 11px">Mpesa</td>
                        <td style="text-align:right;font-size: 11px">NETCASH</td>
                    </tr>

                <tr>
                    <td colspan="15" style="font-weight: bold; text-align: left; font-size: 12px;">Delivery</td>
                </tr>
                @foreach ($data->filter(function ($item) {
        return $item->vcs == 0;
    }) as $delivery_item)
                    @php
                        $total =
                            ($delivery_item->cs ?? 0.0) -
                            ($delivery_item->csr ?? 0.0) -
                            ($delivery_item->returns ?? 0.0);
                    @endphp

                    <tr>
                        <td style="font-size: 9px">{{ $loop->index + 1 }}</td>
                        <td style="margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ ucwords(strtolower($delivery_item->name)) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($delivery_item->cs) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($delivery_item->csr) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat(0) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($delivery_item->returns) }}</td>
                        @php
                            $total = 0;
                            $delivery_sum_INV += $delivery_item->inv_backend - $delivery_item->inv_backend_return;
                            $sum_INV += $delivery_item->inv_backend - $delivery_item->inv_backend_return;
                            $total =
                                ($delivery_item->cs ?? 0.0) -
                                ($delivery_item->csr ?? 0.0) -
                                ($delivery_item->returns ?? 0.0);
                        @endphp
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($delivery_item->inv_backend - $delivery_item->inv_backend_return) }}
                        </td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($total) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($delivery_item->petty_cash) }}</td>
                            <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat(0) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($delivery_item->Eazzy) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($delivery_item->Vooma) }}</td>
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($delivery_item->Mpesa) }}</td>
                        @php
                            // $delivery_netcash = 0;
                            $delivery_netcash =
                                ($delivery_item->Eazzy ?? 0.0) +
                                ($delivery_item->Mpesa ?? 0.0) +
                                ($delivery_item->Vooma ?? 0.0);
                            $cash_sales += $delivery_item->cs ?? 0.0;
                            $delivery_cash_sales += $delivery_item->cs ?? 0.0;
                            $cash_sales_returns += $delivery_item->csr ?? 0.0;
                            $delivery_cash_sales_returns += $delivery_item->csr ?? 0.0;
                            $invoices += $delivery_item->vcs ?? 0.0;
                            $delivery_invoices += $delivery_item->vcs ?? 0.0;
                            $invoices_return += $delivery_item->returns ?? 0.0;
                            $delivery_invoices_return += $delivery_item->returns ?? 0.0;
                            $petty_cash += $delivery_item->petty_cash ?? 0.0;
                            $delivery_petty_cash += $delivery_item->petty_cash ?? 0.0;
                            $customer_receipt += abs($delivery_item->customer_receipt ?? 0.0);
                            $delivery_customer_receipt += abs($delivery_item->customer_receipt ?? 0.0);
                            $eazzy += $delivery_item->Eazzy ?? 0.0;
                            $delivery_eazzy += $delivery_item->Eazzy ?? 0.0;
                            $vooma += $delivery_item->Vooma ?? 0.0;
                            $delivery_vooma += $delivery_item->Vooma ?? 0.0;
                            $mpesa += $delivery_item->Mpesa ?? 0.0;
                            $delivery_mpesa += $delivery_item->Mpesa ?? 0.0;
                            $totalDeliveryNetCash += $delivery_netcash ?? 0.0;
                            $totalNetCash += $delivery_netcash ?? 0.0;
                        @endphp
                        <td
                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                            {{ manageAmountFormat($delivery_netcash) }}</td>
                    </tr>
                @endforeach

                <tr style="border-top: 1px solid #000;border-bottom:1px solid #000;">
                    <td colspan="2" style="font-size: 11px;text-align:left;">Totals</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($delivery_cash_sales) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($delivery_cash_sales_returns) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($delivery_invoices) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($delivery_invoices_return) }}</td>
                    @php
                        $delivery_total1 = 0;
                        $delivery_total1 =
                            ($delivery_cash_sales ?? 0.0) +
                            ($delivery_invoices ?? 0.0) -
                            ($delivery_cash_sales_returns ?? 0.0) -
                            ($delivery_invoices_return ?? 0.0);
                    @endphp
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($delivery_sum_INV) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($delivery_total1) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($delivery_petty_cash) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat(0) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($delivery_eazzy) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($delivery_vooma) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($delivery_mpesa) }}</td>
                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($totalDeliveryNetCash) }}</td>
                </tr>

                <tr style="border-top: 2px solid #000;">
                    <td colspan="15"></td>
                </tr>
            
        @endif


     
                @if($posSalesExist)
                    <tr class="heading">
                        <td style="font-size: 11px">#</td>
                        <td style="font-size: 11px">CASHIER</td>
                        <td style="text-align:right;font-size: 11px">CS</td>
                        <td style="text-align:right;font-size: 11px">CSR</td>
                        <td style="text-align:right;font-size: 11px">VCS</td>
                        <td style="text-align:right;font-size: 11px">VCR</td>
                        <td style="text-align:right;font-size: 11px">INV</td>
                        <td style="text-align:right;font-size: 11px">SALES</td>
                        <td style="text-align:right;font-size: 11px">EXP</td>
                        <td style="text-align:right;font-size: 11px">EC</td>
                        <td style="text-align:right;font-size: 11px">Eazzy</td>
                        <td style="text-align:right;font-size: 11px">Vooma</td>
                        <td style="text-align:right;font-size: 11px">Mpesa</td>
                        <td style="text-align:right;font-size: 11px">NETCASH</td>
                    </tr>
                    <tr>
                        <td colspan="15" style="font-weight: bold; text-align: left; font-size: 12px;">Cash Sales</td>
                    </tr>
                    @php
                        $cash_sales_inner = $expectedCash = $cash_sales_returns_inner = $cash_sale_vooma = $cash_sale_eazzy = $cash_sale_mpesa = $cash_sale_net_cash = 0;
                    @endphp
                    @foreach ($posSales as $posSale)
                        @php
                            $expectedCashValue = ($posSale->cash_sales ?? 0) - ($posSale->cash_returns ?? 0) - ($posSale->Eazzy ?? 0) - ($posSale->Vooma ?? 0) - ($posSale->Mpesa ?? 0) ;
                        @endphp
                        <tr>
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
                                {{ manageAmountFormat(0) }}</td>
                            <td
                                style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                                {{ manageAmountFormat(0) }}</td>
                            <td
                                style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                                {{ manageAmountFormat(0) }}
                            </td>
                            <td
                                style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                                {{ manageAmountFormat($posSale->cash_sales - $posSale->cash_returns) }}</td>
                            <td
                                style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                                {{ manageAmountFormat(0) }}</td>
                            <td
                                style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                                {{ manageAmountFormat($expectedCashValue) }}</td>
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
                                {{ manageAmountFormat(($posSale->Eazzy ?? 0)+($posSale->Vooma ?? 0)+($posSale->Mpesa ?? 0) + ($expectedCashValue ?? 0)) }}</td>
                        </tr>
                           @php
                                $cash_sales = $cash_sales ?? 0.00;
                                $cash_sales_returns = $cash_sales_returns ?? 0.00;
                                $eazzy = $eazzy ?? 0.00;
                                $vooma = $vooma ?? 0.00;
                                $mpesa = $mpesa ?? 0.00;
                                $totalNetCash = $totalNetCash ?? 0.00;
                                
                                $cash_sales_inner += $posSale->cash_sales ?? 0.0;
                                $cash_sales_returns_inner += $posSale->cash_returns ?? 0.0;
                                $cash_sale_vooma += $posSale->Vooma ?? 0.0;
                                $cash_sale_mpesa += $posSale->Mpesa ?? 0.0;
                                $cash_sale_eazzy += $posSale->Eazzy ?? 0.0;
                                $cash_sale_net_cash += (($posSale->Eazzy ?? 0)+($posSale->Vooma ?? 0)+($posSale->Mpesa ?? 0) + ($expectedCashValue ?? 0));
                                $cash_sales += $posSale->cash_sales ?? 0.0;
                                $cash_sales_returns += $posSale->cash_returns ?? 0.0;
                                $expectedCash += ($posSale->cash_sales - $posSale->cash_returns - $posSale->Eazzy - $posSale->Vooma - $posSale->Mpesa );
                                $eazzy += $posSale->Eazzy ?? 0.0;
                                $vooma += $posSale->Vooma ?? 0.0;
                                $mpesa += $posSale->Mpesa ?? 0.0;
                                $totalNetCash += ($posSale->Eazzy ?? 0.0) + ($posSale->Vooma ?? 0.0) + ($posSale->Mpesa ?? 0.0) + ($expectedCashValue ?? 0);
                                
                            @endphp

                        
                    @endforeach
                    <tr style="border-top: 1px solid #000;border-bottom:1px solid #000;">
                        <td colspan="2" style="font-size: 11px;text-align:left;">Totals</td>
                        <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sales_inner) }}</td>
                        <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sales_returns_inner) }}</td>
                        <td style="font-size: 9px;text-align:right">{{ manageAmountFormat(0) }}</td>
                        <td style="font-size: 9px;text-align:right">{{ manageAmountFormat(0) }}</td>
                        <td style="font-size: 9px;text-align:right">{{ manageAmountFormat(0) }}</td>
                        <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sales - $cash_sales_returns) }}</td>
                        <td style="font-size: 9px;text-align:right">{{ manageAmountFormat(0) }}</td>
                        <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($expectedCash) }}</td>
                        <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sale_eazzy) }}</td>
                        <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sale_vooma) }}</td>
                        <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sale_mpesa) }}</td>
                        <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($cash_sale_net_cash) }}</td>
                    </tr>
    
                    <tr style="border-top: 2px solid #000;">
                        <td colspan="15"></td>
                    </tr>

                @endif
                <tr>
                    <th style="text-align:left;font-size: 12px" colspan="2">Grand Totals</th>
                    <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($cash_sales  ?? 0) }}</th>
                    <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($cash_sales_returns ?? 0) }}</th>
                    <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($invoices ?? 0) }}</th>
                    <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($invoices_return ?? 0) }}</th>
                    @php
                        $total1 = 0;
                        $total1 =
                            ($cash_sales ?? 0.0) +
                            ($invoices ?? 0.0) -
                            ($cash_sales_returns ?? 0.0) -
                            ($invoices_return ?? 0.0);
                    @endphp
                    <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($sum_INV ?? 0) }}</th>
                    <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($total1 ?? 0) }}</th>
                    <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($petty_cash ?? 0) }}</th>
                    <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($expectedCash ?? 0) }}</th>
                    <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($eazzy ?? 0) }}</th>
                    <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($vooma ?? 0) }}</th>
                    <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($mpesa ?? 0) }}</th>
                    @php
                        $netcash1 = 0;
                        $netcash1 =
                            ($cash_sales ?? 0.0) -
                            ($cash_sales_returns ?? 0.0) -
                            ($petty_cash ?? 0.0) +
                            ($customer_receipt ?? 0.0);
                        $sum_cashSales = $cash_sales ?? 0.0;
                        $sum_CRS = $cash_sales_returns ?? 0.0;
                        $sum_VCS = $invoices ?? 0.0;
                        $sum_VCR = $invoices_return ?? 0.0;
                        $sum_CRN = abs($customer_receipt ?? 0);
                    @endphp
                    <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalNetCash ?? 0) }}</th>
                </tr>
                <tr>
                    <td colspan="15" style="border-bottom: 2px solid #000;"></td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div class="horizontal_dotted_line" style="font-size: 12px">
                            Done By:
                        </div>
                    </td>
                    <td colspan="1"></td>
                    <td colspan="2">
                        <div class="horizontal_dotted_line" style="font-size: 12px"> Checked By</div>
                    </td>
                    <td colspan="1"></td>
                    <td colspan="2">
                        <div class="horizontal_dotted_line" style="font-size: 12px"> Approved:</div>
                    </td>
                    <td colspan="5"></td>
                </tr>
            </tbody>
        </table>

        {{-- <br> --}}
        @if( (count($salesmanPettyCashTransactions) > 0) || (count($deliveryPettyCashTransactions) > 0))
            <table style="text-align: left;">
                <tbody>
                    <tr class="top">
                        <th>
                            <h3>EXPENSES | Date: {{ date('d/m/Y', strtotime(request()->date)) }}</h3>
                        </th>
                    </tr>
                </tbody>
            </table>
        @endif

        {{-- Travel order taking table start --}}
        @if (count($salesmanPettyCashTransactions) > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 5%; text-align: left;">#</th>
                        <th style="text-align: left;">Reference</th>
                        <th style="text-align: left;">Recipient</th>
                        @if (count($salesmanPettyCashTransactions) > 0)
                            <th style="text-align: right; width: 10%">Shift type</th>
                        @endif
                        <th style="text-align: right; width: 10%">Tonnage</th>
                        <th style="text-align: right; width: 15%">Sales Amount</th>
                        <th style="text-align: right; width: 10%">Amount</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <th colspan="4" style="text-align: left;" scope="row">TRAVEL - ORDER TAKING</th>
                    </tr>
                    @foreach ($salesmanPettyCashTransactions as $record)
                        <tr style="border-bottom: 2px solid #ccc">
                            <th style="width: 5%; text-align: left; font-size:9px" scope="row">
                                {{ $loop->index + 1 }}</th>
                            <td style="text-align: left; font-size:9px"> {{ $record->route_name }} </td>
                            <td style="text-align: left; font-size:9px">
                                {{ $record->recipient . ' - ' . $record->phone_number }} </td>
                            <td style="text-align: right; font-size:9px"> {{ ucfirst($record->shift_type) }} </td>
                            <td style="text-align: right; font-size:9px"> {{ $record->tonnage ?? '' }} </td>
                            <td style="text-align: right; font-size:9px">
                                {{ $record->sales_amount ? manageAmountFormat($record->sales_amount) : '' }} </td>
                            <td style="text-align: right; font-size:9px">
                                {{ $record->amount ? manageAmountFormat($record->amount) : '' }} </td>
                        </tr>
                    @endforeach
                    <tr>
                        <th colspan="6" style="text-align: center;">Total</th>
                        <th style="text-align: right;">
                            {{ manageAmountFormat($salesmanPettyCashTransactions->sum('amount')) }}</th>
                    </tr>

                </tbody>
            </table>
        @endif

        @if (count($deliveryPettyCashTransactions) > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 5%; text-align: left;">#</th>
                        <th style="text-align: left;">Reference</th>
                        <th style="text-align: left;" colspan="2">Recipient</th>
                        <th style="text-align: right; width: 10%">Tonnage</th>
                        <th style="text-align: right; width: 15%">Sales Amount</th>
                        <th style="text-align: right; width: 10%">Amount</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <th colspan="4" style="text-align: left;" scope="row">TRAVEL - DELIVERY</th>
                    </tr>
                    @foreach ($deliveryPettyCashTransactions as $record)
                        <tr>
                            <th style="width: 5%; text-align: left; font-size:9px" scope="row">
                                {{ $loop->index + 1 }}</th>
                            <td style="text-align: left; font-size:9px"> {{ $record->route_name }} </td>
                            <td style="text-align: left; font-size:9px" colspan="2">
                                {{ $record->recipient . ' - ' . $record->phone_number }} </td>
                            <td style="text-align: right; font-size:9px"> {{ $record->tonnage ?? '' }} </td>
                            <td style="text-align: right; font-size:9px">
                                {{ $record->sales_amount ? manageAmountFormat($record->sales_amount) : '' }} </td>
                            <td style="text-align: right; font-size:9px">
                                {{ $record->amount ? manageAmountFormat($record->amount) : '' }} </td>
                        </tr>
                    @endforeach
                    <tr>
                        <th colspan="6" style="text-align: center;">Total</th>
                        <th style="text-align: right;">
                            {{ manageAmountFormat($deliveryPettyCashTransactions->sum('amount')) }}</th>
                    </tr>

                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="6" style="text-align: center;">GRAND TOTAL</th>
                        <th style="text-align: right;">{{ manageAmountFormat($pettyCashTransactions->sum('amount')) }}
                        </th>
                    </tr>
                </tfoot>
            </table>
        @endif

        {{-- Travel delivery end --}}

        {{-- <br> --}}
        @php
            $sum_TOTAL = ($sum_cashSales ?? 0) - ($sum_CRS ?? 0) + ($sum_VCS ?? 0) - ($sum_VCR ?? 0) + ($sum_INV ?? 0) - ($sum_CRN ?? 0);
        @endphp
        <table style="text-align: left;">
            <tbody>
                <tr class="top">
                    <th colspan="13">
                        <h3>TOTAL SALES SUMMARY</h3>
                    </th>
                </tr>

                <tr class="item">
                    <th style="text-align:center" style="font-size: 11px"><span
                            style="border-bottom:1px solid #000;">Cash Sales</span></th>
                    <th>-</th>
                    <th style="text-align:center" style="font-size: 11px"><span
                            style="border-bottom:1px solid #000;">CRS</span></th>
                    <th>+</th>
                    <th style="text-align:center" style="font-size: 11px"><span
                            style="border-bottom:1px solid #000;">VCS</span></th>
                    <th>-</th>
                    <th style="text-align:center" style="font-size: 11px"><span
                            style="border-bottom:1px solid #000;">VCR</span></th>
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
                        {{ manageAmountFormat($sum_cashSales ?? 0) }}</th>
                    <th></th>
                    <th style="text-align:center;font-size:9px" colspan="1">{{ manageAmountFormat($sum_CRS ?? 0) }}
                    </th>
                    <th></th>
                    <th style="text-align:center;font-size:9px" colspan="1">{{ manageAmountFormat($sum_VCS ?? 0) }}
                    </th>
                    <th></th>
                    <th style="text-align:center;font-size:9px" colspan="1">{{ manageAmountFormat($sum_VCR ?? 0) }}
                    </th>
                    <th></th>
                    <th style="text-align:center;font-size:9px" colspan="1">{{ manageAmountFormat($sum_INV ?? 0) }}
                    </th>
                    <th></th>
                    <th style="text-align:center;font-size:9px" colspan="1">{{ manageAmountFormat($sum_CRN ?? 0) }}
                    </th>
                    <th></th>
                    <th style="text-align:center;font-size:9px" colspan="1">{{ manageAmountFormat($sum_TOTAL ?? 0) }}
                    </th>
                </tr>

            </tbody>
        </table>


        {{-- <br> --}}
        {{-- Debtors Section --}}
        {{-- <table style="text-align: left;">
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
                <tr>
                    <th style="font-size: 11px">Invoice</th>
                    <th style="font-size: 11px">Account #</th>
                    <th style="font-size: 11px">Company</th>
                    <th style="font-size: 11px">Total</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table> --}}

        {{-- VAN CASH SALES  --}}
        @php
            $countVCS =$data->filter(function($item) {
                return $item->vcs != 0;
            })->count();
        @endphp
        @if ($countVCS > 0)
            <table style="text-align: left;">
                <tbody>
                    <tr class="top">
                        <th>
                            <h3>VAN CASH SALES</h3>
                        </th>
                    </tr>

                </tbody>
            </table>
            <table>
                <thead>
                    <tr class="heading" style="border-bottom: 1px solid #858383;">
                        <th style="font-size: 11px">#</th>
                        <th style="text-align: left;font-size:11px">Account #</th>
                        <th style="text-align: left;font-size:11px">Name</th>
                        <th style="text-align: right;font-size:11px">Transactions Count</th>
                        <th style="text-align: right;font-size:11px">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalVcs = 0;
                    @endphp
                    @foreach ($data as $item)
                        {{-- Eliminate rows without any transaction --}}
                        @if ($item->vcs != 0)
                            <tr class="item">
                                <td style="font-size: 9px">{{ $loop->index + 1 }}</td>
                                <td style="font-size: 9px">{{ $item->customer_code }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($item->name)) }}</td>
                                <td style="text-align:right;font-size: 9px">{{ $item->vcs_count }}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($item->vcs) }}</td>
                                @php
                                    $totalVcs += $item->vcs;
                                @endphp
                            </tr>
                        @endif
                    @endforeach
                    <tr style="border-bottom: 2px solid black; border-top: 2px solid black;">
                        <th style="font-size: 11px" colspan="4">Total</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalVcs) }}</th>
                    </tr>


                </tbody>
            </table>
            
        @endif
        

        {{-- VAN SALES RECEIPT  --}}
        @php
            $countVSR =$data->filter(function($item) {
                return (($item->Eazzy ?? 0.0) + ($item->Mpesa ?? 0.0) + ($item->Vooma ?? 0.0)) != 0;
            })->count();
        @endphp
        @if ($countVSR > 0)
            <table style="text-align: left;">
                <tbody>
                    <tr class="top">
                        <th>
                            <h3>VAN SALES RECEIPT</h3>
                        </th>
                    </tr>

                </tbody>
            </table>
            <table>
                <thead>
                    <tr class="heading" style="border-bottom: 1px solid #858383;">
                        <th style="font-size: 11px">#</th>
                        <th style="text-align: left;font-size: 9px">Account #</th>
                        <th style="text-align: left;font-size: 9px">Name</th>
                        <th style="text-align: right;font-size: 9px">Transactions Count</th>
                        <th style="text-align: right;font-size: 9px">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalNetCash = 0;
                    @endphp
                    @foreach ($data as $item)
                        {{-- Elimminate rows without any transaction --}}
                        @php
                            $netcash = 0;
                            $netcash = ($item->Eazzy ?? 0.0) + ($item->Mpesa ?? 0.0) + ($item->Vooma ?? 0.0);
                        @endphp
                        @if ($netcash != 0)
                            <tr class="item">
                                <td style="font-size: 9px">{{ $loop->index + 1 }}</td>
                                <td style="font-size: 9px">{{ $item->customer_code }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($item->name)) }}</td>
                                <td style="text-align:right;font-size:9px">
                                    {{ $item->Eazzy_count + $item->Vooma_count + $item->Mpesa_count }}</td>
                                <td style="text-align:right;font-size:9px">{{ manageAmountFormat($netcash) }}</td>
                                @php
                                    $totalNetCash += $netcash;
                                @endphp
                            </tr>
                        @endif
                    @endforeach
                    <tr style="border-bottom: 2px solid black; border-top: 2px solid black;">
                        <th style="font-size: 11px" colspan="4">Total</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalNetCash) }}</th>
                    </tr>


                </tbody>
            </table>
            
        @endif
        
        {{-- VAN SALES RETURNS  --}}
        @php
            $countVSRs =$data->filter(function($item) {
                return $item->returns != 0;
            })->count();
        @endphp
        @if ($countVSRs)
            <table style="text-align: left;">
                <tbody>
                    <tr class="top">
                        <th>
                            <h3>VAN SALES RETURN</h3>
                        </th>
                    </tr>

                </tbody>
            </table>
            <table>
                <thead>
                    <tr class="heading" style="border-bottom: 1px solid #858383;">
                        <th style="font-size: 11px">#</th>
                        <th style="text-align: left;font-size: 11px">Account #</th>
                        <th style="text-align: left;font-size: 11px">Name</th>
                        <th style="text-align: right;font-size: 11px">Transactions Count</th>
                        <th style="text-align: right;font-size: 11px">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalReturns = 0;
                        $index = 1;
                    @endphp
                    @foreach ($data as $item)
                        {{-- Elimminate rows without any transaction --}}
                        @if ($item->returns != 0)
                            <tr class="item">
                                <td style="font-size: 9px">{{ $index ++ }}</td>
                                <td style="font-size: 9px">{{ $item->customer_code }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($item->name)) }}</td>
                                <td style="text-align:right;font-size: 9px">{{ $item->returns_count }}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($item->returns) }}</td>
                                @php
                                    $totalReturns += $item->returns;
                                @endphp
                            </tr>
                        @endif
                    @endforeach
                    <tr style="border-bottom: 2px solid black; border-top: 2px solid black;">
                        <th style="font-size: 11px" colspan="4">Total</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalReturns) }}</th>
                    </tr>


                </tbody>
            </table>   
        @endif
      
        {{-- CHEQUES RECEIVED  --}}
        {{-- <table style="text-align: left;">
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
                    <th style="text-align: left;font-size: 11px"></th>
                    <th style="text-align: left;font-size: 11px">Cheque</th>
                    <th style="text-align: left;font-size: 11px">Date</th>
                    <th style="text-align: left;font-size: 11px">Bank</th>
                    <th style="text-align: left;font-size: 11px">Branch</th>
                    <th style="text-align: left;font-size: 11px">Account</th>
                    <th style="text-align: left;font-size: 11px">Customer</th>
                    <th style="text-align: left;font-size: 11px">Amount</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table> --}}
        {{-- EAZZYPAY  --}}
        @php
            $countEazzypay =$data->filter(function($item) {
                return $item->Eazzy != 0;
            })->count();
        @endphp
        @if ($countEazzypay > 0)
            <table style="text-align: left;">
                <tbody>
                    <tr class="top">
                        <th>
                            <h3>EAZZYPAY</h3>
                        </th>
                    </tr>

                </tbody>
            </table>
            <table>
                <thead>
                    <tr class="heading" style="border-bottom: 1px solid #858383;">
                        <th style="font-size: 11px">#</th>
                        <th style="text-align: left;font-size: 11px">Account #</th>
                        <th style="text-align: left;font-size: 11px">Name</th>
                        <th style="text-align: right;font-size: 11px">Transactions Count</th>
                        <th style="text-align: right;font-size: 11px">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalEazzy = 0;
                    @endphp
                    @foreach ($data as $item)
                        {{-- Elimminate rows without any transaction --}}
                        @if ($item->Eazzy != 0)
                            <tr class="item">
                                <td style="font-size: 9px">{{ $loop->index + 1 }}</td>
                                <td style="font-size: 9px">{{ $item->customer_code }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($item->name)) }}</td>
                                <td style="text-align:right;font-size: 9px">{{ $item->Eazzy_count }}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($item->Eazzy) }}</td>
                                @php
                                    $totalEazzy += $item->Eazzy;
                                @endphp
                            </tr>
                        @endif
                    @endforeach
                    <tr style="border-bottom: 2px solid black; border-top: 2px solid black;">
                        <th style="font-size: 11px" colspan="4">Total</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalEazzy) }}</th>
                    </tr>


                </tbody>
            </table>
            
        @endif
       
        {{-- VOOMA  --}}
        @php
            $countVooma =$data->filter(function($item) {
                return $item->Vooma != 0;
            })->count();
        @endphp
        @if ($countVooma > 0)
                <table style="text-align: left;">
                <tbody>
                    <tr class="top">
                        <th>
                            <h3>VOOMA</h3>
                        </th>
                    </tr>

                </tbody>
                </table>
                <table>
                    <thead>
                        <tr class="heading" style="border-bottom: 1px solid #858383;">
                            <th style="font-size: 11px">#</th>
                            <th style="text-align: left;font-size: 11px">Account #</th>
                            <th style="text-align: left;font-size: 11px">Name</th>
                            <th style="text-align: right;font-size: 11px">Transactions Count</th>
                            <th style="text-align: right;font-size: 11px">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalVooma = 0;
                        @endphp
                        @foreach ($data as $item)
                            {{-- Elimminate rows without any transaction --}}
                            @if ($item->Vooma != 0)
                                <tr class="item">
                                    <td style="font-size: 9px">{{ $loop->index + 1 }}</td>
                                    <td style="font-size: 9px">{{ $item->customer_code }}</td>
                                    <td style="font-size: 9px">{{ ucwords(strtolower($item->name)) }}</td>
                                    <td style="text-align:right;font-size: 9px">{{ $item->Vooma_count }}</td>
                                    <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($item->Vooma) }}</td>
                                    @php
                                        $totalVooma += $item->Vooma;
                                    @endphp
                                </tr>
                            @endif
                        @endforeach
                        <tr style="border-bottom: 2px solid black; border-top: 2px solid black;">
                            <th style="font-size: 11px" colspan="4">Total</th>
                            <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalVooma) }}</th>
                        </tr>


                    </tbody>
                </table>
            
        @endif
        {{-- MPESA  --}}
        @php
            $countMpesa =$data->filter(function($item) {
                return $item->Mpesa != 0;
            })->count();
        @endphp
        @if ($countMpesa > 0)
            <table style="text-align: left;">
                <tbody>
                    <tr class="top">
                        <th>
                            <h3>MPESA</h3>
                        </th>
                    </tr>

                </tbody>
            </table>
            <table>
                <thead>
                    <tr class="heading" style="border-bottom: 1px solid #858383;">
                        <th style="font-size: 11px">#</th>
                        <th style="text-align: left;font-size: 11px">Account #</th>
                        <th style="text-align: left;font-size: 11px">Name</th>
                        <th style="text-align: right;font-size: 11px">Transactions Count</th>
                        <th style="text-align: right;font-size: 11px">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalMpesa = 0;
                    @endphp
                    @foreach ($data as $item)
                        {{-- Elimminate rows without any transaction --}}
                        @if ($item->Mpesa != 0)
                            <tr class="item">
                                <td style="font-size: 9px">{{ $loop->index + 1 }}</td>
                                <td style="font-size: 9px">{{ $item->customer_code }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($item->name)) }}</td>
                                <td style="text-align:right;font-size: 9px">{{ $item->Mpesa_count }}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($item->Mpesa) }}</td>
                                @php
                                    $totalMpesa += $item->Mpesa;
                                @endphp
                            </tr>
                        @endif
                    @endforeach
                    <tr style="border-bottom: 2px solid black; border-top: 2px solid black;">
                        <th style="font-size: 11px" colspan="4">Total</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalMpesa) }}</th>
                    </tr>


                </tbody>
            </table>
            
        @endif
        
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
                    {{-- <th style="text-align: right;font-size: 11px">RETURNS TAX</th>
                    <th style="text-align: right;font-size: 11px">RETURNS TAX BE</th> --}}
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
                        {{-- <td style="text-align:right;font-size: 9px">
                                {{ manageAmountFormat(((16 * $data->returns_16) / 116)) }}</td>
                        <td style="text-align:right;font-size: 9px">
                                    {{ manageAmountFormat($data->returns_vat_16) }}</td> --}}
                        
                        
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
        {{-- STOCK TAKE SALES SUMMARY --}}
        @if (count($stockSaleSummary) > 0)
            <table style="text-align: left;">
                <tbody>
                    <tr class="top">
                        <th>
                            <h3>STOCK TAKE SALES SUMMARY</h3>
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
                    @foreach ($stockSaleSummary as $data)
                        <tr class="item">
                            <td style="font-size: 9px">{{ $loop->index + 1 }}</td>
                            <td style="font-size: 9px">{{ \Carbon\Carbon::parse($data->sales_date)->toDateString() }}
                            </td>
                            <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($data->stock_sale_16 - ($data->stock_return_16 ?? 0) -  ($data->stock_sale_vat_16 ?? 0) + ($data->stock_return_vat_16 ?? 0) ) }}
                            </td>
                            <td style="text-align:right;font-size: 9px">
                                {{ manageAmountFormat(($data->stock_sale_vat_16 ?? 0) - ($data->stock_return_vat_16 ?? 0)) }}</td>
                            <td style="text-align:right;font-size: 9px">{{ manageAmountFormat(($data->sales_zero_rated ?? 0) - ($data->returns_zero_rated ?? 0)) }}
                            </td>
                            
                            <td style="text-align:right;font-size: 9px">
                                {{ manageAmountFormat(($data->sales_exempt ?? 0) - ($data->returns_exempt ?? 0)) }}
                            </td>
                        
                            <td style="text-align:right;font-size: 9px">{{ manageAmountFormat(($data->total_sales ?? 0) - ($data->total_returns ?? 0)) }}</td>


                        </tr>
                        <?php
                        $total_sales_all += ($data->total_sales ?? 0) - ($data->total_returns ?? 0);
                        $total_tax += ($data->stock_sale_vat_16 ?? 0) - ($data->stock_return_vat_16 ?? 0);
                        $total_vat_16 += ($data->stock_sale_16 - ($data->stock_return_16 ?? 0) -  ($data->stock_sale_vat_16 ?? 0) + ($data->stock_return_vat_16 ?? 0) );
                        $total_vat_0 += (($data->sales_zero_rated ?? 0) - ($data->returns_zero_rated ?? 0));
                        $total_vat_exempt += (($data->sales_exempt ?? 0) - ($data->returns_exempt ?? 0));
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
            
        @endif
       


        {{-- SALES VS STOCKS  --}}
        {{-- <table style="text-align: left;">
        <tbody>
            <tr class="top">
                <th>
                    <h2>SALES VS STOCKS </h2>
                </th>
            </tr>

        </tbody>
    </table>
    <table>
       <thead>
        <tr class="heading">
            <th style="text-align: right">SALES LEDGER</th>
            <th style="text-align: right">STOCKS LEDGER</th>
            <th style="text-align: right">VARIANCE</th>
            <th style="text-align: right">invoices</th>
            <th  style="text-align: right">returns</th>
        </tr>
       </thead>
       <tbody>
        <tr class="item"  style="border-bottom: 2px solid black; border-top: 2px solid black;">
            <th style="text-align:right">{{ manageAmountFormat($total1) }}</th>
            <th style="text-align:right">{{ manageAmountFormat($salesLedgerInvoices - $salesLedgerReturns) }}</th>
            <th style="text-align:right">{{ manageAmountFormat($total1 -  ($salesLedgerInvoices - $salesLedgerReturns)) }}</th>
            <td style="text-align:right">{{ manageAmountFormat($salesLedgerInvoices )}}</td>
            <td style="text-align:right">{{ manageAmountFormat($salesLedgerReturns) }}</td>
        </tr>

    </tbody>
    </table>
    <br> --}}







        {{-- <table style="text-align: left;">
        <tbody>
            <tr class="top">
                <th>
                    <h2>DEBTORS TRANSACTIONS LIST | Date: {{ date('d/m/Y', strtotime(request()->date)) }}</h2>
                </th>
            </tr>

        </tbody>
    </table>
    <table>
        <thead>
            <tr class="heading">
                <td> Name</td>
                <td style="text-align: right;">Amount</td>
            </tr>
        </thead>
        <tbody>
            <tr class="item">
                <td colspan="3" style="text-align: left;"> CRN | CREDIT NOTE</td>
            </tr>
            @php
                $totalinvoiceReturn = 0;
            @endphp
            @foreach ($invoiceReturn as $invoiceret)
                <tr class="item">
                    <td> {{ @$invoiceret->customer }} </td>
                    <td style="text-align: right;"> {{ manageAmountFormat($invoiceret->max_total) }} </td>

                </tr>
                @php
                    $totalinvoiceReturn += $invoiceret->max_total;
                @endphp
            @endforeach
            <tr style="    border-top: 2px dashed #858383;">
                <td colspan="2"></td>
            </tr>
            <tr class="">
                <th colspan="1" style="text-align: left;"> Sub total</th>
                <th style="text-align: right;">{{ manageAmountFormat($totalinvoiceReturn) }}</th>

            </tr>


            <tr class="item">
                <td colspan="2" style="text-align: left;"> INV | INVOICE</td>
            </tr>
            @php
                $totalinvoice = 0;
            @endphp
            @foreach ($realinvoices as $invoice)
                <tr class="item">
                    <td> {{ @$invoice->customer }} </td>
                    <td style="text-align: right;"> {{ manageAmountFormat($invoice->max_total) }} </td>
                </tr>
                @php
                    $totalinvoice += $invoice->max_total;
                @endphp
            @endforeach
            <tr style="    border-top: 2px dashed #858383;">
                <td colspan="2"></td>
            </tr>
            <tr class="">
                <th colspan="1" style="text-align: left;"> Sub total</th>
                <th style="text-align: right;">{{ manageAmountFormat($totalinvoice) }}</th>

            </tr>

            <tr style="    border-top: 2px dashed #858383;">
                <td colspan="2"></td>
            </tr>

            <tr class="">
                <th colspan="1" style="text-align: left;"> Grand total</th>
                <th style="text-align: right;">{{ manageAmountFormat($totalinvoice + $totalinvoiceReturn) }}</th>

            </tr>
        </tbody>
    </table>
    <br>
    <table style="text-align: left;">
        <tbody>
            <tr class="top">
                <th>
                    <h2>CASH RECEIPT LIST | Date: {{ date('d/m/Y', strtotime(request()->date)) }}</h2>
                </th>
            </tr>

        </tbody>
    </table>
    <table>
        <thead>
            <tr class="heading">
                <td> User</td>
                <td> A/c No</td>
                <td> A/c Name</td>
                <td> Desc</td>
                <td> Col/Rec By</td>
                <td style="text-align: right;">Amount</td>
            </tr>
        </thead>
        <tbody>
            <tr class="item">
                <td colspan="6" style="text-align: left;"> CAR | CASH RECEIPT</td>
            </tr>

            @php
                $totalCASHRECEIPT = 0;
            @endphp
            @foreach ($cashreceipt as $cash)
                <tr class="item">
                    <td> {{ @$cash->paid_user->name }} </td>
                    <td> {{ @$cash->customerDetail->customer_code }} </td>
                    <td> {{ @$cash->customerDetail->customer_name }} </td>
                    <td> {{ $cash->reference }} </td>
                    <td> {{ $cash->paid_by }} </td>
                    <td style="text-align: right;"> {{ manageAmountFormat(abs($cash->amount)) }} </td>

                </tr>
                @php
                    $totalCASHRECEIPT += $cash->amount;
                @endphp
            @endforeach
            <tr style="    border-top: 2px dashed #858383;">
                <td colspan="6"></td>
            </tr>
            <tr class="">
                <th colspan="5" style="text-align: left;"> Sub total</th>
                <th style="text-align: right;">{{ manageAmountFormat(abs($totalCASHRECEIPT)) }}</th>

            </tr>
            <tr>
                <td colspan="6" style="    border-bottom: 2px dashed #858383;"></td>
            </tr>
        </tbody>
    </table> --}}
    </div>
</body>

</html>
