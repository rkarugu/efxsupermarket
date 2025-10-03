<div style="padding: 10px">
    <form action="<?php echo e(route('maintain-items.update-bin-location')); ?>" method="post" class="submitMe">
        <?php echo csrf_field(); ?>

        <?php
            $authuser = Auth::user();
            $authuserlocation = $authuser->wa_location_and_store_id;
            $isAdmin = $authuser->role_id == 1;
            $hasPermission = isset($permission['maintain-items___view-all-stocks']);
            $hasEditPermission = isset($permission['maintain-items___edit-bin-location']);
        ?>

        <input type="hidden" name="inventory_id" value="<?php echo e($item->id); ?>">
        <input type="hidden" name="stockIdCode" value="<?php echo e($item->stock_id_code); ?>">
        <div class="col-md-12 no-padding-h">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="3%">S.No.</th>
                        <th width="10%">Store Location</th>
                        <th width="10%">Bin Location</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php $__currentLoopData = $bins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($loop->iteration); ?></td>
                            <td><?php echo e(ucfirst($bin->location_name)); ?></td>
                            <td>
                                <select name="uom_id[<?php echo e($bin->id); ?>]" class="form-control"
                                    <?php if(!$isAdmin && !$hasPermission && $bin->id != $authuserlocation): ?> disabled <?php endif; ?>>
                                    <option value="" selected disabled>Select Bin Location</option>
                                    <?php $__currentLoopData = $bin->bin_locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($loc->id); ?>" <?php if($bin->uom_id == $loc->id): echo 'selected'; endif; ?>>
                                            <?php echo e($loc->title); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </tbody>
            </table>
        </div>
        <?php if($isAdmin || $hasEditPermission): ?>
            <button type="submit" class="btn btn-primary">Update Bin Location</button>
            <?php else: ?>
            <p style="color: white">#</p>
        <?php endif; ?>
    </form>
</div>
<?php /**PATH C:\laragon\www\kaninichapchap\resources\views/components/item-centre/bin-location.blade.php ENDPATH**/ ?>