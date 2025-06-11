@if($print_type=='B')
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
<div style="width: 100%;padding-bottom: 30px;" class="clearfix" id="div_content">


<table class="table" style="width: 100%;">
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
<b>{!! $order_detail->getAssociateRestro->email!!}</b>
  </td>
</tr>
<tr><td colspan="3" style="text-align: center;">
<b>{!! $order_detail->getAssociateRestro->email!!}</b>
  </td>
</tr>

<tr><td colspan="3" style="padding: 5px 8px;">
 {!! ucfirst(getAssociateWaiteWithOrderWithBadge($order_detail)) !!}
  </td>
</tr>
<tr style="border-top: 2px dashed #000; border-bottom: 2px dashed #000">
    <td class= "managepaddingfotboth" style="text-align: left;">Tbl {!! getAssociateTableWithOrder($order_detail) !!}/1</td>
    <td class= "managepaddingfotboth" style="text-align: center;">Chk {!! manageOrderidWithPad($order_detail->id) !!} <br>{!! date('d M\' y h:i A',strtotime($order_detail->created_at))!!}</td>
    <td class= "managepaddingfotboth" style="text-align: center">Gst 2</td>
  </tr>
  <tr>
    <th colspan="3">EAT IN</th>
    
  </tr>
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
  <tr >
    <td class= "managepaddingtop"colspan="2">{!! date('h:i A')!!} {!! $order_detail->order_type=='PREPAID'?'Paid':'Due'!!}</td>
      <td class= "managepaddingtop" style="font-weight: bold; font-size:20px;">{!! manageAmountFormat($order_detail->order_final_price) !!}</td>
      </tr>


       @if($order_charges && count($order_charges)>0)
        @foreach($order_charges as $charges)
        <tr>
    <td colspan="2" style="margin-bottom: 40px;">  {!! $charges->charges_value !!}%  {!! $charges->charges_name!!}</td>
      <td >{!! isset($charges->charged_amount)?manageAmountFormat($charges->charged_amount):'N/A' !!}</td>
      </tr>

         @endforeach
       @endif
       @endif



  @if($order_detail->order_type=='PREPAID')
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
  <tr >
    <td class= "managepaddingtop" colspan="2">Total</td>
      <td class= "managepaddingtop" style="font-weight: bold; font-size:20px;">{!! manageAmountFormat($order_detail->order_final_price) !!}</td>
      </tr>

       <tr >
    <td class= "managepaddingbottom" colspan="2">Paid</td>
      <td class= "managepaddingbottom" style="font-weight: bold; font-size:20px;">{!! manageAmountFormat($order_detail->order_final_price) !!}</td>
      </tr>


       @if($order_charges && count($order_charges)>0)
        @foreach($order_charges as $charges)
        <tr>
    <td colspan="2">  {!! $charges->charges_value !!}%  {!! $charges->charges_name!!}</td>
      <td >{!! isset($charges->charged_amount)?manageAmountFormat($charges->charged_amount):'N/A' !!}</td>
      </tr>

         @endforeach
       @endif
       <tr><td colspan="3" style="text-align: center;">
 --------------- {!! date('d M\' y h:i A',strtotime($order_detail->updated_at))!!} ---------------
  </td>
</tr>

<tr><td class= "managepaddingtop" colspan="3" style="text-align: center;">
 --------------------------------------
  </td>
</tr>
<tr><td  colspan="3" style="text-align: center;">
 --------------------------------------
  </td>
</tr>
<tr><td colspan="3" style="text-align: center;">
 --------------------------------------
  </td>
</tr>
<tr><td  colspan="3" style="text-align: center;">
 --------------------------------------
  </td>
</tr>
<tr><td  colspan="3" style="text-align: center;">
 --------------------------------------
  </td>
</tr>
<tr><td  colspan="3" style="text-align: center;">
 --------------------------------------
  </td>
</tr>
<tr><td  colspan="3" style="text-align: center;">
 --------------------------------------
  </td>
</tr>
<tr><td  colspan="3" style="text-align: center;margin-bottom: 40px;">
 --------------------------------------
  </td>
</tr>
       @endif






</table>
</div>
@endif


@if($print_type=='D' && $user_type == 'A')

<div style="width: 100%;" class="clearfix" id="div_content">
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
<table class="table" style="width: 100%;">

  <tr><td colspan="3" >
  <b>Order No: {!! manageOrderidWithPad($order_detail->id) !!}</b>
  </td>
</tr>
 <tr><td colspan="3" >
  <b>Table No: {!! getAssociateTableWithOrder($order_detail) !!}</b>
  </td>
</tr>
<tr><td colspan="3" >
  <b>Date and Time: {!! date('d M\' y h:i A',strtotime($order_detail->created_at))!!}</b>
  </td>
</tr>

