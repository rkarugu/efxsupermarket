@php
    $settings = getAllSettings();
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> STOCK DEBTORS </title>
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
                        
                    </th>
                </tr>

                <tr class="top" style="margin-bottom:5px !important;">
                    <th colspan="1" style="font-size:15px !important;font-weight: bold; text-align:left !important">
                        STOCK DEBTOR
                    </th>
                    <th colspan="1"
                        style="font-size:15px !important;font-weight: bold; text-align:right !important; margin:3px !important;">
                        {{ date('d/m/Y') }}
                    </th>
                </tr>


            </tbody>
        </table>
    </div>

    <table id="customers-table" style="font-size: 10px !important;" class="table table-bordered">
        <thead>
            <tr class="heading">
                <th style="width: 3%; text-align: left !important;">#</th>
                <th style="text-align: left;">Name</th>
                <th style="text-align: left;">Phone Number</th>
                <th style="text-align: left;">Role</th>
                <th style="text-align: left;">Bin Location</th>
                <th style="text-align: left;">Store</th>
                <th style="text-align: right;">Amount</th>
            </tr>
        </thead>

        <tbody>
            @php
                $total = 0;
            @endphp

            @foreach ($debtors as $index => $debtor)
            @php
                $amount = $debtor->stockDebtorTrans->sum('total'); 
                $total += $amount;
            @endphp
                <tr>
                    <th scope="row" style="width: 3%;"> {{ $loop->index + 1 }}</th>
                    <td> {{ $debtor->employee->name }} </td>
                    <td> {{ $debtor->employee->phone_number }} </td>
                    <td> {{ $debtor->employee->userRole->title }} </td>
                    <td> {{ $debtor->employee->uom? $debtor->employee->uom->title : '-' }} </td>
                    <td> {{ $debtor->employee->location_stores->location_name }} </td>
                    <td style="text-align: right;"> {{ manageAmountFormat($amount) }}</td>
                </tr>

            @endforeach
            <tr style="">
                <td style="text-align: right;" colspan="6"><strong>TOTALS</strong></td>
                <td style="text-align: right;border-top: 2px solid !important; border-bottom: 2px solid !important;"><strong>{{ manageAmountFormat($total) }}</strong></td>
            </tr>
        </tbody>
    </table>
</body>

</html>
