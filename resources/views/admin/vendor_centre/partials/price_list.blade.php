<div style="padding:10px">
    <table class="table table-bordered table-hover" id="priceListDataTable">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Item Code</th>
                <th>Item</th>
                <th>Pack Size</th>
                <th>Price List Cost</th>
                <th>Standard Cost</th>
                <th>Selling Price</th>
                <th>Margin Type</th>
                <th>Margin</th>
            </tr>
        </thead>
    </table>
</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            $("#priceListDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('maintain-suppliers.vendor_centre.price_list', $supplier->id) !!}',
                },
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        sortable: false,
                        width: "80px"
                    },
                    {
                        data: 'stock_id_code',
                        name: 'stock_id_code'
                    },
                    {
                        data: 'title',
                        name: 'wa_inventory_items.title'
                    },
                    {
                        data: 'pack_size.title',
                        name: 'packSize.title'
                    }, {
                        data: 'price_list_cost',
                        name: 'price_list_cost',
                        className: "text-right"
                    },
                    {
                        data: 'standard_cost',
                        name: 'standard_cost',
                        className: "text-right"
                    },
                    {
                        data: 'selling_price',
                        name: 'selling_price',
                        className: "text-right"
                    },
                    {
                        data: 'margin_type',
                        name: 'margin_type',
                    },
                    {
                        data: 'percentage_margin',
                        name: 'percentage_margin',
                        className: "text-right"
                    },
                ],
            });
        })
    </script>
@endpush
