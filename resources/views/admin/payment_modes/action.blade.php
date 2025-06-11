<a title="Edit" href="#" data-toggle="modal" data-target="#editPaymentModeModal"
    data-url="{{ route('payment-modes.update', $paymentMode) }}"
    data-details="{{ json_encode($paymentMode->only('mode', 'description')) }}">
    <i class="fa fa-edit"></i>
</a>

<a title="Delete" href="#" data-toggle="delete"
    data-target="#deleteForm{{ $paymentMode->id }}">
    <i class="fa fa-trash"></i>
</a>

<form id="deleteForm{{ $paymentMode->id }}" action="{{ route('payment-modes.destroy', $paymentMode) }}"
    style="display: none" method="post">
    @csrf()
    @method('delete')
</form>
