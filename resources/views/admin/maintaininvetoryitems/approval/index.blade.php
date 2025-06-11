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
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> {!! $title !!} </h3>
                    <div>
                        <a href="{!! route('admin.downloadExcel.approval',$status) !!}" class="btn btn-primary">Excel</a>
                    </div>
                </div>
              
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3 form-group">
                        <select name="user" id="user" class="mlselect form-control">
                            <option value="" selected disabled>Select User</option>
                            @foreach ($users as $user )
                                <option value="{{$user->id}}" {{ $user->id == request()->user ? 'selected' : '' }}>{{$user->name}}</option>

                            @endforeach
                        </select>

                    </div>
                    <div class="col-md-4     form-group">
                        <select name="item" id="item" class="mlselect form-control">
                            <option value="" selected disabled>Select Item</option>
                            @foreach ($pendingNewApprovalStatuses as $item )
                                <option value="{{$item->id}}" {{ $item->id == request()->item ? 'selected' : '' }}>{{$item->stock_id_code. " "}}{{$item->title}}</option>

                            @endforeach
                        </select>

                    </div>
{{-- 
                    <div class="col-md-2 form-group">
                        <input type="date" name="start-date" id="from" class="form-control" value="{{ request()->get('start-date') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-2 form-group">
                        <input type="date" name="end-date" id="to" class="form-control" value="{{ request()->get('end-date') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div> --}}

                    <div class="col-md-3 form-group">
                        <button type="submit" class="btn btn-success" name="manage-request" value="filter">Filter</button>
                    </div>
                </div>

                <hr>

                    @include('message')
                    <div class="col-md-12">
                        <table class="table table-bordered table-hover" id="dataTable">
                            <thead>
                                <tr>
                                    <th>Requested By</th>
                                    <th>Requested On</th>
                                    <th>Stock ID Code</th>
                                    <th>Title</th>
                                    <th>Item Category</th>
                                    <th>Pack Size</th>
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
        });
    </script>
      <script type="text/javascript">
        $(function () {

            $(".mlselect").select2();
        });
    </script>
    <script type="text/javascript">
        $(function() {
            var selectedCategoryId;
            var selectedSupplierId;

            var table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                "ajax": {
                    "url": '{!! route('admin.maintain-items-datatable-approval') !!}',
                    "dataType": "json",
                    "type": "POST",
                    // "data": { _token: "{{ csrf_token() }}", category_id: selectedCategoryId}
                    "data": function(d) {
                        // Include the selectedCategoryId in the request data
                        d._token = "{{ csrf_token() }}";
                        d.category_id = selectedCategoryId;
                        d.supplier_id = selectedSupplierId;
                        d.approval_status = '{{$status}}';
                        d.user = $('#user').val(); 
                        d.item = $('#item').val();
                    }

                },
                columns: [{
                        data: 'requested_by',
                        name: 'requested_by',
                        orderable: false
                    },
                    {
                        data: 'requested_on',
                        name: 'requested_on',
                        orderable: false
                    },
                    {
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
            $('button[name="manage-request"]').on('click', function() {
            table.ajax.reload();
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
@endsection
