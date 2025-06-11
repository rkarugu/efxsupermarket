@if ($logged_user_info->role_id == 1 || isset($my_permissions['supplier-portal___view']))
    <li class="treeview @if (isset($model) &&
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
                $model == 'order-delivery-slots')) active @endif">

        <a href="#"><i class="fa fa-cubes"></i><span>Supplier Portal</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>

        <ul class="treeview-menu">
            @if ($logged_user_info->role_id == 1 || isset($my_permissions['supplier-maintain-suppliers___view']))
                <li class="@if (isset($model) && $model == 'supplier-portal') active @endif">
                    <a href="{!! route('supplier-portal.get_all_supplier_from_portal') !!}">
                        <i class="fa fa-circle"></i> Onboarded Suppliers
                    </a>
                </li>
            @endif
            @if ($logged_user_info->role_id == 1 || isset($my_permissions['pending-suppliers___view']))
                <li class="@if (isset($model) && $model == 'pending-suppliers') active @endif">
                    <a href="{!! route('supplier-portal.pending-suppliers') !!}">
                        <i class="fa fa-circle"></i> Pending Invites
                    </a>
                </li>
            @endif

            @if ($logged_user_info->role_id == 1 || isset($my_permissions['order-delivery-slots___view']))
                <li class="@if (isset($model) && $model == 'order-delivery-slots') active @endif">
                    <a href="{!! route('order-delivery-slots.delivery_branches') !!}">
                        <i class="fa fa-circle"></i> LPO Delivery Slots
                    </a>
                </li>
            @endif

            @if ($logged_user_info->role_id == 1 || isset($my_permissions['supplier-vehicle-type___view']))
                <li class="@if (isset($model) && $model == 'supplier-vehicle-type') active @endif">
                    <a href="{!! route('supplier-vehicle-type.index') !!}">
                        <i class="fa fa-circle"></i> Vehicle Type
                    </a>
                </li>
            @endif
            
            @if ($logged_user_info->role_id == 1 || isset($my_permissions['suggested-order___view']))
                <li class="@if (isset($model) && $model == 'suggested_order') active @endif">
                    <a href="{!! route('suggested-order.index') !!}"><i class="fa fa-circle"></i>
                        Suggested Order</a>
                </li>
            @endif

            @if ($logged_user_info->role_id == 1 || isset($my_permissions['order-delivery-slots___show']))
                <li class="@if (isset($model) && $model == 'order-delivery-slots') active @endif">
                    <a href="{!! route('order-delivery-slots.show_booked_slots') !!}">
                        <i class="fa fa-circle"></i> LPO Booked Slots
                    </a>
                </li>
            @endif
            @if ($logged_user_info->role_id == 1 || isset($my_permissions['lpo-portal-req-approval___view']))
                <li class="@if (isset($model) && $model == 'lpo-portal-req-approval') active @endif">
                    <a href="{!! route('lpo-portal-req-approval.index') !!}"><i class="fa fa-circle"></i>
                        Approve LPO Changes </a>
                </li>
            @endif

            @if ($logged_user_info->role_id == 1 || isset($my_permissions['price-list-cost-change___view']))
                <li class="@if ((isset($model) && $model == 'price-list-cost-change') || $model == 'maintain-items-manual-cost-change') active @endif"><a href="{!! route('maintain-items.approve-price-list-change') !!}"><i
                            class="fa fa-circle"></i>
                        Aprrove Price List Changes</a>
                </li>
            @endif

            @if ($logged_user_info->role_id == 1 || isset($my_permissions['api-call-logs___view']))
                <li class="@if (isset($model) && $model == 'api-call-logs') active @endif">
                    <a href="{!! route('api_call_logs.index') !!}">
                        <i class="fa fa-circle"></i> Api Call Logs
                    </a>
                </li>
            @endif

            {{-- Temporarily disabled due to missing route
            @if ($logged_user_info->role_id == 1 || isset($my_permissions['supplier-portal___logs']))
                <li class="@if (isset($model) && $model == 'supplier-portal') active @endif">
                    <a href="#">
                        <i class="fa fa-circle"></i> Logs
                    </a>
                </li>
            @endif
            --}}

            @if ($logged_user_info->role_id == 1 || isset($my_permissions['email-templates___view']))
                <li class="@if (isset($model) && $model == 'email-templates') active @endif">
                    <a href="{!! route('admin.email_templates.index') !!}">
                        <i class="fa fa-circle"></i> Email Templates
                    </a>
                </li>
            @endif

            @if ($logged_user_info->role_id == 1 || isset($my_permissions['billing-description_view']))
                <li class="@if (isset($model) && $model == 'billing-description') active @endif">
                    <a href="{!! route('supplier-portal.billing-description') !!}">
                        <i class="fa fa-circle"></i> Billing Note
                    </a>
                </li>
            @endif

            @if ($logged_user_info->role_id == 1 || isset($my_permissions['request-new-sku___view']))
                <li class="@if (isset($model) && $model == 'request-new-sku') active @endif">
                    <a href="{!! route('request-new-sku.index') !!}">
                        <i class="fa fa-circle"></i> New SKU's Requests
                    </a>
                </li>
            @endif

            @if ($logged_user_info->role_id == 1 || isset($my_permissions['approve-bank-deposits___view']))
                <li class="treeview @if (
                    (isset($model) && $model == 'approve-bank-deposits') ||
                        $model == 'supplier-bank-deposits-initial-approval' ||
                        $model == 'supplier-bank-deposits-final-approval') active @endif">
                    <a href="#"><i class="fa fa-circle"></i> Approve Bank Desposits
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['supplier-bank-deposits-initial-approval___view']))
                            <li class="@if (isset($model) && $model == 'supplier-bank-deposits-initial-approval') active @endif"><a
                                    href="{!! route('supplier_bank_deposits_initial_approval.index') !!}"><i class="fa fa-circle"></i>
                                    Initial Approval</a></li>
                        @endif
                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['supplier-bank-deposits-final-approval___view']))
                            <li class="@if (isset($model) && $model == 'supplier-bank-deposits-final-approval') active @endif"><a
                                    href="{!! route('supplier_bank_deposits_final_approval.index') !!}"><i class="fa fa-circle"></i>
                                    Final Approval</a></li>
                        @endif
                    </ul>
                </li>
            @endif

            @if ($logged_user_info->role_id == 1 || isset($my_permissions['supplier-billing___view']))
                <li class="treeview @if (
                    (isset($model) && $model == 'supplier-billing') ||
                        $model == 'billing-submitted' ||
                        $model == 'billing-submitted-final') active @endif">
                    <a href="#"><i class="fa fa-circle"></i> Billing
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['billing-submitted___view']))
                            <li class="@if (isset($model) && $model == 'billing-submitted') active @endif"><a
                                    href="{!! route('billings_submitted') !!}"><i class="fa fa-circle"></i>
                                    Initial Approval Billings</a></li>
                        @endif
                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['billing-submitted-final___view']))
                            <li class="@if (isset($model) && $model == 'billing-submitted-final') active @endif"><a
                                    href="{!! route('billings_submitted_final') !!}"><i class="fa fa-circle"></i>
                                    Final Approval Billings</a></li>
                        @endif
                        {{-- @if ($logged_user_info->role_id == 1 || isset($my_permissions['billings-bank-deposits___view']))
                            <li class="@if (isset($model) && $model == 'billings-bank-deposits') active @endif"><a
                                    href="{!! route('billings_bank_deposits') !!}"><i class="fa fa-circle"></i>
                                    Billing Bank Deposits</a></li>
                        @endif
                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['billing-settings___view']))
                            <li class="@if (isset($model) && $model == 'billing-settings') active @endif"><a
                                    href="{!! route('billings_submitted') !!}"><i class="fa fa-circle"></i>
                                    Billing Settings</a></li>
                        @endif --}}
                    </ul>
                </li>
            @endif

        </ul>
    </li>
@endif
