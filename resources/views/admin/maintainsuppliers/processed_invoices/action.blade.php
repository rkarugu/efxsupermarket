<a target="_blank" href="{{ route('maintain-suppliers.processed_invoices.show', $invoice) }}">
    <i class="fa fa-file-alt"></i>
</a>

@if (!$invoice->suppTrans->payments()->exists() && can('reverse', 'suppliers-invoice'))
    <a href="#" data-toggle="reverse" data-target="#reverse{{ $invoice->id }}"
        style="margin-left:5px">
        <i class="fa fa-undo"></i>
    </a>
    <form style="display: none" action="{{ route('maintain-suppliers.processed_invoices.reverse', $invoice) }}" id="reverse{{ $invoice->id }}"
        method="post">
        @csrf();
    </form>
@endif
