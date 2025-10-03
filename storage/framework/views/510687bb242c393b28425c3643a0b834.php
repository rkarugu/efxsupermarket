<?php if($logged_user_info->role_id == 1 || isset($my_permissions['supplier-portal___view'])): ?>
    <li class="treeview <?php if(isset($model) &&
            ($model == 'lpo-portal-req-approval' ||
                $model == 'supplier-portal' ||
                $model == 'suggested-orders' ||
                $model == 'email-templates' ||
                $model == 'pending-suppliers' ||
                $model == 'request-new-sku' ||
                $model == 'api-call-logs' ||
                $model == 'supplier-vehicle-type' ||
                $model == 'approve-bank-deposits' ||
                $model == 'supplier-bank-deposits-initial-approval' ||
                $model == 'supplier-bank-deposits-final-approval' ||
                $model == 'billing-submitted' ||
                $model == 'billing-submitted-final' ||
                $model == 'order-delivery-slots')): ?> active <?php endif; ?>">

        <a href="#"><i class="fa fa-cubes"></i><span>Supplier Portal</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>

        <ul class="treeview-menu">
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['supplier-maintain-suppliers___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'supplier-portal'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('supplier-portal.get_all_supplier_from_portal'); ?>">
                        <i class="fa fa-circle"></i> Onboarded Suppliers
                    </a>
                </li>
            <?php endif; ?>
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['pending-suppliers___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'pending-suppliers'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('supplier-portal.pending-suppliers'); ?>">
                        <i class="fa fa-circle"></i> Pending Invites
                    </a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['order-delivery-slots___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'order-delivery-slots'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('order-delivery-slots.delivery_branches'); ?>">
                        <i class="fa fa-circle"></i> LPO Delivery Slots
                    </a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['supplier-vehicle-type___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'supplier-vehicle-type'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('supplier-vehicle-type.index'); ?>">
                        <i class="fa fa-circle"></i> Vehicle Type
                    </a>
                </li>
            <?php endif; ?>
            
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['suggested-order___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'suggested_order'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('suggested-order.index'); ?>"><i class="fa fa-circle"></i>
                        Suggested Order</a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['order-delivery-slots___show'])): ?>
                <li class="<?php if(isset($model) && $model == 'order-delivery-slots'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('order-delivery-slots.show_booked_slots'); ?>">
                        <i class="fa fa-circle"></i> LPO Booked Slots
                    </a>
                </li>
            <?php endif; ?>
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['lpo-portal-req-approval___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'lpo-portal-req-approval'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('lpo-portal-req-approval.index'); ?>"><i class="fa fa-circle"></i>
                        Approve LPO Changes </a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['price-list-cost-change___view'])): ?>
                <li class="<?php if((isset($model) && $model == 'price-list-cost-change') || $model == 'maintain-items-manual-cost-change'): ?> active <?php endif; ?>"><a href="<?php echo route('maintain-items.approve-price-list-change'); ?>"><i
                            class="fa fa-circle"></i>
                        Aprrove Price List Changes</a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['api-call-logs___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'api-call-logs'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('api_call_logs.index'); ?>">
                        <i class="fa fa-circle"></i> Api Call Logs
                    </a>
                </li>
            <?php endif; ?>

            

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['email-templates___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'email-templates'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('admin.email_templates.index'); ?>">
                        <i class="fa fa-circle"></i> Email Templates
                    </a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['billing-description_view'])): ?>
                <li class="<?php if(isset($model) && $model == 'billing-description'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('supplier-portal.billing-description'); ?>">
                        <i class="fa fa-circle"></i> Billing Note
                    </a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['request-new-sku___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'request-new-sku'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('request-new-sku.index'); ?>">
                        <i class="fa fa-circle"></i> New SKU's Requests
                    </a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['approve-bank-deposits___view'])): ?>
                <li class="treeview <?php if(
                    (isset($model) && $model == 'approve-bank-deposits') ||
                        $model == 'supplier-bank-deposits-initial-approval' ||
                        $model == 'supplier-bank-deposits-final-approval'): ?> active <?php endif; ?>">
                    <a href="#"><i class="fa fa-circle"></i> Approve Bank Desposits
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['supplier-bank-deposits-initial-approval___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'supplier-bank-deposits-initial-approval'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('supplier_bank_deposits_initial_approval.index'); ?>"><i class="fa fa-circle"></i>
                                    Initial Approval</a></li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['supplier-bank-deposits-final-approval___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'supplier-bank-deposits-final-approval'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('supplier_bank_deposits_final_approval.index'); ?>"><i class="fa fa-circle"></i>
                                    Final Approval</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['supplier-billing___view'])): ?>
                <li class="treeview <?php if(
                    (isset($model) && $model == 'supplier-billing') ||
                        $model == 'billing-submitted' ||
                        $model == 'billing-submitted-final'): ?> active <?php endif; ?>">
                    <a href="#"><i class="fa fa-circle"></i> Billing
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['billing-submitted___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'billing-submitted'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('billings_submitted'); ?>"><i class="fa fa-circle"></i>
                                    Initial Approval Billings</a></li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['billing-submitted-final___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'billing-submitted-final'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('billings_submitted_final'); ?>"><i class="fa fa-circle"></i>
                                    Final Approval Billings</a></li>
                        <?php endif; ?>
                        
                    </ul>
                </li>
            <?php endif; ?>

        </ul>
    </li>
<?php endif; ?>
<?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/includes/sidebar_includes/supplier_portal.blade.php ENDPATH**/ ?>