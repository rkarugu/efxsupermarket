@extends('layouts.admin.admin')
@section('content')
    <form id="purchaseForm" method="POST" action="{{ route($model . '.update', ['purchase_order' => $order->id]) }}"
        accept-charset="UTF-8" class="addExpense" enctype="multipart/form-data">
        <input type="hidden" id="action" name="action" value="process">
        @csrf
        @method('put')
        <section class="content">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"> {!! $title !!} </h3>
                    @include('message')
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class = "row">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Purchase No.</label>
                                    <div class="col-sm-7">
                                        {!! Form::text('emp_name', $order->purchase_no, [
                                            'maxlength' => '255',
                                            'placeholder' => '',
                                            'class' => 'form-control',
                                            'readonly' => true,
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Purchase Date</label>
                                    <div class="col-sm-7">
                                        {!! Form::text('purchase_date', $order->purchase_date, [
                                            'maxlength' => '255',
                                            'placeholder' => '',
                                            'class' => 'form-control',
                                            'readonly' => true,
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label for="advance_payment" class="col-sm-5 control-label">Advance Payment</label>
                                    <div class="col-sm-7">
                                        <input type="checkbox" class="single_lpo_type" name="advance_payment"
                                            id="advance_payment" @checked($order->advance_payment)>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label for="lpo_type" class="col-sm-5 control-label">Bulk LPO</label>
                                    <div class="col-sm-7">
                                        <input type="checkbox" name="lpo_type" class="single_lpo_type lpo_type"
                                            value="Bulk" @checked($order->lpo_type == 'Bulk')>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label for="lpo_type" class="col-sm-5 control-label">Normal LPO</label>
                                    <div class="col-sm-7">
                                        <input type="checkbox" name="lpo_type" class="single_lpo_type lpo_type"
                                            value="Normal" @checked($order->lpo_type == 'Normal')>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="row">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Store Location</label>
                                    <div class="col-sm-7">
                                        {!! Form::select('store_location_id', getStoreLocationDropdown(), $order->wa_location_and_store_id, [
                                            'class' => 'form-control store_location_id',
                                            'required' => true,
                                            'placeholder' => 'Please select store',
                                            'disabled' => true,
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Note</label>
                                    <div class="col-sm-7">
                                        {!! Form::text('note', $order->note, ['maxlength' => '255', 'placeholder' => '', 'class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Supplier Name</label>
                                    <div class="col-sm-7">
                                        {!! Form::select('wa_supplier_id', getSupplierDropdown(), $order->wa_supplier_id, [
                                            'class' => 'form-control  mlselec6t wa_supplier_id',
                                            'id' => 'wa_supplier_id',
                                            'placeholder' => 'Please select',
                                        ]) !!}
                                        <span id = "error_msg_wa_supplier_id"></span>
                                    </div>
                                </div>
                            </div>                            
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-7">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="supplier_own" id="input_supplier_delivery"
                                                    class="input_supplier_own" value="SupplierDelivery"
                                                    @checked($order->supplier_own == 'SupplierDelivery')>
                                                <b>Supplier Delivery</b>
                                            </label>

                                            <label>
                                                <input type="radio" name="supplier_own" id="input_own_collection"
                                                    class="input_supplier_own" value="OwnCollection"
                                                    @checked($order->supplier_own == 'OwnCollection')>
                                                <b>Own Collection</b>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class = "row hideme_supplier_own" style="display:none">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Vehicles</label>
                                    <div class="col-sm-7">
                                        <select name="vehicle_id" id="inputvehicle_id" class="form-control mlselec6t">
                                            <option value="" selected>-- Select Vehicle --</option>
                                            @foreach ($vehicles as $vehicle)
                                                <option value="{{ $vehicle->id }}" @selected($order->vehicle_id == $vehicle->id)>
                                                    {{ $vehicle->name }}
                                                    {{ $vehicle->license_plate_number }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class = "row hideme_supplier_own" style="display:none">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Employees</label>
                                    <div class="col-sm-7">
                                        <select name="employee_id" id="inputemployee_id" class="form-control mlselec6t">
                                            <option value="" selected>-- Select Employee --</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}" @selected($order->employee_id == $employee->id)>
                                                    {{ $employee->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header with-border no-padding-h-b">
                    <div class="col-md-12 no-padding-h ">
                        <h3 class="box-title"> Purchase Order Line</h3>
                        <button type="button" class="btn btn-danger btn-sm addNewrow"
                            style="position: fixed;bottom: 30%;left:4%;"><i class="fa fa-plus"
                                aria-hidden="true"></i></button>

                        <button type="button" class="btn btn-danger btn-sm tradeAgreement" data-toggle="modal"
                            data-target="#tradeAgreement" style="position: fixed;top: 30%;right:0%;"><i
                                class="fa fa-check-circle" aria-hidden="true"></i>
                            <span class="show_discount_on_hover">
                                Trade Discounts
                            </span>
                        </button>
                        <!-- Modal -->
                        <div class="modal fade" id="tradeAgreement" tabindex="-1" role="dialog"
                            aria-labelledby="modelTitleId" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Trade Discounts</h5>

                                    </div>
                                    <div class="modal-body">

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span id = "requisitionitemtable">
                            <table class="table table-bordered table-hover" id="mainItemTable">
                                <thead>
                                    <tr>
                                        <th>Selection</th>
                                        <th>Description</th>
                                        <th style="width: 90px;">Unit</th>
                                        <th style="width: 90px;">QTY</th>
                                        <th style="width: 90px;">Free Stock</th>
                                        <th style="width: 90px;">QOO</th>
                                        <th>SOH</th>
                                        <th>Sales</th>
                                        <th>Reorder Level</th>
                                        <th>Max Stock</th>
                                        <th>Incl. Price</th>
                                        <th>VAT Type</th>
                                        <th style="width: 90px;">Disc</th>
                                        <th style="width: 90px;">Disc Type</th>
                                        <th style="width: 90px;">Disc.</th>
                                        <th>Excl.</th>
                                        <th>VAT</th>
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        {!! $item !!}
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="19" style="text-align:center">
                                            Total Tonnage: <span id="total_tonnage">0</span>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="16" style="text-align:right">
                                            Total Exclusive
                                        </th>
                                        <td colspan="3">KES <span id="total_exclusive">0.00</span></td>
                                    </tr>
                                    <tr>
                                        <th colspan="16" style="text-align:right">
                                            Total VAT
                                        </th>
                                        <td colspan="3">KES <span id="total_vat">0.00</span></td>
                                    </tr>
                                    <tr>
                                        <th colspan="16" style="text-align:right">
                                            Invoice Discount
                                        </th>
                                        <td colspan="3">KES <span id="invoice_discount">0.00</span></td>
                                        {{-- (<span id="invoice_discount_per">0%</span>)</td> --}}
                                    </tr>
                                    <tr>
                                        <th colspan="16" style="text-align:right">
                                            Transport Rebate Discount
                                        </th>
                                        <td colspan="3">KES <span id="transport_rebate_discount">0.00</span></td>
                                    </tr>
                                    <tr>
                                        <th colspan="16" style="text-align:right">
                                            Distribution Discount
                                        </th>
                                        <td colspan="3">KES <span id="distribution_discount_total">0.00</span></td>
                                    </tr>
                                    <tr>
                                        <th colspan="16" style="text-align:right">
                                            Total
                                        </th>
                                        <td colspan="3">KES <span id="total_total">0.00</span></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </span>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="hidden" name="invoice_discount_per" class="invoice_discount_per">
                            <input type="hidden" name="invoice_discount" class="invoice_discount">
                            <button type="submit" class="btn btn-primary btn-sm" id="save-btn">
                                <i class="fa fa-save"></i>
                                Save
                            </button>
                            <button type="submit" class="btn btn-primary btn-sm" id="process-btn">
                                <i class="fa fa-circle"></i>
                                Process
                            </button>
                            <button type="button" class="btn btn-primary getItemsBtn btn-sm">
                                <i class="fa fa-shopping-cart"></i>
                                Run Out Of Stock Items
                            </button>
                        </div>
                        <div class="col-md-3"></div>
                        <div class="col-md-3"></div>
                    </div>
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
                    Are you sure you want to proceed with the LPO?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmSubmit">Confirm</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/iCheck/flat/blue.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">

    <style type="text/css">
        .tradeAgreement {
            display: none;
        }

        .tradeAgreement i {
            line-height: 1.5;
            margin-right: 5px;
            font-size: 13px;
            font-weight: bold;
        }

        .tradeAgreement:hover .show_discount_on_hover {
            display: block;
        }

        .show_discount_on_hover {
            display: none;
            font-size: 13px
        }

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
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/iCheck/icheck.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    {{-- <script type="text/javascript">
        document.getElementById('purchaseForm').addEventListener('submit', function(event) {
            event.preventDefault(); 
            $('#confirmationModal').modal('show'); 
        });

        document.getElementById('confirmSubmit').addEventListener('click', function() {
            $('#purchaseForm').off('submit').submit();  
            $('#confirmationModal').modal('hide '); 

        });
    </script> --}}
    <script type="text/javascript">
        $(document).ready(function() {
            $('.single_lpo_type').change(function() {
                if ($(this).is(':checked')) {
                    $('.single_lpo_type').not(this).prop('disabled', true);
                } else {
                    $('.single_lpo_type').prop('disabled', false);
                }
            })
            $('.single_lpo_type').trigger('change');

            $("#wa_supplier_id").trigger('change');

            vat_list();
        });


        var supplier_discounts = {};

        function get_transport_discount(obj) {
            var a = [];
            $.each(obj.location_discounts, function(indexInArray, valueOfElement) {
                var ch = {};
                $.each(valueOfElement.discount, function(i, v) {
                    ch[v.inventory_id] = v.discount;
                });
                a[valueOfElement.location] = ch;
            });
            return a;
        }

        function calculate_transport_rebate_discount(total, tonnage) {
            // let transport_rebate_discount_type = $('#transport_rebate_discount_type').val();
            let per_unit = supplier_discounts['Transport rebate per unit'];
            let percentage = supplier_discounts['Transport rebate percentage'];
            let per_tonnage_ag = supplier_discounts['Transport rebate per tonnage'];

            if (per_unit) {
                var other_options = JSON.parse(per_unit.other_options);
                var tag = 'transport_rebate_per_unit';
                $('#transport_rebate_discount_type').val('per_unit');
            } else if (percentage) {
                var other_options = JSON.parse(percentage.other_options);
                var tag = 'transport_rebate_percentage';
                $('#transport_rebate_discount_type').val('invoice_amount');
            } else if (per_tonnage_ag) {
                var other_options = JSON.parse(per_tonnage_ag.other_options);
                var tag = 'transport_rebate_per_tonnage';
                $('#transport_rebate_discount_type').val('per_tonnage');
            } else {
                return;
            }

            let per_tonnage = 0;
            let discount_percentage = 0;
            let discount_units = 0;

            let location = $('.store_location_id option:selected').html();
            transport_discounts = get_transport_discount(other_options);
            console.log(transport_discounts);
            console.log(transport_discounts[location]);
            console.log(location);
            console.log(tag);
            if (transport_discounts['All']) {
                location = 'All';
            }
            if (transport_discounts[location]) {
                console.log(location);
                var allt = $(document).find('.total');
                var transport_rebate_discount = 0;
                $.each(allt, function(indexInArray, valueOfElement) {
                    let itemid = $(valueOfElement).parents('tr').find('.itemid').val();
                    let _d = transport_discounts[location][itemid] ?? 0;
                    console.log(_d)
                    console.log(itemid)
                    let qty = $(valueOfElement).parents('tr').find('.quantity').val();
                    console.log(qty)

                    $(valueOfElement).parents('tr').find(`.${tag}`).val(_d);
                    if (tag == 'transport_rebate_per_unit') {
                        transport_rebate_discount = parseFloat(transport_rebate_discount) + parseFloat(_d) *  parseFloat(qty);
                    } else if (tag == 'transport_rebate_percentage') {
                        transport_rebate_discount = parseFloat(transport_rebate_discount) + (parseFloat($(valueOfElement).text()) * parseFloat(_d) / 100)
                    } else if (tag == 'transport_rebate_per_tonnage') {
                        transport_rebate_discount = parseFloat(transport_rebate_discount) + parseFloat(_d) * parseFloat(tonnage);
                    }
                    $(valueOfElement).parents('tr').find(`.${tag}`).val(_d);
                });
                $('#transport_rebate_discount').html(transport_rebate_discount);
                $('.transport_rebate_discount').val(transport_rebate_discount);
                console.log(transport_rebate_discount);

                // $('.transport_rebate_discount_value').val(null);
                $('#total_total').html(parseFloat(total - transport_rebate_discount).toFixed(2));
            }
        }

        function calculate_free_stock(input) {
            var quantity = $(input).parents('tr').find('.quantity').val();
            var itemid = $(input).parents('tr').find('.itemid').val();
            if (quantity < 0 || quantity == '') {
                quantity = 0;
            }
            $(input).parents('tr').find('.free_stock').val(0);
            let free_qos = supplier_discounts["Purchase Quantity Offer"]
            if (!free_qos) {
                return;
            }

            let other_options = JSON.parse(free_qos['other_options']);
            console.log(other_options);
            if (other_options) {
                let found = false;
                for (const key in other_options) {
                    if (Object.hasOwnProperty.call(other_options, key) && itemid == key) {
                        console.log(`Key '${key}' contains the value '${itemid}'`);
                        found = true;
                        break;
                    }
                }
                if (!found) {
                    return;
                }
                let item = other_options[`${itemid}`]
                if (item) {
                    let free_product_quantity = item.free_stock ?? 0;
                    let purchased_product_quantity = item.purchase_quantity ?? 1;
                    let pre_qualified = parseFloat(quantity) / parseFloat(purchased_product_quantity);
                    let post_qualified = parseFloat(pre_qualified) * parseFloat(free_product_quantity);
                    $(input).parents('tr').find('.free_stock').val(parseInt(post_qualified));
                }
            }
        }

        function add_discount_percentage(input) {
            let itemid = $(input).parents('tr').find('.itemid').val();

            let base_discount = supplier_discounts["Base Discount"];
            if (!base_discount) {
                return;
            }
            let all = JSON.parse(base_discount["other_options"]);
            if (all) {
                // $(input).parents('tr').find('.discount_per').val(parseFloat(all[`${itemid}`].discount));
                $.each(all, function(indexInArray, valueOfElement) {
                    if (valueOfElement.discount > 0) {
                        console.log(indexInArray, valueOfElement.discount)
                        $(input).parents('tr').find(`.discount_per[name="item_discount_per[${indexInArray}]"]`).val(
                            parseFloat(valueOfElement.discount));
                        $(input).parents('tr').find(`.discount_type[name="item_discount_type[${indexInArray}]"]`)
                            .val(valueOfElement.type ?? 'Percentage');
                    }
                });
            }
        }

        function load_supplier_discounts() {
            supplier_discounts = {};
            var mainSupplierId = $('#mainSupplierId').val();
            var supplier_id;

            if (mainSupplierId) {
                supplier_id = mainSupplierId;
            } else {
                supplier_id = $('.wa_supplier_id option:selected').val();
            }


            if (!supplier_id || supplier_id == "") {
                form.errorMessage('Select supplier to get inventory item');
                return false;
            }
            $.ajax({
                type: "POST",
                url: "{{ route('purchase-orders.get-supplier-discounts') }}",
                data: {
                    'supplier_id': supplier_id,
                    '_token': "{{ csrf_token() }}"
                },
                success: function(response) {
                    if(response.result == -1){
                        $('#tradeAgreement .modal-body').html('');
                        
                        return form.errorMessage(response.message);
                    }

                    let tradeAgreement = "<table class='table table-hover'>";
                    $.each(response.data, function(k, v) {
                        supplier_discounts[v.discount_type] = v;
                        if (v.discount_type == "Purchase Quantity Offer" || v.discount_type ==
                            "Invoice Discount" || v.discount_type == "Base Discount" || v.discount_type == "Distribution Discount" || v
                            .discount_type == "Transport rebate per unit" || v.discount_type ==
                            "Transport rebate percentage" || v.discount_type ==
                            "Transport rebate per tonnage") {
                            var child = `<tr><th>
                                ${v.discount_type}
                                </th>`;

                            if (v.discount_type == "Purchase Quantity Offer") {
                                child = child + `<td>
                                    Purchase Product Quantity: ${v.purchased_product_quantity}
                                    <br />
                                    Free Product Quantity: ${v.free_product_quantity}
                                    </td>`;
                            } else if (v.discount_type == "Transport rebate") {
                                let other_options = JSON.parse(v.other_options);
                                child = child + `
                                    <td>`;
                                $.each(other_options.location_discounts, function(indexInArray,
                                    valueOfElement) {
                                    child = child + `

                                        Location: ${valueOfElement.location}
                                        <br />
                                        Per Unit: ${valueOfElement.per_unit_discount}
                                        /
                                        % of invoice amount: ${valueOfElement.percentage_of_invoice}
                                       /
                                        Per Tonnage: ${valueOfElement.per_tonnage_discount_value}
                                        <hr />

                                    `;
                                });
                                child = child + `
                                    </td>`;
                            } else if (v.discount_type == "Distribution Discount") {
                                let other_options = JSON.parse(v.other_options);
                                child = child + `
                                    <td>`;
                                $.each(other_options.location_discounts, function(indexInArray,
                                    valueOfElement) {
                                    child = child + `

                                        Location: ${valueOfElement.location}
                                        <br />
                                        Per Unit: ${valueOfElement.per_unit_discount}
                                        <hr />

                                    `;
                                });
                                child = child + `
                                    </td>`;
                            } else if (v.discount_type == "Base Discount") {
                                if (v.applies_to_all_item) {
                                    child = child + `<td>Discount Percentage: ${v.discount_value}</td>`;
                                } else {
                                    let other_options = JSON.parse(v.other_options);
                                    child = child + `
                                        <td>`;
                                    $.each(other_options, function(indexInArray, valueOfElement) {
                                        if (valueOfElement.discount > 0) {
                                            child = child + `

                                                Stock: ${valueOfElement.stock_id}
                                                <br />
                                                Discount %: ${valueOfElement.discount}

                                                <hr />

                                            `;
                                        }
                                    });
                                    child = child + `
                                        </td>`;
                                }
                            } else {
                                child = child + `<td>Discount Percentage: ${v.discount_value}</td>`;
                            }

                            child = child + `</tr>`;
                            tradeAgreement = tradeAgreement + child;
                        }
                    });
                    tradeAgreement = tradeAgreement + "</table>";
                    $('#tradeAgreement .modal-body').html(tradeAgreement);

                    $("input.quantity").trigger('change');
                }
            });
        }

        $('#wa_supplier_id').change(function(e) {
            load_supplier_discounts();
            $('.tradeAgreement').css('display', 'flex');
        });
        $('.input_supplier_own').change(function(e) {
            if ($(this).val() == "OwnCollection") {
                $('.hideme_supplier_own').show();
            } else {
                $('.hideme_supplier_own').hide();
            }
        })
        var form = new Form();
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
        // $('#save-btn').on('click', function(e) {
        //     e.preventDefault();
        //     $("#action").val('save');
        //     $('#confirmationModal').modal('show');
        // });

        // $('#process-btn').on('click', function(e) {
        //     e.preventDefault();
        //     $("#action").val('process');
        //     $('#confirmationModal').modal('show');
        // });
        // $(document).on('submit', '.addExpense', function(e) {
        $('#show_supplier_own_error').hide()
        $('#confirmSubmit').on('click', function() {
            $('#confirmationModal').modal('hide');

            var $confirmBtn = $(this);
            $confirmBtn.prop('disabled', true).text('Processing...');
            $('#save-btn, #process-btn, .getItemsBtn').prop('disabled', true);

            // e.preventDefault();
            $('button[type="submit"]').attr('disabled', true);
            document.getElementById("process-btn").disabled = true;

            // $('#loader-on').show();
            // var postData = new FormData($(this)[0]);
            var postData = new FormData($('.addExpense')[0]);

            // var url = $(this).attr('action');
            var url = $('.addExpense').attr('action');

            postData.append('_token', $(document).find('input[name="_token"]').val());
            postData.append('store_location_id', $("#store_location_id").val());
            $.ajax({
                url: url,
                data: postData,
                contentType: false,
                cache: false,
                processData: false,
                method: 'POST',
                success: function(out) {

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
                    $(".remove_error").remove();

                    let errorMessage = 'Something went wrong';

                    if (err?.responseJSON?.errors) {
                        for (let key in err.responseJSON.errors) {
                            let id = key.split(".");
                            if (id && id[1]) {
                                $("[name='" + id[0] + "[" + id[1] + "]']").parent().append(
                                    '<label class="error d-block remove_error w-100" id="' +
                                    key +
                                    '_error">' + err.responseJSON.errors[key].join('<br>') +
                                    '</label>'
                                );
                            } else {
                                if (id == 'supplier_own') {
                                    $('#show_supplier_own_error').show()
                                }
                                $("[name='" + key[0] + "']").parent().append(
                                    '<label class="error d-block remove_error w-100" id="' + key +
                                    '_error">' + err.responseJSON.errors[key].join('<br>') +
                                    '</label>'
                                );
                                $("." + key).parent().append(
                                    '<label class="error d-block remove_error w-100" id="' + key +
                                    '_error">' + err.responseJSON.errors[key].join('<br>') +
                                    '</label>'
                                );
                            }
                        }
                    } else if (err?.responseJSON?.error) {
                        errorMessage = err.responseJSON.error;
                    } else {
                        errorMessage = 'Something went wrong.';
                    }

                    // Swal.fire({
                    //     icon: 'error',
                    //     title: 'Error!',
                    //     html: 'Something went wrong.',
                    // });

                    $('button[type="submit"]').attr('disabled', false);
                    $('#loader-on').hide();
                },
                complete: function() {
                    $confirmBtn.prop('disabled', false).text('Confirm');
                    $('#save-btn, #process-btn, .getItemsBtn').prop('disabled', false);
                }
            });
        });

        var selectedButton;

        // Show modal on button click
        $('#save-btn, #process-btn').on('click', function(e) {
            e.preventDefault();
            selectedButton = $(this);
            var actionValue = selectedButton.attr('id') === 'save-btn' ? 'save' : 'process';
            $("#action").val(actionValue);
            $('#confirmationModal').modal('show');
        });

        $(function() {
            $('.wa_unit_of_measures_id').select2({
                placeholder: 'Select Bin Location',
                ajax: {
                    url: '{{ route('uom.search_by_item_location') }}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function(params) {
                        var store_location_id = $('.store_location_id option:selected').val();
                        return {
                            q: params.term,
                            id: store_location_id
                        };
                    },
                    processResults: function(data) {
                        var res = data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.title
                            };
                        });
                        return {
                            results: res
                        };
                    }
                },
            });
            $(document).on('keyup', '.item_quantity_max_stock', function() {
                $(this).parent().find('.error_validation').remove();
                $('.submitbtn').attr('disabled', false);
                if (parseFloat($(this).val()) > parseFloat($(this).data('max_stock'))) {
                    // $(this).parent().append('<span style="color:red" class="error_validation">Qty cannot be greater than the Max Stock</span>');
                    // $('.submitbtn').attr('disabled',true);
                }
            });
            $('.getItemsBtn').click(function(e) {
                e.preventDefault();

                var button = $(this);
                var originalButtonHtml = button.html();
                button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
                $('#save-btn, #process-btn').prop('disabled', true);

                var mainSupplierId = $('#mainSupplierId').val();
                var sup;

                if (mainSupplierId) {
                    sup = mainSupplierId;
                } else {
                    sup = $('.wa_supplier_id option:selected').val();
                }

                var store_location_id = $('.store_location_id option:selected').val();
                var bin_location_id = $('.wa_unit_of_measures_id option:selected').val();
                if (!sup || sup == "") {
                    form.errorMessage('Select supplier to get inventory item');
                    button.prop('disabled', false).html(originalButtonHtml);
                    $('#save-btn, #process-btn').prop('disabled', false);
                    return false;
                }
                $.ajax({
                    type: "GET",
                    url: "{{ route('external-requisitions.getOutOfStockItems') }}",
                    data: {
                        'supplier_id': sup,
                        'store_id': store_location_id,
                        'bin_location_id': bin_location_id
                    },
                    success: function(response) {
                        form.successMessage('Items fetched successfully');
                        button.prop('disabled', false).html(originalButtonHtml);
                        $('#save-btn, #process-btn').prop('disabled', false);
                        $('#mainItemTable tbody').html("");
                        $(".vat_list").select2('destroy');
                        $.each(response, function(index, item) {
                            var per = 0;
                            var vat = 0.00;
                            var l = "";
                            var qq = (parseFloat(item.max_stock_f ?? 0) - parseFloat(
                                item.quantity ?? 0));
                            if (qq < 0) {
                                qq = 0;
                            }

                            if (item.get_taxes_of_item) {
                                l = l + '<option value="' + item.get_taxes_of_item.id +
                                    '" selected>' + item.get_taxes_of_item.title +
                                    '</option>';
                                per = item.get_taxes_of_item.tax_value;
                                vat = (parseFloat(parseFloat(item.price_list_cost *
                                        qq) *
                                    parseFloat(per)) / 100).toFixed(2);
                            }
                            $('#mainItemTable tbody').append('<tr>' +
                                '<td><input type="hidden" name="item_id[' + item
                                .id + ']" class="itemid" value="' + item.id + '">' +
                                '<input type="hidden" name="item_net_weight[' + item
                                .id + ']" class="item_net_weight" value="' + item
                                .net_weight + '">' +
                                '<input style="padding: 3px 3px;"  type="text" class="testIn form-control" value="' +
                                item.stock_id_code + '">' +
                                '<div class="textData" style="width: 100%;position: relative;z-index: 99;"></div>' +
                                '</td>' +
                                '<td>' + item.description + '</td>' +
                                '<td>' + (item.pack_size?.title ?? NULL) + '</td>' +
                                '<td><input style="padding: 3px 3px;" onkeyup="getTotal(this)" onchange="getTotal(this)"  value="' +
                                (parseFloat(item.max_stock_f) - parseFloat(item
                                    .quantity)) + '" data-max_stock="' + item
                                .max_stock_f +
                                '" type="text" name="item_quantity[' + item.id +
                                ']" data-id="' + item.id +
                                '"  class="quantity item_quantity_max_stock form-control"></td>' +
                                '<td ><input type="text" class="form-control free_stock" readonly name="free_qualified_stock[' +
                                item.id + ']"></td>' +
                                '<td>' + (item.qty_on_order) + '</td>' +
                                '<td>' + (item.quantity) + '</td>' +
                                '<td>' + (item.total_sales + item.pack_sales)
                                .toFixed(2) +
                                '</td>' +
                                '<td>' + item.re_order_level + '</td>' +
                                '<td>' + item.max_stock_f + '</td>' +
                                '<td><input style="padding: 3px 3px;" onchange="getTotal(this)" onkeyup="getTotal(this)" type="text" readonly name="item_standard_cost[' +
                                item.id + ']" data-id="' + item.id +
                                '"  class="standard_cost form-control" value="' + (
                                    item.price_list_cost ?? 0) + '"></td>' +
                                // '<td>'+(item.location?.location_name ?? '-')+'</td>'+
                                '<td><select class="form-control vat_list" name="item_vat[' +
                                item.id + ']">' + l +
                                '</select>' +
                                '<input type="hidden" class="vat_percentage" value="' +
                                per + '"  name="item_vat_percentage[' + item.id +
                                ']"></td>' +
                                '<td><input style="padding: 3px 3px;" onchange="getTotal(this)" onkeyup="getTotal(this)" type="text" readonly name="item_discount_per[' +
                                item.id + ']" data-id="' + item.id +
                                '" class="discount_per form-control" value="0.00" ></td>' +
                                '<td><input style="padding: 3px 3px;" onchange="getTotal(this)" onkeyup="getTotal(this)" type="text" readonly name="item_discount_typ[' +
                                item.id + ']" data-id="' + item.id +
                                '" class="discount_per form-control" value="" ></td>' +
                                '<td><input style="padding: 3px 3px;"  type="text" readonly name="item_discount[' +
                                item.id + ']" data-id="' + item.id +
                                '"  class="discount form-control" value="0.00"></td>' +
                                '<td><span class="exclusive">' + (parseFloat(
                                    parseFloat(item.price_list_cost * qq) - vat
                                ))
                                .toFixed(2) + '</span></td>' +
                                '<td><span class="vat">' + vat + '</span></td>' +
                                '<td><span class="total">' + parseFloat(item
                                    .price_list_cost * qq).toFixed(2) +
                                '</span></td>' +
                                '<td><button type="button" class="btn btn-danger btn-sm deleteparent"><i class="fas fa-trash" aria-hidden="true"></i></button></td>' +
                                '</tr>'
                            );
                            $(document).find('[name="item_discount_per[' + item.id +
                                ']"]').change();
                        });
                        vat_list();
                        totalofAllTotal();
                        calculate_free_stock('.quantity');
                        add_discount_percentage('.quantity');

                        $('button').prop('disabled', false);
                        // $this.text('Run Out Of Stock Items');
                        $('.getItemsBtn').prop('disabled', true);
                        $('.getItemsBtn').text('Run Out Of Stock Items');
                    },
                    error: function(xhr) {
                        var error = xhr.responseJSON ? xhr.responseJSON.message :
                            'An error occurred';
                        Swal.fire('Error', error, 'error');
                    },
                    complete: function() {
                        button.prop('disabled', false).html(originalButtonHtml);
                        $('#save-btn, #process-btn').prop('disabled', false);
                    }
                });
            });
            $('body').addClass('sidebar-collapse');
            $('.wa_priority_level_id').select2({
                placeholder: 'Select Priority Level',
                ajax: {
                    url: '{{ route('priority-level.dropdown_search') }}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        var res = data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.title
                            };
                        });
                        return {
                            results: res
                        };
                    }
                },
            });
        });
        $(function() {
            $(".mlselec6t").select2();
        });
        $(function() {
            $(".mlselec6t_modal").select2({
                dropdownParent: $('.modal')
            });
        });
    </script>
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>
    <script>
        var valueTest = null;
        $(document).on('keyup keypress click', '.testIn', function(e) {
            var vale = $(this).val();
            var mainSupplierId = $('#mainSupplierId').val();
            var sup;

            if (mainSupplierId) {
                sup = mainSupplierId;
            } else {
                sup = $('.wa_supplier_id option:selected').val();
            }



            if (!sup || sup == "") {
                form.errorMessage('Select supplier to get inventory item');
                return false;
            }
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
                    $(this).parent().find('.textData table').scrollTop($(this).parent().find('.textData table')
                        .scrollTop() + obj.next().height());
                }
                if (selectedLi == 0) {
                    $(this).parent().find('.textData table').scrollTop($(this).parent().find(
                        '.textData table tr:first').position().top);
                }

                return false;
            } else if (code == 38) { //Down Arrow
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
            } else if (valueTest != vale && (e.type == 'keyup' || e.type == 'click') && code != 13 && code != 38 &&
                code != 40 && vale != '') {
                var $this = $(this);
                var store_location_id = $('.store_location_id option:selected').val();


                if (vale.length >= 3) {
                    var mainSupplierId = $('#mainSupplierId').val();
                    var sup;

                    if (mainSupplierId) {
                        sup = mainSupplierId;
                    } else {
                        sup = $('.wa_supplier_id option:selected').val();
                    }
                    $.ajax({
                        type: "GET",
                        url: "{{ route('purchase-orders.inventoryItems') }}",
                        data: {
                            'search': vale,
                            'store_location_id': store_location_id,
                            'supplier_id': sup
                        },
                        success: function(response) {
                            $this.parent().find('.textData').html(response);
                        }
                    });
                    valueTest = vale;
                }

                return true;
            }


        });

        $(document).click(function(e) {
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
            var mainSupplierId = $('#mainSupplierId').val();
            var sup;

            if (mainSupplierId) {
                sup = mainSupplierId;
            } else {
                sup = $('.wa_supplier_id option:selected').val();
            }


            if (!sup || sup == "") {
                form.errorMessage('Select supplier to get inventory item');
                return false;
            }
            $.each(itemids, function(indexInArray, valueOfElement) {
                if ($this.data('id') == $(valueOfElement).val()) {
                    form.errorMessage('This Item is already added in list');
                    furtherCall = false;
                    return true;
                }
            });
            if (furtherCall == true) {
                var store_location_id = $('.store_location_id option:selected').val();

                $.ajax({
                    type: "GET",
                    url: "{{ route('purchase-orders.getInventryItemDetails') }}",
                    data: {
                        'id': $this.data('id'),
                        'wa_supplier_id': sup,
                        'store_location_id': store_location_id,
                    },
                    success: function(response) {
                        $(".vat_list").select2('destroy');
                        $this.parents('tr').replaceWith(response);
                        vat_list();
                        totalofAllTotal();
                    }
                });
            }
        }
        $(document).on('click', '.deleteparent', function() {
            $(this).parents('tr').remove();
            totalofAllTotal()
        });
        $(document).on('click', '.addNewrow', function() {
            $('#mainItemTable tbody').append(
                '<tr><td><input type="text" class="testIn form-control makemefocus"><div class="textData" style="width: 100%;position: relative;z-index: 99;"></div></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td></td>' +
                '<td><button class="btn btn-danger btn-sm deleteparent"><i class="fa fa-trash" aria-hidden="true"></i></button></td>' +
                '</tr>');
            makemefocus();
        });

        var vat_list = function() {
            $(".vat_list").select2({
                placeholder: 'Select Vat',
                ajax: {
                    url: '{{ route('expense.vat_list') }}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        var res = data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.text
                            };
                        });
                        return {
                            results: res
                        };
                    }
                },
            });
        };
        $(document).on('change', '.vat_list', function() {
            var vat = $(this).val();
            var $this = $(this);
            $.ajax({
                type: "GET",
                url: "{{ route('expense.vat_find') }}",
                data: {
                    'id': vat
                },
                success: function(response) {
                    $this.parents('tr').find('.vat_percentage').val(response.tax_value);
                    getTotal($this);
                }
            });

        });

        function getTotal(vara) {
            var price = $(vara).parents('tr').find('.standard_cost').val();
            var quantity = $(vara).parents('tr').find('.quantity').val();
            if (quantity < 0 || quantity == '') {
                quantity = 0;
            }
            var discount_per = $(vara).parents('tr').find('.discount_per').val();
            var discount_type = $(vara).parents('tr').find(`.discount_type`).val();
            if (discount_type == 'Value') {
                var discount = parseFloat(quantity) * parseFloat(discount_per);
            } else {
                var discount = ((parseFloat(price) * parseFloat(quantity)) * parseFloat(discount_per)) / 100;
            }

            var vat_percentage = $(vara).parents('tr').find('.vat_percentage').val();
            var exclusive = ((parseFloat(price) * parseFloat(quantity)) - parseFloat(discount));
            var vat = parseFloat(exclusive) - ((parseFloat(exclusive) * parseFloat(100)) / (parseFloat(vat_percentage) +
                100));
            var total = parseFloat(exclusive);
            exclusive = (parseFloat(exclusive) - parseFloat(vat));
            $(vara).parents('tr').find('.discount').val((discount).toFixed(2));
            $(vara).parents('tr').find('.exclusive').html((exclusive).toFixed(2));
            $(vara).parents('tr').find('.vat').html((vat).toFixed(2));
            $(vara).parents('tr').find('.total').html((total).toFixed(2));
            calculate_free_stock(vara);
            add_discount_percentage(vara);
            totalofAllTotal();
        }
        $(document).on('keyup', '.discount', function(e) {
            var discount = $(this).val();
            var price = $(this).parents('tr').find('.standard_cost').val();
            var quantity = $(this).parents('tr').find('.quantity').val();
            var vat_percentage = $(this).parents('tr').find('.vat_percentage').val();
            if (quantity < 0 || quantity == '') {
                quantity = 0;
            }
            var discount_per = (discount / parseFloat(price) * parseFloat(quantity)) * 100;
            var exclusive = ((parseFloat(price) * parseFloat(quantity)) - parseFloat(discount));
            var vat = parseFloat(exclusive) - ((parseFloat(exclusive) * parseFloat(100)) / (parseFloat(
                vat_percentage) + 100));
            var total = parseFloat(exclusive);
            exclusive = (parseFloat(exclusive) - parseFloat(vat));
            $(this).parents('tr').find('.discount_per').val((discount_per).toFixed(2));
            $(this).parents('tr').find('.exclusive').html((exclusive).toFixed(2));
            $(this).parents('tr').find('.vat').html((vat).toFixed(2));
            $(this).parents('tr').find('.total').html((total).toFixed(2));
            totalofAllTotal();
        });

        $('#transport_rebate_discount_type').change(function(e) {
            totalofAllTotal();
        })

        function calculate_distribution_discount(total, tonnage) {

            let location = $('.store_location_id option:selected').html();

            let distribution_discount = supplier_discounts['Distribution Discount'];
            if (!distribution_discount) {
                return;
            }
            var discounts = get_transport_discount(JSON.parse(distribution_discount.other_options));
            if (discounts['All']) {
                location = 'All';
            }
            if (discounts[location]) {
                console.log(location);
                console.log(discounts[location]);
                var allt = $(document).find('.total');
                var d_discount = 0;
                $.each(allt, function(indexInArray, valueOfElement) {
                    let itemid = $(valueOfElement).parents('tr').find('.itemid').val();
                    let _d = discounts[location][itemid] ?? 0;
                    let qty = $(valueOfElement).parents('tr').find('.quantity').val();

                    d_discount = parseFloat(d_discount) + parseFloat(_d) * parseFloat(qty);

                    $(valueOfElement).parents('tr').find(`.distribution_discount`).val(_d);
                });
                $('#distribution_discount_total').html(d_discount);
                $('.distribution_discount_total').val(d_discount);

                $('#total_total').html(parseFloat(total - d_discount).toFixed(2));
            }
        }

        function totalofAllTotal() {
            var alle = $(document).find('.exclusive');
            var allv = $(document).find('.vat');
            var allt = $(document).find('.total');
            var exclusive = 0;
            var vat = 0;
            var total = 0;
            var total_tonnage = 0;
            let discount_value_total = 0;
            let discount_value = 0;
            $.each(alle, function(indexInArray, valueOfElement) {
                let weight = $(valueOfElement).parents('tr').find('.item_net_weight').val();
                let quantity = $(valueOfElement).parents('tr').find('.quantity').val();
                if (quantity > 0 && weight > 0) {
                    total_tonnage = parseFloat(total_tonnage) + parseFloat(parseFloat(weight) * parseFloat(
                        quantity));
                }
            });
            $.each(alle, function(indexInArray, valueOfElement) {
                exclusive = parseFloat(exclusive) + parseFloat($(valueOfElement).text());
            });
            $.each(allv, function(indexInArray, valueOfElement) {
                vat = parseFloat(vat) + parseFloat($(valueOfElement).text());
            });
            var invoice_discount = supplier_discounts["Invoice Discount"];
            if (invoice_discount) {
                var other_options = JSON.parse(invoice_discount["other_options"]);
            }
            $.each(allt, function(indexInArray, valueOfElement) {
                let _t = parseFloat($(valueOfElement).text());
                let itemid = $(valueOfElement).parents('tr').find('.itemid').val();
                if (invoice_discount && other_options[`${itemid}`]) {
                    console.log(itemid)
                    let _d = other_options[`${itemid}`].discount ?? 0;
                    let dvt = parseFloat(_d) > 0 ? _t * parseFloat(_d) / 100 : 0;
                    $(valueOfElement).parents('tr').find('.invoice_percentage').val(_d);
                    discount_value_total += dvt;
                }
                total = parseFloat(total) + _t;
            });


            // if (invoice_discount && invoice_discount['discount_value'] && invoice_discount['discount_value'] > 0) {
            //     discount_value = invoice_discount['discount_value'];
            //     $(document).find('.invoice_discount_per').val(discount_value);
            // } else {
            //     $(document).find('.invoice_discount_per').val(0);
            // }

            // let discount_value_total =
            $(document).find('.invoice_discount').val(discount_value_total);
            $(document).find('#invoice_discount').html(discount_value_total);
            $(document).find('#invoice_discount_per').hide();
            // $(document).find('#invoice_discount_per').html(discount_value + "%");
            $('#total_exclusive').html((exclusive).toFixed(2));
            $('#total_vat').html((vat).toFixed(2));
            $('#total_total').html(parseFloat(total - discount_value_total).toFixed(2));
            $('#total_tonnage').html((total_tonnage).toFixed(2));
            calculate_transport_rebate_discount(parseFloat(total - discount_value_total).toFixed(2), parseFloat(
                total_tonnage).toFixed(2));
            calculate_distribution_discount(parseFloat(total - discount_value_total).toFixed(2), parseFloat(total_tonnage)
                .toFixed(2));
        }
    </script>
@endsection
