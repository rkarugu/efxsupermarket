@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Supplier Edit for User: <span> {{ $suppliers->first()->name }}</span></h3>
                    <button data-toggle="modal" data-target="#confirm-create-distributor-modal" data-backdrop="static"
                        class="btn btn-primary"> Add Supplier</button>
                </div>
            </div>

            <div class="box-body">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-bordered" id="create_datatable_100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Suppliers</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($suppliers as $userSuppliers)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $userSuppliers->suppname }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger" data-toggle="modal"
                                            data-target="#confirm-delete-modal"
                                            data-supplier-id="{{ $userSuppliers->suppid }}"
                                            data-supplier-name="{{ $userSuppliers->suppname }}"
                                            data-user-id="{{ $currentuserId }}"
                                            data-user-name="{{ $suppliers->first()->name }}">
                                            <i class="fa fa-remove"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="confirm-create-distributor-modal" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Add Supplier </h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-footer">
                        <div class="">
                            <form action="{{ route('utility.supplier_user_management_update') }}" method="post">
                                {{ @csrf_field() }}
                                <div class="form-group">
                                    <label class="control-label">Suppliers</label>
                                    <select name="wa_supplier_id" id="supplierDropdown" class="form-control mlselect">

                                        @foreach ($allsuppliers as $allsupplier)
                                            <option value="{{ $allsupplier->id }}">{{ $allsupplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <input type="hidden" id="userId" value="{{ $currentuserId }}" name="user_id">
                                <button type="submit" class="btn btn-primary" style="margin-top: 7px">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="confirm-delete-modal" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Confirm Delete </h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <p id="delete-message"></p>
                    </div>
                    <div class="box-footer">
                        <div class="flex pull-right">
                            <button type="button" class="btn btn-danger" id="confirm-delete-btn">Detach</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">

    <style>
        .box-footer .flex {
            display: flex;
        }

        .box-footer .flex .btn {
            margin-left: 10px;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('/js/form.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {

            $(".mlselect").select2();
        });
    </script>
    <script type="text/javascript">
        function approveShop() {
            let subjectShopId = $("#subject-shop").val();
            $(`#source-${subjectShopId}`).val('approval_requests');

            $(`#approve-shop-form-${subjectShopId}`).submit();
        }

        function approveAllShops() {
            $("#approve-all-shops-form").submit();
        }

        $('#view-issue-modal').on('show.bs.modal', function(event) {
            let triggeringButton = $(event.relatedTarget);
            let dataValue = triggeringButton.data('issue');

            let date = new Date();
            date.setTime(date.getTime() + (2 * 60 * 1000));
            let expires = "; expires=" + date.toGMTString();

            document.cookie = 'issue' + "=" + dataValue + expires + "; path=/";
        })

        var response = ["Administrator", "Anna"];
        $("#supplierDropdown").val(response).change();


        // delete logic start

        $(document).ready(function() {
            $('#confirm-delete-modal').on('show.bs.modal', function(event) {
                let button = $(event.relatedTarget);
                let supplierId = button.data('supplier-id');
                let supplierName = button.data('supplier-name');
                let userId = button.data('user-id');
                let userName = button.data('user-name');

                $('#confirm-delete-btn').data('supplier-id', supplierId);
                $('#confirm-delete-btn').data('user-id', userId);

                $('#delete-message').text(
                    `Are you sure you want to detach ${supplierName} from ${userName}?`);
            });

            $('#confirm-delete-btn').on('click', function() {
                let supplierId = $(this).data('supplier-id');
                let userId = $(this).data('user-id');

                $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

                $.ajax({
                    url: '{{ route('utility.supplier_user_management_delete', ['userId' => ':userId', 'supplierId' => ':supplierId']) }}'
                        .replace(':userId', userId)
                        .replace(':supplierId', supplierId),
                    type: 'DELETE',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "_method": "DELETE",
                        "currentuserId": userId
                    },
                    success: function(response) {
                        if (response.success) {
                            form.successMessage('Supplier detached from user successfully.')
                            window.location.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Something went wrong.',
                            }).then(() => {
                                $('#confirm-delete-btn').prop('disabled', false).html(
                                    'Delete');
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Something went wrong.',
                        })
                        $('#confirm-delete-btn').prop('disabled', false).html(
                            'Delete');
                    }
                });
            });

        });

        // delete logic end
    </script>
@endsection
