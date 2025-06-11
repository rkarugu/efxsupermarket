@if ($logged_user_info->role_id == 1 || isset($my_permissions['fleet-management-module___view']))
    <li class="treeview @if (isset($model) &&
                    ($model == 'vehicles' ||
                        // $model == 'fueling-stations' ||
                        // $model == 'fuel-lpos' ||
                        // $model == 'pending-fuel-lpos' ||
                        // $model == 'verified-fuel-lpos' ||
                        // $model == 'approved-fuel-lpos' ||
                        $model == 'vehicle-suppliers' ||
                        $model == 'vehicles-overview' ||
                        // $model == 'fuel-suppliers' ||
                        $model == 'vehicle-command-center' ||
                        $model == 'vehicle-command-center-exemption-schedules' ||
                        $model == 'vehicle-command-center-custom-schedules' ||
                        // $model == 'confirmed-fuel-lpos' ||
                        // $model == 'expired-fuel-lpos' ||
                        $model == 'vehicle-models')) active @endif">
        <a href="#"><i class="fa fa-truck"></i><span>Fleet Management </span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
        </a>

        <ul class="treeview-menu">
            <li class="treeview @if (isset($model) && ($model == 'vehicle-suppliers' ||  $model == 'vehicle-command-center-exemption-schedules' || $model == 'vehicle-command-center-custom-schedules' ||
                $model == 'vehicle-command-center' || $model == 'vehicles' || $model == 'vehicles-overview' || $model == 'vehicle-models')) active @endif">
                <a href="#"><i class="fa fa-circle"></i><span> Fleet </span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>

                <ul class="treeview-menu">
                    @if ($logged_user_info->role_id == 1 || isset($my_permissions['vehicles-overview___view']))
                        <li class="@if (isset($model) && $model == 'vehicles-overview') active @endif">
                            <a href="{{route('vehicle-overview-all')}}"><i class="fa fa-circle" aria-hidden="true"></i>
                                Live Tracking
                            </a>
                        </li>
                    @endif

                    @if ($logged_user_info->role_id == 1 || isset($my_permissions['vehicles-overview___listing']))
                        <li class="@if (isset($model) && $model == 'vehicles') active @endif">
                            <a href="{!! route('vehicles.index') !!}"><i class="fa fa-circle" aria-hidden="true"></i>
                                Vehicle Listing
                            </a>
                        </li>
                    @endif
                    @if ($logged_user_info->role_id == 1 || isset($my_permissions['vehicle-command-center___view']))
                        <li class="@if (isset($model) && $model == 'vehicle-command-center') active @endif">
                            <a href="{!! route('vehicle-command-center') !!}"><i class="fa fa-circle" aria-hidden="true"></i>
                                Control Centre
                            </a>
                        </li>
                    @endif
                    @if ($logged_user_info->role_id == 1 || isset($my_permissions['vehicle-command-center___exemption-schedules']))
                        <li class="@if (isset($model) && $model == 'vehicle-command-center-exemption-schedules') active @endif">
                            <a href="{!! route('exemption-schedules') !!}"><i class="fa fa-circle" aria-hidden="true"></i>
                                Exemption Schedules
                            </a>
                        </li>
                    @endif
                    @if ($logged_user_info->role_id == 1 || isset($my_permissions['vehicle-command-center-custom-schedules___view']))
                        <li class="@if (isset($model) && $model == 'vehicle-command-center-custom-schedules') active @endif">
                            <a href="{!! route('custom-schedules') !!}"><i class="fa fa-circle" aria-hidden="true"></i>
                                Custom Schedules
                            </a>
                        </li>
                    @endif

                    @if ($logged_user_info->role_id == 1 || isset($my_permissions['vehicle-suppliers___view']) || isset($my_permissions['vehicle-models___view']))
                        <li class="treeview @if (isset($model) && ($model == 'vehicle-suppliers' ||  $model == 'vehicle-models')) active @endif">
                            <a href="#"><i class="fa fa-circle"></i><span> Set Up </span>
                                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                            </a>
                            <ul class="treeview-menu">
                                @if ($logged_user_info->role_id == 1 || isset($my_permissions['vehicle-suppliers___view']))
                                    <li class="@if (isset($model) && $model == 'vehicle-suppliers') active @endif">
                                        <a href="{!! route('vehicle-suppliers.index') !!}"><i class="fa fa-circle" aria-hidden="true"></i>
                                            Vehicle Suppliers
                                        </a>
                                    </li>
                                @endif

                                @if ($logged_user_info->role_id == 1 || isset($my_permissions['vehicle-models___view']))
                                    <li class="@if (isset($model) && $model == 'vehicle-models') active @endif">
                                        <a href="{!! route('vehicle-models.index') !!}"><i class="fa fa-circle" aria-hidden="true"></i>
                                            Vehicle Models
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                </ul>
            </li>

            {{-- <li class="treeview @if (isset($model) && in_array($model, ['fuel-suppliers', 'fueling-stations', 'pending-fuel-lpos', 'fuel-lpos', 'confirmed-fuel-lpos', 'expired-fuel-lpos'])) active @endif">
                <a href="#"><i class="fa fa-circle"></i><span> Fuel Management </span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                </a>

                <ul class="treeview-menu">
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

                    @if ($logged_user_info->role_id == 1 || isset($my_permissions['fuel-lpos___view']))
                        <li class="@if (isset($model) && $model == 'fuel-lpos') active @endif">
                            <a href="{{ route('fuel-lpos.index') }}"><i class="fa fa-circle" aria-hidden="true"></i>
                                Purchase Orders
                            </a>
                        </li>
                    @endif

                    <li class="treeview @if (isset($model) && in_array($model, ['pending-fuel-lpos', 'verified-fuel-lpos', 'approved-fuel-lpos','confirmed-fuel-lpos', 'expired-fuel-lpos'])) active @endif">
                        <a href="#"><i class="fa fa-circle"></i><span> Fuel Entries </span>
                            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                        </a>

                        <ul class="treeview-menu">
                            @if ($logged_user_info->role_id == 1 || isset($my_permissions['pending-fuel-lpos___view']))
                                <li class="@if (isset($model) && $model == 'pending-fuel-lpos') active @endif">
                                    <a href="{{ route('fuel-lpos.pending') }}"><i class="fa fa-circle" aria-hidden="true"></i>
                                        Pending Verification
                                    </a>
                                </li>
                            @endif

                            @if ($logged_user_info->role_id == 1 || isset($my_permissions['confirmed-fuel-lpos___view']))
                                <li class="@if (isset($model) && $model == 'confirmed-fuel-lpos') active @endif">
                                    <a href="{{ route('fuel-lpos.confirmed') }}"><i class="fa fa-circle" aria-hidden="true"></i>
                                        Confirmed Entries
                                    </a>
                                </li>
                            @endif

                            @if ($logged_user_info->role_id == 1 || isset($my_permissions['pending-fuel-lpos___view']))
                                <li class="@if (isset($model) && $model == 'Approved-fuel-lpos') active @endif">
                                    <a href="{{ route('fuel-lpos.pending') }}"><i class="fa fa-circle" aria-hidden="true"></i>
                                        Approved Entries
                                    </a>
                                </li>
                            @endif
                            @if ($logged_user_info->role_id == 1 || isset($my_permissions['expired-fuel-lpos___view']))
                                <li class="@if (isset($model) && $model == 'expired-fuel-lpos') active @endif">
                                    <a href="{{ route('fuel-lpos.expired') }}"><i class="fa fa-circle" aria-hidden="true"></i>
                                        Expired Entries
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                </ul>
            </li> --}}
        </ul>
    </li>
@endif