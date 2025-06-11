
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
                               <table class="table table-bordered table-hover" id="create_datatable_desc">
                                                    <thead>
                                    <tr>
                                        <th>Bill Id</th>
                                        
                                       <th class="noneedtoshort">Date And Time</th>
                                       <th>No of Orders</th>
                                        <th class="noneedtoshort">Total Amount</th>
                                       
                                        <th  class="noneedtoshort">Action</th>
                                        
                                       
                                        
                                       
                                        
                                        
                                      
                                       
                                       
                                      
                                       
                                       
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                                        
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($lists as $list)

                                    <tr>
                                   
                                      <td>{{ $list->id }}</td>
                                       <td>{!! date('d/m/Y h:i A',strtotime($list->created_at)) !!}</td>
                                      <td>{!! count($list->getAssociateOrdersWithBill) !!}</td>

                                      <td>
                                        
                                       <?php 
                                              $total_bill = []; ?>
                                              @foreach($list->getAssociateOrdersWithBill as $single_order)
                                              <?php $total_bill[] = $single_order->getAssociateOrderForBill->order_final_price;
                                               ?>

                                              @endforeach 
                                              {!! array_sum($total_bill); !!}



                                         
                                      </td>

                                      <td>
                                         @if(isset($permission[$pmodule.'___close-bill']) || $permission == 'superadmin')
                                                <a onclick = "return confirm('Do you want to close the bill?');" href = "{!! route('admin.pend-delivery-orders.get.cash.receipt',$list->slug) !!}" class = "btn btn-primary">Close Bill</a>

                                                @endif

                                                  @if(isset($permission[$pmodule.'___delete_bill']) || $permission == 'superadmin')
                                                <span>
                                                    <a title="Delete Bill" href="{!! route('admin.opend-delivery-orders.delete.bill.request',$list->slug)!!}"  onclick="return confirm('Do you really want to delete the bill ?')">
                                                    <i aria-hidden="true" class="fa fa-trash" style="font-size: 20px;"></i>
                                                    </a>
                                                    </span>
                                                     @endif
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
   <style type="text/css">


   </style>

   <script type="text/javascript">
       function printBill()
       {
          
       }
   </script>
@endsection
