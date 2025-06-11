@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> {!! $title !!}</h3>

                    <form id="download-users-suppliers-form"
                        action="{{ route('utility.download_users_suppliers_documents') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <div class="btn-group">
                                <label style="display: block;">&nbsp;</label>
                                <button type="submit" class="btn btn-primary" name="intent" id="download-pdf"
                                    value="Download Pdf">
                                    <i class="fa fa-file-pdf"></i> Download PDF
                                </button>
                                <button type="submit" class="btn btn-primary" name="intent" id="download-excel"
                                    value="Download Excel">
                                    <i class="fa fa-file-excel"></i> Download Excel
                                </button>
                            </div>

                        </div>
                    </form>

                </div>
            </div>

            <div class="box-body">
                <div class="table-responsive">
                    @php
                        $totalSupplierCount = 0;
                    @endphp
                    <table class="table table-bordered" id="user_supplier_table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>No of Suppliers</th>
                                <th style="display: none">Suppliers</th>
                                <th>Actions</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($suppliers as $userName => $userSuppliers)
                                @php
                                    $user = \App\Model\User::where('name', $userName)->first();
                                    $userId = $user ? $user->id : null;
                                    $totalSupplierCount += $userSuppliers->count();
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $userName }}</td>
                                    <td>{{ $userSuppliers->count() }}</td>
                                    <td style="display: none">
                                        @foreach ($userSuppliers as $supplier)
                                            {{ $supplier->suppname }}@if (!$loop->last)
                                                ,
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        <button class="btn btn-sm no-bg"><a
                                                href="{{ route('utility.supplier_user_management_edit', ['id' => $userId]) }}"><i
                                                    class="fa fa-edit"></i></a></button>
                                        <button class="btn btn-sm download-btn no-bg" data-user-id="{{ $userId }}"
                                            data-intent="Download Pdf">
                                            <i class="fa fa-file-pdf"></i>
                                        </button>
                                        <button class="btn btn-sm download-btn no-bg" data-user-id="{{ $userId }}"
                                            data-intent="Download Excel">
                                            <i class="fa fa-file-excel"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="2"><strong>Total</strong></td>
                                <td colspan="2"><strong>{{ $totalSupplierCount }} <strong></td>
                            </tr>
                        </tfoot>

                    </table>

                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">

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
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('/js/form.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js">
        < script src = "https://code.jquery.com/jquery-3.6.0.min.js" >
    </script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();

            var table = $('#user_supplier_table').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });

            function toggleSuppliers(index) {
                var icon = document.getElementById('icon' + index);
                var element = document.getElementById('suppliers' + index);
                if (element.style.display === 'none') {
                    element.style.display = 'block';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    element.style.display = 'none';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }

        });

        $(document).ready(function() {
            $('.download-btn').on('click', function(e) {
                e.preventDefault();

                var submitButton = $(this);
                var originalButtonText = submitButton.html();
                var userId = submitButton.data('user-id');
                var intent = submitButton.data('intent');

                var form = $('#download-users-suppliers-form');
                form.find('input[name="user_id"]').remove();
                form.find('input[name="intent"]').remove();

                $('<input>').attr({
                    type: 'hidden',
                    name: 'user_id',
                    value: userId
                }).appendTo(form);

                $('<input>').attr({
                    type: 'hidden',
                    name: 'intent',
                    value: intent
                }).appendTo(form);

                submitButton.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin"></i>');

                var formData = new FormData(form[0]);

                var xhr = new XMLHttpRequest();
                xhr.open('POST', form.attr('action'), true);

                xhr.responseType = (intent === 'Download Pdf' || intent === 'Download Excel') ? 'blob' :
                    'json';

                xhr.onload = function() {
                    submitButton.prop('disabled', false).html(
                        originalButtonText);
                    if (xhr.status >= 200 && xhr.status < 300) {
                        if (intent === 'Download Pdf' || intent === 'Download Excel') {
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(xhr.response);
                            link.download = intent === 'Download Pdf' ? 'Users-Suppliers-PDF.pdf' :
                                'Users-Suppliers-EXCEL.xlsx';
                            link.click();
                            form.successMessage('File downloaded successfully.')

                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Something went wrong.',
                            });
                        }
                    } else {
                        handleError();
                    }
                };

                xhr.onerror = function() {
                    submitButton.prop('disabled', false).html(
                        originalButtonText);
                    handleError();
                };

                function handleError() {
                    var errorMessage = 'Something went wrong.';
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response && response.error) {
                            errorMessage = response.error;
                        }
                    } catch (err) {}
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage,
                    });
                }

                xhr.send(formData);
                setTimeout(() => {
                    location.reload();
                }, 2000);
            });


            $('#download-users-suppliers-form').on('submit', function(e) {
                e.preventDefault();

                var submitButton = $(this).find('button[type="submit"]:focus');
                var intentValue = submitButton.val();

                submitButton.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin"></i> Processing...');

                var formData = new FormData(this);
                formData.append('intent', intentValue);

                var xhr = new XMLHttpRequest();
                xhr.open('POST', $(this).attr('action'), true);

                xhr.responseType = (intentValue === 'Download Pdf' || intentValue === 'Download Excel') ?
                    'blob' : 'json';

                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        if (intentValue === 'Download Pdf' || intentValue === 'Download Excel') {
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(xhr.response);
                            link.download = intentValue === 'Download Pdf' ? 'Users-Suppliers-PDF.pdf' :
                                'Users-Suppliers-EXCEL.xlsx';
                            link.click();
                            form.successMessage('File downloaded successfully.')
                        } else {
                            form.successMessage('File downloaded successfully.')
                        }
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        var errorMessage = 'Something went wrong.';
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response && response.error) {
                                errorMessage = response.error;
                            }
                        } catch (err) {}
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                        });
                    }
                    submitButton.prop('disabled', false).html(submitButton.data('original-text'));
                };

                xhr.onerror = function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Something went wrong.',
                    });
                    submitButton.prop('disabled', false).html(submitButton.data('original-text'));
                };

                xhr.send(formData);
            });

            $('#download-users-suppliers-form button[type="submit"]').each(function() {
                $(this).data('original-text', $(this).html());
            });
        });
    </script>
@endsection
