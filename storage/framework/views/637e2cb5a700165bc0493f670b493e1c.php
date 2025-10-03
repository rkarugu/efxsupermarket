<?php
    $user = request()->user();

    $isAdmin = $user->role_id == 1;
?>

<?php $__env->startSection('content'); ?>
    <div class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <?php echo $__env->make('message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <div class="row">
                    <div class="col-sm-9">
                        <form action="<?php echo e(route('admin.downloadExcel')); ?>" method="get">
                            <div class="row">
                                <div class="col-sm-3">
                                    <select name="branch" id="branch" class="form-control" <?php if(!$isAdmin && !isset($user->permissions['maintain-items___view-per-branch'])): ?> disabled <?php endif; ?>>
                                        <option value="">Select Branch</option>
                                        <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option 
                                                value="<?php echo e($branch->id); ?>"
                                                <?php if($branchId == $branch->id): ?> selected <?php endif; ?>
                                            >
                                                <?php echo e($branch->location_name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <select name="bin" id="bin" class="form-control" <?php if(!$isAdmin && !isset($user->permissions['maintain-items___view-per-branch'])): ?> disabled <?php endif; ?>>
                                        <option value="">Select bin</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <select name="category" id="category" class="form-control">
                                        <option value="">Select Category</option>
                                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($category->id); ?>"><?php echo e($category->category_description); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <select name="supplier" id="supplier" class="form-control">
                                        <option value="">Select Supplier</option>
                                        <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($supplier->id); ?>"><?php echo e($supplier->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <input type="hidden" name="productId" id="productId" value="<?php echo e(request()->productId); ?>"
                                    class="form-control" placeholder="Enter Product ID">
                                <div class="col-sm-1">
                                    <button type="submit" class="btn btn-primary" name="action" value="excel">
                                        <i class="fa fa-file-excel"></i>
                                        Excel
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-3">
                        <div class="text-right">
                            <?php if(can('add', $model)): ?>
                                <a href="<?php echo route($model . '.create'); ?>" class="btn btn-success">
                                    <i class="fa fa-plus"></i>
                                    Add Item</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-hover" id="inventoryItemsDataTable">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Stock ID Code</th>
                            <th>Title</th>
                            <th>Item Category</th>
                            <th>Pack Size</th>
                            <?php if(can('price_list_cost', 'maintain-items')): ?>
                                <th>Price List Cost</th>
                            <?php endif; ?>
                            <?php if(can('last_grn_cost', 'maintain-items')): ?>
                                <th>Last GRN Cost</th>
                            <?php endif; ?>
                            <?php if(can('weighted_average_cost', 'maintain-items')): ?>
                                <th>Weighted Cost</th>
                            <?php endif; ?>
                            <?php if(can('standard_cost', 'maintain-items')): ?>
                                <th>Standard Cost</th>
                            <?php endif; ?>
                            <th>Selling Price</th>
                            <th>QOH</th>
                            <th>QOO</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" role="dialog" id="stockStatusModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="statusItemCode"></h4>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Store</th>
                                <th>Quantity On Hand</th>
                                <th>Max Stock</th>
                                <th>Reorder Level</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4">Loading...</td>
                            </tr>
                        </tbody>
                        <tfoot>

                        </tfoot>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('styles'); ?>
    <link href="<?php echo e(asset('assets/admin/bower_components/select2/dist/css/select2.min.css')); ?>" rel="stylesheet" />
    <style>
        .span-action {
            display: inline-block;
            margin: 0 3px;
        }
    </style>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('scripts'); ?>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo e(asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')); ?>"></script>
    <script>
        const bins = <?php echo json_encode($bins, 15, 512) ?>;
        const user = <?php echo json_encode($user, 15, 512) ?>;
        const binId = <?php echo json_encode($binId, 15, 512) ?>;

        $(function() {
            $("#category, #supplier, #branch", "#bin").select2();
            $("#category, #supplier").change(function() {
                refreshTable();
            });

            let branch = $('#branch').val();
            
            if (branch) {
                let select = $('#bin');

                let branchBins = bins.filter(bin => bin.branch_id == branch);
                
                branchBins.forEach(function (bin) {
                    let option = $('<option></option>', {
                        value: bin.id,
                        text: bin.title,
                        selected: user.role_id == 152 && bin.id == binId
                    });
                    
                    select.append(option)
                })
            }

            var table = $('#inventoryItemsDataTable').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '<?php echo route('maintain-items.index'); ?>',
                    data: function(data) {
                        data.branch = $("#branch").val();
                        data.bin = $("#bin").val();
                        data.category = $("#category").val();
                        data.supplier = $("#supplier").val();
                        data.productId = $("#productId").val();
                    }
                },
                columns: [{
                        data: 'image',
                        name: 'image',
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'stock_id_code',
                        name: 'stock_id_code',
                    },
                    {
                        data: 'title',
                        name: 'title',
                    },
                    {
                        data: 'category.category_description',
                        name: 'category.category_description',
                        searchable: false,
                    },
                    {
                        data: 'pack_size.title',
                        name: 'packSize.title',
                        searchable: false,
                    },
                    <?php if(can('price_list_cost', 'maintain-items')): ?>
                        {
                            data: 'price_list_cost',
                            name: 'price_list_cost',
                            className: 'text-right',
                            searchable: false,
                        },
                    <?php endif; ?>
                    <?php if(can('last_grn_cost', 'maintain-items')): ?>
                        {
                            data: 'last_grn_cost',
                            name: 'last_grn_cost',
                            className: 'text-right',
                            searchable: false,
                        },
                    <?php endif; ?>
                    <?php if(can('weighted_average_cost', 'maintain-items')): ?>
                        {
                            data: 'weighted_average_cost',
                            name: 'weighted_average_cost',
                            className: 'text-right',
                            searchable: false,
                        },
                    <?php endif; ?>
                    <?php if(can('standard_cost', 'maintain-items')): ?>
                        {
                            data: 'standard_cost',
                            name: 'standard_cost',
                            className: 'text-right',
                            searchable: false,
                        },
                    <?php endif; ?> {
                        data: 'selling_price',
                        name: 'selling_price',
                        className: 'text-right',
                        searchable: false,
                    },
                    {
                        data: 'qty_on_hand',
                        name: 'items.qty_on_hand',
                        className: 'text-right',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'qty_on_order',
                        name: 'orders.qty_on_order',
                        className: 'text-right',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        searchable: false,
                        orderable: false,
                        width: "100px",
                        className: 'text-center'
                    },
                ],
            });

            $('.table tbody').on('click', '[data-toggle="delete"]', function() {
                let target = $(this).data('target');

                Swal.fire({
                    title: 'Confirm',
                    text: 'Are you sure want to delete the item?',
                    showCancelButton: true,
                    confirmButtonColor: '#252525',
                    cancelButtonColor: 'red',
                    confirmButtonText: 'Yes, Delete It',
                    cancelButtonText: `No, Cancel It`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(target).submit();
                    }
                })
            });

            $('.table tbody').on('click', '[data-toggle="item"]', function(e) {
                e.preventDefault();
                let itemCode = $(this).data('item-code');
                let itemTitle = $(this).data('item-title');
                $("#statusItemCode").text(itemCode + ' - ' + itemTitle);
                $("#stockStatusModal").modal('show')

                $.ajax({
                    url: "<?php echo e(route('maintain-items.item-stock-status')); ?>",
                    method: "GET",
                    data: {
                        "item_code": itemCode
                    },
                    success: function(response) {
                        let tbody = $("#stockStatusModal table tbody");
                        let tfoot = $("#stockStatusModal table tfoot");
                        let rows = '';
                        response.locations.forEach(function(location) {
                            rows += `
                            <tr>
                                <td>${location.location_name}</td>
                                <td>${location.qty_on_hand}</td>
                                <td>${location.max_stock}</td>
                                <td>${location.re_order_level}</td>
                            </tr>`;
                        });

                        tbody.html(rows);

                        tfoot.html(`
                            <tr>
                                <th>Total</th>
                                <th>${response.total_qty_on_hand}</th>
                                <td></td>
                                <td></td>
                            </tr>
                        `)
                    },
                    error: function(error) {

                    }
                });
            });

            $("#stockStatusModal").on('hide.bs.modal', function() {
                let tbody = $("#stockStatusModal table tbody");
                tbody.html('<tr><td colspan="4">Loading...</td></tr>')
            })
        });

        function refreshTable() {
            $("#inventoryItemsDataTable").DataTable().ajax.reload();
        }

        function fetchCompetingBrands(itemId) {
            $.ajax({
                url: 'fetch-competing-brands/' + itemId,
                method: 'GET',
                success: function(data) {
                    let tableBody = $('#competingBrandsTableBody');
                    $('#itemName').text(data.itemName);
                    tableBody.empty();
                    data.competingBrands.forEach(function(brand, index) {
                        let qoh = brand.qoh !== null ? brand.qoh : 0;
                        tableBody.append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${brand.stock_id_code}</td>
                                <td>${brand.title}</td>
                                <td>${brand.standard_cost}</td>
                                <td>${brand.selling_price}</td>
                                <td>${qoh}</td>
                            </tr>
                        `);
                    });
                },
                error: function() {
                    alert('Failed to fetch competing brands. Please try again later.');
                }
            });
        }

        function showCloneModal(itemId) {
            $('#cloneItemId').val(itemId);
            $('#cloneItemModal').modal('show');
        }

        $('#branch').on('change', function () {
            let branch = $(this).val();
            
            let select = $('#bin');

            select.empty();
            select.append(`
                <option value="">Select Bin</option>
            `)

            if (branch) {
                let branchBins = bins.filter(bin => bin.branch_id == $('#branch').val());
                
                branchBins.forEach(function (bin) {
                    let option = $('<option></option>', {
                        value: bin.id,
                        text: bin.title,
                    });
                    
                    select.append(option)
                })
            }
        })

        $('#bin').on('change', function () {
            refreshTable()
        })
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/maintain_items/index.blade.php ENDPATH**/ ?>