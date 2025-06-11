@extends('layouts.admin.admin')

@section('content')
    <style>
        .span-action {

            display: inline-block;
            margin: 0 3px;

        }
    </style>
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Item Sales Route Performance Report</h3>
                    <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Inventory Reports </a>
                </div>
            </div>

            <div class="box-header with-border no-padding-h-b">

                <form action="" method="get" role="form">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="">From Date</label>
                                <input type="date" name="from" id="start_date" class="form-control"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="">To Date</label>
                                <input type="date" name="to" id="end_date" class="form-control"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="">Supplier</label>
                                <select name="supplier" class="form-control mtselect" id="supplier">
                                    <option value="" disabled selected>-- Select Supplier --</option>
                                    @foreach (getSupplierDropdown() as $key => $supplier)
                                        <option value="{{ $key }}"
                                            {{ $key == request()->supplier ? 'selected' : '' }}>{{ $supplier }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="">Branch</label>
                                <select name="branch" class="form-control mtselect" id="branch">
                                    <option value="" selected>Show All</option>
                                    @foreach ($branches as $key => $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ $branch->id == request()->branch ? 'selected' : '' }}>{{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <button type="button" class="btn btn-primary" onclick="load_report(); return;">Filter</button>
                            <button type="submit" class="btn btn-primary" name="action" value="excel">
                                <i class="fa fa-file-alt"></i> Excel
                            </button>
                        </div>

                    </div>
                </form>

                @include('message')
                <div class="table-wrapper"> 
                    <div id="show_loading" style="text-align:center;display:none">
                        <h3>Loading....</h3>
                        <hr>
                    </div>
                    <table class="table table-bordered table-hover " id="main_table" style="margin-top:10px">

                    </table>
                </div>

            </div>
        </div>
    </section>
@endsection
@section('uniquepagestyle')
<link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
<style type="text/css">
.select2 {
            width: 100% !important;
        }.table-wrapper {
 overflow: auto;
    max-width: 100%;
    max-height: 80vh; 
    position: relative;
}

#main_table {
    width: auto;
    min-width: 100%;
    table-layout: fixed;
}

#main_table thead th {
    position: sticky;
    top: 0;
    background-color: #fff;
    z-index: 3;
}

#main_table thead th:nth-child(1),
#main_table tbody td:nth-child(1) {
    position: sticky;
    left: 0;
    background-color: #fff;
    z-index: 4;
}

#main_table thead th:nth-child(2),
#main_table tbody td:nth-child(2)  {
    position: sticky;
    left: 70px; 
    background-color: #fff; 
    z-index: 100;
}

#main_table thead th:last-child,
#main_table tbody td:last-child {
    left: auto;
    z-index: 3;
}

#main_table thead th,
#main_table tbody td {
    white-space: nowrap;
}

.customer-data {
    overflow-x: auto;
}
</style>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function(e) {
            $('.mtselect').select2();
        });

        function load_report() {
            let supplier = $('#supplier').val();
            if (supplier) {
                $('#main_table').html("");
                let start_date = $('#start_date').val();
                let end_date = $('#end_date').val();
                let branch = $('#branch').val();
                $('#show_loading').show();
                $.ajax({
                    type: "GET",
                    url: "{{ route('reports.route_performance_report') }}",
                    data: {
                        'from': start_date,
                        'to': end_date,
                        'branch': branch,
                        'supplier': supplier
                    },
                    success: function (response) {
                        console.log(response.data);
                        let head = "<thead><tr>";
                        head += "<th >Stock ID</th>";
                        head += "<th >Product</th>";
                        head += "<th >Total</th>";
                        let customers_qty_arr = [];
                        $.each(response.customers, function(indexInArray, valueOfElement) {
                            customers_qty_arr[indexInArray] = 0;
                            head += `<th >${valueOfElement}</th>`;
                        });
                    
                        head += "</tr></thead>";
                        $('#main_table').append(head);
                        let grand_total = 0;
                        body = "<tbody>";
                        $.each(response.data, function(indexInArray, valueOfElement) {
                            let row_total = 0; // Initialize row total
                            let child = "<tr>";
                            child += `<td >${valueOfElement.stock_id_code}</td>`;
                            child += `<td >${valueOfElement.title}</td>`;
                              let customers_qty = "";
                            //redo calculations
                            $.each(response.customers, function (l_i, l_v) { 
                                    let qty = parseFloat(valueOfElement['qty_'+l_i] ?? 0);
                                    let returns = parseFloat(valueOfElement['returns_'+l_i] ?? 0);
                                    let mother_qty = parseFloat(valueOfElement['mother_qty_'+l_i] ?? 0);
                                    let mother_returns = parseFloat(valueOfElement['mother_returns_'+l_i] ?? 0);
                                    let conversion_factor = parseFloat(valueOfElement.conversion_factor ?? 1);

                                    let net_qty = (qty - returns) / conversion_factor + (mother_qty - mother_returns);

                                    row_total += net_qty;
                                    grand_total += net_qty;
                                    customers_qty_arr[l_i] += net_qty;

                                    customers_qty += `<td>${net_qty.toFixed(2)}</td>`;
                            });
                            child += `<th>${row_total.toFixed(2)}</th>`;
                            child += customers_qty;                         
                            child += "</tr>";
                            body += child;
                        });
                        body += "</tbody>";
                        $('#main_table').append(body);

                        let foot = "<tfoot>";
                        foot += "<th  colspan='2'>Grand Total</th>";
                        foot += `<th  >${grand_total.toFixed(2)}</th>`;
                        $.each(response.customers, function(indexInArray, valueOfElement) {
                            foot += `<th >${customers_qty_arr[indexInArray].toFixed(2)}</th>`;
                        });
                        foot += "</tfoot>";
                        $('#main_table').append(foot);
                        $('#main_table').DataTable();
                        $('#show_loading').hide();

                    }
                });
            }

        }
        load_report();
    </script>
@endsection
