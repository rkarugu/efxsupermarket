@extends('layouts.admin.admin')

@section('content')
    <div class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Upload New Items </h3>
                </div>
            </div>

            <div class="box-body">

                <form id="update-item-form" action="{{ route('process_upload_new_items') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-group col-md-3">
                        <label for="max_stock_reorder_level_file" class="control-label">Upload Excel File (.xlsx)</label>
                        <input type="file" class="form-control" name="file" id="max_stock_reorder_level_file"
                            accept=".xlsx">
                    </div>

                    <div class="col-md-9">
                        <div class="form-group">
                            <label style="display: block;">&nbsp;</label>
                            <button type="submit" class="btn btn-primary" name="Process" id="process" value="Process">
                                <i class="fa fa-cogs"></i> Process
                            </button>
                            <button type="submit" class="btn btn-primary" name="Template" id="template" value="Template">
                                <i class="fa fa-file-excel"></i> Template
                            </button>
                        </div>
                    </div>

                </form>

            </div>
        </div>

        <div class="box" id="tableContainer" style="display: none;">
            <div class="box-body">
                <table class="table table-bordered" id="averageSalesDataTable">
                    <thead>
                        <tr style="font-size: 12px">
                            <th>ITEM ID</th>
                            <th>STOCK ID CODE</th>
                            <th>DESCRIPTION</th>
                            <th>CURRENT MAX STOCK</th>
                            <th>CURRENT RE-ORDER LEVEL</th>
                            <th>SUGGESTED MAX STOCK</th>
                            <th>SUGGESTED REORDER LEVEL</th>
                        </tr>
                    </thead>
                </table>
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

            var form = new Form();

            let intentValue = '';
            let submitButton = null;

            $('input[type="submit"], button[type="submit"]').click(function() {
                intentValue = $(this).val();
                submitButton = $(this);
            });

            $('#update-item-form').on('submit', function(e) {
                e.preventDefault();

                let originalHtmlProcess = $('#process').html();
                let originalHtmlTemplate = $('#template').html();

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
                            link.download = `Upload_new_items_template.xlsx`;
                            link.click();
                            form.successMessage('File downloaded');
                        } else {
                            var response = xhr.response;
                            form.successMessage('Items Uploaded');
                        }
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
                    $('#process').prop('disabled', false).html(originalHtmlProcess);
                    $('#template').prop('disabled', false).html(originalHtmlTemplate);
                };

                xhr.onerror = function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Something went wrong.',
                    });
                    $('#process').prop('disabled', false).html(originalHtmlProcess);
                    $('#template').prop('disabled', false).html(originalHtmlTemplate);
                };

                xhr.send(formData);
            });

        });
    </script>
@endpush
