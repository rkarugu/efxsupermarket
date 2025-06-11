@extends('layouts.admin.admin')

@section('content')
    <div class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Verify Stocks </h3>
                </div>
            </div>

            <div class="box-body">

                <form id="update-bin-form" action="{{ route('process_verify_stocks') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="form-group col-md-3">
                        <label for="">Store Location</label>
                        <select name="location" id="mlselec6t" class="form-control mlselec6t">
                            <option value="" selected disabled>--Select Store Location--</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}">
                                    {{ $location->location_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="verify_stocks_upload_file" class="control-label">Upload Excel File ( .xlsx / .xls
                            )</label>
                        <input type="file" class="form-control" name="file" id="verify_stocks_upload_file"
                            accept=".xlsx">
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label style="display: block;">&nbsp;</label>
                            <button type="submit" class="btn btn-primary" name="Process" id="process" value="Process"
                                disabled>
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
        $(document).ready(function() {

            $(".mlselec6t").select2();

            function updateButtonState() {
                var selectedLocation = $("#mlselec6t").val();
                var fileSelected = $("#verify_stocks_upload_file")[0].files.length > 0;

                if (selectedLocation && fileSelected) {
                    $('#process').prop('disabled', false);
                } else {
                    $('#process').prop('disabled', true);
                }
            }

            $('#mlselec6t, #verify_stocks_upload_file').on('change', updateButtonState);

            let intentValue = '';
            let submitButton = null;

            $('input[type="submit"], button[type="submit"]').click(function() {
                intentValue = $(this).attr('name');
                submitButton = $(this);
                originalHtml = $(this).html();
            });

            $('#update-bin-form').on('submit', function(e) {
                e.preventDefault();

                let originalHtmlProcess = $('#process').html();
                let originalHtmlTemplate = $('#template').html();

                submitButton.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Processing...');

                var formData = new FormData(this);
                formData.append('intent', intentValue);

                var xhr = new XMLHttpRequest();
                xhr.open('POST', $(this).attr('action'), true);

                xhr.responseType = 'blob';

                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        var contentType = xhr.getResponseHeader('content-type');

                        if (contentType === 'application/json') {
                            var reader = new FileReader();
                            reader.onload = function() {
                                var response = JSON.parse(reader.result);
                                if (response.error) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: response.error,
                                    });
                                } else {
                                    form.successMessage('File downloaded');
                                }
                            };
                            reader.readAsText(xhr.response);
                        } else {
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(xhr.response);
                            link.download = intentValue === 'Template' ?
                                'Combined-Stocks-Template.xlsx' :
                                'Combined-Stocks.xlsx';
                            link.click();
                            form.successMessage('File downloaded');
                        }
                    } else {
                        var reader = new FileReader();
                        reader.onload = function() {
                            var response = JSON.parse(reader.result);
                            var errorMessage = response.error ||
                                'There was an error processing your request.';
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: errorMessage,
                            });
                        };
                        reader.readAsText(xhr.response);
                    }
                    $('#process').prop('disabled', false).html(originalHtmlProcess);
                    $('#template').prop('disabled', false).html(originalHtmlTemplate);
                    $('#verify_stocks_upload_file').val('');
                };

                xhr.onerror = function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred during the request.',
                    });
                    $('#process').prop('disabled', false).html(originalHtmlProcess);
                    $('#template').prop('disabled', false).html(originalHtmlTemplate);
                    $('#verify_stocks_upload_file').val('');
                };

                xhr.send(formData);
            });

            function resetForm() {
                submitButton.prop('disabled', false).val(intentValue);
                $('#bin_upload_file').val('');
                submitButton = null;
            }

        });

        function initializeDataTable(data) {
            var table = $("#averageSalesDataTable").DataTable({
                autoWidth: false,
                pageLength: 10,
                destroy: true,
                data: data,
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'stock_id_code',
                        name: 'stock_id_code'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'max_stock',
                        name: 'max_stock',
                        defaultContent: '0'
                    },
                    {
                        data: 're_order_level',
                        name: 're_order_level',
                        defaultContent: '0'
                    },
                    {
                        data: 'suggested_max_stock',
                        name: 'suggested_max_stock',
                        defaultContent: '0'
                    },
                    {
                        data: 'suggested_reorder_level',
                        name: 'suggested_reorder_level',
                        defaultContent: '0'
                    }
                ]
            });
        }
    </script>
@endpush
