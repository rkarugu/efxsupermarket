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
                    <h3 class="box-title"> {{$title}} </h3>
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
                <form id="update-bin-form" action="{{ route('utility.update_stock_qoh') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="form-group col-md-3" id="file-input-group">
                        <label for="location">Location</label>
                        <select name="location" id="location" class="form-control mlselec6t">
                            <option value="" selected disabled>--Select Location--</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->location_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-3" id="file-input-group">
                        <label for="update_item_prices_file" class="control-label">Update Excel File (.xlsx)</label>
                        <input type="file" class="form-control" name="file" id="update_item_prices_file"
                            accept=".xlsx">
                    </div>

                    <div class="col-md-6" id="update-button-group">
                        <div class="form-group">
                            <label style="display: block;">&nbsp;</label>
                            @if ($logged_user_info->role_id == 1 || isset($my_permissions['update-stock-qoh___update']))
                                <button type="submit" class="btn btn-primary" name="Update" id="update" value="Update">
                                    <i class="fa-solid fa-pen-to-square"></i> Update
                                </button>
                            @endif
                            @if ($logged_user_info->role_id == 1 || isset($my_permissions['update-stock-qoh___download']))
                                <button type="submit" class="btn btn-primary" name="Download Stocks" id="download"
                                    value="Download Stocks">
                                    <i class="fa fa-download"></i> Download Stocks
                                </button>
                            @endif
                            <button type="submit" class="btn btn-primary" name="Clear" id="clear" value="Clear">
                                <i class="fa fa-times"></i> Clear
                            </button>
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

            $('input[type="submit"], button[type="submit"]').click(function() {
                intentValue = $(this).val();
                submitButton = $(this);
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

            $('#update-bin-form').on('submit', function(e) {
                e.preventDefault();

                let originalHtmlUpdate = $('#update').html();
                let originalHtmlDownload = $('#download').html();

                submitButton.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Processing...');

                var formData = new FormData(this);
                formData.append('intent', intentValue);

                var costType = $('#location').val();

                var xhr = new XMLHttpRequest();
                xhr.open('POST', $(this).attr('action'), true);

                xhr.responseType = intentValue === 'Download Stocks' ? 'blob' : 'json';

                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        if (intentValue === 'Download Stocks') {
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(xhr.response);
                            link.download = `ITEM-QOH-EXCEL.xlsx`;
                            link.click();
                            form.successMessage('File downloaded');

                            $('#file-input-group').show();
                            $('#update-button-group').show();
                            $('#download').hide();
                        } else {
                            var response = xhr.response;
                            form.successMessage('Item Cost Saved');
                        }
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                        $('#update').prop('disabled', false).html(originalHtmlUpdate);
                        $('#download').prop('disabled', false).html(originalHtmlDownload);
                        $('#update_item_prices_file').val('');
                    } else {
                        var errorMessage = null;
                        if (xhr.status == 422) {
                            errorMessage = 'Cost Type is Required.';
                        } else {
                            errorMessage = 'Something went wrong.';
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                        });

                        $('#update').prop('disabled', true).html(originalHtmlUpdate);
                        $('#download').prop('disabled', false).html(originalHtmlDownload);
                        $('#update_item_prices_file').val('');
                    }

                };

                xhr.onerror = function(xhr) {
                    console.log(xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Something went wrong.',
                    });
                    $('#update').prop('disabled', false).html(originalHtmlUpdate);
                    $('#download').prop('disabled', false).html(originalHtmlDownload);
                    $('#update_item_prices_file').val('');
                };

                xhr.send(formData);
            });

            let intentUpdateValue = '';
            let processButton = '';

            $('.update_bins').click(function() {
                intentUpdateValue = $(this).val();
                processButton = $(this);
            });

            $('#update').prop('disabled', true);
            $('#download').prop('disabled', false);
            if ($('#update_item_prices_file').val()) {
                $('#update').prop('disabled', false);
                $('#download').prop('disabled', true);
            }
        });
    </script>
@endpush
