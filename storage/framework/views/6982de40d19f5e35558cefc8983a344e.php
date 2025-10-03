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
                    <div class="col-md-3">
                        <h3 class="box-title"> <?php echo $title; ?> </h3>
                    </div>
                    <div class="col-sm-9">
                        <div align="right">
                            <form action="<?php echo e(route('admin.show.item.log')); ?>" method="GET">
                                <div>
                                    <div class="col-md-12 no-padding-h">
                                        <div class="col-sm-6"></div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="start-date">Start Date</label>
                                                <input type="date" class="form-control" placeholder="Start Date" name="start-date" id="start-date" value="<?php echo e(request()->get('start-date')??null); ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="">End Date</label>
                                                <input type="date" class="form-control" placeholder="End Date" name="end-date" id="end-date" value="<?php echo e(request()->get('end-date')??null); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            
                        </div>
                    </div>
                </div>
                <br>
                <?php echo $__env->make('message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="dataTable">
                        <thead>
                            <tr>
                                <th>Requested By</th>
                                <th>Requested On</th>
                                <th>Status</th>
                                <th>Stock ID Code</th>
                                <th>Title</th>
                                <th>Item Category</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>
    </section>   
<?php $__env->stopSection(); ?>

<?php $__env->startSection('uniquepagestyle'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/multistep-form.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/dist/datepicker.css')); ?>">
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('uniquepagescript'); ?>
    <script type="text/javascript">
        $(function() {
            let table = $("#dataTable").DataTable({
            processing: true,
            serverSide: true,
            order: [
                [0, "asc"]
            ],
            autoWidth: false,
            pageLength: '<?= Config::get('params.list_limit_admin') ?>',
            ajax: {
                url: '<?php echo route('admin.show.item.log.datatable'); ?>',
                data: function(data) {
                    data.start_date = $("#start-date").val();
                    data.end_date = $("#end-date").val();
                }
            },
            columns: [{
                        data: 'approval_by.name',
                        name: 'approvalBy.name',
                    },
                    {
                        data: 'requested_on',
                        name: 'requested_on',
                    },
                    {
                        data: 'status',
                        name: 'status',
                    },
                    {
                        data: 'inventory_item.stock_id_code',
                        name: 'inventoryItem.stock_id_code',
                    },
                    {
                        data: 'inventory_item.title',
                        name: 'inventoryItem.title',
                    },
                    {
                        data: 'item_category',
                        name: 'item_category',
                    },
                {
                        data: null,
                        orderable: false,
                        searchable: false
                }
            ],
            columnDefs: [
                    
                    {
                        targets: -1,
                        render: function (data, type, row, meta) {
                            if (type === 'display') {
                                var actions = '<div class="d-flex">';
                                    var url = "<?php echo e(route('admin.show_item_log.view',':id')); ?>";
                                    url = url.replace(':id', row.id);
                                    actions += `<a href="`+url+`" title="view"><i class="fa fa-solid fa-eye"></i></a>`;
                                actions +='</div>';
                                return actions;
                            }
                            return data;
                        }
                    }
            ],
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();
                var json = api.ajax.json();

                $("#grandTotal").text(json.grand_total);
            }
        });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/maintaininvetoryitems/approval/item_history.blade.php ENDPATH**/ ?>