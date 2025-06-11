<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #000000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #d3d3d3;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .user-title {
            font-weight: bold;
            padding: 10px;
        }

        .supplier-name {
            font-size: 10px;
        }

        table .header-table {
            width: 100%;
        }

        .tr-heading td {
            border: 1px solid white;
            border-collapse: collapse;
        }

        .c-heading {
            font-size: 14px;
            font-weight: bold;
            text-align: left;
        }

        .c-heading-date {
            font-size: 12px;
            text-align: right;
        }

        table .header-table {
            width: 100%;
        }

        .tr-heading td {
            border: 1px solid white;
            border-collapse: collapse;
        }

        .table-one-header {
            font-weight: bold;
            font-size: 15px;
            width: 25%;
        }

        .table-header {
            font-size: 8px;
        }

        .table-data {
            font-size: 8px;
        }

    </style>
</head>

<body>

    <div class="header-titles">
        <table class="header-table" style="border-spacing: 0;">
            <tr class="tr-heading">
                <td class="c-heading" style="padding: 2px;">
                    <p style="margin: 2px; margin-left:15px; padding: 0; font-size: 14px;">
                        Kanini Haraka Ltd
                    </p>
                </td>
                <td class="c-heading-date" style="padding: 2px; font-size: 12px;">
                    {{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}
                </td>
            </tr>
        </table>

    </div>

    <div class="header-main-tiles">
        <table class="header-table">
            <tr class="tr-heading">
                <td class="table-one-header">
                    <p style="vertical-align: top; margin: 5px; padding: 5px; font-size: 14px;">
                        Trade Agreement ( {{$trade->reference}} ) Listed Items
                    </p>
                </td>
            </tr>
        </table>
    </div>

    <hr>

    <div class="user-section">
        <div class="user-section-title">
            <table>
                <thead>
                    <tr class="table-header">
                        <th style="width: 3%;">#</th>
                        <th style="text-transform: capitalize;">STOCK CODE</th>
                        <th style="text-transform: capitalize;">TITLE</th>
                        <th style="text-transform: capitalize;">PACK SIZE</th>
                        <th style="text-transform: capitalize;">PRICE LIST COST</th>
                        <th style="text-transform: capitalize;">STANDARD COST</th>
                        <th style="text-transform: capitalize;">SELLING PRICE</th>
                        <th style="text-transform: capitalize;">MARGIN TYPE</th>
                        <th style="text-transform: capitalize;">MARGIN VALUE</th>
                        <th style="text-transform: capitalize;">TRADE AGREEMENT DATE</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inventoryitems as $index => $item)
                        <tr class="table-data">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->stock_id_code ?? '' }}</td>
                            <td>{{ $item->title ?? '' }}</td>
                            <td>{{ $item->pack_size ?? '' }}</td>
                            <td>{{ number_format($item->price_list_cost, 2) ?? '0.00' }}</td>
                            <td>{{ number_format($item->standard_cost, 2) ?? '0.00' }}</td>
                            <td>{{ number_format($item->selling_price, 2) ?? '0.00' }}</td>
                            <td>{{ $item->margin_type == 0 ? 'Value' : 'Percentage' }}</td>
                            <td>{{ number_format($item->percentage_margin, 2) ?? '0.00' }}</td>
                            <td>{{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') : '' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

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
