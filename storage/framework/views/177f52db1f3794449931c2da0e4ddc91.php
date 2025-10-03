<div style="padding:10px">
    <table class="table table-bordered table-hover" id="dataTableGoods">
        <thead>
            <tr>
                <th>Supplier Code</th>
                <th>Name</th>
                <th>Address</th>
                <th>Email</th>
                <th>Withholding Tax</th>
                <th>Balance</th>
                <th width="15%" class="noneedtoshort">Action</th>
            </tr>
        </thead>

        <tfoot>
            <th colspan="5" class="text-right">Total</th>
            <th class="text-right" id="goodsSupplierTotal"></th>
            <th></th>
        </tfoot>
    </table>
</div>
<?php $__env->startPush('scripts'); ?>
    <script>
        $(document).ready(function() {
            $("#dataTableGoods").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [5, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '<?php echo route('maintain-suppliers.datatable'); ?>',
                    data: function(data) {
                        data.service = "goods"
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

                    $("#goodsSupplierTotal").html(Number(json.total).formatMoney());
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/maintainsuppliers/partials/goodsTable.blade.php ENDPATH**/ ?>