@extends('layouts.admin.admin')
@section('content')
    <div class=" multistep">
        <div class="container">
            <div class="stepwizard">
                <div class="stepwizard-row setup-panel">
                    <div class="stepwizard-step col-xs-6">
                        <a href="#step-1" type="button" class="btn btn-success btn-circle step-buttons step-buttons1">1</a>
                        <p><b>Item Information</b></p>
                    </div>
                    <div class="stepwizard-step col-xs-6">
                        <a href="#step-2" type="button" class="btn btn-default btn-circle step-buttons step-buttons2"
                            disabled="disabled">2</a>
                        <p><b>Item Information 2</b></p>
                    </div>
                    {{-- <div class="stepwizard-step col-xs-3">
                        <a href="#step-3" type="button" class="btn btn-default btn-circle step-buttons step-buttons3"
                            disabled="disabled">3</a>
                        <p><b>Additional Information</b></p>
                    </div> --}}

                </div>
            </div>
        </div>
        <form class="validate form-horizontal" role="form" method="POST" action="{{ route($model . '.store') }}"
            enctype = "multipart/form-data" >

            <section class="content setup-content" id="step-1">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"> Item Information </h3>
                    </div>
                    @include('message')
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Stock ID Code</label>
                            <div class="col-sm-10">
                                {!! Form::text('stock_id_code', null, [
                                    'maxlength' => '255',
                                    'placeholder' => 'Stock ID Code',
                                    'required' => true,
                                    'class' => 'form-control',
                                ]) !!}
                            </div>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Item Title</label>
                            <div class="col-sm-10">
                                {!! Form::text('title', null, [
                                    'maxlength' => '255',
                                    'placeholder' => 'Item Title',
                                    'required' => true,
                                    'class' => 'form-control',
                                ]) !!}
                            </div>
                        </div>
                    </div>


                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Description</label>
                            <div class="col-sm-10">
                                {!! Form::text('description', null, [
                                    'maxlength' => '255',
                                    'placeholder' => 'Description',
                                    'required' => true,
                                    'class' => 'form-control',
                                ]) !!}
                            </div>
                        </div>
                    </div>



                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Inventory Category</label>
                            <div class="col-sm-10">
                                {!! Form::select('wa_inventory_category_id', getInventoryCategoryList(), null, [
                                    'maxlength' => '255',
                                    'placeholder' => 'Please select',
                                    'required' => true,
                                    'class' => 'form-control wa_inventory_category_id mlselec6t',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Inventory Sub Category</label>
                            <div class="col-sm-10">
                                {!! Form::select('item_sub_category_id', [], null, [
                                    'maxlength' => '255',
                                    'placeholder' => 'Please select',
                                    'required' => true,
                                    'class' => 'form-control item_sub_category_id',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Preferred Supplier</label>
                            <div class="col-sm-10">
                                {!! Form::select('suppliers[]', $suppliers, null, [
                                    'class' => 'form-control selector_selects2',
                                    'required' => true,
                                    'multiple' => true,
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Selling Price Inc Vat</label>
                            <div class="col-sm-10">
                                {!! Form::number('selling_price', 0, ['min' => '0', 'required' => true, 'class' => 'form-control', 'name'=>'selling_price', 'id'=>'selling_price']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Standard Cost</label>
                            <div class="col-sm-10">
                                {!! Form::number('standard_cost', 0, ['min' => '0', 'required' => true, 'class' => 'form-control', 'name'=>'standard_cost', 'id'=>'standard_cost']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Price List Cost</label>
                            <div class="col-sm-10">
                                {!! Form::number('price_list_cost', 0, ['min' => '0', 'required' => true, 'class' => 'form-control', 'name'=>'price_list_cost', 'id'=>'price_list_cost']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Margin Type</label>
                            <div class="col-sm-10">
                                <div class="d-flex">
                                    <div class="form-check form-check-inline" style="margin-right:10px;">
                                        <input class="form-check-input" type="radio" name="margin_type"
                                            id="marginPercentage" value="1" checked required>
                                        <label class="form-check-label" for="marginPercentage">Percentage</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="margin_type" id="marginValue"
                                            value="0">
                                        <label class="form-check-label" for="marginValue">Value</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Min Margin</label>
                            <div class="col-sm-10">
                                {!! Form::number('percentage_margin', 0, ['min' => '0', 'required' => true, 'class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Actual Margin</label>
                            <div class="col-sm-10">
                                {!! Form::number('actual_margin', 0, ['min' => '0', 'required' => true, 'class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Decimal Selling</label>
                            <div class="col-sm-10">
                                <div class="d-flex">
                                    <div class="form-check form-check-inline" style="margin-right:10px;">
                                        <input class="form-check-input" type="checkbox" name="statusdecimal"
                                            id="statusdecimal" value="1">
                                        <label class="form-check-label" for="statusdecimal">Has item count</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group" id="itemCountGroup" style="display: none">
                            <label for="inputEmail3" class="col-sm-2 control-label">Item Count</label>
                            <div class="col-sm-10">
                                {!! Form::number('item_count', null, [
                                    'min' => '1',
                                    'required' => false,
                                    'class' => 'form-control',
                                    'readonly' => false,
                                    'id' => 'item_count',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Max Order Quantity (CAP)</label>
                            <div class="col-sm-10">
                                {!! Form::number('max_order_quantity', null, ['min' => '0', 'class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary submitMe" name="current_step" value="1"
                            style="float: right;">Next</button>
                    </div>
                </div>
            </section>

            <section class="content setup-content" id="step-2">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"> Item Information 2 </h3>
                    </div>


                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Tax Category</label>
                            <div class="col-sm-10">
                                {!! Form::select('tax_manager_id', $all_taxes, null, [
                                    'maxlength' => '255',
                                    'placeholder' => 'Please select',
                                    'required' => false,
                                    'class' => 'form-control mlselec6t',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Pack Size</label>
                            <div class="col-sm-10">
                                {!! Form::select('pack_size_id', $PackSize, null, [
                                    'maxlength' => '255',
                                    'placeholder' => 'Please select',
                                    'required' => false,
                                    'class' => 'form-control mlselec6t',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    {{-- <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Location and Store</label>
                    <div class="col-sm-10">
                        {!! Form::select('store_location_id',$locations ,null, ['maxlength'=>'255','placeholder' => 'Please select', 'required'=>false, 'class'=>'form-control mlselec6t']) !!}  
                    </div>
                </div>
            </div>
--}}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Alt Code</label>
                            <div class="col-sm-10">
                                {!! Form::text('alt_code', null, [
                                    'maxlength' => '255',
                                    'placeholder' => 'Alt Code',
                                    'required' => false,
                                    'class' => 'form-control',
                                ]) !!}
                            </div>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Packaged Volume (metres cubed)</label>
                            <div class="col-sm-10">
                                {!! Form::text('packaged_volume', null, [
                                    'maxlength' => '255',
                                    'placeholder' => 'Packaged Volume (metres cubed)',
                                    'required' => false,
                                    'class' => 'form-control',
                                ]) !!}
                            </div>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Gross Weight (KGs)</label>
                            <div class="col-sm-10">
                                {!! Form::text('gross_weight', null, [
                                    'maxlength' => '255',
                                    'placeholder' => 'Gross Weight (KGs)',
                                    'required' => false,
                                    'class' => 'form-control',
                                ]) !!}
                            </div>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Net Weight (KGs)</label>
                            <div class="col-sm-10">
                                {!! Form::text('net_weight', null, [
                                    'maxlength' => '255',
                                    'placeholder' => 'Net Weight (KGs))',
                                    'required' => false,
                                    'class' => 'form-control',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">HS Code</label>
                            <div class="col-sm-10">
                                {!! Form::text('hs_code', null, [
                                    'maxlength' => '100',
                                    'placeholder' => 'HS Code',
                                    'required' => false,
                                    'class' => 'form-control',
                                ]) !!}
                                <span class="error-message" style="color:red;display:none;font-weight:bold;"></span>
                            </div>
                        </div>
                    </div>
                    {{--
            <div class="box-body">
                <div class="form-group">
                    <label for="restocking" class="col-sm-2 control-label"> Restocking</label>
                    <div class="col-sm-10">
                        <div>
                            {!! Form::radio('restocking_method', '1', true) !!} <span> I buy this product </span>
                        </div>
                        <div>
                            {!! Form::radio('restocking_method', '2') !!} <span> I make this product </span>
                        </div>
                    </div>
                </div>
            </div>
--}}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Block Sales</label>
                            <div class="col-sm-10">
                                {!! Form::checkbox('block_this') !!}
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Image</label>
                            <div class="col-sm-10">
                                {!! Form::file('image', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button type="button" class="btn btn-primary" style="float: left;"
                            onclick="$('.step-buttons1').trigger('click'); return false;">Previous</button>
                        {{-- <button type="submit" class="btn btn-primary submitMe" name="current_step" value="2"
                            style="float: right;">Next</button> --}}
                            <button type="submit" class="btn btn-primary submitMe" name="current_step" value="3"
                            style="float: right;">Save & Finish</button>

                    </div>
                </div>
            </section>

            {{-- <section class="content setup-content" id="step-3">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"> Additional Information </h3>
                    </div>
                    <div class="col-md-12 no-padding-h">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-2 control-label">Block Sales</label>
                                <div class="col-sm-10">
                                    {!! Form::checkbox('block_this') !!}
                                </div>
                            </div>
                        </div>
                        <h3 class="box-title">
                            <button type="button" class="btn btn-danger btn-sm addNewrow"><i class="fa fa-plus"
                                    aria-hidden="true"></i></button>
                            Assign Inventory Items
                        </h3>
                        <div>
                            <span class="destination_item"></span>
                        </div>
                        <table class="table table-bordered table-hover assigneditems">
                            <thead>
                                <tr>
                                    <th>
                                        Destination Item
                                    </th>
                                    <th>
                                        Conversion factor
                                    </th>
                                    <th>
                                        ##
                                    </th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <div class="box-footer">
                        <button type="button" class="btn btn-primary" style="float: left;"
                            onclick="$('.step-buttons2').trigger('click'); return false;">Previous</button>

                        <button type="submit" class="btn btn-primary submitMe" name="current_step" value="3"
                            style="float: right;">Save & Finish</button>
                    </div>
                </div>
            </section> --}}
        </form>
    </div>
@endsection

@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('css/multistep-form.css') }}">
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
"
        class="loder">
        <div class="loader" id="loader-1"></div>
    </div>
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/multistep-form.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {

            // Function to calculate minimum margin
            function calculateMinMargin() {
                var sellingPrice = parseFloat($('[name="selling_price"]').val());
                var standardCost = parseFloat($('[name="standard_cost"]').val());
                var marginType = $('[name="margin_type"]:checked').val();
                var difference = sellingPrice - standardCost;
                var minMargin;
                if (marginType === '1') {
                    minMargin = (difference / standardCost) * 100;
                } else {
                    minMargin = difference;
                }
                $('[name="actual_margin"]').val(minMargin.toFixed(2));
            }
            $('[name="margin_type"]').on('change', function() {
                calculateMinMargin();
            });
            $('#selling_price').on('change', function() {
                calculateMinMargin();
            });
            $('#standard_cost').on('change', function() {
                calculateMinMargin();
            });

            calculateMinMargin();
        });
        $(function() {
            $(".mlselec6t").select2();
            $(".selector_selects2").select2();
            $('.wa_inventory_category_id').change(function(e) {
                $('.item_sub_category_id option:selected').remove();
            });
            $('.item_sub_category_id').select2({
                placeholder: 'Select Sub Category',
                ajax: {
                    url: '{{ route('inventory-categories.search_sub_categories') }}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            id: $('.wa_inventory_category_id option:selected').val()
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

            $('#statusdecimal').change(function() {
                if ($(this).is(":checked")) {
                    $('#itemCountGroup').show();
                    $('#item_count').prop('required', true);
                } else {
                    $('#itemCountGroup').hide();
                    $('#item_count').prop('required', false).val('');
                }
            });
        });
        $(document).on('click', '.deletemyrow', function() {
            $(this).parents('tr').remove();
            return false;
        });
        var item = '<tr>' +
            '<td>' +
            '<select name="destination_item[0]" class="form-control destination_item destination_items"></select>' +
            '</td>' +
            '<td>' +
            '<input type="text" name="conversion_factor[0]" class="form-control conversion_factor">' +
            '</td>' +
            '<td>' +
            '<button type="button" class="btn btn-danger deletemyrow"><i class="fa fa-trash" aria-hidden="true"></i></button>' +
            '</td>' +
            '</tr>';
        $(document).on('click', '.addNewrow', function() {
            $(".destination_items").select2('destroy');
            $('.assigneditems tbody').append(item);
            var assigneditems = $('.assigneditems tbody tr');
            $.each(assigneditems, function(indexInArray, valueOfElement) {
                $(this).find('.destination_item').attr('name', 'destination_item[' + indexInArray + ']');
                $(this).find('.conversion_factor').attr('name', 'conversion_factor[' + indexInArray + ']');
            });
            destinated_item();
        });
        //maintain-items.inventoryDropdown

        var destinated_item = function() {
            $(".destination_items").select2({
                ajax: {
                    url: "{{ route('maintain-items.inventoryDropdown') }}",
                    dataType: 'json',
                    type: "GET",
                    data: function(term) {
                        return {
                            q: term.term
                        };
                    },
                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
        }
        destinated_item();

        $(document).ready(function() {
            $('.submitMe').click(function(event) {

                var taxCategoryId = $('select[name="tax_manager_id"]').val();
                var taxCategoryName = $('select[name="tax_manager_id"] option:selected').text();

                var hsCode = $('input[name="hs_code"]').val().trim();
                var zeroRatedId = '2';
                var vatExemptedId = '3';

                var errorMessage =
                    'HS Code cannot be 0, null, or an empty string for ZERO RATED or VAT EXEMPTED tax categories.';

                $('.error-message').hide();

                if (taxCategoryId === zeroRatedId || taxCategoryId === vatExemptedId) {
                    if (hsCode === '' || hsCode === null || hsCode === '0') {
                        event.preventDefault();
                        $('input[name="hs_code"]').next('.error-message').text(errorMessage).show();
                        return false;
                    }
                }

                return true;

            });
        });
    </script>
@endsection
