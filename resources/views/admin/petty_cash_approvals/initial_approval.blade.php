@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title"> Petty Cash Initial Approval </h3>
                    <div style="font-size: 18px">
                        <i class="fas fa-money"></i>
                        Paybill Balance: N/A
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <ul class="nav nav-tabs">
                    @php
                        $filterType = request()->query('manage-request');
                        
                        $activeFilter = $filterType ? true : false;
                    @endphp
                    <li class="{{ !$activeFilter || $filterType == 'order-taking-filter' ? 'active' : '' }}">
                        <a href="#order-taking" data-toggle="tab"> Travel - Order Taking </a>
                    </li>

                    <li class="{{ $filterType == 'delivery-filter' ? 'active' : '' }}">
                        <a href="#delivery" data-toggle="tab"> Travel - Delivery </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane {{ !$activeFilter || $filterType == 'order-taking-filter' ? 'active' : '' }}" id="order-taking">
                        @include('admin.petty_cash_approvals.partials.initial.order-taking')
                    </div>

                    <div class="tab-pane {{ $filterType == 'delivery-filter' ? 'active' : '' }}" id="delivery" style="padding: 10px;">
                        @include('admin.petty_cash_approvals.partials.initial.delivery')
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />

    <style>
        .hover-th{
            color: white;
        }
        .hover-th:hover{
            background-color: white;
            color: white;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        var salesmanAllocations = {!! json_encode($salesmanAllocations) !!};
        var driverAllocations = {!! json_encode($driverAllocations) !!};

        $(document).ready(function() {
            $("body").addClass('sidebar-collapse');
            $(".mlselect").select2();
            $('#incetive_datatable').DataTable({
                "scrollX": true,
                "pageLength": 50,
            });

            $("#salesman-approval-all-checkbox").change(function() {
                if ($(this).prop('checked')) {
                    $(".salesman-approval-checkbox").attr('checked', true);
                } else {
                    $(".salesman-approval-checkbox").attr('checked', false);
                }
            });

            $(".delivery-earned-amount").on('input', function() {
                let total = 0;
                $(".delivery-earned-amount").each(function(index) {
                    if (isNaN(parseFloat($(this).val()))) {
                        total += 0;
                    } else {
                        total += parseFloat($(this).val());
                    }
                });

                $("#delivery-total-amount").text(total);
            });

            $(".order-taking-earned-amount").on('input', function() {
                let total = 0;
                $(".order-taking-earned-amount").each(function(index) {
                    if (isNaN(parseFloat($(this).val()))) {
                        total += 0;
                    } else {
                        total += parseFloat($(this).val());
                    }
                });

                $("#order-taking-total-amount").text(total.toFixed(2));
            });

            $('#refresh-recalculate-btn').click(function(e) {
                e.preventDefault();
                var $btn = $(this);
                var originalbuttontext = $btn.val();
                $btn.val("Processing...");
                var route = "{{ route('petty-cash-approvals.recalculate') }}";
                $.ajax({
                    url: route,
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        salesmanAllocations: salesmanAllocations,
                        driverAllocations: driverAllocations
                    },
                    success: function(response) {
                        location.reload()
                    },
                    error: function(error) {
                    },
                    complete: function() {
                        $btn.val(originalbuttontext);
                    }
                });
            });
        });
    </script>
@endsection
