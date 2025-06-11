<div style="padding:10px">
    <table class="table table-bordered table-hover" id="dataTableDormant">
        <thead>
            <tr>
                <th>Supplier Code</th>
                <th>Name</th>
                <th>Address</th>
                <th>Email</th>
                <th>Withholding Tax</th>
                <th>Professional Withholding Tax</th>
                <th>Balance</th>
                <th width="15%" class="noneedtoshort">Action</th>
            </tr>
        </thead>
        <tfoot>
            <th class="text-right" colspan="6">Total</th>
            <th class="text-right" id="dormantSupplierTotal"></th>
            <th></th>
        </tfoot>
    </table>
</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#dataTableDormant').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [6, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('maintain-suppliers.datatable') !!}',
                    data: function(data) {
                        data.service = "dormant"
                    }
                },
                columns: [
                    {data: 'supplier_code', name: 'supplier_code', orderable: true},
                    {data: 'name', name: 'name', orderable: true},
                    {data: 'address', name: 'address', orderable: true},
                    {data: 'email', name: 'email', orderable: true},
                    {data: 'withholding_tax', name: 'withholding_tax', orderable: true},
                    {data: 'professional_withholding', name: 'professional_withholding'},
                    {data: 'total_amount_inc_vat', name: 'total_amount_inc_vat', orderable: false},
                    {data: 'action', name: 'action', orderable: false},
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();
                }
            });
        });
    </script>
@endpush
