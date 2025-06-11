@php
    $filteredByType = isset($filters['type']);

    function getPettyCashType($type) {
        if ($type == 'travel-order-taking') {
            return 'Order Taking';
        } else if ($type == 'travel-delivery') {
            return 'Travel Delivery';
        } else {
            return '';
        }
    }
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Petty Cash Approvals Summary Log</title>

    <style>
        tbody p {
            margin: 0
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px
        }

        th, td {
            padding: 5px;
        }

        th {
            text-align: left
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        .item-info {
            margin-top: 10px
        }
        
        .item-info tr {
            border-bottom: 1px solid black
        }

        .text-right {
            text-align: right
        }
        
    </style>
</head>
<body>
    <div style="font-weight: bold">
        <p style="text-transform: uppercase">
            @if ($filteredByType)
                {{ getPettyCashType($filters['type']) }}
            @else
                Order Taking and Delivery
            @endif
            Petty Cash Payments
        </p>
        <p>From: <span>{{ $dates[0] }}</span> To: <span>{{ $dates[1] }}</span></p>
    </div>

    <hr>

    <div style="margin-top: 20px">
        <div class="item-info">
            <table>
                <thead>
                    <tr>
                        <th>Approved Date</th>
                        @if(!$filteredByType)
                            <th>Petty Cash Type</th>
                        @endif
                        <th>Total Transactions</th>
                        <th>Approved Amount</th>
                        <th>Disbursed Amount</th>
                        <th>Failed Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groupedLogs as $i => $groupedLog)
                        <tr>
                            <td>{{ $groupedLog->approved_date }}</td>
                            @if(!$filteredByType)
                                <td>{{ getPettyCashType($groupedLog->petty_cash_type) }}</td>
                            @endif
                            <td>{{ $groupedLog->approved_transactions }}</td>
                            <td class="text-right">{{ number_format($groupedLog->approved_amount, 2) }}</td>
                            <td class="text-right">{{ number_format($groupedLog->disbursed_amount ?? 0, 2) }}</td>
                            <td class="text-right">{{ number_format($groupedLog->failed_amount ?? 0, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td @if ($filteredByType) colspan="6" @else colspan="7" @endif></td>
                    </tr>
                    <tr>
                        <th @if ($filteredByType) colspan="2" @else colspan="3" @endif class="text-right">
                            Totals
                        </th>
                        <th class="text-right">{{ number_format($groupedLogs->sum('approved_amount') , 2) }}</th>
                        <th class="text-right">{{ number_format($groupedLogs->sum('disbursed_amount') , 2) }}</th>
                        <th class="text-right">{{ number_format($groupedLogs->sum('failed_amount') , 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
</body>
</html>