@if($order_detail->getAssociateUserForOrder->role_id == '11')
  <tr>
    <td colspan="3">
      <b>Customer Name: {!! ucfirst($order_detail->getAssociateUserForOrder->name ) !!}</b>
    </td>
  </tr> 
@endif




 <tr><td colspan="3" class= "managepaddingbottom" >
  <b>Waiter Name: {!! ucfirst(getAssociateWaiteWithOrderWithBadge($order_detail)) !!}</b>
  </td>
</tr>


    

    


  <?php  $order_charges =  json_decode($order_detail->order_charges); ?>
  @foreach($order_detail->getAssociateItemWithOrder as $ordered_item)
  @if(!$ordered_item->order_offer_id && $ordered_item->item_delivery_status != 'CANCLED' )
  <tr>
    <td colspan="3" style="padding-left: 20px;">{!! $ordered_item->item_quantity!!} {!! $ordered_item->item_title!!}  @if($ordered_item->item_comment && $ordered_item->item_comment != '')({!! $ordered_item->item_comment!!}) @endif</td>
    
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
                            <td colspan="3" style="padding-left: 40px;">{!! ucfirst($sub_items->title) !!}</td>
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

    @foreach($ordered_offer->getAssociateItemwithOffers as $offer_item)
     
  
   @if($offer_item->item_delivery_status != 'CANCLED' )
  <tr>
    <td colspan="3" style="padding-left: 20px;">{!! $offer_item->item_quantity!!} {!! $offer_item->item_title!!} @if($offer_item->item_comment && $offer_item->item_comment != '')({!! $offer_item->item_comment!!}) @endif</td>
    
  </tr>
    <?php 
      $condiment_arr =  json_decode($offer_item->condiments_json);


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
                            <td colspan="3" style="padding-left: 40px;">{!! ucfirst($sub_items->title) !!}</td>
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
  @endforeach

  
  <tr style="border-bottom: 1px solid black; height: 120px;"></tr>

   
</table>
</div>
@endif

@if($print_type=='D' && $user_type == 'P')

<div style="width: 100%;" class="clearfix" id="div_content">
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
<table class="table" style="width: 100%;">


  <tr><td colspan="3" >
  <b>Order No: {!! manageOrderidWithPad($order_detail->id) !!}</b>
  </td>
</tr>
 <tr><td colspan="3" >
  <b>Table No: {!! getAssociateTableWithOrder($order_detail) !!}</b>
  </td>
</tr>
<tr><td colspan="3" >
  <b>Date and Time: {!! date('d M\' y h:i A',strtotime($order_detail->created_at))!!}</b>
  </td>
</tr>

<tr><td colspan="3" >
  <b>Print Class Name: {!! $user_detail->printClassUserPrintClass->name !!}</b>
  </td>
</tr>

@if($order_detail->getAssociateUserForOrder->role_id == '11')
  <tr>
    <td colspan="3">
      <b>Customer Name: {!! ucfirst($order_detail->getAssociateUserForOrder->name ) !!}</b>
    </td>
  </tr> 
@endif

<tr><td colspan="3" class= "managepaddingbottom" >
  <b>Waiter Name: {!! ucfirst(getAssociateWaiteWithOrderWithBadge($order_detail)) !!}</b>
  </td>
</tr>



  <?php  $order_charges =  json_decode($order_detail->order_charges); ?>
  @foreach($order_detail->getAssociateItemWithOrder as $ordered_item)
  @if(!$ordered_item->order_offer_id && $ordered_item->item_delivery_status != 'CANCLED' && $ordered_item->print_class_id == $user_detail->print_class_id )
  <tr>
    <td colspan="3" style="padding-left: 20px;">{!! $ordered_item->item_quantity!!} {!! $ordered_item->item_title!!} @if($ordered_item->item_comment && $ordered_item->item_comment != '')({!! $ordered_item->item_comment!!}) @endif</td>
    
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
                            <td colspan="3" style="padding-left: 40px;">{!! ucfirst($sub_items->title) !!}</td>
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

    @foreach($ordered_offer->getAssociateItemwithOffers as $offer_item)
     
  
   @if($offer_item->item_delivery_status != 'CANCLED' && $offer_item->print_class_id == $user_detail->print_class_id )
  <tr>
    <td colspan="3" style="padding-left: 20px;">{!! $offer_item->item_quantity!!} {!! $offer_item->item_title!!} 
    @if($offer_item->item_comment && $offer_item->item_comment != '')({!! $offer_item->item_comment!!}) @endif
    </td>
    
  </tr>
    <?php 
      $condiment_arr =  json_decode($offer_item->condiments_json);


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
                            <td colspan="3" style="padding-left: 40px;">{!! ucfirst($sub_items->title) !!}</td>
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
  @endforeach

   <tr style="border-bottom: 1px solid black; height: 120px;"></tr>

   
</table>
</div>
@endif
    





