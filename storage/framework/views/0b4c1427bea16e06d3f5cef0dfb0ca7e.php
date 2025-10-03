<div style="padding: 10px">
    <div class="row">
        <div class="col-sm-3">
            <div class="form-group">
                <label for="supplierHistory">Supplier</label>
                <select name="supplier" id="supplierHistory" class="form-control">
                    <option value="">Select Option</option>
                    <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($supplier->id); ?>">
                            <?php echo e($supplier->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
    </div>
    <table class="table table-bordered table-hover" id="priceChangeDataTable">
        <thead>
            <tr>
                <th>Old Standard Cost</th>
                <th>Standard Cost</th>
                <th>Old Selling Price</th>
                <th>Selling Price</th>
                <th>Block?</th>
                <th>Status</th>
                <th>Initiator</th>
                <th>Approver</th>
                <th>Updated AT</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<?php $__env->startPush('scripts'); ?>
    <script>
        $(function() {
            // Safe Select2 initialization
            try {
                if ($("#supplierHistory").length) {
                    $("#supplierHistory").select2();
                }
            } catch (e) {
                console.warn('Select2 initialization failed for #supplierHistory:', e);
            }
            
            $("#supplierHistory").change(function() {
                refreshTable($("#priceChangeDataTable"));
            });

            $("#priceChangeDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [8, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '<?php echo route('item-centre.price-change-history', $item); ?>',
                    data: function(data) {
                        data.supplier = $("#supplierHistory").val();
                    }
                },
                columns: [{
                    data: 'old_standard_cost',
                    name: 'old_standard_cost'
                }, {
                    data: 'standard_cost',
                    name: 'standard_cost'
                }, {
                    data: 'old_selling_price',
                    name: 'old_selling_price'
                }, {
                    data: 'selling_price',
                    name: 'selling_price'
                }, {
                    data: 'block_this',
                    name: 'block_this'
                }, {
                    data: 'status',
                    name: 'status'
                }, {
                    data: 'creator.name',
                    name: 'creator.name',
                    render: function(data, type, row) {
                        return data ? data : 'N/A'; 
                    }
                }, {
                    data: 'approver.name',
                    name: 'approver.name',
                    render: function(data, type, row) {
                        return data ? data : 'N/A'; 
                    }
                }, {
                    data: 'updated_at',
                    name: 'updated_at',
                }, ],
            });
        })
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\laragon\www\kaninichapchap\resources\views/components/item-centre/price-change-history.blade.php ENDPATH**/ ?>