@extends('layouts.admin.admin')

@section('content')
        <?php
        $logged_user_info = getLoggeduserProfile();
        $my_permissions = $logged_user_info->permissions;
        ?>
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Pos Cash Payments</h3>
                    <div>
                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['pos_cash_payments___initiate']))
                            <button class="btn btn-success btn-sm initiate" data-toggle="modal" data-target="#initiatePaymentModal">Initiate</button>
                        @endif
                    </div>

                </div>
            </div>

            <div class="box-body">
                {!! Form::open(['route' => 'pos-cash-payments.index', 'method' => 'get']) !!}
                <div class="row">
                    @if($permission =='superadmin')
                        <div class="col-md-2 form-group">
                            <select name="branch" id="branch" class="form-control mlselect">
                                <option value="">--Select Branch--</option>
                                @foreach ($branches as $branch)
                                    <option value="{{$branch->id }}" {{request()->branch == $branch->id ? "selected" : ""}}>{{ $branch->name }}</option>
                                @endforeach
                            </select>

                        </div>
                    @endif

                    <div class="col-md-2 form-group">
                        <input type="date" name="start_date" id="from" class="form-control" value="{{ request()->get('start_date') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-2 form-group">
                        <input type="date" name="end_date" id="to" class="form-control" value="{{ request()->get('end_date') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-3 form-group">
                    
                        <button type="submit" class="btn btn-success btn-sm" name="manage-request" value="filter">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                       
                        <a class="btn btn-success btn-sm" href="{!! route('pos-cash-payments.index') !!}">Clear </a>

                    </div>
                </div>

                {!! Form::close(); !!}

                <hr>

                @include('message')


                <div class="col-md-12">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Ref</th>
                            <th>Initiated At</th>
                            <th>Initiated By</th>
                            <th>Payee</th>
                            <th>Narrative</th>
                            <th>Status</th>
                            <th>Approved/Rejected By</th>
                            <th style="text-align: right">Amount</th>
                            <th>Action</th>
                         
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $key => $payment)
                            <tr>
                                <th>{{ $key + 1 }}</th>
                                <td>{{$payment->document_no}}</td>
                                <td>{{ $payment->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>{{ $payment->initiator->name }}</td>
                                <td>{{ $payment->recipient->name }}</td>
                                <td>{{ $payment->payment_reason }}</td>
                                <td>
                                    @if($payment->status == 'Pending')
                                        <span class="label label-default">Pending</span>
                                    @elseif($payment->status == 'Approved')
                                        <span class="label label-info">Approved</span>
                                    @elseif($payment->status == 'Disbursed')
                                        <span class="label label-success">Disbursed</span>
                                    @elseif($payment->status == 'Rejected')
                                        <span class="label label-danger">Rejected</span>
                                    @else
                                        <span >-</span>
                                    @endif
                                </td>
                                <td>{{ $payment->approvedBy->name?? 'N/A' }}</td>
                                <td style="text-align: right">{{manageAmountFormat($payment->amount)}}</td>
                                <td>
                                    @if (($logged_user_info->role_id == 1 || isset($my_permissions['pos_cash_payments___approve'])) && $payment->status == 'Pending')
                                            <a href="#" class="approve-payment" data-id="{{ $payment->id }}" title="Approve" style="font-size:20px; margin-right:3px;">
                                                <i class="fa fa-check-circle text-success"></i>
                                            </a>
                                            <a href="#" class="reject-payment" data-id="{{ $payment->id }}" title="Reject" style="font-size:20px; margin-left:3px;">
                                                <i class="fa fa-times-circle text-danger"></i>
                                            </a>
                                    @endif
                                    @if (($logged_user_info->role_id == 1 || isset($my_permissions['pos_cash_payments___initiate']))  && $payment->status == 'Approved' &&  $payment->initiated_by == $logged_user_info->id)
                                            <a href="#" class="disburse-payment" data-id="{{ $payment->id }}" title="Disburse Payment" style="font-size:20px; margin-right:3px;">
                                                <i class="fa fa-money-bill"></i>
                                            </a>
                                        
                                    @endif
                                   
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                  
                    </table>
                </div>
            </div>
        </div>
        {{-- initiate modal --}}
        <div class="modal fade" id="initiatePaymentModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Initiate  Cash Payment</h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="">Select Payee</label>
                            <select class="form-control receipts-dropdown select2" name="user_id" id="user_id" required></select>
                        </div>
                        <div class="form-group">
                            <label for="">Amount Requested</label>
                            <input type="number" class="form-control" name="amount" id="amount" required>
                        </div>
                        <div class="form-group">
                            <label for="">Comment/Payment Reason</label>
                            <textarea class="form-control" name="reason" id="reason" rows="3" required></textarea>
                        </div>
                    </div>
                 
                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                            <a id="confirmBtn" href="#" class="btn btn-success btn-sm"> <i class="fas fa-paper-plane"></i> Initiate</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
{{-- confirm initiation --}}
        <div class="modal fade" id="confirmPaymentModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Confirm Payment</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="box-body">
                        <p>Are you sure you want to initiate a payment of <span id="confirmAmount"></span> to <span id="confirmUser"></span>?</p>
                    </div>
                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                            <button type="button" id="finalizePaymentBtn" class="btn btn-success btn-sm">Confirm</button>
                        </div>
                       
                    </div>
                </div>
            </div>
        </div>
        {{-- approve --}}
        <div class="modal fade" id="approvePaymentModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Confirm Payment Approval</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="box-body">
                        <p>Are you sure you want to approve this payment?</p>
                        <div class="form-group">
                            <label for="">Select Expense Account</label>
                            <select class="form-control gl-accounts-dropdown select2" name="gl_account_id" id="gl_account_id" required></select>
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                            <button type="button" id="confirmApproveBtn" class="btn btn-success btn-sm"> <i class="fas fa-thumbs-up"></i> Approve</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- reject --}}
        <div class="modal fade" id="rejectPaymentModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Confirm Payment Rejection</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="box-body">
                        <p>Please provide a reason for rejecting this payment:</p>
                        <textarea class="form-control" id="rejectionReason" rows="3" required></textarea>
                    </div>
                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                            <button type="button" id="confirmRejectBtn" class="btn btn-success btn-sm"> <i class="fas fa-thumbs-down"></i> Reject</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Disburse --}}
        <div class="modal fade" id="disbursePaymentModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Confirm Payment Disbursement</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="box-body">
                        <p>Are you sure you want to disburse this payment?</p>
                        <p>On disbursing, please print the receipt and sign for reference.</p>
                    </div>
                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                            <button type="button" id="confirmDisburseBtn" class="btn btn-success btn-sm"> <i class="fas fa-thumbs-up"></i> Disburse</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
  
    <script type="text/javascript">
        $(function () {
            // $('body').addClass('sidebar-collapse');
            $(".mlselect").select2();

        });
    </script>

    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>
    <script type="text/javascript">
        $(document).on('click', '.initiate', function () {
            $.ajax({
                url: '{{ route("pos-cash-payments.get-all-users") }}', 
                method: 'GET',
                success: function (data) {
                    console.log(data);
                    const dropdown = $('#initiatePaymentModal .receipts-dropdown');
                    dropdown.empty();
                    data.users.forEach(user => {
                        dropdown.append(new Option(user.name, user.id));
                    });
                    dropdown.select2({
                        dropdownParent: $('#initiatePaymentModal')
                    });
                },
                error: function (error) {
                    alert('Error fetching receipts: ' + error.responseText);
                }
            });
        });
        //populate GL accounts
        $(document).on('click', '.approve-payment', function () {
            $.ajax({
                url: '{{ route("pos-cash-payments.get-charts-of-accounts-pcp") }}', 
                method: 'GET',
                success: function (data) {
                    console.log(data);
                    const dropdown = $('#approvePaymentModal .gl-accounts-dropdown');
                    dropdown.empty();
                    data.gl_accounts.forEach(account => {
                        dropdown.append(new Option(account.account_name, account.id));
                    });
                    dropdown.select2({
                        dropdownParent: $('#approvePaymentModal')
                    });
                },
                error: function (error) {
                    alert('Error fetching gl accounts: ' + error.responseText);
                }
            });
        });
        $('#confirmBtn').on('click', function () {
            const user = $('#user_id option:selected').text();
            const userId = $('#user_id').val();
            const amount = $('#amount').val();
            const reason = $('#reason').val();

            // Validate form fields
            if (!userId || !amount || !reason) {
                alert("Please fill all the required fields.");
                return;
            }

            // Set values in confirmation modal
            $('#confirmUser').text(user);
            $('#confirmAmount').text(amount);

            // Show confirmation modal
            $('#confirmPaymentModal').modal('show');
        });
        $('#finalizePaymentBtn').on('click', function () {
            const userId = $('#user_id').val();
            const amount = $('#amount').val();
            const reason = $('#reason').val();
            let form = new Form();

            $.ajax({
                url: '{{ route("pos-cash-payments.store") }}',
                method: 'POST',
                data: {
                    user_id: userId,
                    amount: amount,
                    reason: reason,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {

                    form.successMessage('Payment Initiated Successfully.');

                    $('#confirmPaymentModal').modal('hide');
                    $('#initiatePaymentModal').modal('hide');
                    
                    window.location.reload();
                },
                error: function (error) {
                    form.errorMessage(error.response.data.message);  
                }
            });

         
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function () {
            let paymentId; 
    
            // Show approval confirmation modal
            $(document).on('click', '.approve-payment', function (e) {
                e.preventDefault();
                paymentId = $(this).data('id');
                $('#approvePaymentModal').modal('show');
            });
    
            // Show rejection confirmation modal
            $(document).on('click', '.reject-payment', function (e) {
                e.preventDefault();
                paymentId = $(this).data('id');
                $('#rejectPaymentModal').modal('show');
            });
            // Show disbursement confirmation modal
            $(document).on('click', '.disburse-payment', function (e) {
                e.preventDefault();
                paymentId = $(this).data('id');
                $('#disbursePaymentModal').modal('show');
            });
    
            // Confirm Approval
            $('#confirmApproveBtn').on('click', function () {
                let form = new Form();
                $.ajax({
                    url: '{{ route("pos-cash-payments.confirm-approval") }}',
                    method: 'POST',
                    data: {
                        id: paymentId,
                        gl_account_id: $('#gl_account_id option:selected').val(),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        form.successMessage('Payment Approved Successfully.');
                        $('#approvePaymentModal').modal('hide');
                        location.reload(); 
                    },
                    error: function (error) {
                        form.errorMessage(error.responseText);  
                    }
                });
            });
    
            // Confirm Rejection with Reason
            $('#confirmRejectBtn').on('click', function () {
                let form = new Form();
                const reason = $('#rejectionReason').val();
                if (!reason) {
                    form.errorMessage('Rejection Reason Required');
                    return;
                }
    
                $.ajax({
                    url: '{{ route("pos-cash-payments.reject") }}',
                    method: 'POST',
                    data: {
                        id: paymentId,
                        reason: reason,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        form.successMessage("Payment rejected successfully!");
                        $('#rejectPaymentModal').modal('hide');
                        location.reload(); 
                    },
                    error: function (error) {
                        form.errorMessage(error.responseText);  
                    }
                });
            });

             // Confirm Disbursement
             $('#confirmDisburseBtn').on('click', function () {
                let form = new Form();
                $.ajax({
                    url: '{{ route("pos-cash-payments.disburse") }}',
                    method: 'POST',
                    data: {
                        id: paymentId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {

                        form.successMessage('Payment disbursed Successfully.');
                        printBill();
                        // $('#disbursePaymentModal').modal('hide');
                        // location.reload(); 
                    },
                    error: function (error) {
                        form.errorMessage(error.responseText);  
                    }
                });
            });
            function printBill() {
            jQuery.ajax({
                url: "{{ url('/admin/pos-cash-payments/print-disbursement/') }}"+'/'+paymentId,
                type: 'GET',
                async: false,   //NOTE THIS
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    var divContents = response;
                    var printWindow = window.open('', '', 'width=600');
                    printWindow.document.write(divContents);
                    printWindow.document.close();
                    printWindow.print();
                    printWindow.close();
                    // location.reload();
                    location.href = '{{ route("pos-cash-payments.index") }}';

                }
            });
        }
        });
    </script>
    
@endsection
