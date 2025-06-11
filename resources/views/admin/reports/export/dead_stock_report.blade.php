<table style="width:100%">
    <thead>
        <tr>
            <th style="font-weight: bold" colspan="21">Dead Stock Report</th>
        </tr>
        <tr>
            <th style="font-weight: bold">Duration</th>
            <th style="font-weight: bold" colspan="20">{{ $date_range }}</th>
        </tr>
        <tr>
            <th style="font-weight: bold">Store</th>
            <th style="font-weight: bold" colspan="20">{{ $store }}</th>
        </tr>
        <tr>
            <th colspan="21"></th>
        </tr>
        <tr>
            <th style="font-weight: bold">Item Code</th>
            <th style="font-weight: bold">Item Name</th>
            <th style="font-weight: bold">Category</th>
            <th style="font-weight: bold">Current Max Stock</th>
            <th style="font-weight: bold">Current Re-Order Level</th>
            <th style="font-weight: bold">Opening Stock</th>
            <th style="font-weight: bold">Purchases</th>
            {{-- <th style="font-weight: bold">Transfers In</th>
            <th style="font-weight: bold">Transfers Out</th> --}}
            <th style="font-weight: bold">Sales</th>
            <th style="font-weight: bold">Returns</th>
            <th style="font-weight: bold">Pack Sales</th>
            <th style="font-weight: bold">NET SALES</th>
            <th style="font-weight: bold">STOCK AT HAND</th>
            <th style="font-weight: bold">Qty to Order</th>
            <th style="font-weight: bold">LPO Qty</th>
            <th style="font-weight: bold">Last LPO Date</th>
            <th style="font-weight: bold">Last GRN Date</th>
            <th style="font-weight: bold">Last Sales Date</th>
            <th style="font-weight: bold">Users</th>
            <th style="font-weight: bold">Suppliers</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $item)
            <tr>
                <td>{{ $item->stock_id_code }}</td>
                <td>{{ $item->title }}</td>
                <td>{{ $item->category }}</td>
                <td>{{ $item->max_stock }}</td>
                <td>{{ $item->re_order_level }}</td>
                <td>{{ $item->opening_stock_count }}</td>
                <td>{{ $item->purchases_count }}</td>
                {{-- <td>{{ $item->transfers_in_count }}</td>
                <td>{{ $item->transfers_out_count }}</td> --}}
                <td>{{ $item->excl_total_sales }}</td>
                <td>{{ $item->returns_count }}</td>
                <td>{{ $item->pack_sales }}</td>
                <td>{{ $item->total_sales }}</td>
                <td @style(['background-color:#f80202' => $item->qoh == 0, 'color: #ffffff' => $item->qoh == 0])>{{ $item->qoh }}</td>
                <td @style(['background-color:#0c7edf' => $item->variance > 0, 'color: #ffffff' => $item->variance > 0])>{{ $item->variance ?? 0 }}</td>
                <td>{{ $item->qty_on_order }}</td>
                <td>{{ $item->last_lpo_date ? Illuminate\Support\Carbon::parse($item->last_lpo_date)->format('d/m/Y') : '' }}</td>
                <td>{{ $item->last_grn_date ? Illuminate\Support\Carbon::parse($item->last_grn_date)->format('d/m/Y') : '' }}</td>
                <td>{{ $item->last_sales_date ? Illuminate\Support\Carbon::parse($item->last_sales_date)->format('d/m/Y') : '' }}</td>
                <td>{{ $item->user_names }}</td>
                <td>{{ $item->supplier_names }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
