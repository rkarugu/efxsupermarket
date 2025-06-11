<?php 
$base_url = URL::to('/');
$logged_user_info = getLoggeduserProfile();
$grand_total = 0;
 if($manage_request != 'xls'){ ?>
<style>
    
</style>
<?php } ?>
<table style="border:none; ">
    <tr>
        <td  colspan="3" ><b>{{$restuarantname}}</b></td>
      
    </tr>
    
    <tr>
        <td><b>Transfer Journal</b></td>
        <td> <b>From:</b></td> 
        <td> {{ isset($start_date)?$start_date:'-' }}</td>
        <td> <b>To:</b></td> 
        <td> {{ isset($end_date)?$end_date:'-' }}</td>
    </tr>


    <tr>
        <td><b>Article</b></td>
        <td><b>Quantity</b></td> 
        <td><b>Unit</b></td>
        <td><b>Ave Price</b></td>
        <td><b>Line Total</b></td>
    </tr>
    <?php foreach ($result as $key => $row){ ?>
        <tr>
            <td><?= $row->transfer_no ?></td>
            <td><?= $row->transfer_date ?></td>
            <td><b> From </b></td>
            <td><?= isset($row->fromStoreDetail->location_name) ? $row->fromStoreDetail->location_name : '' ?></td>
            <td><b> To </b></td>
            <td><?= isset($row->toStoreDetail->location_name) ? $row->toStoreDetail->location_name : '' ?></td>
        </tr>
        <?php 
            $items = $row->getRelatedItem;
            $total_cost_sum = 0;
            foreach($items as $item_key => $item_row) {
                $total_cost = isset($item_row->total_cost) ? $item_row->total_cost : 0;
                $total_cost_sum += $total_cost;
                $grand_total += $total_cost;
            ?>
        <tr>
            <td> <?= isset($item_row->getInventoryItemDetail->title) ? $item_row->getInventoryItemDetail->title . ' ('. $item_row->getInventoryItemDetail->stock_id_code .')' : '' ?></td>
            <td> <?= isset($item_row->quantity) ? $item_row->quantity : '' ?></td>
            <td> <?= isset($item_row->getInventoryItemDetail->getUnitOfMeausureDetail->title) ? $item_row->getInventoryItemDetail->getUnitOfMeausureDetail->title : '' ?></td>
            <td> <?= isset($item_row->standard_cost) ? number_format((float)$item_row->standard_cost, 2, '.', '') : '' ?></td>
            <td> <?= $total_cost ?></td>
        </tr>
        
        <?php } ?>
        <tr>
            <td> </td>
            <td> </td>
            <td> </td>
            <td><b> Total: </b></td>
            <td style="text-align: right;"><b> <?= number_format((float)$total_cost_sum, 2, '.', '') ?> </b></td>
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
            <td style="text-align: right;"><b> <?= number_format((float)$grand_total, 2, '.', '') ?> </b></td>
        </tr>
</table>

<style type="text/css">
    table{
        font-family: arial;
    }
</style>