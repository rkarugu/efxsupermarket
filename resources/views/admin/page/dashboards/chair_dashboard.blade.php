@extends('layouts.admin.admin')

@section('content')
    
    @php
        $logged_user_info = getLoggeduserProfile();
        $my_permissions = $logged_user_info->permissions;
        $route_name = \Route::currentRouteName();
    @endphp

    <section class="content">
        <div class="session-message-container">
            @include('message')
        </div>

        <div class="box box-primary">
            <div class="box-body">
                @if($user->role_id == 1 || isset($user->permissions['chairmans-dashboard___view']))
                    <div class="row">
                        <div class="col-lg-6 col-xs-12">
                            <div class="small-box bg-green">
                                <div class="inner">
                                    <h3>Sales Summary</h3>
                                </div>
                                <div class="icon" style="font-size: 50px;margin-top:10px">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <a href="{!! route('chairman-dashboard.general.index') !!}" target="_blank" class="small-box-footer">Go To <i
                                        class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-6 col-xs-12">
                            <div class="small-box bg-green">
                                <div class="inner">
                                    <h3>Sales Report</h3>
                                </div>
                                <div class="icon" style="font-size: 50px;margin-top:10px">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <a href="{!! route('chairman-dashboard.general.index.sales-report') !!}" target="_blank" class="small-box-footer">Go To <i
                                        class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                @endif
                @if($user->role_id == 1 || isset($user->permissions['chairmans-dashboard___view']))
                    <div class="row">
                        <div class="col-lg-12 col-xs-12">
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3>Chairman's Dashboard</h3>
                                </div>
                                <div class="icon" style="font-size: 50px;margin-top:10px">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <a href="{!! route('chair-sales-reports.index') !!}" target="_blank" class="small-box-footer">Go To <i
                                        class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($logged_user_info->role_id == 1 || isset($my_permissions['hq-dashboard___view']))
                <div class="row">
                    <div class="col-lg-12 col-xs-12">
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h3>HQ Dashboard</h3>
                            </div>
                            <div class="icon" style="font-size: 50px;margin-top:10px">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <a href="{{ route('hq-dashboard.index') }}" class="small-box-footer">Go To <i
                                    class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
                @endif

                @if ($logged_user_info->role_id == 1 || isset($my_permissions['procurement-dashboard___view']))
                <div class="row">
                    <div class="col-lg-12 col-xs-12">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h3>Procurement Dashboard</h3>
                            </div>
                            <div class="icon" style="font-size: 50px;margin-top:10px">
                                <i class="ion ion-pie-graph"></i>
                            </div>
                            <a href="{{ route('procurement-dashboard.index') }}" target="_blank" class="small-box-footer">Go
                                To <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
                @endif

                @if ($logged_user_info->role_id == 1 || isset($my_permissions['petty-cash-dashboard___view']))
                <div class="row">
                    <div class="col-lg-12 col-xs-12">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3>Petty Cash Reports</h3>
                            </div>
                            <div class="icon" style="font-size: 50px;margin-top:10px">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <a href="{!! route('chair-petty-cash-reports.index') !!}" class="small-box-footer">Go To <i
                                    class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
                @endif

                @if ($logged_user_info->role_id == 1 || isset($my_permissions['payments-dashboard___view']))
                <div class="row">
                    <div class="col-lg-12 col-xs-12">
                        <div class="small-box bg-blue">
                            <div class="inner">
                                <h3>Payments Reports</h3>
                            </div>
                            <div class="icon" style="font-size: 50px;margin-top:10px">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <a href="" class="small-box-footer">Go To <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
                @endif

                @if ($logged_user_info->role_id == 1 || isset($my_permissions['profitability-dashboard___view']))
                <div class="row">
                    <div class="col-lg-12 col-xs-12">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h3>Profitability Reports</h3>
                            </div>
                            <div class="icon" style="font-size: 50px;margin-top:10px">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <a href="" class="small-box-footer">Go To <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </section>
@endsection

@section('uniquepagescriptforchart')
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('styles')
    <style>
        .category-panel {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 15px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 10px;
            transition: background-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            margin-bottom: 10px;
            text-decoration: none;
        }

        .category-panel a {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: inherit;
        }

        .category-panel i {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .category-panel:hover {
            background-color: #007bff;
            color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 768px) {
            .category-panel {
                padding: 10px;
                font-size: 14px;
            }

            .category-panel i {
                font-size: 20px;
            }
        }
    </style>
@endsection
