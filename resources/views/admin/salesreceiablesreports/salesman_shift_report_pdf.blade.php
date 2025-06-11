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
    <h2>Summery Customer Statement</h2>

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

        <tr>
            <td style="text-align: right;">
                <b>Closing Balance : {{ number_format($grandTotalInvoices + $grandTotalReceipts) }}</b><br>
            </td>
        </tr>
    </table>
    <table class="table" style="width: 50%; float: left; text-align: left;">
        <tr>
            <td style="font-weight:700;font-size:13px">
                CUSTOMER NAME: {{ ucfirst( @$route->route_name ) }}
            </td>
        </tr>
        <tr>
            <td>
                DATE: {{ date('d/m/Y') }} TIME: {{ date('h:i') }}
            </td>
        </tr>


    </table>
    <br><br>
    <br><br>
    <br>
    <br><br>
    <br>
    <table class="table table-bordered table-condensed" style="width: 100%; float: none;">
        <thead>
        <tr>
            <th>#</th>
            <th>Shift</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Total Invoices</th>
            <th>Total Returns</th>
            <th>Total Discounts</th>
            <th>Receipts During Shift</th>
            <th>Receipts After Shift</th>
            <th>Total Receipts</th>
            <th>Opening Balance</th>
            <th>Closing Balance</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($shiftsData as $index => $shift)
            <tr class="parent-row">
                <td>{{ $index + 1 }}</td>
                <td>{{ $shift['shift_id'] }}</td>
                <td>{{ $shift['start_date'] }}</td>
                <td>{{ $shift['end_date'] }}</td>
                <td>{{ number_format($shift['invoices']) }}</td>
                <td>{{ number_format($shift['returns']) }}</td>
                <td>{{ number_format($shift['discounts']) }}</td>
                <td>{{ number_format($shift['receipts_during_shift']) }}</td>
                <td>{{ number_format($shift['receipts_outside_total']) }}</td>
                <td>{{ number_format($shift['receipts']) }}</td>
                <td>{{ number_format($shift['opening_balance']) }}</td>
                <td>{{ number_format($shift['closing_balance']) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr class="totals">
            <td colspan="5">Grand Totals</td>
            <td>{{ number_format($grandTotalInvoices, 2) }}</td>
            <td>{{ number_format($grandTotalReturns) }}</td>
            <td>{{ number_format($grandTotalDiscounts) }}</td>
            <td>{{ number_format($grandTotalReceiptsDuring) }}</td>
            <td>{{ number_format($grandTotalReceiptsAfter) }}</td>
            <td>{{ number_format($grandTotalReceipts) }}</td>
            <td>{{ number_format($openingBalance) }}</td>
            <td>{{ number_format($openingBalance) }}</td>
        </tr>
        <tr class="totals">
            <td colspan="7">Customer Balance</td>
            <td>{{ number_format($grandTotalInvoices + $grandTotalReturns + $grandTotalReceipts), 2 }}</td>
        </tr>
        </tfoot>
    </table>
</div>
