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
                        <h4 style="text-align: left; margin:0">POS OVERVIEW REPORT</h4>
                    </th>
                    <th>
                        <h4 style="text-align: right; margin:0">{{ "BRANCH : ".$branchDetails->name }}
                        </h4>
                    </th>
                </tr>
                <tr>
                    <th>
                        <h5 style="text-align: left; margin:0">Print By: {{ $user->name }}</h5>
                    </th>
                    <th>
                        <h5 style="text-align: right; margin:0">DATE FROM:
                            {{ \Carbon\Carbon::parse($from_date)->toDateString() }} |
                            DATE TO {{ \Carbon\Carbon::parse($to_date) }} | TIME: {{ date('H:i A') }}</h5>
                    </th>
                </tr>
            </tbody>
        </table>
        <table>
            <thead>
                <tr  class="heading">
                    <td style="font-size: 11px">DATE</td>
                    <td style="text-align:right;font-size: 11px; text-align:right;">TOTAL PAYMENT</td>
                    {{-- <td style="text-align:right;font-size: 11px; text-align:right;">TOTAL RETURNS</td> --}}
                    <td style="text-align:right;font-size: 11px; text-align:right;">PENDING RETURNS</td>
                    <td style="text-align:right;font-size: 11px; text-align:right;">ACCEPTED RETURNS</td>
                    <td style="text-align:right;font-size: 11px; text-align:right;">EAZZY</td>
                    <td style="text-align:right;font-size: 11px; text-align:right;">VOOMA</td>
                    <td style="text-align:right;font-size: 11px; text-align:right;">MPESA</td>
                    <td style="text-align:right;font-size: 11px; text-align:right;">CASH</td>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_total_payments = $total_all_returns = $total_pending_returns = $total_accepted_returns = $total_eazzy = $total_vooma = $total_mpesa = $total_cash = 0
                @endphp
                @foreach ($payments as $row)
                    <tr>
                        <td>{{$row->date}}</td>
                        <td style="margin-top: 0px !important; padding-top: 0px !important;font-size: 9px; text-align:right;">{{ manageAmountFormat($row->total_payments)}}</td>
                        {{-- <td style="margin-top: 0px !important; padding-top: 0px !important;font-size: 9px; text-align:right;">{{manageAmountFormat($row->pending_returns + $row->accepted_returns)}}</td> --}}
                        <td style="margin-top: 0px !important; padding-top: 0px !important;font-size: 9px; text-align:right;">{{manageAmountFormat($row->pending_returns)}}</td>
                        <td style="margin-top: 0px !important; padding-top: 0px !important;font-size: 9px; text-align:right;">{{manageAmountFormat($row->accepted_returns)}}</td>
                        <td style="margin-top: 0px !important; padding-top: 0px !important;font-size: 9px; text-align:right;">{{manageAmountFormat($row->Eazzy)}}</td>
                        <td style="margin-top: 0px !important; padding-top: 0px !important;font-size: 9px; text-align:right;">{{manageAmountFormat($row->Vooma)}}</td>
                        <td style="margin-top: 0px !important; padding-top: 0px !important;font-size: 9px; text-align:right;">{{manageAmountFormat($row->Mpesa)}}</td>
                        <td style="margin-top: 0px !important; padding-top: 0px !important;font-size: 9px; text-align:right;">{{manageAmountFormat($row->Cash)}}</td>
                    </tr>
                    @php
                        $total_total_payments += $row->total_payments;
                        $total_all_returns += ($row->pending_returns + $row->accepted_returns);
                        $total_pending_returns += $row->pending_returns;
                        $total_accepted_returns += $row->accepted_returns;
                        $total_eazzy += $row->Eazzy;
                        $total_vooma += $row->Vooma;
                        $total_mpesa += $row->Mpesa;
                        $total_cash += $row->Cash;
                    @endphp
                    
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th >Total</th>
                    <th style="margin-top: 0px !important; padding-top: 0px !important;font-size: 9px; text-align:right;">{{manageAmountFormat($total_total_payments)}}</th>
                    {{-- <th style="margin-top: 0px !important; padding-top: 0px !important;font-size: 9px; text-align:right;">{{manageAmountFormat($total_all_returns)}}</th> --}}
                    <th style="margin-top: 0px !important; padding-top: 0px !important;font-size: 9px; text-align:right;">{{manageAmountFormat($total_pending_returns)}}</th>
                    <th style="margin-top: 0px !important; padding-top: 0px !important;font-size: 9px; text-align:right;">{{manageAmountFormat($total_accepted_returns)}}</th>
                    <th style="margin-top: 0px !important; padding-top: 0px !important;font-size: 9px; text-align:right;">{{manageAmountFormat($total_eazzy)}}</th>
                    <th style="margin-top: 0px !important; padding-top: 0px !important;font-size: 9px; text-align:right;">{{manageAmountFormat($total_vooma)}}</th>
                    <th style="margin-top: 0px !important; padding-top: 0px !important;font-size: 9px; text-align:right;">{{manageAmountFormat($total_mpesa)}}</th>
                    <th style="margin-top: 0px !important; padding-top: 0px !important;font-size: 9px; text-align:right;">{{manageAmountFormat($total_cash)}}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>
