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
                <th colspan="1"  style="    text-align: left;">From : {{date('d/m/y',strtotime(@$request['from']))}}</th>
                <th colspan="1"  style="    text-align: left;"></th>
                <th colspan="1"  style="    text-align: center;">To : {{date('d/m/y',strtotime(@$request['to']))}}</th>
            </tr>
		</tbody>        
</table>



<br><br>

<div style="width:700px; margin-bottom: 20px !important;">
	<div style="width:20%; float:left;" class="col-sm-2">
		<span>Total Cost</span>
    <h2 style="margin-top:0;">{{manageAmountFormat($total_service_total)}}</h2>
	</div>

	<div style="width:20%; float:left;" class="col-sm-2">
		<span>Service Entry</span>
    <h3 style="margin-top:0;">{{manageAmountFormat($total_service_entry)}}</h3>
	</div>	

	<div style="width:20%; float:left;" class="col-sm-2">
		<span>Service Parts</span>
		<h3 style="margin-top:0;">{{manageAmountFormat($total_service_parts)}}</h3>
	</div>	

	<div style="width:20%; float:left;" class="col-sm-2">
		<span>Service Labor</span>
        <h3 style="margin-top:0;">{{manageAmountFormat($total_service_labor)}}</h3>
	</div>

    <div style="width:20%; float:left;" class="col-sm-2">
        <span>Service Task</span>
        <h3 style="margin-top:0;">{{manageAmountFormat($total_service_task)}}</h3>
    </div>  
</div>

<br><br><br>



<table width="100%" style="border: 1px solid; font-size:12px;" class="item_table " border="1" cellpadding="0" cellspacing="0">
	<tr style="font-weight: bold;font-size: 12px;">
	   <th style="text-align: left;">S.No.</th>
      <th style="text-align: left;">Vehicle</th>
        <th style="text-align: left;">Last Service Date</th>
        <th style="text-align: left;">Service Entries</th>
        <th style="text-align: left;">Service Tasks</th>
        <th style="text-align: left;">Parts</th>
        <th style="text-align: left;">Labour</th>
        <th style="text-align: left;">Total</th>
		
	</tr>
 @foreach($lists as $key => $list)
      <tr class="item">
          <td style="text-align: left; ">{!! ++$key !!}</td>
          <td style="text-align: left;">{!! $list->license_plate !!}</td>
          <td style="text-align: left;">{!! $list->last_service_date !!}</td>
          <td style="text-align: left;">{!! manageAmountFormat($list->vehicle_service_entry) !!}</td>
          <td style="text-align: left;">{!! manageAmountFormat($list->vehicle_service_task) !!}</td>
          <td style="text-align: left;">{!! manageAmountFormat($list->vehicle_service_parts) !!}</td>
          <td style="text-align: left;">{!! manageAmountFormat($list->vehicle_service_labor) !!}</td>
          <td style="text-align: left;">{!! manageAmountFormat($list->vehicle_service_total) !!}</td>
      </tr>
  @endforeach 
</table>
<hr>

<table width="100%" style="border: 1px solid; font-size:12px;" class="item_table">

      <tr class="item">
          <th colspan="3" style="text-align: left;">Total</th>
          <th style="text-align: right;">{{manageAmountFormat($total_service_entry)}}</th>
          <th style="text-align: right;">{{manageAmountFormat($total_service_task)}}</th>
          <th style="text-align: right;">{{manageAmountFormat($total_service_parts)}}</th>
          <th style="text-align: right;">{{manageAmountFormat($total_service_labor)}}</th>
          <th style="text-align: right;">{{manageAmountFormat($total_service_total)}}</th>
      </tr>
</table>


</body>
</html>