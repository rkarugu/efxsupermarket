@extends('layouts.admin.admin')

@section('content')
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Allocate Pos Payment Manually</h3>
                    <div>
                        <a href="{{url()->previous()}}" class="btn btn-success btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
                    </div>

                </div>
            </div>  
                
            <div class="box-body">
                {!! Form::open(['route' => 'manually-allocate-pos-payments', 'method' => 'get']) !!}
                <div class="row">

                    <div class="col-md-2 form-group">
                        <label for="date">Date</label>
                        <input type="date" name="start_date" id="from" class="form-control" value="{{ request()->get('start_date') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>
                 

                    <div class="col-md-2 form-group">
                        <label for="reference">Reference</label>
                       <input type="text" name="reference" id="reference" class="form-control" value="{{request()->reference}}" required>
                    </div>
                  

                    <div class="col-md-3 form-group"  style="margin-top:25px;">
                        <button type="submit" class="btn btn-success m-2" name="manage_request" value="filter">
                            <i class="fas fa-search"></i> Search Payment
                        </button>

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
                            <th>Paid At</th>
                            <th>Channel</th>
                            <th>Reference</th>
                            <th>Sale Amount</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr data-payment-id={{$payment->id}} data-amount={{$payment->amount}}>
                                    <th>{{$loop->index+1}}</th>
                                    <td>{{$payment->bank_date}}</td>
                                    <td>{{$payment->channel}}</td>
                                    <td>{{$payment->reference}}</td>
                                    <td style="text-align: right;">{{manageAmountFormat($payment->amount)}}</td>
                                    <td ><i class="fa fa-link"></i></td>
                                </tr>           
                            @endforeach
                        </tbody>
                        
                  
                    </table>
                </div>
            </div>
        </div>
        <div class="modal fade" id="allocatePaymentModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Allocate Payment</h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="form-group">
                            <label for="">Select Sale</label>
                            <select class="form-control receipts-dropdown select2"></select>

                        </div>
                        <div class="form-group">
                            <input type="checkbox" name="deallocate" id="deallocate" class="deallocate">
                            <label for="deallocate"> Deallocate Cash Payments</label>

                        </div>
                        {{-- <div class="form-group deallocation_amount_div">
                            <label for="deallocation_amount"> Deallocation Amount </label>
                            <input type="number" name="deallocation_amount" id="deallocation_amount" class="deallocation_amount form-control">
                        </div> --}}
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <a id="confirmDownloadBtn" href="#" class="btn btn-primary">Confirm</a>
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
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">

        $(function () {
            $('body').addClass('sidebar-collapse');
            $(".mlselect").select2();
            $('.select2').select2();

        });
        //handle deallocation amount
        // $(document).ready(function() {
        //     $('.deallocation_amount_div').hide();
        //     $('#deallocate').on('change', function() {
        //         if ($(this).is(':checked')) {
        //             $('.deallocation_amount_div').show();
        //             $('#deallocation_amount').val($('#allocatePaymentModal').data('amount'));
        //         } else {
        //             $('.deallocation_amount_div').hide();
        //         }
        //     });
        // });
    
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    
        $(document).on('click', '.fa-link', function () {
            const paymentId = $(this).closest('tr').data('payment-id');
            const selectedDate = $('#from').val();
            const reference = $('#reference').val();
            const amount = $(this).closest('tr').data('amount');

    
            $.ajax({
                url: '{{ route("manually-allocate-pos-payments.fetch-sales") }}', 
                method: 'GET',
                data: { date: selectedDate },
                success: function (data) {
                    const dropdown = $('#allocatePaymentModal .receipts-dropdown');
                    dropdown.empty();
                    data.receipts.forEach(receipt => {
                        dropdown.append(new Option(receipt.sales_no, receipt.id));
                    });
             
                    dropdown.select2({
                        dropdownParent: $('#allocatePaymentModal')
                    });

                    $('#allocatePaymentModal').modal('show');

                    // Store payment info in modal for later use
                    $('#allocatePaymentModal').data('payment-id', paymentId);
                    $('#allocatePaymentModal').data('reference', reference);
                    $('#allocatePaymentModal').data('selected-date', selectedDate);
                    $('#allocatePaymentModal').data('amount', amount);

                },
                error: function (error) {
                    alert('Error fetching receipts: ' + error.responseText);
                }
            });
        });
    
        $('#confirmDownloadBtn').on('click', function () {
            const paymentId = $('#allocatePaymentModal').data('payment-id');
            const selectedDate = $('#allocatePaymentModal').data('selected-date');
            const reference = $('#allocatePaymentModal').data('reference');
            const receiptId = $('#allocatePaymentModal .receipts-dropdown').val();
            const deallocateCash = $('#allocatePaymentModal .deallocate').is(':checked');
            // const deallocationAmount = $('#allocatePaymentModal .deallocation_amount').val();
            // console.log(deallocationAmount);
    
            // Make API call to perform allocation
            $.ajax({
                url: '{{route("manually-allocate-pos-payments.process")}}', 
                method: 'POST',
                data: {
                    _token:'{{ csrf_token() }}',
                    payment_id: paymentId,
                    receipt_id: receiptId,
                    reference: reference,
                    date: selectedDate,
                    deallocate_cash: deallocateCash
                    // deallocation_amount: deallocationAmount
                },
                success: function (response) {
                    var VForm = new Form();
                    VForm.successMessage(response.message);
                    $('#splitRequestModal').modal('hide');
                    location.reload();
                    $('#allocatePaymentModal').modal('hide');
                },
                error: function(xhr) {
                    var VForm = new Form();

                        var errorMessage = 'Something went wrong. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        VForm.errorMessage(errorMessage);
                                }
            });
        });
    </script>
@endsection
