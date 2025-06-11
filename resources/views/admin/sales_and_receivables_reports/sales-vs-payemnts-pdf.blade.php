<style>
    table {
        border-collapse: collapse;
        width: 100%;
        font-size: 11px;
        font-family: times new roman;
    }

    /*tr {*/
    /*    border-bottom: 1px solid #000000;*/
    /*}*/

    td,
    th {
        text-align: left;
        padding: 2px 2px;
    }

    th,
    .totals td {
        font-weight: bold;
    }
</style>

<div style="width: 100%;padding-bottom: 30px;margin: auto;" class="clearfix" id="div_content">
    <h2>Sales Vs Payments Report</h2>

    <table class="table" style="width: 50%;">
        <tr>
            <td style="text-align: left;">
                <?php $cn = 0; ?>
                <?php $all_settings = getAllSettings(); ?>
                {{ $all_settings['COMPANY_NAME'] }}</b></span><br>
                {{ $all_settings['ADDRESS_1'] }}<br>
                {{ $all_settings['ADDRESS_2'] }}<br>
                {{ $all_settings['ADDRESS_3'] }}<br>
                Tel: {{ $all_settings['PHONE_NUMBER'] }}<br>
                {{ $all_settings['EMAILS'] }}<br>
                {{ $all_settings['WEBSITE'] }}<br>
                Pin No: {{ $all_settings['PIN_NO'] }}<br><br>
            </td>
        </tr>

    </table>
    <br>
    @isset($salesAndPayments)
        <table class="table table-bordered table-hover">
            <thead>
            <tr style="border-bottom: solid 1px black">
                <th style="width: 3%;">#</th>
                <th>DATE</th>
                <th>Day</th>
                <th>SALES</th>
                <th>RETURNS</th>
                <th>PAYMENTS NEXT DAY</th>
                <th>BALANCE</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($salesAndPayments as $item)
                <tr style="border-bottom: solid 1px black">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->sales_date }}</td>
                    <td>{{  date('l', strtotime($item->sales_date)) }}</td>
                    <td>{{ number_format($item->total_sales, 2) }}</td>
                    <td>{{ number_format(abs($item->total_returns), 2) }}</td>
                    <td>{{number_format( abs($item->total_payments_following_day), 2) }}</td>
                    <td>{{ number_format($item->total_payments_following_day + $item->total_returns + $item->total_sales,2)  }}</td>

                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr style="font-weight:bolder; border-bottom: solid 1px black">
                <td colspan="3" style="text-align: right; font-weight: bold;">Grand Total</td>
                <td>{{ number_format($salesAndPayments->sum('total_sales'), 2) }}</td>
                <td>{{ number_format(abs($salesAndPayments->sum('total_returns')), 2) }}</td>
                <td>{{ number_format(abs($salesAndPayments->sum('total_payments_following_day')), 2) }}</td>
                <td>{{ number_format($salesAndPayments->sum('total_sales')+ $salesAndPayments->sum('total_returns') + $salesAndPayments->sum('total_payments_following_day'), 2)  }}</td>
            </tr>
            </tfoot>
        </table>
    @endisset
</div>
