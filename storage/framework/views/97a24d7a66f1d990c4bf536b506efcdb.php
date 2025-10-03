<?php $__env->startSection('content'); ?>
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
                    

                </div>
            </div>
        </div>
        <?php echo Form::model($row, [
            'method' => 'PATCH',
            'route' => [$model . '.update', $row->slug],
            'class' => 'validate',
            'enctype' => 'multipart/form-data',
        ]); ?>


        <section class="content setup-content" id="step-1">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"> Item Information </h3>
                </div>
                <?php echo $__env->make('message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php echo e(csrf_field()); ?>

                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Stock ID Code</label>
                        <div class="col-sm-10">
                            <?php echo Form::text('stock_id_code', null, [
                                'maxlength' => '255',
                                'placeholder' => 'Stock ID Code',
                                'disabled' => true,
                                'class' => 'form-control',
                            ]); ?>

                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Item Title</label>
                        <div class="col-sm-10">
                            <?php echo Form::text('title', null, [
                                'maxlength' => '255',
                                'placeholder' => 'Item Title',
                                'required' => true,
                                'class' => 'form-control',
                            ]); ?>

                        </div>
                    </div>
                </div>


                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Description</label>
                        <div class="col-sm-10">
                            <?php echo Form::text('description', null, [
                                'maxlength' => '255',
                                'placeholder' => 'Description',
                                'required' => true,
                                'class' => 'form-control',
                            ]); ?>

                        </div>
                    </div>
                </div>



                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Inventory Category</label>
                        <div class="col-sm-10">
                            <?php echo Form::select('wa_inventory_category_id', getInventoryCategoryList(), null, [
                                'maxlength' => '255',
                                'placeholder' => 'Please select',
                                'required' => true,
                                'class' => 'form-control wa_inventory_category_id mlselec6t',
                            ]); ?>

                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Inventory Sub Category</label>
                        <div class="col-sm-10">
                            <?php echo Form::select('item_sub_category_id', [$row->item_sub_category_id => @$row->sub_category->title], null, [
                                'maxlength' => '255',
                                'placeholder' => 'Please select',
                                'required' => true,
                                'class' => 'form-control item_sub_category_id',
                            ]); ?>

                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Preferred Supplier</label>
                        <div class="col-sm-10">
                            <?php echo Form::select('suppliers[]', $suppliers, $row->inventory_item_suppliers->pluck('id')->toArray(), [
                                'class' => 'form-control selector_selects2',
                                'required' => true,
                                'multiple' => true,
                            ]); ?>

                        </div>
                    </div>
                </div>
                
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Selling Price Inc Vat</label>
                        <div class="col-sm-10">
                            <?php echo Form::number('selling_price', null, [
                                'min' => '0',
                                'required' => true,
                                'class' => 'form-control',
                                'id' => 'selling_price',
                                'readonly' => true,
                            ]); ?>

                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Standard Cost</label>
                        <div class="col-sm-10">
                            <?php echo Form::number('standard_cost', null, [
                                'min' => '0',
                                'required' => true,
                                'class' => 'form-control',
                                'id' => 'standard_cost',
                                'readonly' => true,
                            ]); ?>

                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Price List Cost</label>
                        <div class="col-sm-10">
                            <?php echo Form::number('price_list_cost', null, [
                                'min' => '0',
                                'required' => true,
                                'class' => 'form-control',
                                'id' => 'price_list_cost',
                                'readonly' => true,
                            ]); ?>

                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Margin Type</label>
                        <div class="col-sm-10">
                            <div class="d-flex">
                                <div class="form-check form-check-inline" style="margin-right:10px;">
                                    <input class="form-check-input" type="radio" name="margin_type" id="marginPercentage"
                                        value="1" <?php if($row->margin_type): ?> checked <?php endif; ?> required>
                                    <label class="form-check-label" for="marginPercentage">Percentage</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="margin_type" id="marginValue"
                                        value="0" <?php if(!$row->margin_type): ?> checked <?php endif; ?>>
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
                            <?php echo Form::number('percentage_margin', null, ['min' => '0', 'required' => true, 'class' => 'form-control']); ?>

                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Actual Margin</label>
                        <div class="col-sm-10">
                            <?php echo Form::number('actual_margin', null, [
                                'min' => '0',
                                'required' => true,
                                'class' => 'form-control',
                                'id' => 'actual_margin',
                            ]); ?>

                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-10">
                            <div class="d-flex">
                                <div class="form-check form-check-inline" style="margin-right:10px;">
                                    <input class="form-check-input" type="radio" name="status" id="statusActive"
                                        value="1" <?php if($row->status): ?> checked <?php endif; ?> required>
                                    <label class="form-check-label" for="statusActive">Active</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="statusRejected"
                                        value="0" <?php if(!$row->status): ?> checked <?php endif; ?> <?php if($row->qoh): ?> disabled <?php endif; ?>>
                                    <label class="form-check-label" for="statusRejected">Retired</label>
                                </div>
                            </div>
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
                                        id="statusdecimal" value="1"
                                        <?php if($row->item_count != null): ?> checked <?php endif; ?>>
                                    <label class="form-check-label" for="statusdecimal">Has item count</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group" id="itemCountGroup"
                        <?php if($row->item_count == null): ?> style="display: none" <?php endif; ?>>
                        <label for="inputEmail3" class="col-sm-2 control-label">Item Count</label>
                        <div class="col-sm-10">
                            <?php echo Form::number('item_count', null, [
                                'min' => '1',
                                'required' => false,
                                'class' => 'form-control',
                                'readonly' => false,
                                'id' => 'item_count',
                            ]); ?>

                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Max Selling Quantity(CAP)</label>
                        <div class="col-sm-10">
                            <?php echo Form::number('max_order_quantity', null, ['min' => '0', 'class' => 'form-control']); ?>

                        </div>
                    </div>
                </div>
                <div class="box-footer">

                    <button type="submit" class="btn btn-primary submitMe" name="current_step" style="float: right;"
                        value="1">Next</button>
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
                            <?php echo Form::select('tax_manager_id', $all_taxes, null, [
                                'maxlength' => '255',
                                'placeholder' => 'Please select',
                                'required' => true,
                                'class' => 'form-control mlselec6t',
                            ]); ?>

                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Pack Size</label>
                        <div class="col-sm-10">
                            <?php echo Form::select('pack_size_id', $PackSize, null, [
                                'maxlength' => '255',
                                'placeholder' => 'Please select',
                                'required' => false,
                                'class' => 'form-control mlselec6t',
                            ]); ?>

                        </div>
                    </div>
                </div>
                
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Alt Code</label>
                        <div class="col-sm-10">
                            <?php echo Form::text('alt_code', null, [
                                'maxlength' => '255',
                                'placeholder' => 'Alt Code',
                                'required' => false,
                                'class' => 'form-control',
                            ]); ?>

                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Packaged Volume (metres cubed)</label>
                        <div class="col-sm-10">
                            <?php echo Form::text('packaged_volume', null, [
                                'maxlength' => '255',
                                'placeholder' => 'Packaged Volume (metres cubed)',
                                'required' => false,
                                'class' => 'form-control',
                            ]); ?>

                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Gross Weight (KGs)</label>
                        <div class="col-sm-10">
                            <?php echo Form::text('gross_weight', null, [
                                'maxlength' => '255',
                                'placeholder' => 'Gross Weight (KGs)',
                                'required' => false,
                                'class' => 'form-control',
                            ]); ?>

                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Net Weight (KGs)</label>
                        <div class="col-sm-10">
                            <?php echo Form::text('net_weight', null, [
                                'maxlength' => '255',
                                'placeholder' => 'Net Weight (KGs))',
                                'required' => false,
                                'class' => 'form-control',
                            ]); ?>

                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">HS Code</label>
                        <div class="col-sm-10">
                            <?php echo Form::text('hs_code', null, [
                                'maxlength' => '100',
                                'placeholder' => 'HS Code',
                                'required' => false,
                                'class' => 'form-control',
                            ]); ?>

                            <span class="error-message" style="color:red;display:none;font-weight:bold;"></span>
                        </div>
                    </div>
                </div>
                
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Block Sales</label>
                        <div class="col-sm-10">
                            <?php echo Form::checkbox('block_this'); ?>

                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Image</label>
                        <div class="col-sm-10">
                            <?php echo Form::file('image', null, ['required' => true, 'class' => 'form-control']); ?>

                            <br>
                            <?php if($row->image): ?>
                                <img width="100px"
                                    height="100px;"src="<?php echo e(asset_public('uploads/inventory_items/' . $row->image)); ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="button" class="btn btn-primary" style="float: left;"
                        onclick="$('.step-buttons1').trigger('click'); return false;">Previous</button>

                    
                    <button type="submit" class="btn btn-primary submitMe" name="current_step" style="float: right;"
                        value="3">Send to Approval</button>

                </div>
            </div>
        </section>

        
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('uniquepagestyle'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/multistep-form.css')); ?>">
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
    <link href="<?php echo e(asset('assets/admin/bower_components/select2/dist/css/select2.min.css')); ?>" rel="stylesheet" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('uniquepagescript'); ?>
    <script src="<?php echo e(asset('js/sweetalert.js')); ?>"></script>
    <script src="<?php echo e(asset('js/multistep-form.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')); ?>"></script>
    <script type="text/javascript">
        $(function() {
            $(".mlselec6t").select2();
            $(".selector_selects2").select2();
            $('.wa_inventory_category_id').change(function(e) {
                $('.item_sub_category_id option:selected').remove();
            });
            $('.item_sub_category_id').select2({
                placeholder: 'Select Sub Category',
                ajax: {
                    url: '<?php echo e(route('inventory-categories.search_sub_categories')); ?>',
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
        /*
                var destinated_item = function(){
                    $(".destination_items").select2({
                        ajax: {
                            url: "<?php echo e(route('maintain-items.inventoryDropdown', ['id' => $row->id])); ?>",
                            dataType: 'json',
                            type: "GET",
                            data: function (term) {
                                return {
                                    q: term.term
                                };
                            },
                            processResults: function (response) {
                                return {
                                    results: response
                                };
                            },
                            cache: true
                        }
                    });
                }*/
        var destinated_item = function() {
            $(".destination_items").select2({
                ajax: {
                    url: "<?php echo e(route('maintain-items.inventoryDropdown', ['id' => $row->id])); ?>",
                    dataType: 'json',
                    type: "GET",
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        var filteredData = data.filter(function(item) {
                            return item.wa_unit_of_measure_id != '0';
                        });
                        return {
                            results: data
                        };
                    },
                    cache: true
                },
                templateResult: function(data) {
                    if (data.loading) return data.text;
                    return $('<span></span>').text(data.text);
                },
                templateSelection: function(data) {
                    return data.text || data.id;
                }
            }).on('change', function(e) {
                var selectedOption = $(this).find('option:selected');
                var waUnitOfMeasureId = selectedOption.attr('data-wa-unit-of-measure-id');

                if (waUnitOfMeasureId == '0') {
                    var flashMessageDiv = document.getElementById('flash-message');
                    if (flashMessageDiv) {
                        flashMessageDiv.innerHTML =
                            '<div class="alert alert-danger">Cannot select item: Please ensure it has a valid unit of measure</div>';
                    }
                } else {

                    var flashMessageDiv = document.getElementById('flash-message');
                    if (flashMessageDiv) {
                        flashMessageDiv.innerHTML = '';
                    }
                }
            });
        }
        destinated_item();

        $(document).ready(function() {
            $('.submitMe').click(function(event) {

                if ($("#step-2").is(":visible")) {
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
                }

                return true;

            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#statusdecimal').change(function() {
                if ($(this).is(":checked")) {
                    $('#itemCountGroup').show();
                    $('#item_count').prop('required', true);
                } else {
                    $('#itemCountGroup').hide();
                    $('#item_count').prop('required', false);
                }
            });
        })
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sellingPriceInput = document.getElementById('selling_price');
            const standardCostInput = document.getElementById('standard_cost');
            const actualMarginInput = document.getElementById('actual_margin');
            const marginTypeInputs = document.querySelectorAll('input[name="margin_type"]');

            function calculateActualMargin() {
                const sellingPrice = parseFloat(sellingPriceInput.value) || 0;
                const standardCost = parseFloat(standardCostInput.value) || 0;
                let actualMargin = 0;

                const selectedMarginType = document.querySelector('input[name="margin_type"]:checked').value;

                if (selectedMarginType == '0') {
                    actualMargin = sellingPrice - standardCost;
                } else if (selectedMarginType == '1') {
                    actualMargin = ((sellingPrice - standardCost) / standardCost) * 100;
                }

                actualMarginInput.value = actualMargin.toFixed(2);
            }

            marginTypeInputs.forEach(input => {
                input.addEventListener('change', calculateActualMargin);
            });

            calculateActualMargin();
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/maintaininvetoryitems/edit.blade.php ENDPATH**/ ?>