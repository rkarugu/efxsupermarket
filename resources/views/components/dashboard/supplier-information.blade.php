<div id="supplierInformation">
    <table class="table" style="font-size: 1.1rem">
        <thead class="row-sticky">
            <tr>
                <th style="width: 15px">#</th>
                <th>Supplier Name</th>
                <th class="text-right">Pending GRNs</th>
                <th class="text-right">Unpaid Invoices</th>
                <th class="text-right">Missing Items</th>
                <th class="text-right">Reorder Items</th>
                <th class="text-right">Overstock Items</th>
                <th class="text-right">Deadstock Items</th>
                <th class="text-right">Slow Moving Items</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td id="supplierInfoloading" colspan="9">
                    <h4><i class="fa fa-spinner fa-spin"></i> Loading...</h4>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@push('scripts')
    <script>
        $(function() {
            $.ajax({
                url: "{{ route('procurement-dashboard.supplier-information') }}",
                data: {
                    store: $("#store").val()
                },
                success: function(response) {
                    $("#supplierInfoloading").remove();
                    $("#printSupplierInformation").show();

                    $.each(response.suppliers, function(index, supplier) {
                        let row = renderSupplierInformation(index, supplier);
                        $("#supplierInformation .table tbody").append(row);
                    });
                }
            })
        })

        function renderSupplierInformation(index, supplier)
        {
            return `<tr>
                    <td>` + (index + 1) + `</td>
                    <td>` + supplier.name + `</td>
                    <td class="text-right">` + Number(supplier.pending_grn_count).formatMoney() + `</td>
                    <td class="text-right">` + Number(supplier.pending_invoice_count).formatMoney() + `</td>
                    <td class="text-right">` + Number(supplier.missing_items_count).formatMoney() + `</td>
                    <td class="text-right">` + Number(supplier.reorder_items_count).formatMoney() + `</td>
                    <td class="text-right">` + Number(supplier.over_stock_items_count).formatMoney() + `</td>
                    <td class="text-right">` + Number(supplier.dead_stock_items_count).formatMoney() + `</td>
                    <td class="text-right">` + Number(supplier.slow_moving_items_count).formatMoney() + `</td>
                </tr>`;
        }
    </script>
@endpush
