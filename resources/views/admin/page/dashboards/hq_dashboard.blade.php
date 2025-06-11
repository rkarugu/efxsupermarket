@extends('layouts.admin.admin')

<?php
$logged_user_info = getLoggeduserProfile();
$my_permissions = $logged_user_info->permissions;
$route_name = \Route::currentRouteName();

?>

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="row">
                <div class="col-md-6">
                    <a href="{!! route('trial-balances.detailed') !!}"></a>
                </div>
            </div>
            <div class="box-header with-border no-padding-h-b">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title report-main-title">HQ Dashboards Reports</h3>
                    <div>
                        <a href="{{route('admin.chairman-dashboard')}}" class="btn btn-success btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
                    </div>
                   
                </div>
                @include('message')
            </div>

            <div class="box-body">
                <div class="row" id="new-category">
                        <div class="col-md-6">
                            <ul class="list-group" style="cursor: pointer" id="route-data">
                                <ul class="sortable-list" style="margin-top: 4px;">
                                 
                                        @if ($logged_user_info->role_id == 1 || isset($my_permissions[$report_permission]))
                                        <li class="">

                                                <div class="small-box bg-green">
                                                    <div class="inner">
                                                        <h4>Order Taking & POS Summary Report</h4>
                                                    </div>
                                                    <div class="icon" style="font-size: 50px;margin-top:10px">
                                                        <i class="fas fa-money-bill-wave"></i>
                                                    </div>
                                                    <a  href="{{route('hq-dashboard.order-taking-summary')}}" target="_blank" class="small-box-footer">Go To <i class="fa fa-arrow-circle-right"></i></a>
                                                </div>

                                    
                                            </li>
                                            {{-- <li class="">

                                                <div class="small-box bg-blue">
                                                    <div class="inner">
                                                        <h4>Petty Cash</h4>
                                                    </div>
                                                    <div class="icon" style="font-size: 50px;margin-top:10px">
                                                        <i class="fas fa-money-bill-wave"></i>
                                                    </div>
                                                    <a  href="#" target="_blank" class="small-box-footer">Go To <i class="fa fa-arrow-circle-right"></i></a>
                                                </div>

                                    
                                            </li> --}}
                                        @endif
                                </ul>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            {{-- <ul class="list-group" style="cursor: pointer" id="route-data">
                                <ul class="sortable-list" style="margin-top: 4px;">
                                 
                                        @if ($logged_user_info->role_id == 1 || isset($my_permissions[$report_permission]))
                                        <li class="">

                                                <div class="small-box bg-red">
                                                    <div class="inner">
                                                        <h4>Expenses</h4>
                                                    </div>
                                                    <div class="icon" style="font-size: 50px;margin-top:10px">
                                                        <i class="fas fa-money-bill-wave"></i>
                                                    </div>
                                                    <a  href="#" target="_blank" class="small-box-footer">Go To <i class="fa fa-arrow-circle-right"></i></a>
                                                </div>

                                            </li>
                                            <li class="">

                                                <div class="small-box bg-yellow">
                                                    <div class="inner">
                                                        <h4>Expenses</h4>
                                                    </div>
                                                    <div class="icon" style="font-size: 50px;margin-top:10px">
                                                        <i class="fas fa-money-bill-wave"></i>
                                                    </div>
                                                    <a  href="#" target="_blank" class="small-box-footer">Go To <i class="fa fa-arrow-circle-right"></i></a>
                                                </div>

                                            </li>
                                        @endif
                                </ul>
                            </ul> --}}
                        </div>
                </div>
            </div>
        </div>

    </section>
@endsection

@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />
    <style>
        .reportRange {
            display: flex;
            align-content: center;
            justify-content: stretch;
            border: 1px solid #eee;
            cursor: pointer;
            height: 35px;
        }

        .report-title {
            color: black;
            font-weight: normal;
        }

        .report-main-title {
            font-weight: bolder;
            font-size: 14px;
            color: black;
        }

        .report-main-title-black {
            color: black;
            font-weight: bolder
        }

        .category-input {
            display: none;
        }

        .category-title {
            opacity: 1;
        }

        .category-title+.category-input:visible {
            opacity: 0;
        }

        .list-group .sortable-list {
            padding-left: 0;
            margin-left: 0;
            width: 100%;
            list-style: none;
        }

        .list-group .sortable-list .list-group-item {
            padding-left: 15px;
            margin-bottom: 5px;
        }

        .list-group .sortable-list>li {
            margin-bottom: 5px;
        }

        .list-group .sortable-list {
            border: none;
            background-color: transparent;
        }

        .has-permission {
            color: #1B75D0;
            cursor: pointer;
        }

        .has-permission:hover {
            text-decoration: underline;
        }

        .no-permission {
            color: gray;
            cursor: not-allowed;
        }
    </style>
@endpush
@push('scripts')
    <script type="text/javascript" src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}">
    </script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>

    <script>
        function showDataResponse(response) {
            location.reload()
        }

        function showReportDataResponse() {
            location.reload();
        }

        function clearFormFields() {
            $('#addReportForm input[type="text"]').val('');
            $('#categoryID').val('');
        }

        function openModal(id, categoryTitle) {
            clearFormFields();
            $('#categoryID').val(id);
            $('#categoryTitle').val(categoryTitle);
            $('#categoryTitleData').val(categoryTitle)
            $('#addReportModal').modal('show');
        }

    </script>
@endpush
