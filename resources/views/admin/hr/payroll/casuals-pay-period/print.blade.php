@php
    use Carbon\Carbon;
    use Carbon\CarbonPeriod;

    $title = "{$casualsPayPeriod->branch->name} ($casualsPayPeriod->start_date - $casualsPayPeriod->end_date) PAY PERIOD";
    $period = CarbonPeriod::create(Carbon::parse($casualsPayPeriod->start_date), Carbon::parse($casualsPayPeriod->end_date));
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>

    <style>
        body {
            font-size: 13px
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, 
        th,
        td {
            border: 1px solid black;
        }

        th, td {
            padding: 5px;
        }

        th {
            text-align: left;
            text-transform: uppercase
        }

        .approval-info p {
            margin: 0 0 5px 0;
        }

        .strong {
            font-weight: bold;
        }

        .text-right {
            text-align: right
        }
    </style>
</head>
<body>
    <div>
        <h3 class="strong">{{ "{$casualsPayPeriod->branch->name} ($casualsPayPeriod->start_date - $casualsPayPeriod->end_date) PAY PERIOD" }}</h3>
    </div>

    <hr>

    <div class="approval-info">
        <p>
            <span class="strong">INITIAL APPROVER:</span>
            <span style="text-transform: uppercase">{{ $casualsPayPeriod->initialApprover->name }}</span>
        </p>
        <p>
            <span class="strong">INITIAL APPROVAL DATE:</span>
            <span>{{ $casualsPayPeriod->initial_approval_date }}</span>
        </p>
        <p>
            <span class="strong">FINAL APPROVER:</span>
            <span style="text-transform: uppercase">{{ $casualsPayPeriod->finalApprover->name }}</span>
        </p>
        <p>
            <span class="strong">FINAL APPROVAL DATE:</span>
            <span>{{ $casualsPayPeriod->final_approval_date }}</span>
        </p>
    </div>

    <hr style="margin-bottom: 30px">

    <div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>ID</th>
                    <th>Phone</th>
                    @foreach ($period as $date)
                        <th>{{ $date->format('d-m-Y') }}</th>
                    @endforeach
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($casualsPayPeriod->casualsPayPeriodDetails as $i => $payPeriodDetail)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $payPeriodDetail->casual->full_name }}</td>
                        <td>{{ $payPeriodDetail->casual->id_no }}</td>
                        <td>{{ $payPeriodDetail->casual->phone_no }}</td>
                        @foreach ($period as $date)
                            <td style="text-align: center">{{ $payPeriodDetail->dates[$date->format('Y-m-d')] ? 'P' : 'A' }}</td>
                        @endforeach
                        <td class="text-right">{{ number_format($payPeriodDetail->amount, 2) }}</td>
                        <td>{{ strtoupper($payPeriodDetail->disbursement?->call_back_status ?? 'pending') }}</td>
                    </tr>
                @endforeach
                <tr></tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="11" class="text-right">TOTAL</th>
                    <th class="text-right">{{ number_format($casualsPayPeriod->casualsPayPeriodDetails->sum('amount'), 2) }}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>