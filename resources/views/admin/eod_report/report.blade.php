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
                            {{ date('d-M-Y', strtotime(request()->date)) }}</h5>
                    </th>
                </tr>
                <tr>
                    <th>
                    </th>
                    <th>
                        <h5 style="text-align: right; margin:0">REPORT TIME:
                          {{ date('d-M-Y H:i A') }}</h5>
                    </th>
                </tr>
            </tbody>
        </table>
        {{-- ROUTE --}}
        @if ($type == 'route')
            {{-- Section 1  --}}
            {{-- Sales --}}
            <table>
            
                <tbody>
                        @php
                            $countRoutesData = $data->filter(function($item) {
                                return ($item->vcs != 0) || ($item->invoiceSales != 0) ||((($item->Eazzy ?? 0.0) + ($item->Mpesa ?? 0.0) + ($item->Vooma ?? 0.0)) != 0);
                            })->count();
                        @endphp
                        @if ($countRoutesData > 0)
                            <tr class="heading">
                                <td style="font-size: 11px">#</td>
                                <td style="font-size: 11px">ROUTE</td>
                                <td style="text-align:right;font-size: 11px">ROUTE SALES</td>
                                <td style="text-align:right;font-size: 11px">INV SALES</td>
                                <td style="text-align:right;font-size: 11px">TOTAL SALES</td>


                            </tr>
                                <tr>
                                    <td colspan="6" style="font-weight: bold; text-align: left; font-size: 12px;">ORDER TAKING</td>
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

                                    $cash_sales = $cash_sales_returns = $invoices = $invoiceSales = $allTotalSales = $invoices_return = $petty_cash = $customer_receipt = $eazzy = $vooma = $mpesa = $totalNetCash = 0;
                                    $dataSales = $data->filter(function ($item) {
                                        return $item->cs != 0 ||
                                            $item->csr != 0 ||
                                            $item->vcs != 0 ||
                                            $item->returns != 0 ||
                                            $item->vcr != 0 ||
                                            $item->inv_backend != 0 ||
                                            $item->petty_cash != 0 ||
                                            $item->Eazzy != 0 ||
                                            $item->Vooma != 0 ||
                                            $item->Mpesa != 0 ||
                                            $item->invoiceSales != 0;
                                    });
                                @endphp
                                @foreach ($dataSales->sortByDesc('vcs')->filter(function ($item) {
                                    return $item->vcs != 0 ||    $item->invoiceSales != 0;
                                }) as $order_taking_item)
                                    @php
                                        $total =
                                            ($order_taking_item->cs ?? 0.0) +
                                            ($order_taking_item->vcs ?? 0.0) -
                                            ($order_taking_item->csr ?? 0.0) -
                                            ($order_taking_item->returns ?? 0.0);
                                    @endphp

                                    <tr style="border-bottom: 1px solid #ccc">
                                        <td style="font-size: 9px">{{ $loop->index + 1 }}</td>
                                        <td style="margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                                            {{ ucwords(strtolower($order_taking_item->name)) }}</td>
                                        <td
                                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                                            {{ manageAmountFormat($order_taking_item->vcs) }}</td>
                                        <td
                                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                                            {{ manageAmountFormat($order_taking_item->invoiceSales) }}</td>
                                        <td
                                            style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;font-size: 9px">
                                            {{ manageAmountFormat($order_taking_item->vcs + $order_taking_item->invoiceSales) }}</td>
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
                                            $invoiceSales += $order_taking_item->invoiceSales ?? 0.0;
                                            $allTotalSales += ($order_taking_item->vcs ?? 0.0) + ($order_taking_item->invoiceSales ?? 0.0);

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
                                    </tr>
                                @endforeach


                                <tr style="border-top: 1px solid #000;border-bottom:1px solid #000;">
                                    <td colspan="2" style="font-size: 11px;text-align:left;">Totals</td>
                                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($invoices) }}</td>
                                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($invoiceSales) }}</td>
                                    <td style="font-size: 9px;text-align:right">{{ manageAmountFormat($allTotalSales) }}</td>


                                    @php
                                        $order_taking_total1 = 0;
                                        $order_taking_total1 =
                                            ($order_taking_cash_sales ?? 0.0) +
                                            ($invoices ?? 0.0) -
                                            ($order_taking_cash_sales_returns ?? 0.0) -
                                            ($order_taking_invoices_return ?? 0.0);
                                    @endphp
                                </tr>
                                @foreach ($dataSales->filter(function ($item) {
                                        return $item->vcs == 0;
                                    }) as $delivery_item)
                                    @php
                                        $total =
                                            ($delivery_item->cs ?? 0.0) -
                                            ($delivery_item->csr ?? 0.0) -
                                            ($delivery_item->returns ?? 0.0);
                                    @endphp

                                    <tr>
                                        @php
                                            $total = 0;
                                            $delivery_sum_INV += $delivery_item->inv_backend - $delivery_item->inv_backend_return;
                                            $sum_INV += $delivery_item->inv_backend - $delivery_item->inv_backend_return;
                                            $total =
                                                ($delivery_item->cs ?? 0.0) -
                                                ($delivery_item->csr ?? 0.0) -
                                                ($delivery_item->returns ?? 0.0);
                                        @endphp
                                        @php
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
                                    </tr>
                                @endforeach

                                <tr style="border-top: 1px solid #000;border-bottom:1px solid #000;">
                                    @php
                                        $delivery_total1 = 0;
                                        $delivery_total1 =
                                            ($delivery_cash_sales ?? 0.0) +
                                            ($delivery_invoices ?? 0.0) -
                                            ($delivery_cash_sales_returns ?? 0.0) -
                                            ($delivery_invoices_return ?? 0.0);
                                    @endphp
                                </tr>

                                <tr style="border-top: 2px solid #000;">
                                    <td colspan="6"></td>
                                </tr>
                            
                        @endif
                   
                </tbody>
            </table>

            {{--Expenses--}}
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
                            <tr style="border-bottom: 1px solid #ccc">
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
                        <tr style="border-bottom: 1px solid #ccc">
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
         {{-- EXPENSES  --}}
         <table style="text-align: left;">
            <tbody>
                <tr class="top">
                    <th>
                        <h3>OTHER EXPENSES</h3>
                    </th>
                </tr>

            </tbody>
        </table>
        @php
            $otherExpensesGrandTotal = 0;
        @endphp

        @foreach ($pettyCashRequestTypes as $pettyCashRequestType)
            <table>
                <tbody>
                    <tr >
                        <th colspan="15" style="font-weight: bold; text-align: left; font-size: 12px;">
                            <h4 >{{$pettyCashRequestType->name}}</h4>
                        </th>
                    </tr>
                </tbody>
            </table>
            @php
                $headers = [];
                $otherExpensesData = null;
                switch ($pettyCashRequestType->slug){
                    case 'parking-fees' :
                        $headers = ['Route', 'Payee', 'Payment Reason', 'Vehicle', 'Driver', 'Amount'];
                        $otherExpensesData = [];
                        $total = 0;
                        foreach($pettyCashRequestTypesData as $item){
                            if($item->pettyCashRequest?->type == 'parking-fees'){
                                $otherExpensesData[] = [
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
                        $otherExpensesData [] = [
                            '',
                            'Total',
                            '',
                            '',
                            '',
                            manageAmountFormat($total)

                        ];

                        break;
                    case 'driver-grn' :
                        $headers = ['Document No', 'Payee', 'Payment Reason', 'Source','Amount'];
                        $otherExpensesData = [];
                        $total = 0;

                        foreach($pettyCashRequestTypesData as $item){
                            if($item->pettyCashRequest?->type == 'driver-grn'){
                                $otherExpensesData[] = [
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
                        $otherExpensesData [] = [
                            '',
                            'Total',
                            '',
                            '',
                            manageAmountFormat($total)

                        ];
                        break;
                    case 'staff-welfare' :
                        $headers = ['Payee', 'Payment Reason', 'Amount'];
                        $otherExpensesData = [];
                        $total = 0;

                        foreach($pettyCashRequestTypesData as $item){
                            if($item->pettyCashRequest?->type == 'staff-welfare'){
                                $otherExpensesData[] = [
                                    $item->payee_name,
                                    $item->payment_reason,
                                    manageAmountFormat($item->amount)
                                ];
                                $total += $item->amount;
                                $otherExpensesGrandTotal += $item->amount;
                            }
                        }
                        $otherExpensesData [] = [
                            '',
                            'Total',
                            manageAmountFormat($total)

                        ];
                        break;
                    case 'repairs-maintenance-buildings':
                        $headers = ['Payee', 'Payment Reason', 'Amount'];
                        $otherExpensesData = [];
                        $total = 0;

                        foreach($pettyCashRequestTypesData as $item){
                            if($item->pettyCashRequest?->type == 'repairs-maintenance-buildings'){
                                $otherExpensesData[] = [
                                    $item->payee_name,
                                    $item->payment_reason,
                                    manageAmountFormat($item->amount)
                                ];
                                $total += $item->amount;
                                $otherExpensesGrandTotal += $item->amount;
                            }
                        }
                        $otherExpensesData [] = [
                            '',
                            'Total',
                            
                            manageAmountFormat($total)

                        ];
                        break;
                    case 'repairs-maintenance-motor-vehicle':
                        $headers = ['Payee', 'Payment Reason', 'Amount'];
                        $otherExpensesData = [];
                        $total = 0;

                        foreach($pettyCashRequestTypesData as $item){
                            if($item->pettyCashRequest?->type == 'repairs-maintenance-motor-vehicle'){
                                $otherExpensesData[] = [
                                    $item->payee_name,
                                    $item->payment_reason,
                                    manageAmountFormat($item->amount)
                                ];
                                $total += $item->amount;
                                $otherExpensesGrandTotal += $item->amount;
                            }
                        }
                        $otherExpensesData [] = [
                            '',
                            'Total',
                            manageAmountFormat($total)

                        ];
                        break;
                    default:
                        $headers = [];
                        $otherExpensesData = [];
                        break;
                }
            @endphp
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
                    @foreach ($otherExpensesData as $item)
                    <tr class="item" style="{{ $loop->last ? 'font-weight: bold; border-top: 2px solid #000;' : '' }}">
                        <td style="font-size: 9px">{{ $loop->last ? '' : $loop->index + 1 }}</td>

                            @foreach ($item as $value)
                                <td style="font-size: 9px">{{ $value }}</td>
                                
                            @endforeach
                        
                        </tr>
                                                    
                    @endforeach
                </tbody>

            </table>
            
        @endforeach
        <table>
            <tbody>
                <tr style="border-bottom: 2px solid black; border-top: 2px solid black;">
                    <th style="font-size: 11px; font-weight:bold;" colspan="2">Expenses Total</th>
                    <th style="text-align:right; font-size: 9px; font-weight:bold;">{{ manageAmountFormat($otherExpensesGrandTotal) }}</th>
                 
                </tr>
            </tbody>
        </table>

        {{-- SECTION 2  PAYABLES --}}
            {{-- PAYABLES  --}}
           
            <table style="text-align: left;">
                <tbody>
                    <tr class="top">
                        <th>
                            <h3>PAYABLES</h3>
                        </th>
                    </tr>

                </tbody>
                </table>
            <table>
                <thead>
                    <tr class="heading" style="border-bottom: 1px solid #858383;">
                        <th style="font-size: 11px">#</th>
                        {{-- <th style="text-align: left;font-size: 11px">Account #</th> --}}
                        <th style="text-align: left;font-size: 11px">Name</th>
                        <th style="text-align: right;font-size: 11px">Y Sales</th>
                        <th style="text-align: right;font-size: 11px">Y INV Sales</th>
                        <th style="text-align: right;font-size: 11px">Returns</th>
                        <th style="text-align: right;font-size: 11px">Total Sales</th>
                        <th style="text-align: right;font-size: 11px">Eazzy</th>
                        <th style="text-align: right;font-size: 11px">Vooma</th>
                        <th style="text-align: right;font-size: 11px">Mpesa</th>
                        <th style="text-align: right;font-size: 11px">Cheques</th>
                        <th style="text-align: right;font-size: 11px">CRC</th>
                        <th style="text-align: right;font-size: 11px">Total Cash</th>
                        <th style="text-align: right;font-size: 11px">Variance</th>

                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalYesterdaySales = 0;
                        $totalYesterdaySalesInv = 0;
                        $totalReturns = 0;
                        $totalNetSales = 0;
                        $totalEazzy = 0;
                        $totalVooma = 0;
                        $totalMpesa = 0;
                        $totalCheques = 0;
                        $totalCRC = 0;
                        $totalNetCash = 0;
                        $totalVariance = 0;
                        $index = 1;
                        // dd($data);
                    @endphp
                    @foreach ($data->sortByDesc('yesterdaySales') as $item)
                        @if ($item->yesterdaySales >= 1)
                            <tr class="item" style="border-bottom: 1px solid #ccc">
                                <td style="font-size: 9px">{{ $index ++ }}</td>
                                {{-- <td style="font-size: 9px">{{ $item->customer_code }}</td> --}}
                                <td style="font-size: 9px">{{ ucwords(strtolower($item->name)) }}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($item->yesterdaySales) }}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($item->yesterdaySalesInv) }}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($item->returns) }}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($item->yesterdaySales + $item->yesterdaySalesInv - $item->returns) }}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($item->Eazzy) .'('. $item->Eazzy_count.')' }}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($item->Vooma) .'('. $item->Vooma_count.')'}}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($item->Mpesa) .'('. $item->Mpesa_count.')' }}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($item->Cheques) .'('. $item->Cheques_count.')' }}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat(0) .'(0)' }}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($item->Eazzy + $item->Vooma + $item->Mpesa )  }}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat(($item->yesterdaySales + $item->yesterdaySalesInv - $item->returns) - ($item->Eazzy + $item->Vooma + $item->Mpesa) )  }}</td>

                                @php
                                    $totalYesterdaySales += $item->yesterdaySales;
                                    $totalYesterdaySalesInv += $item->yesterdaySalesInv;
                                    $totalReturns += $item->returns;
                                    $totalNetSales += ($item->yesterdaySales + $item->yesterdaySalesInv - $item->returns);
                                    $totalEazzy += $item->Eazzy;
                                    $totalVooma += $item->Vooma;
                                    $totalMpesa += $item->Mpesa;
                                    $totalCheques += $item->Cheques;
                                    $totalCRC += 0;
                                    $totalNetCash += ($item->Eazzy + $item->Vooma + $item->Mpesa + $item->Cheques);
                                    $totalVariance += ($item->yesterdaySales + $item->yesterdaySalesInv - $item->returns) - ($item->Eazzy + $item->Vooma + $item->Mpesa + $item->Cheques);
                                @endphp
                            </tr>
                            
                        @endif
                            
                    @endforeach
                    <tr style="border-bottom: 2px solid black; border-top: 2px solid black;">
                        <th style="font-size: 11px" colspan="2">Total</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalYesterdaySales) }}</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalYesterdaySalesInv) }}</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalReturns) }}</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalNetSales) }}</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalEazzy) }}</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalVooma) }}</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalMpesa) }}</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalCheques) }}</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat(0) }}</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalNetCash) }}</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalVariance) }}</th>

                    </tr>
                    <tr style="border-bottom: 2px solid black; border-top: 2px solid black;">
                        <th style="font-size: 11px" colspan="11">{{'Sales For : '. $yesterday}}</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalNetSales) }}</th>
                        <th style="text-align:right;font-size: 9px"></th>

                    </tr>
                    <tr style="border-bottom: 2px solid black; border-top: 2px solid black;">
                        <th style="font-size: 11px" colspan="11">Variance</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalNetSales - $totalNetCash ) }}</th>
                        <th style="text-align:right;font-size: 9px"></th>
                        
                    </tr>
                    {{-- <tr style="border-bottom: 2px solid black; border-top: 2px solid black;">
                        <th style="font-size: 11px" colspan="11">Other Receivables</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat(0) }}</th>
                        <th style="text-align:right;font-size: 9px"></th>
                        
                    </tr>
                    <tr style="border-bottom: 2px solid black; border-top: 2px solid black;">
                        <th style="font-size: 11px" colspan="11">Total Bankings</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalNetCash) }}</th>
                        <th style="text-align:right;font-size: 9px"></th>
                        
                    </tr> --}}
                </tbody>
            </table>   

            {{-- OTHER PAYABLES  --}}
               
           <table style="text-align: left;">
               <tbody>
                   <tr class="top">
                       <th>
                           <h3>OTHER RECEIVABLES</h3>
                       </th>
                   </tr>

               </tbody>
            </table>
            <table>
                <thead>
                    <tr class="heading" style="border-bottom: 1px solid #858383;">
                        <th style="font-size: 11px">#</th>
                        <th style="text-align: left;font-size: 11px">Name</th>
                        <th style="text-align: right;font-size: 11px">Returns</th>
                        <th style="text-align: right;font-size: 11px">Eazzy</th>
                        <th style="text-align: right;font-size: 11px">Vooma</th>
                        <th style="text-align: right;font-size: 11px">Mpesa</th>
                        <th style="text-align: right;font-size: 11px">Cheques</th>
                        <th style="text-align: right;font-size: 11px">CRC</th>
                        <th style="text-align: right;font-size: 11px">Total Collection</th>
                        <th style="text-align: right;font-size: 11px">Total Receivables</th>

                    </tr>
                </thead>
                <tbody>
                    @php
                  
                        $totalReturnsOther = 0;
                        $totalEazzyOther = 0;
                        $totalVoomaOther = 0;
                        $totalMpesaOther = 0;
                        $totalChequesOther = 0;
                        $totalCRCOther = 0;
                        $totalNetCashOther = 0;
                        $totalReceivablesOther = 0;
                        $indexOther = 1;
                        // dd($data);
                    @endphp
                    @foreach ($data->sortByDesc('yesterdaySales') as $item)
                        @if (!($item->yesterdaySales >= 1))
                            <tr class="item" style="border-bottom: 1px solid #ccc">
                                <td style="font-size: 9px">{{ $indexOther ++ }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($item->name)) }}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($item->returns) }}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($item->Eazzy) .'('. $item->Eazzy_count.')' }}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($item->Vooma) .'('. $item->Vooma_count.')'}}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($item->Mpesa) .'('. $item->Mpesa_count.')' }}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($item->Cheques) .'('. $item->Cheques_count.')' }}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat(0) .'(0)' }}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($item->Eazzy + $item->Vooma + $item->Mpesa )  }}</td>
                                <td style="text-align:right;font-size: 9px">{{ manageAmountFormat(($item->returns) + ($item->Eazzy + $item->Vooma + $item->Mpesa) )  }}</td>

                                @php
                             
                                    $totalReturnsOther += $item->returns;
                                    $totalEazzyOther += $item->Eazzy;
                                    $totalVoomaOther += $item->Vooma;
                                    $totalMpesaOther += $item->Mpesa;
                                    $totalChequesOther += $item->Cheques;
                                    $totalCRCOther += 0;
                                    $totalNetCashOther += ($item->Eazzy + $item->Vooma + $item->Mpesa + $item->Cheques);
                                    $totalReceivablesOther += ($item->returns) + ($item->Eazzy + $item->Vooma + $item->Mpesa + $item->Cheques);
                                @endphp
                            </tr>
                            
                        @endif
                            
                    @endforeach
                    <tr style="border-bottom: 2px solid black; border-top: 2px solid black;">
                        <th style="font-size: 11px" colspan="2">Total</th>

                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalReturnsOther) }}</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalEazzyOther) }}</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalVoomaOther) }}</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalMpesaOther) }}</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalChequesOther) }}</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat(0) }}</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalNetCashOther) }}</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalReceivablesOther) }}</th>

                    </tr>


                 
                        
                    </tr> <tr style="border-bottom: 2px solid black; border-top: 2px solid black;">
                        <th style="font-size: 11px" colspan="8">Total Collections For {{$yesterday}}</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalNetCash) }}</th>
                        <th style="text-align:right;font-size: 9px"></th>
                        
                    </tr>
                  
                 
                  
                    <tr style="border-bottom: 2px solid black; border-top: 2px solid black;">
                        <th style="font-size: 11px" colspan="8">Total Bankings</th>
                        <th style="text-align:right;font-size: 9px">{{ manageAmountFormat($totalNetCashOther + $totalNetCash) }}</th>
                        <th style="text-align:right;font-size: 9px"></th>
                        
                    </tr>
                </tbody>
            </table> 

             {{-- INVOICE SALES   --}}
               
           <table style="text-align: left;">
                <tbody>
                    <tr class="top">
                        <th>
                            <h3>INVOICE SALES</h3>
                        </th>
                    </tr>

                </tbody>
            </table>
            <table>
                <thead>
                    <tr class="heading" style="border-bottom: 1px solid #858383;">
                        <th style="font-size: 11px">#</th>
                        <th style="text-align: left;font-size: 11px">Route</th>
                        <th style="text-align: left;font-size: 11px">Customer</th>
                        <th style="text-align: left;font-size: 11px">INV No.</th>
                        <th style="text-align: right;font-size: 11px">Amount</th>

                    </tr>
                </thead>
                @if ($invoiceSalesDetails->count() > 0 )
                <tbody>
                    @php
                        $total_invoice_sales = 0;
                    @endphp
                    @foreach ($invoiceSalesDetails as $invoiceSale)
                        <tr class="item" style="border-bottom: 1px solid #ccc">
                            <td style="font-size: 9px">{{ $loop->index +1 }}</td>
                            <td style="font-size: 9px">{{ ucwords(strtolower($invoiceSale->route_name)) }}</td>
                            <td style="font-size: 9px">{{ ucwords(strtolower($invoiceSale->customer_name)) }}</td>
                            <td style="font-size: 9px">{{ ucwords(strtolower($invoiceSale->requisition_no)) }}</td>
                            <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($invoiceSale->vcs) }}</td>
                        </tr>
                        @php
                            $total_invoice_sales += $invoiceSale->vcs;
                        @endphp
                        
                    @endforeach
                    <tr class="item" style="border-bottom: 1px solid #ccc">
                        <td style="font-size: 9px" colspan="4">Totals</td>
                        <td style="text-align:right;font-size: 9px">{{ manageAmountFormat($total_invoice_sales) }}</td>
                    </tr>
                </tbody>
            @endif
            </table>
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
                        <th style="text-align: left;font-size: 11px">Route</th>
                        <th style="text-align: left;font-size: 11px">Customer</th>
                        <th style="text-align: left;font-size: 11px">Cheque Date</th>
                        <th style="text-align: left;font-size: 11px">Cheque No.</th>
                        <th style="text-align: left;font-size: 11px">Drawers Name</th>
                        <th style="text-align: left;font-size: 11px">Drawers Bank</th>
                        <th style="text-align: left;font-size: 11px">Bank Deposited</th>
                        <th style="text-align: left;font-size: 11px">Deposited By</th>
                        <th style="text-align: right;font-size: 11px">Amount</th>

                    </tr>
                </thead>
                @if ($maturedCheques->count() > 0 )
                    <tbody>
                        @php
                            $total_cheques = 0;
                        @endphp
                        @foreach ($maturedCheques as $maturedCheque)
                            <tr class="item" style="border-bottom: 1px solid #ccc">
                                <td style="font-size: 9px">{{ $loop->index +1 }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->route_name)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->customer_name)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->cheque_date)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->cheque_no)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->drawers_name)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->drawers_bank)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->bank_deposited)) }}</td>
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
                @endif
               
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
                        <th style="text-align: left;font-size: 11px">Route</th>
                        <th style="text-align: left;font-size: 11px">Customer</th>
                        <th style="text-align: left;font-size: 11px">Cheque Date</th>
                        <th style="text-align: left;font-size: 11px">Cheque No.</th>
                        <th style="text-align: left;font-size: 11px">Drawers Name</th>
                        <th style="text-align: left;font-size: 11px">Drawers Bank</th>
                        <th style="text-align: left;font-size: 11px">Bank Deposited</th>
                        <th style="text-align: left;font-size: 11px">Deposited By</th>
                        <th style="text-align: right;font-size: 11px">Amount</th>

                    </tr>
                </thead>
                @if ($maturedCheques->count() > 0 )
                    <tbody>
                        @php
                            $total_cheques = 0;
                        @endphp
                        @foreach ($maturedCheques as $maturedCheque)
                            <tr class="item" style="border-bottom: 1px solid #ccc">
                                <td style="font-size: 9px">{{ $loop->index +1 }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->route_name)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->customer_name)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->cheque_date)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->cheque_no)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->drawers_name)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->drawers_bank)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->bank_deposited)) }}</td>
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
                @endif
               
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
                        <th style="text-align: left;font-size: 11px">Route</th>
                        <th style="text-align: left;font-size: 11px">Customer</th>
                        <th style="text-align: left;font-size: 11px">Cheque Date</th>
                        <th style="text-align: left;font-size: 11px">Cheque No.</th>
                        <th style="text-align: left;font-size: 11px">Drawers Name</th>
                        <th style="text-align: left;font-size: 11px">Drawers Bank</th>
                        <th style="text-align: left;font-size: 11px">Bank Deposited</th>
                        <th style="text-align: left;font-size: 11px">Deposited By</th>
                        <th style="text-align: right;font-size: 11px">Amount</th>

                    </tr>
                </thead>
                @if ($maturedCheques->count() > 0 )
                    <tbody>
                        @php
                            $total_cheques = 0;
                        @endphp
                        @foreach ($maturedCheques as $maturedCheque)
                            <tr class="item" style="border-bottom: 1px solid #ccc">
                                <td style="font-size: 9px">{{ $loop->index +1 }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->route_name)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->customer_name)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->cheque_date)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->cheque_no)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->drawers_name)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->drawers_bank)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->bank_deposited)) }}</td>
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
                @endif
               
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
                        <th style="text-align: left;font-size: 11px">Route</th>
                        <th style="text-align: left;font-size: 11px">Customer</th>
                        <th style="text-align: left;font-size: 11px">Cheque Date</th>
                        <th style="text-align: left;font-size: 11px">Cheque No.</th>
                        <th style="text-align: left;font-size: 11px">Drawers Name</th>
                        <th style="text-align: left;font-size: 11px">Drawers Bank</th>
                        <th style="text-align: left;font-size: 11px">Bank Deposited</th>
                        <th style="text-align: left;font-size: 11px">Deposited By</th>
                        <th style="text-align: right;font-size: 11px">Amount</th>

                    </tr>
                </thead>
                @if ($maturedCheques->count() > 0 )
                    <tbody>
                        @php
                            $total_cheques = 0;
                        @endphp
                        @foreach ($maturedCheques as $maturedCheque)
                            <tr class="item" style="border-bottom: 1px solid #ccc">
                                <td style="font-size: 9px">{{ $loop->index +1 }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->route_name)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->customer_name)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->cheque_date)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->cheque_no)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->drawers_name)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->drawers_bank)) }}</td>
                                <td style="font-size: 9px">{{ ucwords(strtolower($maturedCheque->bank_deposited)) }}</td>
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
                @endif
               
            </table>
            
         

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

            {{-- SECTION 3 --}}
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

        

        @endif
        
    </div>
</body>

</html>