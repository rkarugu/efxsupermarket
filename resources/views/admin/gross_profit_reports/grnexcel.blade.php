<table style="border:none; ">
 <?php 
$logged_user_info = getLoggeduserProfile();
 ?>

     <tr style="text-align: left;">
        <td  colspan="2"><b>Deliveries By Suppliers</b></td>
    </tr>

      <tr style="text-align: left;">
        <td  colspan="2"><b>{{$restuarantname}}</b></td>
    </tr>
     <tr style="text-align: left;">

        <td > <b>Period From:</b></td> 
         <td  > {{ isset($start_date)?$start_date:'-' }}</td>
           <td > <b>To:</b></td> 
           <td > {{ isset($end_date)?$end_date:'-' }}</td>
    </tr>


    <tr style="text-align: left;">
    <td><b>Article</b></td>
     <td><b>Unit</b></td>
     <td><b>Quantity</b></td>
     <td><b>Net</b></td>
     <td><b>Vat</b></td>
     <td><b>Gross</b></td>


    </tr>
    <!-- Dynamic code start -->

     <?php 
    $main_qty = [];
    $main_vat = [];
    $main_net = [];
    $main_total = [];
    ?>


    @foreach($myData as $arr)
    <tr style="text-align: left;">
        <td colspan="6"><b>{{ $arr['supplier_name']}}</b></td>
    </tr>
    <?php 
    $sub_qty = [];
    $sub_vat = [];
    $sub_net = [];
    $sub_total = [];
    ?>
    @foreach($arr['items'] as $item)
        <tr style="text-align: center;">
    <td>{{ $item['item_description'] }}</td>
     <td>{{ $item['unit'] }}</td>
     <td>{{ $item['quantity'] }}</td>
     <td>{{ manageAmountFormat($item['nett']) }}</td>
     <td>{{ manageAmountFormat($item['vat_amount']) }}</td>
     <td>{{ manageAmountFormat($item['total_amount']) }}</td>

     <?php 
     $sub_qty[] = $item['quantity'];
     $main_qty[] = $item['quantity'];



     $sub_vat[] = $item['vat_amount'];
     $main_vat[] = $item['vat_amount'];

     $sub_net[] = $item['nett'];
     $main_net[] = $item['nett'];

     $sub_total[] = $item['total_amount'];
     $main_total[] = $item['total_amount'];

     ?>


    </tr>
    @endforeach

            <tr style="text-align: center;">
    <td colspan="2" style="text-align: right;"><b>Total for: {{ $arr['supplier_name']}}</b></td>
  
     <td><b>{{ array_sum($sub_qty) }}</b></td>
     <td><b>{{ manageAmountFormat(array_sum($sub_net)) }}</b></td>
     <td><b>{{ manageAmountFormat(array_sum($sub_vat)) }}</b></td>
     <td><b>{{ manageAmountFormat(array_sum($sub_total)) }}</b></td>


    </tr>
    @endforeach


     <tr style="text-align: center;">
    <td colspan="2" style="text-align: right;"><b>Report Total: </b></td>
  
     <td><b>{{ array_sum($main_qty) }}</b></td>
     <td><b>{{ manageAmountFormat(array_sum($main_net)) }}</b></td>
     <td><b>{{ manageAmountFormat(array_sum($main_vat)) }}</b></td>
     <td><b>{{ manageAmountFormat(array_sum($main_total)) }}</b></td>


    </tr>


    


    <!-- Dynamic code end -->


    





</table>

<style type="text/css">
    table{
        font-family: arial;
    }
</style>