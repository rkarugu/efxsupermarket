@extends('layouts.admin.admin')

@section('content')
    <!-- Main content -->

    <section class="content" id="order-taking-overview">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> {{ $shift->shiftId }}</h3>
                    <div>
                        @if ($shift->status == 'close')
                            <a href="{{ route('salesman-shift-details.download', $shift->id) }}"
                                class="btn btn-primary   ">Download</a>
                        @endif

                        <a href="{{ route('salesman-shifts.index') }}" class="btn btn-primary   ">{{ '<< ' }}Back to
                            shifts</a>
                    </div>

                </div>

            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message') 
                </div>

                <div id="top-cards" class="d-flex justify-content-between">
                    <div class="major-detail d-flex flex-column justify-content-between border-success">
                        <div class="d-flex">
                            <i class="fas fa-users major-detail-icon"></i>
                            <span class="major-detail-title"> Met Customers </span>
                        </div>

                        <span class="major-detail-value"> {{ $visitedCustomers }}/{{ $routeCustomers }} </span>
                    </div>
                    <div class="major-detail d-flex flex-column justify-content-between border-success">
                        <div class="d-flex">
                            <i class="fas fa-weight major-detail-icon"></i>
                            <span class="major-detail-title"> Tonnage </span>
                        </div>

                        <span class="major-detail-value">
                            {{ number_format($shift->shift_tonnage, 2) }}/{{ $route->tonnage_target }} </span>
                    </div>

                    <div class="major-detail d-flex flex-column justify-content-between border-info">
                        <div class="d-flex">
                            <i class="fas fa-box-open major-detail-icon"></i>
                            <span class="major-detail-title"> CTNs </span>
                        </div>

                        <span class="major-detail-value"> {{ $shift->shift_ctns }}/{{ $route->ctn_target }} </span>
                    </div>

                    <div class="major-detail d-flex flex-column justify-content-between border-primary">
                        <div class="d-flex">
                            <i class="fas fa-cubes major-detail-icon"></i>
                            <span class="major-detail-title"> Dozens </span>
                        </div>

                        <span class="major-detail-value"> {{ $shift->shift_dzns }}/{{ $route->dzn_target }} </span>
                    </div>

                    <div class="major-detail d-flex flex-column justify-content-between border-danger">
                        <div class="d-flex">
                            <i class="fas fa-money major-detail-icon"></i>
                            <span class="major-detail-title"> Sales Target </span>
                        </div>

                        <span class="major-detail-value">{!! format_amount_with_currency($shift->relatedRoute?->sales_target) !!}</span>
                    </div>
                    <div class="major-detail d-flex flex-column justify-content-between border-success">
                        <div class="d-flex">
                            <i class="fas fa-money major-detail-icon"></i>
                            <span class="major-detail-title"> Shift Total </span>
                        </div>

                        <span class="major-detail-value"> {!! format_amount_with_currency($shift->shift_total) !!} </span>
                    </div>
                </div>


                <div class="mt-20 w-100">
                    <div class="box">

                        <div class="box-header with-border">
                            <h3 class="box-title"> Met Customers with Orders ({{ $met_count }}) </h3>
                        </div>

                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table met_customers_with_orders_table" id="">
                                    <thead>
                                        <tr>
                                            <th style="width: 3%;">#</th>
                                            <th> Date </th>
                                            <th> Name </th>
                                            <th> Phone </th>
                                            <th> Center </th>
                                            <th> Order No. </th>
                                            <th>Tonnage</th>
                                            <th> Order Total</th>
                                            <th> Actions </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php $rowNumber = 0; ?>

                                        @foreach ($data as $shiftCustomer)
                                            @if ($shiftCustomer['is_met'] == 1)
                                                <tr>
                                                    <th>{{ ++$rowNumber }}</th>
                                                    <td>{{ $shiftCustomer['is_met_updated_at'] }}</td>
                                                    <td>{{ $shiftCustomer['customer_name'] }}</td>
                                                    <td>{{ $shiftCustomer['customer_phone_no'] }}</td>
                                                    <td>{{ $shiftCustomer['customer_town'] }}</td>
                                                    <td>
                                                        @if ($shiftCustomer['order_no'])
                                                            {{ $shiftCustomer['order_no'] }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>{{ number_format($shiftCustomer['customer_tonnage'], 2) }}</td>


                                                    <td style="text-align: right;">
                                                        @if ($shiftCustomer['order_total'])
                                                            {!! manageAmountFormat($shiftCustomer['order_total']) !!}
                                                        @else
                                                            N/A
                                                        @endif


                                                    </td>
                                                    <td>
                                                        <div class="action-button-div">
                                                            @if ($shiftCustomer['order_slug'])
                                                                <a href="{{ route('get-shop-order-details', $shiftCustomer['order_slug']) }}"
                                                                    class="text-primary" title="View Order Details"><i
                                                                        class='fa fa-eye text-primary fa-lg'></i></a>
                                                            @endif

                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="box-header with-border">
                            <h3 class="box-title"> Met Customers without Orders ({{ $met_without_orders_count }})</h3>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table met_customers_without_orders_table" id="">
                                    <thead>
                                        <tr>
                                            <th style="width: 3%;">#</th>
                                            <th> Date </th>
                                            <th> Name </th>
                                            <th> Phone </th>
                                            <th> Center </th>
                                            <th>Reason</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php $rowNumber2 = 0; ?>
                                        @foreach ($data as $shiftCustomer)
                                            @if ($shiftCustomer['met_without_orders'])
                                                <tr>
                                                    <th>{{ ++$rowNumber2 }}</th>
                                                    <td>{{ $shiftCustomer['reported_issue_created_at'] }}</td>
                                                    <td>{{ $shiftCustomer['customer_name'] }}</td>
                                                    <td>{{ $shiftCustomer['customer_phone_no'] }}</td>
                                                    <td>{{ $shiftCustomer['customer_town'] }}</td>
                                                    <td>{{ $shiftCustomer['reported_issue'] ?? '-' }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>

                                </table>
                            </div>
                        </div>


                        <div class="box-header with-border">
                            <h3 class="box-title"> Totally Unmet Customers ({{ $totally_unmet_count }})</h3>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table totally_unmet_customers" id="">
                                    <thead>
                                        <tr>
                                            <th style="width: 3%;">#</th>
                                            <th> Name </th>
                                            <th> Phone </th>
                                            <th> Center </th>
                                            <th>Reason</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php $rowNumber2 = 0; ?>

                                        @foreach ($data as $shiftCustomer)
                                            @if ($shiftCustomer['totally_unmet_customers'])
                                                <tr>

                                                    <th>{{ ++$rowNumber2 }}</th>
                                                    <td>{{ $shiftCustomer['customer_name'] }}</td>
                                                    <td>{{ $shiftCustomer['customer_phone_no'] }}</td>
                                                    <td>{{ $shiftCustomer['customer_town'] }}</td>
                                                    <td>{{ $shiftCustomer['reported_issue'] ?? '-' }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- summary modal details --}}
    <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-full">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="staticBackdropLabel"> Order Items</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>


                <div class="modal-body">
                    <div class="col-md-12 no-padding-h table-responsive">
                        <table class="table table-bordered table-hover" id="create_datatable1">
                            <thead>
                                <tr>

                                    <th>Product</th>
                                    <th>Total Ordered</th>
                                    <th>Selling Price</th>
                                    <th>Total Cost</th>
                                    <th>Quantity Delivered</th>
                                    <th>Quantity Returned</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>

                </div>

            </div>
        </div>
    </div>
@endsection


@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />

    <style>
        .major-detail {
            border: 2px solid;
            border-radius: 15px;
            padding: 10px 15px;
            height: 80px;
            flex-grow: 1 !important;
            margin-right: 20px;
        }

        .major-detail.border-primary {
            border-color: #0d6efd;
        }

        .major-detail.border-success {
            border-color: #198754;
        }

        .major-detail.border-danger {
            border-color: #dc3545;
        }

        .major-detail.border-info {
            border-color: #0dcaf0;
        }

        .major-detail-icon {
            font-size: 20px;
        }

        .major-detail-title {
            font-size: 18px;
            font-weight: 500;
            margin-left: 12px;
            margin-top: -5px;
        }

        .major-detail-value {
            font-size: 20px;
            font-weight: 600;
        }

        #activity {
            position: relative;
            width: 40%;
        }

        .mt-20 {
            margin-top: 30px !important;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {

            $(".mlselect").select2();
        });

        $(document).ready(function() {
            $('.met_customers_with_orders_table').DataTable({
                "paging": true,
                "pageLength": 10,
                "searching": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 50, 100],
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [
                    [0, "asc"]
                ]
            });
        });

        $(document).ready(function() {
            $('.met_customers_without_orders_table').DataTable({
                "paging": true,
                "pageLength": 10,
                "searching": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 50, 100],
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [
                    [0, "asc"]
                ]
            });
        });

        $(document).ready(function() {
            $('.totally_unmet_customers').DataTable({
                "paging": true,
                "pageLength": 10,
                "searching": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 50, 100],
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [
                    [0, "asc"]
                ]
            });
        });

        $('.btn-summary').click(function() {
            var centerId = $(this).data('id');
            $.ajax({
                url: '/admin/get-order-item-details/' + centerId,
                method: 'GET',
                success: function(data) {
                    // console.log(data);
                    var tableBody = $('#create_datatable1 tbody');
                    tableBody.empty();
                    $.each(data, function(index, item) {
                        var row = $('<tr>');
                        row.append('<td>' + item.get_inventory_item_detail.title + '</td>');
                        row.append('<td>' + item.quantity + '</td>');
                        row.append('<td>' + item.selling_price + '</td>');
                        row.append('<td>' + item.total_cost + '</td>');
                        row.append('<td>' + item.delivered + '</td>');
                        row.append('<td>' + item.is_returned + '</td>');
                        tableBody.append(row);
                    });

                    // Open the modal 
                    $('#create_datatable1').DataTable();
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });

        });
    </script>

    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>
@endsection
