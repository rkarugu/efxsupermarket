<?php $__env->startSection('content'); ?>
    <section class="content">

        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Active Promotions</h3>
                </div>
            </div>

            <div class="box-body">

                <hr>

                <?php echo $__env->make('message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


                <div class="col-md-12">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Is Active</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $promotionsGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($loop->iteration); ?></td>
                                <td><?php echo e($group -> name); ?></td>
                                <td><?php echo e($group->active?'Yes':'No'); ?></td>
                                <td><?php echo e($group->start_time); ?></td>
                                <td><?php echo e($group->end_time); ?></td>
                                <td>
                                    <a href="<?php echo e(route('active-promotions.show', $group)); ?>" class="btn btn-sm btn-primary"> <i class="fa fa-eye"></i></a>
                                </td>

                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>


<?php $__env->stopSection(); ?>
<?php $__env->startPush('styles'); ?>
    <link href="<?php echo e(asset('assets/admin/bower_components/select2/dist/css/select2.min.css')); ?>" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/dist/datepicker.css')); ?>">
    <style>
        .modal .datepicker {
            z-index: 9999; /* Higher than Bootstrap modal z-index */
        }
        .error-message {
            color: red;
            font-size: 12px;
        }
    </style>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('scripts'); ?>
    <div id="loader-on"
         style="position: fixed; top: 0; text-align: center; z-index: 999999;
                width: 100%;  height: 100%; background: #000000b8; display:none;"
         class="loder">
        <div class="loader" id="loader-1"></div>
    </div>
    <script src="<?php echo e(asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')); ?>"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="<?php echo e(asset('js/sweetalert.js')); ?>"></script>
    <script src="<?php echo e(asset('js/form.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/dist/bootstrap-datepicker.js')); ?>"></script>
    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd'
            });
        });
    </script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/inventory/item/activePromotions/index.blade.php ENDPATH**/ ?>