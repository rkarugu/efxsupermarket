<?php $__env->startSection('content'); ?>
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title" style="font-weight: 500 !important;"> <?php echo $title; ?> </h3>
                    <div class="d-flex">
                        <a href="<?php echo e(route('admin.show.item.log')); ?>" class="btn btn-primary" style="margin-top:0px;"><i class="fas fa-long-arrow-alt-left"></i> Back</a>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="session-message-container">
                    <?php echo $__env->make('message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="box-body">
                            <div class="form-group" style="margin-bottom: 0px">
                                <label for="inputEmail3" class="col-sm-5 text-left">Title</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" class="form-control" value="<?php echo e($item->inventoryItem->title); ?>" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group" style="margin-bottom: 0px">
                                <label for="inputEmail3" class="col-sm-5 text-left">Category</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" class="form-control" value="<?php echo e($item->inventoryItem?->category ? $item->inventoryItem?->category->category_description : ''); ?>" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group" style="margin-bottom: 0px">
                                <label for="inputEmail3" class="col-sm-5 text-left">Category</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" class="form-control" value="<?php echo e(date('d M, Y H:m', strtotime($item->created_at))); ?>" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="box-body">
                            <div class="form-group" style="margin-bottom: 0px">
                                <label for="inputEmail3" class="col-sm-5 text-left">Action By</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" class="form-control" value="<?php echo e($item->approvalBy->name); ?>" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group" style="margin-bottom: 0px">
                                <label for="inputEmail3" class="col-sm-5 text-left">Status</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" class="form-control" value="<?php echo e($item->status); ?>" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12"><hr></div>
                    <div class="col-sm-12">
                        <table class="table">
                            <thead>
                                <th></th>
                                <th>Edited Information</th>
                                <th>Original Information</th>
                            </thead>
                            <tbody>
                                <?php if($changes): ?>
                                    <?php $__currentLoopData = $changes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $change): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $key = key((array)$change);
                                        ?>
                                        <tr>
                                            <td><b><?php echo e($key); ?></b></td>
                                            <?php $__currentLoopData = $change; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <td><?php echo e($item[1]); ?></td>
                                                <td> <?php echo e($item[0]); ?></td>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                                            
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>                   
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('uniquepagestyle'); ?>
    
<?php $__env->stopSection(); ?>

<?php $__env->startSection('uniquepagescript'); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/maintaininvetoryitems/approval/item_history_view.blade.php ENDPATH**/ ?>