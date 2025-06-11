<table>
    <tr collspan="6" >
        <td collspan="6" ><strong>{{ getAllSettings()['COMPANY_NAME'] }}</strong></td>
    </tr>
    @foreach($lists as $arr)
    <tr>
         <td colspan="{{isset($selectedMonthArr['m']) && !is_null($selectedMonthArr['m'])?count($selectedMonthArr['m'])+2:''}}">
         <b>{{ $arr['name']}}</b></td>
    </tr>
    @endforeach
        <tr collspan="6" >
            <td collspan="6" ><strong>Supplier Sales Products Report</strong></td>
        </tr>
</table>

                    @if($monthRange<=12)

                        <table class="table table-bordered table-hover">
                            <tr>
                                <td style="text-align: left;"><b>Item</b></td>
                                <td style="text-align: center;"><b>Current QOH</b></td>
                                @if(isset($selectedMonthArr['m']) && !is_null($selectedMonthArr['m']))

                                    @foreach($selectedMonthArr['m'] as $key => $month)

                                        <td style="text-align: right;"><b>{{getMonthsNameToNumber($month)}}</b></td>
                                    @endforeach
                                @endif
                            </tr>
                            <!-- Dynamic code start -->

                                <?php
                                $main_qty = [];
                                $main_vat = [];
                                $main_net = [];
                                $main_total = [];

                                $final_arr = [];
                                $new_final_arr = [];

                                ?>
                            @if(isset($selectedMonthArr['m']) && !is_null($selectedMonthArr['m']))

                            @foreach($selectedMonthArr['m'] as $key => $month)
                            @php
                            $totalMonthlyStock[$key] = 0;

                            @endphp

                            @endforeach
                            @endif



                            @foreach($lists as $arr)

                               
                                    <?php $sub_qty = []; $sub_vat = []; $sub_net = []; $sub_total = []; ?>
                                @foreach($arr['products'] as $item)
                                    @php $total_stock_arr=[]; @endphp
                                    @php

                                        $itemstock=0;
                                        $itemstockQry="";
                                        if(isset($item->getstockmoves) && !empty($item->getstockmoves)){

                                            $itemstockQry=$item->getstockmoves->where('wa_inventory_item_id',$item->id);
                                            if ($branch) {
                                                $itemstockQry->where('restaurant_id', $branch);
                                            }
                                            $itemstock=$itemstockQry->sum('qauntity');
                                        }
                                    @endphp
                                    <tr style="text-align: right;">
                                        <td style="text-align: left;">{{ $item['description'] }}</td>
                                        <td style="text-align: center;">{{ $itemstock }}</td>
                                        @if(isset($selectedMonthArr['m']) && !is_null($selectedMonthArr['m']))
                                            @foreach($selectedMonthArr['m'] as $key => $month)

                                                @php
                                                    $year = $selectedMonthArr['y'][$key];
                                                    $created_from = date($year.'-'.$month.'-01');
                                                    $created_to = date($year.'-'.$month.'-t');

                                                    $created_to = date('Y-m-t', strtotime($created_from));

                                                    $monthlyStock = 0;

                                                    if ($itemstockQry != "") {
                                                        $monthlyStock = $item->getstockmoves
                                                            ->where('wa_inventory_item_id', $item->id)
                                                            ->whereRaw('(document_no LIKE "%INV%" OR document_no LIKE "%CS%" OR document_no LIKE "%RTN%") && DATE(created_at) >= "'.$created_from.'" && DATE(created_at) <= "'.$created_to.'"');
                                                            if (request()->filled('branch')) {
                                                                $monthlyStock->where('restaurant_id', request()->branch);
                                                            }
                                                            $monthlyStock = $monthlyStock->sum('qauntity');
                                                    }

                                                    $total_stock_arr[] = $monthlyStock;
                                                    $new_final_arr[] = $monthlyStock;
                                                    
                                                @endphp
                                                <td style="text-align: right;">{{abs($monthlyStock)}}</td>
                                                @php
                                                    $totalMonthlyStock[$key] +=  abs($monthlyStock);
                                                @endphp
                                            @endforeach
                                        @endif

                                        @php $final_arr[]=$total_stock_arr; @endphp


                                    </tr>
                                @endforeach

                            @endforeach
                        </table>

                    @endif