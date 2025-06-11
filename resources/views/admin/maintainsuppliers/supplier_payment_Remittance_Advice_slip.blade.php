<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remittance Advice</title>
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
            margin: 0 5px;
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

        h4 {
            font-size: 12px
        }

        h5 {
            font-size: 10px
        }

        a {
            color: #06f;
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
    <div class="text-left">
        <h1>{{ getAllSettings()['COMPANY_NAME'] }}</h1>
        <h3>REMITTANCE ADVICE</h3>
        <h5>DOCNO : {{ $voucher->document_number }}</h5>
    </div>
    <table class="table no-border" style="margin-top: 15px">
        <tr>
            <th class="text-left">
                PAYEE : {{ $voucher->supplier->name }}
            </th>
        </tr>
        <tr>
            <th class="text-left">BANK : {{ $voucher->account->account_code }} - {{ $voucher->account->account_name }}</th>
        </tr>
        <tr>
            <th class="text-left">DATE: {{ $voucher->updated_at->format('d/m/Y') }}</th>
        </tr>
        <tr>
            <td class="text-left">
                <p>Please Find Here With Payment <strong> Voucher No. {{ $voucher->number }}</strong>.</p>
                <p>FOR Slis: {{ $voucher->narration }}</p>
            </td>
        </tr>
    </table>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th style="width:13%;text-align:left">Date</th>
                <th style="width:13%;text-align:left">Type</th>
                <th style="width:20%;text-align:left">Docno</th>
                <th style="width:20%;text-align:right">Pending Amount</th>
                <th style="width:20%;text-align:right;padding-right:8px">Amount Paid </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($voucher->voucherItems as $item)
                <tr class="item">
                    <td style="text-align:left">{{ $item->transaction->trans_date->format('d/m/Y') }}</td>
                    <td style="text-align:left">{{ $item->transaction->getNumberSystem->code }}</td>
                    <td style="text-align:left">{{ $item->transaction->document_no }}</td>
                    <td style="text-align: right">
                        {{ manageAmountFormat((float) $item->transaction->total_amount_inc_vat - $item->transaction->allocated_amount- $item->transaction->withholding_amount - $item->professional_withholding) }}
                    </td>
                    <td style="text-align: right;padding-right:8px">
                        {{ manageAmountFormat($item->amount) }}
                    </td>
                </tr>
            @endforeach

        </tbody>
        <tfoot>
            <tr class="">
                <th colspan="4" style="text-align: right">Total Amount Paid</th>
                <th style="text-align:right">{{ manageAmountFormat($voucher->amount) }}
                </th>
            </tr>
            <tr class="text-center">
                <th colspan="5" style="text-align: center; padding: 15px 0">Please Acknowledge Receipt</th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
