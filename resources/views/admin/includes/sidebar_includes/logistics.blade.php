@php
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
@endphp
 
@if ($logged_user_info->role_id == 1 || isset($my_permissions['delivery_and_logistics___view']))
    <li class="treeview @if (isset($model) && in_array($model, $allModels)) active @endif">
        <a href="#"><i class="fa fa-truck-loading"></i><span> Delivery & Logistics </span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
        </a>

        <ul class="treeview-menu">
            <li class="treeview @if (isset($model) && in_array($model, ['fuel-suppliers', 'fueling-stations', 'fuel-lpos', 'fuel-entries', 'consumption-report', 'fuel-statements',
'fuel-verification', 'fuel-approval'])) active @endif">
                <a href="#"><i class="fa fa-circle"></i><span> Fuel Management </span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>

                <ul class="treeview-menu">
                    @if ($logged_user_info->role_id == 1 || isset($my_permissions['fuel-entries___see-overview']))
                        <li class="@if (isset($model) && $model == 'pending-fuel-lpos') active @endif">
                            <a href="{{ route('fuel-entry-confirmation.overview') }}"><i class="fa fa-circle" aria-hidden="true"></i>
                                Overview
                            </a>
                        </li>
                    @endif

                    <li class="treeview @if (isset($model) && in_array($model, ['fuel-statements', 'fuel-verification'])) active @endif">
                        <a href="#"><i class="fa fa-circle"></i><span> Verification </span>
                            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                        </a>

                        <ul class="treeview-menu">
                            @if ($logged_user_info->role_id == 1 || isset($my_permissions['fuel-statements___view']))
                                <li class="@if (isset($model) && $model == 'fuel-statements') active @endif">
                                    <a href="{{ route('fuel-statements.listing') }}"><i class="fa fa-circle" aria-hidden="true"></i>
                                        Fuel Statements
                                    </a>
                                </li>
                            @endif

                            @if ($logged_user_info->role_id == 1 || isset($my_permissions['fuel-verification___view']))
                                <li class="@if (isset($model) && $model == 'fuel-verification') active @endif">
                                    <a href="{{ route('fuel-verification.listing') }}"><i class="fa fa-circle" aria-hidden="true"></i>
                                        Verification Records
                                    </a>
                                </li>
                            @endif

                            {{--                            @if ($logged_user_info->role_id == 1 || isset($my_permissions['confirmed-fuel-lpos___view']))--}}
                            {{--                                <li class="@if (isset($model) && $model == 'confirmed-fuel-lpos') active @endif">--}}
                            {{--                                    <a href="{{ route('fuel-lpos.confirmed') }}"><i class="fa fa-circle" aria-hidden="true"></i>--}}
                            {{--                                        Confirmed Entries--}}
                            {{--                                    </a>--}}
                            {{--                                </li>--}}
                            {{--                            @endif--}}

                            {{--                            @if ($logged_user_info->role_id == 1 || isset($my_permissions['approved-fuel-lpos___view']))--}}
                            {{--                                <li class="@if (isset($model) && $model == 'approved-fuel-lpos') active @endif">--}}
                            {{--                                    <a href="{{ route('fuel-lpos.processed') }}"><i class="fa fa-circle" aria-hidden="true"></i>--}}
                            {{--                                        Approved Entries--}}
                            {{--                                    </a>--}}
                            {{--                                </li>--}}
                            {{--                            @endif--}}
                            {{--                            @if ($logged_user_info->role_id == 1 || isset($my_permissions['expired-fuel-lpos___view']))--}}
                            {{--                                <li class="@if (isset($model) && $model == 'expired-fuel-lpos') active @endif">--}}
                            {{--                                    <a href="{{ route('fuel-lpos.expired') }}"><i class="fa fa-circle" aria-hidden="true"></i>--}}
                            {{--                                        Expired Entries--}}
                            {{--                                    </a>--}}
                            {{--                                </li>--}}
                            {{--                            @endif--}}
                            {{--                            @if ($logged_user_info->role_id == 1 || isset($my_permissions['expired-fuel-lpos___view']))--}}
                            {{--                                <li class="@if (isset($model) && $model == 'consumption-report') active @endif">--}}
                            {{--                                    <a href="{{ route('fuel_consumption_reports.index') }}"><i class="fa fa-circle" aria-hidden="true"></i>--}}
                            {{--                                        Consumption Reports--}}
                            {{--                                    </a>--}}
                            {{--                                </li>--}}
                            {{--                            @endif--}}
                        </ul>

                    @if ($logged_user_info->role_id == 1 || isset($my_permissions['fuel-approval___approve']))
                        <li class="@if (isset($model) && $model == 'fuel-approval') active @endif">
                            <a href="{{ route('fuel-approval.index') }}"><i class="fa fa-circle" aria-hidden="true"></i>
                                Approval
                            </a>
                        </li>
                @endif
            </li>

            @if ($logged_user_info->role_id == 1 || isset($my_permissions['fuel-suppliers___view']))
                <li class="@if (isset($model) && $model == 'fuel-suppliers') active @endif">
                    <a href="{{route('fuel-suppliers.index')}}"><i class="fa fa-circle" aria-hidden="true"></i>
                        Fuel Suppliers
                    </a>
                </li>
            @endif

            @if ($logged_user_info->role_id == 1 || isset($my_permissions['fuel-suppliers___view']))
                <li class="@if (isset($model) && $model == 'fueling-stations') active @endif">
                    <a href="{{ route('fuel-stations.index') }}"><i class="fa fa-circle" aria-hidden="true"></i>
                        Fueling Stations
                    </a>
                </li>
            @endif

            {{--                    @if ($logged_user_info->role_id == 1 || isset($my_permissions['fuel-lpos___view']))--}}
            {{--                        <li class="@if (isset($model) && $model == 'fuel-lpos') active @endif">--}}
            {{--                            <a href="{{ route('fuel-lpos.index') }}"><i class="fa fa-circle" aria-hidden="true"></i>--}}
            {{--                                Pending LPOs--}}
            {{--                            </a>--}}
            {{--                        </li>--}}
            {{--                    @endif--}}
        </ul>
    </li>
    @if ($logged_user_info->role_id == 1 || isset($my_permissions['small-packs___view']))
        <li class="treeview @if (isset($model) &&
                ($model == 'small-packs-store-loading-sheets' ||
                $model == 'small-packs-dispatched-loading-sheets' ||
                $model == 'view-loading-sheets' ||
                $model == 'dispatched-loading-sheets' ||
                $model == 'dispatched-sheets-view')) active @endif">
            <a href="#">
                <i class="fa fa-circle"></i>Small Packs
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>

            <ul class="treeview-menu">
                @if ($logged_user_info->role_id == 1 || isset($my_permissions['small-packs___store-loading-sheets']))
                    <li class="@if (isset($model) && $model == 'small-packs-store-loading-sheets') active @endif">
                        <a href="{!! route('small-packs.store-loading-sheets') !!}">
                            <i class="fa fa-circle"></i>Dispatch Requests
                        </a>
                    </li>
                @endif
                @if ($logged_user_info->role_id == 1 || isset($my_permissions['small-packs___dispatched-loading-sheets']))
                    <li class="@if (isset($model) && $model == 'small-packs-dispatched-loading-sheets') active @endif">
                        <a href="{!! route('small-packs.dispatched') !!}">
                            <i class="fa fa-circle"></i>Dispatched Loading Sheets
                        </a>
                    </li>
                @endif
                
            </ul>
        </li>
    @endif
    @if ($logged_user_info->role_id == 1 || isset($my_permissions['delivery_and_logistics___reports']))
        <li class="@if (isset($model) && $model == 'custom-delivery-shifts') active @endif">
            <a href="{!! route('vehicle-suppliers.index') !!}"><i class="fa fa-circle" aria-hidden="true"></i>
                Reports
            </a>
        </li>
        @endif
    @if ($logged_user_info->role_id == 1 || isset($my_permissions['device-management___view']))
        <li class="treeview @if (isset($model) &&
                ($model == 'device-type' || $model == 'device-sim-card' || $model == 'device-center' || $model == 'device-repair')) active @endif">
            <a href="#">
                <i class="fa fa-circle"></i>Device Management
                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
            </a>

            <ul class="treeview-menu">
                @if ($logged_user_info->role_id == 1 || isset($my_permissions['device-type___view']))
                    <li class="@if (isset($model) && $model == 'device-type') active @endif">
                        <a href="{!! route('device-type.index') !!}">
                            <i class="fa fa-circle"></i>Device Type
                        </a>
                    </li>
                @endif
                @if ($logged_user_info->role_id == 1 || isset($my_permissions['device-sim-card___view']))
                    <li class="@if (isset($model) && $model == 'device-sim-card') active @endif">
                        <a href="{!! route('device-sim-card.index') !!}">
                            <i class="fa fa-circle"></i>Device Sim Card
                        </a>
                    </li>
                @endif
                @if ($logged_user_info->role_id == 1 || isset($my_permissions['device-repair___view']))
                    <li class="@if (isset($model) && $model == 'device-repair') active @endif">
                        <a href="{!! route('device-repair.index') !!}">
                            <i class="fa fa-circle"></i>Device Repair
                        </a>
                    </li>
                @endif
                @if ($logged_user_info->role_id == 1 || isset($my_permissions['device-center___view']))
                    <li class="@if (isset($model) && $model == 'device-center') active @endif">
                        <a href="{!! route('device-center.index') !!}">
                            <i class="fa fa-circle"></i>Device Center
                        </a>
                    </li>
                @endif
                
            </ul>
        </li>
    @endif
    
        </ul>
        </li>
    @endif