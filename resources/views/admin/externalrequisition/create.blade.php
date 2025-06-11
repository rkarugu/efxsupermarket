@extends('layouts.admin.admin')
@section('content')
    <form method="POST" action="{{ route('external-requisitions.store') }}" accept-charset="UTF-8" class="submitMe"
        enctype="multipart/form-data" novalidate="novalidate">

        <section class="content">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"> Add Branch Requisitions </h3>
                </div>
                @include('message')
                {{ csrf_field() }}
                <?php
                $user = getLoggeduserProfile();
                $purchase_no = getCodeWithNumberSeries('EXTERNAL REQUISITIONS');
                $default_branch_id = $user->restaurant_id;
                $default_department_id = $user->wa_department_id;
                $default_unit_of_measures_id = $user->wa_unit_of_measures_id;
                $default_wa_location_and_store_id = $user->wa_location_and_store_id;
                $requisition_date = date('Y-m-d');
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
                                        {!! Form::text('emp_name', $user->name, [
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
                                        {!! Form::text('requisition_date', $requisition_date, [
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
                                        <select name='wa_priority_level_id' class='form-control wa_priority_level_id'>
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
                                        {!! Form::text('note', null, ['maxlength' => '255', 'placeholder' => '', 'class' => 'form-control']) !!}
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
                                        {!! Form::select('wa_department_id', getDepartmentDropdown($user->restaurant_id), $default_department_id, [
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
                                            $default_wa_location_and_store_id,
                                            [
                                                'class' => 'form-control store_location_id',
                                                'required' => true,
                                                'placeholder' => 'Please select store',
                                                'disabled' => true,
                                            ],
                                        ) !!}
                                        <input type="hidden" value="{{ $default_wa_location_and_store_id }}"
                                            name="store_location_id">
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
                                            'class' => 'form-control wa_unit_of_measures_id',
                                            'required' => true,
                                            'placeholder' => 'Please select bin location',
                                            'disabled' => true,
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class = "row">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Supplier</label>
                                    <div class="col-sm-6">
                                        <select name="wa_supplier_id" id="wa_supplier_id"
                                            class="form-control wa_supplier_id mlselec6t" required>
                                            <option value="" selected> Select supplier </option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier['id'] }}"> {{ $supplier['name'] }} </option>
                                            @endforeach
                                        </select>
                                        <!-- {!! Form::select('wa_supplier_id', getSuppliersForReorderWithBin($default_unit_of_measures_id), null, [
                                            'class' => 'form-control wa_supplier_id mlselec6t',
                                            'required' => true,
                                            'placeholder' => 'Please select supplier',
                                        ]) !!}  -->
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
                        <button type="button" class="btn btn-danger btn-sm addNewrow"
                            style="position: fixed;bottom: 30%;left:4%;"><i class="fa fa-plus"
                                aria-hidden="true"></i></button>

                        <table class="table table-bordered table-hover" id="mainItemTable">
                            <thead>
                                <tr>
                                    <th>Selection</th>
                                    <th>Description</th>
                                    <th style="width: 90px;">Unit</th>
                                    <th style="width: 90px;">Order QTY</th>
                                    {{-- <th>Tonnage</th> --}}
                                    <th>QOO</th>
                                    <th>SOH</th>
                                    <th>Sales</th>
                                    <th>Reorder Level</th>
                                    <th>Max Stock</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <input type="text" class="testIn form-control makemefocus" name="item_id['0']">
                                        <div class="textData" style="width: 100%;position: relative;z-index: 99;"></div>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    {{-- <td></td> --}}
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><button type="button" class="btn btn-danger btn-sm deleteparent"><i
                                                class="fa fa-trash" aria-hidden="true"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-12">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary getItemsBtn">Run Out Of Stock Items</button>
                            <button type="submit" class="btn btn-primary submitbtn">Send Request</button>
                        </div>
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
"
        class="loder">
        <div class="loader" id="loader-1"></div>
    </div>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
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
        // $(document).on('keyup','.item_quantity_max_stock',function(){
        //     $(this).parent().find('.error_validation').remove();
        //     $('.submitbtn').attr('disabled',false);
        //     if(parseFloat($(this).val()) > parseFloat($(this).data('max_stock'))){
        //         $(this).parent().append('<span style="color:red" class="error_validation">Qty cannot be greater than the Max Stock</span>');
        //         $('.submitbtn').attr('disabled',true);
        //     }
        // });
        $(function() {
            $('.getItemsBtn').click(function(e) {
                e.preventDefault();

                let getItemsBtn = $(this);
                let submitBtn = $('.submitbtn');

                let originalText = getItemsBtn.text();

                getItemsBtn.prop('disabled', true).text('Processing...');
                submitBtn.prop('disabled', true);

                let sup = $('.wa_supplier_id option:selected').val();
                let store = $('.store_location_id option:selected').val();
                let bin = $('.wa_unit_of_measures_id option:selected').val();
                if (!sup || sup == "") {
                    form.errorMessage('Select supplier to get inventory item');
                    getItemsBtn.prop('disabled', false).text(originalText);
                    submitBtn.prop('disabled', false);
                    return false;
                }

                $.ajax({
                    type: "GET",
                    url: "{{ route('external-requisitions.getOutOfStockItems') }}",
                    data: {
                        'supplier_id': sup,
                        'store_id': store,
                        'bin_location_id': bin
                    },
                    success: function(response) {
                        $('#mainItemTable tbody').html("");
                        $.each(response, function(index, item) {
                            if (parseFloat(item.quantity) < parseFloat(item
                                    .re_order_level)) {

                                $('#mainItemTable tbody').append('<tr>' +
                                    '<td><input type="hidden" name="item_id[' + item
                                    .id + ']" class="itemid" value="' + item.id +
                                    '">' +
                                    '<input style="padding: 3px 3px;"  type="text" class="testIn form-control" value="' +
                                    item.stock_id_code + '">' +
                                    '<div class="textData" style="width: 100%;position: relative;z-index: 99;"></div>' +
                                    '</td>' +
                                    '<td>' + item.description + '</td>' +
                                    '<td>' + (item.pack_size.title ?? NULL) +
                                    '</td>' +
                                    '<td><input style="padding: 3px 3px;" value="' +
                                    (parseFloat(item.max_stock_f) - parseFloat(item
                                        .quantity)) + '" data-max_stock="' + item
                                    .max_stock_f +
                                    '" type="text" name="item_quantity[' + item.id +
                                    ']" data-id="' + item.id +
                                    '"  class="quantity item_quantity_max_stock form-control"></td>' +
                                    '<td>' + (item.qty_on_order) + '</td>' +
                                    '<td>' + (item.quantity) + '</td>' +
                                    '<td>' + (item.total_sales) + '</td>' +
                                    '<td>' + item.re_order_level + '</td>' +
                                    '<td>' + item.max_stock_f + '</td>' +
                                    '<td><button type="button" class="btn btn-danger btn-sm deleteparent"><i class="fa fa-trash" aria-hidden="true"></i></button></td>' +
                                    '</tr>'
                                );
                            }

                        })
                    },
                    error: function(xhr) {
                        let errorMessage = 'There was an error processing your request.';
                        try {
                            errorMessage = xhr.responseJSON.error;
                        } catch (err) {}
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                        });
                        getItemsBtn.prop('disabled', false).text(originalText);
                        submitBtn.prop('disabled', false);
                    },
                    complete: function() {
                        form.successMessage('Data retrieved successfully');
                        getItemsBtn.prop('disabled', false).text(originalText);
                        submitBtn.prop('disabled', false);
                    }
                });
            });

            $('.submitbtn').on('click', function(e) {
                e.preventDefault();

                let submitBtn = $('.submitbtn');
                let getItemsBtn = $('.getItemsBtn');

                let originalSubmitText = submitBtn.text();
                let originalGetItemsText = getItemsBtn.text();

                submitBtn.prop('disabled', true).text('Processing...');
                getItemsBtn.prop('disabled', true);

                $.ajax({
                    type: "POST",
                    url: "{{ route('external-requisitions.store') }}",
                    data: new FormData($(this).parents('form')[0]),
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.result === 1) {
                            form.successMessage('Form submitted successfully!');
                        } else {
                            let errorMessage = '';
                            if (response.message) {
                                $.each(response.message, function(key, value) {
                                    errorMessage += value.join('<br>') + '<br>';
                                });
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                html: errorMessage,
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage =
                            'There was an error processing your request.';
                        try {
                            errorMessage = xhr.responseJSON.error;
                        } catch (err) {}
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                        });
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).text(originalSubmitText);
                        getItemsBtn.prop('disabled', false).text(originalGetItemsText);
                    }
                });
            });
            
            $(".mlselec6t").select2();

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
        });
        $(document).ready(function() {
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
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });

        var valueTest = null;
        $(document).on('keyup keypress click', '.testIn', function(e) {
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

                if (vale.length >= 3) {
                    var store_location_id = $('.store_location_id option:selected').val();
                    $.ajax({
                        type: "GET",
                        url: "{{ route('purchase-orders.inventoryItems') }}",
                        data: {
                            'search': vale,
                            'store_location_id': store_location_id,
                            'supplier_id': $("#wa_supplier_id").val()
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
            var sup = $('.wa_supplier_id option:selected').val();
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
                $.ajax({
                    type: "GET",
                    url: "{{ route('purchase-orders.getInventryItemDetailsExtension') }}",
                    data: {
                        'id': $this.data('id'),
                        'wa_supplier_id': sup,
                        'store_location_id': {{ $default_wa_location_and_store_id }}
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
                '<td><button class="btn btn-danger btn-sm deleteparent"><i class="fa fa-trash" aria-hidden="true"></i></button></td>' +
                '</tr>');
            makemefocus();
        });
    </script>
@endsection
