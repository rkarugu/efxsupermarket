@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Detailed Transaction Summary Report</h3>
                </div>
            </div>
            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                <form action="" method="get">
                    <div class="row">
                        <div class="form-group col-sm-3">
                            <label for="start_date" class="control-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" value="{{ request()->start_date }}"
                                class="form-control" required>
                        </div>

                        <div class="form-group col-sm-3">
                            <label for="end_date" class="control-label">End Date</label>
                            <input type="date" name="end_date" id="end_date" value="{{ request()->end_date }}"
                                class="form-control" required>
                        </div>

                        <div class="form-group col-sm-3">
                            <label class="control-label" style="display: block;">&nbsp;</label>
                            <input type="submit" name="intent" value="Filter" class="btn btn-primary">
                            <input type="submit" name="intent" value="Excel" class="btn btn-primary ml-12">
                        </div>
                    </div>
                </form>
                <hr>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="transactionsDataTable">
                        <thead>
                            <tr>
                                <th style="width: 3%;">#</th>
                                <th>Transaction Date</th>
                                <th>Posting Date</th>
                                <th>Transaction No</th>
                                <th>Transaction Type</th>
                                <th>Account Code</th>
                                <th>Account Name</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="7" class="text-right">Total</th>
                                <th id="grand_total" class="text-right"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            let table = $("#transactionsDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('gl-reports.detailed-transaction-summary') !!}',
                    data: function(data) {
                        data.start_date = $("#start_date").val();
                        data.end_date = $("#end_date").val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        sortable: false,
                        width: "70px"
                    },
                    {
                        data: "trans_date",
                        name: "trans_date"
                    },
                    {
                        data: "created_at",
                        name: "created_at"
                    },
                    {
                        data: "transaction_no",
                        name: "transaction_no"
                    },
                    {
                        data: "transaction_type",
                        name: "transaction_type"
                    },
                    {
                        data: "account_code",
                        name: "account_code"
                    },
                    {
                        data: "account_name",
                        name: "account_name"
                    },
                    {
                        data: "amount",
                        name: "amount",
                        className: 'text-right',
                    }
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#grand_total").html(json.grand_total);
                }
            });
        });
    </script>
@endpush
