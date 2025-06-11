@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Transaction History</h3>
                    
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form id="topupForm" action="{{ route('transaction-history') }}" method="post">
                    {{ @csrf_field() }}
                    <div class="row">
                        <div class="form-group col-sm-3">
                            <label for="transaction" class="control-label"> Transaction No. </label>
                            <input type="text" name="transaction" id="transaction" class="form-control" required>
                        </div>

                        <div class="form-group col-sm-6">
                            <label style="display: block;">&nbsp;</label>
                            <button type="submit" class="fetch btn btn-primary confirm">Fetch</button>
                        </div>
                    </div>
                </form>
                <hr>

                @if($processingUpload)
                <div class="box-body">                                
                    <div class="table-responsive">
                            @foreach($trans as $tran)
                                <div style="border:1px solid #ddd;padding:10px 0px;margin-bottom:10px;">
                                    <div class="card-body">
                                        <h4 style="border-bottom: 1px solid #ddd;padding:0px 10px;">{{$tran['Cat']}} <small>({{$tran['created_at']}})</small></h4>
                                        @if ($tran['Cat']=='Debtors')
                                            <div class="box-body">
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <label for="">Customer</label>
                                                        <p>{{$tran['customer']}}</p>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label for="">Reference</label>
                                                        <p>{{$tran['reference']}}</p>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label for="">Amount</label>
                                                        <p>{{$tran['amount']}}</p>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label for="">Document No</label>
                                                        <p>{{$tran['document_no']}}</p>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label for="">Channel</label>
                                                        <p>{{$tran['channel']}}</p>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label for="">Branch</label>
                                                        <p>{{$tran['branch_id']}}</p>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label for="">Status</label>
                                                        <p>{{$tran['verification_status']}}</p>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label for="">Entry</label>
                                                        <p>{{$tran['manual_upload_status'] ? 'Manual Upload': 'Bank Hit'}}</p>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label for="">Trans Date</label>
                                                        <p>{{$tran['trans_date']}}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($tran['Cat']=='Suspend')
                                            <div class="box-body">
                                                <div class="row">
                                                    <div class="col-sm-4">
                                                        <label for="">Suspended By</label>
                                                        <p>{{$tran['suspended_by']}}</p>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label for="">Suspended Date</label>
                                                        <p>{{$tran['created_at']}}</p>
                                                    </div>
                                                    @if ($tran['resolved_by'])
                                                        <div class="col-sm-4">
                                                            <label for="">Resolved By</label>
                                                            <p>{{$tran['resolved_by']}}</p>
                                                        </div>
                                                    @endif
                                                    <div class="col-sm-4">
                                                        <label for="">Reason</label>
                                                        <p>{{$tran['reason']}}</p>
                                                    </div>
                                                    
                                                    <div class="col-sm-4">
                                                        <label for="">Document No</label>
                                                        <p>{{$tran['document_no']}}</p>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label for="">Reference</label>
                                                        <p>
                                                            @if ($tran['edited_reference'])
                                                            <p><b>New:</b> {{$tran['reference']}}</p>
                                                            <p><b>Old:</b> {{$tran['edited_reference']}}</p>
                                                            @else
                                                            {{$tran['reference']}}
                                                            @endif
                                                        </p>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label for="">Amount</label>
                                                        <p>
                                                            @if ($tran['edited_amount'])
                                                            <p><b>New:</b> {{$tran['amount']}}</p>
                                                            <p><b>Old:</b> {{$tran['edited_amount']}}</p>
                                                            @else
                                                            {{$tran['amount']}}
                                                            @endif
                                                        </p>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <label for="">Customer</label>
                                                        <p>
                                                            @if ($tran['edited_customer'])
                                                            <p><b>New:</b> {{$tran['customer']}}</p>
                                                            <p><b>Old:</b> {{$tran['edited_customer']}}</p>
                                                            @else
                                                            {{$tran['customer']}}
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                               
                            @endforeach
                    </div>
                </div>
                    
                @endif
            </div>
        </div>
    </section>
    <span class="btn-loader" style="display:none;">
        <img src="<?= asset('/assets/admin/images/loader.gif') ?>" alt="Loader" />
    </span>
@endsection

@section('uniquepagestyle')

@endsection

@section('uniquepagescript')
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('.confirm').on('click',function(e){
                e.preventDefault();
                // $('.btn-loader').show();
                // $("#topupForm").submit();

                // Flag to track if any required field is empty
                let isValid = true;

                // Loop through each required field in the form
                $('#topupForm').find('input[required], select[required], textarea[required]').each(function() {
                    if ($(this).val() === '') {
                        isValid = false;  // Set flag to false if any field is empty
                        $(this).addClass('input-error'); // Optionally add a class to highlight the empty field
                    } else {
                        $(this).removeClass('input-error'); // Remove the error class if the field is filled
                    }
                });

                // If all required fields are filled, submit the form
                if (isValid) {
                    $('.btn-loader').show();
                    $("#topupForm").submit();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Please Provide Transaction No.',
                    });
                    // alert('Please fill out all required fields.');
                }
            });
        });
    </script>
@endsection