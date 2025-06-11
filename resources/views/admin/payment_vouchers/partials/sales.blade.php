<div style="padding: 10px">
    <div class="row" style="margin-bottom: 15px">
        <div class="col-sm-9">
            <h4>Item Sales</h4>
        </div>
        <div class="col-sm-3">
            <div class="row" style="display: none">
                <label for="location" class="col-sm-3">Location</label>
                <div class="col-sm-9">
                    <select name="location" id="sale_location{{ $voucherItem->id }}" class="form-control" @readonly(true)>
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
    <table class="table table-striped" id="itemSalesDataTable{{ $voucherItem->id }}">
        <thead>
            <tr>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Category</th>
                <th>STOCK AT HAND</th>
                <th>Sales Last 7 Days</th>
                <th>Sales Last 30 Days</th>
                <th>Sales Last 60 Days</th>
                <th>Sales Last 90 Days</th>
            </tr>
        </thead>
    </table>
</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            $("#itemSalesDataTable{{ $voucherItem->id }}").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('payment-vouchers.supplier-sales') !!}',
                    data: function(data) {
                        data.location = $("#sale_location{{ $voucherItem->id }}").val();
                        data.supplier = "{{ $voucher->supplier->id }}";
                        data.items = {!! $items !!};
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
                        data: 'category.category_description',
                        name: 'category.category_description'
                    },
                    {
                        data: 'qoh',
                        name: 'qoh',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'sales_7_days',
                        name: 'sales_7_days',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'sales_30_days',
                        name: 'sales_30_days',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'sales_60_days',
                        name: 'sales_60_days',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'sales_90_days',
                        name: 'sales_90_days',
                        orderable: false,
                        searchable: false,
                    },
                ],
            });
        })
    </script>
@endpush
