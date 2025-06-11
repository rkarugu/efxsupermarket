@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> {!! $title !!}</h3>
                </div>
            </div>

            <div class="box-body">

                <ul class="nav nav-tabs">
                    <li class="active"><a href="#new_requests_tab" data-toggle="tab">New Requests</a></li>
                    <li><a href="#approved_requests_tab" data-toggle="tab">Approved Requests</a></li>
                    <li><a href="#rejected_requests_tab" data-toggle="tab">Rejected Requests</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="new_requests_tab">
                        @include('admin.supplier_utility.wallet_bank_slip_approvals.includes.new_requests')
                    </div>
                    <div class="tab-pane" id="approved_requests_tab">
                        @include('admin.supplier_utility.wallet_bank_slip_approvals.includes.approved_requests')
                    </div>
                    <div class="tab-pane" id="rejected_requests_tab">
                        @include('admin.supplier_utility.wallet_bank_slip_approvals.includes.rejected_requests')
                    </div>
                </div>

            </div>


        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .btn-group {
            display: flex;
            gap: 10px;
        }

        .no-bg {
            background: none;
            border: none;
            padding: 0;
            box-shadow: none;
            font-size: 20px;
            color: #337ab7;
        }

        .no-bg i {
            color: inherit;
        }

        .no-bg:focus {
            outline: none;
            box-shadow: none;
        }

        .no-bg:hover {
            background-color: transparent;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>

    <style>
        #action-buttons button {
            margin-right: 10px;
        }

        .modal-body img {
            max-height: 200px;
            object-fit: cover;
        }
    </style>

    <script>
        $(document).ready(function() {

            $('#new_bank_slips_table').DataTable({
                "paging": true,
                "pageLength": 50,
                "searching": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 50, 100],
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [
                    [0, "asc"]
                ]
            });

            $('#approved_bank_slips_table').DataTable({
                "paging": true,
                "pageLength": 50,
                "searching": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 50, 100],
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [
                    [0, "asc"]
                ]
            });

            $('#rejected_bank_slips_table').DataTable({
                "paging": true,
                "pageLength": 50,
                "searching": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 50, 100],
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [
                    [0, "asc"]
                ]
            });

            $('#select-all').on('change', function() {
                var isChecked = $(this).is(':checked');
                $('.select-item').prop('checked', isChecked);
                toggleActionButtons();
            });

            $(document).on('change', '.select-item', function() {
                var allChecked = $('.select-item').length === $('.select-item:checked').length;
                $('#select-all').prop('checked', allChecked);
                toggleActionButtons();
            });

            function toggleActionButtons() {
                var anyChecked = $('.select-item:checked').length > 0;
                if (anyChecked) {
                    $('#action-buttons').show();
                } else {
                    $('#action-buttons').hide();
                }
            }

            $('#approve-btn').on('click', function() {
                handleButtonClick('Approved', $(this), $('#reject-btn'));
            });

            $('#reject-btn').on('click', function() {
                handleButtonClick('Rejected', $(this), $('#approve-btn'));
            });

            function handleButtonClick(status, clickedButton, otherButton) {
                var selectedIds = [];
                var walletBankPaymentIds = [];
                $('.select-item:checked').each(function() {
                    selectedIds.push($(this).val());
                    walletBankPaymentIds.push($(this).data('wallet-id'));
                });

                if (selectedIds.length > 0) {
                    clickedButton.prop('disabled', true).text('Processing...');
                    otherButton.prop('disabled', true);

                    $.ajax({
                        url: "{{ route('update_wallet_slip_status.update') }}",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            ids: selectedIds,
                            wallet_bank_payment_id: walletBankPaymentIds,
                            status: status
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success',
                                text: response.message,
                                icon: 'success'
                            }).then(function() {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Error',
                                text: 'Something went wrong. Please try again.',
                                icon: 'error'
                            });

                            clickedButton.prop('disabled', false).text(status === 'Approved' ?
                                'Approve' : 'Reject');
                            otherButton.prop('disabled', false);
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'No selection',
                        text: 'Please select at least one item.',
                        icon: 'warning'
                    });
                }
            }

            $('.view-files-btn').click(function() {
                var modalId = $(this).data('bs-target');
                $(modalId).modal('show');
            });

        });
    </script>
@endsection
