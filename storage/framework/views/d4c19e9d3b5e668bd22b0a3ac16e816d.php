<?php if($supplier->is_verified): ?>
    <?php if(can('edit', 'maintain-suppliers')): ?>
        <span class='span-action'> <a title='Edit'
                href="<?php echo e(route('maintain-suppliers.edit', $supplier->supplier_code)); ?>">
                <img src="<?php echo e(asset('assets/admin/images/edit.png')); ?>"></a></span>
    <?php endif; ?>
    <?php if(can('vendor-centre', 'maintain-suppliers')): ?>
        <span class='span-action'>
            <a title="Vendor centre" href="<?php echo e(route('maintain-suppliers.vendor_centre', $supplier->supplier_code)); ?>"><i
                    class="fa fa-store"></i></a>
        </span>
    <?php endif; ?>    
    <?php if(can('delete', 'maintain-suppliers') && $supplier->canBeDeleted()): ?>
        <?php if (isset($component)) { $__componentOriginal5939e55ed2bbc3dac4f97a813ab2b143 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5939e55ed2bbc3dac4f97a813ab2b143 = $attributes; } ?>
<?php $component = App\View\Components\Actions\DeleteRecord::resolve(['action' => ''.e(route('maintain-suppliers.destroy', $supplier->id)).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('actions.delete-record'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Actions\DeleteRecord::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['identifier' => 'supp'.e($supplier->id).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5939e55ed2bbc3dac4f97a813ab2b143)): ?>
<?php $attributes = $__attributesOriginal5939e55ed2bbc3dac4f97a813ab2b143; ?>
<?php unset($__attributesOriginal5939e55ed2bbc3dac4f97a813ab2b143); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5939e55ed2bbc3dac4f97a813ab2b143)): ?>
<?php $component = $__componentOriginal5939e55ed2bbc3dac4f97a813ab2b143; ?>
<?php unset($__componentOriginal5939e55ed2bbc3dac4f97a813ab2b143); ?>
<?php endif; ?>
    <?php endif; ?>
<?php else: ?>
    <?php if(can('show', 'maintain-suppliers')): ?>
        <span class='span-action'>
            <a title='Show' href="<?php echo e(route('maintain-suppliers.show', $supplier)); ?>">
                <i class='fa fa-eye'></i></a>
        </span>
    <?php endif; ?>
    <span class='span-action'>
        <a data-toggle="modal" href="#modal-id<?php echo e($supplier->id); ?>">
            <i class="fa fa-check-circle text-success fa-lg" aria-hidden="true"></i>
        </a>
    </span>
    <div class="modal fade" id="modal-id<?php echo e($supplier->id); ?>">
        <div class="modal-dialog">
            <form class="submitMe"
                action="<?php echo e(route('maintain-suppliers.supplier_unverified_process', ['id' => $supplier->id])); ?>"
                method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Verify Supplier : <?php echo e($supplier->supplier_code); ?> -
                            <?php echo e($supplier->name); ?>

                        </h4>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to verify supplier account : <?php echo e($supplier->supplier_code); ?> -
                        <?php echo e($supplier->name); ?>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Verify</button>
                    </div>
                </div>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/maintainsuppliers/actions/supplier.blade.php ENDPATH**/ ?>