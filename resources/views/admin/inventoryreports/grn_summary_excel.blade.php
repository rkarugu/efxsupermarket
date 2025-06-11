<table style="border:none; ">
<?php 
$base_url = URL::to('/');
$logged_user_info = getLoggeduserProfile();
$grand_total = 0;
?>
    <tr>
        <td  colspan="2" ><b>GRN Summary Report</b></td>
        <td> <b>From:</b></td> 
        <td> {{ isset($start_date)?$start_date:'-' }}</td>
        <td> <b>To:</b></td> 
        <td> {{ isset($end_date)?$end_date:'-' }}</td>

    </tr>
    
    <tr style="text-align: left;">
    </tr>

    <?php foreach($result as $key => $row) { ?>
        <tr>
            <td>GRN No - <?= isset($row->getRelatedGrn->first()->grn_number) ? $row->getRelatedGrn->first()->grn_number : '' ?></td>
            <td> <b>Date Received : <?= $row->purchase_date ?></b></td>
            <td><b>Purchase Order No</b>: <?= $row->purchase_no ?></td> 
            <td><b>Supplier Name</b>: <?= isset($row->getSupplier->name) ? $row->getSupplier->name : '' ?></td>
        </tr>
        
        <tr>
            <td>Item No</td>
            <td>Description</td>
            <td>UOM</td>
            <td>Qty</td>
            <td>Cost</td>
            <td>VAT</td>
            <td>Total Cost</td>
        </tr>
        <?php
        $total = 0;
        foreach($row->getRelatedGrn as $key_item => $row_item) { 
            $invoice_info = json_decode($row_item->invoice_info);
            $nett = $invoice_info->order_price*$invoice_info->qty;
            $net_price = $nett;
            if($invoice_info->discount_percent > '0'){
                $discount_amount = ($invoice_info->discount_percent * $nett) / 100;
                $nett = $nett-$discount_amount;
            }

            $vat_amount = 0;
            if($invoice_info->vat_rate > '0') {
                $vat_amount = ($invoice_info->vat_rate*$nett)/100;
            }
            $total += $nett;
            $grand_total += $nett;
        ?>
            <tr>
                <td><?= $row_item->item_code ?></td>
                <td><?= $row_item->item_description ?></td>
                <td><?= isset($invoice_info->unit) ? $invoice_info->unit : '' ?></td>
                <td><?= $row_item->qty_received ?></td>
                <td><?= $row_item->standart_cost_unit ?></td>
                <td><?= $vat_amount ?></td>
                <td><?= $nett ?></td>
            </tr>
                
        
        <?php } ?>
            <?php if(empty($pdf)) { ?>
            <tr> </tr>
            <?php } ?>
            
            <tr>
                <td> </td>
                <td> </td>
                <td> </td>
                <td> </td>
                <td><b>Total </b></td>
                <td> </td>
                <td> <?= $total ?> </td>
            </tr>
            
    <?php } ?>
    <?php if(empty($pdf)) { ?>
        <tr> </tr>
    <?php } ?>
            
    <tr>
        <td> </td>
        <td> </td>
        <td> </td>
        <td> </td>
        <td><b>Grand Total </b></td>
        <td> </td>
        <td> <?= $grand_total ?> </td>
    </tr>
        
</table>

<style type="text/css">
    table{
        font-family: arial;
    }
</style>