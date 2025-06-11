<?php 
use App\Model\Order;

$my_first_order = [];
$total_amount_fo_all_bills=[];
$counter = 1;
$total_order_count = count($orders);
$all_charges= [];
$total_discount = [];
?>
<style>
table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
   
}

td, th {
    text-align: left;
    padding: 2px 8px;
}
.managepaddingfotboth{
padding-top: 15px;
padding-bottom: 15px;

}
.managepaddingtop{
padding-top: 15px;
}
.managepaddingbottom{
padding-bottom: 15px;
}
</style>

@foreach($orders as $order_s)

<?php

  $order_detail = Order::whereId($order_s->order_id)->first();
  $my_first_order = $order_detail;
  $total_amount_fo_all_bills[] = $order_detail->order_final_price;
  $order_discountsdata = json_decode($order_detail->order_discounts);
 



  //dd($order_detail);
 ?>


<div style="width: 100%;padding-bottom: 30px;" class="clearfix" id="div_content">
  <table class="table" style="width: 100%;">

@if($counter==1)

    <tr><td colspan="3" style="text-align: center;">
  <b>{!! strtoupper($order_detail->getAssociateRestro->name)!!}</b>
  </td>
</tr>
<tr><td colspan="3" style="text-align: center;">
  <b>{!! strtoupper($order_detail->getAssociateRestro->location)!!}</b>
  </td>
</tr>
<tr><td colspan="3" style="text-align: center;">
   <b>{!! $order_detail->getAssociateRestro->telephone !!} MPESA TILL {!! $order_detail->getAssociateRestro->mpesa_till !!}</b>
  </td>
</tr>
<tr><td colspan="3" style="text-align: center;">
 <b>PIN : {!! $order_detail->getAssociateRestro->pin!!} VAT: {!! $order_detail->getAssociateRestro->vat !!}</b>
  </td>
</tr>
<tr><td colspan="3" style="text-align: center;">
<b> {!! $order_detail->getAssociateRestro->website_url!!}</b>
  </td>
</tr>
<tr><td colspan="3" style="text-align: center;">
 <b>{!! $order_detail->getAssociateRestro->email!!}</b>
  </td>
</tr>

<tr><td colspan="3" >
 {!! ucfirst(getAssociateWaiteWithOrderWithBadge($order_detail)) !!}
  </td>
</tr>

@endif


    <tr style="border-top: 2px dashed #000; border-bottom: 2px dashed #000">
      <td class= "managepaddingfotboth" style="text-align: left;">Tbl {!! getAssociateTableWithOrder($order_detail) !!}/1</td>
      <td class= "managepaddingfotboth" style="text-align: center;">Chk {!! manageOrderidWithPad($order_detail->id) !!} <br>{!! date('d M\' y h:i A',strtotime($order_detail->created_at))!!}</td>
      <td class= "managepaddingfotboth" style="text-align: center">Gst 2</td>
    </tr>
    <tr> <th colspan="3">EAT IN</th></tr>
    <?php  $order_charges =  json_decode($order_detail->order_charges); ?>
    @foreach($order_detail->getAssociateItemWithOrder as $ordered_item)
      @if(!$ordered_item->order_offer_id && $ordered_item->item_delivery_status != 'CANCLED' )
      <tr>
        <td colspan="2">{!! $ordered_item->item_quantity!!} {!! $ordered_item->item_title!!}</td>
        <td>{!! manageAmountFormat($ordered_item->item_quantity*$ordered_item->price) !!}</td>
      </tr>
      <?php 
      $condiment_arr =  json_decode($ordered_item->condiments_json);
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
                           ?>
                            <tr>
                              <td colspan="3" style="padding-left: 20px;">{!! ucfirst($sub_items->title) !!}</td>
                            </tr>
                            <?php
                          
                        }
                    }
                    
                    
                }
            }
          }
      ?>
  @endif
  @endforeach
  @foreach($order_detail->getAssociateOffersWithOrder as $ordered_offer)
    <tr>
      <td colspan="2">{!! $ordered_offer->quantity!!} {!! $ordered_offer->offer_title!!}</td>
      <td>{!! manageAmountFormat($ordered_offer->quantity*$ordered_offer->price) !!}</td>
    </tr>
  @endforeach
  @if($order_detail->order_type=='POSTPAID')

  @if($order_discountsdata && count($order_discountsdata)>0)
      @foreach($order_discountsdata as $discount_detail)
         @if(isset($discount_detail->discount_value) && $discount_detail->discount_value !="")
      <tr >
        <td class= "managepaddingtop" colspan="2">{!! $discount_detail->discount_value !!}% Discount</td>
        <td class= "managepaddingtop" >{!! manageAmountFormat($discount_detail->discount_amount) !!}</td>
      </tr>
      @endif
    @endforeach

    @endif 
  
  
    @if($order_charges && count($order_charges)>0)
      @foreach($order_charges as $charges)
       <?php 
      $charged_amount =  isset($charges->charged_amount)?$charges->charged_amount:0;
      if(isset($all_charges[$charges->charges_value.'___'.$charges->charges_name]))
      {

       $all_charges[$charges->charges_value.'___'.$charges->charges_name] = $all_charges[$charges->charges_value.'___'.$charges->charges_name]+$charged_amount;
      }
      else
      {
         $all_charges[$charges->charges_value.'___'.$charges->charges_name] = $charged_amount;
      }
    


    ?>
      
      @endforeach
    @endif
  @endif

  @if($order_detail->order_type=='PREPAID')

    @if($order_discountsdata && count($order_discountsdata)>0)
      @foreach($order_discountsdata as $discount_detail)
         @if(isset($discount_detail->discount_value) && $discount_detail->discount_value !="")
      <tr >
        <td class= "managepaddingtop" colspan="2">{!! $discount_detail->discount_value !!}% Discount</td>
        <td class= "managepaddingtop" >{!! manageAmountFormat($discount_detail->discount_amount) !!}</td>
      </tr>
      @endif
    @endforeach

    @endif 

  


    @if($order_charges && count($order_charges)>0)
      @foreach($order_charges as $charges)

      <?php 
      $charged_amount =  isset($charges->charged_amount)?$charges->charged_amount:0;
      if(isset($all_charges[$charges->charges_value.'___'.$charges->charges_name]))
      {

       $all_charges[$charges->charges_value.'___'.$charges->charges_name] = $all_charges[$charges->charges_value.'___'.$charges->charges_name]+$charged_amount;
      }
      else
      {
         $all_charges[$charges->charges_value.'___'.$charges->charges_name] = $charged_amount;
      }
    


    ?>

   
      
      @endforeach
    @endif
   

   
  @endif
