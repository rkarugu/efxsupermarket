<table class="table table-bordered" id="returns" style="font-size: 1.1rem">
    <thead class="row-sticky">
        <tr>
            <th style="width: 3%;"> #</th>
            <th>Supplier</th>
            <th>Date</th>
            <th>Demand No.</th>
            <th>Type</th>
            <th>Document No.</th>
            <th class="text-right">Amount</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="7">
                <h4><i class="fa fa-spinner fa-spin"></i> Loading...</h4>
            </td>
        </tr>
    </tbody>
</table>
@push('scripts')
    <script>
        $(function() {
            $.ajax({
                url: "{{ route('procurement-dashboard.returns') }}",
                data: {

                },
                success: function(response) {
                    if (response.demands.length == 0) {
                        $("#returns tbody").html('<tr><td colspan="7">No records found</td></tr>')

                        return;
                    }

                    $("#returns tbody").html('')
                    $.each(response.demands, function(index, item) {
                        let row = renderDemandRow(index, item);
                        $("#returns tbody").append(row)
                    })
                }
            })
        })

        function renderDemandRow(index, demand) {
            return `<tr>
                    <td>` + (index + 1) + `</td>
                    <td>` + demand.supplier_name + `</td>
                    <td>` + moment(demand.created_at).format('YYYY-MM-DD') + `</td>
                    <td>` + demand.demand_no + `</td>
                    <td>` + demand.type + `</td>
                    <td>` + demand.document_no + `</td>
                    <td class="text-right">` + Number(demand.amount).formatMoney() + `</td>
                </tr>`;
        }
    </script>
@endpush
