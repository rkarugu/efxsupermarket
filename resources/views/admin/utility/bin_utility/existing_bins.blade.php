<div class="box-header with-border">
    <div class="box-header-flex">
    </div>
</div>
<div style="padding:10px;">

    <table class="table table-bordered table-hover table-striped" id="existing_bins_table">
        <thead>
            <tr>
                <th style="width: 15%;">Date</th>
                <th>Item ID</th>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Store Location</th>
                <th>Bin</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($main_existing_bins_data as $item)
                <tr>
                    <td>{{ $item->created_at->format('Y-m-d') }}</td>
                    <td>{{ $item->inventory_id }}</td>
                    <td>{{ $item->item_code }}</td>
                    <td>{{ $item->item_title }}</td>
                    <td>{{ $item->location?->location_name }}</td>
                    <td>{{ $item->bin?->title }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>