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
            @include('message')
            <div class="box-header with-border no-padding-h-b">
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="dataTable">
                        <thead>
                        <tr>
                            <th width="8%"  >Stock ID Code</th>
                            <th width="26%"  >Title</th>
                            <th width="15%"  >Item Category</th>
                            <th width="10%"  >Pack Size</th>
                            <th width="8%"  >Standard Cost</th>
                            <th width="8%"  >Selling Price</th>
                            <th width="8%"  >QOH</th>
                            <th  width="17%" class="noneedtoshort" >Action</th>
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
                 {!! Form::open(['route' => 'supreme-store-issue.inventory-manage-stock','class'=>'validate form-horizontal']) !!}
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
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
    <script type="text/javascript">
        function manageStockPopup(link){
            $('#manage-stock-model').modal('show');
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
                    data:{location_id:location_id, stock_id_code:stock_id_code,'supreme_store':'supreme_store'},
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
$(function() {
    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        order: [[0, "desc" ]],
        pageLength: '<?= Config::get('params.list_limit_admin') ?>',
        "ajax":{
        "url": '{!! route('supreme-store-issue.inventoryItems') !!}',
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
        { data: 'selling_price', name: 'selling_price', orderable:false },
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
    $(document).on('click','.deleteMe',function(e){
        e.preventDefault();
        var $this = $(this);
        $.ajax({
            type: "POST",
            url: $this.attr('href'),
            data:{
                '_token':"{{csrf_token()}}"
            },
            success: function (response) {
                if(response.status == -1){
                    form.errorMessage(response.message);
                    return false;
                }
                if(response.status == 1){
                    form.successMessage(response.message);
                    table.ajax.reload();
                    return false;
                }
            }
        });
    });
});     
</script>   
@endsection