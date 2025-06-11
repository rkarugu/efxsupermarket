<html>
    <title>Print</title>

    <head>
        <style type="text/css">
            .underline{
                text-decoration: underline;

            }

            .item_table td{
                border-right: 1px solid;"
                }
                .align_float_center
                {
                text-align:  center;
                }
                .makebold td{
                font-weight: bold;
                }

            </style>

        </head>
        <body>

            <?php $all_settings = getAllSettings(); ?>

            <span class= "heading"><b>{{ $all_settings['COMPANY_NAME']}}</b></span><br>
            {{ $all_settings['ADDRESS_1']}}<br>
            {{ $all_settings['ADDRESS_2']}}<br>
            {{ $all_settings['ADDRESS_3']}}<br>
            Tel: {{ $all_settings['PHONE_NUMBER']}}<br>
            {{ $all_settings['EMAILS']}}<br>
            {{ $all_settings['WEBSITE']}}<br>
            Pin No: {{ $all_settings['PIN_NO']}}<br>
            Vat No: {{ $all_settings['VAT_NO']}}<br>
            <div align="right" width="50%" >
                <table  width="30%" style="float: right;" class="makebold">
                    <tr>
                        <td width="50%">Date :</td>
                        <td width="50%">{!! date('Y-m-d',strtotime($row->requisition_date))!!}</td>
                    </tr>
                    <tr>
                        <td width="50%">Requisition No :</td>
                        <td  width="50%">{!! $row->requisition_no!!}</td>
                    </tr>

                </table>
                <div style="clear: both;">
                </div>

                <div align="center" ><b> Ferry Requistion</b></div>
                <table width="100%">
                    <tr>
                        <td width="45%" style="border: 1px solid;font-weight: bold;"><?php echo  isset($row->getRelatedFromLocationAndStore->location_name) ? $row->getRelatedFromLocationAndStore->location_name : '' ?></td>
                        <td width="10%" style="font-weight: bold; text-align: center;">To</td>
                        <td width="45%" style="border: 1px solid;font-weight: bold;"> <?php echo  isset($row->getRelatedToLocationAndStore->location_name) ? $row->getRelatedToLocationAndStore->location_name : '' ?></td>
                    </tr>

                </table>
                <br><br>
                <table border="1" width="100%" cellspacing="0" class="makebold">
                    <tr>

                        <td width="17%">Item No.</td>
                        <td width="40%">Description</td>
                        <td width="10%">Required QTY</td>
                        <td width="10%">Issued QTY</td>
                        <!-- <td width="17%">Unit</td> -->
                        <!-- <td width="10%">AVE</td>
                        <td width="10%">Total</td> -->
                    </tr>
                </table>
                <br>
                <table width="100%" >
                    <tr>

                        <td><b>The Following Aticle(s) have been processed successfully.</b></td>

                    </tr>
                </table>
                <hr><hr>

                <table  width="100%" >
                    <?php
                    $total_amount = []; 
                    $items = $row->getRelatedItem->where('issued_quanity','>',0);
                    ?>
                    @foreach($items as $item)
                    <tr>

                        <td width="17%">{!! $item->getInventoryItemDetail->stock_id_code!!}</td>
                        <td width="40%">{!! ucfirst($item->getInventoryItemDetail->title)!!}</td>
                        <td width="10%">{!! $item->quantity!!}</td>
                        <td width="10%">{!! $item->issued_quanity!!}</td>
                        <!-- <td width="17%">{!! $item->getInventoryItemDetail->getUnitOfMeausureDetail->title!!}</td> -->
                        <!-- <td width="10%">{!! manageAmountFormat($item->selling_price) !!}</td>
                        <td width="10%">{!! manageAmountFormat($item->issued_quanity*$item->selling_price) !!}</td> -->
                    </tr>
                    <?php $total_amount[] = $item->issued_quanity * $item->selling_price; ?>
                    @endforeach
                </table>

                <hr>

                <table  width="100%" class="makebold" >

                    <!-- <tr>

                        <td width="17%">Grand Total.</td>

                        <td width="66%"></td>
                        <td width="17%" style="border-bottom: 1px solid;">
                        {!! manageAmountFormat(array_sum($total_amount)) !!}</td>
                    </tr> -->

                    <tr>

                        <td width="17%"></td>

                        <td width="66%"></td>
                        <td width="17%" style="border-top: 1px solid;"></td>
                    </tr>

                </table>
                <br><br>

                <table  width="100%" class="makebold" >


                    <tr>

                        <td width="33%">--------------------------</td>

                        <td width="33%">--------------------------</td>
                        <td width="34%" style="text-align: right;" >------------------------------------------</td>
                    </tr>

                    <tr>

                        <td width="33%">Issued By</td>

                        <td width="33%" >Checked By</td>
                        <td width="34%" style="text-align: center;" >Received By</td>
                    </tr>




                </table>

                <br>
                <table  width="100%" class="makebold" >




                    <tr>

                        <td width="33%">Report Printed By: {!! getLoggeduserProfile()->name!!}</td>

                        <td width="33%" >Stores Department: {!! ucfirst($row->getDepartment->department_name)!!}</td>
                        <td width="34%" style="text-align: center;" ></td>
                    </tr>



                </table>
                <hr><hr>


                </body>
                </html>