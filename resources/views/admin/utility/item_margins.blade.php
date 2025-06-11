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
                    <h3 class="box-title">Download Item Margins</h3>
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
                <form id="update-bin-form" action="{{ route('utility.download_item_margins') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="form-group col-md-3" id="file-input-group">
                        <label for="update_item_prices_file" class="control-label">Update Excel File (.xlsx)</label>
                        <input type="file" class="form-control" name="file" id="update_item_prices_file"
                            accept=".xlsx">
                    </div>

                    <div class="col-md-9">
                        <div class="form-group">
                            <label style="display: block;">&nbsp;</label>
                            @if ($logged_user_info->role_id == 1 || isset($my_permissions['utility___update-item-margins']))
                                {{-- <input type="submit" class="btn btn-primary" name="intent" id="update" value="Update"
                                    disabled> --}}
                                <button type="submit" class="btn btn-primary" name="Update" id="update" value="Update"
                                    disabled>
                                    <i class="fa-solid fa-pen-to-square"></i> Update
                                </button>
                            @endif
                            @if ($logged_user_info->role_id == 1 || isset($my_permissions['utility___download-item-margins']))
                                {{-- <input type="submit" class="btn btn-primary" name="intent" id="download"
                                    value="Download"> --}}
                                <button type="submit" class="btn btn-primary" name="Download" id="download"
                                    value="Download">
                                    <i class="fa fa-download"></i> Download
                                </button>
                            @endif
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
        $(document).ready(function() {
            let fileUploaded = false;

            function updateButtonStates() {
                if (fileUploaded) {
                    $('#update').prop('disabled', false);
                    $('#download').prop('disabled', true);
                } else {
                    $('#update').prop('disabled', true);
                    $('#download').prop('disabled', false);
                }
            }

            $('#update_item_prices_file').on('change', function() {
                fileUploaded = $(this).val() !== "";
                updateButtonStates();
            });

            let intentValue = '';
            let submitButton = '';

            $('input[type="submit"], button[type="submit"]').click(function() {
                intentValue = $(this).val();
                submitButton = $(this);
            });

            $('#update-bin-form').on('submit', function(e) {
                e.preventDefault();

                let originalHtmlDownload = $('#download').html();
                let originalHtmlUpdate = $('#update').html();

                submitButton.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Processing...');

                var formData = new FormData(this);
                formData.append('intent', intentValue);

                var xhr = new XMLHttpRequest();
                xhr.open('POST', $(this).attr('action'), true);

                xhr.responseType = intentValue === 'Download' ? 'blob' : 'json';

                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        if (intentValue === 'Download') {
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(xhr.response);
                            link.download = `Item_details_template.xlsx`;
                            link.click();
                            form.successMessage('File downloaded');
                        } else {
                            var response = xhr.response;
                            form.successMessage('Item Prices Saved');
                        }
                        setTimeout(() => {
                            location.reload()
                        }, 3000);
                    } else {
                        var errorMessage = null;
                        try {
                            errorMessage = xhr.response.error;
                        } catch (err) {
                            errorMessage = 'There was an error processing your request.';
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                        });
                    }
                    $('#download').prop('disabled', false).html(originalHtmlDownload);
                    $('#update').prop('disabled', true).html(originalHtmlUpdate);
                    $('#update_item_prices_file').val('');
                };

                xhr.onerror = function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred during the request.',
                    });
                    $('#download').prop('disabled', false).html(originalHtmlDownload);
                    $('#update').prop('disabled', true).html(originalHtmlUpdate);
                    $('#update_item_prices_file').val('');
                };

                xhr.send(formData);
            });
        });
    </script>
@endpush
