<?php if($logged_user_info->role_id == 1 || isset($my_permissions['sales-and-receivables___view'])): ?>
    <li class="treeview <?php if(
        (isset($model) &&
            ($model == 'route-customers-listing' ||
                $model == 'end-of-the-day-routine' ||
                $model == 'cheque-report' ||
                $model == 'bounced-cheque' ||
                $model == 'cleared-cheque' ||
                $model == 'cheque-management' ||
                $model == 'deposit-cheque' ||
                $model == 'register-cheque' ||
                $model == 'merged-payments' ||
                $model == 'equity-bank-deposits' ||
                $model == 'pos-cash-sales-new' ||
                $model == 'total-vat-report' ||
                $model == 'esd-vat-report' ||
                $model == 'new-kra-signed-invoices' ||
                $model == 'dispatch-invoice-report' ||
                $model == 'dispatch-and-close-loading-sheet' ||
                $model == 'dispatched-loading-sheets' ||
                $model == 'return-transfers' ||
                $model == 'approver-1' ||
                $model == 'late-returns' ||
                $model == 'approver-2' ||
                $model == 'rejected-returns' ||
                $model == 'processed-returns' ||
                $model == 'return-confirm-report' ||
                $model == 'transfers' ||
                $model == 'sales-invoice' ||
                $model == 'authorise-requisitions' ||
                $model == 'confirm-invoice' ||
                $model == 'confirm-invoice-test' ||
                $model == 'summary_report' ||
                $model == 'return-list' ||
                $model == 'route-profitibility-report' ||
                $model == 'dispatched_items_report' ||
                $model == 'end_of_day_utility' ||
                $model == 'number-series-utility' ||
                $model == 'detailed_sales_report' ||
                $model == 'sales_by_date_report' ||
                $model == 'dispatch' ||
                $model == 'dispatch-progress' ||
                $model == 'pos-cash-sales' ||
                $model == 'pos-cash-sales-stale' ||
                $model == 'pos-return-list' ||
                $model == 'pos-return-list_late' ||
                $model == 'tender-entry' ||
                $model == 'tender-entry-channel-summery' ||
                $model == 'cash-sales' ||
                $model == 'credit-sales' ||
                $model == 'credit-note' ||
                $model == 'maintain-customers' ||
                $model == 'payment-reconcilliation' ||
                $model == 'order-taking-schedules' ||
                $model == 'proforma-invoice' ||
                $model == 'customer-aging-analysis' ||
                $model == 'sales-commission-bands' ||
                $model == 'sales-invoices' ||
                $model == 'parking-lists' ||
                $model == 'salesman-orders' ||
                $model == 'salesman-shifts' ||
                $model == 'delivery-schedules' ||
                $model == 'delivery-schedules-live' ||
                $model == 'route-daily-sales-report' ||
                $model == 'reported-shift-issues' ||
                $model == 'field-visits' ||
                $model == 'sales-and-receivables-reports' ||
                $model == 'shift-reopen-request' ||
                $model == 'route-weekly-sales-report' ||
                $model == 'loading-schedule-vs-sales-report' ||
                $model == 'delivery-schedule-report' ||
                $model == 'customer-balances-report' ||
                $model == 'shift-delivery-report' ||
                $model == 'till-direct-banking-report' ||
                $model == 'route-performance-report' ||
                $model == 'group-performance-report' ||
                $model == 'promotion-sales-report' ||
                $model == 'discount-sales-report' ||
                $model == 'sales-and-receivables-dashboard' ||
                $model == 'sales-per-supplier-per-route' ||
                $model == 'sales-analysis-report' ||
                $model == 'bank-reconciliation' ||
                $model == 'abnormal-returns' ||
                $model == 'route-return-summary-report' ||
                $model == 'dashboard_report' ||
                $model == 'route-manager-listing' ||
                $model == 'route-customers-onboarding-requests')) ||
            $model == 'route-customers-approval-requests' ||
            $model == 'salesman-offsite-requests' ||
            $model == 'onsite-vs-offsite-shifts-report' ||
            $model == 'payment-verification' ||
            $model == 'payment-approval' ||
            $model == 'pos-cash-sales-delayed' ||
            $model == 'pos-cash-sales-archived' ||
            $model == 'cashier-management-show' ||
            $model == 'cashier-management-all' ||
            $model == 'cashier-management' ||
            $model == 'initial-petty-cash-approvals' ||
            $model == 'final-petty-cash-approvals' ||
            $model == 'successful-petty-cash-allocations' ||
            $model == 'failed-petty-cash-deposits' ||
            $model == 'rejected-petty-cash-deposits' ||
            $model == 'expunged-petty-cash-deposits' ||
            $model == 'petty-cash-summary-log' ||
            $model == 'petty-cash-detailed-log' ||
            $model == 'petty-cash-logs' ||
            $model == 'debtor-trans' ||
            $model == 'route-customers-duplicate-requests' ||
            $model == 'route-customers-customer-comments' ||
            $model == 'route-customers-customer-time-serived' ||
            $model == 'geomapping-schedules' ||
            $model == 'geomapping-summary' ||
            $model == 'rejected-customers' ||
            $model == 'bank-statement-upload' ||
            $model == 'invoice-balancing-report' ||
            $model == 'view-manual-upload' ||
            $model == 'completed-returns' ||
            $model == 'detailed-completed-returns' ||
            $model == 'detailed-sales-summary-report' ||
            $model == 'pos-overview-report' ||
            $model == 'pos-payments-consumption' ||
            $model == 'pos_cash_payments' ||
            $model == 'undisbursed-petty-cash' ||
            $model == 'reconciliation' ||
            $model == 'bank_post_log' ||
            $model == 'transaction-history' ||
            $model == 'transaction-mispost' ||
            $model == 'bank-statement-mispost' ||
            $model == 'banking-approval' ||
            $model == 'pos-banking-overview' ||
            $model == 'bank-error-logs' ||
            $model == 'cash-banking-report' ||
            $model == 'sales-and-receivables-reports' ||
            $model == 'dispatch-logs' ||
            $model == 'route-group-rep'): ?> ) active <?php endif; ?>">
        <a href="#"><i class="fa fa-fw fa-cash-register"></i> <span> Sales & Receivables</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
        </a>

        <ul class="treeview-menu">
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['maintain-customers___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'maintain-customers'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('maintain-customers.index'); ?>"><i class="fa fa-circle"></i>Maintain Customers</a>
                </li>
            <?php endif; ?>


            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['route-manager___view'])): ?>
                <li class="treeview <?php if(
                    (isset($model) &&
                        ($model == 'route-manager-listing' ||
                            $model == 'route-customers-listing' ||
                            $model == 'route-customers-onboarding-requests')) ||
                        $model == 'route-customers-approval-requests' ||
                        $model == 'route-customers-customer-comments' ||
                        $model == 'route-customers-customer-time-served' ||
                        $model == 'maintain-customers' ||
                        $model == 'route-customers-duplicate-requests' ||
                        $model == 'geomapping-schedules' ||
                        $model == 'geomapping-summary' ||
                        $model == 'rejected-customers' ||
                        $model == 'manage-delivery-centers' ||
                        $model == 'route-group-rep'): ?> ) active <?php endif; ?>">
                    <a href="#">
                        <i class="fa fa-circle"></i> Route Manager
                        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                    </a>

                    <ul class="treeview-menu">
                        <li class="<?php if(isset($model) && $model == 'route-manager-listing'): ?> active <?php endif; ?>">
                            <a href="<?php echo route('manage-routes.listing'); ?>">
                                <i class="fa fa-circle"></i> Route Listing
                            </a>
                        </li>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['route-customers___view'])): ?>
                            <li class="treeview <?php if(
                                (isset($model) &&
                                    ($model == 'route-customers-listing' ||
                                        $model == 'route-customers-duplicate-requests' ||
                                        $model == 'route-customers-onboarding-requests')) ||
                                    $model == 'route-customers-approval-requests' ||
                                    $model == 'route-customers-customer-comments' ||
                                    $model == 'geomapping-schedules' ||
                                    $model == 'geomapping-summary' ||
                                    $model == 'rejected-customers' ||
                                    $model == 'field-visits'): ?> ) active <?php endif; ?>">
                                <a href="#">
                                    <i class="fa fa-circle"></i> Route Customers
                                    <span class="pull-right-container"><i
                                            class="fa fa-angle-left pull-right"></i></span>
                                </a>
                                <ul class="treeview-menu">


                                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['route-customers___listing'])): ?>
                                        <li class="<?php if(isset($model) && $model == 'route-customers-listing'): ?> active <?php endif; ?>">
                                            <a href="<?php echo route('route-customers.index'); ?>">
                                                <i class="fa fa-circle"></i> Listing
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['route-customers___onboarding-requests'])): ?>
                                        <li class="<?php if(isset($model) && $model == 'route-customers-onboarding-requests'): ?> active <?php endif; ?>">
                                            <a href="<?php echo route('route-customers.unverified'); ?>">
                                                <i class="fa fa-circle"></i> Onboarding Requests
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['route-customers___approval-requests'])): ?>
                                        <li class="<?php if(isset($model) && $model == 'route-customers-approval-requests'): ?> active <?php endif; ?>">
                                            <a href="<?php echo route('route-customers.approval-requests'); ?>">
                                                <i class="fa fa-circle"></i> Approval Requests
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['route-customers___duplicate-approval-requests'])): ?>
                                        <li class="<?php if(isset($model) && $model == 'route-customers-duplicate-requests'): ?> active <?php endif; ?>">
                                            <a href="<?php echo route('duplicate-route-customers'); ?>">
                                                <i class="fa fa-circle"></i> Duplicate Requests
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['route-customers___geomapping-schedules'])): ?>
                                        <li class="<?php if(isset($model) && $model == 'geomapping-schedules'): ?> active <?php endif; ?>">
                                            <a href="<?php echo e(route('geomapping-schedules.index')); ?>">
                                                <i class="fa fa-circle"></i> Geomapping Schedules
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['route-customers___geomapping-summary'])): ?>
                                        <li class="<?php if(isset($model) && $model == 'geomapping-summary'): ?> active <?php endif; ?>">
                                            <a href="<?php echo e(route('geomapping-summary')); ?>">
                                                <i class="fa fa-circle"></i> Geomapping Summary
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['route-customers___geomapping-comments'])): ?>
                                        <li class="<?php if(isset($model) && $model == 'route-customers-customer-comments'): ?> active <?php endif; ?>">
                                            <a href="<?php echo e(route('route-customers.comments')); ?>">
                                                <i class="fa fa-circle"></i> Geomapping Feedback
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['route-customers___rejected-customers'])): ?>
                                        <li class="<?php if(isset($model) && $model == 'rejected-customers'): ?> active <?php endif; ?>">
                                            <a href="<?php echo e(route('rejected-customers')); ?>">
                                                <i class="fa fa-circle"></i> Rejected Customers
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['route-group-rep___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'route-group-rep'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('group-rep.index'); ?>">
                                    <i class="fa fa-circle"></i> Group Reps
                                </a>
                            </li>
                        <?php endif; ?>

                    </ul>
                </li>
            <?php endif; ?>


            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['reconciliation___see-overview'])): ?>
                <li class="treeview <?php if(isset($model) && in_array($model, ['banking-approval', 'pos-banking-overview'])): ?> active <?php endif; ?>">

                    <a href="#">
                        <i class="fa fa-circle"></i> Banking
                        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                    </a>

                    <ul class="treeview-menu">
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['reconciliation___see-overview-route'])): ?>
                            <li class="<?php if(isset($model) && $model == 'banking-approval'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('route-banking-approval.overview'); ?>"><i class="fa fa-circle"></i> Route Overview </a>
                            </li>
                        <?php endif; ?>

                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['reconciliation___see-overview-pos'])): ?>
                            <li class="<?php if(isset($model) && $model == 'pos-banking-overview'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('pos-banking.daily-overview'); ?>"><i class="fa fa-circle"></i> POS Overview </a>
                            </li>
                        <?php endif; ?>

                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['reconciliation___gl-reconciliation'])): ?>
                            <li class="<?php if(isset($model) && $model == 'gl-recon'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('gl-recon.overview'); ?>"><i class="fa fa-circle"></i> GL Reconciliation </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>


            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['reconciliation___view'])): ?>
                <li class="treeview <?php if(
                    (isset($model) && $model == 'bank-reconciliation') ||
                        $model == 'payment-verification' ||
                        $model == 'payment-approval' ||
                        $model == 'debtor-trans' ||
                        $model == 'bank-statement-upload' ||
                        $model == 'view-manual-upload' ||
                        $model == 'reconciliation' ||
                        $model == 'bank_post_log' ||
                        $model == 'transaction-history' ||
                        $model == 'bank-error-logs'): ?> active <?php endif; ?>">

                    <a href="#">
                        <i class="fa fa-circle"></i> Reconciliation
                        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                    </a>

                    <ul class="treeview-menu">
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['reconciliation___verification'])): ?>
                            <li class="<?php if(isset($model) && $model == 'payment-verification'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('payment-reconciliation.verification'); ?>"><i class="fa fa-circle"></i> Payment
                                    Verifications
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['reconciliation___approval'])): ?>
                            <li class="<?php if(isset($model) && $model == 'payment-approval'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('payment-reconciliation.approval'); ?>"><i class="fa fa-circle"></i> Payment
                                    Approvals
                                </a>
                            </li>
                        <?php endif; ?>

                        

                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['reconciliation___suspend'])): ?>
                            <li class="<?php if(isset($model) && $model == 'bank-reconciliation'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('suspended-transactions.index'); ?>"><i class="fa fa-circle"></i> Suspended
                                    Transactions</a>
                            </li>

                            <li class="<?php if(isset($model) && $model == 'bank-reconciliation'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('suspended-transactions.expunged'); ?>"><i class="fa fa-circle"></i> Expunged
                                    Transactions</a>
                            </li>

                            <li class="<?php if(isset($model) && $model == 'bank-reconciliation'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('suspended-transactions.restored'); ?>"><i class="fa fa-circle"></i> Restored
                                    Transactions</a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['reconciliation___view-debtor-trans'])): ?>
                            <li class="<?php if(isset($model) && $model == 'debtor-trans'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('debtor-trans'); ?>"><i class="fa fa-circle"></i> Debtor
                                    Transactions</a>
                            </li>
                        <?php endif; ?>

                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['reconciliation___bank-statement-upload'])): ?>
                            <li class="<?php if(isset($model) && $model == 'bank-statement-upload'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('bank-statements'); ?>"><i class="fa fa-circle"></i> Bank Statements</a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['reconciliation___view-manual-upload'])): ?>
                            <li class="<?php if(isset($model) && $model == 'view-manual-upload'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('manual-upload-list'); ?>"><i class="fa fa-circle"></i> Approve Uploads</a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['reconciliation___bank_post_log'])): ?>
                            <li class="<?php if(isset($model) && $model == 'bank_post_log'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('bank-posting-logs'); ?>"><i class="fa fa-circle"></i> Bank Posting Logs</a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['reconciliation___bank-error-logs'])): ?>
                            <li class="<?php if(isset($model) && $model == 'bank-error-logs'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('bank-error-logs'); ?>"><i class="fa fa-circle"></i> Bank Error Logs</a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['reconciliation___transaction-history'])): ?>
                            <li class="<?php if(isset($model) && $model == 'transaction-history'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('transaction-history'); ?>"><i class="fa fa-circle"></i> Transaction
                                    History</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if(
                $logged_user_info->role_id == 1 ||
                    isset($my_permissions['pos-cash-sales-new___view']) ||
                    isset($my_permissions['pos-cash-sales___view']) ||
                    isset($my_permissions['cashier-management___transactions']) ||
                    isset($my_permissions['cashier-management___transactions']) ||
                    isset($my_permissions['cashier-management___view']) ||
                    isset($my_permissions['tender-entry___channel-summery']) ||
                    isset($my_permissions['pos-cash-sales___return-list'])): ?>
                <li class="treeview <?php if(isset($model) &&
                        ($model == 'pos-cash-sales-new' ||
                            $model == 'pos-cash-sales' ||
                            $model == 'dispatch-progress' ||
                            $model == 'cashier-management' ||
                            $model == 'cashier-management-show' ||
                            $model == 'cashier-management-all' ||
                            $model == 'pos-cash-sales-stale' ||
                            $model == 'tender-entry' ||
                            $model == 'tender-entry-channel-summery' ||
                            $model == 'pos-cash-sales-delayed' ||
                            $model == 'pos-cash-sales-archived' ||
                            $model == 'pos-return-list' ||
                            $model == 'pos-payments-consumption' ||
                            $model == 'pos_cash_payments' ||
                            $model == 'cash-banking-report' ||
                            $model == 'pos-return-list_late')): ?> active <?php endif; ?>">
                    <a href="#"><i class="fa fa-circle"></i> Cash Sales
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['pos-cash-sales___view'])): ?>
                            <li class="<?php if(isset($model) && $model == 'pos-cash-sales'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('pos-cash-sales.index') . getReportDefaultFilterForTrialBalance(); ?>"><i class="fa fa-circle"></i>POS Cash
                                    Sales</a></li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['pos-cash-sales___dispatch-progress'])): ?>
                            <li class="<?php if(isset($model) && $model == 'dispatch-progress'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('pos-cash-sales.customer-view'); ?>"><i class="fa fa-circle"></i>Dispatch
                                    Progress</a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['pos-cash-sales___delayed-orders'])): ?>
                            <li class="<?php if(isset($model) && $model == 'pos-cash-sales-delayed'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('pos-cash-sales.stale-orders'); ?>"><i class="fa fa-circle"></i>Delayed Orders</a>
                            </li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['pos-cash-sales___archived-orders'])): ?>
                            <li class="<?php if(isset($model) && $model == 'pos-cash-sales-archived'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('pos-cash-sales.archive-report'); ?>"><i class="fa fa-circle"></i>Archived Orders</a>
                            </li>
                        <?php endif; ?>

                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['pos-cash-sales___return-list'])): ?>
                            <li class="<?php if(isset($model) && $model == 'pos-return-list'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('pos-cash-sales.returned_cash_sales_list') . getReportDefaultFilterForTrialBalance(); ?>"><i class="fa fa-circle"></i>Cash Sales
                                    Return</a></li>
                        <?php endif; ?>
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['pos-cash-sales___return-list_late'])): ?>
                            <li class="<?php if(isset($model) && $model == 'pos-return-list_late'): ?> active <?php endif; ?>"><a
                                    href="<?php echo route('pos-cash-sales.returned_cash_sales_list_late') . getReportDefaultFilterForTrialBalance(); ?>"><i class="fa fa-circle"></i>Late
                                    Cash Sales
                                    Return</a></li>
                        <?php endif; ?>

                        <?php if(
                            $logged_user_info->role_id == 1 ||
                                isset($my_permissions['cashier-management___view']) ||
                                isset($my_permissions['cashier-management___transactions'])): ?>
                            <li class="treeview <?php if(isset($model) &&
                                    ($model == 'cashier-management' ||
                                        $model == 'cashier-management-show' ||
                                        $model == 'cashier-management-all' ||
                                        $model == 'cash-banking-report' ||
                                        $model == 'pos_cash_payments' ||
                                        $model == 'pos-payments-consumption')): ?> active <?php endif; ?>">
                                <a href="#"><i class="fa fa-circle"></i> Cashier Management
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">
                                    
                                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['cashier-management___view'])): ?>
                                        <li class="<?php if(isset($model) && $model == 'cashier-management'): ?> active <?php endif; ?>">
                                            <a href="<?php echo route('cashier-management.index') . getReportDefaultFilterForTrialBalance(); ?>"><i class="fa fa-circle"></i>
                                                Cashier Summary</a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['cashier-management___transactions'])): ?>
                                        <li class="<?php if(isset($model) && $model == 'cashier-management-show'): ?> active <?php endif; ?>">
                                            <a href="<?php echo route('cashier-management.transactions') . getReportDefaultFilterForTrialBalance(); ?>">
                                                <i class="fa fa-circle"></i>Cash Drops </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['cashier-management___cash-banking-report'])): ?>
                                        <li class="<?php if(isset($model) && $model == 'cash-banking-report'): ?> active <?php endif; ?>">
                                            <a href="<?php echo route('cashier-management.cash-banking-report'); ?>">
                                                <i class="fa fa-circle"></i> Cash Banking Report </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['cashier-management___pos-payments-consumption'])): ?>
                                        <li class="<?php if(isset($model) && $model == 'pos-payments-consumption'): ?> active <?php endif; ?>">
                                            <a href="<?php echo route('cashier-management.pos-payments-consumption'); ?>">
                                                <i class="fa fa-circle"></i>Payment Allocations</a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['pos_cash_payments___view'])): ?>
                                        <li class="<?php if(isset($model) && $model == 'pos_cash_payments'): ?> active <?php endif; ?>">
                                            <a href="<?php echo route('pos-cash-payments.index'); ?>">
                                                <i class="fa fa-circle"></i>Cash Payments</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <?php if(
                            $logged_user_info->role_id == 1 ||
                                isset($my_permissions['tender-entry___view']) ||
                                isset($my_permissions['tender-entry___transactions'])): ?>
                            <li class="treeview <?php if(isset($model) && ($model == 'tender-entry' || $model == 'tender-entry-channel-summery')): ?> active <?php endif; ?>">
                                <a href="#"><i class="fa fa-circle"></i> Tender Entry
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">
                                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['tender-entry___channel-summery'])): ?>
                                        <li class="<?php if(isset($model) && $model == 'tender-entry-channel-summery'): ?> active <?php endif; ?>">
                                            <a href="<?php echo route('tender-entry.channels-summery') . getReportDefaultFilterForTrialBalance(); ?>"><i class="fa fa-circle"></i>Channels
                                                Summary</a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['tender-entry___view'])): ?>
                                        <li class="<?php if(isset($model) && $model == 'tender-entry'): ?> active <?php endif; ?>">
                                            <a href="<?php echo route('tender-entry.transactions-by-channel') . getReportDefaultFilterForTrialBalance(); ?>"><i
                                                    class="fa fa-circle"></i>Transactions by
                                                Channel</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                                <?php if($logged_user_info->role_id == 1 || isset($my_permissions['pos-cash-sales___promotions_discounts'])): ?>
                            <li class="<?php if(isset($model) && $model == 'pos-cash-sales'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('active-discounts-promotions') . getReportDefaultFilterForTrialBalance(); ?>"><i class="fa fa-circle"></i>Active Promotions and
                                    Discounts</a>
                            </li>
                        <?php endif; ?>
                </li>
            <?php endif; ?>
        </ul>
    </li>
