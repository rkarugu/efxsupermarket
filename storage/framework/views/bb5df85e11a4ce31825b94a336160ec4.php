<?php if($logged_user_info->role_id == 1 || isset($my_permissions['hr-and-payroll___view'])): ?>
    <li class="<?php echo \Illuminate\Support\Arr::toCssClasses(['treeview' => true, 'active' => request()->is('admin/hr/*')]); ?>">

        <a href="#">
            <i class="fa fa-users"></i>
            <span>HR And Payroll</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>

        <ul class="treeview-menu">
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['hr-and-payroll-configurations___view'])): ?>
                <li class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                    'treeview' => true,
                    'active' => request()->is('admin/hr/configurations/*'),
                ]); ?>">
                    <a href="#">
                        <i class="fa fa-circle"></i>
                        <span>Configurations</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>

                    <ul class="treeview-menu">
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['hr-and-payroll-configurations-general___view'])): ?>
                            <li class="<?php echo \Illuminate\Support\Arr::toCssClasses(['active' => request()->is('admin/hr/configurations/general')]); ?>">
                                <a href="<?php echo route('hr.configurations.general'); ?>">
                                    <i class="fa fa-circle"></i>
                                    General
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['hr-and-payroll-configurations-payroll___view'])): ?>
                            <li class="<?php echo \Illuminate\Support\Arr::toCssClasses(['active' => request()->is('admin/hr/configurations/payroll')]); ?>">
                                <a href="<?php echo e(route('hr.configurations.payroll')); ?>">
                                    <i class="fa fa-circle"></i>
                                    Payroll
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['hr-and-payroll-configurations-banking___view'])): ?>
                            <li class="<?php echo \Illuminate\Support\Arr::toCssClasses(['active' => request()->is('admin/hr/configurations/banking')]); ?>">
                                <a href="<?php echo e(route('hr.configurations.banking')); ?>">
                                    <i class="fa fa-circle"></i>
                                    Banking
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['hr-and-payroll-hr-management___view'])): ?>
                <li class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                    'treeview' => true,
                    'active' => request()->is('admin/hr/management/*'),
                ]); ?>">
                    <a href="#">
                        <i class="fa fa-circle"></i>
                        <span>HR Management</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>

                    <ul class="treeview-menu">
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['hr-management-employee-drafts___view'])): ?>
                            <li class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                'active' => request()->is('admin/hr/management/employee-drafts'),
                            ]); ?>">
                                <a href="<?php echo route('hr.management.employee-drafts'); ?>">
                                    <i class="fa fa-circle"></i>
                                    Employee Drafts
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['hr-management-employees___view'])): ?>
                            <li class="<?php echo \Illuminate\Support\Arr::toCssClasses(['active' => request()->is('admin/hr/management/employees')]); ?>">
                                <a href="<?php echo route('hr.management.employees'); ?>">
                                    <i class="fa fa-circle"></i>
                                    Employees
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['hr-management-casuals___view'])): ?>
                            <li class="<?php echo \Illuminate\Support\Arr::toCssClasses(['active' => request()->is('admin/hr/management/casuals')]); ?>">
                                <a href="<?php echo route('hr.management.casuals'); ?>">
                                    <i class="fa fa-circle"></i>
                                    Casuals
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['hr-and-payroll-payroll___view'])): ?>
                <li class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                    'treeview' => true,
                    'active' => request()->is('admin/hr/payroll/*'),
                ]); ?>">
                    <a href="#">
                        <i class="fa fa-circle"></i>
                        <span>Payroll</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>

                    <ul class="treeview-menu">
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['payroll-payroll-months___view'])): ?>
                            <li class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                'active' => request()->is('admin/hr/payroll/payroll-months'),
                            ]); ?>">
                                <a href="<?php echo route('hr.payroll.payroll-months'); ?>">
                                    <i class="fa fa-circle"></i>
                                    Payroll Months
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['casuals-pay___view'])): ?>
                            <li class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                'treeview' => true,
                                'active' => request()->is('admin/hr/payroll/casuals-pay/*'),
                            ]); ?>">
                                <a href="#">
                                    <i class="fa fa-circle"></i>
                                    <span>Casuals Pay</span>
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>

                                <ul class="treeview-menu">
                                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['casuals-pay-pay-periods___view'])): ?>
                                        <li class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                            'active' => request()->is('admin/hr/payroll/casuals-pay/pay-periods'),
                                        ]); ?>">
                                            <a href="<?php echo route('hr.payroll.casuals-pay.pay-periods'); ?>">
                                                <i class="fa fa-circle"></i>
                                                Pay Periods
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['casuals-pay-successful-disbursements___view'])): ?>
                                        <li class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                            'active' => request()->is('admin/hr/payroll/casuals-pay/successful-disbursements'),
                                        ]); ?>">
                                            <a href="<?php echo route('hr.payroll.casuals-pay.successful-disbursements'); ?>">
                                                <i class="fa fa-circle"></i>
                                                Successful Disbursements
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['casuals-pay-failed-disbursements___view'])): ?>
                                        <li class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                            'active' => request()->is('admin/hr/payroll/casuals-pay/failed-disbursements'),
                                        ]); ?>">
                                            <a href="<?php echo route('hr.payroll.casuals-pay.failed-disbursements'); ?>">
                                                <i class="fa fa-circle"></i>
                                                Failed Disbursements
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['casuals-pay-expunged-disbursements___view'])): ?>
                                        <li class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                            'active' => request()->is('admin/hr/payroll/casuals-pay/expunged-disbursements'),
                                        ]); ?>">
                                            <a href="<?php echo route('hr.payroll.casuals-pay.expunged-disbursements'); ?>">
                                                <i class="fa fa-circle"></i>
                                                Expunged Disbursements
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['payroll-reports___view'])): ?>
                            <li class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                'active' => request()->is('admin/hr/payroll/reports'),
                            ]); ?>">
                                <a href="<?php echo route('hr.payroll.payroll-reports'); ?>">
                                    <i class="fa fa-circle"></i>
                                    Reports
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>
        </ul>
    </li>
<?php endif; ?>
<?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/includes/sidebar_includes/hr.blade.php ENDPATH**/ ?>