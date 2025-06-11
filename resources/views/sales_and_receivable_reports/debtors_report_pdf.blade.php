<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <title>Debtors Report</title>

    <style>
        .w-100 {
            position: relative;
            width: 100%;
        }

        .table {
            font-size: 11px !important;
        }
    </style>
</head>

<body class="antialiased">
    <div id="report-container w-100">
        <div class="d-flex justify-content-center w-100">
            <h5 class="main-title"> KANINI HARAKA ENTERPRISES LTD </h5>
            <h5 class="sub-title"> Debtors Report - {{ $day }} </h5>
        </div>
    </div>

    <table class="table table-hover table-bordered" id="report-table">
        <thead>
            <tr>
                <th style="width: 3%;"> # </th>
                <th style="text-align: left;"> Account </th>
                <th style="text-align: right;"> Opening Balance </th>
                <th style="text-align: right;"> Y Sales </th>
                <th style="text-align: right;"> Total Balance </th>
                <th style="text-align: right;"> Collections </th>
                <th style="text-align: right;"> Returns </th>
                <th style="text-align: right;"> Total Collections </th>
                <th style="text-align: right;"> Discount Returns </th>
                <th style="text-align: right;"> Closing Balance </th>
            </tr>
        </thead>

        <tbody v-cloak>
            @php
                $totals = array_pop($report);
            @endphp
            @foreach ($report as $index => $record)
                <tr>
                    <th style="width: 3%;"> {{ $index + 1 }} </th>
                    <td style="text-align: left;"> {{ $record['account_name'] }}</td>
                    <td style="text-align: right;"> {{ $record['bf'] }}</td>
                    <td style="text-align: right;"> {{ $record['ysales'] }}</td>
                    <td style="text-align: right;"> {{ $record['total_balance'] }}</td>
                    <td style="text-align: right;"> {{ $record['payments'] }}</td>
                    <td style="text-align: right;"> {{ $record['rtns'] }}</td>
                    <td style="text-align: right;"> {{ $record['today_credits'] }}</td>
                    <td style="text-align: right;"> {{ $record['discount_returns'] }}</td>
                    <td style="text-align: right;"> {{ $record['cf'] }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <th colspan="2" style="text-align: left;"> TOTALS </th>
                <th style="text-align: right;"> {{ $totals['bf'] }}</th>
                <th style="text-align: right;"> {{ $totals['ysales'] }}</th>
                <th style="text-align: right;"> {{ $totals['total_balance'] }}</th>
                <th style="text-align: right;"> {{ $totals['payments'] }}</th>
                <th style="text-align: right;"> {{ $totals['rtns'] }}</th>
                <th style="text-align: right;"> {{ $totals['today_credits'] }}</th>
                <th style="text-align: right;"> {{ $totals['discount_returns'] }}</th>
                <th style="text-align: right;"> {{ $totals['cf'] }}</th>
            </tr>
        </tfoot>
    </table>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>
