<?php if($logged_user_info->role_id == 1 || isset($my_permissions['purchases___view'])): ?>
    <li
        class="treeview <?php if(isset($model) &&
                ($model == 'external-requisitions' ||
                    $model == 'purchases-reports' ||
                    $model == 'approve-external-requisitions' ||
                    $model == 'purchase-orders' ||
                    $model == 'approve-lpo' ||
                    $model == 'resolve-requisition-to-lpo' ||
                    $model == 'lpo-status-and-leadtime-report' ||
                    $model == 'pending-suppliers' ||
                    $model == 'suggested_order' ||
                    $model == 'archived-lpo' ||
                    $model == 'completed-lpo' ||
                    $model == 'purchase-order-status' ||
                    $model == 'suggested_order' ||
                    $model == 'order-delivery-slots')): ?> active
                <?php else: ?>
                <?php if(isset($rmodel) &&
                        ($rmodel == 'purchases-by-store-location' ||
                            $rmodel == 'purchases-by-family-group' ||
                            $rmodel == 'purchases-by-supplier')): ?> active <?php endif; ?>
                <?php endif; ?>">

        <a href="#"><i class="fa fa-fw fa-cart-arrow-down"></i><span>Purchases</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['purchase_requisitions___view'])): ?>
                <li class="treeview <?php if(isset($model) &&
                        ($model == 'external-requisitions' ||
                            $model == 'approve-external-requisitions' ||
                            $model == 'suggested-orders' ||
                            $model == 'resolve-requisition-to-lpo')): ?> active <?php endif; ?>">
                    <a href="#"><i class="fa fa-share"></i> Branch Requisitions
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['external-requisitions___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'external-requisitions'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('external-requisitions.index'); ?>"><i class="fa fa-circle"></i>
                                    Initiate Requisitions</a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['external-requisitions___archived-requisition'])): ?>
                            <li class="<?php if(isset($model) && $model == 'external-requisitions'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('external-requisitions.archivedRequisition'); ?>"><i class="fa fa-circle"></i>
                                    Archived Branch Requisitions</a></li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['approve-external-requisitions___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'approve-external-requisitions'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('approve-external-requisitions.index'); ?>"><i class="fa fa-circle"></i> Approve
                                    Branch Requisition</a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['resolve-requisition-to-lpo___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'resolve-requisition-to-lpo'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('resolve-requisition-to-lpo.index'); ?>">
                                    <i class="fa fa-circle"></i> Resolve Requisition to LPO
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['suggested-orders___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'suggested-orders'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('branch-requisitions.suggested-orders'); ?>">
                                    <i class="fa fa-circle"></i> Suggested Orders
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['external-requisitions___external-requisition-report'])): ?>
                            <li class="<?php if(isset($model) && $model == 'external-requisition-report'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('externalRequisitionReport'); ?>"><i class="fa fa-circle"></i> Status
                                    Report </a>
                            </li>
                        <?php endif; ?>


                    </ul>
                </li>
            <?php endif; ?>
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['purchase_orders_module___view'])): ?>
                <li class="treeview <?php if(isset($model) &&
                        ($model == 'purchase-orders' ||
                            $model == 'archived-lpo' ||
                            $model == 'completed-lpo' ||
                            $model == 'purchase-order-status' ||
                            $model == 'approve-lpo')): ?> active <?php endif; ?>">
                    <a href="#"><i class="fa fa-share"></i> Purchase Orders
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['purchase-orders___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'purchase-orders'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('purchase-orders.index'); ?>"><i class="fa fa-circle"></i> New
                                    Purchase Order</a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['approve-lpo___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'approve-lpo'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('approve-lpo.index'); ?>"><i class="fa fa-circle"></i>
                                    Approve LPOs</a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['archived-lpo___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'archived-lpo'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('purchase-orders.archived-lpo'); ?>"><i class="fa fa-circle"></i>
                                    Archived LPOs</a></li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['completed-lpo___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'completed-lpo'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('purchase-orders.completed-lpo'); ?>"><i class="fa fa-circle"></i>
                                    Completed LPOs</a></li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['purchase-order-status___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'purchase-order-status'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('purchase-orders.status_report'); ?>"><i class="fa fa-circle"></i>
                                    Status Report</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['purchases-reports___view'])): ?>
                <li
                    class="<?php if(isset($rmodel) &&
                            ($rmodel == 'purchases-by-store-location' ||
                                $rmodel == 'purchases-by-family-group' ||
                                $rmodel == 'purchases-by-supplier')): ?> active <?php else: ?>
                                        <?php if(isset($model) && ($model == 'purchases-reports' || $model == 'lpo-status-and-leadtime-report')): ?> active <?php endif; ?> <?php endif; ?>">
                    <a href="<?php echo route('purchases-reports.index'); ?>"><i class="fa fa-circle"></i>
                        Reports
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </li>
<?php endif; ?>
<?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/includes/sidebar_includes/purchases.blade.php ENDPATH**/ ?>