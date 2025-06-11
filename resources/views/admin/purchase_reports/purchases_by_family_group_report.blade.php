<table style="border:none; ">
    <?php
    $default_colspan = (!empty($is_pdf)) ? 2 : 1;
    ?>
    <tr>
        <td  colspan="3" ><b>{{$family_group_title}}</b></td>
    </tr>
    <?php if ($is_pdf) { ?>
        <tr> <td> </td> </tr>
        <tr> <td> </td> </tr>
        <tr> <td> </td> </tr>
        <tr> <td> </td> </tr>
        <tr> <td> </td> </tr>
    <?php } ?>

    <tr style="text-align: left;">
        <td  colspan="2"><b><?= $title ?></b></td>
        <td> <b>From:</b></td> 
        <td> {{ isset($start_date)?$start_date:'-' }}</td>
        <td> <b>To:</b></td> 
        <td> {{ isset($end_date)?$end_date:'-' }}</td>
    </tr>

    <?php if ($is_pdf) { ?>
        <tr> <td> </td> </tr>
        
    <?php } ?>


    <tr style="text-align: left;">
        <td colspan="<?= $default_colspan ?>"><b>Family Group</b></td>
        <td colspan="<?= $default_colspan ?>"><b>Total Amount</b></td>
    </tr>
    <!-- Dynamic code start -->

    <?php
    $grand_total = 0;
    ?>
    @foreach($data as $key => $row)
    <tr style="text-align: left;">
        <?php
        $famil_group_row = getFamilyGroupById($row->family_group);
        $grand_total += isset($row->total_cost_with_vat_sum) ? $row->total_cost_with_vat_sum : 0;
        ?>
        <td colspan="<?= $default_colspan ?>">
            <?= isset($famil_group_row->title) ? $famil_group_row->title : '' ?>
        </td>
        <td colspan="<?= $default_colspan ?>"><?= isset($row->total_cost_with_vat_sum) ? $row->total_cost_with_vat_sum : '' ?></td>
    </tr>
    @endforeach
    <?php if ($is_pdf) { ?>
        <tr> <td> </td> </tr>
    <?php } ?>

    <tr style="text-align: left;">
        <td colspan="<?= $default_colspan ?>"><b> Grand Total </b></td>
        <td colspan="<?= $default_colspan ?>"><b>{{ manageAmountFormat($grand_total) }}</b></td>
    </tr>





</table>

<style type="text/css">
    table{
        font-family: arial;
    }
</style>