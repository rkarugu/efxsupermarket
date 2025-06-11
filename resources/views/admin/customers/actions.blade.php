@if (can('edit', 'maintain-customers'))
    <span>
        <a title="Edit" href="{{ route('maintain-customers.edit', $customer->slug) }}" data-toggle="tooltip">
            <img src="{{ asset('assets/admin/images/edit.png') }}" alt="">
        </a>
    </span>
@endif
@if (can('view', 'customer-centre'))
    <span style="margin-left: 5px">
        <a title="Customer Centre" href="{{ route('customer-centre.show', $customer) }}" data-toggle="tooltip">
            <i class="fa fa-store"></i>
        </a>
    </span>
@endif
@if (can('enter-customer-payment', 'maintain-customers'))
    <span>
        <a title="enter customer payment" href="{{ route('maintain-customers.enter-customer-payment', $customer->slug) }}" data-toggle="tooltip" style="margin-left: 5px;">
             <i class="fa fa-money-bill"></i>
        </a>
    </span>
@endif

@if (can('settle-from-fraud', 'maintain-customers'))
    <span>
        <a title="Settle From Fraud" href="{{ route('maintain-customers.settle-from-fraud', $customer->slug) }}" data-toggle="tooltip" style="margin-left: 5px;">
             <i class="fas fa-handshake-angle"></i>
        </a>
    </span>
@endif