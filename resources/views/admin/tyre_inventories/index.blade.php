
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
                             @if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
                                <div align = "right"> <a href = "{!! route($model.'.create')!!}" class = "btn btn-success">Add {!! $title !!}</a>
                                    {{--
                                        <a href = "{!! route('admin.downloadExcel')!!}" class = "btn btn-primary">Excel</a>
                                    --}}
                                </div>
                             @endif


                            <div class="col-sm-1">
                                <form action="{{route('tyre.exportPdf')}}" method="post">
                                    {{ csrf_field() }}
                                    <button type="submit" class="btn btn-danger" name="manage-request" value="PDF"  >PDF</button></div>
                                    
                                </form>

                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="dataTable">
                                    <thead>
                                    <tr>
                                        
                                        <th width="10%"  >ID</th>
                                        <th width="10%"  >Stock ID Code</th>
                                        <th width="10%"  >Title</th>
                                        <th width="20%"  >Description</th>
                                        <th width="10%"  >Tyre Size</th>  
                                        <th width="10%"  >New Tire in Store</th>
                                        <th width="10%"  >In Motor Vehicle</th>
                                        <th width="10%"  >Retread Tire in Stock</th>
                                        <th width="10%"  >Tyres in Retread</th>
                                        <th width="10%"  >Damaged Tyres</th>


                                        {{--
                                            <th width="20%"  >Tyre Make</th>
                                            <th width="10%"  >Type</th>
                                            <th width="10%"  >Pattern</th>
                                            <th width="10%"  >Standard Cost</th>
                                            <th width="10%"  >UOM</th>
                                            <th width="10%"  >QOH</th>
                                            <th width="10%"  >QOO</th>
                                        --}}
                                       
                                        <th width="40%" class="noneedtoshort" >Action</th>
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                                    </tr>
                                    </thead>
                                    <tfoot>
                                        <th colspan="5"  >Total</th>
                                        <th width="10%" id="total_new_tyre_in_stock_count" ></th>
                                        <th width="10%" id="total_in_motor_vehicle_count" >In Motor Vehicle</th>
                                        <th width="10%" id="total_retread_tyre_in_stock_count" >Retread Tire in Stock</th>
                                        <th width="10%" id="total_tyres_in_retread_count" >Tyres in Retread</th>
                                        <th width="10%" id="total_damaged_tyre_count" >Damaged Tyres</th>
                                    </tfoot>
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
        "url": '{!! route('tyre_inventories.index') !!}',
        "dataType": "json",
        "type": "GET",
        data:function(data){
            data.processed=$('#is_processed').val();
            var from = $('#from').val();
            var to = $('#to').val();

            data.from = from;
            data.to = to;
        },"dataSrc": function (suc){
                if(suc.total){
                    $('#amount').html(suc.total).css("font-weight","Bold");
                    $('#total_new_tyre_in_stock_count').html(suc.total_new_tyre_in_stock_count).css("font-weight","Bold");
                    $('#total_in_motor_vehicle_count').html(suc.total_in_motor_vehicle_count).css("font-weight","Bold");
                    $('#total_retread_tyre_in_stock_count').html(suc.total_retread_tyre_in_stock_count).css("font-weight","Bold");
                    $('#total_tyres_in_retread_count').html(suc.total_tyres_in_retread_count).css("font-weight","Bold");
                    $('#total_damaged_tyre_count').html(suc.total_damaged_tyre_count).css("font-weight","Bold");
                }
                return suc.data;
            }
        
        },
        @if(isset($permission['vehicle___create']) || $permission == 'superadmin')
        'fnDrawCallback': function (oSettings) {
            $('.dataTables_filter').each(function () {
              $('.remove-btn').remove();
              // $(this).append('<a class="btn btn-danger remove-btn mr-xs pull-right ml-2 btn-sm" style="margin-left:5px" data-toggle="modal" data-target="#AddModal" href="#">Add</a>');
            });
        },
        @endif

        /*<th width="20%"  >Name</th>
          <th width="20%"  >Year</th>
          <th width="10%"  >Make</th>
          <th width="10%"  >Model</th>
          <th width="10%"  >Status</th>
          <th width="20%"  >Type</th>
          <th width="10%"  >Current Meter</th>
          <th width="10%"  >License Platf</th>*/

        columns: [
            { mData: 'id', orderable: true,
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }},
            { data: 'stock_id_code', name: 'stock_id_code', orderable:true },
            { data: 'title', name: 'title', orderable:true },
            { data: 'description', name: 'description', orderable:true },
            { data: 'tyre_size', name: 'tyre_size', orderable:true  },
           
            { data: 'new_tyre_in_stock_count', name: 'new_tyre_in_stock_count', orderable:false  },
            { data: 'in_motor_vehicle_count', name: 'in_motor_vehicle_count', orderable:false  },
            { data: 'retread_tyre_in_stock_count', name: 'retread_tyre_in_stock_count', orderable:false  },
            { data: 'tyres_in_retread_count', name: 'tyres_in_retread_count', orderable:false  },
            { data: 'damaged_tyre_count', name: 'damaged_tyre_count', orderable:false  },

            /*{ data: 'tyre_make', name: 'tyre_make', orderable:true  },
            { data: 'inventory_item_type', name: 'inventory_item_type', orderable:false },
            { data: 'pattern', name: 'pattern', orderable:true },
            { data: 'standard_cost', name: 'standard_cost', orderable:false },
            { data: 'uom', name: 'uom', orderable:false },
            { data: 'qauntity', name: 'qauntity', orderable:false },
            { data: 'qty_on_order', name: 'qty_on_order', orderable:false },*/
            //{ data: 'status', name: 'status', orderable:false },
            { data: 'links', name: 'links', orderable:false},
        ],
        "columnDefs": [
            { "searchable": false, "targets": 0 },
            { className: 'text-center', targets: [1] },
        ]
        , language: {
        searchPlaceholder: "Search"
        },
    });
    $('#filter').click(function(){
        table.draw();
        $('#modelId').modal('hide');
    });
});    
        

     
      function manageStockPopup(link){
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
        
     
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
<script>
   $('table').on('click','.delete-confirm', function (event) {
      event.preventDefault();
      const url = $(this).attr('href');
      swal({
          title: 'Are you sure?',
          text: 'This record and it`s details will be permanantly deleted!',
          icon: 'warning',
          buttons: ["No, Cancel It", "Yes, I Confirm"],
          }).then(function(value) {
          if (value) {
            window.location.href = url;
            }
      });
     });
</script>
   
@endsection
