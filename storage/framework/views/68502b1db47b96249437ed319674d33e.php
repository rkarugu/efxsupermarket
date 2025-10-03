<?php $__env->startSection('content'); ?>
    <section class="content">

        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Promotion Groups</h3>
                    <div>
                        <a href="#" class="btn btn-primary"  data-toggle="modal" data-target="#createModal"><?php echo e('+ '); ?>Create</a>
                    </div>

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
                        <?php $__currentLoopData = $promotionGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $promotion_group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($loop->iteration); ?></td>
                                <td><?php echo e($promotion_group->name); ?></td>
                                <td><?php echo e($promotion_group->active?'Yes':'No'); ?></td>
                                <td><?php echo e($promotion_group->start_time); ?></td>
                                <td><?php echo e($promotion_group->end_time); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info edit-btn" data-id="<?php echo e($promotion_group->id); ?>" data-all="<?php echo e($promotion_group); ?>" data-toggle="modal" data-target="#editModal">
                                        <i class="glyphicon glyphicon-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="<?php echo e($promotion_group->id); ?>" data-name="<?php echo e($promotion_group->name); ?>" data-toggle="modal" data-target="#deleteModal">
                                        <i class="glyphicon glyphicon-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <!-- Create Modal -->
        <div class="modal fade" id="createModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Add Promotion Type</h4>
                    </div>
                    <div class="modal-body">
                        <form class="validate" id="createForm">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                                <div class="error-message" id="name-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="name">Start Time</label>
                                <input type="date" class="form-control" id="start_time" name="start_time" required>
                                <div class="error-message" id="start_time-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="name">End Time</label>
                                <input type="date" class="form-control" id="end_time" name="end_time" required>
                                <div class="error-message" id="end_time-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="active">Activate</label>
                                <input type="checkbox" class="form-check-input" id="active" name="active">
                                <div class="error-message" id="active"></div>
                            </div>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Edit Promotion Type</h4>
                    </div>
                    <div class="modal-body">
                        <form class="validate" id="editForm">
                            <input type="hidden" id="edit-id" name="id">
                            <div class="form-group">
                                <label for="edit-name">Name</label>
                                <input type="text" class="form-control" id="edit-name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="name">Start Time</label>
                                <input type="date" class="form-control" id="edit_start_time" name="start_time" required>
                                <div class="error-message" id="start_time-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="name">End Time</label>
                                <input type="date" class="form-control" id="edit_end_time" name="end_time" required>
                                <div class="error-message" id="end_time-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="active">Activate</label>
                                <input type="checkbox" class="form-check-inputl" id="edit_active" name="active">
                                <div class="error-message" id="active"></div>
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Delete Promotion Type</h4>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete <strong id="delete-name"></strong>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirm-delete">Delete</button>
                    </div>
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

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // Create Promotion Type
            $('#createForm').submit(function(e) {
                e.preventDefault();
                $('.error-message').text('');
                var formData = $(this).serializeArray();
                var active = $('#active').is(':checked') ? 1 : 0;
                formData = formData.filter(function(item) {
                    return item.name !== 'active';
                });
                formData.push({name: 'active', value: active});
                $.ajax({
                    url: "<?php echo e(route('promotion-group.store')); ?>",
                    method: "POST",
                    data: formData,

                    success: function(response) {
                        $('#createModal').modal('hide');
                        location.reload();
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;

                        // Display errors below the respective fields
                        if (errors.name) {
                            $('#name-error').text(errors.name[0]);
                        }
                        if (errors.start_time) {
                            $('#start_time-error').text(errors.start_time[0]);
                        }
                        if (errors.end_time) {
                            $('#end_time-error').text(errors.end_time[0]);
                        }
                        if (errors.active) {
                            $('#end_time-error').text(errors.active[0]);
                        }
                    }
                });
            });

            // Edit Promotion Type
            $('.edit-btn').click(function() {
                var id = $(this).data('id');
                var data = $(this).data('all');
                $('#edit-id').val(id);
                $('#edit-name').val(data.name);
                $('#edit_active').prop('checked', data.active == 1);
                $('#edit_start_time').val(formatDate(data.start_time));
                $('#edit_end_time').val(formatDate(data.end_time));
            });

            function formatDate(dateString) {

                let datePart = dateString.split(" ")[0];
                let resultString = datePart.replace("01 ", "01");
                return resultString;
            }

            $('#editForm').submit(function(e) {
                e.preventDefault();
                var id = $('#edit-id').val();
                // Gather form data
                var formData = $(this).serializeArray();
                var active = $('#edit_active').is(':checked') ? 1 : 0;
                formData = formData.filter(function(item) {
                    return item.name !== 'active';
                });
                formData.push({name: 'active', value: active});
                $.ajax({
                    url: "/admin/promotion-group/" + id,
                    method: "PUT",
                    data: formData,
                    success: function(response) {
                        $('#editModal').modal('hide');
                        location.reload();
                    },
                    error: function(response) {
                        // Handle error
                    }
                });
            });
            // Delete Promotion Type
            var deleteId;
            $('.delete-btn').click(function() {
                deleteId = $(this).data('id');
                var name = $(this).data('name');
                $('#delete-name').text(name);
            });

            $('#confirm-delete').click(function() {
                $.ajax({
                    url: "/admin/promotion-group/" + deleteId,
                    method: "DELETE",
                    success: function(response) {
                        $('#deleteModal').modal('hide');
                        location.reload();
                    },
                    error: function(response) {
                        // Handle error
                    }
                });
            });
        });
    </script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/inventory/item/promotion-group.blade.php ENDPATH**/ ?>