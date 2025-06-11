<html>
<title>Print</title>

<head>
	<style type="text/css">
	body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            text-align: center;
            color: #777;
            margin: 0;
    padding: 0;
        }

        body h1 {
            font-weight: 300;
            margin-bottom: 0px;
            padding-bottom: 0px;
            color: #000;
        }

        body h3 {
            font-weight: 300;
            margin-top: 10px;
            margin-bottom: 20px;
            font-style: italic;
            color: #555;
        }

        body a {
            color: #06f;
        }

        .invoice-box {
            margin: auto;
            font-size: 11px;
            line-height: 20px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }
        .invoice-box *{
            font-size: 12px;
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        .invoice-box table td {
            padding: 3px;
            vertical-align: top;
        }

        .invoice-box table tr td:last-child {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 40px;
            line-height: 40px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            /* border-bottom: 1px solid #eee; */
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

	</style>
	
</head>
<body>

<?php $all_settings = getAllSettings();?>
<div class="invoice-box">
    <table  style="text-align: center;">
        <tbody>
            <tr class="top">
                <th colspan="2">
                    <h2 style="font-size:18px !important">{{ $all_settings['COMPANY_NAME']}}</h2>
                </th>
            </tr>
           
            <tr class="top">
                <td colspan="2" style="    text-align: center;">Salesman Return</td>
            </tr> 
            <tr class="top">
                <th colspan="1"  style="    text-align: left;">Salesman : {{$salesmanname}}</th>
                <th colspan="2"  style="    text-align: right;">Shift ID : {{@implode(', ',$shiftData)}}</th>
            </tr>
            <tr class="top">
            </tr>
		</tbody>        
    </table>

    <br>
    <table class="table table-bordered table-hover">
        <thead>
          <tr class="heading">                                    
              <th>Return No</th>
              <th>Date Returned</th>
              <th>Item Code</th>
              <th>Item Description</th>
              <th>Qty</th>                                      
              <th>Amount</th>                                      
              <th>Total Amount</th>                                      
          </tr>
        </thead>
        <tbody>
          <?php 
          $qty = [];
          $total_amount = [];
           ?>
          @foreach($returns as $key=> $val)
          <tr class="item">     
            <td>{{ $val->document_no }}</td>                                      
            <td>{{ date('d/m/Y',strtotime($val->created_at)) }}</td>                                      
            <td>{{ @$val->getInventoryItemDetail->stock_id_code }}</td>                                      
            <td>{{ @$val->getInventoryItemDetail->title }}</td>                                      
            <td>{{ manageAmountFormat(abs(@$val->qauntity)) }}</td>                                      
            <td>{{ manageAmountFormat(@$val->price) }}</td> 
            <td>{{ manageAmountFormat(abs($val->qauntity * $val->price)) }}</td>  
          </tr>
          <?php $total_amount[] = abs($val->qauntity * $val->price); $qty[] = abs(@$val->qauntity)?>
          @endforeach
        </tbody>
    
        <tfoot style="font-weight: bold;">
          <tr class="item">
           
            <td colspan="3">Grand Total</td>
            <td colspan="1">{{ manageAmountFormat(count($returns)) }}</td>
            <td colspan="1">{{ manageAmountFormat(array_sum($qty)) }}</td>
            <td colspan="1"></td>
            <td>{{ manageAmountFormat(array_sum($total_amount)) }}</td>
          </tr>
        </tfoot>
      </table>
      
</div>   

</body>
</html>