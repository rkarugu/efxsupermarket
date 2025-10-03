<?php
    $allModels = [
        'fueling-stations',
        'fuel-lpos',
        'fuel-suppliers', 
        'consumption-report', 
        'fuel-entries', 
        'fuel-statements', 
        'fuel-verification', 
        'fuel-approval', 
        'small-packs-store-loading-sheets', 
        'small-packs-dispatched-loading-sheets',
        'small-packs-view-loading-sheets',
        'device-type',
        'device-center',
        'device-sim-card',
        'device-repair',];
?>
 
<?php if($logged_user_info->role_id == 1 || isset($my_permissions['delivery_and_logistics___view'])): ?>
    <li class="treeview <?php if(isset($model) && in_array($model, $allModels)): ?> active <?php endif; ?>">
        <a href="#"><i class="fa fa-truck-loading"></i><span> Delivery & Logistics </span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
        </a>

        <ul class="treeview-menu">
            <li class="treeview <?php if(isset($model) && in_array($model, ['fuel-suppliers', 'fueling-stations', 'fuel-lpos', 'fuel-entries', 'consumption-report', 'fuel-statements',
'fuel-verification', 'fuel-approval'])): ?> active <?php endif; ?>">
                <a href="#"><i class="fa fa-circle"></i><span> Fuel Management </span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>

                <ul class="treeview-menu">
                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['fuel-entries___see-overview'])): ?>
                        <li class="<?php if(isset($model) && $model == 'pending-fuel-lpos'): ?> active <?php endif; ?>">
                            <a href="<?php echo e(route('fuel-entry-confirmation.overview')); ?>"><i class="fa fa-circle" aria-hidden="true"></i>
                                Overview
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="treeview <?php if(isset($model) && in_array($model, ['fuel-statements', 'fuel-verification'])): ?> active <?php endif; ?>">
                        <a href="#"><i class="fa fa-circle"></i><span> Verification </span>
                            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                        </a>

                        <ul class="treeview-menu">
                            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['fuel-statements___view'])): ?>
                                <li class="<?php if(isset($model) && $model == 'fuel-statements'): ?> active <?php endif; ?>">
                                    <a href="<?php echo e(route('fuel-statements.listing')); ?>"><i class="fa fa-circle" aria-hidden="true"></i>
                                        Fuel Statements
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['fuel-verification___view'])): ?>
                                <li class="<?php if(isset($model) && $model == 'fuel-verification'): ?> active <?php endif; ?>">
                                    <a href="<?php echo e(route('fuel-verification.listing')); ?>"><i class="fa fa-circle" aria-hidden="true"></i>
                                        Verification Records
                                    </a>
                                </li>
                            <?php endif; ?>

                            
                            
                            
                            
                            
                            
                            

                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                        </ul>

                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['fuel-approval___approve'])): ?>
                        <li class="<?php if(isset($model) && $model == 'fuel-approval'): ?> active <?php endif; ?>">
                            <a href="<?php echo e(route('fuel-approval.index')); ?>"><i class="fa fa-circle" aria-hidden="true"></i>
                                Approval
                            </a>
                        </li>
                <?php endif; ?>
            </li>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['fuel-suppliers___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'fuel-suppliers'): ?> active <?php endif; ?>">
                    <a href="<?php echo e(route('fuel-suppliers.index')); ?>"><i class="fa fa-circle" aria-hidden="true"></i>
                        Fuel Suppliers
                    </a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['fuel-suppliers___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'fueling-stations'): ?> active <?php endif; ?>">
                    <a href="<?php echo e(route('fuel-stations.index')); ?>"><i class="fa fa-circle" aria-hidden="true"></i>
                        Fueling Stations
                    </a>
                </li>
            <?php endif; ?>

            
            
            
            
            
            
            
        </ul>
    </li>
    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['small-packs___view'])): ?>
        <li class="treeview <?php if(isset($model) &&
                ($model == 'small-packs-store-loading-sheets' ||
                $model == 'small-packs-dispatched-loading-sheets' ||
                $model == 'view-loading-sheets' ||
                $model == 'dispatched-loading-sheets' ||
                $model == 'dispatched-sheets-view')): ?> active <?php endif; ?>">
            <a href="#">
                <i class="fa fa-circle"></i>Small Packs
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>

            <ul class="treeview-menu">
                <?php if($logged_user_info->role_id == 1 || isset($my_permissions['small-packs___store-loading-sheets'])): ?>
                    <li class="<?php if(isset($model) && $model == 'small-packs-store-loading-sheets'): ?> active <?php endif; ?>">
                        <a href="<?php echo route('small-packs.store-loading-sheets'); ?>">
                            <i class="fa fa-circle"></i>Dispatch Requests
                        </a>
                    </li>
                <?php endif; ?>
                <?php if($logged_user_info->role_id == 1 || isset($my_permissions['small-packs___dispatched-loading-sheets'])): ?>
                    <li class="<?php if(isset($model) && $model == 'small-packs-dispatched-loading-sheets'): ?> active <?php endif; ?>">
                        <a href="<?php echo route('small-packs.dispatched'); ?>">
                            <i class="fa fa-circle"></i>Dispatched Loading Sheets
                        </a>
                    </li>
                <?php endif; ?>
                
            </ul>
        </li>
    <?php endif; ?>
    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['delivery_and_logistics___reports'])): ?>
        <li class="<?php if(isset($model) && $model == 'custom-delivery-shifts'): ?> active <?php endif; ?>">
            <a href="<?php echo route('vehicle-suppliers.index'); ?>"><i class="fa fa-circle" aria-hidden="true"></i>
                Reports
            </a>
        </li>
        <?php endif; ?>
    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['device-management___view'])): ?>
        <li class="treeview <?php if(isset($model) &&
                ($model == 'device-type' || $model == 'device-sim-card' || $model == 'device-center' || $model == 'device-repair')): ?> active <?php endif; ?>">
            <a href="#">
                <i class="fa fa-circle"></i>Device Management
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>

            <ul class="treeview-menu">
                <?php if($logged_user_info->role_id == 1 || isset($my_permissions['device-type___view'])): ?>
                    <li class="<?php if(isset($model) && $model == 'device-type'): ?> active <?php endif; ?>">
                        <a href="<?php echo route('device-type.index'); ?>">
                            <i class="fa fa-circle"></i>Device Type
                        </a>
                    </li>
                <?php endif; ?>
                <?php if($logged_user_info->role_id == 1 || isset($my_permissions['device-sim-card___view'])): ?>
                    <li class="<?php if(isset($model) && $model == 'device-sim-card'): ?> active <?php endif; ?>">
                        <a href="<?php echo route('device-sim-card.index'); ?>">
                            <i class="fa fa-circle"></i>Device Sim Card
                        </a>
                    </li>
                <?php endif; ?>
                <?php if($logged_user_info->role_id == 1 || isset($my_permissions['device-repair___view'])): ?>
                    <li class="<?php if(isset($model) && $model == 'device-repair'): ?> active <?php endif; ?>">
                        <a href="<?php echo route('device-repair.index'); ?>">
                            <i class="fa fa-circle"></i>Device Repair
                        </a>
                    </li>
                <?php endif; ?>
                <?php if($logged_user_info->role_id == 1 || isset($my_permissions['device-center___view'])): ?>
                    <li class="<?php if(isset($model) && $model == 'device-center'): ?> active <?php endif; ?>">
                        <a href="<?php echo route('device-center.index'); ?>">
                            <i class="fa fa-circle"></i>Device Center
                        </a>
                    </li>
                <?php endif; ?>
                
            </ul>
        </li>
    <?php endif; ?>
    
        </ul>
        </li>
    <?php endif; ?><?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/includes/sidebar_includes/logistics.blade.php ENDPATH**/ ?>