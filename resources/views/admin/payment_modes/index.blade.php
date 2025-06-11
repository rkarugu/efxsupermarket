@extends('layouts.admin.admin')

@section('content')
    <div class="content">
        <div class="box box-primary">
            <div class="box-header">
                @include('message')
                <div class="d-flex justify-content-between">
                    <h4 class="flex-grow-1">Payment Modes</h4>
                    <div class="text-right">
                        <button data-toggle="modal" data-target="#addPaymentModeModal" class="btn btn-primary">
                            <i class="fa fa-plus"></i>
                            Add Payment Mode
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-body">
                <table class="table table-striped" id="paymentModesDataTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Mode</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div id="addPaymentModeModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Payment Model</h4>
                </div>
                <form action="{{ route('payment-modes.store') }}" method="POST" class="validate-form">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="mode">Name</label>
                            <input type="text" class="form-control" id="mode" name="mode"
                                data-rule-maxLength="255" data-rule-required="true">
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <input type="text" class="form-control" id="description" name="description"
                                data-rule-maxLength="255">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="button" data-dismiss="modal">
                            <i class="fa fa-close"></i>
                            close
                        </button>
                        <button class="btn btn-primary" type="submit">
                            <i class="fa fa-save"></i>
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="editPaymentModeModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Payment Model</h4>
                </div>
                <form id="editPaymentModeForm" method="POST" class="validate-form">
                    @csrf
                    @method('put')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="mode">Name</label>
                            <input type="text" class="form-control" id="editMode" name="mode"
                                data-rule-maxLength="255" data-rule-required="true">
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <input type="text" class="form-control" id="editDescription" name="description"
                                data-rule-maxLength="255">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="button" data-dismiss="modal">
                            <i class="fa fa-close"></i>
                            close
                        </button>
                        <button class="btn btn-primary" type="submit">
                            <i class="fa fa-save"></i>
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            var table = $("#paymentModesDataTable").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('payment-modes.index') !!}',
                    data: function(data) {

                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        sortable: false,
                        width: "70px"
                    },
                    {
                        data: "mode",
                        name: "mode"
                    },
                    {
                        data: "description",
                        name: "description"
                    },
                    {
                        data: "actions",
                        name: "actions",
                        width: "80px",
                    }
                ],
            });

            $("#editPaymentModeModal").on('show.bs.modal', function(e) {
                let link = $(e.relatedTarget);
                let details = link.data('details');

                $("#editPaymentModeForm").prop('action', link.data('url'));
                $("#editMode").val(details.mode);
                $("#editDescription").val(details.description);
            })

            $('.table tbody').on('click', '[data-toggle="delete"]', function() {
                let target = $(this).data('target');

                Swal.fire({
                    title: 'Confirm',
                    text: 'Are you sure want to delete payment mode?',
                    showCancelButton: true,
                    confirmButtonColor: '#252525',
                    cancelButtonColor: 'red',
                    confirmButtonText: 'Yes',
                    cancelButtonText: `No, Cancel It`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(target).submit();
                    }
                })
            });
        })
    </script>
@endpush
