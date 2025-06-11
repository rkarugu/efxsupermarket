<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $return->rfs_no }}</title>

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
        <p>RETURN FROM STORE</p>
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
                    <td>RFS No: {{ $return->rfs_no }}</td>
                    <td>Date Created: {{ date_format(date_create($return->created_at), 'd/m/Y') }}</td>
                    <td>Date Placed: {{ date_format(date_create($return->created_at), 'd/m/Y') }}</td>
                    <td>Placed By: <span style="text-transform: uppercase">{{ $return->user->name }}</span></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px">
        <div class="shipping-info">
            <table style="width: 100%">
                <thead>
                    <tr>
                        <th>FROM</th>
                        <th>SHIP TO</th>
                    </tr>
                </thead>
                <tbody style="vertical-align: baseline">
                    <tr>
                        <td>
                            <p>KANINI HARAKA ENTERPRISES LTD</p>
                            <p>{{ $return->location->location_name }}</p>
                        </td>
                        <td>
                            <p>{{ $return->supplier->name }}</p>
                            <p>{{ $return->supplier->address ?? 'N/A' }}</p>
                            <p>{{ $return->supplier->telephone ?? 'N/A' }}</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="item-info">
            <table>
                <thead>
                    <tr>
                        <th>Item Code</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Weight (Kg)</th>
                        <th>Cost</th>
                        <th>Total Cost</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($return->storeReturnItems as $item)
                        <tr>
                            <td>{{ $item->inventoryItem->stock_id_code }}</td>
                            <td>{{ $item->inventoryItem->description }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->weight }}</td>
                            <td>{{ number_format($item->cost, 2) }}</td>
                            <td style="text-align: right">{{ number_format($item->total_cost, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr></tr>
                    <tr>
                        <th colspan="3" style="text-align: right">Totals</th>
                        <td>{{ number_format($return->storeReturnItems->sum('weight'), 2) }} (Kg)</td>
                        <td></td>
                        <td style="text-align: right">KES {{ number_format($return->storeReturnItems->sum('total_cost'), 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div>
            <p>Total Items: {{ $return->storeReturnItems->count() }}</p>
        </div>

        <div>
            <p>REMARKS</p>
            <P style="border-bottom: 1px solid black; padding-bottom: 20px"></P>
        </div>

        <div class="signature-section">
            <p>PREPARED BY:</p>
            <table style="table-layout: fixed">
                <tr style="text-align: left">
                    <td>NAME: {{ $return->user->name }}</td>
                    <td>SIGN: </td>
                    <td>DATE: {{ date_format(date_create($return->created_at), 'd/m/Y') }}</td>
                </tr>
            </table>

            <p>APPROVED BY:</p>
            <table style="table-layout: fixed">
                <tr style="text-align: left">
                    <td>NAME: {{ $return->approvedBy?->name }}</td>
                    <td>SIGN: </td>
                    <td>DATE: {{ date_format(date_create($return->approved_date), 'd/m/Y') }}</td>
                </tr>
            </table>

            <p>AUTHORISED BY:</p>
            <table style="table-layout: fixed">
                <tr style="text-align: left">
                    <td>NAME: </td>
                    <td>SIGN: </td>
                    <td>DATE: </td>
                </tr>
            </table>

        </div>
    </div>
</body>
</html>