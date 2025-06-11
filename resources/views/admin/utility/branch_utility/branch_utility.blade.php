@extends('layouts.admin.admin')

<?php
$logged_user_info = getLoggeduserProfile();
$my_permissions = $logged_user_info->permissions;
$route_name = \Route::currentRouteName();
?>

@section('content')
    <div class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Download Branch Utilities </h3>
                </div>
            </div>

            <div class="box-body">
                <form action="{{ route('utility.download_branch_utilities') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-row align-items-center" style="display: flex; justify-content: start; gap: 20px;">
                        <div class="col-auto" style="width: 200px;">
                            <h4 class="my-2">Inventory Category</h4>
                        </div>
                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['utility___download-inventory-category']))
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary" name="category" id="download"
                                    value="Download">
                                    <i class="fa fa-download"></i> Download
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="form-row align-items-center" style="display: flex; justify-content: start; gap: 20px;">
                        <div class="col-auto" style="width: 200px;">
                            <h4 class="my-2">Inventory Sub Category</h4>
                        </div>
                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['utility___download-inventory-sub-category']))
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary" name="subcategory" id="download"
                                    value="Download">
                                    <i class="fa fa-download"></i> Download
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="form-row align-items-center" style="display: flex; justify-content: start; gap: 20px;">
                        <div class="col-auto" style="width: 200px;">
                            <h4 class="my-2">Suppliers</h4>
                        </div>
                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['utility___download-suppliers']))
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary" name="suppliers" id="download"
                                    value="Download">
                                    <i class="fa fa-download"></i> Download
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="form-row align-items-center" style="display: flex; justify-content: start; gap: 20px;">
                        <div class="col-auto" style="width: 200px;">
                            <h4 class="my-2">Tax Category</h4>
                        </div>
                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['utility___download-tax-category']))
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary" name="taxcategory" id="download"
                                    value="Download">
                                    <i class="fa fa-download"></i> Download
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="form-row align-items-center" style="display: flex; justify-content: start; gap: 20px;">
                        <div class="col-auto" style="width: 200px;">
                            <h4 class="my-2">Pack Size</h4>
                        </div>
                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['utility___download-pack-size']))
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary" name="packsize" id="download"
                                    value="Download">
                                    <i class="fa fa-download"></i> Download
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="form-row align-items-center" style="display: flex; justify-content: start; gap: 20px;">
                        <div class="col-auto" style="width: 200px;">
                            <h4 class="my-2">Bin Locations</h4>
                        </div>
                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['utility___download-bin-locations']))
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary" name="binlocations" id="download"
                                    value="Download">
                                    <i class="fa fa-download"></i> Download
                                </button>
                            </div>
                        @endif
                    </div>
                </form>
            </div>

        </div>

    </div>
@endsection

@push('styles')
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

        .select2 {
            width: 100% !important;
        }
    </style>
@endpush

@push('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>

    <script></script>
@endpush
