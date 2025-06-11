@extends('layouts.admin.admin')
@section('content')
    <form  method="PATCH" action="{{ route($model.'.update',$data->id) }}" accept-charset="UTF-8" class=""
          onsubmit="return false;" enctype="multipart/form-data" id="orderForm">
        <a href="{{ route($model.'.index') }}" class="btn btn-primary">Back</a>
        <br>
        <section class="content">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
                @include('message')

                {{ csrf_field() }}
                {{method_field('PUT')}}
                <input type="hidden" name="id" value="{{$data->id}}">
                <?php
                $getLoggeduserProfile = getLoggeduserProfile();

                $purchase_date = date('d-M-Y', strtotime($data->date));
                $purchase_time = date('H:i:s', strtotime($data->time));


                ?>
                <div class="row">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-sm-10">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="">Customer</label>
                                        <select disabled name="route_customer" class="route_customer">
                                            <option selected
                                                    value="{{$data->wa_route_customer_id}}">{{$data->customer_phone_number}}
                                                ({{$data->customer}})
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="" style="display:block">&nbsp;</label>
                                        <button disabled type="button" class="btn btn-primary"
                                                onclick="load_customer()">
                                            <i class="fa fa-address-book"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center pt-5">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="" class="text-4xl" style="font-size: 28px">Total: </label>
                                <label id="top_total" for="" class="text-4xl" style="font-size: 40px">0.0</label>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="box box-primary">
                <div class="box-header with-border no-padding-h-b">
                    <div class="col-md-12 no-padding-h ">
                        <button type="button" class="btn btn-danger btn-sm addNewrow"
                                style="position: fixed;bottom: 30%;left:4%;">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                        </button>
                        <div id="requisitionitemtable">
                            <table class="table table-bordered table-hover" id="mainItemTable">
                                <thead>
                                <tr>
                                    <th>Selection</th>
                                    <th>Description</th>
                                    <th style="width: 90px;">Bal Stock</th>
                                    <th style="width: 90px;">Unit</th>
                                    <th style="width: 90px;">QTY</th>
                                    <th>Selling Price</th>
                                    <th>VAT Type</th>
                                    <th style="width: 90px;">Disc%</th>
                                    <th style="width: 90px;">Discount</th>
                                    <th>VAT</th>
                                    <th>Total</th>
                                    <th>

                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $discount = $totalvat = $total =0;
                                @endphp
                                @foreach ($data->items as $item)
                                    @php
                                        $discount += $item->discount_amount;
                                        $totalvat += $item->vat_amount;
                                        $total += ceil($item->selling_price*$item->qty);
                                    @endphp
                                    <tr>
                                        <td>
                                            <input type="hidden" name="item_id[{{@$item->wa_inventory_item_id}}]"
                                                   class="itemid" value="{{@$item->wa_inventory_item_id}}">
                                            <input style="padding: 3px 3px;" type="text" class="testIn form-control"
                                                   value="{{@$item->item->stock_id_code}}">
                                            <div class="textData"
                                                 style="width: 100%;position: relative;z-index: 99;"></div>
                                        </td>
                                        <td><input style="padding: 3px 3px;" readonly="" type="text"
                                                   name="item_description[{{@$item->wa_inventory_item_id}}]"
                                                   data-id="{{@$item->wa_inventory_item_id}}" class="form-control"
                                                   value="{{@$item->item->title}}"></td>
                                        <td>{{@$item->item->getAllFromStockMoves->sum('qauntity')}}</td>
                                        <td><input style="padding: 3px 3px;" readonly="" type="text"
                                                   name="item_unit[{{@$item->wa_inventory_item_id}}]"
                                                   data-id="{{@$item->wa_inventory_item_id}}" class="form-control"
                                                   value="{{@$item->item->pack_size->title}}"></td>
                                        <td><input style="padding: 3px 3px;" onkeyup="getTotal(this)"
                                                   onchange="getTotal(this)" type="text"
                                                   name="item_quantity[{{@$item->wa_inventory_item_id}}]"
                                                   data-id="{{@$item->wa_inventory_item_id}}"
                                                   class="quantity form-control" value="{{$item->qty}}"></td>
                                        <td><input style="padding: 3px 3px;"
                                                   {{$editPermission}} onchange="getTotal(this)"
                                                   onkeyup="getTotal(this)" type="text"
                                                   name="item_selling_price[{{@$item->wa_inventory_item_id}}]"
                                                   data-id="{{@$item->wa_inventory_item_id}}"
                                                   class="selling_price form-control send_me_to_next_item"
                                                   value="{{$item->selling_price}}"></td>
                                        <td>
                                            <select class="form-control vat_list send_me_to_next_item"
                                                    name="item_vat[{{@$item->wa_inventory_item_id}}]" {{$editPermission}}>
                                                @if($item->tax_manager)
                                                    <option value="{{@$item->tax_manager->id}}"
                                                            selected>{{@$item->tax_manager->title}}</option>
                                                @endif
                                            </select>
                                            <input type="hidden" class="vat_percentage"
                                                   value="{{$item->vat_percentage}}"
                                                   name="item_vat_percentage[{{@$item->wa_inventory_item_id}}]">
                                        </td>
                                        <td><input style="padding: 3px 3px;"
                                                   {{$editPermission}} onchange="getTotal(this)"
                                                   onkeyup="getTotal(this)" type="text"
                                                   name="item_discount_per[{{@$item->wa_inventory_item_id}}]"
                                                   data-id="{{@$item->wa_inventory_item_id}}"
                                                   class="discount_per form-control send_me_to_next_item"
                                                   value="{{$item->discount_percent}}"></td>
                                        <td><input style="padding: 3px 3px;" {{$editPermission}} type="text"
                                                   name="item_discount[{{@$item->wa_inventory_item_id}}]"
                                                   data-id="{{@$item->wa_inventory_item_id}}"
                                                   class="discount form-control send_me_to_next_item"
                                                   value="{{$item->discount_amount}}"></td>

                                        <td>
                                            <span class="vat">{{round($item->selling_price - (($item->selling_price*100) / ($item->vat_percentage+100)),2)}}</span>
                                        </td>
                                        <td><span class="total">{{ceil($item->selling_price*$item->qty)}}</span></td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm deleteparent">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach


                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="11" style="text-align:right">
                                        Total Price
                                    </th>
                                    <td colspan="2">KES <span
                                                id="total_exclusive">{{number_format($total-$totalvat, 2, '.', ',')}}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th colspan="11" style="text-align:right">
                                        Discount
                                    </th>
                                    <td colspan="2">KES <span id="total_discount">{{$discount}}</span></td>
                                </tr>
                                <tr>
                                    <th colspan="11" style="text-align:right">
                                        Total VAT
                                    </th>
                                    <td colspan="2">KES <span
                                                id="total_vat">{{number_format($totalvat, 2, '.', ',')}}</span></td>
                                </tr>
                                <tr>
                                    <th colspan="11" style="text-align:right">
                                        Total
                                    </th>
                                    <td colspan="2">KES <span
                                                id="total_total">{{number_format(ceil($total - $discount), 2, '.', ',') }}</span>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="box-body">
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary btn-sn" id="continuePayment" data-toggle="modal" onclick="checkBalance()"
                                            data-target="#modelId">
                                        <i class="fa fa-arrow-right"></i>
                                        Continue to Payment
                                    </button>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <input type="hidden" id="attached_sales" name="attached_sales" value="">

        <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Payments</h5>
                        <input class="form-control tenderAmount" name="tenderAmount" type="hidden" value="0"
                               onkeyup="checkBalance()" onchange="checkBalance()">


                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                style="margin-top:-22px !important">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
{{--                    <div class="modal-header">--}}
{{--                        <button type="button" class="btn btn-primary btn-sn pull-right" id="searchModala" data-toggle="modal"--}}
{{--                                data-target="#searchModal">--}}
{{--                            <i class="fa fa-add"></i>--}}
{{--                            Add Pending Sale--}}
{{--                        </button>--}}
{{--                    </div>--}}
                    <div class="modal-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>
                                    Method
                                </th>
                                <th>
                                    Amount
                                </th>
                                <th style="display:none">
                                    Reference
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($paymentMethod as $method)
                                @if($method->is_mpesa)
                                    <tr>
                                        <td>
                                            {{$method->title}}
                                        </td>
                                        <td>

                                            <input type="text" class="form-control mpesa"
                                                   id="mpesa_number"
                                                   name="mpesa_number" value="" placeholder="Enter Customer Number">
                                            <span  id="error-mpesa-number" class="error-message text-danger" style="display:none;"></span>



                                        </td>
                                        <td>
                                            <button id="mpesa_pay" class="btn btn-primary btn-sm mpesa_pay" value="{{ $method->id }}">Push STK</button>
                                        </td>
                                    </tr>
                                    @continue
                                @endif
                                <tr>
                                    <td>
                                        {{$method->title}}
                                    </td>
                                    <td>

                                        <input type="text" class="form-control checkBalance dynamic-input-method dynamic-input amount"
                                               min="1"
                                               id="payment_method[{{$method->id}}]"
                                               name="payment_amount[{{$method->id}}]" onkeyup="checkBalance()"
                                               data-method-title="{{$method->title}}"
                                               data-method-cash="{{$method->is_cash}}"
                                               onchange="checkBalance()" value="" placeholder="Enter Payment">
                                        <span  id="error[{{$method->id}}]" class="error-message text-danger" style="display:none;">Please verify  this payment</span>
                                    </td>
                                    <td>
                                        <input type="hidden" class="form-control reference"
                                               id="payment_remarks[{{$method->id}}]"
                                               name="payment_remarks[{{$method->id}}]" value=""
                                               readonly
                                               placeholder="Enter Reference">
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td class="total_total_sales">Total Due</td>
                                <td colspan="2" style="text-align:right">
                                    <span class="total_total_sales total_total">0.00</span>
                                </td>
                            </tr>
                            <tr>
                            <tr>
                                <td class="total_total_sales">Total Tendered</td>
                                <td colspan="2" style="text-align:right">
                                    <span class="total_total_sales total_tendered">0.00</span>
                                </td>
                            </tr>
                            <td style="font-weight: bold;
                                font-size: 22px;
                                background: red;
                                color: white;">Balance
                            </td>
                            <td colspan="2" style="background-color: red;color: white;font-size: 22px;text-align:right"
                                class="cash_change">0.00

                            </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
                        @if(isset($permission[$pmodule.'___save']) || $permission == 'superadmin')
                            <button type="submit" class="btn btn-primary btn-sm addExpense" value="save"> <i class="fa fa-save"></i> Save</button>
                        @endif
                        @if(isset($permission[$pmodule.'___process']) || $permission == 'superadmin')
                            <button type="submit" class="btn btn-primary btn-sm addExpense processIt" id="process"
                                    value="send_request" disabled>  <i class="fa fa-check"></i>  Process
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="transactionsModal" tabindex="-1" role="dialog" aria-labelledby="transactionsModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="pull-right">
                            <img src="{{ asset('images/mpesa.png') }}" alt="Logo 2" class="payment-logo">
                            <img src="{{ asset('images/equity.png') }}" alt="Logo 1" class="payment-logo">
                            <img src="{{ asset('images/vooma.png') }}" alt="Logo 3" class="payment-logo">
                            <!-- Add more logos as needed -->
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <h4 class="modal-title" id="transactionsModalLabel">Transactions</h4>
                    </div>
                    <div class="modal-body">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#active" aria-controls="active" role="tab" data-toggle="tab">Active</a></li>
                            <li role="presentation"><a href="#inactive" aria-controls="inactive" role="tab" data-toggle="tab">Inactive</a></li>
                            <li role="presentation"><a href="#utilized" aria-controls="utilized" role="tab" data-toggle="tab">Utilized</a></li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <!-- Active Transactions Tab -->
                            <div role="tabpanel" class="tab-pane active" id="active">
                                <input type="text" id="searchActive" class="form-control" placeholder="Search Active Transactions">
                                <table class="table table-bordered" id="activeTable">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Time</th>
                                        <th>Name</th>
                                        <th>Reference</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <!-- Active Transactions Data -->
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-right"><strong>Total Selected:</strong></td>
                                        <td colspan="2" class="table-footer-total">0.00</td>
                                    </tr>
                                    </tfoot>
                                </table>
                                <div class="modal-footer">
                                    <button id="proceedButton" class="btn btn-primary"> <i class="fa fa-check"></i> Proceed</button>
                                </div>
                            </div>
                            <!-- Inactive Transactions Tab -->
                            <div role="tabpanel" class="tab-pane" id="inactive">
                                <input type="text" id="searchInactive" class="form-control" placeholder="Search Inactive Transactions">
                                <table class="table table-bordered" id="inactiveTable">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Time</th>
                                        <th>Name</th>
                                        <th>Reference</th>
                                        <th>Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <!-- Inactive Transactions Data -->
                                    </tbody>
                                </table>
                            </div>
                            <!-- Utilized Transactions Tab -->
                            <div role="tabpanel" class="tab-pane" id="utilized">
                                <input type="text" id="searchUtilized" class="form-control" placeholder="Search Utilized Transactions">
                                <table class="table table-bordered" id="utilizedTable">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Time</th>
                                        <th>Method</th>
                                        <th>Sales No</th>
                                        <th>Use Time</th>
                                        <th>Customer</th>
                                        <th>Cashier</th>
                                        <th>Reference</th>
                                        <th>Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <!-- Utilized Transactions Data -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary pull-left" data-dismiss="modal"> <i class="fa fa-close"></i>Close</button>
                    </div>
                </div>
            </div>
        </div>

        {{--mpesa wating Modal--}}
        <div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-labelledby="loadingModalLabel" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="loadingModalLabel"></h4>
                    </div>
                    <div class="modal-body text-center">
                        <!-- Image above the loader -->
                        <img src="{{asset('images/mpesa.png')}}" alt="Loading Image" class="img-responsive center-block" style="max-width: 100px; margin-bottom: 20px;">

                        <!-- Spinner Loader -->
                        <div class="loader">
                            <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions  Modal -->
        <div id="searchModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Search Sales </h4>
                    </div>
                    <div class="modal-body">
                        <!-- Search Input -->
                        <input type="text" id="searchSaleInput" class="form-control" placeholder="Search Sale...">

                        <!-- Table for displaying results -->
                        <table class="table table-bordered table-hover" id="resultsTable" style="display: none;">
                            <thead>
                            <tr>

                                <th>Time</th>
                                <th>Sales No</th>
                                <th>Customer</th>
                                <th>Customer Phone</th>
                                <th>Total</th>
                                <th>Select</th>
                            </tr>
                            </thead>
                            <tbody id="resultsBody">
                            <!-- Results will be appended here -->
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="4" class="text-right"><strong>Current CashSale:</strong></td>
                                <td colspan="2" class="thisSaleTotal text-right">0.00</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-right"><strong>Total Selected:</strong></td>
                                <td colspan="2" class="totalBeforeAttachments text-right">0.00</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-right"><strong>Cumulative Total:</strong></td>
                                <td colspan="2" class="cumulativeTotal text-right">0.00</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="attachSalesBtn">Proceed To Payments</button>
                    </div>
                </div>
            </div>
        </div>

    </form>

