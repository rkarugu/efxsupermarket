<html>
<title>Goods Received Note</title>

<head>
    <style type="text/css">
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
            margin-bottom: 10px;
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
            text-align: left;
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

        .table.items>thead>tr>th {
            font-size: 0.7rem;
            text-transform: uppercase
        }

        .table.items>tbody>tr>th,
        .table.items>tbody>tr>td {
            padding: 3px;
            font-size: 11px
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

        .underline {
            text-decoration: underline;

        }
    </style>
</head>

<body>
    <table class="table no-border mb-0 mt-0">
        <tr>
            <td style="height: 40px">
                <h1>{{ $settings['COMPANY_NAME'] }}</h1>
                <table class="table no-border">
                    <tr>
                        <td>
                            {{ $settings['ADDRESS_1'] }}<br>
                            {{ $settings['ADDRESS_2'] }}<br>
                            {{ $settings['ADDRESS_3'] }}<br>
                            Tel: {{ $settings['PHONE_NUMBER'] }}<br>
                            {{ $settings['EMAILS'] }}<br>
                            {{ $settings['WEBSITE'] }}<br>
                            Pin No: {{ $settings['PIN_NO'] }}<br>
                        </td>
                        <td class="text-center" style="width: 30%;">
                            <img src="data:image/png;base64,{{ base64_encode($qr_code) }}" alt="QR Code">
                        </td>
                    </tr>
                </table>
            </td>
            <td class="text-right">
                <img src="{{ asset('uploads/restaurants/' . $branch->image) }}" class="img-circle" alt=""
                    style="width:115px; margin-bottom: 10px; display:block">

                <div style="margin-bottom: 5px"><strong>A/C Code:</strong> {{ $supplier->supplier_code }}</div>
                <div style="margin-bottom: 5px"><strong>A/C Name:</strong> {{ $supplier->name }}</div>
                <div style="margin-bottom: 5px"><strong>Period:</strong> {{ $from->format('d/m/Y') }} -
                    {{ $to->format('d/m/Y') }}</div>
                <div style="margin-bottom: 5px"><strong>Date:</strong> {{ now()->format('d/m/Y') }}</div>
            </td>
        </tr>
    </table>
    <h3 style="text-align:center;padding-bottom:5px">STATEMENT OF ACCOUNT</h3>
    <table class="table table-striped table-bordered items" style="margin-top:10px">
        <tr>
            <th class="text-right" colspan="7">Opening Balance</th>
            <th class="text-right">{{ manageAmountFormat($openingBalance) }}</th>
        </tr>
        <thead>
            <tr>
                <th>Date</th>
                <th>Memo</th>
                <th>Document No.</th>
                <th>Reference</th>
                <th>Cu Inoice No.</th>
                <th style="text-align: right;">Debit</th>
                <th style="text-align: right;">Credit</th>
                <th style="text-align: right;">Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>{{ $item->created_at->format('d/m/Y') }}</td>
                    <td>{{ $item->memo }}</td>
                    <td>{{ $item->document_no }}</td>
                    <td>{{ $item->suppreference }}</td>
                    <td>{{ $item->cu_invoice_number }}</td>
                    <td style="text-align: right;">{{ manageAmountFormat($item->debit) }}</td>
                    <td style="text-align: right;">{{ manageAmountFormat($item->credit) }}</td>
                    <td style="text-align: right;">
                        {{ manageAmountFormat($item->opening_balance + $item->total_amount_inc_vat) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th style="text-align: right;" colspan="7">Closing Balance:</th>
                <th id="total" class="text-right">
                    {{ manageAmountFormat($openingBalance + $items->sum('total_amount_inc_vat')) }}</th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
