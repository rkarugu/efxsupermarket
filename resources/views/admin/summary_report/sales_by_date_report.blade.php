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
                <h3 class="box-title">Sales by Date Report
                </h3>
                {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                    << Back to Sales and Receivables Reports </a> --}}
            </div>
        </div>

            <div class="box-header with-border no-padding-h-b">

                @include('message')
                <div class="col-md-12 no-padding-h table-responsive">
                    <form action="" method="GET">
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label for="">Choose From Date</label>
                                <input type="date" name="from" id="date" value="{{ $from }}"
                                    class="form-control">
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="">Choose To Date</label>
                                <input type="date" name="to" id="todate" value="{{ $to }}"
                                    class="form-control">
                            </div>
                            {{--
                                        <div class="col-md-3 form-group">
                                            <label for="">Location</label>
                                            <select name="location" id="location_id" class='form-control'>
                                                <option value="-1" @if (!request()->location || request()->location == '-1') selected @endif>Show All</option>
                                                @php
                                                    $collection = getStoreLocationDropdown();
                                                @endphp
                                                @foreach ($collection as $key => $item)
                                                <option value="{{$key}}" @if (request()->location == $key) selected @endif>{{$item}}</option>
                                                @endforeach
                                            </select>
                                            
                                        </div> --}}
                            <div class="col-md-3 ">
                                <br>
                                <button type="submit" class="btn btn-secondary"
                                    onclick="$('#loader-on').show()">Filter</button>
                                <button type="submit" name="manage" value="excel"
                                    class="btn btn-secondary">Excel</button>
                                <!-- <button type="button" class="btn btn-danger" onclick="printgrn();return false;">Print Report</button> -->
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            <div class="box box-primary">
                <div class="box-header with-border no-padding-h-b">

                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Item Sold</th>
                                <th>Margin</th>
                                <th>Supply Cost</th>
                                <th>Total Sales</th>
                                <th>Tax Value</th>
                            </tr>
                        </thead>
                        @php
                            $total_qty = 0;
                            $total_supply_cost = 0;
                            $total_sum = 0;
                            $total_vat = 0;
                        @endphp
                        <tbody>
                            @foreach ($items as $i)
                                <tr>
                                    <td>{{ $i->Date }}</td>
                                    @php
                                        $supply_cost =
                                            $i->pos_standard_cost_total +
                                            $i->pos_return_standard_cost_total -
                                            $i->pos_returns_standard_cost_total +
                                            $i->invoice_sum_standard_cost -
                                            $i->invoice_return_sum_standard_cost;
                                        $qty =
                                            $i->pos_cash_qty_total +
                                            $i->pos_cash_return_qty_total -
                                            $i->pos_cash_returns_qty_total +
                                            $i->invoices_qty_total -
                                            $i->invoices_return_qty_total;
                                        $total =
                                            $i->pos_cash_sum_total +
                                            $i->pos_cash_return_sum_total -
                                            $i->pos_cash_returns_sum_total +
                                            $i->invoices_sum_total -
                                            $i->invoices_return_sum_total;
                                        $vat =
                                            $i->pos_cash_vat_total +
                                            $i->pos_cash_return_vat_total -
                                            $i->pos_cash_returns_vat_total +
                                            $i->invoices_vat -
                                            $i->invoices_return_vat;
                                        $total_qty += $qty;
                                        $total_sum += $total;
                                        $total_supply_cost += $supply_cost;
                                        $total_vat += $vat;
                                    @endphp

                                    <td>{{ manageAmountFormat($qty) }}</td>
                                    <td>{{ manageAmountFormat($total - $supply_cost) }}</td>
                                    <td>{{ manageAmountFormat($supply_cost) }}</td>
                                    <td>{{ manageAmountFormat($total) }}</td>
                                    <td>{{ manageAmountFormat($vat) }}</td>

                                </tr>
                            @endforeach

                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="1" style="text-align:right">Grand Total</th>
                                <th>{{ manageAmountFormat($total_qty) }}</th>
                                <th>{{ manageAmountFormat($total_sum - $total_supply_cost) }}</th>
                                <th>{{ manageAmountFormat($total_supply_cost) }}</th>
                                <th>{{ manageAmountFormat($total_sum) }}</th>
                                <th>{{ manageAmountFormat($total_vat) }}</th>
                            </tr>
                        </tfoot>
                    </table>

                </div>
            </div>
        </div>


    </section>
@endsection
@section('uniquepagestyle')
    <style type="text/css">
        .select2 {
            width: 100% !important;
        }

        #note {
            height: 60px !important;
        }

        .align_float_right {
            text-align: right;
        }

        .textData table tr:hover,
        .SelectedLi {
            background: #000 !important;
            color: white !important;
            cursor: pointer !important;
        }


        /* ALL LOADERS */

        .loader {
            width: 100px;
            height: 100px;
            border-radius: 100%;
            position: relative;
            margin: 0 auto;
            top: 35%;
        }

        /* LOADER 1 */

        #loader-1:before,
        #loader-1:after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 100%;
            border: 10px solid transparent;
            border-top-color: #3498db;
        }

        #loader-1:before {
            z-index: 100;
            animation: spin 1s infinite;
        }

        #loader-1:after {
            border: 10px solid #ccc;
        }

        @keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
                -ms-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
                -ms-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
    </style>
@endsection

@section('uniquepagescript')
    <div id="loader-on"
        style="
position: absolute;
top: 0;
text-align: center;
display: block;
z-index: 999999;
width: 100%;
height: 100%;
background: #000000b8;
display:none;
">
        <div class="loader" id="loader-1"></div>
    </div>
    <script type="text/javascript">
        function printgrn() {
            jQuery.ajax({
                url: '{{ route('summary_report.report') }}',
                async: false, //NOTE THIS
                type: 'GET',
                data: {
                    date: $('#date').val(),
                    'todate': $('#todate').val(),
                    'request_type': 'print'
                },
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
