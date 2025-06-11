
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
<b> {!! $order_detail->getAssociateRestro->website_url!!}</b>
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
</table>
</div>



 






