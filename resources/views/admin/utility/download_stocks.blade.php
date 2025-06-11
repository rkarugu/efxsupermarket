@extends('layouts.admin.admin')

@section('content')
    <div class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Download Stocks </h3>
                </div>
            </div>

            <div class="box-body">
                <form id="download-stocks-form" action="{{ route('process_download_stocks') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="form-group col-md-3">
                        <label for="supplier">Suppliers</label>
                        <select name="supplier" id="supplier" class="form-control mlselec6t">
                            <option value="" selected disabled>--Select Supplier--</option>
                            @foreach ($suppliers as $index => $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ request()->supplier == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="start_date" class="control-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date" id="start_date"
                            max="{{ date('Y-m-d') }}">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="end_date" class="control-label">End Date</label>
                        <input type="date" class="form-control" name="end_date" id="end_date" max="{{ date('Y-m-d') }}">
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label style="display: block;">&nbsp;</label>
                            <button type="submit" class="btn btn-primary" name="intent" id="download" value="Download">
                                <i class="fa fa-download"></i> Download
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
        $(document).ready(function() {

            $('#items_missing_suppliers_table').DataTable({
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

            $(".mlselec6t").select2();

            function updateButtonState() {
                var supplierSelected = $('#supplier').val();
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();
                var button = $("button[name=intent]");

                if (supplierSelected && (!startDate && !endDate)) {
                    button.prop('disabled', false);
                } else if (startDate && endDate) {
                    button.prop('disabled', false);
                } else {
                    button.prop('disabled', true);
                }
            }

            updateButtonState();

            $('#supplier, #start_date, #end_date').on('change', function() {
                updateButtonState();
            });

            let intentValue = '';
            let submitButton = null;

            $('button[type="submit"]').click(function() {
                intentValue = $(this).val();
                submitButton = $(this);
            });

            $('#download-stocks-form').on('submit', function(e) {
                e.preventDefault();

                const processBtnState = $('#process').html();
                const templateBtnState = $('#download').html();

                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();

                submitButton.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin"></i> Processing...');

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
                                    setTimeout(() => {
                                        location.reload()
                                    }, 3000);
                                }
                            };
                            reader.readAsText(xhr.response);
                        } else {
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(xhr.response);
                            link.download = intentValue === 'Download' ?
                                `Stocks-Download-${startDate}-to-${endDate}.xlsx` :
                                `Stocks-${startDate}-to-${endDate}.xlsx`;
                            link.click();
                            form.successMessage('File downloaded');
                            setTimeout(() => {
                                location.reload()
                            }, 3000);
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Something went wrong.',
                        });
                    }
                    $('#process').prop('disabled', false).html(processBtnState);
                    $('#download').prop('disabled', false).html(templateBtnState);
                    $('#verify_stocks_upload_file').val('');
                };

                xhr.onerror = function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred during the request.',
                    });
                    $('#process').prop('disabled', false).html(processBtnState);
                    $('#download').prop('disabled', false).html(templateBtnState);
                    $('#verify_stocks_upload_file').val('');
                };

                xhr.send(formData);
            });

            function resetForm() {
                submitButton.prop('disabled', false).html(intentValue);
                $('#bin_upload_file').val('');
                submitButton = null;
            }

        });
    </script>
@endpush
