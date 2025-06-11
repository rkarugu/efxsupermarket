<div style="padding: 10px;">
    <div class="table-responsive">
        <table class="table table-bordered" id="returnsDataTable">
            <thead>
                <tr>
                    <th style="width: 3%;">#</th>
                    <th>Return No</th>
                    <th>Return Date</th>
                    <th>Item</th>
                    <th>GRN No</th>
                    <th>Date Received</th>
                    <th>Qty Received</th>
                    <th>Qty Returned</th>
                    <th>Reason</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            $("#returnsDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('maintain-suppliers.vendor_centre.returns', $supplier->id) !!}',
                },
                columns: [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    sortable: false,
                    width: "80px"
                }, {
                    data: 'return_number',
                    name: 'return_number'
                }, {
                    data: 'created_at',
                    name: 'created_at'
                }, {
                    data: 'grn.item_code',
                    name: 'grn.item_code'
                }, {
                    data: 'grn.grn_number',
                    name: 'grn.grn_number'
                }, {
                    data: 'grn.created_at',
                    name: 'grn.created_at'
                }, {
                    data: 'grn.qty_received',
                    name: 'grn.qty_received'
                }, {
                    data: 'returned_quantity',
                    name: 'returned_quantity'
                }, {
                    data: 'reason',
                    name: 'reason'
                }, ],
            });
        })
    </script>
@endpush
