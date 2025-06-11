<div id="supplierBalances">
    <table class="table" style="font-size: 1.1rem">
        <thead class="row-sticky">
            <tr>
                <th style="width: 15px">#</th>
                <th>Supplier Name</th>
                <th class="text-right">Balance</th>
                <th class="text-right">Pending GRNs</th>
                <th class="text-right">Stock Value</th>
                <th class="text-right">Payable Amount</th>
                <th class="text-right">Processing Amount</th>
                <th class="text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td id="loading" colspan="8">
                    <h4><i class="fa fa-spinner fa-spin"></i> Loading...</h4>
                </td>
            </tr>
        </tbody>
        <tfoot>
        </tfoot>
    </table>
</div>
@push('scripts')
    <script>
        $(function() {
            $.ajax({
                url: "{{ route('procurement-dashboard.supplier-balances') }}",
                data: {
                    location: $("#store").val()
                },
                success: function(response) {
                    $("#loading").remove();
                    $("#printSupplierBalances").show();

                    $.each(response.suppliers, function(index, supplier) {
                        let row = renderBalances(index, supplier);
                        $("#supplierBalances .table tbody").append(row);
                    });

                    let row = renderTotals(response.totals);
                    $("#supplierBalances .table tfoot").append(row);
                }
            })
        })

        function renderTotals(totals) {
            return `<tr>
                <th colspan="5" class="text-right">Totals</th>
                <th class="text-right">` + Number(totals.to_pay).formatMoney() + `</th>
                <th class="text-right">` + Number(totals.processing_amount).formatMoney() + `</th>
                <th class="text-right">` + Number(totals.variance).formatMoney() + `</th>
            </tr>`;
        }

        function renderBalances(index, supplier) {
            return `<tr>
                    <td>` + (index + 1) + `</td>
                    <td>` + supplier.name + `</td>
                    <td class="text-right">` + Number(supplier.balance).formatMoney() + `</td>
                    <td class="text-right">` + Number(supplier.grn_value).formatMoney() + `</td>
                    <td class="text-right">` + Number(supplier.stock_value).formatMoney() + `</td>
                    <td class="text-right">` + Number(supplier.to_pay).formatMoney() + `</td>
                    <td class="text-right">` + Number(supplier.processing_amount).formatMoney() + `</td>
                    <td class="text-right">` + Number(supplier.variance).formatMoney() + `</td>
                </tr>`;
        }
    </script>
@endpush
