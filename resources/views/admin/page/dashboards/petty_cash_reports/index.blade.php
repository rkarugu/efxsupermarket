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
            <div class="row">

                <div class="col-lg-3 col-xs-12">
                    <div class="small-box bg-blue">
                        <div class="inner">
                            <h4>Travel/Delivery (Current Month)</h4>
                            <p>Kes {{ number_format($current_month_pettycash_amount, 2) }}</p>
                        </div>
                        <div class="icon" style="font-size: 50px;margin-top:10px">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <a href="javascript:void(0)" class="small-box-footer">Show More
                            <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-xs-12">
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h4>Travel/Delivery (Last Month)</h4>
                            <p>Kes {{ number_format($last_month_pettycash_amount, 2) }}</p>
                        </div>
                        <div class="icon" style="font-size: 50px;margin-top:10px">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <a href="javascript:void(0)" class="small-box-footer">Show More
                            <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-xs-12">
                    <div class="small-box bg-red">
                        <div class="inner">
                            <h4>Travel/Delivery (Previous Month)</h4>
                            <p>Kes {{ number_format($previous_month_pettycash_amount, 2) }}</p>
                        </div>
                        <div class="icon" style="font-size: 50px;margin-top:10px">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <a href="javascript:void(0)" class="small-box-footer">Show More
                            <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-xs-12">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h4>Travel/Delivery (Year to Date)</h4>
                            <p>Kes {{ number_format($year_to_date_pettycash_amount, 2) }}</p>
                        </div>
                        <div class="icon" style="font-size: 50px;margin-top:10px">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <a href="javascript:void(0)" class="small-box-footer">Show More
                            <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>

            </div>


        </div>

        <div class="row flex-row" style="padding: 0 10px 0 10px">
            @include('admin.page.dashboards.petty_cash_reports.petty_cash_chart')
        </div>

        <div class="row flex-row" style="padding: 0 10px 0 10px">
            @include('admin.page.dashboards.petty_cash_reports.petty_cash_table')
        </div>

        </div>
    </section>

    <style type="text/css">
        .row.flex-row {
            display: flex;
            flex-wrap: wrap;
            align-items: stretch;
        }

        .dashboard-card-col {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-top: -10px;
            margin-bottom: -10px;
            padding: 10px;
            height: 100%;
        }

        .dashboard-card-col .card-content {
            flex: 1;
        }

        .show-more {
            margin-top: auto;
            display: flex;
            align-items: center;
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

        .dashboard-card-col {
            height: auto;
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
        }
    </style>
@endsection

@section('uniquepagescriptforchart')
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

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
