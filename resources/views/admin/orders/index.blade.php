
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
                                        
                                        <th>Order No</th>
                                        <th class="noneedtoshort">Date And Time</th>
                                        <th class="noneedtoshort">Table No</th>
                                        <th>No Of Guest</th>
                                        <th class="noneedtoshort">Item Description</th>
                                        <th class="noneedtoshort">Condiments</th>
                                        <th class="noneedtoshort">Waiter</th>
                                         <th>Branch</th>
                                         <th>Total Amount</th>
                                         <th class="noneedtoshort">Status</th>
                                       
                                        
                                        
                                      
                                       
                                       
                                      
                                        <th   class="noneedtoshort" >Action</th>
                                       
                                        <!--th style = "width:10%" class = "noneedtoshort">Date</th-->
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($lists) && !empty($lists))
                                        <?php $b = 1;?>
                                        @foreach($lists as $list)
                                         
                                            <tr>
                                               
                                                <td>{!! manageOrderidWithPad($list->id) !!}</td>
                                                 <td>{!! date('d/m/Y h:i A',strtotime($list->created_at)) !!}</td>
                                                 <td>{!! getAssociateTableWithOrder($list) !!}</td>
                                                 <td>{!! $list->total_guests !!}</td>
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


                                    if($condiment_arr && count($condiment_arr)>0)
                                    {

                                        foreach($condiment_arr as $condiment_data)
                                        {
                                            if($condiment_data->sub_items && count($condiment_data->sub_items)>0)
                                            {
                                                foreach($condiment_data->sub_items as $sub_items)
                                                {
                                                    if($sub_items->title)
                                                    {
                                                        $condiments[] = ucfirst($sub_items->title);
                                                    }
                                                }
                                                
                                                
                                            }
                                        }
                                      }



                                ?>
                            @endforeach  
                            {!! '<b>'.implode(' ,<br>',$item_desc_array).'</b>'!!}   
            </td>
            <td>{!! '<b>'.implode(' ,',$condiments).'</b>' !!}</td>
            <td>{!! getAssociateWaiteWithOrder($list) !!}</td>
            <td>{!! ucfirst($list->getAssociateRestro->name) !!}</td>
             <td>{!! manageAmountFormat($list->order_final_price) !!}</td>
             <td>{!! str_replace('_',' ',$list->status) !!}</td>



                                               
                                               
                                                
                                                <td class = "action_crud">
                                                    
                                                  <span>
                                                    <a title="Print Bill" href="javascript:void(0)" onclick="printBill({!! $list->id!!},{!! $logged_user_info->id !!},'B')"><i aria-hidden="true" class="fa fa-print" style="font-size: 20px;"></i>
                                                    </a>
                                                  </span>
                                                  <span>
                                                    <a title="Print Docket" href="javascript:void(0)" onclick="printBill({!! $list->id!!},{!! $logged_user_info->id !!},'D')"><i aria-hidden="true" class="fa fa-gitlab" style="font-size: 20px;"></i>
                                                    </a>
                                                  </span>
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
   <style type="text/css">
       .table td {
  font-size: 13px;
}

   </style>

   <script type="text/javascript">
       function printBill(order_id,user_id,print_type)
       {
          var confirm_text = 'bill';
          if(print_type == 'D')
          {
            confirm_text = 'docket';
          }
          var isconfirmed=confirm("Do you want to print "+confirm_text+"?");
          if (isconfirmed) 
          {
            jQuery.ajax({
                url: '{{route('admin.orders.receipt')}}',
                type: 'POST',
                async:false,   //NOTE THIS
                data:{order_id:order_id,user_id:user_id,print_type:print_type},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                  },
              success: function (response) {
                var divContents = response;
                var printWindow = window.open('', '', 'width=400');
                printWindow.document.write('<html><head><title>Bill</title>');
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
