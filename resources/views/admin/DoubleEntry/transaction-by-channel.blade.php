@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} : Payments by Method</h3>
            </div>
            @include('message')
            <div class="box-body">
                <div class="row pb-4">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="restaurant_id">Payment Method</label>
                            {!!Form::select('payment_method', $paymentMethods, null, ['placeholder'=>'Select Account ', 'class' => 'form-control mlselec6t','required'=>true,'title'=>'Please select Account','id'=>'payment_method'  ])!!}
                        </div>
                    </div>
                    @if($permission == 'superadmin')
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="restaurant_id">Branch</label>
                                {!!Form::select('restaurant_id', $branches, null, ['placeholder'=>'Select Branch ', 'class' => 'form-control mlselec6t','required'=>true,'title'=>'Please select Branch','id'=>'restaurant_id'  ])!!}
                            </div>
                        </div>
                    @endif

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
                    <div class="col-md-2">
                        <div class="form-group">
                            <button type="submit" id="filter" class="btn btn-primary btn-sm" style="margin-top: 25px;">Filter</button>
                        </div>
                    </div>
                </div>
                <table class="table table-striped mt-3" id="cashiersTable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Date and Time</th>
                        <th>Cashier</th>
                        <th>Payment Method</th>
                        <th>Branch</th>
                        <th>Gl Account</th>
                        <th>Balancing account</th>
                        <th>Reconciled</th>
                        <th>Posted</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th colspan="9" style="text-align:right">Total:</th>
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
        var VForm = new Form();
        $(document).ready(function() {
            var table = $("#cashiersTable").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('tender-entry.transactions-by-channel') !!}',
                    data: function(data) {
                        var payment_method = $('#payment_method').val();
                        var restaurant_id = $('#restaurant_id').val();
                        var from = $('#start_date').val();
                        var to = $('#end_date').val();
                        data.from = from;
                        data.to = to;
                        data.payment_method = payment_method;
                        data.restaurant_id = restaurant_id;
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
                        data: "gl_account_name",
                        name: "gl_account_name",
                        searchable: false
                    },
                    {
                        data: "balancing_account.account_name",
                        name: "balancing_account",
                        searchable: false
                    },
                    {
                        data: "reconciled",
                        name: "reconciled",
                        searchable: false
                    },
                    {
                        data: "posted",
                        name: "posted",
                        searchable: false
                    },
                    {
                        data: "amount",
                        name: "amount",
                        searchable: false
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
                        .column(9)
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);

                    // Total over this page
                    pageTotal = api
                        .column(9, { page: 'current' })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);

                    // Update footer
                    api.column(9).footer().innerHTML =
                        total.toLocaleString('en-US', {
                            style: 'decimal',
                            minimumFractionDigits: 2
                        });
                }

            });

            $('#filter').click(function(e){
                e.preventDefault();
                table.draw();
            });
            $(".mlselec6t").select2();

        })

    </script>
@endpush
