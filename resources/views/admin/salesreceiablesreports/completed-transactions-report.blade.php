@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">{{ $title }}</h3>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form method="get">
                    <div class="row">

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">From</label>
                                <input type="date" name="from" id="start-date" class="form-control" value="{{request()->input('from') ?? date('Y-m-d')}}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">To</label>
                                <input type="date" name="to" id="end-date" class="form-control"  value="{{request()->input('to') ?? date('Y-m-d')}}">
                            </div>
                        </div>


                        <div class="form-group col-md-3" style="padding-top: 1.5%">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <button type="submit" class="btn btn-warning" name="manage-request" value="pdf"><i class="fa fa-file-pdf"></i></button>
                            <button title="Print" type="button" class="btn btn-danger" name="manage-request"
                                    onclick="printgrn(this)" value="print">
                                <i class="fa fa-print" aria-hidden="true"></i>
                            </button>

                        </div>
                    </div>
                </form>

                <hr>

                <table class="table table-bordered table-hover" id="cashiersTable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Invoice</th>
                        <th>Route</th>
                        <th>Item Count</th>
                        <th>Stock Moves</th>
                        <th>Invoice Total</th>
                        <th>Debtor Tran Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($items as $item)

                        <tr data-toggle="collapse" data-target="#details{{ $loop->iteration }}" class="accordion-toggle">
                            <td class="toggle-icon parent-cell"><i class="fas fa-circle-plus"></i></td>
                            <td>{{ $item -> created_at }}</td>
                            <td>{{ $item->requisition_no }}</td>
                            <td>{{ $item->route }}</td>
                            <td>{{ $item->get_related_item_count }}</td>
                            <td>{{ $item->stock_moves_count  }}</td>
                            <td>{{ number_format($item->getOrderTotal(), 2)  }}</td>
                            <td>{{ number_format($item->totalDebtors(), 2) }}</td>
                        </tr>
                        <tr id="details{{ $loop->iteration }}" class="collapse child-row">
                            <td colspan="16">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Item Code</th>
                                        <th>Item Name</th>
                                        <th>Quantity</th>
                                        <th>Amount</th>
                                        <th>Bin</th>
                                        <th>Stock Moved</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $sortedRelatedItems = $item->getRelatedItem->map(function ($relatedItem) use ($item) {
                                                $stockIdCode = $relatedItem->getInventoryItemDetail?->stock_id_code;
                                                $existsInStockMoves = $item->stockMoves->contains('stock_id_code', $stockIdCode);
                                                $relatedItem->status = $existsInStockMoves ? 'Yes' : 'No';
                                                return $relatedItem;
                                            })->sortBy('status');
                                        @endphp

                                        @foreach($sortedRelatedItems as $relatedItem)
                                            <tr>
                                                <td>{{ $relatedItem->getInventoryItemDetail?->stock_id_code }}</td>
                                                <td>{{ $relatedItem->getInventoryItemDetail?->title }}</td>
                                                <td>{{ $relatedItem->quantity }}</td>
                                                <td>{{ number_format($relatedItem->total_cost,2) }}</td>
                                                <td>{{ $relatedItem->getInventoryItemDetail?->getBin($relatedItem->store_location_id) }}</td>
                                                <td>{{ $relatedItem->status }}</td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                                @if(can('reprocess-invoices',$pmodule))
                                    <div class="pull-right" style="margin-top: 15px;">
                                        <a href="{{ route('sales-and-receivables-reports.unbalanced-invoices-process', $item) }}" type="button" class="btn btn-primary">
                                            <i class="fa fa-check"></i>
                                            Process Invoice
                                        </a>
                                    </div>
                                @endif

                            </td>
                        </tr>
                    @endforeach

                    </tbody>

                </table>

            </div>
        </div>
    </section>

@endsection


@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />
    <style>
        #create_datatable1 .even {
            background-color: #ddd;
        }
        .reportRange {
            display: flex;
            align-content: center;
            justify-content: stretch;
            border: 1px solid #eee;
            cursor: pointer;
            height: 35px;
        }

        .text-danger {
            color: #f80202;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            var table = $("#cashiersTable").DataTable();
            $('#filter').click(function(e){
                e.preventDefault();
                table.draw();
            });
        })
        $(document).ready(function () {
            $('.parent-cell').on('click', function() {
                var parentRow = $(this).closest('tr');
                var childRow = parentRow.next('.child-row');
                var toggleIcon = parentRow.find('.toggle-icon i');

                if (childRow.is(':visible')) {

                    childRow.hide();
                    toggleIcon.toggleClass('fa-circle-plus fa-circle-minus');
                } else {

                    $('.child-row').hide();
                    $('.toggle-icon i').removeClass('fa-circle-minus').addClass('fa-circle-plus');

                    childRow.show();
                    toggleIcon.toggleClass('fa-circle-plus fa-circle-minus');
                }

                var childTableId = childRow.find('.child-table').attr('id');
                var childRowIndex = childRow.attr('id').split('-')[2]; // Extract the index from the child-row id

                childRow.toggleClass('collapse');


            });
        });

        $(document).ready(function() {
            let start = moment().subtract(30, 'days');
            let end = moment();

            $('.reportRange').daterangepicker({
                startDate: start,
                endDate: end,
                alwaysShowCalendars: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(7, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            });
        })

        $(function() {

            $(".mlselect").select2();
        });

        function printgrn(input) {
            var postData = $(input).parents('form').serialize() + '&manage-request=print';
            var url = $(input).parents('form').attr('action');

            jQuery.ajax({
                url: url,
                type: 'GET',
                contentType: false,
                cache: false,
                processData: false,
                data: postData,
                success: function(response) {

                    var divContents = response;
                    //alert(divContents);
                    var printWindow = window.open('', '', 'width=600');
                    printWindow.document.write(divContents);
                    printWindow.document.close();
                    printWindow.print();
                    printWindow.close();
                }
            });
        }
    </script>


@endsection
