
<div class="flash-message" onclick="this.remove();">
    <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">×</a></p>
    <?php if(!empty($grn_data)){ ?>
    <ul class="alert-info">
        <?php 
        foreach($grn_data as $key => $row){
        ?>
        <li>
            <span class="col-md-4">
                <?= $row->getRelatedSupplier->name ?>
            </span>
            
            <span class="col-md-2">
                <?= $row->standart_cost_unit ?>
            </span>
            
            <span class="col-md-2">
                <?= date('Y-m-d', strtotime($row->created_at)) ?>
            </span>
        </li>
        <?php } ?>
        <?php }
        else {?>
        <p class="alert-info">No Record found.</p>
        <?php } ?>
    </ul>
</div>