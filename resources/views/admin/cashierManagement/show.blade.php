@extends('layouts.admin.admin')
@section('content')

    @include('message')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="col-md-6">
                    <h3 class="box-title">
                        Manage {{ $cashier->name }}
                    </h3>
                </div>
                <div class="col-md-6">
                    <div  style="text-align: right;">
                        <!-- Drop Button -->
                        {{-- @if($cash_at_hand < 1) disabled @endif --}}
                        <button type="button" class="btn btn-primary drop" data-toggle="modal" data-target="#dropModal">
                            <i class="fa fa-arrow-down"></i> Drop
                        </button>
                        <button type="submit" class="btn btn-primary  declare" id="declare">Declare cashier</button>
                    </div>
                </div>
            </div>

            <div class="box-body box-primary">
                <ul class="nav nav-tabs">
                    <li><a href="#sales" data-toggle="tab">Sales</a></li>
                    <li><a href="#returns" data-toggle="tab">Returns</a></li>
                    <li><a href="#drop_transactions" data-toggle="tab">Drop Transactions</a></li>
                    <li><a href="#tender_transactions" data-toggle="tab">Tender Transactions</a></li>
                    <li class="active"><a href="#cashier_declaration" data-toggle="tab">Cashier Declaration</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane " id="sales">
                        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3>
                        </div>
                        <div class="box-body">
                            <div class="row pb-4">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Date</label>
                                        <input type="date" name="date" id="date" class="form-control" value="{{request()->input('date') ?? date('Y-m-d')}}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <button type="submit" id="filterSales" class="btn btn-primary btn-sm" style="margin-top: 25px;">Filter</button>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-striped" id="sales_table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date Time</th>
                                    <th>Reference</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>


                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="4" style="text-align:right">Total:</th>
                                    <th id="sum_total">0</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="returns">
                        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3>
                        </div>
                        <div class="box-body">
                            <div class="row pb-4">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Date</label>
                                        <input type="date" name="return_date" id="return_date" class="form-control" value="{{request()->input('date') ?? date('Y-m-d')}}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <button type="submit" id="filterReturns" class="btn btn-primary btn-sm" style="margin-top: 25px;">Filter</button>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-striped" id="returns_table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date Time</th>
                                    <th>Sale No</th>
                                    <th>Customer</th>
                                    <th>Item </th>
                                    <th>Status</th>
                                    <th>Quantity</th>
                                    <th>Selling Price</th>
                                    <th>Return Total</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="8" style="text-align:right">Accepted Returns Total:</th>
                                    <th id="returnsTotal">0</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="drop_transactions">
                        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3>
                        </div>
                        <div class="box-body">
                            <div class="row pb-4">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">From</label>
                                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{request()->input('start-date') ?? date('Y-m-d')}}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">To</label>
                                        <input type="date" name="end_date" id="end_date" class="form-control"  value="{{request()->input('end-date') ?? date('Y-m-d')}}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <button type="submit" id="filterDrops" class="btn btn-primary btn-sm" style="margin-top: 25px;">Filter</button>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-striped" id="cashiersDrops">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date Time</th>
                                    <th>Reference</th>
                                    <th>Received By</th>
                                    <th>Amount</th>
                                    <th>Balance</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th colspan="4" style="text-align:right">Total:</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="tender_transactions">
                        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} : Payments by Channel</h3></div>
                        <div class="box-body">
                            <div class="row pb-4">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="payment_method">Payment Method</label>
                                        {!!Form::select('payment_method', $paymentMethods, null, ['placeholder'=>'Select Account ', 'class' => 'form-control mlselec6t','required'=>true,'title'=>'Please select Account','id'=>'payment_method'  ])!!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">From</label>
                                        <input type="date" name="start_date" id="start_date_tender" class="form-control" value="{{request()->input('start-date') ?? date('Y-m-d')}}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">To</label>
                                        <input type="date" name="end_date" id="end_date_tender" class="form-control"  value="{{request()->input('end-date') ?? date('Y-m-d')}}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <button type="submit" id="filterChannels" class="btn btn-primary btn-sm" style="margin-top: 25px;">Filter</button>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-striped mt-3" id="channelsTable">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Document No</th>
                                    <th>Customer</th>
                                    <th>Date and Time</th>
                                    <th>Reference</th>
                                    <th>Cashier</th>
                                    <th>Payment Method</th>
                                    <th>Branch</th>
                                    <th>Amount</th>
                                    <th>Sale Amount</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <td colspan="9">TOTAL</td>
                                    <td id="sum_tender"></td>
                                </tr>

                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane active" id="cashier_declaration">
                        <x-poscash-sales.cashiermanagement.cashier-declaration :cashier="$cashier"/>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="dropModal" tabindex="-1" role="dialog" aria-labelledby="dropModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Confirm Cash Drop</h4>
                    </div>
                    <div class="modal-body">
                        <div id="error-message-container" class="alert alert-danger" style="display: none;"></div>
                        <form id="dropCashForm"  method="POST">
                            @csrf
                            <input id="cashier_id" name="cashier_id" type="number" hidden="hidden" value="{{ $cashier->id }}">
                            <div class="mb-3">
                                <label for="dropAmount" class="form-label"> AmountTo Be Dropped</label>
                                <input type="number" class="form-control" id="amount" name="amount" value="{{ $cash_at_hand }}" readonly >
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="dropCashButton" form="dropCashForm" class="btn btn-primary drop" @if($cash_at_hand < 1) disabled @endif>
                            <i class="fa fa-arrow-down"></i> Confirm Drop
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </section>


@endsection
@push('scripts')
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script>
        var VForm = new Form();
        $(document).ready(function() {

            /*Sales*/
            $('#filterSales').click(function(e){
                e.preventDefault();
                SalesTable.draw();
            });
            var SalesTable = $("#sales_table").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: '{!! route('cashier-management.cashier-sales', base64_encode($cashier->id)) !!}',
                    data: function(data) {
                        data.date = $('#date').val();
                        data.cashier = '{{$cashier->id}}';
                    },
                    dataSrc: function(response) {
                        if (response.sum_total) {
                            $('#sum_total').html(response.sum_total);
                        }
                        return response.data;
                    },
                },
                columns: [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    sortable: false,
                    width: "70px"
                },
                    {
                        data: "created_at",
                        name: "created_at"
                    },
                    {
                        data: "sales_no",
                        name: "sales_no"
                    },
                    {
                        data: "customer",
                        name: "customer"
                    },
                    {
                        data: "total_sales",
                        name: "total_sales",
                        className: "text-right"
                    }
                ],
            });


            /*Drops*/
            var table = $("#cashiersDrops").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: '{!! route('cashier-management.cashier', base64_encode($cashier->id)) !!}',
                    data: function(data) {
                        var from = $('#start_date').val();
                        var to = $('#end_date').val();
                        data.from = from;
                        data.to = to;
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    sortable: false,
                    width: "70px"
                },
                    {
                        data: "created_at",
                        name: "created_at"
                    },
                    {
                        data: "reference",
                        name: "reference"
                    },
                    {
                        data: "user.name",
                        name: "receiver"
                    },
                    {
                        data: "amount",
                        name: "amount",
                        className: "text-right"
                    },
                    {
                        data: "cashier_balance",
                        name: "cashier_balance",
                        className: "text-right"
                    },
                    {
                        data: "action",
                        name: "action"
                    },
                ],
                footerCallback: function (row, data, start, end, display) {
                    let api = this.api();

                    // Remove the formatting to get integer data for summation
                    let intVal = function (i) {
                        return typeof i === 'string'
                            ? i.replace(/[\$,]/g, '') * 1
                            : typeof i === 'number'
                                ? i
                                : 0;
                    };

                    // Total over all pages
                    total = api
                        .column(4)
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);

                    balance = api
                        .column(5)
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);


                    // Update footer
                    api.column(4).footer().innerHTML =
                        total.toLocaleString('en-US', {
                            style: 'decimal',
                            minimumFractionDigits: 2
                        });
                    api.column(5).footer().innerHTML =
                        balance.toLocaleString('en-US', {
                            style: 'decimal',
                            minimumFractionDigits: 2
                        });
                }
            });
            $('#filterDrops').click(function(e){
                e.preventDefault();
                table.draw();
            });
            $('#dropCashButton').click(function(event) {
                // var amount = parseFloat($('#amount').val());
                // var max = parseFloat($('#cash_at_hand').val());
                // var receipt = $('#receipt').val();
                // if (receipt === '')
                // {
                //     $('.error-message-receipt').text('Customer Receipt Number is Required').addClass('text-danger');
                //     event.preventDefault();
                // }else {
                //     $('.error-message-receipt').text('').removeClass('text-danger');
                // }

                // Check if amount is not empty and less than or equal cash at hand
                // if(amount > 1)
                // {
                //     $('.error-message').text('Amount must be above 1 ').addClass('text-danger');
                //     event.preventDefault();
                // }

                // if (isNaN(amount) || amount <= 1 || amount > max) {
                //     $('.error-message').text('Amount must be between 1 and '+max).addClass('text-danger');
                //     event.preventDefault();
                // } else {
                //     $('.error-message').text('').removeClass('text-danger');
                //
                // }
                event.preventDefault()
                saveFormDetails();
            });
            function saveFormDetails() {
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                var form = $('#dropCashForm');
                var formData = form.serialize();
                formData += '&_token=' + encodeURIComponent(csrfToken);
                $('#dropCashButton').prop('disabled', true);
                $.ajax({
                    url: '{{ route('cashier-management.drop') }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        VForm.successMessage('Cash Dropped Successfully');
                        table.ajax.reload();
                        $('#amount').val('');
                        $('#cash_in_hand').html(response.cashier.cash_at_hand)
                        $('#total_dropped').html(response.amount_dropped)
                        printBill(response.drop.id)
                        location.reload();

                    },
                    error: function(error) {
                        var errorMessage = error.responseJSON.message;
                        $('#error-message-container').show()
                        $('#error-message-container').html(errorMessage);
                    }
                });
            }
            $(".mlselec6t").select2();
        })
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
                    // location.reload();
                    location.href = '{{ route($model.'.index') }}';

                }
            });
        }

    </script>
    <script>
        var VForm = new Form();
        $(document).ready(function() {
            $('body').addClass('sidebar-collapse');
            /*table for channel trans*/
            var table = $("#channelsTable").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: '{!! route('cashier-management.tender-transactions', base64_encode($cashier->id)) !!}',
                    data: function(data) {
                        var from = $('#start_date_tender').val();
                        var to = $('#end_date_tender').val();
                        data.payment_method = $('#payment_method').val();
                        data.from = from;
                        data.to = to;
                    },
                    dataSrc: function(response) {
                        if (response.sum_tender) {
                            $('#sum_tender').html(response.sum_tender);
                        }
                        return response.data;
                    },
                },
                columns: [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    sortable: false,
                    width: "70px"
                },
                    {
                        data: "parent.sales_no",
                        name: "parent.sales_no"
                    },
                    {
                        data: "parent.customer",
                        name: "parent.customer"
                    },
                    {
                        data: "created_at",
                        name: "created_at"
                    },
                    {
                        data: "reference",
                        name: "reference"
                    },
                    {
                        data: "parent.user.name",
                        name: "parent.user.name"
                    },
                    {
                        data: "method.title",
                        name: "method.title"
                    },
                    {
                        data: "parent.branch.name",
                        name: "parent.branch.name"
                    },
                    {
                        data: "amount",
                        name: "amount"
                    },
                    {
                        data: "sale_total",
                        name: "sale_total",
                        searchable: false
                    },
                ],
            });
            $('#filterChannels').click(function(e){
                e.preventDefault();
                table.draw();
            });
        })
    </script>
    <script>
        $(document).ready(function() {
            /*Returns*/
            var returnsTable = $("#returns_table").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: '{!! route('cashier-management.cashier-returns')!!}',
                    data: function(data) {
                        data.date = $('#return_date').val();
                        data.cashier = '{{$cashier->id}}';
                    },
                    dataSrc: function(response) {
                        if (response.returnsTotal) {
                            $('#returnsTotal').html(response.returnsTotal);
                        }
                        return response.data;
                    },
                },
                columns: [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    sortable: false,
                    width: "70px"
                },
                    {
                        data: "created_at",
                        name: "created_at"
                    },
                    {
                        data: "pos_cash_sale.sales_no",
                        name: "pos_cash_sale.sales_no"
                    },
                    {
                        data: "pos_cash_sale.customer",
                        name: "pos_cash_sale.customer"
                    },
                    {
                        data: "sale_item.item.title",
                        name: "sale_item.item.title"
                    },
                    {
                        data: "state",
                        name: "state",
                        searchable: false
                    },
                    {
                        data: "return_quantity",
                        name: "return_quantity",
                        className: "text-right"
                    },
                    {
                        data: "sale_item.selling_price",
                        name: "sale_item.selling_price",
                        className: "text-right"
                    },
                    {
                        data: "total",
                        name: "total",
                        className: "text-right",
                        searchable: false
                    },
                ],
            });
            $('#filterReturns').click(function(e){
                e.preventDefault();
                returnsTable.draw();
            });
        })
    </script>
@endpush
