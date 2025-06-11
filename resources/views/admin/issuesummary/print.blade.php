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





<table width="100%" style="border: 1px solid; font-size:12px;" class="item_table " border="1" cellpadding="0" cellspacing="0">
	<tr style="font-weight: bold;font-size: 12px;">
		<th style="text-align: left;">S.No.</th>
        <th style="text-align: left;">Issue</th>
        <th style="text-align: left;">Status</th>
        <th style="text-align: left;">Summary</th>
        <th style="text-align: left;">Assigned</th>
        <th style="text-align: left;">Due Date</th>
        <th style="text-align: left;">Due Meter</th>
        <th style="text-align: left;">Vehicle</th>
		
	</tr>
 @foreach($lists as $key => $list)
      <tr class="item">
          <td style="text-align: left; ">{!! ++$key !!}</td>
          <td style="text-align: left;">{!! $list->id !!}</td>
          <td style="text-align: left;">{!! $list->resolve !!}</td>
          <td style="text-align: left;">{!! $list->summary !!}</td>
          <td style="text-align: left;">{!! $list->assigned !!}</td>
          <td style="text-align: left;">{!! $list->due_date !!}</td>
          <td style="text-align: left;"> - </td>
          <td style="text-align: left;">{!! $list->Vehicle->license_plate !!}</td>
      </tr>
  @endforeach 
</table>
<hr>


</body>
</html>