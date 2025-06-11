
<table class="table table-bordered table-hover">
    <thead>
        <tr>

            <th width="10%">Stock ID Code</th>
            <th width="10%">Title</th>
            <th width="10%">Total</th>
            @foreach ($locations as $loc)
                <th>{{ $loc->location_name }}</th>
            @endforeach
        </tr>
    </thead>
    @php
        $t_t_total = 0;
    @endphp
    <tbody>
        @foreach ($items as $item)
            <tr>
                <td>{{ $item['stock_id_code'] }}</td>
                <td>{{ $item['title'] }}</td>
                @php
                    $t_total = 0;
                @endphp
                @foreach ($locations as $loc)
                    @php
                        $t_total += 
                        $type == 'values' ? $item['qty_inhand_' . $loc->id] * $item['selling_price'] : $item['qty_inhand_' . $loc->id];
                        $t_t_total += $type == 'values' ? $item['qty_inhand_' . $loc->id] * $item['selling_price'] : $item['qty_inhand_' . $loc->id];
                    @endphp
                @endforeach
                <td>{{ manageAmountFormat($t_total) }}</td>
                @foreach ($locations as $loc)
                    <td>{{ manageAmountFormat($type == 'values' ? $item['qty_inhand_' . $loc->id] * $item['selling_price'] : $item['qty_inhand_' . $loc->id]) }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2" style="text-align:right">Grand Total:</th>
            <th>{{ manageAmountFormat($t_t_total) }}</th>
            @foreach ($locations as $loc)
                @php
                    $l_total = 0;
                @endphp
                @foreach ($items as $item)
                    @php
                        $l_total += $type == 'values' ? $item['qty_inhand_' . $loc->id] * $item['selling_price'] : $item['qty_inhand_' . $loc->id];
                    @endphp
                @endforeach
                <td>{{ manageAmountFormat($l_total) }}</td>
            @endforeach
        </tr>
    </tfoot>
</table>