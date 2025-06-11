<table style="border:none; ">
    <tr>
        <td  colspan="5" ><b>{{$family_group_title}}</b></td>
    </tr>
    <?php if ($is_pdf) { ?>
        <tr> <td> </td> </tr>
        <tr> <td> </td> </tr>
        <tr> <td> </td> </tr>
        <tr> <td> </td> </tr>
        <tr> <td> </td> </tr>
    <?php } ?>

    <tr style="text-align: left;">
        <td  colspan="4"><b>Purchases by Store Location</b></td>
        <td> <b>From:</b></td> 
        <td> {{ isset($start_date)?$start_date:'-' }}</td>
        <td> <b>To:</b></td> 
        <td> {{ isset($end_date)?$end_date:'-' }}</td>
    </tr>

    <?php if ($is_pdf) { ?>
        <tr> <td> </td> </tr>
        <tr> <td> </td> </tr>
        <tr> <td> </td> </tr>
    <?php } ?>

    <tr style="text-align: left;">
        <td><b>Family Group</b></td>
        <td><b>Item Code</b></td>
        <td><b>Description</b></td>
        <td><b>Units</b></td>
        <td><b>Delivery Date</b></td>
        <td><b>Store</b></td>
        <td><b>PLO No</b></td>
        <td><b>QTY Received</b></td>
        <td><b>Unit Price</b></td>
        <td><b>Amount</b></td>
    </tr>
    <!-- Dynamic code start -->

    <?php
    $grand_total = 0;
    ?>
    <?php
    foreach ($data as $key => $row_data) {
        //print_r($row_data); die;
        $sub_total = 0;
        ?>
        <tr style="text-align: left;">
            <td colspan="3"><b><?= $row_data['family_group']->title ?></b></td>
        </tr>
        <?php
        foreach ($row_data['data'] as $row_key => $order_item) {
            ?>
            <tr style="text-align: left;">
                <td>
                    <?= isset($order_item->getInventoryItemDetail->getInventoryCategoryDetail->getStockFamilyGroup->title) ? $order_item->getInventoryItemDetail->getInventoryCategoryDetail->getStockFamilyGroup->title : '' ?>
                </td>
                <td>
                    <?= isset($order_item->item_no) ? $order_item->item_no : '' ?>
                </td>
                <td>
                    <?= isset($order_item->getInventoryItemDetail->description) ? $order_item->getInventoryItemDetail->description : '' ?>
                </td>
                <td>
                    <?= isset($order_item->getInventoryItemDetail->getUnitOfMeausureDetail->title) ? $order_item->getInventoryItemDetail->getUnitOfMeausureDetail->title : '' ?>
                </td>
                <td>
                    <?= isset($order_item->getPurchaseOrder->purchase_date) ? $order_item->getPurchaseOrder->purchase_date : '' ?>
                </td>
                <td>
                    <?= isset($order_item->getPurchaseOrder->getStoreLocation->location_code) ? $order_item->getPurchaseOrder->getStoreLocation->location_code : '' ?>
                </td>
                <td>
                    <?= isset($order_item->purchase_no) ? $order_item->purchase_no : '' ?>
                </td>
                <td>
                    <?= isset($order_item->quantity) ? $order_item->quantity : '' ?>
                </td>
                <td>
                    <?= isset($order_item->getInventoryItemDetail->standard_cost) ? $order_item->getInventoryItemDetail->standard_cost : '' ?>
                </td>
                <td>
                    <?= isset($order_item->total_cost_with_vat) ? $order_item->total_cost_with_vat : '' ?>
                </td>

            </tr>
            <?php
            $sub_total += $order_item->total_cost_with_vat;
            $grand_total += $order_item->total_cost_with_vat;
        }
        ?>
        <tr style="text-align: left;">
            <td colspan="8"> </td>
            <td>Total </td>
            <td><b>{{ manageAmountFormat($sub_total) }}</b></td>
        </tr>
    <?php } ?>

    <?php if ($is_pdf) { ?>
        <tr> <td> </td> </tr>
    <?php } ?>
    <tr style="text-align: left;">
        <td colspan="8"> </td>
        <td>Grand Total </td>
        <td><b>{{ manageAmountFormat($grand_total) }}</b></td>
    </tr>




</table>

<style type="text/css">
    table{
        font-family: arial;
    }
</style>