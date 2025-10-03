<div style="padding: 10px">
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th width="3%">S.No.</th>
                <th>Supplier</th>
                <th>Price</th>
                <th>Cost Per Our Unit</th>
                <th>Currency</th>
                <th>Effective From</th>
                <th>Preferred</th>
                <th class="noneedtoshort">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $item_suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($loop->iteration); ?></td>
                    <td><?php echo e($list->supplier?->supplier_code); ?></td>
                    <td><?php echo e($list->price); ?></td>
                    <td><?php echo e($list->our_unit_of_measure); ?></td>
                    <td><?php echo e($list->currency); ?></td>
                    <td><?php echo e($list->price_effective_from); ?></td>
                    <td><?php echo e($list->preferred_supplier); ?></td>
                    <td style="display:flex">
                        <?php echo buttonHtmlCustom('edit', route('maintain-items.purchaseDataEdit', ['stockid' => encrypt($inventoryItem->id), 'itemid' => encrypt($list->id)])); ?>

                        <?php echo buttonHtmlCustom('delete', route('maintain-items.purchaseDataDelete', ['stockid' => encrypt($inventoryItem->id), 'itemid' => encrypt($list->id)])); ?>

                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php if($suppliers->count()): ?>
        <form action="<?php echo e(route('maintain-items.purchaseDataAdd', ['stockid' => $inventoryItem->id])); ?>" method="get">
            <input type="hidden" name="stockid" value="<?php echo e($inventoryItem->id); ?>">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="3%">S.No.</th>
                        <th>Supplier Code</th>
                        <th>Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($loop->iteration); ?></td>
                            <td><input type="submit" name="supplier_code" value="<?php echo e($supplier->supplier_code); ?>"
                                    class="btn btn-primary btn-sm"></td>
                            <td><?php echo e($supplier->name); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </form>
    <?php else: ?>
        <center><b>No Supplier found</b></center>
    <?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\kaninichapchap\resources\views/components/item-centre/purchase-data.blade.php ENDPATH**/ ?>