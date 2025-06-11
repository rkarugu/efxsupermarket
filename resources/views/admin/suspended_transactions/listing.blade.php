@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Suspended Transactions </h3>
                    <a href="{{ route('suspended-transactions.create') }}" class="btn btn-primary">Suspend Transactions</a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="create_datatable">
                        <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Suspension Date</th>
                            <th>Suspended By</th>
                            <th>Document No</th>
                            <th>Route</th>
                            <th>Trans Date</th>
                            <th>Input Date</th>
                            <th>Reference</th>
                            <th>Reason</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($suspendedTransactions as $trans)
                            <tr>
                                <th style="width: 3%;" scope="row">{{ $loop->index + 1 }}</th>
                                <td>{{ $trans->created_at }}</td>
                                <td>{{ $trans->user }}</td>
                                <td>{{ $trans->document_no }}</td>
                                <td>{{ $trans->route }}</td>
                                <td>{{ $trans->trans_date }}</td>
                                <td>{{ $trans->input_date }}</td>
                                <td>{{ $trans->reference }}</td>
                                <td>{{ $trans->reason }}</td>
                                <td>{{ manageAmountFormat($trans->amount) }}</td>
                                <td>
                                    <div class="action-button-div">
                                        <a href="#" data-doc-no="{{ $trans->document_no }}" title="Edit & Restore" class="restore" data-reference="{{ $trans->reference }}"
                                           data-amount="{{ $trans->amount }}" data-route="{{ $trans->wa_customer_id }}">
                                            <i class="fas fa-window-restore text-success"></i>
                                        </a>

                                        <a href="#" data-doc-no="{{ $trans->document_no }}" title="Expunge" class="expunge">
                                            <i class="fas fa-trash text-danger"></i>
                                        </a>
                                    </div>
                                </td>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="9" scope="row" style="text-align: center;"> TOTAL</th>
                            <th colspan="2" scope="row">{{ manageAmountFormat($suspendedTransactions->sum('amount')) }}</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="expunge-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Expunge Transaction</h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        Are you sure you want to completely expunge this transaction?
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <form action="" id="expunge-form" method="post">
                                {{ @csrf_field() }}
                                <input class="btn btn-primary" value="Yes, expunge" type="submit">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="restore-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Restore Transaction</h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        <p> This transaction will be removed from suspense list and restored into the appropriate customer statement. </p>

                        <form action="" id="restore-form" method="post">
                            {{ @csrf_field() }}

                            <div class="form-group">
                                <label for="edited_wa_customer_id" class="control-label"> Route </label>
                                <select name="edited_wa_customer_id" id="edited_wa_customer_id" class="form-control select2">
                                    @foreach($routes as $route)
                                        <option value="{{ $route->id }}">{{ $route->customer_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="reference" class="control-label"> Reference </label>
                                <input type="text" class="form-control" name="edited_reference" id="edited_reference">
                            </div>

                            <div class="form-group">
                                <label for="reference" class="control-label"> Amount </label>
                                <input type="text" class="form-control" name="edited_amount" id="edited_amount">
                            </div>

                            <div class="box-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <input class="btn btn-primary" value="Confirm Restore" type="submit">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('body').addClass('sidebar-collapse');
            $('.select2').select2({
                dropdownParent: $('#restore-modal')
            });

            $('.expunge').on('click', function (event) {
                event.preventDefault();
                let document_no = $(this).data('doc-no');
                $('#expunge-form').attr('action', `/admin/reconciliation/suspended-transactions/${document_no}/expunge`);
                $('#expunge-modal').modal('show');
            });

            $('.restore').on('click', function (event) {
                event.preventDefault();
                let document_no = $(this).data('doc-no');
                let reference = $(this).data('reference');
                let amount = $(this).data('amount');
                let route = $(this).data('route');

                $("#edited_amount").val(amount);
                $("#edited_reference").val(reference);
                $("#edited_wa_customer_id").val(route);

                $('#restore-form').attr('action', `/admin/reconciliation/suspended-transactions/${document_no}/restore`);
                $('#restore-modal').modal('show');
            });
        });
    </script>
@endsection