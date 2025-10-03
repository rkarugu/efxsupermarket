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
                        <h3 class="box-title"> <?php echo $title; ?> </h3>
                    </div>
                    <div class="col-sm-3">
                        <div align="right">
                            <a href="<?php echo route('admin.downloadExcel.approval', $status); ?>" class="btn btn-primary">Excel</a>
                            <button class="btn btn-success" style="display: none;" onclick="approveSelectedItems()">Approve
                                Selected</button>
                        </div>
                    </div>
                </div>
                <br>
                <?php echo $__env->make('message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <div class="col-md-12 no-padding-h">
                    <form id="approvalForm" action="<?php echo e(route('approve_bulk_items')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <table class="table table-bordered table-hover" id="create_datatable">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="select-all-checkbox" onclick="toggleSelectAll(this)">
                                    </th>
                                    <th>Requested By</th>
                                    <th>Requested On</th>
                                    <th>Stock ID Code</th>
                                    <th>Title</th>
                                    <th>Item Category</th>
                                    <th>Pack Size</th>
                                    <th class="noneedtoshort">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $pendingNewApprovalStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $new_data = json_decode($item->new_data);
                                    ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="selectedItems[]"
                                                value="<?php echo e($new_data->stock_id_code); ?>">
                                        </td>
                                        <td> <?php echo e($item->approvalBy->name); ?> </td>
                                        <td><?php echo e($item->created_at); ?></td>
                                        <td><?php echo e($new_data->stock_id_code); ?></td>
                                        <td><?php echo e($new_data?->title); ?></td>
                                        <td><?php echo e(getItemCategory($new_data->wa_inventory_category_id)->category_description); ?>

                                        </td>
                                        <td><?php echo e(getItemPackSize($new_data->pack_size_id)?->title); ?></td>
                                        <td><span class="span-action"><a
                                                    href="<?php echo e(route('item-new-approval-show', $item->id)); ?>"
                                                    title="View"><i class="fas fa-eye" aria-hidden="true"></i></a></span>
                                        </td>

                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>

                        </table>
                    </form>
                </div>
            </div>
        </div>


    </section>

    <div class="modal " id="manage-stock-model" role="dialog" tabindex="-1" aria-hidden="true" role="dialog"
        aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <?php echo Form::open(['route' => 'maintain-items.manage-stock', 'class' => 'validate form-horizontal']); ?>

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                        Adjust Item Stock
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="box-body">


                    </div>
                </div>

                <div class="modal-footer">
                    <input type="submit" class="btn btn-primary" value="Submit">

                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        Close
                    </button>

                </div>
                <?php echo Form::close(); ?>

            </div>
        </div>
    </div>

    <div class="modal " id="manage-category-model" role="dialog" tabindex="-1" aria-hidden="true" role="dialog"
        aria-labelledby="myModalLabel1">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <?php echo Form::open(['route' => 'maintain-items.manage-category-price', 'class' => 'validate form-horizontal']); ?>

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel1">
                        Adjust Item Category Price : <span id="moretitle"></span>
                    </h4>
                </div>
                <div class="modal-body">

                    <div class="box-body">


                    </div>
                </div>

                <div class="modal-footer">
                    <input type="submit" class="btn btn-primary" value="Submit">

                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        Close
                    </button>

                </div>
                <?php echo Form::close(); ?>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('uniquepagestyle'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/multistep-form.css')); ?>">
    <div id="loader-on"
        style="
position: fixed;
top: 0;
text-align: center;
display: block;
z-index: 999999;
width: 100%;
height: 100%;
background: #000000b8;
display:none;
"
        class="loder">
        <div class="loader" id="loader-1"></div>
    </div>
    <link href="<?php echo e(asset('assets/admin/bower_components/select2/dist/css/select2.min.css')); ?>" rel="stylesheet" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('uniquepagescript'); ?>
    <script src="<?php echo e(asset('js/sweetalert.js')); ?>"></script>
    <script src="<?php echo e(asset('js/multistep-form.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')); ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.wa_inventory_category_id').select2();
            $('#supplier-id').select2();

            toggleApproveButton();

        });
    </script>
    <script type="text/javascript">
        function manageStockPopup(link = "") {


            $('#manage-stock-model').modal('show');
            //$('#manage-stock-model').find(".box-body").html('<img style="width:50px;" src="<?php echo e(asset('public/assets/admin/images/loading.gif')); ?>">');
            $('#manage-stock-model').find(".box-body").load(link);

        }

        function getAndUpdateItemAvailableQuantity(input_obj) {
            location_id = $(input_obj).val();
            if (location_id) {
                stock_id_code = $('#stock_id_code_input').val();
                jQuery.ajax({
                    url: '<?php echo e(route('maintain-items.get-available-quantity-ajax')); ?>',
                    type: 'POST',
                    dataType: "json",
                    data: {
                        location_id: location_id,
                        stock_id_code: stock_id_code
                    },
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#current_qty_available').val(response['available_quantity']);
                    }
                });
            } else {
                $('#current_qty_available').val(0);
            }

        }

        function manageCategoryPopup(link, that) {
            $('#manage-category-model').modal('show');
            //$('#manage-stock-model').find(".box-body").html('<img style="width:50px;" src="<?php echo e(asset('public/assets/admin/images/loading.gif')); ?>">');
            $('#myModalLabel1 #moretitle').html($(that).data('title'));
            $('#manage-category-model').find(".box-body").load(link);

        }

        function getAndUpdateItemAvailableQuantity(input_obj) {
            location_id = $(input_obj).val();
            if (location_id) {
                stock_id_code = $('#stock_id_code_input').val();
                jQuery.ajax({
                    url: '<?php echo e(route('maintain-items.get-available-quantity-ajax')); ?>',
                    type: 'POST',
                    dataType: "json",
                    data: {
                        location_id: location_id,
                        stock_id_code: stock_id_code
                    },
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#current_qty_available').val(response['available_quantity']);
                    }
                });
            } else {
                $('#current_qty_available').val(0);
            }

        }

        function toggleSelectAll(checkbox) {
            var subCheckboxes = document.querySelectorAll('input[name="selectedItems[]"]');
            subCheckboxes.forEach(function(subCheckbox) {
                subCheckbox.checked = checkbox.checked;
            });

            toggleApproveButton();
        }


        function toggleApproveButton() {
            var checkboxes = document.querySelectorAll('input[name="selectedItems[]"]');
            var approveButton = document.querySelector('button.btn-success');
            var isChecked = Array.from(checkboxes).some(function(checkbox) {
                return checkbox.checked;
            });

            approveButton.style.display = isChecked ? 'inline-block' : 'none';
        }

        function approveSelectedItems() {
            var form = document.getElementById('approvalForm');
            var checkboxes = form.querySelectorAll('input[name="selectedItems[]"]:checked');
            var itemIds = Array.from(checkboxes).map(function(checkbox) {
                return checkbox.value;
            });

            if (itemIds.length === 0) {
                swal("Error", "Please select at least one item to approve.", "error");
                return;
            }

            form.submit();
        }

        toggleApproveButton();

        var checkboxes = document.querySelectorAll('input[name="selectedItems[]"]');
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', toggleApproveButton);
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/maintaininvetoryitems/approval/new_item_approval.blade.php ENDPATH**/ ?>