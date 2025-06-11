@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Payment Approval Info </h3>
            </div>
            <div class="box-header with-border">
                <div class="row">
                    <div class="col-sm-3">
                        <label for="branch">Branch</label>
                        <select name="branch" id="branch" class="form-control">
                            <option value="">Select Branch</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @selected($branch->id == auth()->user()->restaurant_id)>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped" id="approvalsDataTable">
                    <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Branch</th>
                            <th>Approval Total</th>
                            <th>Pending Approval</th>
                            <th>Variance</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>
    <div class="modal fade" id="confirmApproveModal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title" id="approveModalTitle"> Approve Transactions</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form id="updateApproveForm" method="POST">
                    @csrf
                    <div class="box-body">
                        Are you sure You want to Approve these Transactions?
                    </div>
                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancel</button>
                            <button type="submit" id="confirmApproveBtn" class="btn btn-primary">
                                Approve</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="confirmApproveAllModal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title" id="approveModalTitle"> Approve Transactions</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form id="updateApproveForm" action="" method="POST">
                    @csrf
                    <div class="box-body">
                        Are you sure You want to Approve these Transactions? This will post to the General Ledger.
                    </div>
                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <input type="hidden" name="" id="">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cancel</button>
                            <button type="submit" id="confirmApproveAllBtn" class="btn btn-primary">
                                Approve All</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <span class="btn-loader" style="display:none;">
        <img src="<?= asset('/assets/admin/images/loader.gif') ?>" alt="Loader" />
    </span>
@endsection

@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script>
        $('body').addClass('sidebar-collapse');
        var form = new Form();

        $(document).ready(function() {
            $("select.form-control").select2();

            $('#branch').on('change', function() {
                refreshTable();
            });

            var table = $("#approvalsDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, "asc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('payment-reconciliation.approval') !!}',
                    data: function(data) {
                        data.branch = $("#branch").val()
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                    },
                    {
                        data: 'start_date',
                        name: 'start_date'
                    }, {
                        data: 'end_date',
                        name: 'end_date'
                    },
                    {
                        data: 'branch_name',
                        name: 'restaurants.name'
                    },
                    {
                        data: 'total_payments',
                        name: 'total_payments'
                    },
                    {
                        data: 'pending_approval_count',
                        name: 'pending_approval_count'
                    },
                    {
                        data: 'variance',
                        name: 'variance'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: "text-center"
                    }

                ]
            });

            table.on('draw', function() {
                $('[data-toggle="tooltip"]').tooltip();
            });

            $('#confirmApproveBtn').on('click', function(e) {
                e.preventDefault();
                $('.btn-loader').show();
                var id = $(this).data('id');

                var checkboxValues = [];
                $('#childtable-' + id).DataTable().rows().nodes().to$().find('.matchCheck-' + id).each(
                    function() {
                        if ($(this).is(":checked")) {
                            console.log($(this).val());
                            checkboxValues.push($(this).val());
                        }
                    });

                var reconJson = [];
                var newData = [];

                var postData = {
                    reconJson: checkboxValues,
                    verification: id
                };

                $.ajax({
                    url: "{{ route('payment-reconciliation.approval.store') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: postData,
                    success: function(response) {
                        $('.btn-loader').hide();
                        form.successMessage('Payments set for processing successfully');
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    },
                    error: function(xhr, status, error) {
                        $('.btn-loader').hide();
                        form.errorMessage('Something went wrong!!');
                        console.error(xhr.responseText);
                    }
                });

            });

            $('#confirmApproveAllBtn').on('click', function(e) {
                e.preventDefault();
                $('#confirmApproveAllModal').modal('hide');
                $('.btn-loader').show();
                var id = $(this).data('id');
                var postData = {
                    verification: id,
                    approveAll: 1,
                };

                $.ajax({
                    url: "{{ route('payment-reconciliation.approval.store') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: postData,
                    success: function(response) {
                        $('.btn-loader').hide();
                        form.successMessage('Payments set for processing successfully');
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    },
                    error: function(xhr, status, error) {
                        $('.btn-loader').hide();
                        form.errorMessage('Something went wrong!!');
                        console.error(xhr.responseText);
                    }
                });
            });
        });

        function refreshTable() {
            $("#approvalsDataTable").DataTable().ajax.reload();
        }

        function approveBtn(id) {
            var checkboxValues = [];
            $('#childtable-' + id).DataTable().rows().nodes().to$().find('.matchCheck-' + id).each(function() {
                if ($(this).is(":checked")) {
                    checkboxValues.push($(this).val());
                }
            });

            if (!checkboxValues.length) {
                $('#approveCheckError-' + id).html("Select Transactions to Approve");
                return;
            }
            $('#confirmApproveBtn').data('id', id);
            $('#confirmApproveModal').modal();
        }

        function approveAll(id) {
            $('#confirmApproveAllBtn').data('id', id);
            $('#confirmApproveAllModal').modal();
        }
    </script>
@endpush
