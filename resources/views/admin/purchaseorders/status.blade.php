@if ($order->status == 'COMPLETED')
    <span class="label bg-purple">COMPLETED</span>
@elseif ($order->status == 'APPROVED')
    <span class="label bg-green">APPROVED</span>
@elseif ($order->goods_released && $order->slot_booked && $order->supplier_accepted)
    <span class="label bg-green">Released</span>
@elseif($order->slot_booked && $order->supplier_accepted)
    <span class="label bg-teal">Scheduled</span>
@elseif($order->supplier_accepted)
    <span class="label bg-aqua">Accepted</span>
@else
    <span class="label bg-yellow">Pending</span>
@endif
