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
    <h2>Unbalanced Invoices</h2>

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
    <br><br>
    <br><br>
    <br>
    <br><br>
    <br>
    <table class="table table-bordered table-condensed" style="width: 100%; float: none;">
        <thead>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Invoice</th>
            <th>Route</th>
            <th>Item Count</th>
            <th>Stock Moves</th>
            <th>Invoice Total</th>
            <th>Debtor Tran Amount</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            <tr>
                <td>{{ $loop -> iteration }}</td>
                <td>{{ $item -> requisition_date }}</td>
                <td>{{ $item->requisition_no }}</td>
                <td>{{ $item->route }}</td>
                <td>{{ $item->get_related_item_count }}</td>
                <td>{{ $item->stock_moves_count }}</td>
                <td>{{ $item->getOrderTotalForEsd()}}</td>
                <td>{{ $item->debtorTrans?->amount }}</td>
            </tr>
        @endforeach

        </tbody>
    </table>
</div>