<?php endif; ?>


<?php if(
    $logged_user_info->role_id == 1 ||
        isset($my_permissions['sales-invoice___view']) ||
        isset($my_permissions['sales-invoice___view-invoice']) ||
        isset($my_permissions['sales-invoice___invoices']) ||
        isset($my_permissions['cheque-management___view'])): ?>
    <li class="treeview <?php if(isset($model) &&
            ($model == 'cheque-report' ||
                $model == 'bounced-cheque' ||
                $model == 'cleared-cheque' ||
                $model == 'cheque-management' ||
                $model == 'deposit-cheque' ||
                $model == 'register-cheque' ||
                $model == 'sales-invoice')): ?> active <?php endif; ?>">
        <a href="#"><i class="fa fa-circle"></i> Credit Sales Invoices
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['sales-invoice___invoices'])): ?>
                <li class="<?php if(isset($model) && $model == 'sales-invoice'): ?> active <?php endif; ?>"><a href="<?php echo route('sales-invoice.index') . getReportDefaultFilterForTrialBalance(); ?>"><i
                            class="fa fa-circle"></i>Invoices</a></li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['sales-invoice___cheques'])): ?>
                <li class="<?php if(isset($model) && $model == 'cheque-management'): ?> active <?php endif; ?>"><a href="<?php echo route('cheque-management.index') . getReportDefaultFilterForTrialBalance(); ?>"><i
                            class="fa fa-circle"></i>Cheque Management</a></li>
            <?php endif; ?>
            
            
            
            
            
            
            
            
            
            
            
            

        </ul>
    </li>