@endsection

@section('uniquepagestyle')
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
    <link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">
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

        .textData table tr:hover {
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

        #loader-1:before, #loader-1:after {
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
    <div id="loader-on" style="
position: fixed;
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
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/idb-keyval@6/dist/umd.js"></script>
    <script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>


{{--    <script type="text/javascript">--}}
{{--        function load_customer() {--}}
{{--            $('#loader-on').show();--}}
{{--            url = "{{route('pos.route_customer.create')}}";--}}
{{--            $.ajax({--}}
{{--                type: "GET",--}}
{{--                url: url,--}}
{{--                contentType: false,--}}
{{--                cache: false,--}}
{{--                processData: false,--}}
{{--                success: function (response) {--}}
{{--                    $('#loader-on').hide();--}}
{{--                    if (response.result === -1) {--}}
{{--                        form.errorMessage(response.message);--}}
{{--                    } else {--}}
{{--                        form.successMessage(response.message);--}}
{{--                        $("#pos_route_customer_create").html(response.data)--}}
{{--                    }--}}
{{--                }--}}
{{--            });--}}

{{--        }--}}

{{--        checkBalance();--}}

{{--        function checkBalance() {--}}

{{--            var checkBalance = $('.checkBalance');--}}
{{--            var total = $('#total_total').html();--}}
{{--            // $('.processIt').attr('disabled',true);--}}
{{--            // var payment_amounts = parseFloat($('.tenderAmount').val());--}}
{{--            var payment_amount = 0;--}}
{{--            $.each(checkBalance, function (indexInArray, valueOfElement) {--}}
{{--                var thisval = $(valueOfElement).val();--}}
{{--                if (thisval != '' && !isNaN(thisval)) {--}}
{{--                    payment_amount = parseFloat(payment_amount) + parseFloat(thisval);--}}
{{--                }--}}
{{--            });--}}
{{--            var balance = parseFloat(total.replace(/,/g, '')) - parseFloat(payment_amount);--}}
{{--            $('.cash_change').text((balance).toFixed(2));--}}
{{--            $('.processIt').attr('disabled', false);--}}
{{--            $('.processItR').attr('disabled', false);--}}
{{--            if ((parseFloat(balance)) !== 0) {--}}
{{--                $('.processIt').attr('disabled', true);--}}
{{--                $('.processItR').attr('disabled', true);--}}
{{--            }--}}
{{--            $('.total_tendered').html(parseFloat(payment_amount));--}}
{{--            $('.due').text(total)--}}
{{--            $('.total_tendered').html(parseFloat(payment_amount));--}}
{{--        }--}}

{{--        $(document).ready(function () {--}}
{{--            var amount = $('#total_total').text();--}}
{{--            $('#top_total').text(amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));--}}

{{--        });--}}
{{--        $(document).on('keypress', ".quantity", function (event) {--}}
{{--            if (event.keyCode === 13) {--}}
{{--                event.preventDefault();--}}
{{--                $(".addNewrow").click();--}}
{{--            }--}}
{{--        });--}}
{{--        $(document).on('keypress', ".start_process", function (event) {--}}
{{--            if (event.keyCode === 13) {--}}
{{--                event.preventDefault();--}}
{{--                $(".processIt").click();--}}
{{--            }--}}
{{--        });--}}

{{--        function makemefocus() {--}}
{{--            if ($(".makemefocus")[0]) {--}}
{{--                $(".makemefocus")[0].focus();--}}
{{--            }--}}
{{--        }--}}

{{--        $(document).on('keypress', '.customer_name_enter', function (event) {--}}
{{--            if (event.keyCode === 13) {--}}
{{--                event.preventDefault();--}}
{{--                makemefocus();--}}
{{--            }--}}
{{--        });--}}
{{--        $(document).on('keypress change', '.send_me_to_next_item', function (event) {--}}
{{--            if (event.keyCode === 13) {--}}
{{--                event.preventDefault();--}}
{{--                makemefocus();--}}
{{--            }--}}
{{--        });--}}
{{--        var form = new Form();--}}
{{--        $(document).on('click', '.btnUploadData', function (e) {--}}
{{--            e.preventDefault();--}}
{{--            $('#loader-on').show();--}}
{{--            var postData = new FormData();--}}

{{--            var url = $(this).parents('form').attr('action');--}}
{{--            postData.append('_token', $(document).find('input[name="_token"]').val());--}}
{{--            $.each($('#upload_data')[0].files, function (indexInArray, valueOfElement) {--}}
{{--                postData.append('upload_data[' + indexInArray + ']', $('#upload_data')[0].files[indexInArray]);--}}
{{--            });--}}
{{--            $.ajax({--}}
{{--                type: "POST",--}}
{{--                url: "{{route('pos-cash-sales.esd_upload')}}",--}}
{{--                data: postData,--}}
{{--                contentType: false,--}}
{{--                cache: false,--}}
{{--                processData: false,--}}
{{--                success: function (response) {--}}
{{--                    $('#loader-on').hide();--}}
{{--                    $('#upload_data').replaceWith('<input type="file" style="width: 80%" name="upload_data[]" id="upload_data" class="form-control" multiple accept="text/plain">');--}}
{{--                    if (response.result === -1) {--}}
{{--                        form.errorMessage(response.message);--}}
{{--                    } else {--}}
{{--                        form.successMessage(response.message);--}}
{{--                    }--}}
{{--                }--}}
{{--            });--}}
{{--        });--}}
{{--        // $(document).on('click', '.addExpenseold', function (e) {--}}
{{--        //     e.preventDefault();--}}
{{--        //     $('#loader-on').show();--}}
{{--        //     const form = document.querySelector('#orderForm');--}}
{{--        //     const formId = '#orderForm';--}}
{{--        //     var postData = new FormData($(formId)[0]);--}}
{{--        //     // var postData = new FormData($(this).parents('form')[0]);--}}
{{--        //     var url = $(this).parents('form').attr('action');--}}
{{--        //     postData.append('_token', $(document).find('input[name="_token"]').val());--}}
{{--        //     postData.append('request_type', $(this).val());--}}
{{--        //     console.log(postData)--}}
{{--        //     // alert('kaka')--}}
{{--        //     $.ajax({--}}
{{--        //         url: url,--}}
{{--        //         data: postData,--}}
{{--        //         contentType: false,--}}
{{--        //         cache: false,--}}
{{--        //         processData: false,--}}
{{--        //         headers: {--}}
{{--        //             'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')--}}
{{--        //         },--}}
{{--        //         method: 'PATCH',--}}
{{--        //         success: function (out) {--}}
{{--        //             $('#loader-on').hide();--}}
{{--        //--}}
{{--        //             $(".remove_error").remove();--}}
{{--        //             if (out.result == 0) {--}}
{{--        //--}}
{{--        //                 for (let i in out.errors) {--}}
{{--        //                     var id = i.split(".");--}}
{{--        //                     if (id && id[1]) {--}}
{{--        //                         $("[name='" + id[0] + "[" + id[1] + "]']").parent().append('<label class="error d-block remove_error w-100" id="' + i + '_error">' + out.errors[i][0] + '</label>');--}}
{{--        //                     } else {--}}
{{--        //                         $("[name='" + i + "']").parent().append('<label class="error d-block remove_error w-100" id="' + i + '_error">' + out.errors[i][0] + '</label>');--}}
{{--        //                         $("." + i).parent().append('<label class="error d-block remove_error w-100" id="' + i + '_error">' + out.errors[i][0] + '</label>');--}}
{{--        //                     }--}}
{{--        //                 }--}}
{{--        //                 $('#modelId').modal('hide');--}}
{{--        //             }--}}
{{--        //             if (out.result === 1) {--}}
{{--        //                 form.successMessage(out.message);--}}
{{--        //                 if (out.location) {--}}
{{--        //                     // setTimeout(() => {--}}
{{--        //                     $('#mainItemTable tbody').html('');--}}
{{--        //                     $('#mainItemTable tbody').append('<tr><td><input type="text" class="testIn form-control makemefocus"><div class="textData" style="width: 100%;position: relative;z-index: 99;"></div></td>'--}}
{{--        //                         + '<td></td>'--}}
{{--        //                         + '<td></td>'--}}
{{--        //                         + '<td></td>'--}}
{{--        //                         + '<td></td>'--}}
{{--        //                         + '<td></td>'--}}
{{--        //                         + '<td></td>'--}}
{{--        //                         + '<td></td>'--}}
{{--        //                         + '<td></td>'--}}
{{--        //                         + '<td></td>'--}}
{{--        //                         + '<td></td>'--}}
{{--        //                         + '<td></td>'--}}
{{--        //                         + '<td><button class="btn btn-danger btn-sm deleteparent"><i class="fa fa-trash"></i></button></td>'--}}
{{--        //                         + '</tr>');--}}
{{--        //                     $('[name="customer_name"]').val('');--}}
{{--        //                     $('.entered_cash').val('');--}}
{{--        //                     $('.cash_change').html('');--}}
{{--        //                     $('#total_exclusive').html('0.00');--}}
{{--        //                     $('#total_discount').html('0.00');--}}
{{--        //                     $('#total_vat').html('0.00');--}}
{{--        //                     $('#total_total').html('0.00');--}}
{{--        //                     if (out.requestty == 'save') {--}}
{{--        //                         location.href = out.location;--}}
{{--        //                     } else {--}}
{{--        //                         printBill(out.location);--}}
{{--        //                     }--}}
{{--        //                     // }, 1000);--}}
{{--        //                 }--}}
{{--        //             }--}}
{{--        //             if (out.result === -1) {--}}
{{--        //                 $('#modelId').modal('hide');--}}
{{--        //                 form.errorMessage(out.message);--}}
{{--        //--}}
{{--        //             }--}}
{{--        //         },--}}
{{--        //--}}
{{--        //         error: function (err) {--}}
{{--        //             $('#loader-on').hide();--}}
{{--        //             $(".remove_error").remove();--}}
{{--        //             form.errorMessage('Something went wrong');--}}
{{--        //         }--}}
{{--        //     });--}}
{{--        // });--}}

{{--        $(document).on('click', '.addExpense', function (e) {--}}
{{--            e.preventDefault();--}}
{{--            $('#loader-on').show();--}}
{{--            var postData = new FormData($('#orderForm')[0]);--}}
{{--            var url = $('#orderForm').attr('action');--}}
{{--            const method = $('#orderForm').attr('method').toUpperCase();--}}
{{--            postData.append('_method', method);--}}
{{--            // var errorDisplayed = false;--}}
{{--            // var $amountInputs = $('.amount');--}}
{{--            // var $referenceInputs = $('.reference');--}}
{{--            // $amountInputs.each(function(index) {--}}
{{--            //     var $amountInput = $(this);--}}
{{--            //     var methodTitle = $amountInput.data('method-title');--}}
{{--            //     var inputName =$amountInput.attr('name');--}}
{{--            //     var item_id = inputName.substring(inputName.indexOf("[") + 1, inputName.indexOf("]"));--}}
{{--            //     var remark = "payment_remarks"+'['+item_id+']';--}}
{{--            //     var error = "error"+'['+item_id+']';--}}
{{--            //     var $referenceInput = document.getElementById(remark);--}}
{{--            //     var $errorMessage = document.getElementById(error);--}}
{{--            //--}}
{{--            //     if (methodTitle !== 'CASH'&& $amountInput.val() !== '' && $referenceInput.value === '') {--}}
{{--            //         e.preventDefault();--}}
{{--            //         errorDisplayed = true;--}}
{{--            //         $errorMessage.style.display = 'block';--}}
{{--            //--}}
{{--            //     }--}}
{{--            // });--}}
{{--            //--}}
{{--            // if (errorDisplayed) {--}}
{{--            //     $('#loader-on').hide(); // Hide loader if stopping request--}}
{{--            //     return; // Stop execution--}}
{{--            // }--}}

{{--            postData.append('_token', $(document).find('input[name="_token"]').val());--}}
{{--            var bar = checkBalance()--}}
{{--            if (bar === 0)--}}
{{--            {--}}
{{--                postData.append('request_type', 'send_request');--}}
{{--            }else {--}}
{{--                postData.append('request_type', $(this).val());--}}
{{--            }--}}
{{--            $.ajax({--}}
{{--                url: url,--}}
{{--                data: postData,--}}
{{--                contentType: false,--}}
{{--                cache: false,--}}
{{--                processData: false,--}}
{{--                method: 'POST',--}}
{{--                success: function (out) {--}}
{{--                    $('#loader-on').hide();--}}
{{--                    $(".remove_error").remove();--}}
{{--                    if (out.result == 0) {--}}
{{--                        for (let i in out.errors) {--}}
{{--                            var id = i.split(".");--}}
{{--                            if (id && id[1]) {--}}
{{--                                $("[name='" + id[0] + "[" + id[1] + "]']").parent().append('<label class="error d-block remove_error w-100" id="' + i + '_error">' + out.errors[i][0] + '</label>');--}}
{{--                            } else {--}}
{{--                                $("[name='" + i + "']").parent().append('<label class="error d-block remove_error w-100" id="' + i + '_error">' + out.errors[i][0] + '</label>');--}}
{{--                                $("." + i).parent().append('<label class="error d-block remove_error w-100" id="' + i + '_error">' + out.errors[i][0] + '</label>');--}}
{{--                            }--}}
{{--                        }--}}
{{--                        $('#modelId').modal('hide');--}}
{{--                    }--}}
{{--                    if (out.result === 1) {--}}
{{--                        form.successMessage(out.message);--}}
{{--                        if (out.location) {--}}
{{--                            // setTimeout(() => {--}}
{{--                            $('#mainItemTable tbody').html('');--}}
{{--                            $('#mainItemTable tbody').append('<tr><td><input type="text" class="testIn form-control makemefocus"><div class="textData" style="width: 100%;position: relative;z-index: 99;"></div></td>'--}}
{{--                                + '<td></td>'--}}
{{--                                + '<td></td>'--}}
{{--                                + '<td></td>'--}}
{{--                                + '<td></td>'--}}
{{--                                + '<td></td>'--}}
{{--                                + '<td></td>'--}}
{{--                                + '<td></td>'--}}
{{--                                + '<td></td>'--}}
{{--                                + '<td></td>'--}}
{{--                                + '<td></td>'--}}
{{--                                + '<td><button class="btn btn-danger btn-sm deleteparent"><i class="fa fa-trash" aria-hidden="true"></i></button></td>'--}}
{{--                                + '</tr>');--}}
{{--                            $('[name="customer_name"]').val('');--}}
{{--                            $('.entered_cash').val('');--}}
{{--                            $('.cash_change').html('');--}}
{{--                            $('#total_exclusive').html('0.00');--}}
{{--                            $('#total_discount').html('0.00');--}}
{{--                            $('#total_vat').html('0.00');--}}
{{--                            $('#total_total').html('0.00');--}}
{{--                            if (out.requestty == 'save') {--}}
{{--                                // location.href = out.location;--}}
{{--                            } else {--}}
{{--                                printBill(out.location);--}}
{{--                                printDispatch(out.dispatch);--}}
{{--                            }--}}
{{--                            // }, 1000);--}}
{{--                        }--}}
{{--                    }--}}
{{--                    if (out.result === -1) {--}}
{{--                        form.errorMessage(out.message);--}}
{{--                    }--}}
{{--                },--}}

{{--                error: function (err) {--}}
{{--                    $('#loader-on').hide();--}}
{{--                    $(".remove_error").remove();--}}
{{--                    form.errorMessage('Something went wrong');--}}
{{--                }--}}
{{--            });--}}
{{--        });--}}
{{--        $(document).ready(function () {--}}
{{--            $('body').addClass('sidebar-collapse');--}}
{{--            $('#modelId').on('shown.bs.modal', function () {--}}
{{--                $(this).find('input, textarea, select').val('');--}}
{{--                $('.dynamic-input').eq(0).focus();--}}
{{--                $('.cash_change').html('');--}}
{{--                $('.total_tendered').html('');--}}
{{--            });--}}
{{--            route_customer();--}}
{{--        });--}}
{{--        $(function () {--}}
{{--            $(".mlselec6t").select2();--}}
{{--        });--}}
{{--        $(function () {--}}
{{--            $(".mlselec6t_modal").select2({dropdownParent: $('.modal')});--}}
{{--        });--}}

{{--        function printBill(slug) {--}}
{{--            jQuery.ajax({--}}
{{--                url: slug,--}}
{{--                type: 'GET',--}}
{{--                async: false,   //NOTE THIS--}}
{{--                headers: {--}}
{{--                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')--}}
{{--                },--}}
{{--                success: function (response) {--}}
{{--                    var divContents = response;--}}
{{--                    var printWindow = window.open('', '', 'width=600');--}}
{{--                    printWindow.document.write(divContents);--}}
{{--                    printWindow.document.close();--}}
{{--                    printWindow.print();--}}
{{--                    printWindow.close();--}}
{{--                    location.href = '{{ route($model.'.index') }}';--}}


{{--                }--}}
{{--            });--}}

{{--        }--}}
{{--        function printDispatch(slug) {--}}
{{--            jQuery.ajax({--}}
{{--                url: slug,--}}
{{--                type: 'GET',--}}
{{--                async: false,   //NOTE THIS--}}
{{--                headers: {--}}
{{--                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')--}}
{{--                },--}}
{{--                success: function (response) {--}}
{{--                    var divContents = response;--}}
{{--                    var printWindow = window.open('', '', 'width=600');--}}
{{--                    printWindow.document.write(divContents);--}}
{{--                    printWindow.document.close();--}}
{{--                    printWindow.print();--}}
{{--                    printWindow.close();--}}
{{--                    location.href = '{{ route($model.'.index') }}';--}}


{{--                }--}}
{{--            });--}}

{{--        }--}}

{{--    </script>--}}

{{--    <script>--}}
{{--        $('.datepicker').datepicker({--}}
{{--            format: 'yyyy-mm-dd'--}}
{{--        });--}}
{{--    </script>--}}
{{--    <script>--}}

{{--        $(document).on('keyup', '.testIn', function (e) {--}}
{{--            var vale = $(this).val();--}}
{{--            $(this).parent().find(".textData").show();--}}
{{--            var $this = $(this);--}}
{{--            $.ajax({--}}
{{--                type: "GET",--}}
{{--                url: "{{route('purchase-orders.inventoryItems')}}",--}}
{{--                data: {--}}
{{--                    'search': vale--}}
{{--                },--}}
{{--                success: function (response) {--}}
{{--                    $this.parent().find('.textData').html(response);--}}
{{--                }--}}
{{--            });--}}
{{--        });--}}

{{--        $(document).click(function (e) {--}}
{{--            var container = $(".textData");--}}
{{--            // if the target of the click isn't the container nor a descendant of the container--}}
{{--            if (!container.is(e.target) && container.has(e.target).length === 0) {--}}
{{--                container.hide();--}}
{{--            }--}}
{{--        });--}}
{{--        $(".vat_list").select2();--}}

{{--        function fetchInventoryDetails(varia) {--}}
{{--            var $this = $(varia);--}}
{{--            var itemids = $('.itemid');--}}
{{--            var furtherCall = true;--}}
{{--            $.each(itemids, function (indexInArray, valueOfElement) {--}}
{{--                if ($this.data('id') == $(valueOfElement).val()) {--}}
{{--                    form.errorMessage('This Item is already added in list');--}}
{{--                    furtherCall = false;--}}
{{--                    return true;--}}
{{--                }--}}
{{--            });--}}
{{--            if (furtherCall == true) {--}}
{{--                $.ajax({--}}
{{--                    type: "GET",--}}
{{--                    url: "{{route('pos-cash-sales.getInventryItemDetails')}}",--}}
{{--                    data: {--}}
{{--                        'id': $this.data('id')--}}
{{--                    },--}}
{{--                    success: function (response) {--}}
{{--                        $(".vat_list").select2('destroy');--}}
{{--                        $this.parents('tr').replaceWith(response);--}}
{{--                        $('#mainItemTable tbody tr:first-child td:nth-child(5) input').focus()--}}
{{--                        vat_list();--}}
{{--                        totalofAllTotal();--}}
{{--                    }--}}
{{--                });--}}
{{--            }--}}
{{--        }--}}

{{--        $(document).on('click', '.deleteparent', function () {--}}
{{--            $(this).parents('tr').remove();--}}
{{--            totalofAllTotal()--}}
{{--        });--}}
{{--        $(document).on('click', '.addNewrow', function () {--}}
{{--            $('#mainItemTable tbody').prepend('<tr><td><input type="text" class="testIn form-control makemefocus"><div class="textData" style="width: 100%;position: relative;z-index: 99;"></div></td>'--}}
{{--                + '<td></td>'--}}
{{--                + '<td></td>'--}}
{{--                + '<td></td>'--}}
{{--                + '<td></td>'--}}
{{--                + '<td></td>'--}}
{{--                + '<td></td>'--}}
{{--                + '<td></td>'--}}
{{--                + '<td></td>'--}}
{{--                + '<td></td>'--}}
{{--                + '<td></td>'--}}
{{--                + '<td><button class="btn btn-danger btn-sm deleteparent"><i class="fa fa-trash"></i></button></td>'--}}
{{--                + '</tr>');--}}
{{--            makemefocus();--}}
{{--        });--}}

{{--        var vat_list = function () {--}}
{{--            $(".vat_list").select2(--}}
{{--                {--}}
{{--                    placeholder: 'Select Vat',--}}
{{--                    ajax: {--}}
{{--                        url: '{{route("expense.vat_list")}}',--}}
{{--                        dataType: 'json',--}}
{{--                        type: "GET",--}}
{{--                        delay: 250,--}}
{{--                        data: function (params) {--}}
{{--                            return {--}}
{{--                                q: params.term--}}
{{--                            };--}}
{{--                        },--}}
{{--                        processResults: function (data) {--}}
{{--                            var res = data.map(function (item) {--}}
{{--                                return {id: item.id, text: item.text};--}}
{{--                            });--}}
{{--                            return {--}}
{{--                                results: res--}}
{{--                            };--}}
{{--                        }--}}
{{--                    },--}}
{{--                });--}}
{{--        };--}}
{{--        $(document).on('change', '.vat_list', function () {--}}
{{--            var vat = $(this).val();--}}
{{--            var $this = $(this);--}}
{{--            $.ajax({--}}
{{--                type: "GET",--}}
{{--                url: "{{route('expense.vat_find')}}",--}}
{{--                data: {--}}
{{--                    'id': vat--}}
{{--                },--}}
{{--                success: function (response) {--}}
{{--                    $this.parents('tr').find('.vat_percentage').val(response.tax_value);--}}
{{--                    getTotal($this);--}}
{{--                }--}}
{{--            });--}}

{{--        });--}}

{{--        function getTotal(vara) {--}}
{{--            var price = $(vara).parents('tr').find('.selling_price').val();--}}
{{--            if (price < 0) {--}}
{{--                $(vara).parents('tr').find('.selling_price').val(0);--}}
{{--                price = 0;--}}
{{--            }--}}
{{--            var quantity = $(vara).parents('tr').find('.quantity').val();--}}
{{--            if (quantity <= 0) {--}}
{{--                $(vara).parents('tr').find('.quantity').val('');--}}
{{--                quantity = 0;--}}
{{--            }--}}
{{--            var discount_per = $(vara).parents('tr').find('.discount_per').val();--}}
{{--            if (discount_per < 0) {--}}
{{--                $(vara).parents('tr').find('.discount_per').val(0);--}}
{{--                discount_per = 0;--}}
{{--            }--}}
{{--            var vat_percentage = $(vara).parents('tr').find('.vat_percentage').val();--}}
{{--            if (vat_percentage < 0) {--}}
{{--                $(vara).parents('tr').find('.vat_percentage').val(0);--}}
{{--                vat_percentage = 0;--}}
{{--            }--}}
{{--            var discount = ((parseFloat(price) * parseFloat(quantity)) * parseFloat(discount_per)) / 100;--}}
{{--            var exclusive = ((parseFloat(price) * parseFloat(quantity)) - parseFloat(discount));--}}
{{--            var vat = parseFloat(exclusive) - parseFloat((parseFloat(exclusive) * 100) / (parseFloat(vat_percentage) + 100));--}}
{{--            var total = parseFloat(exclusive);--}}
{{--            $(vara).parents('tr').find('.discount').val((discount).toFixed(2));--}}
{{--            $(vara).parents('tr').find('.vat').html((vat).toFixed(2));--}}
{{--            $(vara).parents('tr').find('.total').html((total).toFixed(2));--}}

{{--            totalofAllTotal();--}}
{{--        }--}}

{{--        $(document).on('keyup', '.discount', function (e) {--}}
{{--            var discount = $(this).val();--}}
{{--            if (discount < 0) {--}}
{{--                $(this).parents('tr').find('.discount').val(0);--}}
{{--                discount = 0;--}}
{{--            }--}}
{{--            var price = $(this).parents('tr').find('.selling_price').val();--}}
{{--            if (price < 0) {--}}
{{--                $(this).parents('tr').find('.selling_price').val(0);--}}
{{--                price = 0;--}}
{{--            }--}}
{{--            var quantity = $(this).parents('tr').find('.quantity').val();--}}
{{--            if (quantity <= 0) {--}}
{{--                $(this).parents('tr').find('.quantity').val('');--}}
{{--                quantity = 0;--}}
{{--            }--}}
{{--            var vat_percentage = $(this).parents('tr').find('.vat_percentage').val();--}}
{{--            if (vat_percentage < 0) {--}}
{{--                $(this).parents('tr').find('.vat_percentage').val(0);--}}
{{--                vat_percentage = 0;--}}
{{--            }--}}
{{--            var totalPriceBeforeDiscount = parseFloat(price) * parseFloat(quantity);--}}
{{--            var discount_per = (discount / totalPriceBeforeDiscount) * 100;--}}
{{--            var exclusive = ((parseFloat(price) * parseFloat(quantity)) - parseFloat(discount));--}}
{{--            var vat = parseFloat(exclusive) - parseFloat((parseFloat(exclusive) * 100) / (parseFloat(vat_percentage) + 100));--}}
{{--            var total = parseFloat(exclusive);--}}
{{--            $(this).parents('tr').find('.discount_per').val((discount_per).toFixed(2));--}}
{{--            $(this).parents('tr').find('.vat').html((vat).toFixed(2));--}}
{{--            $(this).parents('tr').find('.total').html((total).toFixed(2));--}}
{{--            totalofAllTotal();--}}
{{--        });--}}
{{--        $(document).on('change', '.quantity', function (e) {--}}
{{--            var qty = $(this).val();--}}
{{--            var inputName = $(this).attr('name');--}}
{{--            var item_id = inputName.substring(inputName.indexOf("[") + 1, inputName.indexOf("]"));--}}
{{--            $.ajax({--}}
{{--                type: "GET",--}}
{{--                url: "{{route('pos-cash-sales.cal_discount')}}",--}}
{{--                data: {--}}
{{--                    'item_id': item_id,--}}
{{--                    'item_quantity': qty--}}
{{--                },--}}
{{--                success: function (response) {--}}
{{--                    var discountName = 'item_discount['+response.item_id+']';--}}
{{--                    var discId = $('input[name="' + discountName + '"]'); // Assuming `discId` is the selector for the input field where you want to set the discount value--}}
{{--                    // $(discId).val(response.discount);--}}
{{--                    $(discId).val(response.discount).trigger('keyup');--}}
{{--                }--}}
{{--            });--}}
{{--        });--}}

{{--        function totalofAllTotal() {--}}
{{--            var alld = $(document).find('.discount');--}}
{{--            var allv = $(document).find('.vat');--}}
{{--            var allt = $(document).find('.total');--}}
{{--            // var alle = $(document).find('.selling_price');--}}
{{--            // var exclusive = 0;--}}
{{--            var vat = 0;--}}
{{--            var total = 0;--}}
{{--            var discount = 0;--}}
{{--            $.each(alld, function (indexInArray, valueOfElement) {--}}
{{--                discount = parseFloat(discount) + parseFloat($(valueOfElement).val());--}}
{{--            });--}}
{{--            // $.each(alle, function (indexInArray, valueOfElement) {--}}
{{--            //   exclusive = parseFloat(exclusive) + parseFloat($(valueOfElement).val());--}}
{{--            // });--}}
{{--            $.each(allv, function (indexInArray, valueOfElement) {--}}
{{--                vat = parseFloat(vat) + parseFloat($(valueOfElement).text());--}}
{{--            });--}}
{{--            $.each(allt, function (indexInArray, valueOfElement) {--}}
{{--                total =  Math.ceil(parseFloat(total) + parseFloat($(valueOfElement).text())) ;--}}
{{--            });--}}
{{--            var total_exc = (parseFloat(total) - parseFloat(vat));--}}
{{--            $('#total_exclusive').html(total_exc.toLocaleString('en-US', {--}}
{{--                minimumFractionDigits: 2,--}}
{{--                maximumFractionDigits: 2--}}
{{--            }));--}}
{{--            $('#total_vat').html((vat.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})));--}}
{{--            $('#total_total').html((total.toLocaleString('en-US', {--}}
{{--                minimumFractionDigits: 2,--}}
{{--                maximumFractionDigits: 2--}}
{{--            })));--}}
{{--            $('#total_discount').html((discount.toLocaleString('en-US', {--}}
{{--                minimumFractionDigits: 2,--}}
{{--                maximumFractionDigits: 2--}}
{{--            })));--}}
{{--            $('#top_total').text(total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));--}}
{{--            $('#due').text(total)--}}

{{--            checkBalance();--}}

{{--        }--}}

{{--        var payment_method = function () {--}}
{{--            $("#payment_method").select2(--}}
{{--                {--}}
{{--                    placeholder: 'Select Payment Method',--}}
{{--                    ajax: {--}}
{{--                        url: '{{route("expense.payment_method")}}',--}}
{{--                        dataType: 'json',--}}
{{--                        type: "GET",--}}
{{--                        delay: 250,--}}
{{--                        data: function (params) {--}}
{{--                            return {--}}
{{--                                q: params.term--}}
{{--                            };--}}
{{--                        },--}}
{{--                        processResults: function (data) {--}}
{{--                            var res = data.map(function (item) {--}}
{{--                                return {id: item.id, text: item.text};--}}
{{--                            });--}}
{{--                            return {--}}
{{--                                results: res--}}
{{--                            };--}}
{{--                        }--}}
{{--                    },--}}
{{--                });--}}
{{--        };--}}
{{--        var route_customer = function () {--}}
{{--            $(".route_customer").select2(--}}
{{--                {--}}
{{--                    placeholder: 'Select Customer',--}}
{{--                    ajax: {--}}
{{--                        url: '{{route("pos.route_customer.dropdown")}}',--}}
{{--                        dataType: 'json',--}}
{{--                        type: "GET",--}}
{{--                        delay: 250,--}}
{{--                        data: function (params) {--}}
{{--                            return {--}}
{{--                                q: params.term--}}
{{--                            };--}}
{{--                        },--}}
{{--                        processResults: function (data) {--}}
{{--                            var res = data.map(function (item) {--}}
{{--                                return {id: item.id, text: item.title};--}}
{{--                            });--}}
{{--                            return {--}}
{{--                                results: res--}}
{{--                            };--}}
{{--                        }--}}
{{--                    },--}}
{{--                });--}}
{{--        };--}}
{{--    </script>--}}
    @include('partials.shortcuts')
@endsection


