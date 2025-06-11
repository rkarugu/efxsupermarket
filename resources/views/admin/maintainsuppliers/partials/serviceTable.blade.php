<div style="padding:10px">
    <table class="table table-bordered table-hover" id="dataTableService">
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
            <th class="text-right" id="servicesSupplierTotal"></th>
            <th></th>
        </tfoot>
    </table>
</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#dataTableService').DataTable({
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
                        data.service = "services"
                    }
                },
                columns: [{
                        data: 'supplier_code',
                        name: 'supplier_code',
                    },
                    {
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'address',
                        name: 'address',
                    },
                    {
                        data: 'email',
                        name: 'email',
                    },
                    {
                        data: 'tax_withhold',
                        name: 'tax_withhold',
                    },
                    {
                        data: 'professional_withholding',
                        name: 'professional_withholding',
                    },
                    {
                        data: 'balance',
                        name: 'transactions.balance',
                        className: 'text-right'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                        width: "95px"
                    },
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#servicesSupplierTotal").html(Number(json.total).formatMoney());
                }
            });
        });
    </script>
@endpush
