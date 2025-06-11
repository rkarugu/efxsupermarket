@if ($logged_user_info->role_id == 1 || isset($my_permissions['hr-and-payroll___view']))
    <li @class(['treeview' => true, 'active' => request()->is('admin/hr/*')])>

        <a href="#">
            <i class="fa fa-users"></i>
            <span>HR And Payroll</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>

        <ul class="treeview-menu">
            @if ($logged_user_info->role_id == 1 || isset($my_permissions['hr-and-payroll-configurations___view']))
                <li @class([
                    'treeview' => true,
                    'active' => request()->is('admin/hr/configurations/*'),
                ])>
                    <a href="#">
                        <i class="fa fa-circle"></i>
                        <span>Configurations</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>

                    <ul class="treeview-menu">
                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['hr-and-payroll-configurations-general___view']))
                            <li @class(['active' => request()->is('admin/hr/configurations/general')])>
                                <a href="{!! route('hr.configurations.general') !!}">
                                    <i class="fa fa-circle"></i>
                                    General
                                </a>
                            </li>
                        @endif

                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['hr-and-payroll-configurations-payroll___view']))
                            <li @class(['active' => request()->is('admin/hr/configurations/payroll')])>
                                <a href="{{ route('hr.configurations.payroll') }}">
                                    <i class="fa fa-circle"></i>
                                    Payroll
                                </a>
                            </li>
                        @endif

                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['hr-and-payroll-configurations-banking___view']))
                            <li @class(['active' => request()->is('admin/hr/configurations/banking')])>
                                <a href="{{ route('hr.configurations.banking') }}">
                                    <i class="fa fa-circle"></i>
                                    Banking
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if ($logged_user_info->role_id == 1 || isset($my_permissions['hr-and-payroll-hr-management___view']))
                <li @class([
                    'treeview' => true,
                    'active' => request()->is('admin/hr/management/*'),
                ])>
                    <a href="#">
                        <i class="fa fa-circle"></i>
                        <span>HR Management</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>

                    <ul class="treeview-menu">
                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['hr-management-employee-drafts___view']))
                            <li @class([
                                'active' => request()->is('admin/hr/management/employee-drafts'),
                            ])>
                                <a href="{!! route('hr.management.employee-drafts') !!}">
                                    <i class="fa fa-circle"></i>
                                    Employee Drafts
                                </a>
                            </li>
                        @endif
                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['hr-management-employees___view']))
                            <li @class(['active' => request()->is('admin/hr/management/employees')])>
                                <a href="{!! route('hr.management.employees') !!}">
                                    <i class="fa fa-circle"></i>
                                    Employees
                                </a>
                            </li>
                        @endif
                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['hr-management-casuals___view']))
                            <li @class(['active' => request()->is('admin/hr/management/casuals')])>
                                <a href="{!! route('hr.management.casuals') !!}">
                                    <i class="fa fa-circle"></i>
                                    Casuals
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if ($logged_user_info->role_id == 1 || isset($my_permissions['hr-and-payroll-payroll___view']))
                <li @class([
                    'treeview' => true,
                    'active' => request()->is('admin/hr/payroll/*'),
                ])>
                    <a href="#">
                        <i class="fa fa-circle"></i>
                        <span>Payroll</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>

                    <ul class="treeview-menu">
                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['payroll-payroll-months___view']))
                            <li @class([
                                'active' => request()->is('admin/hr/payroll/payroll-months'),
                            ])>
                                <a href="{!! route('hr.payroll.payroll-months') !!}">
                                    <i class="fa fa-circle"></i>
                                    Payroll Months
                                </a>
                            </li>
                        @endif
                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['casuals-pay___view']))
                            <li @class([
                                'treeview' => true,
                                'active' => request()->is('admin/hr/payroll/casuals-pay/*'),
                            ])>
                                <a href="#">
                                    <i class="fa fa-circle"></i>
                                    <span>Casuals Pay</span>
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>

                                <ul class="treeview-menu">
                                    @if ($logged_user_info->role_id == 1 || isset($my_permissions['casuals-pay-pay-periods___view']))
                                        <li @class([
                                            'active' => request()->is('admin/hr/payroll/casuals-pay/pay-periods'),
                                        ])>
                                            <a href="{!! route('hr.payroll.casuals-pay.pay-periods') !!}">
                                                <i class="fa fa-circle"></i>
                                                Pay Periods
                                            </a>
                                        </li>
                                    @endif

                                    @if ($logged_user_info->role_id == 1 || isset($my_permissions['casuals-pay-successful-disbursements___view']))
                                        <li @class([
                                            'active' => request()->is('admin/hr/payroll/casuals-pay/successful-disbursements'),
                                        ])>
                                            <a href="{!! route('hr.payroll.casuals-pay.successful-disbursements') !!}">
                                                <i class="fa fa-circle"></i>
                                                Successful Disbursements
                                            </a>
                                        </li>
                                    @endif

                                    @if ($logged_user_info->role_id == 1 || isset($my_permissions['casuals-pay-failed-disbursements___view']))
                                        <li @class([
                                            'active' => request()->is('admin/hr/payroll/casuals-pay/failed-disbursements'),
                                        ])>
                                            <a href="{!! route('hr.payroll.casuals-pay.failed-disbursements') !!}">
                                                <i class="fa fa-circle"></i>
                                                Failed Disbursements
                                            </a>
                                        </li>
                                    @endif

                                    @if ($logged_user_info->role_id == 1 || isset($my_permissions['casuals-pay-expunged-disbursements___view']))
                                        <li @class([
                                            'active' => request()->is('admin/hr/payroll/casuals-pay/expunged-disbursements'),
                                        ])>
                                            <a href="{!! route('hr.payroll.casuals-pay.expunged-disbursements') !!}">
                                                <i class="fa fa-circle"></i>
                                                Expunged Disbursements
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['payroll-reports___view']))
                            <li @class([
                                'active' => request()->is('admin/hr/payroll/reports'),
                            ])>
                                <a href="{!! route('hr.payroll.payroll-reports') !!}">
                                    <i class="fa fa-circle"></i>
                                    Reports
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif
        </ul>
    </li>
@endif
