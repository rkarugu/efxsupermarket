<div style="padding: 10px">
    <form action="<?php echo e(route('maintain-items.update-stock-status')); ?>" method="post" class="submitMe">
        <?php echo csrf_field(); ?>

        <input type="hidden" name="inventory_id" value="<?php echo e($item->id); ?>">
        <input type="hidden" name="stockIdCode" value="<?php echo e($item->stock_id_code); ?>">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th width="1%">S.No.</th>
                    <th width="10%">Store Location</th>
                    <th width="10%">Quantity On Hand</th>
                    <th width="10%">Max Stock</th>
                    <th width="10%">Re-Order Level</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td><?php echo e(ucfirst($location->location_name)); ?></td>
                        <td><?php echo e($isAdmin || $hasPermission || $location->id == $authuserlocation ? $location->qoh : '#####'); ?></td>
                        <td>
                            <input type="text" class="form-control" name="max_stock[<?php echo e($location->id); ?>]"
                                value="<?php echo e($isAdmin || $hasPermission || $location->id == $authuserlocation ? $location->max_stock : '#####'); ?>" 
                                
                                <?php if(!$isAdmin && !$hasPermission && $location->id != $authuserlocation): ?> readonly <?php endif; ?>>
                        </td>
                        <td>
                            <input type="text" class="form-control" name="re_order_level[<?php echo e($location->id); ?>]"
                                value="<?php echo e($isAdmin || $hasPermission || $location->id == $authuserlocation ? $location->re_order_level : '#####'); ?>" 
                                
                                <?php if(!$isAdmin && !$hasPermission && $location->id != $authuserlocation): ?> readonly <?php endif; ?>>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Update Stock Details</button>
    </form>
</div>
<?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/item_centre/partials/stock_status.blade.php ENDPATH**/ ?>