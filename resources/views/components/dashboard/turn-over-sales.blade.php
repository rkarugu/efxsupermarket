<div id="secondarySales">
    <table class="table table-bordered" style="font-size: 1.1rem">
        <thead class="row-sticky">
            <tr>
                <th style="width: 3%;"> #</th>
                <th>Supplier</th>
                <th class="text-right">Current</th>
                <th class="text-right">Last Month</th>
                <th class="text-right">Last 90 Days</th>
                <th class="text-right">This Year</th>
                <th class="text-right">Last Year</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="7">
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
                url: "{{ route('procurement-dashboard.turnover-sales') }}",
                data: {

                },
                success: function(response) {
                    if (response.sales.length == 0) {
                        $("#secondarySales tbody").html('<tr><td colspan="7">No records found</td></tr>')

                        return;
                    }

                    $("#printSecondarySales").show();

                    $("#secondarySales tbody").html('')
                    $.each(response.sales, function(index, item) {
                        let row = renderSecondarySalesRow(index, item);
                        $("#secondarySales tbody").append(row)
                    })

                    let row = renderSecondarySalesTotals(response.totals);
                    $("#secondarySales tfoot").append(row);
                }
            })
        })

        function renderSecondarySalesTotals(totals) {
            return `<tr>
                <th colspan="5" class="text-right">Totals</th>
                <th class="text-right">` + Number(totals.current_year).formatMoney() + `</th>
                <th class="text-right">` + Number(totals.last_year).formatMoney() + `</th>
            </tr>`;
        }

        function renderSecondarySalesRow(index, purchase) {
            return `<tr>
                    <td>` + (index + 1) + `</td>
                    <td>` + purchase.supplier_name + `</td>
                    <td class="text-right">` + Number(purchase.current_month).formatMoney() + `</td>
                    <td class="text-right">` + Number(purchase.last_month).formatMoney() + `</td>
                    <td class="text-right">` + Number(purchase.last_90).formatMoney() + `</td>
                    <td class="text-right">` + Number(purchase.current_year).formatMoney() + `</td>
                    <td class="text-right">` + Number(purchase.last_year).formatMoney() + `</td>
                </tr>`;
        }
    </script>
@endpush
