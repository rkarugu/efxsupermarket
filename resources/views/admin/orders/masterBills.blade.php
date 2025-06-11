
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">

                            
                            <br>
                            @include('message')
                               <?php 
                            $myTotalAmount = [];
                             if(isset($lists) && !empty($lists))
                             {
                              foreach($lists as $listing)
                              {
                                 foreach($listing->getAssociateOrdersWithBill as $single_order_new)
                                 {
                                  $myTotalAmount[] = $single_order_new->getAssociateOrderForBill->order_final_price;
                                 }
                              }
                             }
                            ?>
                            <div class="col-md-12 no-padding-h table-responsive">
                                <table class="table table-bordered table-hover" id="create_datatable_desc">
                                    <thead>
                                    <tr>
                                        
                                        <th>Bill ID</th>
                                        <th>Date</th>
                                        <th>Waiter Name</th>
                                        <th>Number of Orders</th>
                                        <th class="noneedtoshort" >Total Amount</th>
                                        <th>Orders</th>
                                        <th class="noneedtoshort" >Print Count</th>
                                        <th class="noneedtoshort" >Print Bill</th>
                                        <th class="noneedtoshort" >Close Bill</th>
                                        <th class="noneedtoshort" >Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1; $totalorderamnt = 0; ?>
                                        @foreach($lists as $list)

                                        <?php 
                                        $showTheRow = false;
                                        if($permission == 'superadmin')
                                        {
                                            $showTheRow = true;
                                        }
                                        else
                                        {
                                          if(count($list->getAssociateOrdersWithBill)>0 && $list->getAssociateOrdersWithBill[0]->getAssociateOrderForBill->restaurant_id == $logged_user_info->restaurant_id)
                                          {
                                            
                                             $showTheRow = true;
                                          }
                                        }

                                        if($showTheRow == true){
                                        ?>


                                       
                                         
                                            <tr>
                                               
                                              <td>{!! $list->id !!}</td>
                                              <td>
                                                {!! date('Y-m-d',strtotime($list->created_at)) !!}
                                              </td>
                                              <td>{!! ucfirst(@$list->getAssociateUserForBill->name) !!}</td>
                                              <td>{!! count($list->getAssociateOrdersWithBill) !!}</td>

                                               {{-- <td>{!! implode(', ',$list->getAssociateOrdersWithBill->pluck('order_id')->toArray()) !!}</td> --}}
                                              
                                              <td>
                                              <?php 
                                              $total_bill = []; ?>
                                              @foreach($list->getAssociateOrdersWithBill as $single_order)
                                              <?php $total_bill[] = $single_order->getAssociateOrderForBill->order_final_price;
                                              $totalorderamnt += $single_order->getAssociateOrderForBill->order_final_price;
                                               ?>

                                              @endforeach 
                                              {!! round(array_sum($total_bill)); !!}
                                              </td>
                                              <td> <a title="View Orders" href="{{route('admin.master-bills-orders',$list->id)}}"><i aria-hidden="true" class="fa fa-eye" style="font-size: 20px;"></i>
                                                </a></td>
                                              
                                              <td class = "action_crud">

                                                <span>
                                                <a title="Print Bill" href="javascript:void(0)" onclick="printBill({!! $list->id!!})"><i aria-hidden="true" class="fa fa-print" style="font-size: 20px;"></i>
                                                </a>
                                                </span>

                                              </td>
                                              <td>{!! $list->print_count !!}</td>
                                              <td class = "action_crud">
                                               @if(isset($permission[$pmodule.'___close']) || $permission == 'superadmin')
                                                <a onclick = "return confirm('Do you want to close the bill?');" href = "{!! route('admin.get.cash.receipt',$list->slug) !!}" class = "btn btn-primary">Close Bill</a>

                                                @endif

                                              </td>

                                               <td class = "action_crud">
                                                @if(isset($permission[$pmodule.'___delete_bill']) || $permission == 'superadmin')
                                                <span>
                                                    <a title="Delete Bill" href="{!! route('admin.delete.bill.request',$list->slug)!!}"  onclick="return confirm('Do you really want to delete the bill ?')">
                                                    <i aria-hidden="true" class="fa fa-trash" style="font-size: 20px;"></i>
                                                    </a>
                                                    </span>
                                                     @endif


                                                      @if(isset($permission[$pmodule.'___discount']) || $permission == 'superadmin')
                                                    <span>
                                                    <a title="Add Discount" href="{!! route('admin.get.bills.discount.request',$list->slug)!!}"  onclick="return confirm('Do you really want to add discount ?')"><img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                                    </a>
                                                    </span>




                                                    @endif

                                                    <span>
                                                    @if(isset($permission[$pmodule.'___transfer']) || $permission == 'superadmin')
                                                    <span>
                                                    <a title="Transfer Bill To Order" href="{{ route('admin.get.transfer-bill-to-order', $list->slug) }}"  onclick="return confirm('Do you really want to transfer the order?')">
                                                    <i aria-hidden="true" class="fa fa-expand" style="font-size: 20px;"></i>
                                                    </a>
                                                    </span>
                                                    @endif

                                                    @if(isset($permission[$pmodule.'___void']) || $permission == 'superadmin')
                                                        <span>
                                                        <a title="Void Items From Bill" href="{{ route('admin.get.void-items-from-bill', $list->slug) }}"  onclick="return confirm('Do you really want to void the items ?')">
                                                        <i aria-hidden="true" class="fa fa-calendar-times" style="font-size: 20px;"></i>
                                                        </a>
                                                        </span>
                                                    @endif





                                               </td>
                                            </tr>
                                           <?php $b++; ?>

                                           <?php } ?>
                                        @endforeach
 
                                    @endif


                                    </tbody>
                                     <tfoot>
                                        <td ></td>
                                        <td></td>
                                        <td ></td>
                                        <td></td>
                                        <td ><b>Total</b></td>
                                        <td ><b>{{ manageAmountFormat(round($totalorderamnt)) }}</b></td>
                                        <td  ></td>
                                        <td  ></td>
                                        <td ></td>

                                    </tfoot>
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
       function printBill(bill_id)
       {
          var confirm_text = 'bill';
          
          var isconfirmed=confirm("Do you want to print "+confirm_text+"?");
          if (isconfirmed) 
          {
            jQuery.ajax({
                url: '{{route('admin.orders.printmultiplebillreceipt')}}',
                type: 'POST',
                async:false,   //NOTE THIS
                data:{bill_id:bill_id},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                  },
              success: function (response) {
                var divContents = response;
                var printWindow = window.open('', '', 'width=400');
                printWindow.document.write('<html><head><title>Receipt</title>');
                printWindow.document.write('</head><body >');
                printWindow.document.write(divContents);
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.print();
              }
            });
          }
       }
   </script>
@endsection
