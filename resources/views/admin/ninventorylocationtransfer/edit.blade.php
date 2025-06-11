@extends('layouts.admin.admin')
@section('content')
    <form method="POST" action="{{ route($model . '.update', $transfer->slug) }}" accept-charset="UTF-8"
        class="submitMe addExpense" enctype="multipart/form-data" novalidate="novalidate" id="transferForm">
        @csrf
        @method('put')
        <input type="hidden" name="action" id="action" value="save">
        <section class="content">
            <div class="box box-primary">
                @include('message')
                <div class="box-header with-border">
                    <div class="box-header-flex">
                        <h3 class="box-title"> Edit Transfers </h3>
                        <a href="{{ route('n-transfers.index') }}" class="btn btn-success btn-sm">Back</a>
                    </div>
                </div>
                <div class="box-body">

                    <div class = "row">
                        <div class = "col-sm-6">
                            <div class="form-group">
                                <div class="row">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Transfer No.</label>
                                    <div class="col-sm-7">
                                        {!! Form::text('transfer_no', $transfer->transfer_no, [
                                            'maxlength' => '255',
                                            'placeholder' => '',
                                            'required' => true,
                                            'class' => 'form-control',
                                            'readonly' => true,
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Employee name</label>
                                    <div class="col-sm-7">
                                        {!! Form::text('emp_name', $transfer->getrelatedEmployee->name, [
                                            'maxlength' => '255',
                                            'placeholder' => '',
                                            'required' => true,
                                            'class' => 'form-control',
                                            'readonly' => true,
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Transfer Date</label>
                                    <div class="col-sm-7">
                                        {!! Form::date('transfer_date', $transfer->transfer_date, [
                                            'maxlength' => '255',
                                            'placeholder' => '',
                                            'required' => true,
                                            'class' => 'form-control',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Manual Document No.</label>
                                    <div class="col-sm-7">
                                        {!! Form::text('manual_doc_number', $transfer->manual_doc_number, [
                                            'maxlength' => '255',
                                            'placeholder' => '',
                                            'required' => false,
                                            'class' => 'form-control',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class = "col-sm-6">
                            <div class="form-group">
                                <div class="row">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Branch</label>
                                    <div class="col-sm-7">
                                        {!! Form::select('restaurant_id', getBranchesDropdown(), $transfer->restaurant_id, [
                                            'class' => 'form-control ',
                                            'required' => true,
                                            'placeholder' => 'Please select branch',
                                            'id' => 'branch',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Department</label>
                                    <div class="col-sm-7">
                                        {!! Form::select('wa_department_id', $department, $transfer->getrelatedEmployee->wa_department_id, [
                                            'class' => 'form-control ',
                                            'required' => true,
                                            'placeholder' => 'Please select department',
                                            'id' => 'department',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                            @php
                                $allToStore = [];
                                $tostore = \DB::table('wa_location_and_stores')->get();
                                $fromstore = $tostore->whereIn('id', [46, 37, 38]);


                            @endphp
                            
                            @foreach ($tostore as $t)
                            @php
                                $allToStore[$t->id] = $t->location_name . ' ( ' . $t->location_code . ')';
                            @endphp
                            @endforeach
                            @foreach ($fromstore as $t)
                                @php
                                    $allFromStore[$t->id] = $t->location_name . ' ( ' . $t->location_code . ')';
                                @endphp
                        @endforeach
                            <div class="form-group">
                                <div class="row">
                                    <label for="inputEmail3" class="col-sm-5 control-label">From Store</label>
                                    <div class="col-sm-7">
                                        {!! Form::select('from_strore_location_id', $allFromStore, $transfer->from_store_location_id, [
                                            'class' => 'form-control mlselec6t',
                                            'id' => 'from_strore_location_id',
                                            'placeholder' => 'Please select',
                                        ]) !!}
                                        <span id = "error_msg_from_strore_location_id"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label for="inputEmail3" class="col-sm-5 control-label">To Store</label>
                                    <div class="col-sm-7">
                                        {!! Form::select('to_store_location_id', $allToStore, $transfer->to_store_location_id, [
                                            'class' => 'form-control mlselec6t',
                                            'id' => 'to_store_location_id',
                                            'placeholder' => 'Please select',

                                        ]) !!}
                                        <span id = "error_msg_to_store_location_id"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header with-border no-padding-h-b">
                    <h3 class="box-title"> Transfer Line</h3>
                </div>
                <div class="box-body">
                    <button type="button" class="btn btn-danger btn-sm addNewrow"
                        style="position: fixed;bottom: 30%;left:4%;"><i class="fa fa-plus" aria-hidden="true"></i></button>

                    <span id = "requisitionitemtable">
                        <table class="table table-bordered table-hover" id="mainItemTable">
                            <thead>
                                <tr>
                                    <th style="width: 8%">Item</th>
                                    <th style="width: 16%">Description</th>
                                    <th style="width: 16%">Bin</th>
                                    <th style="width: 8%">UOM</th>
                                    <th>Qty</th>
                                    <th>QOO</th>
                                    <th>SOH</th>
                                    <th>Max Stock</th>
                                    <th>Cost</th>
                                    <th style="width: 10%">Total Cost</th>
                                    <th style="width: 10%">Note</th>
                                    <th style="width: 5%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transfer->items as $item)
                                    {!! $item !!}
                                @empty
                                    <tr>
                                        <td>
                                            <input type="text" class="testIn form-control makemefocus"
                                                name="item_id['0']">
                                            <div class="textData" style="width: 100%;position: relative;z-index: 99;">
                                            </div>
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
                                        <td><button type="button" class="btn btn-danger btn-sm deleteparent"
                                                style="background-color: transparent !important; border:none; color:red !important;"><i
                                                    class="fas fa-trash" style="color:red;" aria-hidden="true"></i></button>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">Tonnage:</th>
                                    <th id="tonnage" class="text-right">0.00</th>
                                    <th colspan="7"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </span>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary btn-sm" id="save-btn">
                        <i class="fa fa-save"></i>
                        Save</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="process-btn">
                        <i class="fa fa-check-circle"></i>
                        Process</button>
                </div>
            </div>
        </section>
    </form>
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog"
        aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="confirmationModalLabel">Confirm Action</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to proceed with the transfer?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmSubmit">Confirm</button>
                </div>
            </div>
        </div>
    </div>
    <div id="addRequisitionItemModel" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Item To Transfer</h4>
                </div>
                <form class="validate" role="form" method="POST" action="{{ route($model . '.store') }}"
                    enctype = "multipart/form-data" id="additemformoncreate">
                    <div class="modal-body">
                        {{ csrf_field() }}
                        {!! Form::hidden('transfer_no', $transfer_no, []) !!}
                        {!! Form::hidden('transfer_date', $transfer_date, []) !!}
                        {!! Form::hidden('restaurant_id', $user->restaurant_id, ['id' => 'restaurant_id']) !!}
                        {!! Form::hidden('wa_department_id', $user->wa_department_id, ['id' => 'wa_department_id']) !!}
                        {!! Form::hidden('to_store_location_id', null, ['id' => 'to_store_location_id_hidden']) !!}
                        {!! Form::hidden('from_store_location_id', null, ['id' => 'from_store_location_id_hidden']) !!}
                        {!! Form::select('wa_inventory_category_id', getInventoryCategoryList(), null, [
                            'maxlength' => '255',
                            'placeholder' => 'Please select category',
                            'required' => true,
                            'class' => 'form-control mlselec6t',
                            'id' => 'inventory_category',
                        ]) !!}
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Item Name</label>
                                <div class="col-sm-9">
                                    {!! Form::select('wa_inventory_item_id', inventoryUItemDropDown(), null, [
                                        'maxlength' => '255',
                                        'placeholder' => 'Please select item',
                                        'required' => true,
                                        'class' => 'form-control mlselec6t',
                                        'id' => 'item',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Item No</label>
                                <div class="col-sm-9">

                                    {!! Form::text('item_no', null, [
                                        'maxlength' => '255',
                                        'class' => 'form-control',
                                        'id' => 'item_no',
                                        'readonly' => true,
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Unit Of Measure</label>
                                <div class="col-sm-9">
                                    {!! Form::select('unit_of_measure', getUnitOfMeasureList(), null, [
                                        'maxlength' => '255',
                                        'placeholder' => '',
                                        'class' => 'form-control ',
                                        'id' => 'unit_of_measure',
                                        'disabled' => true,
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Quantity</label>
                                <div class="col-sm-9">
                                    {!! Form::number('quantity', null, [
                                        'min' => '0',
                                        'required' => true,
                                        'class' => 'form-control',
                                        'id' => 'quantity',
                                    ]) !!}
                                    <div class="error_qty" style="color: red;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Note</label>
                                <div class="col-sm-9">
                                    {!! Form::textarea('note', null, ['maxlength' => '1000', 'class' => 'form-control', 'id' => 'note']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label"></label>
                                <div class="col-sm-9">
                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-primary"
                                            class="addItemByCreate">Add</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">

    <style type="text/css">
        .select2 {
            width: 100% !important;
        }

        #note {
            height: 80px !important;
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
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script type="text/javascript">
        $('body').addClass('sidebar-collapse');

        $(document).ready(function() {
            $('.quantity').each(function(index, item) {
                getTotal(item);
            })
        });

        $(function() {
            $(".mlselec6t").select2();
        });

        $(document).on('keyup', '.testIn', function(e) {
            var vale = $(this).val();
            $(this).parent().find(".textData").show();
            var $this = $(this);
            var from_strore_location_id = $('#from_strore_location_id').val();
            if (!from_strore_location_id) {
                $('#error_msg_from_strore_location_id').html('');
                $("#error_msg_from_strore_location_id").append(
                    '<span class="error_msg" style="color:red;font-weight:700;">This field is required.</span>');
                return false;
            }
            $.ajax({
                type: "GET",
                url: "{{ route('purchase-orders.inventoryItemsTransfers') }}",
                data: {
                    'search': vale,
                    'store_location_id': from_strore_location_id
                },
                success: function(response) {
                    $this.parent().find('.textData').html(response);
                }
            });
        });

        $(document).on('keypress', ".quantity", function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                $(".addNewrow").click();
            }
        });

        function makemefocus() {
            if ($(".makemefocus")[0]) {
                $(".makemefocus")[0].focus();
            }
        }

        $(document).on('click', '.deleteparent', function() {
            $(this).parents('tr').remove();
            totalofAllTotal()
        });

        function fetchInventoryDetails(varia) {
            var $this = $(varia);
            if ($this.data('quantity') <= 0) {
                form.errorMessage('This item don\'t have enough quantity');
                return false;
            }
            var itemids = $('.itemid');
            var from_strore_location_id = $('#from_strore_location_id').val();
            if (!from_strore_location_id) {
                $('#error_msg_from_strore_location_id').html('');
                $("#error_msg_from_strore_location_id").append(
                    '<span class="error_msg" style="color:red;font-weight:700;">This field is required.</span>');
                return false;
            }
            var furtherCall = true;
            $.each(itemids, function(indexInArray, valueOfElement) {
                if ($this.data('id') == $(valueOfElement).val()) {
                    form.errorMessage('This Item is already added in list');
                    furtherCall = false;
                    return true;
                }
            });
            if (furtherCall == true) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('n-transfers.getInventryItemDetails') }}",
                    data: {
                        'id': $this.data('id'),
                        'location': from_strore_location_id,
                        // 'transfer': {!! $transfer->id !!}
                    },
                    success: function(response) {
                        $this.parents('tr').replaceWith(response);
                        totalofAllTotal();
                    }
                });
            }
        }

        $(document).on('click', '.addNewrow', function() {
            $('#mainItemTable tbody').append(
                '<tr><td><input type="text" class="testIn form-control makemefocus"><div class="textData" style="width: 100%;position: relative;z-index: 99;"></div></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>'

                +
                '<td><button class=" btn-sm deleteparent" style="background-color:transparent !important; border:none !important; color:red !important;"><i class="fas fa-trash"  style="color:red !important;" aria-hidden="true"></i></button></td>' +
                '</tr>');
            makemefocus();
        });

        $(document).click(function(e) {
            var container = $(".textData");
            // if the target of the click isn't the container nor a descendant of the container
            if (!container.is(e.target) && container.has(e.target).length === 0) {
                container.hide();
            }
        });

        function getTotal(vara) {
            var price = $(vara).parents('tr').find('.standard_cost').val();
            var quantity = $(vara).parents('tr').find('.quantity').val();
            if (quantity < 0 || quantity == '') {
                quantity = 0;
            }
            var vat_percentage = $(vara).parents('tr').find('.vat_percentage').val();

            var exclusive = ((parseFloat(price) * parseFloat(quantity)));
            var vat = (parseFloat(exclusive) * parseFloat(vat_percentage)) / 100;
            var total = parseFloat(parseFloat(exclusive) + parseFloat(vat));
            $(vara).parents('tr').find('.exclusive').html((exclusive).toFixed(2));
            $(vara).parents('tr').find('.vat').html((vat).toFixed(2));
            $(vara).parents('tr').find('.total').html((total).toFixed(2));


            totalofAllTotal();
        }

        function totalofAllTotal() {
            var alle = $(document).find('.exclusive');
            var allv = $(document).find('.vat');
            var allt = $(document).find('.total');
            var allq = $(document).find('.quantity.form-control');
            var exclusive = 0;
            var vat = 0;
            var total = 0;
            var tonnage = 0;

            $.each(alle, function(indexInArray, valueOfElement) {
                exclusive = parseFloat(exclusive) + parseFloat($(valueOfElement).text());
            });
            $.each(allv, function(indexInArray, valueOfElement) {
                vat = parseFloat(vat) + parseFloat($(valueOfElement).text());
            });
            $.each(allt, function(indexInArray, valueOfElement) {
                total = parseFloat(total) + parseFloat($(valueOfElement).text());
            });

            $.each(allq, function(indexInArray, valueOfElement) {
                tonnage += parseFloat($(valueOfElement).val()) * parseFloat($(valueOfElement).data('weight'));
            });


            $('#total_exclusive').html((exclusive).toFixed(2));
            $('#total_vat').html((vat).toFixed(2));
            $('#total_total').html((total).toFixed(2));
            $('#tonnage').html((tonnage).toFixed(2));
        }

        $('#addbuttonpu').click(function() {
            var to_store_location_id = $("#to_store_location_id").val();
            var from_strore_location_id = $("#from_strore_location_id").val();
            $(".error_msg").remove();
            if (!from_strore_location_id || !to_store_location_id) {
                if (!from_strore_location_id) {
                    $("#error_msg_from_strore_location_id").append(
                        '<span class="error_msg" style="color:red;font-weight:700;">This field is required.</span>'
                    );
                }
                if (!to_store_location_id) {

                    $("#error_msg_to_store_location_id").append(
                        '<span class="error_msg" style="color:red;font-weight:700;">This field is required.</span>'
                    );
                }
                return false;
            }

        });


        $("#to_store_location_id").change(function() {
            $("#to_store_location_id_hidden").val($(this).val());
            checkAddItemAvailavility();
        });

        $("#from_strore_location_id").change(function() {
            $("#from_store_location_id_hidden").val($(this).val());
            checkAddItemAvailavility();
        });

        $("#branch").change(function() {
            $("#restaurant_id").val($(this).val());
        });

        $("#department").change(function() {
            $("#wa_department_id").val($(this).val());
        });

        $("#inventory_category").change(function() {
            $("#item_no").val('');
            $("#unit_of_measure").val('');

            var selected_inventory_category = $("#inventory_category").val();
            manageitem(selected_inventory_category);

        });

        $("#item").change(function() {
            $("#item_no").val('');
            $("#unit_of_measure").val('');
            var selected_item_id = $("#item").val();

            getItemDetails(selected_item_id);
        });

        function getItemDetails(selected_item_id) {
            if (selected_item_id != "") {
                jQuery.ajax({
                    url: '{{ route('external-requisitions.items.detail') }}',
                    type: 'POST',
                    data: {
                        selected_item_id: selected_item_id
                    },
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {

                        var obj = jQuery.parseJSON(response);
                        $("#item_no").val(obj.stock_id_code);
                        $("#unit_of_measure").val(obj.unit_of_measure);
                    }
                });
            }

        }

        function manageitem(selected_inventory_category) {
            if (selected_inventory_category != "") {
                jQuery.ajax({
                    url: '{{ route('external-requisitions.items') }}',
                    type: 'POST',
                    data: {
                        selected_inventory_category: selected_inventory_category
                    },
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $("#item").val('');
                        $("#item").html(response);
                    }
                });
            } else {
                $("#item").val('');
                $("#item").html('<option selected="selected" value="">Please select item</option>');
            }
        }

        function checkAddItemAvailavility() {
            if ($("#from_strore_location_id").val() == $("#to_store_location_id").val()) {

                $("#addbuttonpu").css('display', 'none');
            } else {
                $("#addbuttonpu").css('display', '');
            }
        }

        $('#additemformoncreate').submit(function() {
            $(".error_qty").html('');
            if ($(this).valid()) {
                var myresponse = false;

                var item_id = $("#item_no").val();
                var quantity = $("#quantity").val();
                var from_strore_location_id = $("#from_strore_location_id").val();


                jQuery.ajax({
                    url: '{{ route('transfers.checkQuantity') }}',
                    type: 'POST',
                    data: {
                        item_id: item_id,
                        quantity: quantity,
                        from_strore_location_id: from_strore_location_id
                    },
                    async: false,
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response == '1') {

                            myresponse = true;
                        } else {
                            $(".error_qty").html('Invalid quantity');
                        }
                    }
                });

                if (myresponse == false) {
                    return false;
                }
            } else {
                return false;
            }
        });

        //submit form
        $('#process-btn').on('click', function(e) {
            e.preventDefault();
            $("#action").val('process')
            $('#confirmationModal').modal('show');
        });

        $('#save-btn').on('click', function(e) {
            e.preventDefault();
            $("#action").val('save')
            $('#confirmationModal').modal('show');
        });

        // $(document).on('submit', '.addExpense', function(e) {
        $('#confirmSubmit').on('click', function() {

            $('#confirmationModal').modal('hide');


            // e.preventDefault();
            $('button[type="submit"]').attr('disabled', true);
            document.getElementById("process-btn").disabled = true;

            $('#loader-on').show();
            // var postData = new FormData($(this)[0]);
            var postData = new FormData($('.addExpense')[0]);

            // var url = $(this).attr('action');
            var url = $('.addExpense').attr('action');

            postData.append('_token', $(document).find('input[name="_token"]').val());
            $.ajax({
                url: url,
                data: postData,
                contentType: false,
                cache: false,
                processData: false,
                method: 'POST',
                success: function(out) {
                    $('#loader-on').hide();

                    $(".remove_error").remove();
                    if (out.result == 0) {
                        for (let i in out.errors) {
                            var id = i.split(".");
                            if (id && id[1]) {
                                $("[name='" + id[0] + "[" + id[1] + "]']").parent().append(
                                    '<label class="error d-block remove_error w-100" id="' + i +
                                    '_error">' + out.errors[i][0] + '</label>');
                            } else {
                                $("[name='" + i + "']").parent().append(
                                    '<label class="error d-block remove_error w-100" id="' + i +
                                    '_error">' + out.errors[i][0] + '</label>');
                                $("." + i).parent().append(
                                    '<label class="error d-block remove_error w-100" id="' + i +
                                    '_error">' + out.errors[i][0] + '</label>');
                            }
                        }
                        $('button[type="submit"]').attr('disabled', false);
                        $('#loader-on').hide();

                    }
                    if (out.result === 1) {
                        form.successMessage(out.message);
                        if (out.location) {
                            setTimeout(() => {
                                location.href = out.location;
                            }, 1000);
                        } else {
                            $('button[type="submit"]').attr('disabled', false);
                            document.getElementById("process-btn").disabled = true;

                            $('#loader-on').hide();
                        }
                    }
                    if (out.result === -1) {
                        form.errorMessage(out.message);
                        $('button[type="submit"]').attr('disabled', false);
                        $('#loader-on').hide();
                    }
                },

                error: function(err) {
                    $('#loader-on').hide();
                    $(".remove_error").remove();
                    form.errorMessage('Something went wrong');
                    $('button[type="submit"]').attr('disabled', false);
                }
            });
        });
    </script>
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>
@endsection
