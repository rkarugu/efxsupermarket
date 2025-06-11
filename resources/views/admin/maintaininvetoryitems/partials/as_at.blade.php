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

        .header {
            margin-bottom: 10px;
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
            border-top: 1px solid #949090
        }

        .table>thead>tr>th {
            border-bottom: 2px solid #f4f4f4;
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
            border: 1px solid #f4f4f4
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
        <h1 class="text-left">{{ getAllSettings()['COMPANY_NAME'] }}</h1>
        <h3 class="text-left">@yield('title')</h3>
    </div>
    <table class="table no-border" style="margin-bottom:15px">
        <tr>
            <th class="text-left">
                @isset($description)
                    <h5>{{ $description }}</h5>
                @endisset
            </th>
            <th class="text-right">Report As At Date: {{ $start_date }}</th>
        </tr>
    </table>
 
<table class="table table-bordered table-hover">
    <thead>
        <tr>

            <th width="10%">Stock ID Code</th>
            <th width="10%">Title</th>
            <th width="10%">Total</th>
            @foreach ($locations as $loc)
                <th>{{ $loc->location_name }}</th>
            @endforeach
        </tr>
    </thead>
    @php
        $t_t_total = 0;
    @endphp
    <tbody>
        @foreach ($items as $item)
            <tr>
                <td>{{ $item->stock_id_code }}</td>
                <td>{{ $item->title }}</td>
                @php
                    $t_total = 0;
                @endphp
                @foreach ($locations as $loc)
                    @php
                        $t_total += $type == 'values' ? $item['qty_inhand_' . $loc->id] * $item->selling_price : $item['qty_inhand_' . $loc->id];
                        $t_t_total += $type == 'values' ? $item['qty_inhand_' . $loc->id] * $item->selling_price : $item['qty_inhand_' . $loc->id];
                    @endphp
                @endforeach
                <td>{{ manageAmountFormat($t_total) }}</td>
                @foreach ($locations as $loc)
                    <td>{{ manageAmountFormat($type == 'values' ? $item['qty_inhand_' . $loc->id] * $item->selling_price : $item['qty_inhand_' . $loc->id]) }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2" style="text-align:right">Grand Total:</th>
            <th>{{ manageAmountFormat($t_t_total) }}</th>
            @foreach ($locations as $loc)
                @php
                    $l_total = 0;
                @endphp
                @foreach ($items as $item)
                    @php
                        $l_total += $type == 'values' ? $item['qty_inhand_' . $loc->id] * $item->selling_price : $item['qty_inhand_' . $loc->id];
                    @endphp
                @endforeach
                <td>{{ manageAmountFormat($l_total) }}</td>
            @endforeach
        </tr>
    </tfoot>
</table>
</body>

</html>

