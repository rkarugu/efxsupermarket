<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sales By Category</title>

    <style>
        .header p,
        .order-info p,
        tbody p {
            margin: 0
        }

        .order-info {
            display: flex;
            flex-direction: row
        }

        .shipping-info table,
        .shipping-info th,
        .shipping-info td {
            border: 1px solid black;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table {
            cellpa
        }

        th, td {
            padding: 5px;
        }

        th {
            text-align: left
        }

        .item-info {
            margin-top: 10px
        }
        
        .item-info tr {
            border-bottom: 1px solid black
        }
        
        .signature-section > p {
            font-weight: bold
        }
    </style>
</head>
<body>
    <div class="header" style="text-align: center">
        <p>SALES BY CATEGORY</p>
        <p>KANINI HARAKA ENTERPRISES LTD.</p>
        <p>PO BOX 5292-01000</p>
        <p>Tel:0716901443 Fax:N/A</p>
        <p>reports@kaniniharaka.co.ke</p>
    </div>

    <hr>

    <div class="order-info">
        <table>
            <tbody>
                <tr>
                    <td>Category: {{ $category }}</td>
                    <td>Start Date: {{ $startDate->format('Y-m-d') }}</td>
                    <td>End Date: {{ $endDate->format('Y-m-d') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px">
        <div class="shipping-info">
            <table style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Supplier Name</th>
                        <th style="text-align: right">Total Sales</th>
                    </tr>
                </thead>
                <tbody style="vertical-align: baseline">
                    @foreach ($salesData as $i => $saleData)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ $saleData->supplier_name }}</td>
                            <td style="text-align: right">{{ number_format($saleData->amount, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <th colspan="2" style="text-align: right">Total</th>
                        <th style="text-align: right">{{ number_format($salesData->sum('amount'), 2) }}</th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>