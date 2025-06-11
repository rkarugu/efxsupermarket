@extends('layouts.admin.admin')
@section('content')
    <style>
        .total_total_sales {
            font-size: 16px;
            font-weight: 700;
        }
    </style>
    <form id="orderForm" method="POST" action="{{ route($model.'.store') }}" accept-charset="UTF-8" class="" onsubmit="return false;"
          enctype="multipart/form-data">

        <br>
        <div class="container-fluid">
            <div class="clearfix pr-6">
                <a href="{{ route($model.'.index') }}" class="btn btn-primary pull-left"><i class="fa fa-arrow-rotate-back"> </i> Back</a>
                <x-drop-component/>
            </div>
        </div>

        <br>
        <section class="content">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
                @include('message')

                {{ csrf_field() }}
                <?php
                $getLoggeduserProfile = getLoggeduserProfile();
                $purchase_date = date('d-M-Y');
                $purchase_time = date('H:i:s');
                ?>
                <div class="row">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-sm-10">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="">Customer</label>
                                        <select name="route_customer" id="route_customer" class="route_customer"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="" style="display:block">&nbsp;</label>
                                        <button type="button" class="btn btn-primary" onclick="load_customer()">
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
                        {{--                <h3 class="box-title"> Cash Sales</h3>--}}
                        <button type="button" class="btn btn-danger btn-sm addNewrow"
                                style="position: fixed;bottom: 30%;left:4%;"><i class="fa fa-plus" aria-hidden="true"></i></button>
                        <div id="requisitionitemtable" name="item_id[0]">
                            <table class="table table-bordered table-hover" id="mainItemTable">
                                <thead>
                                <tr>
                                    <th>Selection <span style="color: red;">(Search Atleast 3 Keyword)</span></th>
                                    <th>Image</th>
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
                                <tr>
                                    <td>
                                        <input type="text" autofocus placeholder="Search Atleast 3 Keyword"
                                               class="testIn form-control makemefocus">
                                        <div class="textData" style="width: 100%;position: relative;z-index: 99;"></div>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm deleteparent">

                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="11" style="text-align:right">
                                        Total Price
                                    </th>
                                    <td colspan="2">KES <span id="total_exclusive">0.00</span></td>
                                </tr>
                                <tr>
                                    <th colspan="11" style="text-align:right">
                                        Discount
                                    </th>
                                    <td colspan="2">KES <span id="total_discount">0.00</span></td>
                                </tr>
                                <tr>
                                    <th colspan="11" style="text-align:right">
                                        Total VAT
                                    </th>
                                    <td colspan="2">KES <span id="total_vat">0.00</span></td>
                                </tr>
                                <tr>
                                    <th colspan="11" style="text-align:right">
                                        Total
                                    </th>
                                    <td colspan="2">KES <span id="total_total">0.00</span></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="box-body">
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary btn-sn" id="continuePayment" data-toggle="modal"
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
                                        <span  id="error-amount[{{$method->id}}]" class="error-message text-danger" style="display:none;">Please Enter Amount</span>
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
                            <img src="{{ asset('images/mpesa.png') }}" alt="mpesa" class="payment-logo">
                            <img src="{{ asset('images/equity.png') }}" alt="equity" class="payment-logo">
                            <img src="{{ asset('images/vooma.png') }}" alt="vooma" class="payment-logo">
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
{{--                            <li role="presentation"><a href="#inactive" aria-controls="inactive" role="tab" data-toggle="tab">Inactive</a></li>--}}
{{--                            <li role="presentation"><a href="#utilized" aria-controls="utilized" role="tab" data-toggle="tab">Utilized</a></li>--}}
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
                                        <th>Method</th>
                                        <th>Customer</th>
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

        .textData table tr:hover, .SelectedLi {
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

    @include('partials.shortcuts')

@endsection


