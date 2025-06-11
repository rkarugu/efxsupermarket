<?php
$base_url = URL::to('/');
$logged_user_info = getLoggeduserProfile();
$grand_total = 0;
if($manage_request != 'xls'){ ?>
<style>
    td{
        width: 100%
    }
    </style>
<?php } ?>

<table>
<?php 

?>
    <tr>
        <?php /* <td colspan="3"><b>The Big Five Breweries Limited {{ $title }}</b></td> */ ?>
        <td colspan="3"><b> {{$restuarantname}} </b></td>
        
    </tr>
    
    <tr>
        <td  colspan="2"><b>Transfers by Store / Item Group Detailed</b></td>
        <td> <b>From:</b></td> 
        <td> {{ isset($start_date)?$start_date:'-' }}</td>
        <td> <b>To:</b></td> 
        <td> {{ isset($end_date)?$end_date:'-' }}</td>
    </tr>


    <tr>
        <td><b>Article</b></td>
        <td><b>Unit</b></td>
        <td><b>Quantity</b></td> 
        <td><b>Price</b></td>
        <td><b>Total</b></td>
    </tr>
    <?php foreach ($data_formatted as $key => $row){
        $key_ex = explode('-', $key);
        list($from_location_id, $to_location_id) = $key_ex;
        $from_location_row = getlocationRowById($from_location_id);
        $to_location_row = getlocationRowById($to_location_id);
        ?>
        <tr>
            <td><b> From Store: </b></td>
            <td colspan="2"><b><?= isset($from_location_row->location_name) ? $from_location_row->location_name : '' ?></b></td>
        </tr>
        <tr>
            <td><b> To Store: </b></td>
            <td colspan="2"><b><?= isset( $to_location_row->location_name) ? $to_location_row->location_name : '' ?></b></td>
        </tr>
        <?php 
            //$items = $row->getRelatedItem;
            $total_cost_sum = 0;
            foreach($row as $key_item => $item_rows){
                $quantity_total = $price_total = 0;
                foreach($item_rows as $item_key => $item_row) {
                    $quantity_total += isset($item_row->quantity) ? $item_row->quantity : 0;
                    $price_total += isset($item_row->total_cost) ? $item_row->total_cost : 0;
                    
                }
                $total_cost_sum += $price_total;
                $grand_total  += $price_total;
                ?>
                <tr>
                    <td> <?= isset($item_row->getInventoryItemDetail->title) ? $item_row->getInventoryItemDetail->title . ' ('. $item_row->getInventoryItemDetail->stock_id_code .')' : '' ?></td>

                    <td> <?= isset($item_row->getInventoryItemDetail->getUnitOfMeausureDetail->title) ? $item_row->getInventoryItemDetail->getUnitOfMeausureDetail->title : '' ?></td>

                    <td> <?= $quantity_total ?></td>
                    
                    <td style="text-align: right;"> <?= isset($item_row->standard_cost) ? manageAmountFormat($item_row->standard_cost) : '' ?></td>
                    <td style="text-align: right;"> <?= manageAmountFormat($price_total) ?></td>
                </tr>
            <?php } ?>
            <tr>
                <td> </td>
                <td> </td>
                <td> </td>
                <td><b> Total: </b></td>
                <td style="text-align: right;"><b> <?= manageAmountFormat($total_cost_sum) ?> </b></td>
            </tr>
    <?php } ?>
        <tr>
            <td> </td>
        </tr>
        <tr>
            <td> </td>
            <td> </td>
            <td> </td>
            <td><b>Grand Total: </b></td>
            <td style="text-align: right;"><b> <?= manageAmountFormat($grand_total) ?> </b></td>
        </tr>
</table>

<style type="text/css">
    table{
        font-family: arial;
    }
</style>