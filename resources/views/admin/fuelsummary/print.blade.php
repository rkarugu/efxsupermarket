<html>
<title>Print</title>

<head> 
	<style type="text/css">
	.underline{
		 text-decoration: underline;

	}

	.item_table td{
		border-right: 1px solid;"
		}
		.align_float_right
{
  text-align:  right;
}

.align_float_center
{
  text-align:  center;
}

td {
  padding: 7px;
}

 th {
  padding: 10px;
}

body { font-family:'Source Sans Pro','Helvetica Neue',Helvetica,Arial,sans-serif; }

	</style>
	
</head>
<body>

<?php $all_settings = getAllSettings();?>
<table  style="width: 100%; text-align: center;">
        <tbody>
            <tr class="top">
                <th colspan="3">
                    <h2 style="font-size:18px !important">{{ $all_settings['COMPANY_NAME']}}</h2>
                </th>
            </tr>
            <!-- <tr class="top">
                <td colspan="3" style="    text-align: center;">{{@$all_settings['ADDRESS_2']}} {{@$all_settings['ADDRESS_3']}}. TEL: {{@$all_settings['PHONE_NUMBER']}}</td>
            </tr>
            <tr class="top">
                <th colspan="3"  style="    text-align: center;">Equity Bank Deposit</th>
            </tr> -->
            <tr class="top">
                <th colspan="1"  style="    text-align: left;">From : {{date('d/m/y',strtotime(@$request['from']))}} &nbsp; To : {{date('d/m/y',strtotime(@$request['to']))}}</th>
                <th colspan="2"></th>
            </tr>
		</tbody>        
</table>



 <br><br>

  


<div style="width:700px; margin-bottom: 20px !important;">
	<div style="width:30%; float:left;" class="col-sm-2">
		<span>Total Cost</span>
    <h2 style="margin-top:0;">{{manageAmountFormat($grand_total)}}</h2>
	</div>

	<div style="width:30%; float:left;" class="col-sm-2">
		<span>Total Odometer</span>
    <h3 style="margin-top:0;">{{manageAmountFormat($total_odometer)}}</h3>
	</div>	

	<div style="width:30%; float:left;" class="col-sm-2">
		<span>Total Litres</span>
		<h3 style="margin-top:0;">{{manageAmountFormat($total_fuel_economy)}}</h3>
	</div>	

	<div style="width:30%; float:left;" class="col-sm-2">
		<span>Total Cost</span>
    <h3 style="margin-top:0;">{{manageAmountFormat($total_price)}}</h3>
	</div>	
</div> 

<br><br><br>



<table width="100%" style="border: 1px solid; font-size:12px;" class="item_table " border="1" cellpadding="0" cellspacing="0">
	<tr style="font-weight: bold;font-size: 12px;">
			<th style="text-align: left;">S.No.</th>
            <th style="text-align: left;">Date</th>
            <th style="text-align: left;">Fuel Type</th>
            <th style="text-align: left;">Odometer</th>
            <th style="text-align: left;">Litres</th>
            <th style="text-align: left;">Cost</th>
            <th style="text-align: left;">Total</th>
	</tr>
     @foreach($lists as $key => $list)
          <tr class="item">
              <td style="text-align: right; ">{!! ++$key !!}</td>
              <td style="text-align: right;">{!! $list->fuel_entry_date !!}</td>
              <td style="text-align: right;">{!! $list->fuel_type !!}</td>
              <td style="text-align: right;">{!! manageAmountFormat($list->odometer) !!}</td>
              <td style="text-align: right;">{!! manageAmountFormat($list->gallons) !!}</td>
              <td style="text-align: right;">{!! manageAmountFormat($list->price) !!}</td>
              <td style="text-align: right;">{!! manageAmountFormat($list->total) !!}</td>
          </tr>
      @endforeach 

      <tr class="item">
          <th colspan="3" style="text-align: left;">Total</th>
          <th style="text-align: right;">{{manageAmountFormat($total_odometer)}}</th>
          <th style="text-align: right;">{{manageAmountFormat($total_fuel_economy)}}</th>
          <th style="text-align: right;">{{manageAmountFormat($total_price)}}</th>
          <th style="text-align: right;">{{manageAmountFormat($grand_total)}}</th>
      </tr>
</table>
<hr>




</body>
</html>