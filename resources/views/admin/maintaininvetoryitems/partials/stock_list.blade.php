<table class="table table-bordered table-hover">
    <thead>
        <tr>

            <th width="30%" >Title</th>
            @foreach ($locations as $loc)
                <th style="text-align:right">{{ $loc->location_name }}</th>
            @endforeach
            <th width="10%" style="text-align:right">Total</th>

        </tr>
    </thead>
    @php
        $t_t_total = 0;
    @endphp
    <tbody>
        @foreach ($items as $item)
            <tr>
                {{-- $item->stock_id_code." -  " . --}}
                <td>{{ $item->title }}</td>
                @php
                    $t_total = 0;
                @endphp
              
                @foreach ($locations as $loc)
                    <td style="text-align:right">{{ manageAmountFormat($type == 'values' ? $item['qty_inhand_' . $loc->id] * $item->selling_price : $item['qty_inhand_' . $loc->id]) }}</td>
                @endforeach
                @foreach ($locations as $loc)
                @php
                    $t_total += $type == 'values' ? $item['qty_inhand_' . $loc->id] * $item->selling_price : $item['qty_inhand_' . $loc->id];
                    $t_t_total += $type == 'values' ? $item['qty_inhand_' . $loc->id] * $item->selling_price : $item['qty_inhand_' . $loc->id];
                @endphp
            @endforeach
            <td style="text-align:right">{{ manageAmountFormat($t_total) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="1" style="text-align:right">Grand Total:</th>
            @foreach ($locations as $loc)
                @php
                    $l_total = 0;
                @endphp
                @foreach ($items as $item)
                    @php
                        $l_total += $type == 'values' ? $item['qty_inhand_' . $loc->id] * $item->selling_price : $item['qty_inhand_' . $loc->id];
                    @endphp
                @endforeach
                <td style="text-align:right">{{ manageAmountFormat($l_total) }}</td>

            @endforeach
            <th style="text-align:right">{{ manageAmountFormat($t_t_total) }}</th>

        </tr>
    </tfoot>
</table>