<html>
    <title>Print</title>

    <head>
    <style>
            html{
                margin:8px;
                padding:0;
            }
            .headr {font-size: 16px;}
            .makeborderbottom{           
            }
            th{
                padding:4px 5px;
                text-align:left;
            }
            td{               
                padding:4px 6px;
            }
            table *{
                font-size: 12px;
            }
        </style>

    </head>
    <body>
        <main>
        <table  width="100%" cellspacing="0" border="1">
            <thead>
                <tr>
                    <th width="10%">CODE</th>
                    <th width="40%">ITEM DESCRIPTION</th>
                    <th width="16%">UOM</th>
                    <th width="10%">QOH</th>
                    <th width="14%">Counted</th>
                    <th width="10%">Adjustment</th>
                </tr>
            </thead>
            <?php foreach ($items_by_location_category as $location_id => $items_by_category) { ?>
            
                <tr style="">
                    <td colspan="6">
                        <b>
                            {{ isset($location_list[$location_id]) ? $location_list[$location_id] : '' }}
                        </b>
                    </td>
                </tr>
            <?php foreach ($items_by_category as $category_id => $items) { ?>
               
                <tr style="">
                    <td colspan="6">
                        
                            {{ isset($category_list[$category_id]) ? $category_list[$category_id]['category_code'].' - '.$category_list[$category_id]['category_description'] : '' }}
                        
                    </td>
                </tr>
                <?php foreach ($items as $key => $row) { ?>

                    <tr>
                        <td width="10%" class="makeborderbottom"><?= isset($row->getAssociateItemDetail->stock_id_code) ? $row->getAssociateItemDetail->stock_id_code : '' ?></td>
                        <td width="40%" class="makeborderbottom"><?= isset($row->getAssociateItemDetail->title) ? $row->getAssociateItemDetail->title : '' ?></td>
                        <td width="16%"class="makeborderbottom"> <?= isset($row->uom) ? $row->getUomDetail?->title: '' ?></td>
                        <td width="10%" class="makeborderbottom"><?= $row->quantity_on_hand ?></td>
                        <td width="14%" class="makeborderbottom">
                        @if($row->quantity > '0')

                            <?= $row->quantity ?>
                            @else
                                <span style="font-size: 10px;">No Counts Entered</span>
                        @endif

                        </td>
                        <td width="10%" class="makeborderbottom"><?= $row->adjustment ?></td>
                        
                    </tr>
                <?php } ?>

            <?php } ?>
                    
            <?php } ?>


        </table>
        </main>
    </body>
</html>


