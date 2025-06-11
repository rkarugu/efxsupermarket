@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3>
            </div>
            @include('message')
            <div class="box-body">
                <div class="row pb-4">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="restaurant_id">Account Type</label>
                            {!!Form::select('account_id', $acs, null, ['placeholder'=>'Select Account ', 'class' => 'form-control mlselec6t','required'=>true,'title'=>'Please select Account','id'=>'account_id'  ])!!}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="restaurant_id">Branch</label>
                            {!!Form::select('restaurant_id', $branches, null, ['placeholder'=>'Select Branch ', 'class' => 'form-control mlselec6t','required'=>true,'title'=>'Please select Branch','id'=>'restaurant_id'  ])!!}
                        </div>
                    </div>
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
                        <th>Account</th>
                        <th>Branch</th>
                        <th>Credit</th>
                        <th>Debit</th>
                        <th>Balance</th>
{{--                        <th>Action</th>--}}
                    </tr>
                    </thead>
                    <tfoot>

                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Total Page</th>
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
                    url: '{!! route('tender-entry.index') !!}',
                    data: function(data) {
                        var account_id = $('#account_id').val();
                        var restaurant_id = $('#restaurant_id').val();
                        var from = $('#start_date').val();
                        var to = $('#end_date').val();
                        data.from = from;
                        data.to = to;
                        data.account_id = account_id;
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
                        data: "transaction_time",
                        name: "transaction_time"
                    },
                    {
                        data: "account.account_name",
                        name: "account"
                    },
                    {
                        data: "branch.name",
                        name: "branch"
                    },
                    {
                        data: "credit",
                        name: "credit"
                    },
                    {
                        data: "debit",
                        name: "debit"
                    },
                    {
                        data: "balance",
                        name: "balance"
                    }
                    // {
                    //     data: "action",
                    //     name: "action",
                    //     width: "80px",
                    // }
                ],
                footerCallback: function (row, data, start, end, display) {
                    let api = this.api();

                    let intVal = function (i) {
                        return typeof i === 'string'
                            ? i.replace(/[\$,]/g, '') * 1
                            : typeof i === 'number'
                                ? i
                                : 0;
                    };

                    // Total age over all pages
                    let totalAge = api
                        .column(1)
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);

                    // Total gross total over all pages
                    let totalGrossTotal = api
                        .column(4)
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);

                    // Total age over this page
                    let pageTotal = api
                        .column(1, { page: 'current' })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);

                    // Total gross total over this page
                    let pageTotalGrossTotal = api
                        .column(4, { page: 'current' })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);

                    $(api.column(5).footer()).html(
                        'Gross Total: -' + parseFloat(pageTotalGrossTotal).toFixed(2)
                    );
                    // $(api.column(5).footer()).html(
                    //     'Total Gross Total: ' + pageTotal
                    // );
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
