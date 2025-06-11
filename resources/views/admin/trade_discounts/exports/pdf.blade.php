<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-weight: 400;
            margin: 0;
            padding: 0;
            font-size: 12px;
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

        /* Base styles */
        .table {
            width: 100%;
            max-width: 100%;
            margin-bottom: 20px;
            background-color: transparent;
            border-collapse: collapse;
        }

        .table>thead>tr>th,
        .table>tbody>tr>th,
        .table>tfoot>tr>th,
        .table>thead>tr>td,
        .table>tbody>tr>td,
        .table>tfoot>tr>td {
            padding: 5px;
            line-height: 1.42857143;
            vertical-align: top;
            border-top: 1px solid #aaa;
        }

        /* Table header */
        .table>thead>tr>th {
            vertical-align: bottom;
            border-bottom: 2px solid #aaa;
            text-align: left;
        }

        /* Striped tables */
        .table-striped>tbody>tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }

        /* Bordered tables */
        .table-bordered {
            border: 1px solid #aaa;
        }

        .table-bordered>thead>tr>th,
        .table-bordered>tbody>tr>th,
        .table-bordered>tfoot>tr>th,
        .table-bordered>thead>tr>td,
        .table-bordered>tbody>tr>td,
        .table-bordered>tfoot>tr>td {
            border: 1px solid #aaa;
        }

        /* Condensed tables */
        .table-condensed>thead>tr>th,
        .table-condensed>tbody>tr>th,
        .table-condensed>tfoot>tr>th,
        .table-condensed>thead>tr>td,
        .table-condensed>tbody>tr>td,
        .table-condensed>tfoot>tr>td {
            padding: 5px;
        }

        .table.no-border,
        .table.no-border td,
        .table.no-border th {
            border: 0;
            padding: 5px 0 0
        }

        /* Margin utility classes */
        .m-0 {
            margin: 0 !important;
        }

        .mt-0 {
            margin-top: 0 !important;
        }

        .mr-0 {
            margin-right: 0 !important;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        .ml-0 {
            margin-left: 0 !important;
        }

        .mx-0 {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        .my-0 {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }

        /* Padding utility classes */
        .p-0 {
            padding: 0 !important;
        }

        .pt-0 {
            padding-top: 0 !important;
        }

        .pr-0 {
            padding-right: 0 !important;
        }

        .pb-0 {
            padding-bottom: 0 !important;
        }

        .pl-0 {
            padding-left: 0 !important;
        }

        .px-0 {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .py-0 {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
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
    <table class="table no-border m-0">
        <tbody>
            <tr>
                <td>
                    <h1>{{ getAllSettings()['COMPANY_NAME'] }}</h1>
                </td>
            </tr>
            <tr>
                <td>
                    <h3>{{ $description }}</h3>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="table no-border">
        <tbody>
            @if ($from && $to)
                <tr>
                    <td><strong>PERIOD: {{ $from }} - {{ $to }}</strong></td>
                    <td class="text-right"><strong>Date: {{ now()->format('d/m/Y') }}</strong></td>
                </tr>
            @endif
        </tbody>
    </table>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th><strong>Ref</strong></th>
                <th><strong>Supplier</strong></th>
                <th><strong>Discount Type</strong></th>
                <th><strong>Invoice No.</strong></th>
                <th><strong>Invoice Date</strong></th>
                <th><strong>Demand No.</strong></th>
                <th><strong>Description</strong></th>
                <th><strong>Prepared By</strong></th>
                <th><strong>Approval</strong></th>
                <th><strong>Invoice Amount</th>
                <th><strong>Disc. Amount</strong></th>
                <th><strong>Approved Disc. Amount</strong></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($discounts as $discount)
                <tr>
                    <td>{{ $discount->id }}
                    <td>{{ $discount->supplier_name }}
                    <td>{{ $discount->discount_type }}
                    <td>{{ $discount->supplier_invoice_number }}
                    <td>{{ $discount->invoice_date }}
                    <td>{{ $discount->demand_no }}
                    <td>{{ $discount->description }}
                    <td>{{ $discount->prepared_by }}
                    <td>{{ $discount->status ? 'Yes' : 'No' }}
                    <td style="text-align: right">{{ $discount->invoice_amount }}
                    <td style="text-align: right">{{ number_format($discount->amount, 2) }}</td>
                    <td style="text-align: right">{{ number_format($discount->approved_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="10" style="text-align: right"><strong>Total</strong></td>
                <td style="text-align: right"><strong>{{ number_format($discounts->sum('amount'), 2) }}</strong></td>
                <td style="text-align: right">
                    <strong>{{ number_format($discounts->sum('approved_amount'), 2) }}</strong>
                </td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
