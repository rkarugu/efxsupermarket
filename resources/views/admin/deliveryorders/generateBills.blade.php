
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

                             {!! Form::model(null, ['method' => 'post','route' => ['admin.delivery-orders.open-orders.generateBills.post'],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
                              {{ csrf_field() }}
                                <table class="table table-bordered table-hover" >
                                                    <thead>
                                    <tr>
                                        <th>Mark</th>
                                        <th>Delivery No</th>
                                       <th class="noneedtoshort">Date And Time</th>
                                        <th class="noneedtoshort">Customer</th>
                                         <th class="noneedtoshort">Item Description</th>
                                        <th >Amount</th>
                                        <th  class="noneedtoshort">Address</th>
                                        
                                       
                                        
                                       
                                        
                                        
                                      
                                       
                                       
                                      
                                       
                                       
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                                        
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($lists as $list)

                                    <tr>
                                    <td>
                                                {{ Form::checkbox('order___'.$list->id , $list->id ) }}



                                                </td>
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
                                       
                                   


                                    </tr>



                                    @endforeach

                                    @if(count($lists)>0)
                                        <tr style="margin-top: 20px;"><td colspan="7"><button title="Generate Bill" type="submit" class="btn btn-warning" >Generate Bill
                                </button></td></tr>
                                  @else
                                        <tr><td colspan="11">Do not have any pending order</td></tr>
                                      @endif
                               


                                    </tbody>
                                </table>
                                 </form>
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
