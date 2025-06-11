<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
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
            margin: 0 0 10px 0;
            line-height: 1.6;
        }

        h1 {
            font-size: 18px
        }

        h2 {
            font-size: 16px
        }

        h3 {
            font-size: 14px
        }

        h5 {
            font-size: 12px
        }

        a {
            color: #06f;
        }

        .header {
            margin-bottom: 15px;
            text-align: center;
        }

        .table * {
            font-size: 12px;
        }

        table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        table td {
            padding: 3px;
            vertical-align: top;
        }

        .table>thead>tr>th,
        .table>tbody>tr>th,
        .table>tfoot>tr>th,
        .table>thead>tr>td,
        .table>tbody>tr>td,
        .table>tfoot>tr>td {
            border-top: 1px solid #949090;
        }

        .table>thead>tr>th {
            border-bottom: 2px solid #e3e3e3;
            padding: 5px 3px;
            background: #eee;
        }

        .table-bordered {
            border: 1px solid #f4f4f4
        }

        .table-bordered>thead>tr>th,
        .table-bordered>tbody>tr>th,
        .table-bordered>tfoot>tr>th,
        .table-bordered>thead>tr>td,
        .table-bordered>tbody>tr>td,
        .table-bordered>tfoot>tr>td {
            border: 1px solid #f4f4f4;
        }

        .table-bordered>thead>tr>th,
        .table-bordered>thead>tr>td {
            border-bottom-width: 2px
        }

        .table.no-border,
        .table.no-border td,
        .table.no-border th {
            border: 0;
            padding: 5px 0;
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


        .text-center {
            text-align: center
        }


        .text-left {
            text-align: left
        }


        .text-right {
            text-align: right
        }

        .border-top {
            border-top: 1px solid #111 !important;
            margin-left: 10px;
            padding: 5px 0;
        }

        .double-underline {
            border-bottom: 6px double #000 !important;
        }
    </style>
</head>

<body>
    <h1>{{ getAllSettings()['COMPANY_NAME'] }}</h1>
    <h2 style="margin-bottom: 0">Trade Profit & Loss</h2>
    <table class="table no-border" style="margin-bottom:15px;">
        <tr>
            <th class="text-left" style="padding: 0">For the Period: {{ $from }} - {{ $to }}</th>
            <th class="text-right">Date: {{ date('d/m/Y') }}</th>
        </tr>
    </table>
    <table class="table no-border" style="margin-top: 25px">
        <tr>
            <th class="text-left">Sales</th>
            <th style="width: 180px" class="text-right"></th>
            <th style="width: 180px" class="text-right">{{ manageAmountFormat($sales) }}</th>
        </tr>
        <tr>
            <th class="text-left">Cost of Goods Sold</th>
            <th colspan="2"></th>
        </tr>
        <tr>
            <td class="text-left">Opening Stock</td>
            <td class="text-right">{{ manageAmountFormat($openingStock) }}</td>
            <td class="text-right"></td>
        </tr>
        <tr>
            <td class="text-left">Purchases</td>
            <td class="text-right">{{ manageAmountFormat($purchases) }}</td>
            <td class="text-right"></td>
        </tr>
        <tr>
            <td></td>
            <td class="text-right">
                <div class="border-top">
                    {{ manageAmountFormat($cog = $openingStock + $purchases) }}
                </div>
            </td>
            <td></td>
        </tr>
        <tr>
            <td class="text-left">Closing Stock</td>
            <td class="text-right">{{ manageAmountFormat($closingStock) }}</td>
            <td class="text-right">{{ manageAmountFormat($netCost = $cog - $closingStock) }}</td>
        </tr>
        <tr>
            <th class="text-left">Gross Profit</th>
            <td class="text-right">
                <div class="border-top">
                </div>
            </td>
            <td class="text-right">
                <div class="border-top">
                    {{ manageAmountFormat($grossProfit = $sales - $netCost) }}
                </div>
            </td>
        </tr>
        <tr>
            <th class="text-left">Expenses</th>
            <td colspan="2"></td>
        </tr>
        @foreach ($expenses as $expense)
            <tr>
                <td>{{ $expense->getAccountDetail->account_name }}</td>
                <td class="text-right">{{ manageAmountFormat($expense->amount) }}</td>
                <td></td>
            </tr>
            @if ($loop->last)
                <tr>
                    <td></td>
                    <td class="text-right">{{ manageAmountFormat($expense->amount) }}</td>
                    <td class="text-right">{{ manageAmountFormat($expenses->sum('amount')) }}</td>
                </tr>
            @endif
        @endforeach
        <tr>
            <th class="text-left">Net Profit</th>
            <th class="text-right"></th>
            <th class="text-right">
                <div class="double-underline border-top">
                    {{ manageAmountFormat($grossProfit - $expenses->sum('amount')) }}
                </div>
            </th>
        </tr>
    </table>
</body>

</html>
