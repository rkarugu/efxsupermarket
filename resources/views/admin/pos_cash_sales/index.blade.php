@extends('layouts.admin.admin')
@section('content')
    <br>
    <div class="container-fluid">
        <div class="clearfix pr-6">
            <a href="#" class="btn btn-primary pull-left"><i class="fa fa-rotate-left"></i> Back</a>
            <x-drop-component/>
        </div>
    </div>
    <br>
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="box-title"> {!! $title !!} </h3>
                {{-- <div>
                    <button class="btn btn-success btn-sm">Missing</button>
                    <button class="btn btn-success btn-sm">New Item</button>
                    <button class="btn btn-success btn-sm">Price Conflict</button>
                </div> --}}
                <div>
                    <ul class="nav nav-tabs" id="salesTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="sales-tab" data-toggle="tab" href="#sales" role="tab" aria-controls="sales" aria-selected="true">Sales</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="missing-tab" data-toggle="tab" href="#missing" role="tab" aria-controls="missing" aria-selected="false">Missing</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="new-item-tab" data-toggle="tab" href="#new-item" role="tab" aria-controls="new-item" aria-selected="false">New Item</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="price-conflict-tab" data-toggle="tab" href="#price-conflict" role="tab" aria-controls="price-conflict" aria-selected="false">Price Conflict</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="split-request-tab" data-toggle="tab" href="#split-request" role="tab" aria-controls="split-request" aria-selected="false">Split Request</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
         @include('message')
         <div class="box-body" style="padding-bottom:15px">
            <div class="tab-content">

            <div class="tab-pane fade" id="sales" role="tabpanel" aria-labelledby="sales-tab">
                <form action="" method="get">
                    <div class="row">
                        @if($permission == 'superadmin')
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="restaurant_id">Branch</label>
                                    {!!Form::select('restaurant_id', $branches, request()->input('restaurant_id') ?? null, ['placeholder'=>'Select Branch ', 'class' => 'form-control mlselec6t','title'=>'Please select Branch','id'=>'restaurant_id'  ])!!}
                                </div>
                            </div>
                        @endif
                        <div class="col-md-2">
                            <div class="form-group">
                            <label for="">From</label>
                            <input type="date" name="start-date" id="start-date" class="form-control" value="{{request()->input('start-date') ?? date('Y-m-d')}}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                            <label for="">To</label>
                            <input type="date" name="end-date" id="end-date" class="form-control"  value="{{request()->input('end-date') ?? date('Y-m-d')}}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                            <label for="">&nbsp;</label>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="pos-status" class="pos-status redraw" checked="checked"  value="PENDING">
                                        Pending
                                    </label>
                                    <label>
                                        <input type="radio" name="pos-status" class="pos-status redraw" value="completed">
                                        Completed
                                    </label>
    
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                            <button type="submit" id="filter" class="btn btn-primary btn-sm" style="margin-top: 25px;"> <i class="fa fa-filter"></i> Filter</button>
                            <a class="btn btn-primary remove-btn mr-xs pull-right ml-2 btn-sm" style="margin-top: 25px;" href="{{route($model.'.create')}}">
                                <i class="fa fa-shopping-cart"> </i> Add {{$title}}
                            </a>
                            </div>
                        </div>
                    </div>
                </form>
                 <div class="table-responsive">
                    <table class="table table-hover table-bordered table-invert" id="dataTable" style="width: 100%">
                        <thead>
                            <tr>
                                <th style="width:2%">Sr. No.</th>
                                <th style="width:10%">Tablet Cashier</th>
                                <th style="width:10%">Counter Cashier</th>
                                <th style="width:10%">Cash Sales</th>
                                <th style="width:11%">Date/Time</th>
                                <th style="width:10%">Customer</th>
                                {{-- <th style="width:10%">Payment</th>
                                <th style="width:10%">Cash</th>
                                <th style="width:10%">Change</th> --}}
                                <th style="width:10%">Total</th>
                                <th  style="width:10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="6">Total</th>
                                <th id="getTotal"></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                 <div class="d-flex justify-content-end">
                     <div class="otp-send  ">
                         <button type="button" class="btn btn-primary" id="archive_btn" name="archive" value="approve" style="display: none"> Archive </button>
                     </div>
                 </div>
            </div>
            <!-- Missing Items Tab -->
            <div class="tab-pane fade" id="missing" role="tabpanel" aria-labelledby="missing-tab">
                <x-missing-items-tab />
            </div>
            <div class="tab-pane fade" id="new-item" role="tabpanel" aria-labelledby="new-item-tab">
                <x-new-items-tab />
            </div>
            <div class="tab-pane fade" id="price-conflict" role="tabpanel" aria-labelledby="price-conflict-tab">
                <x-price-conflict-tab />
            </div>
            <div class="tab-pane fade" id="split-request" role="tabpanel" aria-labelledby="split-request-tab">
                <x-split-requests-component />
            </div>
            </div>


           
         </div>
    </div>
</section>
@endsection
@section('uniquepagestyle')
<link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>

<style>
       .hidden {
            display: none !important;
        }
</style>
@endsection
@section('uniquepagescript')
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
<script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>



