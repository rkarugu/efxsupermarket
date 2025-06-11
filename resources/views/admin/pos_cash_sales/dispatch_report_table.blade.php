<table class="table table-bordered table-hover" style="text-align: left !important;">
    <style>
        .bg-grey td{
            background: #e2e2e2;
        }
    </style>
    @php
        $totalQty = 0;
    @endphp
    @if ($request->type == 'Cash Sales')
    <thead>

        <tr class="heading">
            <th style="text-align: left !important;width: 7%;">DocNo</th>
            <th style="text-align: left !important;width: 7%;">Date</th>
            <th style="text-align: left !important;width: 10%;">Cashier</th>
            <th style="text-align: left !important;width: 25%;">Item</th>
            <th style="text-align: left !important;width: 7%;">Qty</th>
            <th style="text-align: left !important;width: 8%;">Price</th>
            <th style="text-align: left !important;width: 8%;">Amount</th>
            <th style="text-align: left !important;width: 8%;">Status</th>
            <th style="text-align: left !important;width: 10%;">DispatchAt</th>
            <th style="text-align: left !important;width: 10%;" style="text-align: right">User</th>
        </tr>
    </thead>
        <tbody>
                @php
                    $i = 0;
                @endphp
            @foreach ($data as $data_item)
                <tr class="item {{($i%2 == 0) ? 'bg-grey' : NULL}}" style="text-align: left !important;">
                    <td >{{@$data_item->sales_no}}</td>
                    <td>{{date('d/M/y',strtotime($data_item->date))}}</td>
                    <td>{{@$data_item->user->name}}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @foreach ($data_item->items as $item)
                <tr class="item {{($i%2 == 0) ? 'bg-grey' : NULL}}" style="text-align: left !important;">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{@$item->item->title}}</td>
                    @php
                        $totalQty += $item->qty;
                    @endphp
                    <td>{{manageAmountFormat($item->qty)}}</td>
                    <td>{{manageAmountFormat($item->selling_price)}}</td>
                    <td>{{manageAmountFormat($item->selling_price*$item->qty)}}</td>

                    <td>
                        @if ($item->is_dispatched == 1)
                            Dispatched
                        @elseif($item->is_dispatched == 0 && $item->dispatch_details->count() == 0)
                            Not Yet
                        @elseif($item->is_dispatched == 0 && $item->dispatch_details->count() != 0)
                            Partially
                        @endif
                    </td>
                    @php
                        $latestDispatch = $item->dispatch_details->first();
                    @endphp
                    <td>{{isset($latestDispatch->dispatched_time) ? date('d/M/y H A',strtotime($latestDispatch->dispatched_time)) : NULL}}</td>
                    <td>{{@$latestDispatch->dispatch_user->name}}</td>
                </tr>
                @endforeach                
                @php
                    $i++;
                @endphp
            @endforeach    
        
            <tr class="heading">
                <th colspan="4" style="text-align: right">Total QTY</th>
                <th colspan="6" style="text-align: left">{{manageAmountFormat($totalQty)}}</th>
            </tr>
        </tbody>
    @else
    <thead>
        <tr class="heading">
            <th style="text-align: left !important;width: 7%;">DocNo</th>
            <th style="text-align: left !important;width: 7%;">Date</th>
            <th style="text-align: left !important;width: 10%;">Cashier</th>
            <th style="text-align: left !important;width: 25%;">Item</th>
            <th style="text-align: left !important;width: 7%;">Qty</th>
            <th style="text-align: left !important;width: 8%;">Price</th>
            <th style="text-align: left !important;width: 8%;">Amount</th>
            <th style="text-align: left !important;width: 8%;">Status</th>
            <th style="text-align: left !important;width: 10%;">DispatchAt</th>
            <th style="text-align: left !important;width: 10%;" style="text-align: right">User</th>
        </tr>

    </thead>
        <tbody>
                @php
                    $i = 0;
                @endphp
            @foreach ($data as $data_item)
                <tr class="item {{($i%2 == 0) ? 'bg-grey' : NULL}}" style="text-align: left !important;">
                    <td>{{@$data_item->requisition_no}}</td>
                    <td>{{date('d/M/y',strtotime($data_item->requisition_date))}}</td>
                    <td>{{@$data_item->getrelatedEmployee->name}}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @foreach ($data_item->getRelatedItem as $item)
                <tr class="item {{($i%2 == 0) ? 'bg-grey' : NULL}}" style="text-align: left !important;">
                    <td></td>
                    <td></td>
                    <td></td>
                    @php
                        $totalQty += $item->quantity;
                    @endphp
                    <td>{{@$item->getInventoryItemDetail->title}}</td>
                    <td>{{manageAmountFormat($item->quantity)}}</td>
                    <td>{{manageAmountFormat($item->selling_price)}}</td>
                    <td>{{manageAmountFormat($item->selling_price*$item->quantity)}}</td>
                    <td>
                        @if ($item->is_dispatched == 1)
                            Dispatched
                        @elseif($item->is_dispatched == 0 && $item->dispatch_details->count() == 0)
                            Not Yet
                        @elseif($item->is_dispatched == 0 && $item->dispatch_details->count() != 0)
                            Partially
                        @endif
                    </td>
                    @php
                        $latestDispatch = $item->dispatch_details->first();
                    @endphp
                    <td>{{isset($latestDispatch->dispatched_time) ? date('d/M/y H A',strtotime($latestDispatch->dispatched_time)) : NULL}}</td>
                    <td>{{@$latestDispatch->dispatch_user->name}}</td>
                </tr>
                @endforeach                
                @php
                    $i++;
                @endphp
            @endforeach    
       
            <tr class="heading">
                <th colspan="4" style="text-align: right">Total QTY</th>
                <th colspan="6" style="text-align: left">{{manageAmountFormat($totalQty)}}</th>
            </tr>
        </tbody>
    @endif
</table>