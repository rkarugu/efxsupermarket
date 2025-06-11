@extends('layouts.admin.admin')
@section('content')
    <div class="">

        {!! Form::model($row, [
                'method' => 'PATCH',
                'route' => [$model . '.update', $row->id],
                'class' => 'validate form-horizontal',
                'enctype' => 'multipart/form-data',
            ]) !!}
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Additional Information </h3>
            </div>

            <div class="col-md-12">
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Block Sales</label>
                        <div class="col-sm-10">
                            {!! Form::checkbox('block_this') !!}
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Selling Price Inc Vat</label>
                            <div class="col-sm-10">
                                {!! Form::number('selling_price', $row->selling_price, ['min' => '0', 'required' => true, 'class' => 'form-control','readonly']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Standard Cost</label>
                            <div class="col-sm-10">
                                {!! Form::number('standard_cost', $row->standard_cost, ['min' => '0', 'required' => true, 'class' => 'form-control','readonly']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Branch</label>
                        <div class="col-sm-10">
                            {!! Form::select('location_id', $locations, null, [
                          'maxlength' => '255',
                          'placeholder' => 'Please select',
                          'required' => true,
                          'class' => 'form-control mlselec6t',
                      ]) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Quantity</label>
                        <div class="col-sm-10">
                            {!! Form::number('quantity', 1, ['min' => '1', 'required' => true, 'class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2  control-label" for="name">Start Time</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control" id="start_time" name="start_time" required>
                            <span class="error-message" style="color:red;display:none;font-weight:bold;"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2  control-label" for="name">End Time</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control" id="end_time" name="end_time" required>
                            <span class="error-message" style="color:red;display:none;font-weight:bold;"></span>
                        </div>
                    </div>
                </div>
                <h3 class="box-title">
                    <button type="button" class="btn btn-danger btn-sm addNewrow">
                        <i class="fa fa-plus" aria-hidden="true"></i></button>
                    Assign Hamper Items
                </h3>
                <div>
                    <span class="destination_item"></span>
                </div>
                <table class="table table-bordered table-hover assigneditems">
                    <thead>
                    <tr>
                        <th>
                            Hamper Item
                        </th>
                        <th>
                            Supplier
                        </th>
                        <th>
                            Cost
                        </th>
                        <th>
                            Selling Price
                        </th>
                        <th>
                            ##
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @if ($row->hamperItem->isNotEmpty())
                        @foreach ($row->hamperItem as $key => $item)
                            <tr>
                                <td>
                                    <select name="destination_item[{{ $key }}]"
                                            class="form-control destination_item destination_items">
                                        <option value="{{ $item->wa_inventory_item_id }}">
                                            {{ $item->item->title }}
                                        </option>
                                    </select>
                                </td>
                                <td>

                                    {!! Form::select("supplier_item[{{ $key }}]", $suppliers, $item->supplier_id, [
                                    'maxlength' => '255',
                                    'placeholder' => 'Please select',
                                    'required' => false,
                                    'class' => 'form-control mlselec6t',
                                ]) !!}
                                </td>
                                <td>
                                    <input type="text" name="cost_item[{{ $key }}]"
                                           class="form-control cost_item"
                                           value="{{ $item->standard_cost }}">
                                </td>
                                <td>
                                    <input type="text" name="selling_price_item[{{ $key }}]"
                                           class="form-control selling_price_item"
                                           value="{{ $item->selling_price }}">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger deletemyrow"><i
                                                class="fa fa-trash" aria-hidden="true"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
            <div class="box-footer">

                <button type="submit" class="btn btn-primary submitMe" name="current_step" value="3"
                        style="float: right;">Save & Finish</button>
            </div>
        </div>

        {!! Form::close() !!}
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
            display:none; "
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
                $('[name="percentage_margin"]').val(minMargin.toFixed(2));
            }
            $('[name="selling_price"], [name="standard_cost"], [name="margin_type"]').on('change', function() {
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
            '<select name="supplier_item[0]" class="form-control supplier_item"></select>' +
            '</td>' +
            '<td>' +
            '<input type="text" name="cost_item[0]" class="form-control cost_item">' +
            '</td>' +
            '<td>' +
            '<input type="text" name="selling_price_item[0]" class="form-control selling_price_item">' +
            '</td>' +
            '<td>' +
            '<button type="button" class="btn btn-danger deletemyrow"><i class="fa fa-trash" aria-hidden="true"></i></button>' +
            '</td>' +
            '</tr>';

        $(document).on('click', '.addNewrow', function() {
            $('.assigneditems tbody').append(item);
            var assigneditems = $('.assigneditems tbody tr');
            assigneditems.each(function(index) {
                $(this).find('.destination_item').attr('name', 'destination_item[' + index + ']');
                $(this).find('.supplier_item').attr('name', 'supplier_item[' + index + ']');
                $(this).find('.cost_item').attr('name', 'cost_item[' + index + ']');
                $(this).find('.selling_price_item').attr('name', 'selling_price_item[' + index + ']');
            });
            initSelect2();  // Reinitialize Select2 after appending the new row
        });

        var initSelect2 = function() {
            $(".destination_items").select2({
                ajax: {
                    url: "{{ route('hampers.inventoryDropdown.search') }}",
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

            // Initialize supplier_items select2
            $(".supplier_items").each(function() {
                $(this).select2({
                    ajax: {
                        url: "{{ route('hampers.suppliers.search') }}",
                        dataType: 'json',
                        type: "GET",
                        data: function(term) {
                            var selectedItemId = $(this).closest('tr').find('.destination_item').val();
                            return {
                                q: term.term,
                                item_id: selectedItemId
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
            });
        };

        $(document).on('change', '.destination_item', function() {
            var row = $(this).closest('tr');
            var selectedItemId = $(this).val();
            var supplierSelect = row.find('.supplier_item');

            // Destroy current select2 and reinitialize with updated data
            supplierSelect.empty().select2({
                ajax: {
                    url: "{{ route('hampers.suppliers.search') }}",
                    dataType: 'json',
                    type: "GET",
                    data: function(term) {
                        return {
                            q: term.term,
                            item_id: selectedItemId
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
        });


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
