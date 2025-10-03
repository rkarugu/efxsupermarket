<?php if($logged_user_info->role_id == 1 || isset($my_permissions['financial-management___view'])): ?>
    <li class="treeview <?php if(isset($model) &&
            ($model == 'route-manager' ||
                $model == 'pack-size' ||
                $model == 'account-sections' ||
                $model == 'account-groups' ||
                $model == 'branches' ||
                $model == 'departments' ||
                $model == 'company-preferences' ||
                $model == 'settings' ||
                $model == 'tax-manager' ||
                $model == 'currency-managers' ||
                $model == 'accounting-periods' ||
                $model == 'stock-type-categories' ||
                $model == 'stock-family-groups' ||
                $model == 'inventory-categories' ||
                $model == 'category' ||
                $model == 'unit-of-measures' ||
                $model == 'payment-terms' ||
                $model == 'item-sub-categories' ||
                $model == 'location-and-stores' ||
                $model == 'priority-level' ||
                $model == 'payment-methods' ||
                $model == 'payment-providers' ||
                $model == 'number-series' ||
                $model == 'roles' ||
                $model == 'employees' ||
                $model == 'user-denied-accesses' ||
                $model == 'processes' ||
                $model == 'manage-routes' ||
                $model == 'sub-account-sections' ||
                $model == 'alerts' ||
                $model == 'scheduled-notifications' ||
                $model == 'teams' ||
                $model == 'manage-delivery-centers' ||
                $model == 'wallet-matrix' ||
                $model == 'petty-cash-type' ||
                $model == 'petty-cash-request-types' ||
                $model == 'payment-modes' ||
                $model == 'loaders' ||
                $model == 'gl_tags' ||
                $model == 'return-reasons' ||
                $model == 'log-in-activity' ||
                $model == 'promotion-groups' ||
                $model == 'promotion-types' ||
                $model == 'active-promotions' ||
                $model == 'hampers' ||
                $model == 'cheque-bank' ||
                $model == 'projects' ||
                $model == 'support-team' ||
                $model == 'ticket-category' ||
                $model == 'activity-logs')): ?> active <?php endif; ?>">
        <a href="#"><i class="fa fa-fw fa-server"></i><span>System Administration</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['general-ledger___view'])): ?>
                <li class="treeview <?php if(isset($model) &&
                        ($model == 'account-sections' ||
                            $model == 'account-groups' ||
                            $model == 'chart-of-accounts' ||
                            $model == 'branches' ||
                            $model == 'wallet-matrix' ||
                            $model == 'petty-cash-type' ||
                            $model == 'petty-cash-request-types' ||
                            $model == 'sub-account-sections' ||
                            $model == 'departments' ||
                            $model == 'gl_tags' ||
                            $model == 'projects')): ?> active <?php endif; ?>">
                    <a href="#"><i class="fa fa-circle"></i> General Ledger
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['account-sections___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'account-sections'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('account-sections.index'); ?>"><i class="fa fa-circle"></i>
                                    Account Groups </a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['account-groups___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'account-groups'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('account-groups.index'); ?>"><i class="fa fa-circle"></i>
                                    Account Sections </a>
                            </li>
                        <?php endif; ?>

                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['account-groups___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'sub-account-sections'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('sub-account-sections.index'); ?>"><i class="fa fa-circle"></i>Account Sub
                                    Sections</a></li>
                        <?php endif; ?>


                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['dimensions___view'])): ?>
                            <li class="treeview <?php if(isset($model) && ($model == 'branches' || $model == 'departments' || $model == 'projects' || $model == 'gl_tags')): ?> active <?php endif; ?>">
                                <a href="#"><i class="fa fa-share"></i> Dimensions<span
                                        class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">
                                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['branches___view'])): ?>
                                        <li class="<?php if(isset($model) && $model == 'branches'): ?> active <?php endif; ?>">
                                            <a href="<?php echo route('branches.index'); ?>"><i class="fa fa-circle"></i>
                                                <span>Branches</span></a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['departments___view'])): ?>
                                        <li class="<?php if(isset($model) && $model == 'departments'): ?> active <?php endif; ?>">
                                            <a href="<?php echo route('departments.index'); ?>"><i class="fa fa-circle"></i>
                                                Departments</a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['projects___view'])): ?>
                                        <li class="<?php if(isset($model) && $model == 'projects'): ?> active <?php endif; ?>">
                                            <a href="<?php echo route('projects.index'); ?>"><i class="fa fa-circle"></i>
                                                Projects</a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['gl_tags___view'])): ?>
                                        <li class="<?php if(isset($model) && $model == 'gl_tags'): ?> active <?php endif; ?>">
                                            <a href="<?php echo route('gl_tags.index'); ?>"><i class="fa fa-circle"></i>
                                                Gl Tags</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['wallet-matrix___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'wallet-matrix'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('wallet-matrix.index'); ?>"><i class="fa fa-circle"></i>Wallet
                                    Matrix</a></li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['petty-cash___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'petty-cash-type'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('petty-cash-types.index'); ?>"><i class="fa fa-circle"></i>Wallet
                                    Types</a></li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['petty-cash-request-types___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'petty-cash-request-types'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('petty-cash-request.types'); ?>">
                                    <i class="fa fa-circle"></i>
                                    Petty Cash Request Types
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['finanacial-system-setup___view'])): ?>
                <li class="treeview <?php if(isset($model) &&
                        ($model == 'company-preferences' ||
                            $model == 'settings' ||
                            $model == 'tax-manager' ||
                            $model == 'currency-managers' ||
                            $model == 'accounting-periods' ||
                            $model == 'number-series' ||
                            $model == 'roles' ||
                            $model == 'user-denied-accesses' ||
                            $model == 'teams' ||
                            $model == 'employees' ||
                            $model == 'return-reasons' ||
                            $model == 'log-in-activity' ||
                             $model == 'cheque-bank' ||
                            $model == 'loaders'||
                            $model == 'support-team' ||
                            $model == 'ticket-category' ||
                            $model == 'activity-logs')): ?> active <?php endif; ?>">
                    <a href="#">
                        <i class="fa fa-circle"></i> System Setup<span class="pull-right-container"><i
                                class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['account-sections___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'company-preferences'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('company-preferences.index'); ?>"><i class="fa fa-circle"></i>
                                    Company
                                    Preferences </a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['settings___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'settings'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('settings.index'); ?>"><i class="fa fa-circle"></i>
                                    Settings
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['tax-manager___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'tax-manager'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('tax-manager.index'); ?>"><i class="fa fa-circle"></i>
                                    <span>Tax Manager</span></a></li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['currency-managers___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'currency-managers'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('currency-managers.index'); ?>"><i class="fa fa-circle"></i>
                                    <span>Currency Managers</span></a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['accounting-periods___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'accounting-periods'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('accounting-periods.index'); ?>"><i class="fa fa-circle"></i>
                                    <span>Accounting Periods</span></a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['number-series___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'number-series'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('number-series.index'); ?>"><i class="fa fa-circle"></i>
                                    <span>Number Series</span></a></li>
                        <?php endif; ?>

                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['roles___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'roles'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('roles.index'); ?>"><i class="fa fa-circle"></i>
                                    <span>Roles</span></a></li>
                        <?php endif; ?>

                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['employees___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'employees'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('employees.index'); ?>"><i class="fa fa-circle"></i>
                                    <span>Employees</span></a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['employees___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'loaders'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('loaders.index'); ?>"><i class="fa fa-circle"></i>
                                    <span>Loaders</span></a>
                            </li>
                        <?php endif; ?>

                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['teams___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'teams'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('teams.index'); ?>"><i class="fa fa-circle"></i><span>Teams</span></a>
                            </li>
                        <?php endif; ?>

                        <?php if($logged_user_info->role_id == 1): ?>
                            <li class="<?php if(isset($model) && $model == 'employees'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('userlog.index'); ?>"><i class="fa fa-circle"></i>
                                    <span>User Logs</span></a></li>
                        <?php endif; ?>



                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['log-in-activity___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'log-in-activity'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('user-login-activity-report'); ?>"><i class="fa fa-circle"></i>
                                    <span>Log in Activity</span></a></li>
                        <?php endif; ?>

                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['activity-logs___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'activity-logs'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('activitylogs.index'); ?>"><i class="fa fa-circle"></i>
                                    <span>Activity Logs</span></a></li>
                        <?php endif; ?>

                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['return-reasons___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'return-reasons'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('return-reasons.index'); ?>"><i class="fa fa-circle"></i>
                                    <span>Return Reasons</span></a></li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['cheque-bank___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'cheque-bank'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('cheque-banks'); ?>"><i class="fa fa-circle"></i>
                                    <span>Banks </span></a></li>
                        <?php endif; ?>

                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['help-desk-setup___view'])): ?>
                        <li class="treeview <?php if(isset($model) && $model == 'support-team' || $model == 'ticket-category'): ?> active <?php endif; ?>">
                            <a href="#"><i class="fa fa-circle"></i> Help Desk Setup
                                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                            </a>
                            <ul class="treeview-menu">
                                <?php if($logged_user_info->role_id == 1 || isset($my_permissions['support-team___view'])): ?>
                                    <li class="<?php if(isset($model) && $model == 'support-team'): ?> active <?php endif; ?>"><a
                                            href="<?php echo route('support-team.index'); ?>"><i class="fa fa-circle"></i>
                                            <span>Support Team</span></a></li>
                                <?php endif; ?>
                                <?php if($logged_user_info->role_id == 1 || isset($my_permissions['ticket-category___view'])): ?>
                                    <li class="<?php if(isset($model) && $model == 'ticket-category'): ?> active <?php endif; ?>"><a
                                            href="<?php echo route('ticket-category.index'); ?>"><i class="fa fa-circle"></i>
                                            <span>Ticket Category</span></a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>

                            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['incentive-settings___view'])): ?>
                                <li class="<?php if(isset($model) && $model == 'incentive-settings'): ?> active <?php endif; ?>"><a
                                            href="<?php echo route('incentive-settings.index'); ?>"><i class="fa fa-circle"></i>
                                        <span>Incentives</span></a></li>
                            <?php endif; ?>
                        
                    </ul>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['finanacial-inventory___view'])): ?>
                <li class="treeview <?php if(isset($model) &&
                        ($model == 'pack-size' ||
                            $model == 'stock-type-categories' ||
                            $model == 'stock-family-groups' ||
                            $model == 'item-sub-categories' ||
                            $model == 'priority-level' ||
                            $model == 'location-and-stores' ||
                            $model == 'inventory-categories' ||
                            $model == 'unit-of-measures' ||
                            $model == 'promotion-groups' ||
                            $model == 'promotion-types' ||
                            $model == 'active-promotions' ||
                            $model == 'hampers' ||
                            $model == 'category')): ?> active <?php endif; ?>">
                    <a href="#"><i class="fa fa-circle"></i> Inventory
                        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                    </a>
                    <ul class="treeview-menu">

                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['location-and-stores___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'location-and-stores'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('location-and-stores.index'); ?>"><i class="fa fa-circle"></i>
                                    Location and Stores</a>
                            </li>
                        <?php endif; ?>

                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['stock-type-categories___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'stock-type-categories'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('stock-type-categories.index'); ?>"><i class="fa fa-circle"></i> Stock
                                    Type
                                    Categories </a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['stock-family-groups___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'stock-family-groups'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('stock-family-groups.index'); ?>"><i class="fa fa-circle"></i> Stock
                                    Family
                                    Groups </a>
                            </li>
                        <?php endif; ?>

                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['inventory-categories___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'inventory-categories'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('inventory-categories.index'); ?>"><i class="fa fa-circle"></i>
                                    Inventory Categories</a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['item-sub-categories___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'item-sub-categories'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('item-sub-categories.index'); ?>"><i class="fa fa-circle"></i>
                                    <span>Item Sub Categories</span></a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['priority-level___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'priority-level'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('priority-level.index'); ?>"><i class="fa fa-circle"></i>
                                    <span>Priority Level</span></a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['unit-of-measures___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'unit-of-measures'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('unit-of-measures.index'); ?>"><i class="fa fa-circle"></i> Bin
                                    Location</a>
                            </li>
                        <?php endif; ?>

                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['category___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'category'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('category.index'); ?>"><i class="fa fa-circle"></i>
                                    Category Price</a></li>
                        <?php endif; ?>

                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['pack-size___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'pack-size'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('pack-size.index'); ?>"><i class="fa fa-circle"></i>
                                    <span>Pack size</span></a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['utility___promotion-type-view'])): ?>
                                <li class="<?php if(isset($model) && $model == 'promotion-types'): ?> active <?php endif; ?>"><a
                                            href="<?php echo route('promotion-types'); ?>"><i class="fa fa-circle"></i>
                                        Promotion Type</a></li>
                            <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['utility___promotion-group-view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'promotion-groups'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('promotion-group.index'); ?>"><i class="fa fa-circle"></i>
                                    Promotion Groups</a></li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['utility___active-promotions-view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'active-promotions'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('active-promotions.index'); ?>"><i class="fa fa-circle"></i>
                                    Active Promotion </a></li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['utility___hampers-view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'hampers'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('hampers.index'); ?>"><i class="fa fa-circle"></i>
                                    Hampers </a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['route-manager___view'])): ?>
                <li class="treeview <?php if(isset($model) && ($model == 'manage-routes' || $model == 'manage-delivery-centers')): ?> active <?php endif; ?>">
                    <a href="#">
                        <i class="fa fa-circle"></i> Route Manager
                        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                    </a>

                    <ul class="treeview-menu">
                        <li class="<?php if(isset($model) && $model == 'manage-routes'): ?> active <?php endif; ?>">
                            <a href="<?php echo route('manage-routes.index'); ?>">
                                <i class="fa fa-circle"></i> Route Mapping
                            </a>
                        </li>

                        
                        <li class="<?php if(isset($model) && $model == 'manage-routes'): ?> active <?php endif; ?>">
                            <a href="<?php echo route('manage-routes.route-tonnage-summary'); ?>">
                                <i class="fa fa-circle"></i> Route Targets Summary
                            </a>
                        </li>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['finanacial-receivables-payables___view'])): ?>
                <li class="treeview <?php if(isset($model) &&
                        ($model == 'payment-terms' ||
                            $model == 'payment-methods' ||
                            $model == 'payment-modes' ||
                            $model == 'payment-providers')): ?> active <?php endif; ?>">
                    <a href="#"><i class="fa fa-circle"></i> Receivables/Payables
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['payment-terms___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'payment-terms'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('payment-terms.index'); ?>">
                                    <i class="fa fa-circle"></i>
                                    Payment Terms
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['payment-providers___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'payment-providers'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('payment-providers.index'); ?>">
                                    <i class="fa fa-circle"></i><span>Payment Providers</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['payment-methods___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'payment-methods'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('payment-methods.index'); ?>">
                                    <i class="fa fa-circle"></i><span>Payment Methods</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['payment-modes___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'payment-modes'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('payment-modes.index'); ?>">
                                    <i class="fa fa-circle"></i><span>Payment Modes</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['financial-production___view'])): ?>
                <li class="treeview <?php if(isset($model) && $model == 'processes'): ?> active <?php endif; ?>">
                    <a href="#"><i class="fa fa-circle"></i> Production
                        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                    </a>
                    <ul class="treeview-menu">
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['processes___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'processes'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('processes.index'); ?>"><i class="fa fa-circle"></i>
                                    Processes </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['alerts-and-notifications___view'])): ?>
                <li class="treeview <?php if(isset($model) && ($model == 'alerts' || $model == 'scheduled-notifications')): ?> active <?php endif; ?>">
                    <a href="#"><i class="fa fa-circle"></i> Alerts & Notifications
                        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                    </a>

                    <ul class="treeview-menu">
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['alerts___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'alerts'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('alerts.index'); ?>"><i class="fa fa-circle"></i> Alerts
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if(can('view', 'scheduled-notifications')): ?>
                            <li class="<?php if(isset($model) && $model == 'scheduled-notifications'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('scheduled-notifications.index'); ?>">
                                    <i class="fa fa-circle"></i>
                                    Scheduled Notifications
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>
        </ul>
    </li>
<?php endif; ?>
<?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/includes/sidebar_includes/system_administration.blade.php ENDPATH**/ ?>