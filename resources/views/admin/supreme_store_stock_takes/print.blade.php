<html>
    <title>Stock Take Sheet</title>

    <head>
        <style>
            html{
                margin:4px;
                padding:0;
            }
            .headr {font-size: 16px;}
            .makeborderbottom{           
            }
            th{
                padding:2px 4px;
                text-align:left;
            }
            td{               
                padding:2px 4px;
            }
            table *{
                font-size: 10px;
            }
        </style>

    </head>
    <body>
            {{-- <b class="headr">
                {{ strtoupper('STORE: '.@$data->getAssociateLocationDetail->location_name)}} 
                <span style="margin-left:20px">
                {{  strtoupper('BIN LOCATION: '.@$data->unit_of_measure->title)}}
                </span>
            </b>
    --}}
        <?php
            $toLines= 0;
        ?>
    <main>

        <table  width="100%" cellspacing="0" border="1">
            <thead>
                <tr>
                    <th width="8%">CODE</th>
                    <th width="28%">DESCRIPTION</th>
                    <th width="6%">UOM</th>
                    <th width="10%">Physical</th>
                    <th width="10%">Bal</th>
                    <th width="10%">Total</th>
                    <th width="10%" style="text-align:center">Bizwiz</th>
                    <th width="9%">Short</th>
                    <th width="9%">Over</th>
                
                </tr>
            </thead>
            <tbody>
            <?php foreach ($items_by_category as $category_id => $items) { ?>
                <tr style="">
                    <td colspan="9" style="padding-left:10px">
                        <b>
                            {{ isset($category_list[$category_id]) ? $category_list[$category_id]['category_code'].' - '.$category_list[$category_id]['category_description'] : '' }}
                        </b>
                    </td>
                    {{-- <td colspan="2">
                        <b>
                            BIN LOCATION : {{ isset($items[0]) ? $items[0]->wa_unit_of_measure : NULL }}
                        </b>
                    </td> --}}
                </tr>
                <?php
                        $thisCategoryLines = 0;
                ?>
                <?php foreach ($items as $key => $row) { ?>

                    <tr>
                        <td class="makeborderbottom"><?= @$row->getAssociateItemDetail->stock_id_code; ?></td>
                        <td class="makeborderbottom"><?= @$row->getAssociateItemDetail->title; ?></td>
                        <td class="makeborderbottom"> <?= @$row->getAssociateItemDetail->pack_size->title; ?></td>
                        <td class=""></td>
                        <td class=""></td>
                        <td class=""></td>
                        <td style="text-align:center">{{$row->quantity_on_hand}}</td>
                        <td class=""></td>
                        <td class=""></td>
                     
                        
                    </tr>
                    <?php
                        $thisCategoryLines++;
                ?>
                   
                <?php } ?>
                <tr>
                    <td colspan="9"  style="padding-left:10px">LINES : {{ $thisCategoryLines }}</td>                     
                </tr>
                <?php
                        $toLines+=$thisCategoryLines;
                    ?>
            <?php } ?>
          
            </tbody>
        </table>
        <table  width="100%" cellspacing="0" border="1" style="border-top: 0px solid">
            
            <tr>
                <td colspan="9"  style="padding-left:10px">TOTAl LINES : {{ $toLines }}</td>                     
            </tr>
            <tr>
                <td colspan="9"  style="padding-left:10px;height: 70px">SIGNED BY : STORE SUPERVISOR</td>                     
            </tr>
            <tr>
                <td colspan="9"  style="padding-left:10px;height: 70px">SIGNED BY : STORE OBSERVER</td>                     
            </tr>
            <tr>
                <td colspan="9"  style="padding-left:10px;height: 70px">SIGNED BY : DIR</td>                     
            </tr>
            
        </table>
        </main>
    </body>
</html>


