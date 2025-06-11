
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

                                    <form method="post" action="{!! route('admin.table.exportCategoryPrice')!!}">
                                       {{csrf_field()}}
                                       <div class="col-sm-3">
                               {!! Form::select('wa_inventory_category_id', getInventoryCategoryList(),null, ['maxlength'=>'255','placeholder' => 'Please select', 'required'=>true, 'class'=>'form-control mlselec6t']) !!}  
                                       </div>
                                       <div class="col-sm-2">
                                           <button type="submit" class="btn btn-success">Download Excel File</button>
                                       </div>
                                   </form> 
                                    <form method="post" action="{!! route('admin.table.importexcelforitempriceupdate')!!}" enctype="multipart/form-data">
                                       {{csrf_field()}}
                                       <div class="col-sm-3">
                                           <input type="file" class="form-control" name="excel_file" required />
                                       </div>
                                       <div class="col-sm-2">
                                           <button type="submit" class="btn btn-success">Import Excel File</button>
                                       </div>
                                   </form> 
                                </div>
		                        <div class="col-sm-3">
                             @if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
                             <div align = "right"> <a href = "{!! route($model.'.create')!!}" class = "btn btn-success">Add Raw Material</a>
                                <a href = "{!! route('admin.downloadExcel')!!}" class = "btn btn-primary">Excel</a>
                            </div>
                             @endif
		                        </div>
	                        </div>
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="dataTable">
                                    <thead>
                                    <tr>
                                        
                                        <th width="10%"  >Stock ID Code</th>
                                        <th width="20%"  >Title</th>
                                        <th width="20%"  >Item Category</th>
                                        <th width="10%"  >Pack Size</th>
                                        <th width="10%"  >Standard Cost</th>
                                        <!-- <th width="10%"  >Selling Price</th> -->
                                        <th width="10%"  >QOH</th>
                                        <th  width="40%" class="noneedtoshort" >Action</th>
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                                    </tr>
                                    </thead>
                                    
                                </table>
                            </div>
                        </div>
                    </div>


    </section>
    
    <div class="modal " id="manage-stock-model" role="dialog" tabindex="-1"  aria-hidden="true" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                 {!! Form::open(['route' => 'maintain-items.manage-stock','class'=>'validate form-horizontal']) !!}
                <div class="modal-header">
                    <button type="button" class="close" 
                            data-dismiss="modal">
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

    <div class="modal " id="manage-category-model" role="dialog" tabindex="-1"  aria-hidden="true" role="dialog" aria-labelledby="myModalLabel1">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                 {!! Form::open(['route' => 'maintain-items.manage-category-price','class'=>'validate form-horizontal']) !!}
                <div class="modal-header">
                    <button type="button" class="close" 
                            data-dismiss="modal">
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
    @section('uniquepagescript')
    <script type="text/javascript">
       
        
$(function() {
    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        order: [[0, "desc" ]],
        pageLength: '<?= Config::get('params.list_limit_admin') ?>',
        "ajax":{
        "url": '{!! route('admin.maintain-raw-material-items-datatable') !!}',
                "dataType": "json",
                "type": "POST",
                "data":{ _token: "{{csrf_token()}}"}
        },
        columns: [
        { data: 'stock_id_code', name: 'stock_id_code', orderable:false },
        { data: 'title', name: 'title', orderable:false  },
        { data: 'item_category', name: 'item_category', orderable:false  },
        { data: 'uom', name: 'uom', orderable:false },
        { data: 'standard_cost', name: 'standard_cost', orderable:false },
        // { data: 'selling_price', name: 'selling_price', orderable:false },
        { data: 'qauntity', name: 'qauntity', orderable:false },
        { data: 'action', name: 'action', orderable:false},
        ],
        "columnDefs": [
            { "searchable": false, "targets": 0 },
            { className: 'text-center', targets: [1] },
        ]
        , language: {
        searchPlaceholder: "Search"
        },
    });
});
     
        function manageStockPopup(link=""){



            $('#manage-stock-model').modal('show');
            //$('#manage-stock-model').find(".box-body").html('<img style="width:50px;" src="{{ asset('public/assets/admin/images/loading.gif') }}">');
            $('#manage-stock-model').find(".box-body").load(link);

        }
        
        function getAndUpdateItemAvailableQuantity(input_obj){
            location_id = $(input_obj).val();
            if(location_id){
                stock_id_code = $('#stock_id_code_input').val();
                jQuery.ajax({
                    url: '{{route('maintain-items.get-available-quantity-ajax')}}',
                    type: 'POST',
                    dataType: "json",
                    data:{location_id:location_id, stock_id_code:stock_id_code},
                    headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        $('#current_qty_available').val(response['available_quantity']);
                    }
                });
            }
            else{
                $('#current_qty_available').val(0);
            }
            
        }
      function manageCategoryPopup(link,that){
            $('#manage-category-model').modal('show');
            //$('#manage-stock-model').find(".box-body").html('<img style="width:50px;" src="{{ asset('public/assets/admin/images/loading.gif') }}">');
            $('#myModalLabel1 #moretitle').html($(that).data('title'));
            $('#manage-category-model').find(".box-body").load(link);

        }
        
        function getAndUpdateItemAvailableQuantity(input_obj){
            location_id = $(input_obj).val();
            if(location_id){
                stock_id_code = $('#stock_id_code_input').val();
                jQuery.ajax({
                    url: '{{route('maintain-items.get-available-quantity-ajax')}}',
                    type: 'POST',
                    dataType: "json",
                    data:{location_id:location_id, stock_id_code:stock_id_code},
                    headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        $('#current_qty_available').val(response['available_quantity']);
                    }
                });
            }
            else{
                $('#current_qty_available').val(0);
            }
            
        }
        
     
</script>
   
@endsection
