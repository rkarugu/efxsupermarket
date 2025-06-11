@extends('layouts.admin.admin')
@section('content')
    @if(Request::url() != URL::previous())
        <a href="{!! URL::previous() !!}" class="btn btn-primary">Back</a>
    @endif
    <form method="POST" action="{{route($model.'.update',$row->slug)}}" accept-charset="UTF-8"
          enctype="multipart/form-data" novalidate="novalidate" onsubmit="return false;">
        <section class="content">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
                @include('message')
                {{ csrf_field() }}
                {{method_field('PUT')}}
                <input type="hidden" name="id" value="{{$row->id}}">
                @if(isset($_GET['from']))
                    <input type="hidden" name="from" value="confirm">
                @endif
                <?php
                $requisition_no = $row->requisition_no;
                $default_branch_id = $row->restaurant_id;
                $default_department_id = $row->wa_department_id;
                $default_wa_location_and_store_id = null;
                $requisition_date = $row->requisition_date;


                ?>

                <div class="row">

                    <div class="col-sm-6">
                        <div class="row">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Invoice No.</label>
                                    <div class="col-sm-7">


                                        {!! Form::text('requisition_no',  $requisition_no , ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Employee name</label>
                                    <div class="col-sm-7">
                                        {!! Form::text('emp_name', $getLoggeduserProfile->name, ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Invoice Date</label>
                                    <div class="col-sm-7">
                                        {!! Form::text('requisition_date', $requisition_date, ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Credit Limit</label>
                                    <div class="col-sm-7">
                                        <span id="credit_limit"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Current A/C Balance</label>
                                    <div class="col-sm-7">
                                        <span id="used_balance"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Available Limit</label>
                                    <div class="col-sm-7">
                                        <span id="ac_balance"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                    <div class="col-sm-6">
                        <div class="row">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Branch</label>
                                    <div class="col-sm-6">
                                        {!!Form::select('restaurant_id', getBranchesDropdown(),$default_branch_id, ['class' => 'form-control ','required'=>true,'placeholder' => 'Please select branch','id'=>'branch','disabled'=>true  ])!!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Department</label>
                                    <div class="col-sm-6">
                                        {!!Form::select('wa_department_id',getDepartmentDropdown($getLoggeduserProfile->restaurant_id), $default_department_id, ['class' => 'form-control ','required'=>true,'placeholder' => 'Please select department','id'=>'department','disabled'=>true  ])!!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Salesman</label>
                                    <div class="col-sm-6">
                                        @php
                                            if($getLoggeduserProfile->role_id == 4){
                                              $loc = $getLoggeduserProfile->wa_location_and_store_id;
                                            }else {
                                              $loc = $row->to_store_id;
                                            }
                                        @endphp
                                        {!!Form::select('to_store_location_id',getStoreLocationDropdownByBranch_with_user($getLoggeduserProfile->restaurant_id), $loc, ['class' => 'form-control mlselec6t','id'=>'to_store_location_id' ,'placeholder'=>'Please select' ])!!}
                                        <span id="error_msg_to_store_location_id"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Name</label>
                                    <div class="col-sm-6">
                                        <input type="hidden" name="customer" id="customer" value="{{ $row->customer_id }}">
                                        {!! Form::text('name', $row->name, ['maxlength'=>'255','placeholder' => 'Name','id'=>'name', 'required'=>true, 'class'=>'form-control', 'readonly']) !!}
                                        <span id="error_msg_customer_name"></span>


                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>


                </div>


            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Small boxes (Stat box) -->
            <div class="box box-primary">
                <div class="box-header with-border no-padding-h-b">


                    <div class="col-md-12 no-padding-h">
                        <h3 class="box-title"> Invoice Line</h3>

                        <div id="requisitionitemtable">
                            <button type="button" class="btn btn-danger btn-sm addNewrow"
                                    style="position: fixed;bottom: 30%;left:4%;"><i class="fa fa-plus"
                                                                                    aria-hidden="true"></i></button>

                            <table class="table table-bordered table-hover" id="mainItemTable">
                                <thead>
                                <tr>
                                    <th>Selection</th>
                                    <th>Description</th>
                                    <th style="width: 90px;">QOH</th>
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
                                    $total_exclusive = $total_discount = $total_vat = $total_total = 0;
                                @endphp
                                @foreach ($row->getRelatedItem as $item)
                                    <tr>
                                        <td>
                                            <input type="hidden" name="item_id[{{@$item->getInventoryItemDetail->id}}]"
                                                   class="itemid" value="{{@$item->getInventoryItemDetail->id}}">
                                            <input style="padding: 3px 3px;" type="text" class="testIn form-control"
                                                   value="{{@$item->getInventoryItemDetail->stock_id_code}}">
                                            <div class="textData"
                                                 style="width: 100%;position: relative;z-index: 99;"></div>
                                        </td>
                                        <td><input style="padding: 3px 3px;" readonly="" type="text"
                                                   name="item_description[{{@$item->getInventoryItemDetail->id}}]"
                                                   data-id="{{@$item->getInventoryItemDetail->id}}" class="form-control"
                                                   value="{{@$item->getInventoryItemDetail->title}}"></td>
                                        <td>{{@$item->getInventoryItemDetail->getAllFromStockMoves->sum('qauntity')}}</td>
                                        <td><input style="padding: 3px 3px;" readonly="" type="text"
                                                   name="item_unit[{{@$item->getInventoryItemDetail->id}}]"
                                                   data-id="{{@$item->getInventoryItemDetail->id}}" class="form-control"
                                                   value="{{@$item->getInventoryItemDetail->pack_size->title}}"></td>
                                        <td><input style="padding: 3px 3px;" onkeyup="getTotal(this)"
                                                   onchange="getTotal(this)" type="text"
                                                   name="item_quantity[{{@$item->getInventoryItemDetail->id}}]"
                                                   data-id="{{@$item->getInventoryItemDetail->id}}"
                                                   class="quantity form-control" value="{{$item->quantity}}"></td>
                                        <td><input style="padding: 3px 3px;"
                                                   {{$editPermission}} onchange="getTotal(this)"
                                                   onkeyup="getTotal(this)" type="text"
                                                   name="item_selling_price[{{@$item->getInventoryItemDetail->id}}]"
                                                   data-id="{{@$item->getInventoryItemDetail->id}}"
                                                   class="send_me_to_next_item selling_price form-control"
                                                   value="{{@$item->getInventoryItemDetail->selling_price}}" readonly></td>
                                        <td><input type="hidden" class="vat_percentage" value="{{$item->vat_rate}}"
                                                   name="item_vat_percentage[{{@$item->getInventoryItemDetail->id}}]">
                                            <input type="hidden" class=""
                                                   name="item_vat[{{@$item->getInventoryItemDetail->id}}]"
                                                   value="{{$item->vat_rate}}">
                                            <span class="form-control">{{$item->vat_rate}}</span>
                                        </td>
                                        <td><input style="padding: 3px 3px;" readonly="" onchange="getTotal(this)"
                                                   onkeyup="getTotal(this)" type="text"
                                                   name="item_discount_per[{{@$item->getInventoryItemDetail->id}}]"
                                                   data-id="{{@$item->getInventoryItemDetail->id}}"
                                                   class="discount_per form-control" value="0.00"></td>
                                        <td><input style="padding: 3px 3px;" readonly="" type="text"
                                                   name="item_discount[{{@$item->getInventoryItemDetail->id}}]"
                                                   data-id="{{@$item->getInventoryItemDetail->id}}"
                                                   class="discount form-control" value="0.00"></td>

                                        <td><span class="vat">{{$item->vat_amount}}</span></td>
                                        <td><span class="total">{{$item->total_cost_with_vat}}</span></td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm deleteparent"><i
                                                        class="fa fa-trash" aria-hidden="true"></i></button>
                                        </td>
                                    </tr>
                                    @php
                                        $total_exclusive += $item->total_cost_with_vat - $item->vat_amount;
                                        $total_vat += $item->vat_amount;
                                        $total_total += $item->total_cost_with_vat;
                                    @endphp
                                @endforeach


                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="11" style="text-align:right">
                                        Total Price
                                    </th>
                                    <td colspan="2">KES <span id="total_exclusive">{{$total_exclusive}}</span></td>
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
                                    <td colspan="2">KES <span id="total_vat">{{$total_vat}}</span></td>
                                </tr>
                                <tr>
                                    <th colspan="11" style="text-align:right">
                                        Total
                                    </th>
                                    <td colspan="2">KES <span id="total_total">{{$total_total}}</span></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>


                    <div class="col-md-12">
                        <div class="col-md-6 request_type"><span>
                             
                                  <button type="submit" class="btn btn-success btn-lg addExpense"
                                          value="save">Save</button>
                                  <button type="submit" class="btn btn-success btn-lg addExpense" value="send_request">Send Request</button>
                         
                              </span></div>
                        <div class="col-md-3"></div>
                        <div class="col-md-3"></div>
                    </div>


                </div>
            </div>


        </section>
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
    <script type="text/javascript">
        var form = new Form();

        function getcustomercredit() {
            var customer = $(document).find('#customer').val();
            if (customer == '' || customer == 'Customer') {
                return false;
            }
            $.ajax({
                type: "GET",
                url: "{{route('sales-invoice.getcustomercredit')}}",
                data: {
                    'id': customer
                },
                success: function (response) {
                    if (response.result) {
                        if (response.result === -1) {
                            form.errorMessage(response.message);
                            $('#credit_limit').html('').removeClass('form-control');
                            $('#ac_balance').html('').removeClass('form-control');
                            $('#used_balance').html('').removeClass('form-control');
                        } else {
                            $('#credit_limit').html(response.credit_limit).addClass('form-control');
                            $('#ac_balance').html(response.balance).addClass('form-control');
                            $('#used_balance').html(response.used_balance).addClass('form-control');
                        }
                    }
                }
            });
        }



        @if($loc)
        $(document).ready(function () {
            var myval = $('#to_store_location_id').val();
            getcustomercredit();
        });
        @endif
        $(document).ready(function () {
            $('#to_store_location_id').change(function (e) {
                e.preventDefault();
                var myval = $(this).val();
                getcustomercredit();
            });
            $('body').addClass('sidebar-collapse');
            // payment_method();
        });
        $(function () {
            $(".mlselec6t").select2();
        });
        $(function () {
            $(".mlselec6t_modal").select2({dropdownParent: $('.modal')});
        });


    </script>
    <script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>


    <script>

        $(document).on('keypress', ".start_process", function (event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                $(".processIt").click();
            }
        });

        function makemefocus() {
            if ($(".makemefocus")[0]) {
                $(".makemefocus")[0].focus();
            }
        }

        $(document).on('keypress change', '.send_me_to_next_item', function (event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                makemefocus();
            }
        });
        $(document).on('click', '.addExpense', function (e) {
            e.preventDefault();
            $('#loader-on').show();
            var postData = new FormData($(this).parents('form')[0]);
            var url = $(this).parents('form').attr('action');
            postData.append('_token', $(document).find('input[name="_token"]').val());
            postData.append('request_type', $(this).val());
            $.ajax({
                url: url,
                data: postData,
                contentType: false,
                cache: false,
                processData: false,
                method: 'POST',
                success: function (out) {
                    $('#loader-on').hide();

                    $(".remove_error").remove();
                    if (out.result == 0) {
                        for (let i in out.errors) {
                            var id = i.split(".");
                            if (id && id[1]) {
                                $("[name='" + id[0] + "[" + id[1] + "]']").parent().append('<label class="error d-block remove_error w-100" id="' + i + '_error">' + out.errors[i][0] + '</label>');
                            } else {
                                $("[name='" + i + "']").parent().append('<label class="error d-block remove_error w-100" id="' + i + '_error">' + out.errors[i][0] + '</label>');
                                $("." + i).parent().append('<label class="error d-block remove_error w-100" id="' + i + '_error">' + out.errors[i][0] + '</label>');
                            }
                        }
                    }
                    if (out.result === 1) {
                        form.successMessage(out.message);
                        if (out.location) {
                            setTimeout(() => {
                                location.href = out.location;
                            }, 1000);
                        }
                    }
                    if (out.result === -1) {
                        form.errorMessage(out.message);
                    }
                },

                error: function (err) {
                    $('#loader-on').hide();
                    $(".remove_error").remove();
                    form.errorMessage('Something went wrong');
                }
            });
        });
        $(document).on('keypress', ".quantity", function (event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                $(".addNewrow").click();
            }
        });

        var valueTest = null;
        $(document).on('keyup keypress click', '.testIn', function (e) {
            var vale = $(this).val();
            $(this).parent().find(".textData").show();
            var objCurrentLi, obj = $(this).parent().find(".textData tbody tr.SelectedLi"),
                objUl = $(this).parent().find('.textData tbody'),
                code = (e.keyCode ? e.keyCode : e.which);
            console.log(code);
            if (code == 40) { //Up Arrow

                //if object not available or at the last tr item this will roll that back to first tr item
                if ((obj.length === 0) || (objUl.find('tr:last').hasClass('SelectedLi') === true)) {
                    objCurrentLi = objUl.find('tr:first').addClass('SelectedLi').addClass('industryli');
                }
                //This will add class to next tr item
                else {
                    objCurrentLi = obj.next().addClass('SelectedLi').addClass('industryli');
                }

                //this will remove the class from current item
                obj.removeClass('SelectedLi');

                var listItem = $(this).parent().find('.SelectedLi.industryli');
                var selectedLi = $(this).parent().find(".textData tbody tr").index(listItem);

                var len = $(this).parent().find('.textData tbody tr').length;


                if (selectedLi > 1) {
                    var scroll = selectedLi + 1;
                    $(this).parent().find('.textData table').scrollTop($(this).parent().find('.textData table').scrollTop() + obj.next().height());
                }
                if (selectedLi == 0) {
                    $(this).parent().find('.textData table').scrollTop($(this).parent().find('.textData table tr:first').position().top);
                }

                return false;
            } else if (code == 38) {//Down Arrow
                if ((obj.length === 0) || (objUl.find('tr:first').hasClass('SelectedLi') === true)) {
                    objCurrentLi = objUl.find('tr:last').addClass('SelectedLi').addClass('industryli');
                } else {
                    objCurrentLi = obj.prev().addClass('SelectedLi').addClass('industryli');
                }
                obj.removeClass('SelectedLi');

                var listItem = $(this).parent().find('.SelectedLi.industryli');
                var selectedLi = $(this).parent().find(".textData tbody tr").index(listItem);

                var len = $(this).parent().find('.textData tbody tr').length;


                if (selectedLi > 1) {
                    var scroll = selectedLi - 1;
                    $(this).parent().find('.textData table').scrollTop(
                        $(this).parent().find('.textData table tr:nth-child(' + scroll + ')').position().top -
                        $(this).parent().find('.textData table tr:first').position().top);
                }
                return false;
            } else if (code == 13) {
                obj.click();
                return false;
            } else if (valueTest != vale && (e.type == 'keyup' || e.type == 'click') && code != 13 && code != 38 && code != 40 && vale != '') {
                var $this = $(this);

                if (vale.length >= 3) {
                    $.ajax({
                        type: "GET",
                        url: "{{route('sales-invoice.search')}}",
                        data: {
                            'search': vale,
                            'store_location_id': {{ getLoggeduserProfile()->wa_location_and_store_id }},
                        },
                        success: function (response) {
                            $this.parent().find('.textData').html(response);
                        }
                    });
                    valueTest = vale;
                }

                return true;
            }

        });


        $(document).click(function (e) {
            var container = $(".textData");
            // if the target of the click isn't the container nor a descendant of the container
            if (!container.is(e.target) && container.has(e.target).length === 0) {
                container.hide();
            }
        });

        function fetchInventoryDetails(varia) {
            var $this = $(varia);
            var itemids = $('.itemid');
            var furtherCall = true;
            $.each(itemids, function (indexInArray, valueOfElement) {
                if ($this.data('id') == $(valueOfElement).val()) {
                    form.errorMessage('This Item is already added in list');
                    furtherCall = false;
                    return true;
                }
            });
            if (furtherCall == true) {
                $.ajax({
                    type: "GET",
                    url: "{{route('sales-invoice.getInventryItemDetails')}}",
                    data: {
                        'id': $this.data('id')
                    },
                    success: function (response) {
                        if (response.result) {
                            form.errorMessage(response.message);
                            return true;
                        }
                        $(".vat_list").select2('destroy');
                        $this.parents('tr').replaceWith(response);
                        vat_list();
                        totalofAllTotal();
                    }
                });
            }
        }

        $(document).on('click', '.deleteparent', function () {
            $(this).parents('tr').remove();
            totalofAllTotal()
        });
        $(document).on('click', '.addNewrow', function () {
            $('#mainItemTable tbody').prepend('<tr><td><input type="text" class="testIn form-control makemefocus"><div class="textData" style="width: 100%;position: relative;z-index: 99;"></div></td>'
                + '<td></td>'
                + '<td></td>'
                + '<td></td>'
                + '<td></td>'
                + '<td></td>'
                + '<td></td>'
                + '<td></td>'
                + '<td></td>'
                + '<td></td>'
                + '<td></td>'
                + '<td></td>'
                + '<td><button class="btn btn-danger btn-sm deleteparent"><i class="fa fa-trash" aria-hidden="true"></i></button></td>'
                + '</tr>');
            makemefocus();
        });

        var vat_list = function () {
            $(".vat_list").select2(
                {
                    placeholder: 'Select Vat',
                    ajax: {
                        url: '{{route("expense.vat_list")}}',
                        dataType: 'json',
                        type: "GET",
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term
                            };
                        },
                        processResults: function (data) {
                            var res = data.map(function (item) {
                                return {id: item.id, text: item.text};
                            });
                            return {
                                results: res
                            };
                        }
                    },
                });
        };
        $(document).on('change', '.vat_list', function () {
            var vat = $(this).val();
            var $this = $(this);
            $.ajax({
                type: "GET",
                url: "{{route('expense.vat_find')}}",
                data: {
                    'id': vat
                },
                success: function (response) {
                    $this.parents('tr').find('.vat_percentage').val(response.tax_value);
                    getTotal($this);
                }
            });

        });

        function getTotal(vara) {
            var price = $(vara).parents('tr').find('.selling_price').val();
            if (price < 0) {
                $(vara).parents('tr').find('.selling_price').val(0);
                price = 0;
            }
            var quantity = $(vara).parents('tr').find('.quantity').val();
            if (quantity <= 0) {
                $(vara).parents('tr').find('.quantity').val('');
                quantity = 0;
            }
            var discount_per = $(vara).parents('tr').find('.discount_per').val();
            if (discount_per < 0) {
                $(vara).parents('tr').find('.discount_per').val(0);
                discount_per = 0;
            }
            var vat_percentage = $(vara).parents('tr').find('.vat_percentage').val();
            if (vat_percentage < 0) {
                $(vara).parents('tr').find('.vat_percentage').val(0);
                vat_percentage = 0;
            }
            var discount = ((parseFloat(price) * parseFloat(quantity)) * parseFloat(discount_per)) / 100;
            var exclusive = ((parseFloat(price) * parseFloat(quantity)) - parseFloat(discount));
            var vat = parseFloat(exclusive) - parseFloat((parseFloat(exclusive) * 100) / (parseFloat(vat_percentage) + 100));
            var total = parseFloat(exclusive);
            $(vara).parents('tr').find('.discount').val((discount).toFixed(2));
            $(vara).parents('tr').find('.vat').html((vat).toFixed(2));
            $(vara).parents('tr').find('.total').html((total).toFixed(2));

            totalofAllTotal();
        }

        $(document).on('keyup', '.discount', function (e) {
            var discount = $(this).val();
            if (discount < 0) {
                $(this).parents('tr').find('.discount').val(0);
                discount = 0;
            }
            var price = $(this).parents('tr').find('.selling_price').val();
            if (price < 0) {
                $(this).parents('tr').find('.selling_price').val(0);
                price = 0;
            }
            var quantity = $(this).parents('tr').find('.quantity').val();
            if (quantity <= 0) {
                $(this).parents('tr').find('.quantity').val('');
                quantity = 0;
            }
            var vat_percentage = $(this).parents('tr').find('.vat_percentage').val();
            if (vat_percentage < 0) {
                $(this).parents('tr').find('.vat_percentage').val(0);
                vat_percentage = 0;
            }
            var discount_per = (discount / parseFloat(price) * parseFloat(quantity)) * 100;
            var exclusive = ((parseFloat(price) * parseFloat(quantity)) - parseFloat(discount));
            var vat = parseFloat(exclusive) - parseFloat((parseFloat(exclusive) * 100) / (parseFloat(vat_percentage) + 100));
            var total = parseFloat(exclusive);
            $(this).parents('tr').find('.discount_per').val((discount_per).toFixed(2));
            $(this).parents('tr').find('.vat').html((vat).toFixed(2));
            $(this).parents('tr').find('.total').html((total).toFixed(2));
            totalofAllTotal();
        });

        function totalofAllTotal() {
            var alld = $(document).find('.discount');
            var allv = $(document).find('.vat');
            var allt = $(document).find('.total');
            var alle = $(document).find('.selling_price');
            var exclusive = 0;
            var vat = 0;
            var total = 0;
            var discount = 0;
            $.each(alld, function (indexInArray, valueOfElement) {
                discount = parseFloat(exclusive) + parseFloat($(valueOfElement).val());
            });
            $.each(alle, function (indexInArray, valueOfElement) {
                exclusive = parseFloat(exclusive) + parseFloat($(valueOfElement).val());
            });
            $.each(allv, function (indexInArray, valueOfElement) {
                vat = parseFloat(vat) + parseFloat($(valueOfElement).text());
            });
            $.each(allt, function (indexInArray, valueOfElement) {
                total = parseFloat(total) + parseFloat($(valueOfElement).text());
            });
            $('#total_vat').html((vat).toFixed(2));
            $('#total_total').html((total).toFixed(2));
            $('#total_exclusive').html((parseFloat(total) - parseFloat(vat)).toFixed(2));
            $('#total_discount').html((discount).toFixed(2));

        }


        var payment_method = function () {
            $("#payment_method").select2(
                {
                    placeholder: 'Select Payment Method',
                    ajax: {
                        url: '{{route("expense.payment_method")}}',
                        dataType: 'json',
                        type: "GET",
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term
                            };
                        },
                        processResults: function (data) {
                            var res = data.map(function (item) {
                                return {id: item.id, text: item.text};
                            });
                            return {
                                results: res
                            };
                        }
                    },
                });
        };

        $(document).on('change', '.quantity', function (e) {
            var qty = $(this).val();
            var inputName = $(this).attr('name');
            var item_id = inputName.substring(inputName.indexOf("[") + 1, inputName.indexOf("]"));
            var counts = $(this).data('counts');
            var $inputElement = $(this);
            $inputElement.next('.error-message').remove();

            console.log('count == ' + counts);
            console.log('qty is decimal ' + isDecimal(qty));

            // Validate if the quantity is a decimal
            if (isDecimal(qty)) {
                if (counts !== null && counts !== '') {
                    console.log('has count');
                    if (!checkSplit(qty)) {
                        $inputElement.after('<span class="error-message" style="color: red;">Item cannot be split into given quantity</span>');
                    }
                } else {
                    console.log('No count');
                    $inputElement.after('<span class="error-message" style="color: red;">Item cannot be sold into halves</span>');
                }
            }

            // AJAX call to calculate the discount
            $.ajax({
                type: "GET",
                url: "{{route('pos-cash-sales.cal_discount')}}",
                data: {
                    'item_id': item_id,
                    'item_quantity': qty
                },
                success: function (response) {
                    var discountName = 'item_discount[' + response.item_id + ']';
                    var discId = $('input[name="' + discountName + '"]');
                    // Update the discount field and trigger keyup for any further updates
                    $(discId).val(response.discount).attr('value', response.discount).trigger('keyup');
                    vat_list();
                    totalofAllTotal();
                }
            });
            totalofAllTotal();
        });
        /* New js start here  */
        function isDecimal(num) {
            return num % 1 !== 0;
        }
        function checkSplit(number) {
            // Get the decimal part of the number using modulo operation
            var decimalPart = number % 1;

            // Check if the decimal part is one of the allowed values
            return [0.25, 0.5, 0.75].includes(decimalPart);
        }

        $(document).on('submit', '.addSubCustomer', function (e) {
            e.preventDefault();
            $('#loader-on').show();
            var postData = new FormData($(this)[0]);
            var url = $(this).attr('action');
            postData.append('_token', $(document).find('input[name="_token"]').val());
            $.ajax({
                url: url,
                data: postData,
                contentType: false,
                cache: false,
                processData: false,
                method: 'POST',
                success: function (out) {
                    $('#loader-on').hide();

                    $(".remove_error").remove();
                    if (out.result == 0) {
                        for (let i in out.errors) {
                            var id = i.split(".");
                            if (id && id[1]) {
                                $("[name='" + id[0] + "[" + id[1] + "]']").parent().append('<label class="error d-block remove_error w-100" id="' + i + '_error">' + out.errors[i][0] + '</label>');
                            } else {
                                $("[name='" + i + "']").parent().append('<label class="error d-block remove_error w-100" id="' + i + '_error">' + out.errors[i][0] + '</label>');
                                $("." + i).parent().append('<label class="error d-block remove_error w-100" id="' + i + '_error">' + out.errors[i][0] + '</label>');
                            }
                        }
                    }
                    if (out.result === 1) {
                        form.successMessage(out.message);
                        if (out.data) {
                            $('#subCustomers').append('<option value="' + out.data.id + '" selected>' + out.data.name + ' : ' + out.data.bussiness_name + ' : ' + out.data.phone + ' </option>');
                            $('#modelId').modal('hide');
                        }
                    }
                    if (out.result === -1) {
                        form.errorMessage(out.message);
                    }
                },

                error: function (err) {
                    $('#loader-on').hide();
                    $(".remove_error").remove();
                    form.errorMessage('Something went wrong');
                }
            });
        });


        var subCustomers = function () {
            $("#subCustomers").select2(
                {
                    placeholder: 'Select Customer',
                    ajax: {
                        url: '{{route("maintain-customers.route_customer_dropdown")}}',
                        dataType: 'json',
                        type: "GET",
                        delay: 250,
                        data: function (params) {
                            return {
                                search: params.term,
                                'role_id': {{$getLoggeduserProfile->role_id}},
                                'route': $('#route_id').val()
                            };
                        },
                        processResults: function (data) {
                            var res = data.map(function (item) {
                                return {id: item.id, text: item.text};
                            });
                            return {
                                results: res
                            };
                        }
                    },
                });
        };
        subCustomers();

        $(document).ready(function () {
            $('#phone_no').on('keyup', function () {
                console.log("abhisheksoneir");
                var input = $(this).val().replace(/\D/g, ''); // remove non-digit characters
                input = input.substring(0, 10); // limit to 10 digits
                input = input.replace(/^(\d{4})(\d{3})(\d{3})$/, '$1 $2 $3'); // add spaces
                $(this).val(input);
            });
        });


    </script>
@endsection