<script>
      $(document).ready(function() {
        $('#sales-tab').trigger('click');

        $('#salesTabs a').on('click', function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
    });
    var VForm = new Form();


    $(document).ready(function() {
        const $submitButton = $('#archive_btn');
        var selectedItems = [];
        $('#dataTable').on('change', '.archive_checkbox', function() {
            var itemId = $(this).val();
            if ($(this).is(':checked')) {
                if (!selectedItems.includes(itemId)) {
                    selectedItems.push(itemId);
                }
            } else {
                selectedItems = selectedItems.filter(function(id) {
                    return id !== itemId;
                });
            }

            if (selectedItems.length > 0) {
                $submitButton.show();
            } else {
                $submitButton.hide();
            }
            console.log(selectedItems);
        });

        $('#archive_btn').on('click', function() {
            $.ajax({
                url: '{!! route('pos-cash-sales.archive-pending') !!}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ids: selectedItems
                },
                success: function(response) {
                    VForm.successMessage('Orders Archived Successfully');
                    $submitButton.hide();
                    $('#dataTable').DataTable().draw();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    VForm.successMessage(xhr.responseText);
                }
            });
        });
    });


    $(document).ready(function() {

        $('body').addClass('sidebar-collapse');
        $(function() {
            var table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                order: [[1, "desc" ]],
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                "ajax":{
                    "url": '{!! route($model.'.index') !!}',
                    "dataType": "json",
                    "type": "GET",
                    data:function(data){
                        var from = $('#start-date').val();
                        var to = $('#end-date').val();
                        var restaurant_id = $('#restaurant_id').val();
                        var status = $('.pos-status:checked').val();
                        data.from = from;
                        data.to = to;
                        data.status = status;
                        data.restaurant_id = restaurant_id;
                    },
                    "dataSrc": function (suc){
                        if(suc.total){
                            $('#getTotal').html(suc.total);
                        }
                        return suc.data;
                    }
                },
                // 'fnDrawCallback': function (oSettings) {
                //     $('.dataTables_filter').each(function () {
                //       $('.remove-btn').remove();



                //       @if($permission == 'superadmin' || isset($permission['pos-cash-sales___add']))
                //             $(this).append('<a class="btn btn-danger remove-btn mr-xs pull-right ml-2 btn-sm" style="margin-left:5px" href="{{route($model.'.create')}}">Add {{$title}}</a>');
                //       @endif
                //     });
                // },
                columns: [
                    { mData: 'id', orderable: true,
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }},
                    { data: 'tablet_cashier', name: 'tablet_cashier', orderable:true  },
                    { data: 'counter_cashier', name: 'counter_cashier', orderable:true  },
                    { data: 'sales_no', name: 'sales_no', orderable:true  },
                    { data: 'date_time', name: 'date_time', orderable:false },
                    { data: 'customer', name: 'customer', orderable:true },
                    // { data: 'total', name: 'total', orderable:false },
                    { data: 'total', name: 'total', orderable: false,
                        render: function (data, type, row) {
                            return parseFloat(data).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        }},
                    { data: 'links', name: 'links', orderable:false},
                ],
                "columnDefs": [
                    { "searchable": false, "targets": 0 },
                ],
                language: {
                    searchPlaceholder: "Search"
                },
            });
            $('#filter').click(function(e){
                e.preventDefault();
                table.draw();
                $('#modelId').modal('hide');
            });

            $(document).on('click','.redraw', function(){
                table.draw()
            });
            $(document).on('click','.archive_btn', function(){
                if(confirm('Do you confirm to archive this item?')){
                    return true;
                }
                return false;
            });
        });
    });
    function printBill(slug)
    {
        let isConfirm = confirm('Do you want to print this Cash Sale Receipt?');
        if (isConfirm) {
            jQuery.ajax({
                url: slug,
                type: 'GET',
                async: false,   //NOTE THIS
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    var divContents = response;
                    var printWindow = window.open('', '', 'width=600');
                    printWindow.document.write(divContents);
                    printWindow.document.close();
                    printWindow.print();
                    printWindow.close();
                    // location.reload();
                    // location.href = '{{ route($model.'.index') }}';
                },
                error: function (xhr, status, error) {
                    alert("Invoice Not signed. Try again Later");
                }
            });
        }

    }

    function printDispatch(slug) {
        if (slug != null){
            jQuery.ajax({
                url: slug,
                type: 'GET',
                async: false,   //NOTE THIS
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    var divContents = response;
                    var printWindow = window.open('', '', 'width=600');
                    printWindow.document.write(divContents);
                    printWindow.document.close();
                    printWindow.print();
                    printWindow.close();
                    // location.reload();
                    {{--location.href = '{{ route($model.'.index') }}';--}}

                },
                error: function (xhr, status, error) {
                    alert("Invoice Not signed. Try again Later");
                }
            });
        }
    }
          


    function printLoadings(id)
    {
        let dispatch = "{{ url('admin/pos-cash-sale/dispatch-slip/') }}"+'/' +id
        let display = "{{ url('admin/pos-cash-sale/dispatch-slip/display/') }}"+'/'+id


        printDispatch(dispatch)
        printDispatch(display)
    }
   
</script>






@endsection
@stack('scripts')
