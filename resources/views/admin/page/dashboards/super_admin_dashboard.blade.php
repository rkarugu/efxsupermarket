@extends('layouts.admin.admin')
@section('content')
    <?php
    $logged_user_info_dash = getLoggeduserProfile();
    $my_permissions_dash = $logged_user_info_dash->permission;
    $route_name_dash = \Route::currentRouteName();
    
    ?>

    <section class="content">
        <div class="session-message-container">
            @include('message')
        </div>

        <div class="box box-primary">
            <div class="row flex-row" style="padding: 0 10px 0 10px">
                <div class="col-md-3 dashboard-card">
                    <div class="card-content">
                        <p class="title">Daily Average Sales <span class="change increase"><i
                                    class="fas fa-arrow-up slanted-up"></i> 10%</span></p>
                        <p class="value">$1,234</p>
                        <div class="show-more">
                            <a href="#">Show More</a><i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 dashboard-card">
                    <div class="card-content">
                        <p class="title">Daily Average Profit <span class="change decrease"><i
                                    class="fas fa-arrow-down slanted-down"></i> 5%</span></p>
                        <p class="value">$3,345</p>
                        <div class="show-more">
                            <a href="#">Show More</a><i class="fas fa-cart-arrow-down"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 dashboard-card">
                    <div class="card-content">
                        <p class="title">Daily Served Customers <span class="change increase"><i
                                    class="fas fa-arrow-up slanted-up"></i> 20%</span></p>
                        <p class="value">78</p>
                        <div class="show-more">
                            <a href="#">Show More</a><i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 dashboard-card">
                    <div class="card-content">
                        <p class="title">Total Branches: <span class="value">20</span></p>
                        <p class="title">Total Routes: <span class="value">51</span></p>
                        <p class="title">Total Customers: <span class="value">5000</span></p>
                    </div>
                </div>
            </div>

            <div class="row flex-row" style="padding: 0 10px 0 10px">
                <div class="col-md-3">
                    <div class="dashboard-card">
                        @include('admin.page.dashboards.includes.fall_in_profit_reasons')
                    </div>
                </div>
                <div class="col-md-6 dashboard-card">
                    @include('admin.page.dashboards.includes.sales_stock_movements')
                </div>
                <div class="col-md-3">
                    <div class="dashboard-card" style="margin-top: 15px;">
                        @include('admin.page.dashboards.includes.returns_reasons')
                    </div>
                </div>
            </div>

            <div class="row flex-row" style="padding: 0 10px 0 10px">
                @include('admin.page.dashboards.includes.sales_revenue_returns')
                @include('admin.page.dashboards.includes.branch_average_perfomance')
            </div>
        </div>
    </section>

    <style type="text/css">
        .row.flex-row {
            display: flex;
            flex-wrap: wrap;
            align-items: stretch;
        }

        .dashboard-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
            padding: 10px;
            margin-right: 10px;
            margin-left: 10px;
            flex: 1;
            height: 100%;
        }

        .col-md-3>.dashboard-card {
            flex: 1;
        }

        .col-md-3 {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-content {
            text-align: center;
            font-size: 12px;
        }

        .middle-cards-content {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
            font-size: 12px;
            height: 100%;
        }


        .card-content .title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-content .value {
            font-size: 13px;
            font-weight: bold;
            text-align: left;
        }

        .card-content .change {
            font-size: 14px;
        }

        .card-content .increase {
            color: green;
        }

        .card-content .decrease {
            color: red;
        }

        .chart-container {
            margin-bottom: 15px;
        }

        .chart-separator {
            height: 15px;
        }

        .dashboard-card canvas {
            margin-top: 10px;
        }

        .show-more {
            margin-top: 20px;
            text-align: right;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .show-more a {
            color: #007bff;
            text-decoration: none;
            font-size: 12px;
        }

        .show-more a:hover {
            text-decoration: underline;
        }

        .slanted-up {
            transform: rotate(45deg);
        }

        .slanted-down {
            transform: rotate(-45deg);
        }

        .box-primary {
            padding: 15px;
            background-color: #f7f7f7;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .date-filter {
            display: flex;
            align-items: flex-start;
            justify-content: start;
            text-align: center;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .input-group label {}

        .input-group input[type="date"] {
            width: 200px;
            padding: 0px;
            margin-right: 10px;
        }

        #apply-filter-btn {
            background-color: transparent;
            border: none;
            cursor: pointer;
            margin-top: 28px;
        }

        #apply-filter-btn:focus {
            outline: none;
        }

        #apply-filter-btn i {
            font-size: 15px;
            color: #007bff;
            /* Adjust icon color as needed */
        }
    </style>
@endsection

@section('uniquepagescriptforchart')
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Date filter start

        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('start-date');
            const endDateInput = document.getElementById('end-date');

            const today = new Date();
            const startDate = new Date(today);
            const endDate = new Date(today);

            const startYear = startDate.getFullYear();
            let startMonth = startDate.getMonth() + 1;
            let startDay = startDate.getDate();

            if (startMonth < 10) {
                startMonth = '0' + startMonth;
            }
            if (startDay < 10) {
                startDay = '0' + startDay;
            }

            const startDateValue = `${startYear}-${startMonth}-${startDay}`;
            startDateInput.value = startDateValue;

            endDate.setMonth(today.getMonth() + 1);

            const endYear = endDate.getFullYear();
            let endMonth = endDate.getMonth() + 1;
            let endDay = endDate.getDate();

            if (endMonth < 10) {
                endMonth = '0' + endMonth;
            }
            if (endDay < 10) {
                endDay = '0' + endDay;
            }

            const endDateValue = `${endYear}-${endMonth}-${endDay}`;
            endDateInput.value = endDateValue;

            const maxDate = `${startYear}-${startMonth}-${startDay}`;
            endDateInput.max = maxDate;
        });

        // Date filter end
    </script>
@endsection
