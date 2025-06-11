<a href='{!! route('route-customers.show-custom', [$customer->id, 'route-customers-onboarding-process']) !!}' title='View Route Customer'>
    <i class='fa fa-eye text-info fa-lg'></i>
</a>
<button title='Verify Route Customer' data-toggle='modal'
    data-target='#confirm-verify-shop-modal' data-backdrop='static'
    data-id='{{ $customer->id }}' style="border: none; background:transparent;">
    <i class='fa fa-check-circle text-success fa-lg'></i>
    <form action=" {!! route('route-customers.verify', $customer->id) !!} " method='post'
        id='verify-shop-form-{{ $customer->id }}'>
        {{ csrf_field() }}

        <input type='hidden' id='source-{{ $customer->id }}' name='source'>
    </form>
</button>
<button title='Reject Route Customer' data-toggle='modal'
    data-target='#reject-shop-modal' data-backdrop='static'
    data-id='{{ $customer->id }}' style="border: none; background:transparent;">
    <i class='fa fa-trash text-danger fa-lg'></i>
    <form action=" {!! route('route-customers.verification-reject', $customer->id) !!} " method='post'
        id='reject-shop-form-{{ $customer->id }}'>
        {{ csrf_field() }}

        <input type='hidden' id='source-{{ $customer->id }}' name='source'>
    </form>
</button>
