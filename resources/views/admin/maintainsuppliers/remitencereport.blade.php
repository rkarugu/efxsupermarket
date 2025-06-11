
<style>
table {
 
    border-collapse: collapse;
    width: 100%;
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


<table class="table" style="width: 100%;">
<tr><td  style="text-align: center;">
 <?php $cn = 0;?>
 @foreach(getCompanyDropdownFromPreferences() as $c_name)
  @if($cn == 0)
    {{ $c_name }}
  @endif

 @endforeach
  </td>
</tr>

<tr><td  style="text-align: center;">
  CHEQUE PAYMENT VOUCHER
  </td>
</tr>
</table>


<table class="table" style="width: 100%;">
<tr><td  >
 A/C CODE: {{ $supplier->supplier_code}}
  </td>
  <td  >
 A/C NAME: {{ ucfirst($supplier->name)}}
  </td>
</tr>

<tr>
<td colspan="2" >
DATE: {{ date('d/m/Y') }}
  </td>
 
</tr>


<tr><td  >
TIME: {{ date('h:i') }}
  </td>
  <td  style="text-align: right;">
CHEQUE NO:______________
  </td>
</tr>

</table>
<br><br>
<table class="table" style="width: 100%;">
<tr style="border: 3px solid black;">
<th style="text-align: center;">DATE
  </th>
  <th style="text-align: center;">REFERENCE NO.
  </th>

   <th style="text-align: center;">DESCRIPTION
  </th>

     <th style="text-align: center;">AMOUNT
  </th>
</tr>
<?php $total_amount= [];?>
 @foreach($lists as $list)

<tr>
	<td style="text-align: center;">{{ $list->trans_date }}</td>
	<td style="text-align: center;">{{ $list->suppreference }}</td>
	<td style="text-align: center;">{{ $list->description?$list->description:'-' }}</td>
	<td style="text-align: center;">{{ manageAmountFormat($list->total_amount_inc_vat) }}</td>
</tr>
<?php $total_amount[] = $list->total_amount_inc_vat; ?>
@endforeach

<tr>
	<td></td>
	<td></td>
	<td></td>
	<td style="text-align: center;border-top: 1px dashed black;border-bottom: 1px dashed black;">{{ manageAmountFormat(array_sum($total_amount)) }}</td>
</tr>


</table>

<br><br><br><br><br><br>
<table class="table" style="width: 100%;" >
<tr><td  >
 PREPAIRED BY:________________
  </td>
  <td  >
 APPROVED BY:________________
  </td>
</tr>



</table>

<br><br>
<table class="table" style="width: 100%;">


<tr style="margin-top: 10px;"><td  >
 CHECKED BY:________________
  </td>
  <td  >
 AUTHORISED BY:________________
  </td>
</tr>

</table>






</div>









    





