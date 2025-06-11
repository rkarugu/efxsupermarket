<div style="padding:10px;">
    <div class="row" style="margin-bottom: 10px">
        <form id="filterForm" action="#" method="get">
            <div class="col-sm-8">
                <div class="row">

                </div>
            </div>
        </form>
    </div>
    <hr>
    <table class="table table-bordered table-hover table-striped" id="update_item_bins_table">
        <thead>
            <tr>
                <th style="width: 15%;">Date</th>
                <th>Initiated By</th>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Location</th>
                <th>Previous Bin</th>
                <th>Current Bin</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($query_4 as $log)
                <tr>
                    <td>{{ $log->created_at }}</td>
                    <td>{{ $log?->user?->name }}</td>
                    <td>{{ $log->item?->stock_id_code }}</td>
                    <td>{{ $log->item?->title }}</td>
                    <td>{{ $log->location?->location_name }}</td>
                    <td>{{ $log->newbin?->title }}</td>
                    <td>{{ $log->previousbin?->title }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
    <script></script>
@endpush
