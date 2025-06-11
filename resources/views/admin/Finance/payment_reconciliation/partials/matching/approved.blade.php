<div class="table-responsive"  style="padding:10px">     
    <table class="table table-bordered" id="approvedMatchingTable">
        <thead>
            <tr>
                <th>Route</th>
                <th>Channel</th>
                <th>Trans Date</th>
                <th>Doc. No</th>
                <th>Reference</th>
                <th>Bank Ref</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align: right;padding-right:10px;"><b>Total</b></td>
                <td><b id="approvedMatchingTotal"></b></td>
            </tr>
        </tfoot>
    </table>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            $("#approvedMatchingTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('payment-reconciliation.verification.matching.datatable',$payment->id) !!}',
                    data: function(data) {
                        data.type = 'approved';
                    }
                },
                columns: [
                    {
                        data: 'debtor.customer_detail.customer_name',
                        name: 'debtor.customerDetail.customer_name'
                    },
                    {
                        data: 'debtor.channel',
                        name: 'debtor.channel'
                    },
                    {
                        data: 'debtor.trans_date',
                        name: 'debtor.trans_date'
                    },
                    {
                        data: 'document_no',
                        name: 'document_no'
                    },
                    {
                        data: 'reference',
                        name: 'reference'
                    },
                    {
                        data: 'bank_verification.reference',
                        name: 'bankVerification.reference'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#approvedMatchingTotal").text(json.total);
                }
            });
        });
    </script>
@endpush