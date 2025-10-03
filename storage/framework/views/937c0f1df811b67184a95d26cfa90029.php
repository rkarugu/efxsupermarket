<?php $__env->startSection('content'); ?>
    <style>
        .span-action {

            display: inline-block;
            margin: 0 3px;

        }
    </style>
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                <div class="row">
                    <div class="col-md-9">
                        <h3><?php echo e($inventoryItem->title); ?> | Promotions</h3>

                      
                       
                    </div>
                    <div class="col-sm-3">
                        <?php if(isset($permission[$pmodule.'___manage-promotions']) || $permission == 'superadmin'): ?>
                        <?php if($can_create): ?>
                        <div align="right"><a href="<?php echo route('promotions-bands.create' , $inventoryItem->id); ?>" class="btn btn-success">Add Promotion</a>
                        </div>
                            
                        <?php endif; ?>
                           
                        <?php endif; ?>
                    </div>
                </div>
                <br>
                <?php echo $__env->make('message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="create_datatable_10">
                        <thead>
                        <tr>
                            <th width="3%">#</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th >Sale Quantity</th>
                            <th >Promotion Item</th>
                            <th >Promotion Item Quantity</th>
                            <th>Created By</th> 
                            <th >Action</th>
                            
                        </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $promotions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $promotion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <th><?php echo e($loop->iteration); ?></th>
                                    <td><?php echo e(\Carbon\Carbon::parse($promotion->from_date)->toDateString() ?? '-'); ?></td>                                   
                                    <td><?php echo e(\Carbon\Carbon::parse($promotion->to_date)->toDateString()); ?></td>
                                    <td><?php echo e($promotion->status ?? '-'); ?></td>
                                    <td><?php echo e($promotion->sale_quantity); ?></td>
                                    <td><?php echo e($promotion->promotionItem?->stock_id_code. ' '. $promotion->promotionItem?->title); ?></td>
                                    <td><?php echo e($promotion->promotion_quantity); ?></td>
                                    <td><?php echo e($promotion->initiatedBy?->name); ?></td>
                                    <td>
                                        <div class="action-button-div">
                                            <?php if(isset($permission[$pmodule.'___manage-promotions']) || $permission =='superadmin'): ?>  
                                            <a href="<?php echo e(route('promotions-bands.edit', $promotion->id)); ?>"><i class="fas fa-pen" title="edit"></i></a>
                                            <?php if($promotion->status != 'blocked'): ?>
                                            
                                            <a href="<?php echo e(route('promotions-bands.block', $promotion->id)); ?>"><i class="fa fa-lock fa-lg" title="Block Promotion"></i></a>

                                                
                                            <?php endif; ?>
                                            <?php if($promotion->status == 'blocked'): ?>
                                            <a href="<?php echo e(route('promotions-bands.unblock', $promotion->id)); ?>"><i class="fa fa-lock-open fa-lg" title="unblock  prommotion"></i></a>

                                                
                                            <?php endif; ?>
                                           
                                            <button type="button"  class="text-primary mr-2 btn-decline2 transparent-btn" data-toggle="modal" title="Delete" data-target="#confirmationModal2" data-promotion-id="<?php echo e($promotion->id); ?>">
                                                <i class="fas fa-trash-alt" style="color: red;"></i>
                                            </button>
                                            <?php endif; ?>
            
                                        </div>
                                    </td>

                                </tr>
                                
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

<div class="modal fade" id="confirmationModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
aria-labelledby="staticBackdropLabel" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="staticBackdropLabel">Are you sure you want to block this promotion?</h4>
           
        </div>
        <form method="post" id="confirmationForm" action="">
            <?php echo csrf_field(); ?>
            
            
            <input name="user_requested_access" type="hidden" id="user_requested_access"
                    value="<?php echo e(old('user_requested_access')); ?>" required />
           
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary btn-submit-updated-center">Yes, Block Promotion</button>
            </div>
        </form>
    </div>
</div>
</div>

<div class="modal fade" id="confirmationModal2" data-backdrop="static" data-keyboard="false" tabindex="-1"
aria-labelledby="staticBackdropLabel" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="staticBackdropLabel">Are you sure you want to Delete this promotion?</h4>
           
        </div>
        <form method="POST" id="confirmationForm2" action="">
            <?php echo csrf_field(); ?>
            <?php echo method_field("DELETE"); ?>
            
            <input name="user_requested_access2" type="hidden" id="user_requested_access2"
                    value="<?php echo e(old('user_requested_access2')); ?>" required />
           
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary btn-submit-updated-center2">Yes, Delete Promotion</button>
            </div>
        </form>
    </div>
</div>
</div>


    </section>

  

<?php $__env->stopSection(); ?>
<?php $__env->startSection('uniquepagescript'); ?>
<script>
    $(document).ready(function() {
        $('.btn-decline').click(function() {
            var promotionId = $(this).data('promotion-id');
            $('#confirmationModal').find('#promotion_id').val(promotionId);
            console.log(promotionId);
            $('#confirmationForm').attr('action', '<?php echo e(route('promotions-bands.block', ['promotionId' => ':promotionId'])); ?>'.replace(':promotionId', promotionId));
            console.log("Form action:", $('#confirmationForm').attr('action')); // Check if action attribute is set correctly

        });
    
        $('#confirmationModal').on('show.bs.modal', function(event) {
            var modal = $(this);
            modal.find('.btn-submit-updated-center').off('click').on('click', function() {
                // Here you can submit the form
                modal.find('form').submit();
                // Close the modal
                modal.modal('hide');
            });
        });


        $('.btn-decline2').click(function() {
            var promotionId = $(this).data('promotion-id');
            $('#confirmationModal2').find('#promotion_id').val(promotionId);
            $('#confirmationForm2').attr('action', '<?php echo e(route('promotions-bands.delete', ['promotionId' => ':promotionId'])); ?>'.replace(':promotionId', promotionId));
        });
    
        $('#confirmationModal2').on('show.bs.modal', function(event) {
            var modal = $(this);
            modal.find('.btn-submit-updated-center2').off('click').on('click', function() {
                // Here you can submit the form
                modal.find('form').submit();
                // Close the modal
                modal.modal('hide');
            });
        });

      

    });
    </script>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.admin.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/promotions/index.blade.php ENDPATH**/ ?>