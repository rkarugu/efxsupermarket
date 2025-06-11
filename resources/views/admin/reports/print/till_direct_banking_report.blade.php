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
            font-size: 10px;
        }
        .table tbody tr td{
            font-size: 9px;
            overflow: hidden;
            max-height:5px;
        }
        .table tbody tr td div{
            max-height:5px;
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
    <h1>{{ getAllSettings()['COMPANY_NAME'] }}</h1>
    <h2>Tender Entries Report</h2>
    <table class="table no-border" style="margin-bottom:15px">
        <tr>
            <th class="text-left">Channel:{{ $channel ? $channel : 'All' }}</th>
            <th class="text-right">Dates: {{ $dates }}</th>
        </tr>
    </table>
        </div>
        <table class="table table-striped" id="tenderEntriesDataTable">
            <thead>
                <tr>
                    <th class="text-left"width="3%" style="width:3%;">#</th>
                    <th class="text-left" width="6%" style="width:6%;">Date</th>
                    <th class="text-left" width="10%" style="width:10%;">Channel</th>
                    <th class="text-left" width="10%" style="width:10%;">Customer Name</th>
                    <th class="text-left" width="15%" style="width:15%;max-width:15%;">Reference</th>
                    <th class="text-left" width="15%" style="width:15%;max-width:15%;">Adiitonal Info</th>
                    {{-- <th>Paid By</th> --}}
                    <th class="text-right" width="10%" style="width:10%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $transaction)
                    <tr>
                        <td class="text-left">{{ $loop->iteration }}</td>
                        <td class="text-left">{{ date('Y-m-d', strtotime($transaction['trans_date'])) }}</td>
                        <td class="text-left">{{ $transaction['channel']=='KENYA COMMERCIAL BANK'? 'KCB' : $transaction['channel'] }}</td>
                        <td class="text-left"><div>{{ $transaction['customer_name'] }}</div></td>
                        <td class="text-left" >
                            <div>{{ str_replace('/', '/ ', $transaction['reference']) }}</div>
                        </td>
                        <td class="text-left"><div>{{ str_replace('/', '/ ', $transaction['additional_info'])}}</div></td>
                        {{-- <td>{{ $transaction->paid_by }}</td> --}}
                        <td class="text-right" style="font-size:9px;">{{ manageAmountFormat($transaction['amount']) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-right" colspan="6">Total</th>
                    <th class="text-right" id="total">
                        {{ manageAmountFormat($transactions->sum('amount')) }}
                    </th>
                </tr>
            </tfoot>
        </table>

        <script type="text/php">
            if (isset($pdf)) {
                $pdf->page_script('
                    $text = __("Page :pageNum of :pageCount", ["pageNum" => $PAGE_NUM, "pageCount" => $PAGE_COUNT]);
                    $font = 100;
                    $size = 9;
                    $color = array(0,0,0);
                    $word_space = 0.0;  //  default
                    $char_space = 0.0;  //  default
                    $angle = 0.0;   //  default
    
                    // Compute text width to center correctly
                    $textWidth = $fontMetrics->getTextWidth($text, $font, $size);
    
                    $x = ($pdf->get_width() - $textWidth) / 1.05;
                    $y = $pdf->get_height() - 33;
    
                    $pdf->text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
                '); // End of page_script
            }
        </script>
</body>

</html>
