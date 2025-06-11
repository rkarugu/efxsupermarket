
<style>
table {
 
    border-collapse: collapse;
    width: 100%;
    font-size: 11px;
    font-family: times new roman;
   
}

tr{
	border-bottom: 7px solid #fff;
}


<td, th {
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
<div style="width: 80%;padding-bottom: 30px;margin: auto;" class="clearfix" id="div_content">
<h2>Statement Of Account</h2>

<table class="table" style="width: 50%;
float: right;">
<tr><td  style="text-align: right;">
 <?php $cn = 0;?>
<?php $all_settings = getAllSettings();?>
 {{ $all_settings['COMPANY_NAME']}}</b></span><br>
{{ $all_settings['ADDRESS_1']}}<br>
{{ $all_settings['ADDRESS_2']}}<br>
{{ $all_settings['ADDRESS_3']}}<br>
Tel: {{ $all_settings['PHONE_NUMBER']}}<br>
{{ $all_settings['EMAILS']}}<br>
{{ $all_settings['WEBSITE']}}<br>
Pin No: {{ $all_settings['PIN_NO']}}<br><br>
  </td>
</tr>

<tr>
	<td style="text-align: right;">
		<b>Opening Balance : {{ manageAmountFormat($getOpeningBlance) }}</b><br>
  </td>
</tr>
</table>

 
<table class="table" style="width: 50%; float: left; text-align: left;">
<tr>
	<td  >
 A/C CODE: {{ @$supplier->supplier_code}}
  </td>
</tr>
<tr>
  <td  >
 A/C NAME: {{ ucfirst(@$supplier->name)}}
  </td>
</tr>
<tr>
<td>
DATE: {{ date('d/m/Y') }} TIME: {{ date('h:i') }}
   </td>
</tr>
<tr>
	<td>
From : {{ $date1 }} | To : {{ $date2 }}
  </td>
 </tr>

</table>
<br><br>
<br><br>
<br>
<br><br>
 <br>
<table class="table" style="width: 100%; float: none;">
<tr style="background-color: #ddd;">
<th style="text-align: left;">Date
  </th>
  <th style="text-align: left;">Reference
  </th>

   <th style="text-align: left;">Description
  </th>

     <th style="text-align: right;">Debit
  </th>
  </th>

     <th style="text-align: right;">Credit
  </th>
  </th>

     <th style="text-align: right;">Balance
  </th>

</tr>
<?php $total_amount= [];?>
 @foreach($lists as $list)

<tr>
	<td style="text-align: left;">{{ $list->trans_date }}</td>
	<td style="text-align: left;">{{ $list->suppreference }}</td>
    <td>{!! isset($number_series_list[$list->grn_type_number])?$number_series_list[$list->grn_type_number] : '' !!}-{{ $list->document_no?$list->document_no:'-' }}</td>
 
	<td style="text-align: right;">{{ ($list->total_amount_inc_vat > 0) ? manageAmountFormat($list->total_amount_inc_vat) :'' }}</td>
	<td style="text-align: right;">{{ ($list->total_amount_inc_vat < 0) ? manageAmountFormat($list->total_amount_inc_vat) :'' }}</td>

	<td style="text-align: right;">{{ manageAmountFormat($list->total_amount_inc_vat) }}</td>
</tr>
<?php $total_amount[] = $list->total_amount_inc_vat; ?>
@endforeach

<tfoot>
<tr>
	<td></td>
	<td></td>
	<th style="text-align: left;">Amount Overdue:</th>
	<td></td>
	<td></td>
	<td style="text-align: right; border-top: 1px solid #000;border-bottom: 1px solid #000;"><b>{{ manageAmountFormat($getOpeningBlance+array_sum($total_amount)) }}</b></td>
</tr>
</tfoot>

</table>

 

</div>









    





