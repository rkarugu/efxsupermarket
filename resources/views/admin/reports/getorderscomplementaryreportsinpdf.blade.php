<?php 
use App\Model\Restaurant;
?>


<table >
<tr>
    <td style = "border:none;" colspan="2" style="text-align: center"><b>Complementary reports with orders</b></td>

</tr>   

<tr>
    <td style = "border:none;"  ><b>Printed On:</b></td> 
     <td style = "border:none;"  ><b>{!! date('Y-m-d h:i A')!!}</b></td> 
</tr> 

<?php 
 if ($request->has('restaurant'))
        {
            $restro_detail = Restaurant::select(['name'])->whereId($request->input('restaurant'))->first();

            ?>

            <tr>
    <td style = "border:none;"  ><b>Restaurant:</b></td> 
     <td style = "border:none;"  ><b>{!! strtoupper($restro_detail->name) !!}</b></td> 
</tr> 
            <?php
           
        }


?>




<?php 
if ($request->has('start-date'))
        {
            ?>

            <tr>
    <td style = "border:none;"  ><b>Period From :</b></td> 
     <td style = "border:none;"  ><b>{!! date('d/m/Y h:i A',strtotime($request->input('start-date')))!!}</b></td> 
</tr> 

            <?php
           
          
        }
        if ($request->has('end-date'))
        {

            ?>

            <tr>
    <td style = "border:none;"  ><b>Period To :</b></td> 
     <td style = "border:none;"  ><b>{!! date('d/m/Y h:i A',strtotime($request->input('end-date')))!!}</b></td> 
</tr> 
           
      <?php  }?>

</table><br>
 
                                <table >
                                    <thead>
                                    <tr>
                                        <th >Order.No.</th>
                                        <th>Date & Time</th>
                                         <th >Item Desc</th>
                                       
                                        <th >Price</th>
                                      
                                        <th  >Reason Complementary</th>
                                        <!--th  >Complementary Code</th-->
                                         <th  >Employee Name</th>
                                         <th  >Family Groups</th>
                                       
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                    $total_amount = [];
                                    $total_discount = [];
                                    ?>

                                    @foreach($data as $list)
                                    
                                   
                                    <tr>
                                        <td>{!! manageOrderidWithPad($list->id) !!}</td>
                                        <td>{!! date('Y-m-d h:i A',strtotime($list->created_at)) !!}</td>
                                        <td> <?php 
                            $item_desc_array = [];
                           
                            ?>
                            @foreach($list->getAssociateItemWithOrder as $ordered_item)
                                <?php 
                                    $condiment_arr =  json_decode($ordered_item->condiments_json);
                                    $item_desc = 'Item: '.$ordered_item->item_title;
                                    $item_desc .= '<br>Qty: '.$ordered_item->item_quantity;
                                    $item_desc_array[] = $item_desc;
                                ?>
                            @endforeach  
                            {!! implode(' ,<br>',$item_desc_array)!!} </td>
                                       
                                        <td>{!! manageAmountFormat($list->order_final_price) !!}</td>
                                        <td>{!! $list->compliementary_reason !!}</td>
                                        <!--td>{!! $list->complimentry_code !!}</td-->

                                        
                                       
                                        <td>{!! isset($list->getAssociateComplimentaryUserDetail)?$list->getAssociateComplimentaryUserDetail->name:'-'!!}</td>
                                          <td>{!! getFamilyGroupsListByOrderId($list->id) !!}</td>

                                    </tr>
                                   
                                    @endforeach

                                    
                                 
                                    
                  
                                    </tbody>
                                </table>
                           
                                
                                    
<style>
  table td {
    border-right: 1px solid;
}
table th {
    border-right: 1px solid;
}
.last_row {
    /* border: 1px solid red; */
    border-right: 1px solid red !important;
    font-size: 20px;
    border: none !important;
}
</style>




