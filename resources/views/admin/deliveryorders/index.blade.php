
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">

                            
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h table-responsive">
                                <table class="table table-bordered table-hover" id="create_datatable_desc">
                                                    <thead>
                                    <tr>
                                        
                                        <th>Delivery No</th>
                                       <th class="noneedtoshort">Date And Time</th>
                                        <th class="noneedtoshort">Customer</th>
                                         <th class="noneedtoshort">Item Description</th>
                                        <th >Amount</th>
                                        <th  class="noneedtoshort">Address</th>
                                       
                                        
                                       
                                        
                                        
                                      
                                       
                                       
                                      
                                        <th   class="noneedtoshort" >Action</th>
                                       
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                                        
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($lists as $list)

                                    <tr>
                                      <td>{{ $list->id }}</td>
                                       <td>{!! date('d/m/Y h:i A',strtotime($list->created_at)) !!}</td>
                                      <td>{!! $list->getAssociateUserForOrder->name !!}</td>

                                      <td>
                                        
                                      



                                         <?php 
                            $item_desc_array = [];
                            $condiments = [];
                            ?>
                            @foreach($list->getAssociateItemWithOrder as $ordered_item)
                                <?php 
                                    $condiment_arr =  json_decode($ordered_item->condiments_json);
                                    $item_desc = 'Item: '.$ordered_item->item_title;
                                    if($ordered_item->item_comment && $ordered_item->item_comment !="")
                                    {
                                        $item_desc .= '('.$ordered_item->item_comment.')';
                                    }
                                    $item_desc .= '<br>Qty: '.$ordered_item->item_quantity;
                                    $item_desc_array[] = $item_desc;


                               



                                ?>
                            @endforeach  
                            {!! '<b>'.implode(' ,<br>',$item_desc_array).'</b>'!!}   
                                      </td>

                                      <td>{{ manageAmountFormat($list->order_final_price) }}</td>
                                      <td>{{ $list->address }}</td>
                                      <td style="font-size: 20px !important;"> 

                                         @if(isset($permission[$pmodule.'___cancle']) || $permission == 'superadmin')
                                      <a href="{{ route('admin.delivery-orders.cancel',$list->slug)}}" class="fa fa-danger" title="Cancel Order"><i class="fa fa-times-circle"></i></a>

                                      @endif

                                        @if(isset($permission[$pmodule.'___confirm']) || $permission == 'superadmin')
                                      <a href="{{ route('admin.delivery-orders.confirm',$list->slug)}}" class="fa fa-danger" title="Confirm Order"><i class="fa fa-check-circle"></i></a>

                                      @endif


                                     


                                        @if(isset($permission[$pmodule.'___assign']) || $permission == 'superadmin')

                                        <!--a href="{{ route('admin.delivery-orders.get.assign',$list->slug)}}" class="fa fa-danger" title="Assign"><i class="fa fa-tasks"></i></a-->

                                         @endif

                                      </td>


                                    </tr>



                                    @endforeach
                               


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


    </section>
   <style type="text/css">
       .table td {
  font-size: 13px;
}

   </style>

   <script type="text/javascript">
       function printBill()
       {
          
       }
   </script>
@endsection
