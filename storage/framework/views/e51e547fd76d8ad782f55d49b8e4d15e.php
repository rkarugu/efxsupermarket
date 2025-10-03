<div style="padding: 10px">
    <div class="row">
        <div class="col-md-9">
        </div>
        <div class="col-sm-3">
            <?php if(can('manage-promotions', 'maintain-items')): ?>
                <?php if($can_create): ?>
                    <div align="right" class="form-group">
                        <a href="<?php echo route('promotions-bands.create', $itemId); ?>" class="btn btn-success">
                            <i class="fa fa-plus"></i>
                            Add Promotion</a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <table class="table table-bordered table-hover" id="create_datatable_25">
        <thead>
            <tr>
                <th width="3%">#</th>
                <th>Type</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Original Price</th>
                <th>Promotion Price</th>
                <th>Sale Quantity</th>
                <th>Promotion Item</th>
                <th>Promotion Item Quantity</th>
                <th>Created By</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $promotions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $promotion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <th><?php echo e($loop->iteration); ?></th>
                    <td><?php echo e($promotion->promotionType->name); ?></td>
                    <td><?php echo e(\Carbon\Carbon::parse($promotion->from_date)->toDateString() ?? '-'); ?></td>
                    <td><?php echo e(\Carbon\Carbon::parse($promotion->to_date)->toDateString()); ?></td>
                    <td><?php echo e($promotion->status ?? '-'); ?></td>
                    <td><?php echo e($promotion->current_price ?? '-'); ?></td>
                    <td><?php echo e($promotion->promotion_price ?? '-'); ?></td>
                    <td><?php echo e($promotion->sale_quantity); ?></td>
                    <td><?php echo e($promotion->promotionItem?->stock_id_code . ' ' . $promotion->promotionItem?->title); ?></td>
                    <td><?php echo e($promotion->promotion_quantity); ?></td>
                    <td><?php echo e($promotion->initiatedBy?->name); ?></td>
                    <td>
                        <div class="action-button-div">
                            <?php if(can('manage-promotions', 'maintain-items')): ?>
                                <a href="<?php echo e(route('promotions-bands.edit', $promotion->id)); ?>">
                                    <i class="fas fa-pen" title="edit"></i></a>
                                <?php if($promotion->status != 'blocked'): ?>
                                    <a href="<?php echo e(route('promotions-bands.block', $promotion->id)); ?>">
                                        <i class="fa fa-lock fa-lg" title="Block Promotion"></i></a>
                                <?php endif; ?>
                                <?php if($promotion->status == 'blocked'): ?>
                                    <a href="<?php echo e(route('promotions-bands.unblock', $promotion->id)); ?>">
                                        <i class="fa fa-lock-open fa-lg" title="unblock  prommotion"></i></a>
                                <?php endif; ?>
                                <button type="button" class="text-primary mr-2 btn-decline2 transparent-btn"
                                    data-toggle="modal" title="Delete" data-target="#confirmationModal3"
                                    data-promotion-id="<?php echo e($promotion->id); ?>">
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
                    <button type="submit" class="btn btn-primary btn-submit-updated-center">Yes, Block
                        Promotion</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmationModal3" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="staticBackdropLabel">Are you sure you want to Delete this promotion?</h4>
            </div>
            <form method="POST" id="confirmationForm3">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <input name="user_requested_access2" type="hidden" id="user_requested_access2"
                    value="<?php echo e(old('user_requested_access2')); ?>" required />
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-submit-updated-center2">Yes, Delete
                        Promotion</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->startPush('scripts'); ?>
    <script>
        $(document).ready(function() {
            $('.btn-decline').click(function() {
                var promotionId = $(this).data('promotion-id');
                $('#confirmationModal').find('#promotion_id').val(promotionId);
                console.log(promotionId);
                $('#confirmationForm').attr('action',
                    '<?php echo e(route('promotions-bands.block', ['promotionId' => ':promotionId'])); ?>'.replace(
                        ':promotionId', promotionId));
                console.log("Form action:", $('#confirmationForm').attr(
                'action')); 
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
                $('#confirmationModal3').find('#promotion_id').val(promotionId);
                $('#confirmationForm3').attr('action',
                    '<?php echo e(route('promotions-bands.delete', ['promotionId' => ':promotionId'])); ?>'
                    .replace(':promotionId', promotionId));
            });

            $('#confirmationModal3').on('show.bs.modal', function(event) {
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
<?php $__env->stopPush(); ?>
<?php /**PATH C:\laragon\www\kaninichapchap\resources\views/components/item-centre/promotions.blade.php ENDPATH**/ ?>