@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"> {!! $title !!}</h3>
            </div>
            @include('message')
            <div class="box-body">
                <div class="row pb-4">
                    @if($permission == 'superadmin')
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="restaurant_id">Branch</label>
                                {!!Form::select('restaurant_id', $branches, request()->input('restaurant_id') ?? null, ['placeholder'=>'Select Branch ', 'class' => 'form-control mlselec6t','title'=>'Please select Branch','id'=>'restaurant_id'  ])!!}
                            </div>
                        </div>
                    @endif
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">From</label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                   value="{{request()->input('start-date') ?? date('Y-m-d')}}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">To</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                   value="{{request()->input('end-date') ?? date('Y-m-d')}}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" name="status" id="status">
                                <option value="">All</option>
                                <option value="1">Accepted</option>
                                <option value="2">Rejected</option>
                                <option value="3">Pending</option>

                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <button type="submit" id="filter" class="btn btn-primary btn-sm" style="margin-top: 25px;">
                                Filter
                            </button>
                        </div>
                    </div>
                </div>
                <table class="table table-striped mt-3" id="cashiersTable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Return Date</th>
                        <th>Processing Date</th>
                        <th>Return GRN</th>
                        <th>Cash Sale No.</th>
                        <th>Product</th>
                        <th>Customer</th>
                        <th>Bin</th>
                        <th>Cashier</th>
                        <th>Returned By</th>
                        <th>Received By</th>
                        <th>Return QTY</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="13" style="text-align:right">Total:</th>
                        <th></th>
                        <th></th>
                    </tr>

                    </tfoot>
                </table>
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
        function printgrn(transfer_no) {
            var url = "{{url('admin/pos-cash-sales/invoice/return-print')}}/" + transfer_no;
            print_this(url);
        }
    </script>
    <script>
        var VForm = new Form();
        $(document).ready(function () {
            var table = $("#cashiersTable").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('pos-cash-sales.returned_cash_sales_list') !!}',
                    data: function (data) {
                        // var payment_method = $('#payment_method').val();
                        var status = $('#status').val();
                        var from = $('#start_date').val();
                        var to = $('#end_date').val();
                        var restaurant_id = $('#restaurant_id').val();
                        data.status = status;
                        data.from = from;
                        data.to = to;
                        data.restaurant_id = restaurant_id;
                    }
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        searchable: false,
                        sortable: false,
                    },
                    {
                        data: "created_at",
                        name: "created_at",
                        searchable: false
                    },
                    {
                        data: "return_date",
                        name: "return_date",
                        searchable: false
                    },
                    {
                        data: "return_grn",
                        name: "return_grn",
                        searchable: false
                    },
                    {
                        data: "pos_cash_sale.sales_no",
                        name: "PosCashSale.sales_no"
                    },
                    {
                        data: "sale_item.item.title",
                        name: "saleItem.item.title"
                    },
                    {
                        data: "pos_cash_sale.customer",
                        name: "PosCashSale.customer"
                    },
                    {
                        data: "bin",
                        name: "bin",
                        searchable: false
                    },
                    {
                        data: "returner.name",
                        name: "name.name",
                        searchable: false
                    },
                    {
                        data: "pos_cash_sale.user.name",
                        name: "PosCashSale.user.name",
                        searchable: true
                    },
                    {
                        data: "receiver.name",
                        name: "receiver.name",
                        searchable: false
                    },
                    {
                        data: "return_quantity",
                        name: "return_quantity",
                        searchable: false
                    },
                    {
                        data: "status",
                        name: "status",
                        searchable: false
                    },
                    {
                        data: "rtn_total",
                        name: "rtn_total",
                        searchable: false
                    },
                    {
                        data: "action",
                        name: "action",
                        searchable: false
                    }
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
                        .column(13)
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);


                    api.column(13).footer().innerHTML =
                        total.toLocaleString('en-US', {
                            style: 'decimal',
                            minimumFractionDigits: 2
                        });
                }
            });

            var orderId = 0;
            $('#cashiersTable tbody').on('click', '.show-details', function (e) {
                e.preventDefault();
                orderId = $(this).data('orderId');
                $('#staticBackdropModal').modal('show');
            });

            $('#filter').click(function (e) {
                e.preventDefault();
                table.draw();
            });
            $(".mlselec6t").select2();

        })

    </script>
@endpush

