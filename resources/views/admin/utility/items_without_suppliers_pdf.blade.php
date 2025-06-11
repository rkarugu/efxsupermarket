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
    </style>
</head>

<body>

    <h3>Items Without Suppliers</h3>
    <hr>

    <div class="user-section">
        <div class="user-section-title">
            <table>
                <thead>
                    <tr style="font-size: 10px">
                        <th style="width: 3%;">#</th>
                        <th>STOCK ID CODE</th>
                        <th>DESCRIPTION</th>
                        <th>CATEGORY</th>
                        <th>PACK SIZE</th>
                        <th>STANDARD COST</th>
                        <th>SELLING PRICE</th>
                        <th>% MARGIN</th>
                        <th>QOH</th>
                        <th>TAX CATEGORY</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $item)
                        <tr style="font-size: 10px">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->stock_id_code ?? '' }}</td>
                            <td>{{ $item->description ?? '' }}</td>
                            <td>{{ $item->category ?? '' }}</td>
                            <td>{{ $item->pack_size ?? '' }}</td>
                            <td>{{ $item->standard_cost ?? '' }}</td>
                            <td>{{ $item->selling_price ?? '' }}</td>
                            <td>
                                {{ number_format(
                                    $item->standard_cost != 0 ? (($item->selling_price - $item->standard_cost) / $item->standard_cost) * 100 : 0,
                                    2,
                                ) }}
                            </td>
                            <td>{{ $item->item_total_quantity ?? 0 }}</td>
                            <td>{{ $item->tax_manager ?? '' }}</td>
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
