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

        .user-title.page-break {
            page-break-before: always;
        }

        .supplier-name {
            font-size: 10px;
        }
    </style>
</head>

<body>

    <h3>Users and Suppliers Report</h3>
    <hr>

    @foreach ($suppliers as $username => $userSuppliers)
        <div class="user-section">
            <div class="user-section-title">
                <div class="user-title {{ $loop->first ? '' : 'page-break' }}">{{ $username }}</div>
                <table>
                    <thead>
                        <tr style="font-size: 10px">
                            <th>No.</th>
                            <th>Supplier Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($userSuppliers as $index => $supplier)
                            <tr style="font-size: 10px">
                                <td>{{ $index + 1 }}</td>
                                <td class="supplier-name">{{ $supplier->supplier_name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

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
