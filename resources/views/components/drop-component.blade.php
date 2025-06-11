<div>
    <div style="display: flex; align-items: center; justify-content: flex-end;">
        <button class="btn btn-greenish btn-circle call_cashier" id="call_cashier" style="margin-right: 10px;"  @if($cash_at_hand < 1)  @endif>
            <i class="fa fa-phone"></i>
        </button>
        <button class="btn btn-success btn-circle" style="margin-right: 10px;" data-toggle="modal" data-target="#downloadModal">
            <i class="fa fa-download"></i>
        </button>

        <div style="text-align: right;">
            <div style="font-size: 20px; font-weight: bold">Drop Balance: {{ number_format($selling_allowance, 2) }}</div>
{{--            <div style="font-size: 20px; font-weight: bold">Cash At Hand: {{ number_format($cash_at_hand, 2) }}</div>--}}
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="downloadModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title text-center">
                        Drop Cash
                        <i class="fa fa-coins"></i>
                    </h4>
                </div>
                <div class="modal-body">
                        <div class="form-group">
                            <label>Cash at Hand</label>
                            <input type="text" class="form-control" value="{{ $cash_at_hand }}" readonly>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" class="form-control" id="phone_number" name="phone_number" placeholder="Enter phone number">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter password">
                        </div>
                        <div class="form-group" id="otpGroup" style="display: none;">
                            <label>OTP</label>
                            <input type="text" class="form-control" id="otp" name="otp" placeholder="Enter OTP">
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
                    <button type="button" class="btn btn-primary" id="sendOtp" @if($cash_at_hand < 1) disabled @endif><i class="fa fa-key"></i> Send OTP</button>
                    <button type="button" class="btn btn-success" id="verifyOtp" style="display: none;">Verify OTP</button>
                </div>
            </div>
        </div>
    </div>
@push('styles')
    <style>
        .btn-greenish {
            background: #24d800;
            color: white;
        }
        .swal-actions-wide {
            display: flex !important;
            justify-content: space-between !important;
            padding: 0 1rem !important;
        }

        /* Option 2: Right-align both buttons */
        .swal-edge-button {
            margin-right: 0 !important;
            margin-left: 1rem !important;
        }
    </style>
@endpush
    @push('scripts')
        <script src="{{asset('js/form.js')}}"></script>
        <script>
            var VForm = new Form();
            $(document).ready(function() {
                // Setup AJAX CSRF token
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Send OTP button click handler
                $('#sendOtp').click(function() {
                    let phone = $('#phone_number').val();
                    let password = $('#password').val();

                    // Validate inputs
                    if (!phone || !password) {
                        alert('Please fill in all fields');
                        return;
                    }

                    $.ajax({
                        url: '{{ route("drop.sendOtp") }}',
                        type: 'POST',
                        data: {
                            phone_number: phone,
                            password: password
                        },
                        beforeSend: function() {
                            $('#sendOtp').prop('disabled', true).text('Sending...');
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#otpGroup').show();
                                $('#verifyOtp').show();
                                $('#sendOtp').text('Resend OTP');
                                // alert('OTP sent successfully!');
                                $('#sendOtp').prop('disabled', true);
                            } else {
                                $('#sendOtp').prop('disabled', false).text('Send OTP');

                                VForm.errorMessage(response.message || 'Failed to send OTP');
                            }
                        },
                        error: function(xhr) {
                            VForm.errorMessage('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'));

                        },
                        complete: function() {
                            $('#sendOtp').prop('disabled', false);
                        }
                    });
                });



                function dropCash() {
                    let otp = $('#otp').val();
                    let phone = $('#phone_number').val();
                    $.ajax({
                        url: '{{ route("drop.dropcash") }}',
                        type: 'POST',
                        data: {
                            otp: otp,
                            phone_number: phone,
                        },
                        beforeSend: function() {
                            $('#verifyOtp').prop('disabled', true).text('Processing...');
                        },
                        success: function(response) {
                            if (response.success) {
                                VForm.successMessage('Cash dropped successfully!');
                                $('#downloadModal').modal('hide');
                                printBill(response.drop.id)
                                window.location.reload();
                            } else {
                                VForm.errorMessage(response.message || 'Failed to drop cash');
                            }
                        },
                        error: function(xhr) {
                            VForm.errorMessage('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'));
                        },
                        complete: function() {
                            $('#verifyOtp').prop('disabled', false).text('Verify OTP');
                        }
                    });
                }

                function printBill(slug) {
                    jQuery.ajax({
                        url: "{{ url('/admin/drop-cash-pdf') }}"+'/'+slug,
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
                        }
                    });
                }

                // Reset modal on close
                $('#downloadModal').on('hidden.bs.modal', function () {
                    $('#dropForm')[0].reset();
                    $('#otpGroup').hide();
                    $('#verifyOtp').hide();
                    $('#sendOtp').text('Send OTP');
                });

                // Add click event handler to the call_cashier button
                $('.call_cashier').on('click', function(e) {
                    e.preventDefault();

                    // Show confirmation dialog using SweetAlert
                    Swal.fire({
                        title: 'Confirm Call',
                        text: 'Are you sure you want to send notification to chief cashier?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#24d800',
                        cancelButtonColor: '#d33',
                        confirmButtonText: '<i class="fa fa-phone"></i> Call',
                        cancelButtonText: '<i class="fa fa-times"></i> Cancel',
                        reverseButtons: true,
                        allowHtml: true,
                        customClass: {
                            // Option 1: Spread buttons to edges
                            actions: 'swal-actions-wide',
                            // Option 2: Right-align both buttons
                            confirmButton: 'swal-edge-button',
                            cancelButton: 'swal-edge-button'
                        }
                    }).then((result) => {
                        // If user confirms
                        if (result.isConfirmed) {
                            // Show loading state
                            $(this).prop('disabled', true);
                            $(this).find('i').removeClass('fa-phone').addClass('fa-spinner fa-spin');

                            // Send AJAX request
                            $.ajax({
                                url: '{{ route("drop.call-cashier") }}',
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    // Show success message
                                    Swal.fire({
                                        title: 'Success!',
                                        text: 'Notification sent to chief cashier',
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false,
                                        position: 'top-end',
                                        toast: true,
                                        timerProgressBar: true
                                    });
                                },
                                error: function(xhr, status, error) {
                                    // Show error message
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Failed to send notification. Please try again.',
                                        icon: 'error'
                                    });
                                },
                                complete: function() {
                                    // Reset button state
                                    $('.call_cashier').prop('disabled', false);
                                    $('.call_cashier i').removeClass('fa-spinner fa-spin').addClass('fa-phone');
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
</div>