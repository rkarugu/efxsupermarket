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
                    <h3 class="box-title"> Update Batch Items Stock Code </h3>
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
                <form id="update-stock-code" action="{{ route('utility.process_update_item_code') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="form-group col-md-3" id="file-input-group">
                        <label for="update_item_prices_file" class="control-label">Update Excel File (.xlsx)</label>
                        <input type="file" class="form-control" name="file" id="update_item_prices_file"
                            accept=".xlsx">
                    </div>

                    <div class="col-md-6" id="update-button-group">
                        <div class="form-group">
                            <label style="display: block;">&nbsp;</label>
                            @if ($logged_user_info->role_id == 1 || isset($my_permissions['update-item-code___update']))
                                <button type="submit" class="btn btn-primary" name="Update" id="update">
                                    <i class="fa-solid fa-pen-to-square"></i> Update
                                </button>
                            @endif
                            <button type="submit" class="btn btn-primary" name="Template" id="template">
                                <i class="fas fa-file-excel"></i> Template
                            </button>
                            <button type="button" class="btn btn-primary" name="clear" id="clear">
                                <i class="fas fa-times"></i> Clear
                            </button>
                        </div>
                    </div>

                </form>

            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Update Single Item Stock Code </h3>
                </div>
            </div>


            <div class="box-body">
                <form id="update-single-stock-code" action="{{ route('utility.single_update_item_code') }}" method="post">
                    @csrf

                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="current_item_code" class="control-label">Current Item Code</label>
                            <input type="text" class="form-control" name="current_item_code" id="current_item_code">
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="display: block;">&nbsp;</label>
                                <button type="button" class="btn btn-primary" name="Process" intent="process"
                                    id="process">
                                    <i class="fas fa-cogs"></i> Process
                                </button>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row" id="item-details" style="display: none;">
                        <div class="form-group col-md-3">
                            <label for="item_id" class="control-label">Current Item ID</label>
                            <input type="text" class="form-control" name="item_id" id="item_id" disabled>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="item_description" class="control-label">Item Description</label>
                            <input type="text" class="form-control" name="item_description" id="item_description"
                                disabled>
                        </div>
                    </div>

                    <div class="row" id="other-item-details" style="display: none;">
                        <div class="form-group col-md-3">
                            <label for="item_qoh" class="control-label">QOH</label>
                            <input type="text" class="form-control" name="item_qoh" id="item_qoh" disabled>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="new_stock_code" class="control-label">New Stock Code</label>
                            <input type="text" class="form-control" name="new_stock_code" id="new_stock_code">
                        </div>
                    </div>

                    <div class="row" id="save-button-group" style="display: none;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label style="display: block;">&nbsp;</label>
                                <button type="submit" class="btn btn-primary" name="Save" intent="save"
                                    id="save">
                                    <i class="fas fa-save"></i> Save
                                </button>
                            </div>
                        </div>
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

        $(document).ready(function() {
            $(".mlselec6t").select2();
            $(".storeLocation").select2();

            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                var activeTab = $(e.target).attr('href');
                localStorage.setItem('activeTab', activeTab);
            });
            var activeTab = localStorage.getItem('activeTab');
            if (activeTab) {
                $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
            }
            $('.storeLocation').on('change', function() {
                selectedLocation = this.value;
            });

            let intentValue = '';
            let submitButton = '';
            let originalHtml = '';

            $('input[type="submit"], button[type="submit"]').click(function() {
                intentValue = $(this).attr('name');
                submitButton = $(this);
                originalHtml = $(this).html();
            });

            $('#update_item_prices_file').on('change', function() {
                if ($(this).val()) {
                    $('#update').prop('disabled', false);
                    $('#download').prop('disabled', true);
                } else {
                    $('#update').prop('disabled', true);
                    $('#download').prop('disabled', false);
                }
            });

            $('#clear').click(function() {
                location.reload()
            });

            $('#update-stock-code').on('submit', function(e) {
                e.preventDefault();

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
                            link.download = `ITEM-BATCH-STOCK-ID-UPDATE.xlsx`;
                            link.click();
                            form.successMessage('File downloaded');

                            $('#file-input-group').show();
                            $('#update-button-group').show();
                            $('#download').hide();
                        } else {
                            var response = xhr.response;
                            form.successMessage('Item Code Updated');
                        }
                        setTimeout(() => {
                            location.reload();
                        }, 3000);
                    } else {
                        var errorMessage = null;
                        try {
                            errorMessage = xhr.response.error;
                        } catch (err) {
                            errorMessage = 'Something went wrong.';
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                        });
                    }
                    submitButton.prop('disabled', false).html(originalHtml);
                    $('#update_item_prices_file').val('');
                };

                xhr.onerror = function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Something went wrong.',
                    });
                    submitButton.prop('disabled', false).html(originalHtml);
                    $('#update_item_prices_file').val('');
                };

                xhr.send(formData);
            });

            let intentUpdateValue = '';
            let processButton = '';

            $('.update-stock-code').click(function() {
                intentUpdateValue = $(this).val();
                processButton = $(this);
            });

            $('#update').prop('disabled', true);
            $('#download').prop('disabled', false);
            if ($('#update_item_prices_file').val()) {
                $('#update').prop('disabled', false);
                $('#download').prop('disabled', true);
            }

            $('#process').click(function() {
                var currentItemCode = $('#current_item_code').val();
                intent = $(this).attr('name');
                submitButton = $(this);
                originalHtml = $(this).html();

                if (currentItemCode.trim() === '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Please enter the current item code.'
                    });
                    return;
                }

                $('#process').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Processing...');

                $.ajax({
                    url: "{{ route('utility.single_update_item_code') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        current_item_code: currentItemCode,
                        intent: intent
                    },
                    success: function(response) {
                        
                        $('#item_id').val(response?.item?.id);
                        $('#item_description').val(response?.item?.description);
                        $('#item_qoh').val(response?.item_qoh);
                        $('#new_stock_code').val('');

                        $('#item-details').show();
                        $('#other-item-details').show();
                        $('#save-button-group').show();

                        $('#process').prop('disabled', false).html(originalHtml);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseJSON.message);
                        var errorMessage = xhr.responseJSON && xhr.responseJSON.message ?
                            xhr.responseJSON.message :
                            'Something went wrong.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage
                        });

                        $('#process').prop('disabled', false).html(originalHtml);
                    }
                });
            });

            $('#update-single-stock-code').submit(function(e) {
                e.preventDefault();

                intent = $('#save').attr('name');
                submitButton = $(this);
                originalHtml = $(this).html();

                var newStockCode = $('#new_stock_code').val();
                var currentItemCode = $('#current_item_code').val();

                $('#save').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Processing...');

                if (newStockCode.trim() === '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Please enter the new stock code.'
                    });
                    return;
                }

                $.ajax({
                    url: "{{ route('utility.single_update_item_code') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        current_item_code: currentItemCode,
                        new_stock_code: newStockCode,
                        intent: intent
                    },
                    success: function(response) {
                        form.successMessage(response.message)
                        $('#save').prop('disabled', false).html(originalHtml);
                        $('#current_item_code').val('')
                        $('#item_id').val('');
                        $('#item_description').val('');
                        $('#item_qoh').val('');
                        $('#new_stock_code').val('');

                        $('#item-details').hide();
                        $('#other-item-details').hide();
                        $('#save-button-group').hide();
                        setTimeout(() => {
                            location.reload()
                        }, 3000);
                    },
                    error: function(xhr) {
                        var errorMessage = xhr.responseJSON ? xhr.responseJSON.error :
                            'Something went wrong.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage
                        });

                        $('#save').prop('disabled', false).html(originalHtml);
                    }
                });
            });

        });
    </script>
@endpush
