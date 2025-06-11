<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

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
            padding: 4px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 8px;
        }

        td {
            font-size: 8px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .user-row th {
            text-align: left;
            font-size: 12px;
            background-color: #e0e0e0;
            border: none;
            padding: 6px;
        }

        .table-header {
            font-size: 8px;
        }

        .table-data {
            font-size: 8px;
        }

        tfoot td {
            font-weight: bold;
            background-color: #f2f2f2;
            font-size: 10px;
        }

        .total-p {
            text-align: center;
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

        .table-two-header {
            font-weight: normal;
            font-size: 12px;
            width: 20%;
            text-align: right;
            background-color: #dc3545;
        }

        .table-three-header {
            font-weight: normal;
            font-size: 12px;
            width: 20%;
            text-align: right;
            background-color: #28a745;
        }

        .table-four-header {
            font-weight: normal;
            font-size: 12px;
            width: 20%;
            text-align: right;
            background-color: #00c0ef;
        }

        .p-titles {
            text-align: left;
        }

        /* .p-icon-lock{
            content: "\2193";
        } */

        .p-icon-open {}
    </style>
</head>

<body>

    <div class="header-titles">
        <table class="header-table" style="border-spacing: 0;">
            <tr class="tr-heading">
                <td class="c-heading" style="padding: 2px;">
                    <p style="margin: 2px; margin-left:10px; padding: 0; font-size: 14px;">
                        Kanini Haraka Ltd
                    </p>
                </td>
                <td class="c-heading-date" style="padding: 2px; font-size: 12px;">
                    {{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}
                </td>
            </tr>
        </table>

    </div>

    @php
        $svg = '
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
    <path d="M384 223.1L368 224V144c0-79.41-64.59-144-144-144S80 64.59 80 144V224L64 223.1c-35.35 0-64 28.65-64 64v160c0 35.34 28.65 64 64 64h320c35.35 0 64-28.66 64-64v-160C448 252.7 419.3 223.1 384 223.1zM144 144C144 99.88 179.9 64 224 64s80 35.88 80 80V224h-160V144z"/></svg>';

        $svg_1 = '
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
    <path d="M448 288v160C448 483.3 419.3 512 384 512H64c-35.35 0-64-28.66-64-63.1v-160c0-35.35 28.65-64 64-64L80 224V144C80 64.59 144.6 0 224 0s144 64.59 144 144V160h-64V144C304 99.88 268.1 64 224 64S144 99.88 144 144V224L384 224C419.3 224 448 252.7 448 288z"/></svg>';

        $svg_2 = '
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
    <path d="M336 0c-97.2 0-176 78.8-176 176c0 14.71 2.004 28.93 5.406 42.59l-156 156C3.371 380.6 0 388.8 0 397.3V496C0 504.8 7.164 512 16 512l96 0c8.836 0 16-7.164 16-16v-48h48c8.836 0 16-7.164 16-16v-48h57.37c4.242 0 8.312-1.688 11.31-4.688l32.72-32.72C307.1 349.1 321.3 351.1 336 351.1c97.2 0 176-78.8 176-176S433.2 0 336 0zM376 176c-22.09 0-40-17.91-40-40S353.9 96 376 96S416 113.9 416 136S398.1 176 376 176z"/></svg>';

        $html = '<img src="data:image/svg+xml;base64,' . base64_encode($svg) . '"  width="15" height="15" />';
        $html_1 = '<img src="data:image/svg+xml;base64,' . base64_encode($svg_1) . '"  width="15" height="15" />';
        $html_2 = '<img src="data:image/svg+xml;base64,' . base64_encode($svg_2) . '"  width="15" height="15" />';
    @endphp
    <div class="header-main-tiles">
        <table class="header-table">
            <tr class="tr-heading">
                <td class="table-one-header">
                    <p style="vertical-align: top; margin: 5px; padding: 5px; font-size: 14px;">Trade Agreements</p>
                </td>
                <td class="table-three-header" style="border-radius: 3px; padding: 5px;">
                    <p class="p-titles" style="color: white; margin: 5px; font-size: 12px;">Locked Suppliers
                        <span style="margin-left: 30px; display: inline-block; vertical-align: bottom;">
                            {!! $html !!}
                        </span>
                    </p>
                    <p class="p-titles" style="color: white; margin: 5px; font-size: 12px;">{{ $locked_count }} out of
                        {{ $total_count }}</p>
                </td>
                <td class="table-two-header" style="border-radius: 3px; padding: 5px;">
                    <p class="p-titles" style="color: white; margin: 5px; font-size: 12px;">Open Suppliers
                        <span style="margin-left: 30px; display: inline-block; vertical-align: bottom;">
                            {!! $html_1 !!}
                        </span>
                    </p>
                    <p class="p-titles" style="color: white; margin: 5px; font-size: 12px;">{{ $unlocked_count }} out of
                        {{ $total_count }}</p>
                </td>
                <td class="table-four-header" style="border-radius: 3px; padding: 5px;">
                    <p class="p-titles" style="color: white; margin: 5px; font-size: 12px;">Signed in Portal
                        <span style="margin-left: 30px; display: inline-block; vertical-align: bottom;">
                            {!! $html_2 !!}
                        </span>
                    </p>
                    <p class="p-titles" style="color: white; margin: 5px; font-size: 12px;">
                        {{ $signed_in_portal_count }} out of
                        {{ $total_count }}</p>
                </td>

            </tr>
        </table>
    </div>

    <hr>

    <table>
        <thead>
            <tr class="user-row">
                <th colspan="8">Locked Suppliers</th>
            </tr>
            <tr class="table-header">
                <th style="width: 3%;">#</th>
                <th>REFERENCE</th>
                <th>SUPPLIER CODE</th>
                <th>SUPPLIER NAME</th>
                <th>USER NAME</th>
                <th>DATE</th>
                <th>SIGNED IN PORTAL</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($grouped_trades->get(1, collect()) as $index => $trade)
                <tr class="table-data">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $trade->reference }}</td>
                    <td>{{ optional($trade->supplier)->supplier_code }}</td>
                    <td>{{ optional($trade->supplier)->name }}</td>
                    <td>{{ optional(optional($trade->supplier)->users->first())->name }}</td>
                    <td>{{ $trade->date }}</td>
                    <td style="background-color: {{ !$trade->linked_to_portal ? '#00c0ef' : '#008000' }};">
                        {{ $trade->linked_to_portal ? 'Yes' : 'No' }}
                    </td>
                    <td>
                        @if ($trade->is_locked == 0)
                            Open
                        @elseif ($trade->is_locked == 1)
                            Locked
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="total-p">Totals</td>
                <td>{{ $locked_count }}</td>
            </tr>
        </tfoot>
    </table>

    <table>
        <thead>
            <tr class="user-row">
                <th colspan="8">Open Suppliers</th>
            </tr>
            <tr class="table-header">
                <th style="width: 3%;">#</th>
                <th>REFERENCE</th>
                <th>SUPPLIER CODE</th>
                <th>SUPPLIER NAME</th>
                <th>USER NAME</th>
                <th>DATE</th>
                <th>SIGNED IN PORTAL</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($grouped_trades->get(0, collect()) as $index => $trade)
                <tr class="table-data">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $trade->reference }}</td>
                    <td>{{ optional($trade->supplier)->supplier_code }}</td>
                    <td>{{ optional($trade->supplier)->name }}</td>
                    <td>{{ optional(optional($trade->supplier)->users->first())->name }}</td>
                    <td>{{ $trade->date }}</td>
                    <td style="background-color: {{ !$trade->linked_to_portal ? '#00c0ef' : '#008000' }};">
                        {{ $trade->linked_to_portal ? 'Yes' : 'No' }}
                    </td>
                    <td>
                        @if ($trade->is_locked == 0)
                            Open
                        @elseif ($trade->is_locked == 1)
                            Locked
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="total-p">Totals</td>
                <td>{{ $unlocked_count }}</td>
            </tr>
        </tfoot>
    </table>

    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script('
                $text = __("Page :pageNum of :pageCount", ["pageNum" => $PAGE_NUM, "pageCount" => $PAGE_COUNT]);
                $font = $fontMetrics->get_font("Arial, sans-serif", "normal");
                $size = 9;
                $color = array(0,0,0);
                $word_space = 0.0;
                $char_space = 0.0;
                $angle = 0.0;
                $textWidth = $fontMetrics->getTextWidth($text, $font, $size);
                $x = ($pdf->get_width() - $textWidth) / 1.05;
                $y = $pdf->get_height() - 33;
                $pdf->text($x, $y, $text, $font, $size, $color);
            ');
        }
    </script>

</body>


</html>
