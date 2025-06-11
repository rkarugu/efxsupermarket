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
                        <h3 class="box-title"> {!! $title !!} </h3>
                    </div>
                    <div class="col-sm-3">
                        <div align="right">
                            <a href="{!! route('admin.downloadExcel.approval', $status) !!}" class="btn btn-primary">Excel</a>
                            <button class="btn btn-success" style="display: none;" onclick="approveSelectedItems()">Approve
                                Selected</button>
                        </div>
                    </div>
                </div>
                <br>
                @include('message')
                <div class="col-md-12 no-padding-h">
                    <form id="approvalForm" action="{{ route('approve_bulk_items') }}" method="POST">
                        @csrf
                        <table class="table table-bordered table-hover" id="create_datatable">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="select-all-checkbox" onclick="toggleSelectAll(this)">
                                    </th>
                                    <th>Requested By</th>
                                    <th>Requested On</th>
                                    <th>Stock ID Code</th>
                                    <th>Title</th>
                                    <th>Item Category</th>
                                    <th>Pack Size</th>
                                    <th class="noneedtoshort">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingNewApprovalStatuses as $item)
                                    @php
                                        $new_data = json_decode($item->new_data);
                                    @endphp
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="selectedItems[]"
                                                value="{{ $new_data->stock_id_code }}">
                                        </td>
                                        <td> {{ $item->approvalBy->name }} </td>
                                        <td>{{ $item->created_at }}</td>
                                        <td>{{ $new_data->stock_id_code }}</td>
                                        <td>{{ $new_data?->title }}</td>
                                        <td>{{ getItemCategory($new_data->wa_inventory_category_id)->category_description }}
                                        </td>
                                        <td>{{ getItemPackSize($new_data->pack_size_id)?->title }}</td>
                                        <td><span class="span-action"><a
                                                    href="{{ route('item-new-approval-show', $item->id) }}"
                                                    title="View"><i class="fas fa-eye" aria-hidden="true"></i></a></span>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </form>
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
        $(document).ready(function() {
            $('.wa_inventory_category_id').select2();
            $('#supplier-id').select2();

            toggleApproveButton();

        });
    </script>
    <script type="text/javascript">
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

        function toggleSelectAll(checkbox) {
            var subCheckboxes = document.querySelectorAll('input[name="selectedItems[]"]');
            subCheckboxes.forEach(function(subCheckbox) {
                subCheckbox.checked = checkbox.checked;
            });

            toggleApproveButton();
        }


        function toggleApproveButton() {
            var checkboxes = document.querySelectorAll('input[name="selectedItems[]"]');
            var approveButton = document.querySelector('button.btn-success');
            var isChecked = Array.from(checkboxes).some(function(checkbox) {
                return checkbox.checked;
            });

            approveButton.style.display = isChecked ? 'inline-block' : 'none';
        }

        function approveSelectedItems() {
            var form = document.getElementById('approvalForm');
            var checkboxes = form.querySelectorAll('input[name="selectedItems[]"]:checked');
            var itemIds = Array.from(checkboxes).map(function(checkbox) {
                return checkbox.value;
            });

            if (itemIds.length === 0) {
                swal("Error", "Please select at least one item to approve.", "error");
                return;
            }

            form.submit();
        }

        toggleApproveButton();

        var checkboxes = document.querySelectorAll('input[name="selectedItems[]"]');
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', toggleApproveButton);
        });
    </script>
@endsection
