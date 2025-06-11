@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Payment Verification </h3>
                    <div>
                        <a href="{{ route('payment-reconciliation.verification.create') }}" class="btn btn-primary">Start
                            Verification</a>
                    </div>
                </div>
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
                    <div class="col-sm-3">
                        <label for="branch">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">All</option>
                            <option value="pending" selected>Pending</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="verificationDataTable">
                        <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Branch</th>
                            <th>Channel</th>
                            <th>Total Payments</th>
                            <th>Pending Approval</th>
                            <th>Approved</th>
                            <th>Total Match</th>
                            <th>Total Missing</th>
                            <th class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <td colspan="5"></td>
                            <td><b id="footer_total_payments"></b></td>
                            <td><b id="footer_pending_approval_count"></b></td>
                            <td></td>
                            <td><b id="footer_total_match"></b></td>
                            <td><b id="footer_total_missing_system"></b></td>
                            <td></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="discardVerificationModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title" id="approveModalTitle"> Discard Verification Range</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form id="updateApproveForm" action="" method="POST">
                    @csrf
                    <div class="box-body">
                        Are you sure You want to Discard this Payment Verification?
                    </div>
                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <input type="hidden" name="" id="">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" id="discardVerificationBtn" class="btn btn-primary" data-id="0"
                                    data-dismiss="modal">Discard
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <span class="btn-loader" style="display:none;">
        <img src="<?= asset('/assets/admin/images/loader.gif') ?>" alt="Loader"/>
    </span>
@endsection

@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $('body').addClass('sidebar-collapse');

        $(document).ready(function () {
            $("select.form-control").select2();

            $('#branch, #status').on('change', function () {
                refreshTable();
            });

            $("#verificationDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('payment-reconciliation.verification') !!}',
                    data: function (data) {
                        data.branch = $("#branch").val()
                        data.status = $("#status").val()
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                }, {
                    data: 'start_date',
                    name: 'start_date'
                }, {
                    data: 'end_date',
                    name: 'end_date'
                },
                    {
                        data: 'branch',
                        name: 'branch'
                    },
                    {
                        data: 'channel',
                        name: 'channel'
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
                        data: 'approved_count',
                        name: 'approved_count'
                    },
                    {
                        data: 'total_match',
                        name: 'total_match'
                    },
                    {
                        data: 'total_missing_system',
                        name: 'total_missing_system'
                    },
                    {
                        data: 'actions',
                        data: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();
                    $("#footer_total_payments").text(json.footer_total_payments);
                    $("#footer_pending_approval_count").text(json.footer_pending_approval_count);
                    $("#footer_total_match").text(json.footer_total_match);
                    $("#footer_total_missing_system").text(json.footer_total_missing_system);
                }
            });

            $('#discardVerificationBtn').on('click', function (e) {
                e.preventDefault();
                var id = $(this).data('id');

                location.href = "/admin/payment-reconciliation-verification-discard-date-range/" + id;
            });

            $('.reverify').on('click', function (e) {
                e.preventDefault();
                $('.btn-loader').show();

                jQuery.ajax({
                    url: '{{ route('payment-reconciliation.verify-all') }}',
                    type: 'GET',
                    data: null,
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        location.reload();
                    }
                });
            });
        });

        function refreshTable() {
            $("#verificationDataTable").DataTable().ajax.reload();
        }

        function discardBtn(id) {
            $('#discardVerificationModal').modal();
            $('#discardVerificationBtn').data('id', id);
        }
    </script>
@endpush