<?php endif; ?>

<?php if($logged_user_info->role_id == 1 || isset($my_permissions['sales-invoice___view'])): ?>
    <li class="treeview <?php if(isset($model) &&
            ($model == 'transfers' ||
                $model == 'sales-invoice' ||
                $model == 'authorise-requisitions' ||
                $model == 'confirm-invoice' ||
                $model == 'confirm-invoice-test' ||
                $model == 'salesman-shift' ||
                $model == 'return-confirm-report' ||
                $model == 'print-invoice-delivery-note')): ?> active <?php endif; ?>">
        <a href="#"><i class="fa fa-circle"></i> Salesman Invoice
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            
            
            
            
            

            
            
            
            
            

            
            
            
            
            

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['print-invoice-delivery-note___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'transfers'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('transfers.index') . getReportDefaultFilterForTrialBalance(); ?>">
                        <i class="fa fa-circle"></i> Print Invoice/Delivery Note
                    </a>
                </li>
            <?php endif; ?>

        </ul>
    </li>
<?php endif; ?>
<?php if(
    $logged_user_info->role_id == 1 ||
        isset($my_permissions['sales-invoice___view']) ||
        isset($my_permissions['confirm-invoice___view']) ||
        isset($my_permissions['print-invoice-delivery-note___view']) ||
        isset($my_permissions['print-invoice-delivery-note___return'])): ?>
    <li class="treeview <?php if(isset($model) &&
            ($model == 'return-transfers' ||
                // $model == 'transfers' ||
                $model == 'sales-invoice' ||
                $model == 'authorise-requisitions' ||
                $model == 'confirm-invoice' ||
                $model == 'confirm-invoice-test' ||
                $model == 'salesman-shift' ||
                $model == 'processed-returns' ||
                $model == 'approver-1' ||
                $model == 'late-returns' ||
                $model == 'approver-2' ||
                $model == 'rejected-returns' ||
                $model == 'return-confirm-report' ||
                $model == 'abnormal-returns' ||
                $model == 'completed-returns' ||
                $model == 'detailed-completed-returns' ||
                $model == 'print-invoice-delivery-note')): ?> active <?php endif; ?>">
        <a href="#"><i class="fa fa-circle"></i> Salesman Returns
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['print-invoice-delivery-note___return'])): ?>
                <li class="<?php if(isset($model) && $model == 'return-transfers'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('transfers.return_list') . getReportDefaultFilterForTrialBalance(); ?>">
                        <i class="fa fa-circle"></i> Sales Invoice Returns
                    </a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['approver-limit-returns___view'])): ?>
                <li class="treeview <?php if(isset($model) &&
                        ($model == 'approver-1' ||
                            $model == 'approver-2' ||
                            $model == 'return-confirm-report' ||
                            $model == 'late-returns')): ?> active <?php endif; ?>">
                    <a href="#">
                        <i class="fa fa-circle"></i> Over limit Returns
                        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                    </a>
                    <ul class="treeview-menu">
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['approver-limit-returns___approver-1'])): ?>
                            <li class="<?php if(isset($model) && $model == 'approver-1'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('transfers.return_list_groups') . getReportDefaultFilterForTrialBalance(); ?>">
                                    <i class="fa fa-circle"></i> Approver 1
                                </a>
                            </li>
                        <?php endif; ?>


                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['approver-limit-returns___approver-2'])): ?>
                            <li class="<?php if(isset($model) && $model == 'approver-2'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('transfers.return_list_groups_2') . getReportDefaultFilterForTrialBalance(); ?>">
                                    <i class="fa fa-circle"></i> Approver 2
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['approver-limit-returns___late-returns'])): ?>
                            <li class="<?php if(isset($model) && $model == 'late-returns'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('transfers.return_list_groups_late_returns') . getReportDefaultFilterForTrialBalance(); ?>">
                                    <i class="fa fa-circle"></i> Late Returns
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['approver-limit-returns___return-confirm-report'])): ?>
                            <li class="<?php if(isset($model) && $model == 'return-confirm-report'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('transfers.return_groups') . getReportDefaultFilterForTrialBalance(); ?>">
                                    <i class="fa fa-circle"></i> Over Limit Returns
                                </a>
                            </li>
                        <?php endif; ?>


                    </ul>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['processed-returns___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'processed-returns'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('transfers.processed-returns') . getReportDefaultFilterForTrialBalance(); ?>">
                        <i class="fa fa-circle"></i> Processed Returns
                    </a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['rejected-returns___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'rejected-returns'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('transfers.rejected-returns') . getReportDefaultFilterForTrialBalance(); ?>">
                        <i class="fa fa-circle"></i> Rejected Returns
                    </a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['abnormal-returns___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'abnormal-returns'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('sales-invoice.returns.abnormal') . getReportDefaultFilterForTrialBalance(); ?>">
                        <i class="fa fa-circle"></i> Abnormal Returns
                    </a>
                </li>
            <?php endif; ?>
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['completed-returns___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'completed-returns'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('completed_returns.index'); ?>">
                        <i class="fa fa-circle"></i> Completed Returns
                    </a>
                </li>
            <?php endif; ?>
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['detailed-completed-returns___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'detailed-completed-returns'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('detailedCompletedReturns'); ?>">
                        <i class="fa fa-circle"></i> Detailed Completed Returns
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </li>
<?php endif; ?>


<?php if($logged_user_info->role_id == 1 || isset($my_permissions['order-taking___view'])): ?>
    <li class="treeview <?php if(isset($model) &&
            ($model == 'order-taking-schedules' ||
                $model == 'reported-shift-issues' ||
                $model == 'salesman-orders' ||
                $model == 'salesman-shifts' ||
                $model == 'shift-reopen-request' ||
                $model == 'salesman-offsite-requests')): ?> active <?php endif; ?>">
        <a href="#">
            <i class="fa fa-circle"></i> Order Taking
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
        </a>

        <ul class="treeview-menu">
            
            
            
            
            
            
            

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['salesman-orders___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'salesman-orders'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('salesman-orders.index'); ?>"><i class="fa fa-circle"></i>
                        Salesman Orders
                    </a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['order-taking-schedules___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'salesman-shifts'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('salesman-shifts.index'); ?>"><i class="fa fa-circle"></i>
                        Salesman Shifts
                    </a>
                </li>
            <?php endif; ?>

            
            
            
            
            
            

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['order-taking-schedules___offsite-requests'])): ?>
                <li class="<?php if(isset($model) && $model == 'salesman-offsite-requests'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('salesman-shift.offsite-requests'); ?>">
                        <i class="fa fa-circle"></i>Offsite Shift Requests
                    </a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['reported-shift-issues___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'reported-shift-issues'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('reported-shift-issues.index'); ?>">
                        <i class="fa fa-circle"></i>Reported Issues
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </li>
<?php endif; ?>

<?php if($logged_user_info->role_id == 1 || isset($my_permissions['dispatch-and-delivery___view'])): ?>
    <li class="treeview <?php if(isset($model) &&
            ($model == 'parking-lists' ||
                $model == 'delivery-schedules' ||
                $model == 'dispatch' ||
                $model == 'credit-sales' ||
                $model == 'dispatch-logs' ||
                $model == 'pos-return-list' ||
                $model == 'shift-delivery-report')): ?> active <?php endif; ?>">
        <a href="#">
            <i class="fa fa-circle"></i> Dispatch & Delivery
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
        </a>

        <ul class="treeview-menu">
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['store-loading-sheet___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'parking-lists'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('store-loading-sheets.index'); ?>">
                        <i class="fa fa-circle"></i>Dispatch Loading Sheet
                    </a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['dispatched-loading-sheets___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'parking-lists'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('store-loading-sheets.dispatched'); ?>">
                        <i class="fa fa-circle"></i>Dispatched Loading Sheets
                    </a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['delivery-schedule___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'delivery-schedules'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('delivery-schedules.index'); ?>"><i class="fa fa-circle"></i>Deliveries</a>
                </li>
            <?php endif; ?>
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['shift_delivery_report___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'shift-delivery-report'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('dispatch-reports.shift-delivery-report'); ?>"><i class="fa fa-circle"></i>Shift
                        Delivery Report</a>
                </li>
            <?php endif; ?>
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['dispatch___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'dispatch'): ?> active <?php endif; ?>"><a href="<?php echo route('pos-cash-sales.dispatch'); ?>"><i
                            class="fa fa-circle"></i>POS
                        Dispatch</a></li>
            <?php endif; ?>
                <?php if($logged_user_info->role_id == 1 || isset($my_permissions['credit-sales___dispatch'])): ?>
                    <li class="<?php if(isset($model) && $model == 'credit-sales'): ?> active <?php endif; ?>"><a href="<?php echo route('credit-sales-dispatch'); ?>"><i
                                    class="fa fa-circle"></i>Credit Sales Dispatch</a></li>
                <?php endif; ?>
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['dispatch___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'dispatch-logs'): ?> active <?php endif; ?>"><a href="<?php echo route('pos-cash-sales.dispatch-logs'); ?>"><i
                            class="fa fa-circle"></i>POS Dispatch Log</a>
                </li>
            <?php endif; ?>
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['pos-return-list___return-accept'])): ?>
                <li class="<?php if(isset($model) && $model == 'pos-return-list'): ?> active <?php endif; ?>"><a href="<?php echo route('pos-cash-sales.returned_cash_sales_list_dispatcher'); ?>"><i
                            class="fa fa-circle"></i>Pos Cash Sales
                        Returns</a></li>
            <?php endif; ?>
        </ul>
    </li>
<?php endif; ?>

<?php if($logged_user_info->role_id == 1 || isset($my_permissions['sales-and-receivables-reports___view'])): ?>
    <li class="treeview <?php if(
        (isset($model) && $model == 'end_of_day_utility') ||
            $model == 'number-series-utility' ||
            $model == 'transaction-mispost' ||
            $model == 'bank-statement-mispost'): ?> active <?php endif; ?>">
        <a href="#"><i class="fa fa-circle"></i><span> Utilities</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['end-of-day-utility___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'end_of_day_utility'): ?> active <?php endif; ?>"><a href="<?php echo route('eod-routine.index'); ?>"><i
                            class="fa fa-circle"></i>
                        End of Day Routine
                    </a></li>
            <?php endif; ?>
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['number-series-utility___missing_invoice_series_numbers'])): ?>
                <li class="<?php if(isset($model) && $model == 'number-series-utility'): ?> active <?php endif; ?>"><a href="<?php echo route('number-series-report.invoices-missing'); ?>"><i
                            class="fa fa-circle"></i>
                        Missing Invoice Numbers
                    </a></li>
            <?php endif; ?>
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['route-split___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'route-split'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('route-split.index'); ?>"><i class="fa fa-circle" aria-hidden="true"></i>
                        Split Routes
                    </a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['transaction-mispost___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'transaction-mispost'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('transaction-mispost.index'); ?>"><i class="fa fa-circle" aria-hidden="true"></i>
                        Transaction Mispost
                    </a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['bank-statement-mispost___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'bank-statement-mispost'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('bank-statement-mispost.index'); ?>"><i class="fa fa-circle" aria-hidden="true"></i>
                        Bank Statement Mispost
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </li>
<?php endif; ?>


<?php if(
    $logged_user_info->role_id == 1 ||
        isset($my_permissions['petty-cash-approvals___initial_approval']) ||
        isset($my_permissions['petty-cash-approvals___final_approval']) ||
        isset($my_permissions['petty-cash-approvals___view']) ||
        isset($my_permissions['petty-cash-approvals___successful_allocations']) ||
        isset($my_permissions['petty-cash-approvals___failed_deposits']) ||
        isset($my_permissions['petty-cash-approvals___logs'])): ?>
    <li class="treeview <?php if(isset($model) &&
            ($model == 'initial-petty-cash-approvals' ||
                $model == 'final-petty-cash-approvals' ||
                $model == 'successful-petty-cash-allocations' ||
                $model == 'failed-petty-cash-deposits' ||
                $model == 'rejected-petty-cash-deposits' ||
                $model == 'expunged-petty-cash-deposits' ||
                $model == 'petty-cash-summary-log' ||
                $model == 'petty-cash-detailed-log' ||
                $model == 'petty-cash-logs' ||
                $model == 'undisbursed-petty-cash')): ?> active <?php endif; ?>">
        <a href="#">
            <i class="fa fa-circle"></i> Petty Cash Approvals
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
        </a>

        <ul class="treeview-menu">
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['petty-cash-approvals___initial_approval'])): ?>
                <li class="<?php if(isset($model) && $model == 'initial-petty-cash-approvals'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('petty-cash-approvals.initial'); ?>">
                        <i class="fa fa-circle"></i>Initial Approval
                    </a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['undisbursed-petty-cash___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'undisbursed-petty-cash'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('petty-cash-approvals.undisbursed-petty-cash'); ?>">
                        <i class="fa fa-circle"></i>Undisbursed Petty Cash
                    </a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['petty-cash-approvals___final_approval'])): ?>
                <li class="<?php if(isset($model) && $model == 'final-petty-cash-approvals'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('petty-cash-approvals.final'); ?>">
                        <i class="fa fa-circle"></i>Final Approval
                    </a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['petty-cash-approvals___successful_allocations'])): ?>
                <li class="<?php if(isset($model) && $model == 'successful-petty-cash-allocations'): ?> active <?php endif; ?>">
                    <a href="<?php echo e(route('petty-cash-approvals.successful-allocations')); ?>">
                        <i class="fa fa-circle"></i>Successful Payments
                    </a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['petty-cash-approvals___failed_deposits'])): ?>
                <li class="<?php if(isset($model) && $model == 'failed-petty-cash-deposits'): ?> active <?php endif; ?>">
                    <a href="<?php echo e(route('petty-cash-approvals.failed-deposits')); ?>">
                        <i class="fa fa-circle"></i>Failed Deposits
                    </a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['petty-cash-approvals___rejected_deposits'])): ?>
                <li class="<?php if(isset($model) && $model == 'rejected-petty-cash-deposits'): ?> active <?php endif; ?>">
                    <a href="<?php echo e(route('petty-cash-approvals.rejected-deposits')); ?>">
                        <i class="fa fa-circle"></i>Rejected Deposits
                    </a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['petty-cash-approvals___expunged_deposits'])): ?>
                <li class="<?php if(isset($model) && $model == 'expunged-petty-cash-deposits'): ?> active <?php endif; ?>">
                    <a href="<?php echo e(route('petty-cash-approvals.expunged-deposits')); ?>">
                        <i class="fa fa-circle"></i>Expunged Deposits
                    </a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['petty-cash-approvals___logs'])): ?>
                <li class="<?php if(isset($model) && $model == 'petty-cash-summary-log'): ?> active <?php endif; ?>">
                    <a href="<?php echo e(route('petty-cash-approvals.summary-log')); ?>">
                        <i class="fa fa-circle"></i>Summary Log
                    </a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['petty-cash-approvals___logs'])): ?>
                <li class="<?php if(isset($model) && $model == 'petty-cash-detailed-log'): ?> active <?php endif; ?>">
                    <a href="<?php echo e(route('petty-cash-approvals.detailed-log')); ?>">
                        <i class="fa fa-circle"></i>Detailed Log
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </li>
<?php endif; ?>

