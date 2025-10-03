<div style="padding:10px">
    <div class="row">
        <div class="col-md-9">
        </div>
        <div class="col-sm-3">
            <?php if(can('route-pricing', 'maintain-items')): ?>
                    <div align="right" class="form-group">
                        <a href="<?php echo route('route.pricing.create' , $inventoryItem->id); ?>" class="btn btn-success btn-sm">Add pricing</a>
                    </div>
            <?php endif; ?>
        </div>
    </div>

   

        <table class="table table-bordered table-hover" id="create_datatable">
            <thead>
            <tr>
                <th width="3%">#</th>
                <th>Created At</th>
                <th>Branch</th>
                <th>Routes</th>
                <th>Price</th>
                <th>Route Price</th>
                <th>Created By</th>
                <th >Flash/Non Flash</th>
                <th >Status</th>
                <th >Action</th>
                
            </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $routePricing; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pricing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($loop->index + 1); ?></td>
                    <td><?php echo e($pricing->created_at); ?></td>
                    <td><?php echo e($pricing->restaurant?->name); ?></td>
                    <td><?php $__currentLoopData = $pricing->getRoutesAttribute(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <?php echo e($route->route_name . ',   '); ?> 

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </td>
                    <td style="text-align: right"><?php echo e(number_format($pricing->getInventoryItemDetails?->selling_price, 2)); ?></td>
                    <td style="text-align: right"><?php echo e($pricing->price); ?></td>
                    <td><?php echo e($pricing->createdBy?->name); ?></td>
                    <td><?php echo e(($pricing->is_flash == 1) ? 'Flash' : 'Non Flash'); ?></td>
                    <td><?php echo e(($pricing->status == 0) ? 'Active' : 'Inactive'); ?></td>
                    <td>
                        <div class="action-button-div">
                            <a href="<?php echo e(route('route.pricing.edit',[$inventoryItem->id, $pricing->id])); ?>"><i class="fas fa-pen" title="edit"></i></a>
                            

                        </div>
                    </td>
                </tr>
                    
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
             
            </tbody>

        </table>
    </div>
</div><?php /**PATH C:\laragon\www\kaninichapchap\resources\views/components/item-centre/route-pricing-component.blade.php ENDPATH**/ ?>