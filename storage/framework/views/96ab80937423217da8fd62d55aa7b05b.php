<aside class="main-sidebar">
    <?php
        $logged_user_info = getLoggeduserProfile();
        $my_permissions = $logged_user_info->permissions;
        $route_name = \Route::currentRouteName();
    ?>
    <section class="sidebar">
        <div class="user-panel">
            <div class="pull-left image">
                <?php if($logged_user_info->image && file_exists('uploads/users/thumb/' . $logged_user_info->image)): ?>
                    <img src="<?php echo e(asset('uploads/users/thumb/' . $logged_user_info->image)); ?>" class="img-circle"
                        alt="User Image">
                <?php else: ?>
                    <img src="<?php echo e(asset('assets/userdefault.jpg')); ?>" alt="User" class="img-circle">
                <?php endif; ?>
            </div>

            <div class="pull-left info">
                <p><?php echo ucfirst($logged_user_info->name); ?></p>
            </div>
        </div>

        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">MAIN NAVIGATION</li>
            <li><a href="<?php echo route('admin.dashboard'); ?>"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['management-dashboard___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'management-dashboard'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('admin.chairman-dashboard'); ?>">
                        <i class="fa fa-dashboard"></i>
                        <span>Management Dashboard</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php echo $__env->make('admin.includes.sidebar_includes.sales_and_receivables', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <?php echo $__env->make('admin.includes.sidebar_includes.logistics', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <?php echo $__env->make('admin.includes.sidebar_includes.purchases', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <?php echo $__env->make('admin.includes.sidebar_includes.supplier_portal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <?php echo $__env->make('admin.includes.sidebar_includes.accounts_payable', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <?php echo $__env->make('admin.includes.sidebar_includes.inventory', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <?php echo $__env->make('admin.includes.sidebar_includes.general_ledger', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <?php echo $__env->make('admin.includes.sidebar_includes.hr', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <?php echo $__env->make('admin.includes.sidebar_includes.fleet', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <?php echo $__env->make('admin.includes.sidebar_includes.help_desk', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <?php echo $__env->make('admin.includes.sidebar_includes.communication_centre', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <?php echo $__env->make('admin.includes.sidebar_includes.system_administration', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </ul>
    </section>
</aside>
<?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/includes/sidebar.blade.php ENDPATH**/ ?>