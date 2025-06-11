
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                             @if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
                            <div align = "right"> <a href = "{!! route($model.'.create')!!}" class = "btn btn-success">Add {!! $title !!}</a></div>
                             @endif
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                    <tr>
                                        <th width="10%">S.No.</th>
                                       
                                        <th width="10%"  >Stock ID Code</th>
                                          <th width="13%"  >Title</th>
                                           <th width="10%"  >Pack Size</th>

                                            <?php //<th width="10%"  >Re-Order Level</th>  ?>


                                           

                                          <th width="10%"  >Standard Cost</th>

                                         <th width="14%"  >Quantity on Hand</th>
                                          <th width="14%"  >Quantity on Order</th>

                                        
                                          
                                        
                                          <th  width="14%" class="noneedtoshort" >Action</th>
                                       
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1;?>
                                        @foreach($lists as $list)
                                         
                                            <tr>
                                                <td>{!! $b !!}</td>
                                                
                                                <td>{!! $list->stock_id_code !!}</td>
                                                 <td>{!! $list->title !!}</td>
                                                   <td>{!! @$list->pack_size->title !!}</td>
                                                    <?php /*<td>{!! $list->minimum_order_quantity !!}</td> */ ?>



                                                  <td>{!! $list->standard_cost !!}</td>
                                                      <td>{!! $list->getAllFromStockMoves ? $list->getAllFromStockMoves->sum('qauntity'):0 !!}</td>
                                                      <td>{!! getQtyOnOrder($list->id)!!}</td>

                                                      

                                                       
                                                
                                                <td class = "action_crud">
                                                @if($list->slug != 'mpesa')
                                                 @if(isset($permission[$pmodule.'___edit']) || $permission == 'superadmin')
                                                    <span>
                                                    <a title="Edit" href="{{ route($model.'.edit', $list->slug) }}" ><img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                                    </a>
                                                    </span>

                                                    @endif


                                                  

                                                       



                                                    @endif


                                                  
                                                     <span >
                                                    <a style="font-size: 16px;"  href="{{ route($model.'.stock-movements', $list->stock_id_code) }}" ><i class="fa fa-list" title= "Stock Movements"></i>
                                                    </a>
                                                    </span>

                                                     <span >
                                                    <a style="font-size: 16px;"  href="{{ route($model.'.stock-status', $list->stock_id_code) }}" ><i class="fa fa-book" title= "Stock Status"></i>
                                                    </a>
                                                    </span>
                                                    <span >
                                                     <a style="font-size: 16px;"  href="javascript:void(0);" row_id='<?= $list->id ?>' onclick="manageStockPopup('<?= route('admin.table.adjust-item-stock-form', $list->slug) ?>')">
                                                        <i class="fa fa-bolt" title= "Manage Item Stock"></i>
                                                    </a>
                                                    </span>

                                                     @if(isset($permission[$pmodule.'___delete']) || $permission == 'superadmin')

                                                        <span>
                                                        <form title="Trash" action="{{ URL::route($model.'.destroy', $list->slug) }}" method="POST">
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        <button  style="float:left"><i class="fa fa-trash" aria-hidden="true" style="font-size: 16px;"></i>
                                                        </button>
                                                        </form>
                                                        </span>
                                                        @endif



                                                </td>
                                                
											
                                            </tr>
                                           <?php $b++; ?>
                                        @endforeach
                                    @endif


                                    </tbody>
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
    
    <script type="text/javascript">
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
   
@endsection
