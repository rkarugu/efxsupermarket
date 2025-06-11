@extends('layouts.admin.admin')

@section('content')
    <style>
        .span-action {

            display: inline-block;
            margin: 0 3px;

        }
    </style>
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                <div class="row">
                    <div class="col-md-9">

                        <form method="post" action="{!! route('admin.table.exportCategoryPrice') !!}">
                            {{ csrf_field() }}
                            <div class="col-sm-3">
                                {!! Form::select('wa_inventory_category_id', getInventoryCategoryList(), null, [
                                    'maxlength' => '255',
                                    'placeholder' => 'Please select',
                                    'required' => true,
                                    'class' => 'form-control mlselec6t wa_inventory_category_id',
                                ]) !!}
                            </div>

                            <div class="col-sm-1">
                                <button type="submit" class="btn btn-success">Excel</button>
                            </div>
                        </form>

                        <form method="post" action="{!! route('admin.table.exportCategoryPrice') !!}">
                            {{ csrf_field() }}
                            <div class="col-sm-3">
                                {!! Form::select('wa_inventory_category_id', getInventoryCategoryList(), null, [
                                    'maxlength' => '255',
                                    'placeholder' => 'Select Category',
                                    'required' => true,
                                    'class' => 'form-control mlselec6t wa_inventory_category_id',
                                    'id' => 'wa_inventory_category_id',
                                ]) !!}
                            </div>
                            <div class="col-sm-3">
                                {!! Form::select('supplier_id', getInventoryItemsSuppliers(), null, [
                                    'maxlength' => '255',
                                    'placeholder' => 'Select Supplier',
                                    'required' => true,
                                    'class' => 'form-control mlselec6t wa_inventory_category_id',
                                    'id' => 'supplier_id',
                                ]) !!}
                            </div>
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-success" id="filterBtn">Filter</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-3">
                        <div align="right">
                            @if (isset($permission[$pmodule . '___add']) || $permission == 'superadmin')
                                <a href="{!! route($model . '.create') !!}" class="btn btn-success">Add Item</a>
                            @endif
                            <a href="{!! route('admin.downloadExcel') !!}" class="btn btn-primary">Excel</a>
                        </div>
                    </div>
                </div>
                <br>
                @include('message')
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="dataTable">
                        <thead>
                            <tr>

                                <th>Stock ID Code</th>
                                <th>Title</th>
                                <th>Item Category</th>
                                <th>Pack Size</th>
                                {{-- <th>V Cost</th> --}}
                                <th>Standard Cost</th>
                                {{-- <th>V Price</th> --}}
                                <th>Selling Price</th>
                                <th>QOH</th>
                                <th>QOO</th>
                                <th class="noneedtoshort">Action</th>
                                <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>


    </section>

    <div class="modal " id="manage-stock-model" role="dialog" tabindex="-1" aria-hidden="true" role="dialog"
        aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                {!! Form::open(['route' => 'maintain-items.manage-stock', 'class' => 'validate form-horizontal']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                        Adjust Item Stock
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="box-body">


                    </div>
                </div>

                <div class="modal-footer">
                    <input type="submit" class="btn btn-primary" value="Submit">

                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        Close
                    </button>

                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="modal " id="manage-category-model" role="dialog" tabindex="-1" aria-hidden="true" role="dialog"
        aria-labelledby="myModalLabel1">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                {!! Form::open(['route' => 'maintain-items.manage-category-price', 'class' => 'validate form-horizontal']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel1">
                        Adjust Item Category Price : <span id="moretitle"></span>
                    </h4>
                </div>
                <div class="modal-body">

                    <div class="box-body">


                    </div>
                </div>

                <div class="modal-footer">
                    <input type="submit" class="btn btn-primary" value="Submit">

                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        Close
                    </button>

                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <div class="modal fade" id="price_locations" tabindex="-1" role="dialog" aria-labelledby="price_locations_label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="price_locations_label">Price Locations</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="price_locations_form">
                    <div class="modal-body">
                        <table class="table" style="width: 100%; margin-top: 10px">
                            <tbody>
                            <tr>
                                <th>Standard Cost</th>
                                <td id="std_cost">

                                </td>
                                <th>Selling Price Inc Vat</th>
                                <td id="selling_pr">

                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <div id="validation_errors" class="text-danger mb-3">

                        </div>
                        <table class="table" style="margin-top: 10px">
                            <thead>
                            <tr>
                                <th>Location</th>
                                <th>Selling Price</th>
                                <th>Is Flash</th>
                            </tr>
                            </thead>
                            <tbody id="price_locations_table_body">
                            <!-- Table rows will be dynamically added here -->
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="save_price_locations">Save changes</button>
                        <!-- You can add additional buttons or actions here if needed -->
                    </div>
                </form>
            </div>
        </div>
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
    <link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">
    <style>
        .datepicker {
            z-index: 1600 !important;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/multistep-form.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.wa_inventory_category_id').select2();
            $('#supplier-id').select2();
        });
    </script>
    <script type="text/javascript">
        $(function() {
            var selectedCategoryId;
            var selectedSupplierId;

            $('#filterBtn').on('click', function() {
                selectedCategoryId = $('#wa_inventory_category_id').val();
                selectedSupplierId = $('#supplier_id').val();
                console.log(selectedSupplierId);
                console.log(selectedCategoryId);
                table.ajax.reload();
            });




            var table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                "ajax": {
                    "url": '{!! route('admin.maintain-items-datatable') !!}',
                    "dataType": "json",
                    "type": "POST",
                    // "data": { _token: "{{ csrf_token() }}", category_id: selectedCategoryId}
                    "data": function(d) {
                        // Include the selectedCategoryId in the request data
                        d._token = "{{ csrf_token() }}";
                        d.category_id = selectedCategoryId;
                        d.supplier_id = selectedSupplierId;
                    }

                },
                columns: [{
                        data: 'stock_id_code',
                        name: 'stock_id_code',
                        orderable: false
                    },
                    {
                        data: 'title',
                        name: 'title',
                        orderable: false
                    },
                    {
                        data: 'item_category',
                        name: 'item_category',
                        orderable: false
                    },
                    {
                        data: 'uom',
                        name: 'uom',
                        orderable: false
                    },
                   
                    {
                        data: 'standard_cost',
                        name: 'standard_cost',
                        orderable: false
                    },
                   
                    {
                        data: 'selling_price',
                        name: 'selling_price',
                        orderable: false
                    },
                    {
                        data: 'qauntity',
                        name: 'qauntity',
                        orderable: false
                    },
                    {
                        data: 'qty_on_order',
                        name: 'qty_on_order',
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false
                    },
                ],
                "columnDefs": [{
                        "searchable": false,
                        "targets": 0
                    },
                    {
                        className: 'text-center',
                        targets: [1]
                    },
                ],
                language: {
                    searchPlaceholder: "Search"
                },
            });
        });

        function manageStockPopup(link = "") {


            $('#manage-stock-model').modal('show');
            //$('#manage-stock-model').find(".box-body").html('<img style="width:50px;" src="{{ asset('public/assets/admin/images/loading.gif') }}">');
            $('#manage-stock-model').find(".box-body").load(link);

        }

        function getAndUpdateItemAvailableQuantity(input_obj) {
            location_id = $(input_obj).val();
            if (location_id) {
                stock_id_code = $('#stock_id_code_input').val();
                jQuery.ajax({
                    url: '{{ route('maintain-items.get-available-quantity-ajax') }}',
                    type: 'POST',
                    dataType: "json",
                    data: {
                        location_id: location_id,
                        stock_id_code: stock_id_code
                    },
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#current_qty_available').val(response['available_quantity']);
                    }
                });
            } else {
                $('#current_qty_available').val(0);
            }

        }

        function manageCategoryPopup(link, that) {
            $('#manage-category-model').modal('show');
            //$('#manage-stock-model').find(".box-body").html('<img style="width:50px;" src="{{ asset('public/assets/admin/images/loading.gif') }}">');
            $('#myModalLabel1 #moretitle').html($(that).data('title'));
            $('#manage-category-model').find(".box-body").load(link);

        }

        function getAndUpdateItemAvailableQuantity(input_obj) {
            location_id = $(input_obj).val();
            if (location_id) {
                stock_id_code = $('#stock_id_code_input').val();
                jQuery.ajax({
                    url: '{{ route('maintain-items.get-available-quantity-ajax') }}',
                    type: 'POST',
                    dataType: "json",
                    data: {
                        location_id: location_id,
                        stock_id_code: stock_id_code
                    },
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#current_qty_available').val(response['available_quantity']);
                    }
                });
            } else {
                $('#current_qty_available').val(0);
            }

        }
    </script>
    <script>
        var VForm = new Form();
        $(document).ready(function() {
            $(document).on('click', '.open-modal', function(e) {
                e.preventDefault(); // Prevent the default action of the link

                var targetModal = $(this).attr('data-target'); // Get the target modal ID
                var rowData = $(this).data('row'); // Get the JSON-encoded row data

                console.log(rowData)
                console.log(rowData.standard_cost)
                console.log(rowData.title)

                // Store the modal reference
                var modal = $(targetModal);

                // // Load data when modal is shown
                modal.on('show.bs.modal', function (event) {
                    // Parse the JSON-encoded data back into a JavaScript object
                    var row = rowData.title;
                    $('#std_cost').html(rowData.standard_cost)
                    $('#selling_pr').html(rowData.selling_price)
                    populateTable(rowData);
                });

                // Show the modal
                modal.modal('show');
            });
            let active_item = null
            function populateTable(data) {
                var tableBody = $('#price_locations_table_body');
                tableBody.empty(); // Clear existing rows
                for (var i = 0; i < data.location_prices.length; i++) {
                    active_item = data.id
                    var item = data.location_prices[i];
                    var row = '<tr>' +
                        '<td>' + item.location.location_name + '</td>' +
                        '<td><input type="text" name="selling_price_'+item.location.id+'" class="form-control selling_price" value="' + item.selling_price + '"></td>' +
                        '<td><input type="checkbox" name="is_flash_' + item.location.id + '" class="form-check-input is_flash" ' + (item.is_flash === 1 ? 'checked' : '') + ' ></td>' +
                        '</tr>';
                    tableBody.append(row);
                }

                $('.ends_at').datepicker({
                    format: 'yyyy-mm-dd'
                });
            }
            function captureInputData() {
                var inputData = [];

                $('#price_locations_table_body tr').each(function() {
                    var storeId = $(this).find('input[type="text"]').attr('name').split('_')[2];
                    var sellingPrice = $(this).find('input[type="text"]').val();
                    var isFlash = $(this).find('input[type="checkbox"]').prop('checked');

                    inputData.push({
                        store_id: storeId,
                        selling_price: sellingPrice,
                        is_flash: isFlash
                    });
                });

                return { price_data: inputData };
            }


            $('#save_price_locations').click(function() {
                var inputData = captureInputData();
                generateAjaxRequest(inputData);
            });

            function generateAjaxRequest(data) {
                // Assuming you have your AJAX configuration here
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: 'maintain-items/update-price-per-location/'+active_item,
                    method: 'POST',
                    data: JSON.stringify(data), // Convert data to JSON format
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken // Include CSRF token in the headers
                    },
                    success: function(response) {
                        console.log('sucsess')
                        VForm.successMessage('Prices Updated successful');
                        $('#price_locations').modal('hide');
                        $('#dataTable').DataTable().draw()
                    },
                    error: function(xhr, status, error) {
                        var errors = xhr.responseJSON.errors;
                        if (errors) {
                            // Display validation errors on the modal
                            var errorMessages = Object.values(errors).flat().join('<br>');
                            $('#validation_errors').html(errorMessages);
                            // $.each(errors, function(field, messages) {
                            //     var storeNumber = field.split('_').pop(); // Extract store number from the field name
                            //     console.log(field)
                            //     var errorMessage = messages[0].replace(':store_id', storeNumber); // Replace placeholder with store number
                            //     var inputField = $('input[name="name_' + storeNumber + '"]');
                            //     var errorContainer = $('<span class="text-danger">' + errorMessage + '</span>');
                            //     inputField.closest('td').append(errorContainer);
                            // });
                        } else {
                            // Handle other types of errors
                            console.error(error);
                        }
                    }
                });
            }
        });
    </script>

@endsection
