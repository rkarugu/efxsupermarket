<html>
    <head>
        <title>Return Demand {{ $demand->demand_no }}</title>

        <style type="text/css">
            .underline {
                text-decoration: underline;
            }

            .item_table td {
                border-right: 1px solid;
            }

            .align_float_center {
                text-align:  center;
            }

            .makebold td {
                font-weight: bold;
            }

            .table {
                font-family: arial, sans-serif;
                border-collapse: collapse;
                width: 100%;
                font-size: 10px;
            }

            .table td, th {
                border: 1px solid #dddddd;
                text-align: left;
                padding: 2px;
            }
        </style>
    </head>

    <body>
        <?php $all_settings = getAllSettings();?>

        <div style="width: 100%; height: auto; text-align:center;" >
            <span class= "heading"><b>{{ $all_settings['COMPANY_NAME']}}</b></span><br>
            {{ $all_settings['ADDRESS_1']}}<br>
            {{ $all_settings['ADDRESS_2']}}<br>
            {{ $all_settings['ADDRESS_3']}}<br>
            Tel: {{ $all_settings['PHONE_NUMBER']}}<br>
        </div>

        <h3 style="text-align: center;">Supplier Delta </h3>

        <div style="width: 50%; float: left;  height: auto;" >
            <table  width="100%" style="float: right;" class="makebold">
                <tr>
                    <td width="20%">Supplier </td>
                    <td  > {!! ':  '.$supplier->name !!}</td>
                </tr>
                <tr>
                    <td width="20%">Delta No</td>
                    <td > {{  ':  '.$demand->demand_no}}</td>
                </tr>
                <tr>
                    <td width="20%">Date :</td>
                    <td > {!! ':  '.$demand->created_at !!}</td>
                </tr>
            </table>
        </div>

        <div style="clear: both;"></div>

        <table border="1" width="100%" cellspacing="0" class="table">
            <tr>
                <th width="5%">#</th>	
                <th >Item No.</th>
                <th style="text-align: center  !important;">Quantity</th>
                <th style="text-align: right  !important;">Cost</th>
                <th style="text-align: right  !important;">Delta</th>
            </tr>

            @foreach($data as $i => $delta)
                <tr>
                    <th>{{ $i + 1 }}</th>
                    <td style="width:45% !important; ">{!! $delta->inventoryItem?->title !!}</td>
                    <td style="text-align: center;">{!! $delta->quantity !!}</td>
                    <td style="text-align: right;" >{!! manageAmountFormat($delta->cost) !!}</td>
                    <td style="text-align: right;" >{!! manageAmountFormat($delta->demand_cost) !!}</td>
                </tr>
            @endforeach
        </table>

        <hr>

        <table  width="100%" class="makebold" >
            <tr>
                <td colspan="4">Grand Total.</td>
                <td style="border-bottom: 1px solid; text-align:right;">{!! manageAmountFormat($demand->totalDemandCost()) !!}</td>
            </tr>
        </table>

        <br>

        <br>

        <hr>
    </body>
</html>