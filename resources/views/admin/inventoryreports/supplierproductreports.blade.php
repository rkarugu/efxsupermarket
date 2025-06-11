@extends('layouts.admin.admin')

@section('content')

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Supplier Sales Products Report</h3>
                    <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Inventory Reports </a>
                </div>
            </div>

            <div class="box-header with-border no-padding-h-b">
                <div style="height: 150px ! important;">
                    {{-- <div class="card-header">

                        <div class="box-header-flex">
                            <h3 class="box-title">Supplier Sales Products Report</h3>
                        </div> 
                    </div> --}}
                    <br>
                    {!! Form::open(['route' => 'inventory-reports.supplier-product-reports', 'method' => 'get']) !!}
                    <div>
                        <div class="col-md-12 no-padding-h">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="start-date">Start Date:</label>
                                    <input type="date" id="start-date" class="form-control" name="start-date"
                                        autocomplete="off" value="{{ @request()->get('start-date') }}">
                                </div>
                            </div>

                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="end-date">End Date:</label>
                                    <input type="date" id="end-date" class="form-control" name="end-date"
                                        autocomplete="off" value="{{ @request()->get('end-date') }}">
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="end-date">Select Supplier:</label>

                                    {!! Form::select('supplier_id', getSuppliers(), request()->supplier_id, [
                                        'class' => 'form-control mlselec6t',
                                        'placeholder' => 'Select Supplier',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="branch">Select Branch:</label>

                                    {!! Form::select('branch', getRestaurants(), request()->branch, [
                                        'class' => 'form-control mlselec6t',
                                        'placeholder' => 'Select Branch',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-sm-3" style="margin-top:24px;">
                                <button type="submit" class="btn btn-success" name="manage-request" value="filter">
                                    Filter
                                </button>
                                <button title="Export In Excel" type="submit" class="btn btn-success" name="manage-request"
                                    value="xls">Excel
                                </button>

                                <a class="btn btn-success" href="{!! route('inventory-reports.supplier-product-reports') !!}">Clear</a>
                            </div>


                        </div>


                    </div>

                    </form>
                </div>

                <br>
                @include('message')

                @php
                    //echo "<pre>"; print_r($lists); die;
                @endphp
                <div class="col-md-12 no-padding-h">

                    @if ($monthRange <= 12)

                        <table class="table table-bordered table-hover">
                            <?php
                            $logged_user_info = getLoggeduserProfile();
                            ?>

                            <tr>
                                <td style="text-align: left;width: 5%;"><b>#</b></td>
                                <td style="text-align: left;width: 30%;"><b>Item</b></td>
                                <td style="text-align: center;width: 10%;"><b>Current QOH</b></td>
                                @if (isset($selectedMonthArr['m']) && !is_null($selectedMonthArr['m']))
                                    @foreach ($selectedMonthArr['m'] as $key => $month)
                                        <td style="text-align: right;"><b>{{ getMonthsNameToNumber($month) }}</b></td>
                                    @endforeach
                                @endif
                            </tr>
                            <!-- Dynamic code start -->

                            <?php
                            $main_qty = [];
                            $main_vat = [];
                            $main_net = [];
                            $main_total = [];
                            
                            $final_arr = [];
                            $new_final_arr = [];
                            
                            ?>
                            @if (isset($selectedMonthArr['m']) && !is_null($selectedMonthArr['m']))
                                @foreach ($selectedMonthArr['m'] as $key => $month)
                                    @php
                                        $totalMonthlyStock[$key] = 0;

                                    @endphp
                                @endforeach
                            @endif



                            @foreach ($lists as $arr)
                                <tr style="text-align: left;">
                                    <td
                                        colspan="{{ isset($selectedMonthArr['m']) && !is_null($selectedMonthArr['m']) ? count($selectedMonthArr['m']) + 2 : '' }}">
                                        <b>{{ $arr['name'] }}</b>
                                    </td>
                                </tr>
                                <?php $sub_qty = [];
                                $sub_vat = [];
                                $sub_net = [];
                                $sub_total = []; ?>
                                @foreach ($arr['products'] as $item)
                                    @php $total_stock_arr=[]; @endphp
                                    @php

                                        $itemstock = 0;
                                        $itemstockQry = '';
                                        if (isset($item->getstockmoves) && !empty($item->getstockmoves)) {
                                            $itemstockQry = $item->getstockmoves->where(
                                                'wa_inventory_item_id',
                                                $item->id,
                                            );
                                            if (request()->filled('branch')) {
                                                $itemstockQry->where('restaurant_id', request()->branch);
                                            }
                                            $itemstock = $itemstockQry->sum('qauntity');
                                        }
                                    @endphp
                                    <tr style="text-align: right;">
                                        <td>{{ $loop->iteration }}</td>
                                        <td style="text-align: left;">{{ $item['description'] }}</td>
                                        <td style="text-align: center;">{{ $itemstock }}</td>
                                        @if (isset($selectedMonthArr['m']) && !is_null($selectedMonthArr['m']))
                                            @foreach ($selectedMonthArr['m'] as $key => $month)
                                                @php
                                                    $year = $selectedMonthArr['y'][$key];
                                                    $created_from = date($year . '-' . $month . '-01');
                                                    $created_to = date($year . '-' . $month . '-t');

                                                    $created_to = date('Y-m-t', strtotime($created_from));

                                                    $monthlyStock = 0;

                                                    if ($itemstockQry != '') {
                                                        $monthlyStock = $item->getstockmoves
                                                            ->where('wa_inventory_item_id', $item->id)
                                                            ->whereRaw(
                                                                '(document_no LIKE "%INV%" OR document_no LIKE "%CS%" OR document_no LIKE "%RTN%") && DATE(created_at) >= "' .
                                                                    $created_from .
                                                                    '" && DATE(created_at) <= "' .
                                                                    $created_to .
                                                                    '"',
                                                            );
                                                            if (request()->filled('branch')) {
                                                                $monthlyStock->where('restaurant_id', request()->branch);
                                                            }
                                                            $monthlyStock = $monthlyStock->sum('qauntity');
                                                    }

                                                    $total_stock_arr[] = $monthlyStock;
                                                    $new_final_arr[] = $monthlyStock;

                                                @endphp
                                                <td style="text-align: right;">{{ abs($monthlyStock) }}</td>
                                                @php
                                                    $totalMonthlyStock[$key] += abs($monthlyStock);
                                                @endphp
                                            @endforeach
                                        @endif

                                        @php $final_arr[]=$total_stock_arr; @endphp


                                    </tr>
                                @endforeach
                            @endforeach


                            <!--  <tr style="text-align: right;">
                                    <td colspan="1" style="text-align: left;">
                                        <b>Total:  </b>
                                    </td>
                                    <td style="text-align: center;">
                                        <b>{{ abs(array_sum($new_final_arr)) }}</b>
                                    </td>
                                    @if (isset($totalMonthlyStock))
    @foreach ($totalMonthlyStock as $sum)
    <td style="text-align: center;">{{ $sum }}</td>
    @endforeach
    @endif
                                </tr> -->
                        </table>

                    @endif
                </div>

            </div>
        </div>
    </section>

@endsection



@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.0/themes/smoothness/jquery-ui.css">
@endsection

@section('uniquepagescript')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script>
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <!-- <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script> -->
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>


    <script>
        $(".mlselec6t").select2();


        // $('#start_date').datepicker({
        //     format: 'yyyy-mm-dd',
        // });

        // $('#end_date').datepicker({
        //     format: 'yyyy-mm-dd',
        // });


        //  $(window).on('load',function(){
        //     var today = new Date().toISOString().split('T')[0];
        //     document.getElementById("end_date").setAttribute('min', today);
        //  });


        // 	$(document).ready(function() {
        //   $(".datepicker").datepicker({
        //     changeMonth: true,
        //     changeYear: true,
        //     dateFormat: "dd/mm/yy",
        //     minDate: 0, // Disable previous dates
        //     maxDate: "+1y",
        //     onSelect: function(selectedDate) {
        //       if (this.id === "start-date") {
        //         var endDate = $("#end-date").datepicker("getDate");
        //         if (endDate && endDate <= new Date(selectedDate)) {
        //           endDate.setDate(endDate.getDate() + 1);
        //           $("#end-date").datepicker("setDate", endDate);
        //         }
        //       } else {
        //         var startDate = $("#start-date").datepicker("getDate");
        //         if (startDate && startDate >= new Date(selectedDate)) {
        //           startDate.setDate(startDate.getDate() - 1);
        //           $("#start-date").datepicker("setDate", startDate);
        //         }
        //       }
        //     }
        //   });
        // });


        $(document).ready(function() {
            $(".datepicker").datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: "yy-mm-dd",
                // minDate: 0, // Disable previous dates
                // maxDate: "+1y",
                onSelect: function(selectedDate) {
                    if (this.id === "start-date") {
                        var endDate = $("#end-date").datepicker("getDate");
                        if (endDate && endDate <= new Date(selectedDate)) {
                            endDate.setDate(endDate.getDate() + 1);
                            $("#end-date").datepicker("setDate", endDate);
                        }
                    } else {
                        var startDate = $("#start-date").datepicker("getDate");
                        if (startDate && startDate >= new Date(selectedDate)) {
                            startDate.setDate(startDate.getDate() - 1);
                            $("#start-date").datepicker("setDate", startDate);
                        }
                    }

                    // Check if the difference between start and end dates is not more than 12 months
                    var startDate = $("#start-date").datepicker("getDate");
                    var endDate = $("#end-date").datepicker("getDate");
                    if (startDate && endDate) {
                        var diffMonths = (endDate.getFullYear() - startDate.getFullYear()) * 12;
                        diffMonths -= startDate.getMonth();
                        diffMonths += endDate.getMonth();
                        if (diffMonths > 12) {
                            alert(
                                "The difference between start and end dates cannot be more than 12 months.");
                            $("#end-date").val("");
                        }
                    }
                }
            });
        });
    </script>
@endsection
