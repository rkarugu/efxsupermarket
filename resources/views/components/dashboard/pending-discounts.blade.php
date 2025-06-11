<table class="table" id="discounts" style="font-size: 1.1rem">
    <thead class="row-sticky">
        <tr>
            <th>#</th>
            <th>Supplier</th>
            <th>Date</th>
            <th>Demand No</th>
            <th>Reference</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="6">
                <h4><i class="fa fa-spinner fa-spin"></i> Loading...</h4>
            </td>
        </tr>
    </tbody>
</table>
@push('scripts')
    <script>
        $(function() {
            $.ajax({
                url: "{{ route('procurement-dashboard.discounts') }}",
                data: {

                },
                success: function(response) {
                    if (response.discounts.length == 0) {
                        $("#discounts tbody").html('<tr><td colspan="6">No records found</td></tr>')

                        return;
                    }

                    $("#discounts tbody").html('')
                    $.each(response.discounts, function(index, item) {
                        let row = renderDiscountRow(index, item);
                        $("#discounts tbody").append(row)
                    })
                }
            })
        })

        function renderDiscountRow(discount) {
            return `<tr>
                    <td>` + (index + 1) + `</td>
                    <td>` + discount.supplier_name + `</td>
                    <td>` + moment(discount.created_at).format('YYYY-MM-DD') + `</td>
                    <td>` + discount.demand_no + `</td>
                    <td>` + discount.supplier_reference + `</td>
                    <td>` + Number(discount.amount).formaMoney() + `</td>
                </tr>`;
        }
    </script>
@endpush