<?php if($logged_user_info->role_id == 1 || isset($my_permissions['sales-and-receivables-reports___view'])): ?>
    <li class="<?php if(isset($model) &&
            ($model == 'dashboard_report' ||
                $model == 'total-vat-report' ||
                $model == 'esd-vat-report' ||
                $model == 'new-kra-signed-invoices' ||
                $model == 'sales-and-receivables-reports' ||
                $model == 'route-profitibility-report' ||
                $model == 'summary_report' ||
                $model == 'sales_by_date_report' ||
                $model == 'detailed_sales_report' ||
                $model == 'route-daily-sales-report' ||
                $model == 'route-weekly-sales-report' ||
                $model == 'loading-schedule-vs-sales-report' ||
                $model == 'delivery-schedule-report' ||
                $model == 'customer-balances-report' ||
                $model == 'till-direct-banking-report' ||
                $model == 'route-performance-report' ||
                $model == 'group-performance-report' ||
                $model == 'promotion-sales-report' ||
                $model == 'discount-sales-report' ||
                $model == 'sales-per-supplier-per-route' ||
                $model == 'sales-analysis-report' ||
                $model == 'route-return-summary-report' ||
                $model == 'archived_orders_report' ||
                $model == 'invoice-balancing-report' ||
                $model == 'onsite-vs-offsite-shifts-report' ||
                $model == 'detailed-sales-summary-report' ||
                $model == 'pos-overview-report' ||
                $model == 'sales-and-receivables-reports' ||
                $model == 'dispatched_items_report')): ?> active <?php endif; ?>">
        <a href="<?php echo route('sales-and-receivables-reports.index'); ?>"><i class="fa fa-circle" aria-hidden="true"></i>
            Reports
        </a>
    </li>
<?php endif; ?>

</ul>
</li>
<?php endif; ?>
<?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/includes/sidebar_includes/sales_and_receivables.blade.php ENDPATH**/ ?>