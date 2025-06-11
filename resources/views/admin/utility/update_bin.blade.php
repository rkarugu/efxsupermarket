@extends('layouts.admin.admin')
<?php
$logged_user_info = getLoggeduserProfile();
$my_permissions = $logged_user_info->permissions;
$route_name = \Route::currentRouteName();
?>

<script>
    var location_data = @json($main_pending_approval_bins_data);
    var location_bin_data = @json($main_update_item_bins_data);
</script>

@section('content')
    <div class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Update Bin Location </h3>
                </div>
            </div>

            <div class="box-body">
                @if (session('error'))
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
                    <script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: '{{ session('error') }}',
                            confirmButtonText: 'OK'
                        });
                    </script>
                @endif
                <form id="update-bin-form" action="{{ route('utility.update_bin_location_excel') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-group col-md-3">
                        <label for="">Branch</label>
                        <select name="branch" id="mlselec6t" class="form-control mlselec6t">
                            <option value="" selected disabled>--Select Branch--</option>
                            @foreach (getStoreLocationDropdown() as $index => $store)
                                <option value="{{ $index }}" {{ request()->branch == $index ? 'selected' : '' }}>
                                    {{ $store }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="max_stock_reorder_level_file" class="control-label">Upload Excel File (.xlsx)</label>
                        <input type="file" class="form-control" name="file" id="max_stock_reorder_level_file"
                            accept=".xlsx">
                    </div>
                    @if ($logged_user_info->role_id == 1 || isset($my_permissions['utility___update-bin']))
                        <div class="col-md-2">
                            <div class="form-group">
                                <label style="display: block;">&nbsp;</label>
                                <button type="submit" class="btn btn-primary" name="Process Bins" id="update"
                                    value="Process Bins">
                                    <i class="fa fa-cogs"></i> Process Bins
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label style="display: block;">&nbsp;</label>
                                <button type="submit" class="btn btn-primary" name="Template" id="template"
                                    value="Template">
                                    <i class="fa fa-file-excel"></i> Template
                                </button>
                            </div>
                        </div>
                    @endif
                </form>

            </div>
        </div>

        <div class="box box-primary">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#pending_bin_approvals" data-toggle="tab">Pending Approvals</a></li>
                <li><a href="#unmatched_bins" data-toggle="tab">Rejected Bins</a></li>
                <li><a href="#existing_bins" data-toggle="tab">Existing Bins</a></li>
                <li><a href="#update_item_bins" data-toggle="tab">Update Item Bins</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="pending_bin_approvals">
                    @include('admin.utility.bin_utility.bin_approvals')
                </div>

                <div class="tab-pane" id="unmatched_bins">
                    @include('admin.utility.bin_utility.unmatched_bins')
                </div>

                <div class="tab-pane" id="existing_bins">
                    @include('admin.utility.bin_utility.existing_bins')
                </div>

                <div class="tab-pane" id="update_item_bins">
                    @include('admin.utility.bin_utility.update_item_bins')
                </div>

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
    <script src="{{ asset('/js/form.js') }}"></script>


    <script>
        var selectedLocation = null;

        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        $(function() {
            // var form = new Form();
        })

        $(document).ready(function() {
            // $("body").addClass('sidebar-collapse');
            $(".mlselec6t").select2();
            $(".storeLocation").select2();
            $('#pending_approvals_table').DataTable({
                "paging": true,
                "pageLength": 10,
                "searching": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 50, 100],
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [
                    [0, "asc"]
                ]
            });
            $('#existing_bins_table').DataTable({
                "paging": true,
                "pageLength": 30,
                "searching": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 50, 100],
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [
                    [0, "asc"]
                ]
            });
            $('#unmatched_bins_table').DataTable({
                "paging": true,
                "pageLength": 30,
                "searching": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 50, 100],
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [
                    [0, "asc"]
                ]
            });
            $('#update_item_bins_table').DataTable({
                "paging": true,
                "pageLength": 30,
                "searching": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 50, 100],
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [
                    [0, "asc"]
                ]
            });

            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                var activeTab = $(e.target).attr('href');
                localStorage.setItem('activeTab', activeTab);
            });
            var activeTab = localStorage.getItem('activeTab');
            if (activeTab) {
                $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
            }
            $('.storeLocation').on('change', function() {
                selectedLocation = this.value
            });
            $('#approveBinsForm button[type="submit"]').on('click', function(e) {
                if (selectedLocation !== null) {
                    $('#approveBinsForm input[name="location"]').val(selectedLocation);
                    $('#approveBinsForm').submit();
                }
            });

            let intentValue = '';
            let submitButton = '';

            $('input[type="submit"], button[type="submit"]').click(function() {
                intentValue = $(this).val();
                submitButton = $(this);
            });

            $('#update-bin-form').on('submit', function(e) {
                e.preventDefault();

                let originalHtmlUpdate = $('#update').html();
                let originalHtmlDownload = $('#download').html();

                submitButton.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Processing...');

                var formData = new FormData(this);
                formData.append('intent', intentValue);

                var xhr = new XMLHttpRequest();
                xhr.open('POST', $(this).attr('action'), true);

                xhr.responseType = intentValue === 'Template' ? 'blob' : 'json';

                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        if (intentValue === 'Template') {
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(xhr.response);
                            link.download = `Upload_bins_template.xlsx`;
                            link.click();
                            form.successMessage('File downloaded');
                        } else {
                            var response = xhr.response;
                            form.successMessage('Items Uploaded');
                        }
                        location.reload()
                    } else {
                        var errorMessage = null;
                        try {
                            errorMessage = xhr.response
                                .error;
                        } catch (err) {
                            errorMessage = 'There was an error processing your request.';
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                        });
                    }
                    $('#update').prop('disabled', false).html(originalHtmlUpdate);
                    $('#download').prop('disabled', false).html(originalHtmlDownload);
                };

                xhr.onerror = function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred during the request.',
                    });
                    $('#update').prop('disabled', false).html(originalHtmlUpdate);
                    $('#download').prop('disabled', false).html(originalHtmlDownload);
                };

                xhr.send(formData);
            });


            let intentUpdateValue = '';
            let processButton = '';
            let intentUpdateValueBtn = '';
            let processButtonBtn = '';

            $('.update_bins').click(function() {
                intentUpdateValue = $(this).val();
                processButton = $(this);
            });

            $('.update_bins_btn').click(function() {
                intentUpdateValueBtn = $(this).val();
                processButtonBtn = $(this);
            });


            $('#confirm_update_bins_form').on('submit', function(e) {
                e.preventDefault();

                let originalHtmlUpdate = $('.update_bins_btn').html();

                submitButton.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Processing...');

                var formData = new FormData(this);
                formData.append('intent', intentUpdateValueBtn);
                formData.append('main_pending_approval_bins_data', JSON.stringify(location_data));

                var xhr = new XMLHttpRequest();
                xhr.open('POST', $(this).attr('action'), true);

                xhr.responseType = intentUpdateValueBtn === 'json';

                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        var response = xhr.response;
                        form.successMessage('Bins Updated');
                        location.reload()
                    } else {
                        var errorMessage = null;
                        try {
                            if (typeof xhr.response === 'string') {
                                response = JSON.parse(xhr.response);
                            }
                            errorMessage = response.error;
                        } catch (err) {
                            errorMessage = 'There was an error processing your request.';
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                        });
                    }
                    $('.update_bins_btn').prop('disabled', false).html(originalHtmlUpdate);
                };

                xhr.onerror = function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred during the request.',
                    });
                    $('.update_bins_btn').prop('disabled', false).html(originalHtmlUpdate);
                };

                xhr.send(formData);
            });

            $('#confirm_update_item_bins_form').on('submit', function(e) {
                e.preventDefault();

                let originalHtmlProcess = $('.update_bins').html();

                submitButton.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Processing...');

                var formData = new FormData(this);
                formData.append('intent', intentUpdateValue);
                formData.append('main_update_item_bins_data', JSON.stringify(location_bin_data));

                var xhr = new XMLHttpRequest();
                xhr.open('POST', $(this).attr('action'), true);

                xhr.responseType = intentUpdateValue === 'json';

                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        var response = xhr.response;
                        form.successMessage('Item bin updated');
                        location.reload()
                    } else {
                        console.log(xhr.response);
                        var errorMessage = null;
                        try {
                            if (typeof xhr.response === 'string') {
                                response = JSON.parse(xhr.response);
                            }
                            errorMessage = response.error;
                        } catch (err) {
                            errorMessage = 'There was an error processing your request.';
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                        });
                    }
                    $('.update_bins').prop('disabled', false).html(originalHtmlProcess);
                };

                xhr.onerror = function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred during the request.',
                    });
                    $('.update_bins').prop('disabled', false).html(originalHtmlProcess);
                };

                xhr.send(formData);
            });
        });
    </script>
@endpush
