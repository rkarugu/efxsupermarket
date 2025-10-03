<?php if($logged_user_info->role_id == 1 || isset($my_permissions['account-payables___view'])): ?>
    <li class="treeview <?php if(isset($model) &&
            ($model == 'account-payables-reports' ||
                $model == 'maintain-suppliers' ||
                $model == 'suppliers-overview' ||
                $model == 'vat-report' ||
                $model == 'pending-grns' ||
                $model == 'advance-payments' ||
                $model == 'grns-against-invoices' ||
                $model == 'supplier-statement' ||
                $model == 'supplier-ledger-report' ||
                $model == 'trade-discounts' ||
                $model == 'trade-discount-demands' ||
                $model == 'payment-vouchers-report' ||
                $model == 'trade-agreement-change-request-list' ||
                $model == 'supplier-aging-analysis' ||
                $model == 'supplier-listing' ||
                $model == 'supplier-bank-listing' ||
                $model == 'withholding-tax-payments' ||
                $model == 'suppliers-invoice' ||
                $model == 'payment-vouchers' ||
                $model == 'item-demands' ||
                $model == 'return-demands' ||
                $model == 'credit-debit-notes' ||
                $model == 'supplier-bills' ||
                $model == 'pending-suppliers' ||
                $model == 'processed-invoices' ||
                $model == 'bank-files' ||
                $model == 'trade-agreement' ||
                $model == 'bank-payments-report' ||
                $model == 'trade-discounts-report' ||
                $model == 'trade-discount-demands-report' ||
                $model == 'withholding-tax-payments-report' ||
                $model == 'withholding-files')): ?> active <?php endif; ?>">
        <a href="#"><i class="fa fa-fw fa-credit-card"></i><span>Accounts Payables</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>

        <ul class="treeview-menu">
            <li class="treeview <?php if(isset($model) && ($model == 'maintain-suppliers' || $model == 'suppliers-overview' || $model == 'trade-agreement')): ?> active <?php endif; ?>">
                <a href="#"><i class="fa fa-circle"></i><span>Maintain
                        Suppliers</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>

                <ul class="treeview-menu">
                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['suppliers-overview___view'])): ?>
                        <li class="<?php if(isset($model) && $model == 'suppliers-overview'): ?> active <?php endif; ?>">
                            <a href="<?php echo route('suppliers-overview.index'); ?>"><i class="fa fa-circle"></i>Dashboard</a>
                        </li>
                    <?php endif; ?>
                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['maintain-suppliers___view'])): ?>
                        <li class="<?php if(isset($model) && $model == 'maintain-suppliers'): ?> active <?php endif; ?>">
                            <a href="<?php echo route('maintain-suppliers.index'); ?>"><i class="fa fa-circle"></i>Suppliers</a>
                        </li>
                    <?php endif; ?>
                    <?php if($logged_user_info->role_id == 1 || isset($my_permissions['trade-agreement___trade-agreement-view'])): ?>
                        <li class="<?php if(isset($model) && $model == 'trade-agreement'): ?> active <?php endif; ?>">
                            <a href="<?php echo route('trade-agreement.index'); ?>"><i class="fa fa-circle"></i>Trade
                                Agreements</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['pending-grns___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'pending-grns'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('pending-grns.index'); ?>"><i class="fa fa-circle"></i>Pending GRNs</a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['suppliers-invoice___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'processed-invoices'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('maintain-suppliers.processed_invoices.index'); ?>"><i class="fa fa-circle"></i>Processed
                        Invoices</a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['advance-payments___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'advance-payments'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('advance-payments.index'); ?>"><i class="fa fa-circle"></i>
                        Advance Payments</a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['payment-vouchers___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'payment-vouchers'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('payment-vouchers.index'); ?>"><i class="fa fa-circle"></i>Payment
                        Vouchers</a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['bank-files___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'bank-files'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('bank-files.index'); ?>"><i class="fa fa-circle"></i>Generate Bank
                        File</a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['withholding-files___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'withholding-files'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('withholding-files.index'); ?>"><i class="fa fa-circle"></i>Process
                        Withholding Tax</a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['withholding-tax-payments___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'withholding-tax-payments'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('withholding-tax-payments.index'); ?>"><i class="fa fa-circle"></i>
                        Withholding Tax Payments</a>
                </li>
            <?php endif; ?>

            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['credit-debit-notes___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'credit-debit-notes'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('credit-debit-notes.index'); ?>"><i class="fa fa-circle"></i>Credit/Debit
                        Notes</a>
                </li>
            <?php endif; ?>
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['supplier-bills___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'supplier-bills'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('supplier-bills.index'); ?>"><i class="fa fa-circle"></i>
                        Supplier Bills
                    </a>
                </li>
            <?php endif; ?>
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['maintain-suppliers___view'])): ?>
                <li class="treeview <?php if(request()->routeIs(
                        'maintain-suppliers.supplier_unverified_list',
                        'maintain-suppliers.supplier_unverified_edit_list')): ?> active <?php endif; ?>">
                    <a href="#">
                        <i class="fa fa-circle"></i> Approve Supplier
                        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                    </a>


                    <ul class="treeview-menu">
                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['maintain-suppliers___approve-new-supplier'])): ?>
                            <li class="<?php if(request()->routeIs('maintain-suppliers.supplier_unverified_list')): ?> active <?php endif; ?>">
                                <a href="<?php echo route('maintain-suppliers.supplier_unverified_list'); ?>">
                                    <i class="fa fa-circle"></i> New Requests
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if($logged_user_info->role_id == 1 || isset($my_permissions['maintain-suppliers___approve-edits-supplier'])): ?>
                            <li class="<?php if(request()->routeIs('maintain-suppliers.supplier_unverified_edit_list')): ?> active <?php endif; ?>">
                                <a href="<?php echo route('maintain-suppliers.supplier_unverified_edit_list'); ?>">
                                    <i class="fa fa-circle"></i> Edit Requests
                                </a>
                            </li>
                        <?php endif; ?>


                    </ul>
                </li>
            <?php endif; ?>
            <?php if(
                $logged_user_info->role_id == 1 ||
                    isset($my_permissions['maintain-suppliers___trade-agreement-change-request-list'])): ?>
                <li class="treeview <?php if(isset($model) && $model == 'trade-agreement-change-request-list'): ?> active <?php endif; ?>">
                    <a href="#">
                        <i class="fa fa-circle"></i> Approve Price Change
                        <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                    </a>

                    <ul class="treeview-menu">
                        <?php if(
                            $logged_user_info->role_id == 1 ||
                                isset($my_permissions['maintain-suppliers___trade-agreement-change-request-list'])): ?>
                            <li class="<?php if(isset($model) && $model == 'trade-agreement-change-request-list'): ?> active <?php endif; ?>">
                                <a href="<?php echo route('maintain-suppliers.tradeAgreementChangeRequestList'); ?>">
                                    <i class="fa fa-circle"></i> Pending Requests
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['item-demands___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'item-demands'): ?> active <?php endif; ?>">
                    
                    <a href="<?php echo route('demands.item-demands.new'); ?>"><i class="fa fa-circle"></i>Price
                        Demands</a>

                </li>
            <?php endif; ?>
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['return-demands___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'return-demands'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('return-demands.index'); ?>"><i class="fa fa-circle"></i>Return Demands</a>
                </li>
            <?php endif; ?>
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['trade-discount-demands___view'])): ?>
                <li class="<?php if(isset($model) && $model == 'trade-discount-demands'): ?> active <?php endif; ?>">
                    <a href="<?php echo route('trade-discount-demands.index'); ?>">
                        <i class="fa fa-circle"></i>Trade Discount Demands</a>
                </li>
            <?php endif; ?>
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['account-payables-reports___view'])): ?>
                <li class="<?php if(isset($model) &&
                        ($model == 'account-payables-reports' ||
                            $model == 'supplier-aging-analysis' ||
                            $model == 'vat-report' ||
                            $model == 'supplier-statement' ||
                            $model == 'supplier-ledger-report' ||
                            $model == 'payment-vouchers-report' ||
                            $model == 'bank-payments-report' ||
                            $model == 'supplier-listing' ||
                            $model == 'grns-against-invoices' ||
                            $model == 'withholding-tax-payments-report' ||
                            $model == 'trade-discounts-report' ||
                            $model == 'trade-discount-demands-report' ||
                            $model == 'supplier-bank-listing')): ?> active <?php endif; ?>">
                    <a href="<?php echo route('account-payables-reports.index'); ?>"><i class="fa fa-circle"></i>
                        Reports
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </li>
<?php endif; ?>
<?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/includes/sidebar_includes/accounts_payable.blade.php ENDPATH**/ ?>