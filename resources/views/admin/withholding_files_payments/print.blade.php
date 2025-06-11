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
            margin: 0 0 5px 0;
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

        .table * {
            font-size: 12px;
        }

        .table.lists * {
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
            padding: 3px 5px;
            vertical-align: center;
        }

        .table>thead>tr>th,
        .table>tbody>tr>th,
        .table>tfoot>tr>th,
        .table>thead>tr>td,
        .table>tbody>tr>td,
        .table>tfoot>tr>td {
            border-top: 1px solid #aaaaaa
        }

        .table>thead>tr>th {
            border-bottom: 2px solid #aaaaaa;
            padding: 5px;
            background: #eee;
            text-align: left;
        }

        .table.signatures>tbody>tr>td {
            padding: 15px 5px
        }

        .table.signatures>tbody>tr>th {
            padding: 15px 5px;
            width: 15%;
        }

        .table-bordered {
            border: 1px solid #aaaaaa
        }

        .table-bordered>thead>tr>th,
        .table-bordered>tbody>tr>th,
        .table-bordered>tfoot>tr>th,
        .table-bordered>thead>tr>td,
        .table-bordered>tbody>tr>td,
        .table-bordered>tfoot>tr>td {
            border: 1px solid #aaaaaa
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
            text-align: center !important;
        }

        .text-left {
            text-align: left !important;
        }

        .text-right {
            text-align: right !important;
        }
    </style>
</head>

<body>
    <div class="text-left">
        <h1>{{ getAllSettings()['COMPANY_NAME'] }}</h1>
        <h3>WITHHOLDING TAX PAYMENT VOUCHER</h3>
        <h5>VOUCHER NO : {{ $voucher->number }}</h5>
    </div>
    <table class="table no-border" style="margin-top: 15px">
        <tr>
            <th class="text-left">
                <span style="margin-right: 10px"> {{ $voucher->withholdingGlAccount->account_code }} </span>
                <span style="margin-right: 10px">{{ $voucher->withholdingGlAccount->account_name }}</span>
            </th>
            <th class="text-right">
                {{ $voucher->created_at->format('d/m/Y H:i') }}
            </th>
        </tr>
    </table>
    <hr style="border-color: #aaa">
    <table class="table table-bordered" style="margin-top: 30px; margin-right:auto; width:70%">
        <tr>
            <th class="text-left">Account</th>
            <td>{{ $voucher->bankAccount->account_number }}</td>
            <td>{{ $voucher->bankAccount->account_name }}</td>
        </tr>
        <tr>
            <th class="text-left">Details</th>
            <th class="text-left">Date</th>
            <th class="text-right">Amount</th>
        </tr>
        <tr>
            <td>{{ $voucher->cheque_number }}</td>
            <td>{{ $voucher->payment_date->format('d/m/Y') }}</td>
            <td class="text-right">{{ manageAmountFormat($voucher->amount) }}</td>
        </tr>

        <tfoot>
            <tr>
                <td colspan="2"></td>
                <th class="text-right">{{ manageAmountFormat($voucher->amount) }}</th>
            </tr>
        </tfoot>
    </table>

    <table class="table table-striped lists" style="margin-top: 20px;">
        <thead>
            <tr>
                <th>Date</th>
                <th>Ref</th>
                <th>Memo</th>               
                <th class="text-right">Amount</th>
                <th class="text-right">Paid</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $voucher->payment_date->format('d/m/Y') }}</td>
                <td>{{ $voucher->withholdingGlAccount->account_code }}</td>
                <td>{{ $voucher->memo }}</td>
                <td class="text-right">{{ manageAmountFormat($voucher->amount) }}</td>
                <td class="text-right">{{ manageAmountFormat($voucher->amount) }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Total</th>
                <td class="text-right">{{ manageAmountFormat($voucher->amount) }}</td>
                <td class="text-right">{{ manageAmountFormat($voucher->amount) }}</td>
            </tr>
        </tfoot>
    </table>

    <table class="table signatures no-border" style="margin-top: 50px">
        <tbody>
            <tr>
                <th class="text-left">Prepared By:</th>
                <td class="underline"><strong>{{ strtoupper($voucher->preparedBy->name) }}</strong></td>
                <th class="text-left">Sign:</th>
                <td colspan="3" class="underline"></td>
            </tr>
            <tr>
                <th class="text-left">Approved By:</th>
                <td class="underline"></td>
                <th class="text-left">Authorized By:</th>
                <td colspan="3" class="underline"></td>
            </tr>
            <tr>
                <th class="text-left">Collected By:</th>
                <td class="underline"></td>
                <th class="text-left" style="width:5%">Sign:</th>
                <td class="underline"></td>
                <th class="text-left" style="width:5%">ID</th>
                <td class="underline"></td>
            </tr>
        </tbody>
    </table>
</body>

</html>
