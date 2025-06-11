<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Breaking</title>
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
            font-size: 9px;
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
    <table class="table no-border">
        <tbody>
            @if ($data->print_count > 1)
                <tr>
                    <th colspan="1" style="width: 33%;text-align:left"></th>
                    <th colspan="1" style="width: 33%;text-align:right">Reprint: {{ $data->print_count - 1 }}</th>
                </tr>
            @endif
            <tr>
                <th colspan="2" class="text-center">
                    <h2 style="font-size:18px !important">{{ $settings['COMPANY_NAME'] }}</h2>
                </th>
            </tr>
            <tr>
                <th colspan="2" class="text-center">
                    {{ $settings['ADDRESS_1'] }}<br />
                    {{ $settings['ADDRESS_2'] }},
                    {{ $settings['ADDRESS_3'] }},
                    Tel: {{ $settings['PHONE_NUMBER'] }}
                </th>
            </tr>
            <tr>
                <th colspan="2" class="text-center">STOCK BREAKING</th>
            </tr>
            <tr>
                <th colspan="1" style="width: 33%;text-align:left">Receipt NO: {{ $data->breaking_code }}</th>
                <th colspan="1" style="width: 33%;text-align:right">DATE: {{ date('d/m/Y', strtotime($data->date)) }}
                </th>
            </tr>
        </tbody>
    </table>
    <table class="table table-bordered items">
        <tbody>
            <tr>
                <th>Bulk Code</th>
                <td></td>
                <th>Split Qty</th>
                <th>Split Code</th>
                <td></td>
                <th>Factor</th>
                <th>Total Split</th>
            </tr>
            @foreach ($data->items as $item)
                <tr class="item">
                    <td>{{ @$item->source_item->stock_id_code }}</td>
                    <td>{{ @$item->source_item->description }}</td>
                    <td class="text-right">{{ @$item->source_qty }}</td>
                    <td>{{ @$item->destination_item->stock_id_code }}</td>
                    <td>{{ @$item->destination_item->description }}</td>
                    <td class="text-right">{{ @$item->conversion_factor }}</td>
                    <td class="text-right">{{ @$item->destination_qty }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <table class="table no-border" style="margin-top: 20px">
        <tbody>
            <tr>
                <td colspan="6">
                    Dispatch Date: {{ $data->dispatched_date ? $data->dispatched_date->format('d/m/Y H:i:s') : 'N/A' }}</td>
                </td>
            </tr>
            <tr>
                <td colspan="6">
                    Print Date: {{ now()->format('d/m/Y H:i:s') }}</td>
                </td>
            </tr>
            @if ($data->posRequest)
                <tr>
                    <td colspan="6" style="text-align: left">Initiated
                        By....................................{{ @$data->posRequest->getInitiatingUser->name }} </td>
                </tr>
                <tr>
                    <td colspan="6" style="text-align: left">StoreKeeper
                        ............................................{{ @$item->user->name }} </td>
                </tr>
                <tr>
                    <td colspan="6" style="text-align: left">Mother Bin
                        ..........................{{ $data->posRequest->getMotherBinDetail->title }} </td>
                </tr>
            @endif
            <tr>
                <td colspan="6" style="text-align: left">Received
                    By........................................................ </td>
            </tr>
            <tr>
                <td colspan="6" style="text-align: left">Confirmed
                    By........................................................ </td>
            </tr>
        </tbody>
    </table>
    </div>
</body>

</html>
