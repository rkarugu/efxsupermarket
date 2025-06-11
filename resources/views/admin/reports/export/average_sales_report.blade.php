<table style="width:100%">
    <thead>
        <tr>
            <th style="font-weight: bold" colspan="21">Average Sales Report</th>
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
            <th style="font-weight: bold">Bin</th>
            <th style="font-weight: bold">Current Max Stock</th>
            <th style="font-weight: bold">Current Re-Order Level</th>
            <th style="font-weight: bold">Opening Stock</th>
            <th style="font-weight: bold">Purchases</th>
            <th style="font-weight: bold">Transfers In</th>
            <th style="font-weight: bold">Transfers Out</th>
            <th style="font-weight: bold">Sales</th>
            <th style="font-weight: bold">Returns</th>
            <th style="font-weight: bold">Pack Sales</th>
            <th style="font-weight: bold">NET SALES</th>
            <th style="font-weight: bold">STOCK AT HAND</th>
            <th style="font-weight: bold">LPO Qty</th>
            <th style="font-weight: bold">Over Stock</th>
            <th style="font-weight: bold">Suggested Max Stock</th>
            <th style="font-weight: bold">Suggested Reorder Level</th>
            <th style="font-weight: bold">Users</th>
            <th style="font-weight: bold">Suppliers</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $item)
            <tr>
                <td>{{ $item->stock_id_code }}</td>
                <td>{{ $item->title }}</td>
                <td>{{ $item->category->category_description }}</td>
                <td>{{ $item->bin_title }}</td>
                <td>{{ $item->max_stock }}</td>
                <td>{{ $item->re_order_level }}</td>
                <td>{{ $item->opening_stock_count }}</td>
                <td>{{ $item->purchases_count }}</td>
                <td>{{ $item->transfers_in_count }}</td>
                <td>{{ $item->transfers_out_count }}</td>
                <td>{{ $item->excl_total_sales }}</td>
                <td>{{ $item->returns_count }}</td>
                <td>{{ $item->pack_sales }}</td>
                <td>{{ $item->total_sales }}</td>
                <td @style(['background-color:#f80202' => $item->qoh == 0, 'color: #ffffff' => $item->qoh == 0])>{{ $item->qoh }}</td>
                <td>{{ $item->qty_on_order }}</td>
                <td @style(['background-color:#0c7edf' => $item->variance > 0, 'color: #ffffff' => $item->variance > 0])>{{ $item->variance ?? 0 }}</td>
                <td>{{ ceil(1.15 * ($item->total_sales + $item->pack_sales) / $months) ?? 0 }}</td>
                <td>{{ ceil(0.3 * (1.15 * ($item->total_sales + $item->pack_sales) / $months)) }}</td>
                <td>{{ $item->users }}</td>
                <td>{{ $item->suppliers }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
