@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> {!! $title !!}</h3>

                    <form id="download-items-form" action="{{ route('utility.download_items_without_suppliers') }}"
                        method="post">
                        @csrf
                        <div class="form-group">
                            <div class="btn-group">
                                <label style="display: block;">&nbsp;</label>
                                <button type="submit" class="btn btn-primary" id="download-pdf" name="action"
                                    value="pdf">
                                    <i class="fa fa-file-pdf"></i> Download PDF
                                </button>
                                <button type="submit" class="btn btn-primary" id="download-excel" name="action"
                                    value="excel">
                                    <i class="fa fa-file-excel"></i> Download Excel
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="items_missing_suppliers_table">
                        <thead>
                            <tr>
                                <th style="width: 3%;">#</th>
                                <th>STOCK ID CODE</th>
                                <th>DESCRIPTION</th>
                                <th>CATEGORY</th>
                                <th>PACK SIZE</th>
                                <th>STANDARD COST</th>
                                <th>SELLING PRICE</th>
                                <th>% MARGIN</th>
                                <th>QTY</th>
                                <th>TAX CATEGORY</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $index => $record)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $record->stock_id_code }}</td>
                                    <td>{{ $record->description }}</td>
                                    <td>{{ $record->category }}</td>
                                    <td>{{ $record->pack_size }}</td>
                                    <td>{{ $record->standard_cost }}</td>
                                    <td>{{ $record->selling_price }}</td>
                                    <td>
                                        {{ number_format($record->standard_cost != 0 ? 
                                        ((($record->selling_price - $record->standard_cost) / $record->standard_cost) * 
                                        100) : 0, 2) }}
                                    </td>
                                    <td>{{ $record->item_total_quantity ?? 0 }}</td>
                                    <td>{{ $record->tax_manager }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .btn-group {
            display: flex;
            gap: 10px;
        }

        .no-bg {
            background: none;
            border: none;
            padding: 0;
            box-shadow: none;
            font-size: 20px;
            color: #337ab7;
        }

        .no-bg i {
            color: inherit;
        }

        .no-bg:focus {
            outline: none;
            box-shadow: none;
        }

        .no-bg:hover {
            background-color: transparent;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#items_missing_suppliers_table').DataTable({
                "paging": true,
                "pageLength": 10,
                "searching": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 50, 100],
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [
                    [0, "asc"]
                ]
            });

            $('#download-items-form').on('submit', function(e) {
                e.preventDefault();

                var form = $(this);
                var submitButton = $(this).find('button[type="submit"]:focus');
                var originalButtonText = submitButton.html();
                var intentValue = submitButton.val();

                var originalDownloadHtml = $('#download-pdf').html()
                var originalExcelHtml = $('#download-excel').html()

                $('#download-pdf').prop('disabled', true)
                $('#download-excel').prop('disabled', true)

                submitButton.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin"></i> Processing...');

                var formData = new FormData(form[0]);
                formData.append('intent', intentValue);

                var xhr = new XMLHttpRequest();
                xhr.open('POST', $(this).attr('action'), true);

                xhr.responseType = intentValue === 'pdf' ? 'blob' : 'blob';

                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        $('#download-pdf').prop('disabled', false).html(originalDownloadHtml)
                        $('#download-excel').prop('disabled', false).html(originalExcelHtml)
                        if (intentValue === 'pdf') {
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(xhr.response);
                            link.download = `ITEMS-WITHOUT-SUPPLIER.pdf`;
                            link.click();
                        } else if (intentValue === 'excel') {
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(xhr.response);
                            link.download = `ITEMS-WITHOUT-SUPPLIER.xlsx`;
                            link.click();
                        }
                        form.successMessage('File downloaded');
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
                    submitButton.prop('disabled', false).val(intentValue);
                    $('#download-pdf').prop('disabled', false).html(originalDownloadHtml)
                    $('#download-excel').prop('disabled', false).html(originalExcelHtml)
                };

                xhr.onerror = function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Something went wrong.',
                    });
                    submitButton.prop('disabled', false).val(intentValue);
                    $('#download-pdf').prop('disabled', false).html(originalDownloadHtml)
                    $('#download-excel').prop('disabled', false).html(originalExcelHtml)
                };

                xhr.send(formData);

            });
        });
    </script>
@endsection