</table>
</div>


<?php $counter++; ?>

@endforeach

@if(count($my_first_order)>0)

<table class="table" style="width: 100%;">


<?php 
$charge_counter = 1;
$charg_count = count($all_charges);?>        
<tr  colspan="2" style ="border:1px solid black;"></tr>


@if(!$my_first_order->complimentry_code)

<tr > 
<td colspan="2">
Total Amount @if($my_first_order->order_type!='PREPAID')To Be @endif Paid
</td>
<td style="text-align: center;"><b>{!! manageAmountFormat(array_sum($total_amount_fo_all_bills))!!}</b></td>
</tr>
@foreach($all_charges as $charge_key=>$charge_value)
<tr>
          <td colspan="2">
          
          <?php $charge_key_detail = explode('___',$charge_key);

          echo $charge_key_detail[0].'% '.$charge_key_detail[1];

          ?>

          </td>
          <td style="text-align: center;" >{!! manageAmountFormat($charge_value)!!}</td>
        </tr>
        <?php $charge_counter++;?>

@endforeach

@else

  <tr> <td colspan="2">
   COMPLAIND
    </td>
    <td style="text-align: center;"><b>{!! manageAmountFormat(array_sum($total_amount_fo_all_bills))!!}</b></td>
  </tr>
  <tr> <td colspan="2">
   100 %
    </td>
    <td style="text-align: center;"></td>
  </tr>
  <tr> <td colspan="2">
  COMP
    </td>
    <td style="text-align: center;">{!! manageAmountFormat(array_sum($total_amount_fo_all_bills))!!}-</td>
  </tr>

   <tr> <td colspan="2">
  COMP BILL
    </td>
    <td style="text-align: center;">0.00</td>
  </tr>
   <tr> <td colspan="2">
  COMP REASON
    </td>
    <td style="text-align: center;">{!! $my_first_order->compliementary_reason !!}</td>
  </tr>
  

@endif

<tr style="border-bottom: .5px solid #000" >
          <td colspan="3">
          
          

          </td>
         
        </tr>

@if($my_first_order->order_type=='PREPAID')
   <tr>
    <td colspan="3" class= "managepaddingtop" style="padding-left: 20px;">Name: ____________________________</td>
    </tr>
    <tr>
    <td colspan="3" style="padding-left: 20px;">Sign: _____________________________</td>
    </tr>
    <tr>
    <td colspan="3" style="padding-left: 20px;">Note: _____________________________</td>
    </tr>
    <tr>
    <td colspan="3" style="padding-left: 20px;">__________________________________</td>
    </tr>
    <tr>
    <td colspan="3" class= "managepaddingbottom" style="padding-left: 20px;">__________________________________</td>
    </tr>

@endif

        </table>

@endif





    





