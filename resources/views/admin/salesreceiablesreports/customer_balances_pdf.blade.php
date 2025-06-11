@php
    $settings = getAllSettings();
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> CUSTOMER BALANCE REPORT </title>
    <style>
        body {
            font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-weight: 400;
            margin: 0;
            padding: 0;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            margin: 0;
            line-height: 1.6;
        }

        h1 {
            font-size: 22px
        }

        h2 {
            font-size: 18px
        }

        h3 {
            font-size: 16px
        }

        h5 {
            font-size: 14px
        }

        a {
            color: #06f;
        }

        .table * {
            font-size: 10px;
        }

        table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 3px;
            vertical-align: center;
        }

        .table>thead>tr>th,
        .table>tbody>tr>th,
        .table>tfoot>tr>th,
        .table>thead>tr>td,
        .table>tbody>tr>td,
        .table>tfoot>tr>td {
            border-top: 1px solid #373737
        }

        .table>thead>tr>th {
            border-bottom: 2px solid #373737;
            padding: 5px;
            background: #eee;
        }

        .table.signatures>tbody>tr>td {
            padding: 15px 5px
        }

        .table.signatures>tbody>tr>th {
            padding: 15px 5px;
            width: 15%;
        }

        .table-bordered {
            border: 1px solid #373737
        }

        .table-bordered>thead>tr>th,
        .table-bordered>tbody>tr>th,
        .table-bordered>tfoot>tr>th,
        .table-bordered>thead>tr>td,
        .table-bordered>tbody>tr>td,
        .table-bordered>tfoot>tr>td {
            border: 1px solid #373737
        }

        .table-bordered>thead>tr>th,
        .table-bordered>thead>tr>td {
            border-bottom-width: 2px
        }

        .table.no-border,
        .table.no-border td,
        .table.no-border th {
            border: 0
        }

        table.text-center,
        table td.text-center,
        table th.text-center {
            text-align: center
        }

        table.text-right,
        table td.text-right,
        table th.text-right {
            text-align: right
        }

        table.text-left,
        table td.text-left,
        table th.text-left {
            text-align: left
        }

        .underline {
            border-bottom: 1px solid #aaa !important;
        }

        .text-center {
            text-align: center
        }

        .text-left {
            text-align: left
        }

        .text-right {
            text-align: right
        }
    </style>
</head>

<body>

    <div class="invoice-box" style="margin-bottom: 15px">
        <table class="table no-border" style="text-align: left;">
            <tbody>
                <tr class="top">
                    <th colspan="2" style="font-size:18px !important;font-weight: bold; text-align:left !important">
                        {{ $settings['COMPANY_NAME'] }}
                    </th>
                </tr>
                <tr class="top">
                    <th colspan="2"
                        style="font-size:16px !important;font-weight: bold; text-align:left !important; margin:3px !important;">
                        {{ $branch->name }}
                    </th>
                </tr>

                <tr class="top" style="margin-bottom:5px !important;">
                    <th colspan="1" style="font-size:15px !important;font-weight: bold; text-align:left !important">
                        CUSTOMER BALANCE REPORT
                    </th>
                    <th colspan="1"
                        style="font-size:15px !important;font-weight: bold; text-align:right !important; margin:3px !important;">
                        {{ $date }}
                    </th>
                </tr>


            </tbody>
        </table>
    </div>

    <table id="customers-table" style="font-size: 10px !important;" class="table table-bordered">
        <thead>
            <tr class="heading">
                <th style="width: 3%; text-align: left !important;">#</th>
                <th style="text-align: left;">Customer</th>
                <th style="text-align: left;">Balance B/f</th>
                <th style="text-align: left;">Debits</th>
                <th style="text-align: left;">Credits</th>
                <th style="text-align: left; width: 20%;">Lastrans</th>
                <th style="text-align: right;">Pd Chqs</th>
                <th style="text-align: right;">Balance</th>
            </tr>
        </thead>

        <tbody>
            @php
                $balanceBfTotal = 0;
                $debitsTotal = 0;
                $creditsTotal = 0;
                $pdChqsTotal = 0;
                $balanceTotal = 0;
            @endphp

            @foreach ($records as $index => $record)
                <tr>
                    <th scope="row" style="width: 3%;"> {{ $loop->index + 1 }}</th>
                    <td> {{ $record['customer'] }} </td>
                    <td> {{ number_format($record['balance_bf'], 2) }} </td>
                    <td> {{ number_format($record['debits'], 2) }} </td>
                    <td> {{ number_format($record['credits'], 2) }} </td>
                    <td style="width: 20%;">{{ $record['last_trans_time'] }}</td>
                    <td style="text-align: right;"> {{ number_format($record['pd_cheques'], 2) }} </td>
                    <td style="text-align: right;"> {{ number_format($record['balance'], 2) }} </td>
                </tr>

                @php
                    $balanceBfTotal += $record['balance_bf'];
                    $debitsTotal += $record['debits'];
                    $creditsTotal += $record['credits'];
                    $pdChqsTotal += $record['pd_cheques'];
                    $balanceTotal += $record['balance'];
                @endphp
            @endforeach
            <tr style="border-top: 2px solid !important; border-bottom: 2px solid !important;">
                <td style="text-align: center;" colspan="2"><strong>TOTALS</strong></td>
                <td><strong>{{ number_format($balanceBfTotal, 2) }}</strong></td>
                <td><strong>{{ number_format($debitsTotal, 2) }}</strong></td>
                <td><strong>{{ number_format($creditsTotal, 2) }}</strong></td>
                <td></td>
                <td><strong>{{ number_format($pdChqsTotal, 2) }}</strong></td>
                <td style="text-align: right;"><strong>{{ number_format($balanceTotal, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>
</body>

</html>
