@extends('layouts.admin.admin')

@php
    $user = auth()->user();
    $logged_user_info = getLoggeduserProfile();
    $my_permissions = $logged_user_info->permissions;
    $route_name = \Route::currentRouteName();
@endphp

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Undisbursed Petty Cash </h3>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="filters">
                    <form action="" method="GET">
                        <div class="row d-flex" style="align-items: flex-end">
                            <div class="col-md-2 form-group">
                                <label for="start-date">Branch</label>
                                <select name="branch" id="branch" class="mlselect form-control">
                                    <option value="" selected disabled></option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ $branch->id == request()->branch ? 'selected' : '' }}>
                                            {{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 form-group">
                                <label for="start-date">Petty Cash Type</label>
                                <select name="type" class="mlselect form-control">
                                    <option value="" selected disabled></option>
                                    <option value="order-taking" {{ request()->type == 'order-taking' ? 'selected' : '' }}>
                                        Order Taking</option>
                                    <option value="delivery" {{ request()->type == 'delivery' ? 'selected' : '' }}>Delivery
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-3 form-group">
                                <input type="submit" id="filter-btn" value="Filter" class="btn btn-primary">
                                <a class="btn btn-success ml-12" href="{!! route('petty-cash-approvals.undisbursed-petty-cash') !!}">Clear</a>
                            </div>
                        </div>
                    </form>

                </div>

                <hr>

                <form id="resend-form" action="{{ route('petty-cash-approvals.approve-undisbursed-petty-cash') }}"
                    method="POST">
                    @csrf
                    <input type="hidden" id="resend_ids" name="resend_ids" value="">

                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                            <tr>
                                <th style="width: 3%;"> #</th>
                                <th> Petty Cash Type</th>
                                <th> Shift Date</th>
                                <th> Branch</th>
                                <th> Route</th>
                                <th> Recipient</th>
                                <th> Phone Number</th>
                                <th>Status</th>
                                <th style="text-align: right;"> Amount</th>
                                <th>
                                    <input type="checkbox" name="approve_all" id="salesman-approval-all-checkbox">
                                </th>
                            </tr>
                        </thead>

                        @if (!empty($undisbursedPettyCash))
                            <tbody>
                                @foreach ($undisbursedPettyCash as $record)
                                    <tr>
                                        <th style="width: 3%;" scope="row"> {{ $loop->index + 1 }} </th>
                                        <td>
                                            @if ($record?->user?->role_id == '4')
                                                {{ 'Order Taking' }}
                                            @elseif ($record?->user?->role_id == '6')
                                                {{ 'Delivery' }}
                                            @else
                                                {{ ' ' }}
                                            @endif
                                        </td>
                                        <td> {{ \Carbon\Carbon::parse($record?->created_at)->format('d-m-Y H:i:s') }}
                                        </td>
                                        <td> {{ $record?->travelExpenseTransaction?->route?->branch?->name }}
                                        </td>
                                        <td> {{ $record?->travelExpenseTransaction?->route?->route_name }} </td>
                                        <td> {{ $record?->user?->name }} </td>
                                        <td> {{ $record?->user?->phone_number }} </td>
                                        <td> {{ $record?->initial_approval_status }} </td>
                                        <td style="text-align: right;"> {{ manageAmountFormat($record?->amount) }} </td>
                                        <td>
                                            <input type="checkbox" name="resend_{{ $record->id }}"
                                                class="salesman-approval-checkbox">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tfoot>
                                <tr>
                                    <td colspan="9"></td>
                                    <th style="text-align: right;">
                                        {{ manageAmountFormat($undisbursedPettyCash->sum('amount')) }}
                                    </th>
                                    <td colspan="1"></td>
                                </tr>
                            </tfoot>
                        @endif

                    </table>

                    @if (count($undisbursedPettyCash) > 0)
                        <div style="text-align: right;">
                            @if ($logged_user_info->role_id == 1 || isset($my_permissions['undisbursed-petty-cash___approve']))
                                <input type="submit" id="resend-btn" value="Resend" class="btn btn-primary">
                            @endif
                            @if ($logged_user_info->role_id == 1 || isset($my_permissions['undisbursed-petty-cash___reject']))
                                <input type="submit" id="reject-btn" value="Reject" class="btn btn-primary">
                            @endif
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('/js/form.js') }}"></script>

    <script>
        $(document).ready(function() {

            var allData = @json($undisbursedPettyCash);

            $(".mlselect").select2({
                placeholder: 'Select...'
            });

            function updateSelectedCheckboxes() {
                var selected = [];
                $('.salesman-approval-checkbox:checked').each(function() {
                    // selected.push($(this).attr('name'));
                    selected.push(parseInt($(this).attr('name').replace('resend_', '')));
                });
                return selected;
            }

            function updateResendButtonState() {
                var isAnyChecked = $('.salesman-approval-checkbox:checked').length > 0;
                $('#resend-btn').prop('disabled', !isAnyChecked);
                $('#reject-btn').prop('disabled', !isAnyChecked);
            }

            $('#salesman-approval-all-checkbox').on('change', function() {
                var isChecked = $(this).is(':checked');
                $('.salesman-approval-checkbox').prop('checked', isChecked);
                updateResendButtonState();
            });

            $('.salesman-approval-checkbox').on('change', function() {
                var allChecked = $('.salesman-approval-checkbox').length === $(
                    '.salesman-approval-checkbox:checked').length;
                $('#salesman-approval-all-checkbox').prop('checked', allChecked);
                updateResendButtonState();
            });

            $('#resend-btn').on('click', function() {
                var selectedIds = updateSelectedCheckboxes();

                if (selectedIds.length === 0) {
                    form.errorMessage('Please select at least one checkbox before resending.')
                    return;
                }

                var $btn = $(this);
                $btn.prop('disabled', true).val('Processing...');
                $('#reject-btn').prop('disabled', true);
                var inputchecked = $('.salesman-approval-checkbox').val()
                $('#resend_ids').val(JSON.stringify(selectedIds));

                $.ajax({
                    url: '{{ route('petty-cash-approvals.approve-undisbursed-petty-cash') }}',
                    type: 'POST',
                    data: $('#resend-form').serialize(),
                    success: function(response) {
                        form.successMessage('Petty cash processed successfully.')
                        location.reload();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong.',
                        });
                    },
                    complete: function() {
                        $btn.prop('disabled', false).val('Resend');
                        $('#reject-btn').prop('disabled', false);
                    }
                });
            });

            $('#reject-btn').on('click', function() {
                var selectedIds = updateSelectedCheckboxes();

                if (selectedIds.length === 0) {
                    form.errorMessage('Please select at least one petty cash before rejecting.');
                    return;
                }

                var $btn = $(this);
                $btn.prop('disabled', true).val('Processing...');
                $('#resend-btn').prop('disabled', true);
                var inputchecked = $('.salesman-approval-checkbox').val()
                $('#resend_ids').val(JSON.stringify(selectedIds));

                $.ajax({
                    url: '{{ route('petty-cash-approvals.reject-undisbursed-petty-cash') }}',
                    type: 'POST',
                    data: $('#resend-form').serialize(),
                    success: function(response) {
                        form.successMessage('Petty cash rejected successfully.');
                        location.reload();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong.',
                        });
                    },
                    complete: function() {
                        $('#reject-btn').prop('disabled', false).val('Reject');
                        $('#resend-btn').prop('disabled', false);
                    }
                });
            });

            updateResendButtonState();

        });
    </script>
@endsection
