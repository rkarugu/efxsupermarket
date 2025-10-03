<?php if($logged_user_info->role_id == 1 || isset($my_permissions['fleet-management-module___view'])): ?>
    <li class="treeview <?php if(isset($model) &&
                    ($model == 'vehicles' ||
                        // $model == 'fueling-stations' ||
                        // $model == 'fuel-lpos' ||
                        // $model == 'pending-fuel-lpos' ||
                        // $model == 'verified-fuel-lpos' ||
                        // $model == 'approved-fuel-lpos' ||
                        $model == 'vehicle-suppliers' ||
                        $model == 'vehicles-overview' ||
                        // $model == 'fuel-suppliers' ||
                        $model == 'vehicle-command-center' ||
                        $model == 'vehicle-command-center-exemption-schedules' ||
                        $model == 'vehicle-command-center-custom-schedules' ||
                        // $model == 'confirmed-fuel-lpos' ||
                        // $model == 'expired-fuel-lpos' ||
                        $model == 'vehicle-models')): ?> active <?php endif; ?>">
        <a href="#"><i class="fa fa-truck"></i><span>Fleet Management </span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
        </a>

        <ul class="treeview-menu">
            <li class="treeview <?php if(isset($model) && ($model == 'vehicle-suppliers' ||  $model == 'vehicle-command-center-exemption-schedules' || $model == 'vehicle-command-center-custom-schedules' ||
                $model == 'vehicle-command-center' || $model == 'vehicles' || $model == 'vehicles-overview' || $model == 'vehicle-models')): ?> active <?php endif; ?>">
                <a href="#"><i class="fa fa-circle"></i><span> Fleet </span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>

                <ul class="treeview-menu">
                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['vehicles-overview___view'])): ?>
                        <li class="<?php if(isset($model) && $model == 'vehicles-overview'): ?> active <?php endif; ?>">
                            <a href="<?php echo e(route('vehicle-overview-all')); ?>"><i class="fa fa-circle" aria-hidden="true"></i>
                                Live Tracking
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['vehicles-overview___listing'])): ?>
                        <li class="<?php if(isset($model) && $model == 'vehicles'): ?> active <?php endif; ?>">
                            <a href="<?php echo route('vehicles.index'); ?>"><i class="fa fa-circle" aria-hidden="true"></i>
                                Vehicle Listing
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['vehicle-command-center___view'])): ?>
                        <li class="<?php if(isset($model) && $model == 'vehicle-command-center'): ?> active <?php endif; ?>">
                            <a href="<?php echo route('vehicle-command-center'); ?>"><i class="fa fa-circle" aria-hidden="true"></i>
                                Control Centre
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['vehicle-command-center___exemption-schedules'])): ?>
                        <li class="<?php if(isset($model) && $model == 'vehicle-command-center-exemption-schedules'): ?> active <?php endif; ?>">
                            <a href="<?php echo route('exemption-schedules'); ?>"><i class="fa fa-circle" aria-hidden="true"></i>
                                Exemption Schedules
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['vehicle-command-center-custom-schedules___view'])): ?>
                        <li class="<?php if(isset($model) && $model == 'vehicle-command-center-custom-schedules'): ?> active <?php endif; ?>">
                            <a href="<?php echo route('custom-schedules'); ?>"><i class="fa fa-circle" aria-hidden="true"></i>
                                Custom Schedules
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['vehicle-suppliers___view']) || isset($my_permissions['vehicle-models___view'])): ?>
                        <li class="treeview <?php if(isset($model) && ($model == 'vehicle-suppliers' ||  $model == 'vehicle-models')): ?> active <?php endif; ?>">
                            <a href="#"><i class="fa fa-circle"></i><span> Set Up </span>
                                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                            </a>
                            <ul class="treeview-menu">
                                <?php if($logged_user_info->role_id == 1 || isset($my_permissions['vehicle-suppliers___view'])): ?>
                                    <li class="<?php if(isset($model) && $model == 'vehicle-suppliers'): ?> active <?php endif; ?>">
                                        <a href="<?php echo route('vehicle-suppliers.index'); ?>"><i class="fa fa-circle" aria-hidden="true"></i>
                                            Vehicle Suppliers
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if($logged_user_info->role_id == 1 || isset($my_permissions['vehicle-models___view'])): ?>
                                    <li class="<?php if(isset($model) && $model == 'vehicle-models'): ?> active <?php endif; ?>">
                                        <a href="<?php echo route('vehicle-models.index'); ?>"><i class="fa fa-circle" aria-hidden="true"></i>
                                            Vehicle Models
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>

            
        </ul>
    </li>
<?php endif; ?><?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/includes/sidebar_includes/fleet.blade.php ENDPATH**/ ?>