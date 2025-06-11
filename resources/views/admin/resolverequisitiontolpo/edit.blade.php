@extends('layouts.admin.admin')
@section('content')
    {!! Form::model($row, [
        'method' => 'PATCH',
        'route' => [$model . '.update', $row->id],
        'class' => 'submitMe',
        'enctype' => 'multipart/form-data',
    ]) !!}

    <section class="content">
        <div class="modal fade" id="confirm-item-removal-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Confirm Requisition Line Item Removal </h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        <p style="font-size: 16px;"> Are you sure you want to remove requisition line item <span
                                id="item-desc"></span>? </p>
                        <input type="hidden" id="subject-item-id">
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>

                            <button type="button" class="btn btn-primary" onclick="removeLineItem();">Yes, Remove
                                Item</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> {!! $title !!} </h3>
            </div>
            @include('message')
            {{ csrf_field() }}

            <?php
            $purchase_no = $row->purchase_no;
            $default_branch_id = $row->restaurant_id;
            $default_department_id = $row->wa_department_id;
            $default_unit_of_measures_id = $row->wa_unit_of_measures_id;
            $requisition_date = $row->requisition_date;
            $getLoggeduserProfileName = $row->getrelatedEmployee->name;
            
            ?>

            <div class = "row">

                <div class = "col-sm-6">
                    <div class = "row">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Purchase Requisition No.</label>
                                <div class="col-sm-7">


                                    {!! Form::text('purchase_no', $purchase_no, [
                                        'maxlength' => '255',
                                        'placeholder' => '',
                                        'required' => true,
                                        'class' => 'form-control',
                                        'readonly' => true,
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class = "row">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Employee name</label>
                                <div class="col-sm-7">
                                    {!! Form::text('emp_name', $getLoggeduserProfileName, [
                                        'maxlength' => '255',
                                        'placeholder' => '',
                                        'required' => true,
                                        'class' => 'form-control',
                                        'readonly' => true,
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class = "row">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Requisition Date</label>
                                <div class="col-sm-7">
                                    {!! Form::text('purchase_date', $requisition_date, [
                                        'maxlength' => '255',
                                        'placeholder' => '',
                                        'required' => true,
                                        'class' => 'form-control',
                                        'readonly' => true,
                                    ]) !!}
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class = "row">

                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Priority Level</label>
                                <div class="col-sm-7">
                                    <select name='wa_priority_level_id' class='form-control wa_priority_level_id'
                                        disabled="disabled">
                                        <option value="{{ $row->wa_priority_level_id }}">{{ @$row->priority_level->title }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class = "row">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Note</label>
                                <div class="col-sm-7">
                                    {!! Form::text('note', $row->note, [
                                        'maxlength' => '255',
                                        'placeholder' => '',
                                        'class' => 'form-control',
                                        'readonly' => true,
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class = "col-sm-6">

                    <div class = "row">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Branch</label>
                                <div class="col-sm-6">
                                    {!! Form::select('restaurant_id', getBranchesDropdown(), $default_branch_id, [
                                        'class' => 'form-control ',
                                        'required' => true,
                                        'placeholder' => 'Please select branch',
                                        'id' => 'branch',
                                        'disabled' => true,
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class = "row">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Department</label>
                                <div class="col-sm-6">
                                    {!! Form::select('wa_department_id', getDepartmentDropdown($default_branch_id), $default_department_id, [
                                        'class' => 'form-control ',
                                        'required' => true,
                                        'placeholder' => 'Please select department',
                                        'id' => 'department',
                                        'disabled' => true,
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class = "row">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Store Location</label>
                                <div class="col-sm-6">
                                    {!! Form::select(
                                        'store_location_id',
                                        getStoreLocationDropdownByBranch($default_branch_id),
                                        $row->wa_store_location_id,
                                        ['class' => 'form-control ', 'required' => true, 'placeholder' => 'Please select store', 'disabled' => true],
                                    ) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class = "row">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Supplier</label>
                                <div class="col-sm-6">
                                    {!! Form::select('wa_supplier_id', getSuppliers(), null, [
                                        'class' => 'form-control ',
                                        'required' => true,
                                        'placeholder' => 'Please select supplier',
                                        'disabled' => true,
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class = "row">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Bin Location</label>
                                <div class="col-sm-6">
                                    {!! Form::select('wa_unit_of_measures_id', getUnitOfMeasureList(), $default_unit_of_measures_id, [
                                        'class' => 'form-control ',
                                        'required' => true,
                                        'placeholder' => 'Please select bin location',
                                        'disabled' => true,
                                    ]) !!}
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
                    <h3 class="box-title"> Requisition Line</h3>

                    <span id = "requisitionitemtable">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Item Code</th>
                                    <th>Item Category</th>
                                    <th>Item Description</th>
                                    <th>Current Stock</th>
                                    <th>Re-Order Level</th>
                                    <th>Max Stock</th>
                                    <th>Quantity Ordered</th>
                                    <th>Movements</th>
                                    <th>Stock in other branches</th>
                                    <th> Actions </th>
                                </tr>
                            </thead>
                            <tbody>

                                @if ($requisition_items && count($requisition_items) > 0)
                                    @foreach ($requisition_items as $getRelatedItem)
                                        <tr>
                                            <td>{{ @$getRelatedItem->item_no }}</td>
                                            <td>{{ $getRelatedItem->getInventoryItemDetail->getInventoryCategoryDetail->category_description }}
                                            </td>
                                            <td>{{ @$getRelatedItem->getInventoryItemDetail?->title }}</td>
                                            <td>{{ manageAmountFormat($getRelatedItem->current_qty) }}</td>
                                            <td>{{ manageAmountFormat($getRelatedItem->re_order_level) }}</td>
                                            <td>{{ manageAmountFormat($getRelatedItem->max_stock_f) }}</td>
                                            <td>{{ manageAmountFormat($getRelatedItem->quantity) }}</td>
                                            <td>{{ manageAmountFormat($getRelatedItem->movements) }}</td>
                                            <td>
                                                <a style="font-size: 16px;" target="_blank"
                                                    href="{{ route('maintain-items.stock-status', $getRelatedItem->getInventoryItemDetail->stock_id_code) }}">
                                                    {{ manageAmountFormat($getRelatedItem->other_branches_qty) }}
                                                </a>
                                            </td>
                                            <td>
                                                <div class="action-button-div">
                                                    <a href="javascript:void(0);" title="Remove Line Item" role="button"
                                                        data-toggle="modal" data-target="#confirm-item-removal-modal"
                                                        data-backdrop="static" data-id="{{ $getRelatedItem->id }}"
                                                        data-desc="{{ $getRelatedItem->getInventoryItemDetail?->title }}">
                                                        <i class="fa fa-trash text-danger fa-lg" aria-hidden="true"></i>
                                                    </a>

                                                    <a href="/admin/n-transfers/create" title="Transfer Stock"
                                                        target="_blank">
                                                        <i class="fas fa-exchange-alt text-primary"
                                                            aria-hidden="true"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="9">Do not have any item in list.</td>

                                    </tr>
                                @endif





                            </tbody>
                        </table>
                    </span>
                </div>



                <div class="col-md-12">
                    <div class="col-md-3"><span>
                            <button type="submit" class="btn btn-success" id="resolve-now">Resolve Now</button>
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
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">

    <style type="text/css">
        .select2 {
            width: 100% !important;
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

        .select2 {
            width: 100% !important;
        }

        #last_total_row td {
            border: none !important;
        }

        #note {
            height: 80px !important;
        }

        .align_float_right {
            text-align: right;
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
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            $(".mlselec6t").select2();


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


        function editRequisitionItem(link) {

            $('#edit-Requisition-Item-Model').find(".modal-content").load(link);
        }
    </script>
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>

    <script type="text/javascript">
        $('#confirm-item-removal-modal').on('show.bs.modal', function(event) {
            let triggeringButton = $(event.relatedTarget);
            let dataValue = triggeringButton.data('id');
            let desc = triggeringButton.data('desc');

            $("#subject-item-id").val(dataValue);
            $("#item-desc").text(desc)
        })

        function removeLineItem() {
            let subjectItemId = $("#subject-item-id").val();
            $('#loader-on').show();
            $.ajax({
                type: "POST",
                url: "{{ route('resolve-requisition-to-lpo.remove-item') }}",
                data: {
                    'id': subjectItemId
                },
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#loader-on').hide();

                    let form = new Form();
                    if (response.result === -1) {
                        form.errorMessage(response.message);
                    } else {
                        form.successMessage(response.message);
                        location.href = response.location;
                    }
                }
            });

            // $(`#delete-item-form-${subjectItemId}`).submit();
        }

        $(document).ready(function() {
            var form = new Form();

            $(document).on('click', '#resolve-now', function(e) {
                e.preventDefault();

                let submitBtn = $(this);
                let originalSubmitBtnText = submitBtn.text();
                let submitForm = $(this).closest('form');

                submitBtn.prop('disabled', true).text('Processing...');

                $.ajax({
                    url: submitForm.attr('action'),
                    method: submitForm.attr('method'),
                    data: submitForm.serialize(),
                    success: function(response) {
                        form.successMessage('LPO Items updated Successfully.');
                        setTimeout(() => {
                            window.location.href = response.redirect_url;
                        }, 3000);
                    },
                    error: function(err) {
                        console.log(err)
                        let errorMessage = '';
                        if (!err?.responseJSON?.result) {
                            return form.errorMessage(err.responseJSON.message);
                        }
                        
                        form.errorMessage(errorMessage);
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).text(originalSubmitBtnText);
                    }
                });
            });
        });
    </script>
@endsection
