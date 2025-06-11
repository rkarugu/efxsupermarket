<div style="padding: 10px">
    <div class="row" style="margin-bottom: 15px">
        <div class="col-sm-9">
            <h4>(Stock at Hand = 0, Sales > 0, Time 30 days)</h4>
        </div>
        <div class="col-sm-3">
            <div class="row" style="display: none">
                <label for="missingstocks_location" class="col-sm-3">Location</label>
                <div class="col-sm-9">
                    <select name="location" id="missingstocks_location" class="form-control"
                        @disabled(true)>
                        <option value="">Select Option</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}" @selected($location->id == 46)>
                                {{ $location->location_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-striped" id="missingstocksDataTable">
        <thead>
            <tr>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Category</th>
                <th>Max Stock</th>
                <th>Re-Order Level</th>
                <th>NET SALES</th>
                <th>STOCK AT HAND</th>
                <th>LPO Qty</th>
                <th>Last LPO Date</th>
                <th>Last GRN Date</th>
            </tr>
        </thead>
    </table>
</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            $("#missingstocks_location").change(function() {
                refreshMissingStockTable()
            });

            $("#missingstocksDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [2, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('payment-vouchers.supplier-missingstocks') !!}',
                    data: function(data) {
                        data.location = $("#missingstocks_location").val();
                        data.supplier = "{{ $voucher->supplier->id }}";
                    }
                },
                columns: [{
                        data: 'stock_id_code',
                        name: 'stock_id_code'
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'category_description',
                        name: 'categories.category_description'
                    },
                    {
                        data: 'max_stock',
                        name: 'stock_status.max_stock'
                    },
                    {
                        data: 're_order_level',
                        name: 'stock_status.re_order_level'
                    },
                    {
                        data: 'total_sales',
                        name: 'total_sales',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'qty_on_hand',
                        name: 'qty_on_hand',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'qty_on_order',
                        name: 'qty_on_order',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'last_lpo_date',
                        name: 'last_lpo_date',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'last_grn_date',
                        name: 'last_grn_date',
                        searchable: false,
                        orderable: false,
                    },
                ],
            });
        })

        function refreshMissingStockTable() {
            $("#missingstocksDataTable").DataTable().ajax.reload()
        }
    </script>
@endpush
