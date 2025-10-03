<?php $__env->startSection('content'); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header">
                    <h3 class="box-title mb-3" style="margin-bottom: 5px"> Employees </h3>
                    <div class="row mt-2">
                        <form method="GET" action="<?php echo url('admin/employees'); ?>">
                            <?php echo e(csrf_field()); ?>

                            <?php if($authuser->role_id == 1): ?>
                                <div class="col-sm-4 ml-1">
                                    <?php echo Form::select('role_id', getRoles(),$roleFilter, ['maxlength'=>'255','placeholder' => 'Select Role', 'required'=>false, 'class'=>'form-control mlselec6t']); ?>

                                </div>
                            <?php endif; ?>

                            <div class="col-sm-4">
                                
                                
                                
                                <?php
                                if ($canviewall) {
                                    $restaurants = getBranchListWithId();
                                } else {
                                    $restaurants = [$restaurantid => getBranchListWithId()[$restaurantid]];
                                }
                                ?>
                                <?php echo Form::select('restaurant_id', $restaurants, $restaurantid, ['maxlength' => '255', 'placeholder' => 'Select Branch', 'required' => false, 'class' => 'form-control mlselec6t']); ?>


                            </div>

                            <div class="form-group col-md-3">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <button type="submit" class="btn btn-warning" name="manage-request" value="excel"><i class="fa fa-file-excel"></i></button>

                            </div>
                        </form>

                        <?php if(isset($permission[$pmodule . '___add']) || $permission == 'superadmin'): ?>
                            <a href="<?php echo route($model . '.create'); ?>" class="btn btn-success">Add <?php echo $title; ?></a>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    <?php echo $__env->make('message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>

                <div class="col-md-12">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                        <tr>
                            <th width="7%">S.No.</th>
                            <th width="20%">Name</th>
                            <th width="10%">Phone Number</th>
                            <th width="10%">Role</th>
                            <th width="15%">Branch</th>
                            <th width="18%">Routes</th>
                            
                            <th width="20%" class="noneedtoshort">Action</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php if(isset($users) && !empty($users)): ?>
                                <?php $b = 1; ?>
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo $b; ?></td>
                                    <td><?php echo ucfirst($user->name); ?></td>
                                    <td><?php echo $user->phone_number; ?></td>
                                    <td><?php echo @$user->userRole->title; ?></td>
                                    <td><?php echo ucfirst(@$user->userRestaurent->name); ?></td>
                                    <td><?php echo @$user->routes; ?></td>
                                    
                                    <td class="action_crud">
                                        <?php if(isset($permission[$pmodule . '___change_password']) || $permission == 'superadmin'): ?>
                                            <span>
                                                    <a title="Change Password"
                                                       href="<?php echo e(route($model . '.change_password', $user?->slug)); ?>">
                                                        <i class="fa fa-key"></i>
                                                    </a>
                                                </span>
                                        <?php endif; ?>

                                        <?php if(isset($permission[$pmodule . '___edit']) || $permission == 'superadmin'): ?>
                                            <span>
                                                    <a title="Edit" href="<?php echo e(route($model . '.edit', $user->slug)); ?>">
                                                        <img src="<?php echo asset('assets/admin/images/edit.png'); ?>" alt="">
                                                    </a>
                                                </span>
                                        <?php endif; ?>

                                        <?php if(
                                            (isset($permission[$pmodule . '___delete']) || $permission == 'superadmin') &&
                                                $user->stock_count == 0 &&
                                                $user->debtor_count == 0 && 1==2): ?>
                                            <span>
                                                    <form title="Trash"
                                                          action="<?php echo e(URL::route($model . '.destroy', $user->slug)); ?>"
                                                          method="POST">
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                                                        <button style="float:left"><i class="fa fa-trash"
                                                                                      aria-hidden="true"></i></button>
                                                    </form>
                                                </span>
                                        <?php endif; ?>

                                        <?php if(isset($permission[$pmodule . '___edit']) || $permission == 'superadmin'): ?>
                                            <span class="">
                                                    <a title="Change Status"
                                                       href="<?php echo e(route($model . '.status', [$user->slug, $user->status])); ?>">
                                                        <?php if($user->status == '1'): ?>
                                                            <img src="<?php echo asset('assets/admin/images/icon-active.png'); ?>" alt="">
                                                        <?php else: ?>
                                                            <img src="<?php echo asset('assets/admin/images/deactivate.png'); ?>" alt="">
                                                        <?php endif; ?>


                                                    </a>
                                                </span>
                                        <?php endif; ?>


                                        <?php if(isset($permission[$pmodule . '___edit']) || $permission == 'superadmin'): ?>
                                            <?php if($user->userRole?->slug == 'waiter'): ?>
                                                <span>

                                                        <a class="left-padding-small" data-href="<?php echo route('admin.table.assignment', $user->slug); ?>"
                                                           onclick="managetableassignment('<?php echo route('admin.table.assignment', $user->slug); ?>')"
                                                           data-toggle="modal" data-target="#assign-table"
                                                           data-dismiss="modal"><i class="fa fa-table "
                                                                                   style="color: #444; cursor: pointer;"
                                                                                   title="Assign Table"></i></a>
                                                    </span>
                                            <?php endif; ?>
                                            <span>
                                                    <a class="left-padding-small" data-href="<?php echo route('admin.table.assignment', $user->slug); ?>"
                                                       onclick="manageauuthorizationlevel('<?php echo route('admin.authorization.assignment', $user->slug); ?>')"
                                                       data-toggle="modal" data-target="#assign-authorization-level"
                                                       data-dismiss="modal"><i class="fa fa-shield"
                                                                               style="color: #444; cursor: pointer;"
                                                                               title="Internal Requisitions Authorization Level"></i></a></span>


                                            <span>
                                                    <a class="left-padding-small" data-href="<?php echo route('admin.table.assignment', $user->slug); ?>"
                                                       onclick="manageexternalauuthorizationlevel('<?php echo route('admin.external.authorization.assignment', $user->slug); ?>')"
                                                       data-toggle="modal"
                                                       data-target="#assign-extarnal-authorization-level"
                                                       data-dismiss="modal"><i class="fa fa-fw fa-anchor"
                                                                               style="color: #444; cursor: pointer;"
                                                                               title="External Requisitions Authorization Level"></i></a></span>


                                            <span>
                                                    <a class="left-padding-small" data-href="<?php echo route('admin.table.assignment', $user->slug); ?>"
                                                       onclick="managepurchaseauuthorizationlevel('<?php echo route('admin.purchase.order.authorization.assignment', $user->slug); ?>')"
                                                       data-toggle="modal"
                                                       data-target="#assign-purchase-authorization-level"
                                                       data-dismiss="modal"><i class="fa fa-fw fa-archive"
                                                                               style="color: #444; cursor: pointer;"
                                                                               title="Purchase Order Requisitions Authorization Level"></i></a></span>
                                        <?php endif; ?>


                                        <span>








                                            </span>

                                        <?php if($permission == 'superadmin'): ?>
                                            &nbsp;
                                            <span>
                                                    <input <?php echo e($user->invoice_r_permission_count > 0 ? 'checked' : ''); ?>

                                                           data-user-id="<?php echo e(base64_encode($user->id)); ?>" type="checkbox"
                                                           title="Invoice R Permission" class="invoice_r_permission"
                                                           value="1">
                                                </span>
                                            &nbsp;
                                        <?php endif; ?>

                                        <?php if(isset($permission[$pmodule . '___edit']) || isset($permission[$pmodule . '___suppliers']) || $permission == 'superadmin'): ?>
                                            &nbsp;
                                            <span>
                                                    
                                                    <a onclick="openAssignSupplier(<?php echo e($user->id); ?>,'<?php echo ucfirst($user->name); ?>')">
                                                        <i class="fa fa-users"></i>
                                                    </a>
                                                    
                                                    
                                                </span>
                                            &nbsp;
                                        <?php endif; ?>

                                        <?php if((isset($permission[$pmodule . '___edit']) || $permission == 'superadmin') && 1==2): ?>
                                            <!-- Button trigger modal -->
                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                                    data-target="#modelId<?php echo e($user->id); ?>">
                                                <i class="fa fa-lock"></i>
                                            </button>

                                            <!-- Modal -->
                                            <div class="modal fade" id="modelId<?php echo e($user->id); ?>" tabindex="-1"
                                                 role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                                                <form action="<?php echo e(route('users.appAssignUserPermission')); ?>"
                                                      method="post" class="submitMe">
                                                    <?php echo e(csrf_field()); ?>

                                                    <input type="hidden" name="user_id"
                                                           value="<?php echo e($user->id); ?>">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Assign App Menu Permissions to
                                                                    <?php echo e($user->name); ?></h5>
                                                                <button type="button" class="close"
                                                                        data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <?php
                                                                    $getAssignedPermisisons = $user->app_permissions->pluck('module')->toArray();
                                                                ?>
                                                                <div class="row">
                                                                    <?php $__currentLoopData = $app_permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <div class="col-sm-6">
                                                                            <div class="form-check">
                                                                                <label class="form-check-label">
                                                                                    <input type="checkbox"
                                                                                           class="form-check-input"
                                                                                           name="permission[]"
                                                                                           <?php if(in_array($perm, $getAssignedPermisisons)): ?> checked <?php endif; ?>
                                                                                           value="<?php echo e($perm); ?>">
                                                                                    <?php echo e($perm); ?>

                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                        data-dismiss="modal">Close
                                                                </button>
                                                                <button type="submit"
                                                                        class="btn btn-primary">Save
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                        <span>
                                                        <a class="left-padding-small" onclick="assignBranches('<?php echo route('admin.users.assign_branches', ['id'=>$user->id]); ?>')" data-toggle="modal"
                                                           data-target="#assign-branches" data-dismiss="modal"><i class="fa fa-fw fa-list" style="color: #444; cursor: pointer;"
                                                                                                                  title="Assign Branches"></i></a></span>
                                        </span>

                                    </td>


                                </tr>
                                    <?php $b++; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <?php echo $__env->make('admin.users.supplier_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="modal " id="assign-table" role="dialog" tabindex="-1" aria-hidden="true" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

            </div>
        </div>
    </div>


    <div class="modal " id="assign-authorization-level" role="dialog" tabindex="-1" aria-hidden="true"
         role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

            </div>
        </div>
    </div>

    <div class="modal " id="assign-extarnal-authorization-level" role="dialog" tabindex="-1" aria-hidden="true"
         role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

            </div>
        </div>
    </div>

    <div class="modal " id="assign-purchase-authorization-level" role="dialog" tabindex="-1" aria-hidden="true"
         role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

            </div>
        </div>
    </div>
    <div class="modal " id="assign-branches" role="dialog" tabindex="-1" aria-hidden="true" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

            </div>
        </div>
    </div>






    <script type="text/javascript">
        $(document).ready(function() {
            console.log('Document ready');
            
            // Initialize select2
            try {
                $('.mlselec6t').select2();
                console.log('Select2 initialized');
            } catch (e) {
                console.error('Error initializing select2:', e);
            }

            // Debug modal element
            console.log('Modal element exists:', $('#assign-supplier').length > 0);
            console.log('Modal HTML:', $('#assign-supplier').html());

            // Initialize modal
            try {
                $('#assign-supplier').modal({
                    show: false,
                    backdrop: 'static',
                    keyboard: false
                });
                console.log('Modal initialized');

                // Add modal event listeners
                $('#assign-supplier').on('show.bs.modal', function () {
                    console.log('Modal show event triggered');
                });

                $('#assign-supplier').on('shown.bs.modal', function () {
                    console.log('Modal shown event triggered');
                });

                $('#assign-supplier').on('hide.bs.modal', function () {
                    console.log('Modal hide event triggered');
                });

                $('#assign-supplier').on('hidden.bs.modal', function () {
                    console.log('Modal hidden event triggered');
                });
            } catch (e) {
                console.error('Error initializing modal:', e);
            }
        });

        function unassginSuppliers() {
            $('.assignBtn').text("Update Supplier");
            $('#list-suppliers').html('');
            $('.select_supplier option').attr('disabled', false);
            $('.select_supplier').val('').trigger('change.select2');
            return false;
        }

        function openAssignSupplier(userId, name) {
            console.log('Opening supplier modal for user:', userId, name);
            
            // Show loader
            $('.loder').show();
            
            // Clear any existing hidden inputs
            $('#assign-supplier form input[name="user_id"]').remove();
            
            // Add the user ID
            $('#assign-supplier form').append("<input type='hidden' name='user_id' value='" + userId + "'>");
            $('#user_name').html(name);
            $('.select_supplier option').attr('disabled', false);
            $('#list-suppliers').html("");
            $('.select_supplier').val('').trigger('change.select2');
            
            // Remove existing change event handler
            $('.select_supplier').off('change');
            
            // Add new change event handler
            $('.select_supplier').on('change', function() {
                var selectedValue = $(this).val();
                console.log('Supplier selected:', selectedValue);
                
                if (!selectedValue) return;
                
                $('.assignBtn').text("Assign Suppliers");
                
                if (selectedValue === "Select All") {
                    $('#list-suppliers').html('');
                    $('.select_supplier option').each(function(i, v) {
                        if ($(v).val() !== "Select All" && $(v).val() !== "") {
                            $('#list-suppliers').append('<li class="list-group-item">' +
                                '<input type="checkbox" name="supplier[]" checked value="' + $(v).val() + '"> ' + $('.select_supplier option[value="' + $(v).val() + '"]').html() +
                                '</li>');
                            $('.select_supplier option[value="' + $(v).val() + '"]').attr('disabled', true);
                        }
                    });
                } else {
                    $('#list-suppliers').append('<li class="list-group-item">' +
                        '<input type="checkbox" name="supplier[]" checked value="' + selectedValue + '"> ' + $('.select_supplier option[value="' + selectedValue + '"]').html() +
                        '</li>');
                    $('.select_supplier option[value="' + selectedValue + '"]').attr('disabled', true);
                }
                
                // Reset select2 to default option
                $(this).val('').trigger('change.select2');
            });
            
            $.ajax({
                url: '<?php echo e(route("admin.users.get_user_suppliers")); ?>',
                type: 'GET',
                dataType: 'json',
                data: {
                    id: userId
                },
                success: function (data) {
                    console.log('Received supplier data:', data);
                    
                    // Populate suppliers list
                    for (let i in data.data) {
                        $('#list-suppliers').append('<li class="list-group-item">' +
                            '<input type="checkbox" checked name="supplier[]" value="' + data.data[i].wa_supplier_id + '"> ' + data.data[i].supplier.name +
                            '</li>');
                        $('.select_supplier option[value="' + data.data[i].wa_supplier_id + '"]').attr('disabled', true);
                    }
                    
                    // Hide loader
                    $('.loder').hide();
                    
                    // Debug modal state before showing
                    console.log('Modal element exists before show:', $('#assign-supplier').length > 0);
                    console.log('Modal is initialized before show:', $('#assign-supplier').data('bs.modal') !== undefined);
                    
                    // Show modal
                    try {
                        $('#assign-supplier').modal('show');
                        console.log('Modal show called');
                        
                        // Force modal to be visible
                        $('#assign-supplier').css('display', 'block');
                        $('#assign-supplier').addClass('in');
                        $('body').addClass('modal-open');
                        $('.modal-backdrop').addClass('in');
                    } catch (e) {
                        console.error('Error showing modal:', e);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    $('.loder').hide();
                    alert('Error loading suppliers. Please try again.');
                }
            });
        }

        function release_all_waiter() {
            var isconfirmed = confirm("Do you want to clear all assigned table?");
            if (isconfirmed) {
                window.location.href = '<?php echo e(route('clear.all.tables.from.waiters')); ?>';
            }
        }

        function assignBranches(link) {
            $('#assign-branches').find(".modal-content").load(link);
        }

        function managetableassignment(link) {
            $('#assign-table').find(".modal-content").load(link);
        }

        function manageauuthorizationlevel(link) {
            $('#assign-authorization-level').find(".modal-content").load(link);
        }

        function manageexternalauuthorizationlevel(link) {
            //alert();
            $('#assign-extarnal-authorization-level').find(".modal-content").load(link);
        }

        function managepurchaseauuthorizationlevel(link) {
            //alert();
            $('#assign-purchase-authorization-level').find(".modal-content").load(link);
        }
    </script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('uniquepagescript'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/multistep-form.css')); ?>">
    <link href="<?php echo e(asset('assets/admin/bower_components/select2/dist/css/select2.min.css')); ?>" rel="stylesheet"/>
    <script src="<?php echo e(asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')); ?>"></script>

    <style type="text/css">
        .select2 {
            width: 100% !important;
        }

        #note {
            height: 80px !important;
        }

        .align_float_right {
            text-align: right;
        }

        .textData table tr:hover {
            background: #000 !important;
            color: white !important;
            cursor: pointer !important;
        }

        /* Modal styles */
        .modal-backdrop {
            z-index: 1040;
        }
        .modal {
            z-index: 1050;
        }
    </style>

    <script type="text/javascript">
        $(document).ready(function() {
            console.log('Document ready');
            
            // Initialize select2
            try {
                $('.mlselec6t').select2();
                console.log('Select2 initialized');
            } catch (e) {
                console.error('Error initializing select2:', e);
            }

            // Debug modal element
            console.log('Modal element exists:', $('#assign-supplier').length > 0);
            console.log('Modal HTML:', $('#assign-supplier').html());

            // Initialize modal
            try {
                $('#assign-supplier').modal({
                    show: false,
                    backdrop: 'static',
                    keyboard: false
                });
                console.log('Modal initialized');

                // Add modal event listeners
                $('#assign-supplier').on('show.bs.modal', function () {
                    console.log('Modal show event triggered');
                });

                $('#assign-supplier').on('shown.bs.modal', function () {
                    console.log('Modal shown event triggered');
                });

                $('#assign-supplier').on('hide.bs.modal', function () {
                    console.log('Modal hide event triggered');
                });

                $('#assign-supplier').on('hidden.bs.modal', function () {
                    console.log('Modal hidden event triggered');
                });
            } catch (e) {
                console.error('Error initializing modal:', e);
            }
        });

        function unassginSuppliers() {
            $('.assignBtn').text("Update Supplier");
            $('#list-suppliers').html('');
            $('.select_supplier option').attr('disabled', false);
            $('.select_supplier').val('').trigger('change.select2');
            return false;
        }

        function openAssignSupplier(userId, name) {
            console.log('Opening supplier modal for user:', userId, name);
            
            // Show loader
            $('.loder').show();
            
            // Clear any existing hidden inputs
            $('#assign-supplier form input[name="user_id"]').remove();
            
            // Add the user ID
            $('#assign-supplier form').append("<input type='hidden' name='user_id' value='" + userId + "'>");
            $('#user_name').html(name);
            $('.select_supplier option').attr('disabled', false);
            $('#list-suppliers').html("");
            $('.select_supplier').val('').trigger('change.select2');
            
            // Remove existing change event handler
            $('.select_supplier').off('change');
            
            // Add new change event handler
            $('.select_supplier').on('change', function() {
                var selectedValue = $(this).val();
                console.log('Supplier selected:', selectedValue);
                
                if (!selectedValue) return;
                
                $('.assignBtn').text("Assign Suppliers");
                
                if (selectedValue === "Select All") {
                    $('#list-suppliers').html('');
                    $('.select_supplier option').each(function(i, v) {
                        if ($(v).val() !== "Select All" && $(v).val() !== "") {
                            $('#list-suppliers').append('<li class="list-group-item">' +
                                '<input type="checkbox" name="supplier[]" checked value="' + $(v).val() + '"> ' + $('.select_supplier option[value="' + $(v).val() + '"]').html() +
                                '</li>');
                            $('.select_supplier option[value="' + $(v).val() + '"]').attr('disabled', true);
                        }
                    });
                } else {
                    $('#list-suppliers').append('<li class="list-group-item">' +
                        '<input type="checkbox" name="supplier[]" checked value="' + selectedValue + '"> ' + $('.select_supplier option[value="' + selectedValue + '"]').html() +
                        '</li>');
                    $('.select_supplier option[value="' + selectedValue + '"]').attr('disabled', true);
                }
                
                // Reset select2 to default option
                $(this).val('').trigger('change.select2');
            });
            
            $.ajax({
                url: '<?php echo e(route("admin.users.get_user_suppliers")); ?>',
                type: 'GET',
                dataType: 'json',
                data: {
                    id: userId
                },
                success: function (data) {
                    console.log('Received supplier data:', data);
                    
                    // Populate suppliers list
                    for (let i in data.data) {
                        $('#list-suppliers').append('<li class="list-group-item">' +
                            '<input type="checkbox" checked name="supplier[]" value="' + data.data[i].wa_supplier_id + '"> ' + data.data[i].supplier.name +
                            '</li>');
                        $('.select_supplier option[value="' + data.data[i].wa_supplier_id + '"]').attr('disabled', true);
                    }
                    
                    // Hide loader
                    $('.loder').hide();
                    
                    // Debug modal state before showing
                    console.log('Modal element exists before show:', $('#assign-supplier').length > 0);
                    console.log('Modal is initialized before show:', $('#assign-supplier').data('bs.modal') !== undefined);
                    
                    // Show modal
                    try {
                        $('#assign-supplier').modal('show');
                        console.log('Modal show called');
                        
                        // Force modal to be visible
                        $('#assign-supplier').css('display', 'block');
                        $('#assign-supplier').addClass('in');
                        $('body').addClass('modal-open');
                        $('.modal-backdrop').addClass('in');
                    } catch (e) {
                        console.error('Error showing modal:', e);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    $('.loder').hide();
                    alert('Error loading suppliers. Please try again.');
                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/users/index.blade.php ENDPATH**/ ?>