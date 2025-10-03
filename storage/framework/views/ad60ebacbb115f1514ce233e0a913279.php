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
            <?php if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin'): ?>
            <div align = "right"> <a href = "<?php echo route($model.'.create'); ?>" class = "btn btn-success">Add <?php echo $title; ?></a></div>
            <?php endif; ?>
            <br>
            <?php echo $__env->make('message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="col-md-12 no-padding-h">
                <table class="table table-bordered table-hover" id="dataTable">
                    <thead>
                        <tr>
                            <th width="20%"  >Supplier Code</th>
                            <th width="20%"  >Name</th>
                            <th width="20%"  >Address</th>
                            <th  width="20%" class="noneedtoshort" >Action</th>
                        </tr>
                    </thead>

                    

                </table>
            </div>
        </div>
    </div>

</section>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('uniquepagescript'); ?>
<div id="loader-on" style="
position: fixed;
top: 0;
text-align: center;
display: block;
z-index: 999999;
width: 100%;
height: 100%;
background: #000000b8;
display:none;
" class="loder">
  <div class="loader" id="loader-1"></div>
</div>
<script src="<?php echo e(asset('js/sweetalert.js')); ?>"></script>
<script src="<?php echo e(asset('js/form.js')); ?>"></script>
<script type="text/javascript">
    $(function() {
    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        order: [[0, "desc" ]],
        pageLength: '<?= Config::get('params.list_limit_admin') ?>',
        "ajax":{
        "url": '<?php echo route('maintain-suppliers.datatable'); ?>',
                "dataType": "json",
               
                "data":{ _token: "<?php echo e(csrf_token()); ?>",'is_verified':'no'}
        },
        columns: [
        { data: 'supplier_code', name: 'supplier_code', orderable:true },
        { data: 'name', name: 'name', orderable:true  },
        { data: 'address', name: 'address', orderable:true },
        { data: 'actions', name: 'actions', orderable:false},
        ],
        "columnDefs": [
            { "searchable": false, "targets": 0 },
            { className: 'text-center', targets: [1] }
        ]
        , language: {
            searchPlaceholder: "Search"
        },
    });
});
    
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/maintainsuppliers/supplier_unverified_list.blade.php ENDPATH**/ ?>