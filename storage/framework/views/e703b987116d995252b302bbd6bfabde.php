<?php $__env->startSection('content'); ?>
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="box-title"> <?php echo e($inventoryItem->title); ?> | Create Promotion </h3>            
                <a href="<?php echo e(route('promotions.listing', $inventoryItem->id)); ?>" class="btn btn-primary   "><?php echo e('<< '); ?>Back to Promotions</a>
            </div>

        </div>
         <?php echo $__env->make('message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <form class="validate form-horizontal"  role="form" method="POST" action="<?php echo e(route('promotions-bands.store', $inventoryItem->id)); ?>" enctype = "multipart/form-data">
            <?php echo e(csrf_field()); ?>

            <div class="box-body">
                <div class="form-group">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Item Supplier</label>
                        <div class="col-sm-9">
                            <select name="supplier_id" id="supplier_id" class="mlselect" required>
                                <option value="" selected >Select Supplier</option>
                                <?php $__currentLoopData = $inventoryItem->suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($supplier->id); ?>">
                                        <?php echo e($supplier->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Promotion Type </label>
                        <div class="col-sm-9">
                            <select name="promotion_type_id" id="promotion_type_id" class="mlselect" required>
                                <option value="" selected >Select Type</option>
                                <?php $__currentLoopData = $promotionTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($type->id); ?>" data-description="<?php echo e($type->description); ?>">
                                        <?php echo e($type->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Promotion Group </label>
                        <div class="col-sm-9">
                            <select name="promotion_group_id" id="promotion_group_id" class="mlselect" required>
                                <option value="" selected >Select Group</option>
                                <?php $__currentLoopData = $promotionGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($group->id); ?>">
                                        <?php echo e($group->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group hidden bsgy">
                        <label for="inputEmail3" class="col-sm-2 control-label">Sale Quantity</label>
                        <div class="col-sm-9" style="">
                            <?php echo Form::number('item_quantity', null, ['maxlength'=>'255','placeholder' => '0', 'required'=>true, 'class'=>'form-control' ]); ?>

                        </div>
                    </div>
                    <div class="form-group hidden bsgy">
                        <label for="inputEmail3" class="col-sm-2 control-label">Promotion Item</label>
                        <div class="col-sm-9">
                            <select name="inventory_item" id="inventory_item" class="mlselect" required>
                                <option value="" selected >Select Item</option>
                                <?php $__currentLoopData = $inventoryItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($item->id); ?>" <?php if($item->id == $inventoryItem->id): echo 'selected'; endif; ?> >
                                    <?php echo e($item->stock_id_code .' '. $item->title); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group hidden bsgy">
                        <label for="inputEmail3" class="col-sm-2 control-label">Promotion Quantity</label>
                        <div class="col-sm-9">
                            <?php echo Form::number('promotion_quantity', null, ['maxlength'=>'255','placeholder' => '0', 'required'=>true, 'class'=>'form-control']); ?>

                        </div>
                    </div>

                    <div class="form-group hidden pd">
                        <label for="inputEmail3" class="col-sm-2 control-label">Current Price</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" readonly value="<?php echo e($inventoryItem-> selling_price); ?>">
                        </div>
                    </div>
                    <div class="form-group hidden pd">
                        <label for="inputEmail3" class="col-sm-2 control-label">Promotion Price</label>
                        <div class="col-sm-9">
                            <?php echo Form::number('promotion_price', null, ['maxlength'=>'255','placeholder' => '0', 'required'=>true, 'class'=>'form-control']); ?>

                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">From Date</label>
                        <div class="col-sm-9">
                            <?php echo Form::date('from_date', null, ['maxlength'=>'255','placeholder' => '0', 'required'=>true, 'class'=>'form-control']); ?>

                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">To Date</label>
                        <div class="col-sm-9">
                            <?php echo Form::date('to_date', null, ['maxlength'=>'255','placeholder' => '0', 'required'=>true, 'class'=>'form-control']); ?>

                        </div>
                </div>
                    <?php if($inventoryItem->item_count != null): ?>
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Apply To Split</label>
                            <div class="col-sm-9">
                                <?php echo Form::checkbox('apply_to_split', 'apply', false, ['class' => 'form-check-input']); ?>

                            </div>
                        </div>
                    <?php endif; ?>
                </div>              
            </div>
            <div class="box-footer" >
                <button type="submit" class="btn btn-primary" >Submit</button>
            </div>
        </form>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('uniquepagestyle'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/dist/datepicker.css')); ?>">
    <link href="<?php echo e(asset('assets/admin/bower_components/select2/dist/css/select2.min.css')); ?>" rel="stylesheet"/>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('uniquepagescript'); ?>
    <script src="<?php echo e(asset('assets/admin/dist/bootstrap-datepicker.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')); ?>"></script>
    <script type="text/javascript">
        $(function () {

            $(".mlselect").select2();
        });
    </script>

    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>



    <script type="text/javascript" class="init">
        $(document).ready(function () {
            $('#promotion_type_id').change(function() {

                const selectedType = $(this).find(':selected').data('description');
                if (selectedType === <?php echo json_encode(\App\Enums\PromotionMatrix::BSGY->value, 15, 512) ?>) {
                    $('.bsgy').toggleClass('hidden');
                }else {
                    $('.bsgy').addClass('hidden');
                }

                if (selectedType === <?php echo json_encode(\App\Enums\PromotionMatrix::PD->value, 15, 512) ?>) {
                    $('.pd').toggleClass('hidden');
                } else {
                    $('.pd').addClass('hidden');
                }
            });
            $('#create_datatable1').DataTable({
                pageLength: "100",
                "order": [
                    [0, "desc"]
                ]
            });
        });
    </script>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.admin.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/promotions/create.blade.php ENDPATH**/ ?>