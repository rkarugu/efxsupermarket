@extends('layouts.admin.admin')
@section('content')

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} : </h3>
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
                    @if($permission == 'superadmin')
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="restaurant_id">Branch</label>
                                {!!Form::select('restaurant_id', $branches, null, ['placeholder'=>'Select Branch ', 'class' => 'form-control mlselec6t','required'=>true,'title'=>'Please select Branch','id'=>'restaurant_id'  ])!!}
                            </div>
                        </div>
                    @endif
                    <div class="col-md-3">
                        <div class="form-group">
                            <button type="submit" id="filter" class="btn btn-success btn-sm" style="margin-top: 25px;"> <i class="fas fa-filter"></i> Filter</button>
                        </div>
                    </div>
                </div>
                <table class="table table-striped" id="cashiersTable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Sale Time</th>
                        <th>Archive Time</th>
                        <th>Cashier</th>
                        <th>Branch</th>
                        <th>Sales No</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th colspan="7" style="text-align:right">Grand Total:</th>
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
        var VForm = new Form();
        $(document).ready(function() {
            var table = $("#cashiersTable").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: '{!! route('pos-cash-sales.archive-report') !!}',
                    data: function(data) {
                        var from = $('#start_date').val();
                        var to = $('#end_date').val();
                        data.restaurant_id = $('#restaurant_id').val();
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
                        data: "archived_at",
                        name: "archived_at"
                    },
                    {
                        data: "user.name",
                        name: "user.name"
                    },
                    {
                        data: "branch.name",
                        name: "branch.name"
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
                        data: "total",
                        name: "total",
                        searchable: false,
                        sortable: false
                    },
                    {
                        data:"links",
                        name: "links",
                        orderable: false,
                        searchable: false,

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
                        .column(7)
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);


                    // Update footer
                    api.column(7).footer().innerHTML =
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
