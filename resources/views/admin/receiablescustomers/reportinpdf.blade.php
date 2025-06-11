
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

<?php $all_settings = getAllSettings();?>
<span class= "heading"><b>{{ $all_settings['COMPANY_NAME']}}</b></span><br>
{{ $all_settings['ADDRESS_1']}}<br>
{{ $all_settings['ADDRESS_2']}}<br>
{{ $all_settings['ADDRESS_3']}}<br>
Tel: {{ $all_settings['PHONE_NUMBER']}}<br>
{{ $all_settings['EMAILS']}}<br>
{{ $all_settings['WEBSITE']}}<br>
Pin No: {{ $all_settings['PIN_NO']}}<br>
Vat No: {{ $all_settings['VAT_NO']}}<br><br>

<div align="center"><b><?php echo $heading ?></b></div><br/><br/>

<div style="width: 100%;padding-bottom: 30px;font-size: 10px;" class="clearfix" id="div_content">
  <table class="table" style="width: 100%;font-size: 11px;" >







   


<tr>
	<td colspan="3" >
	<span style="float: left !important;"> <b>Customer name:</b> <?php echo @$row->customerDetail->customer_name ;?></span>
	<span style="float: right !important;"> <b>Printed At :</b>{!! date('d M\' y h:i A')!!}</span>
	</td>
</tr>

<tr>
	<td colspan="3" >
	<span style="float: left !important;"> <b>Address:</b> <?php echo @$row->customerDetail->address ;?></span>
	<span style="float: right !important;"> <b>Date :</b><?php echo date('Y-m-d',strtotime($row->input_date)) ;?></span>
	</td>
</tr>


<tr>
	<td colspan="3" >
	<span style="float: left !important;"> <b>Country:</b> <?php echo @$row->customerDetail->country ;?></span>
	<span style="float: right !important;"> <b>Time :</b><?php echo date('h:i A',strtotime($row->input_date)) ;?></span>
	</td>
</tr>

<tr>
	<td colspan="3" >
	<span style="float: left !important;"> </span>
	<span style="float: right !important;"> <b>Receipt No. :</b><?php echo $row->document_no ;?></span>
	</td>
</tr>






        </table></div>


        <table class="table" style="clear: both; border:1px solid #ccc;font-size: 14px !important; " >
    <thead>
 <tr style="border-bottom:  1px solid #ccc;">
    
        <th colspan="3" style="text-align: center;"> PAYMENT RECEIPT </th>

      </tr>

      <tr style="border: 1px solid #ccc;" >
        <th style="text-align: left;">Date</th>
        <th style="text-align: left;">Description</th>
        <th style="text-align: left;">Amount</th>
      </tr>
    </thead>
    <tbody>

<tr>
  <td style="border: 1px solid #ccc;"><?php echo $row ? date('Y-m-d',strtotime(@$row->trans_date)) : '';?></td>
  <td style="border: 1px solid #ccc;"><?php echo @$bank_tran->getPaymentMethod->title .'  ('.@$row->reference.')' ;?></td>
  <td style="border: 1px solid #ccc;"><?php echo manageAmountFormat(abs(@$row->amount));?></td>
</tr>
      
    </tbody>
  </table>

  <p  style="text-align: center;margin: 30px 0px;font-size: 14px !important;"> <b> Amount</b>  <span  style="border-bottom: 1px solid #ccc;"><?php echo strtoupper(getCurrencyInWords(abs($bank_tran->amount ?? 0.00)));?> ONLY</span> </p>


<br/><br/><br/>
<b style="float: left;font-size: 14px !important;">Cashier Signature</b>

<b style="float: right;font-size: 14px !important;">Customer Signature</b>








    





