@if (is_null($note->suppreference))
    @if (can('edit', 'credit-debit-notes'))
        <a href="#" data-toggle="modal" data-target="#assignmentModal" data-note="{{ $note->id }}"
            data-supplier="{{ $note->supplier_id }}" title="Allocate">
            <i class="fa fa-hand-point-right"></i>
        </a>
        <a href="{{ route('credit-debit-notes.edit', $note) }}" data-toggle="tooltip" title="Edit Note"
            style="margin-left: 5px">
            <i class="fa fa-edit"></i>
        </a>
    @endif
    @if (can('delete', 'credit-debit-notes'))
        <a href="#" data-toggle="notes" data-target="#delete{{ $note->id }}" data-action="delete"
            data-note="{{ $note->type }}">
            <span class="span-action" data-toggle="tooltip" title="Delete Note" style="margin-left: 5px">
                <i class="fa fa-trash"></i>
            </span>
        </a>
        <form id="delete{{ $note->id }}" action="{{ route('credit-debit-notes.destroy', $note->id) }}"
            style="display: none" method="post">
            @method('DELETE')
            @csrf()
        </form>
    @endif
@elseif(is_null($note->payment_voucher_id))
    @if (can('edit', 'credit-debit-notes'))
        <a href="#" data-toggle="deallocate" data-target="#deallocateFrm{{ $note->id }}" title="Deallocate"
            class="d-block text-danger">
            <i class="fa fa-ban"></i>
        </a>
        <form action="{{ route('credit-debit-notes.deallocate', $note) }}" id="deallocateFrm{{ $note->id }}"
            style="display: none" method="post">
            @csrf
            @method('put')
        </form>
        <a href="{{ route('credit-debit-notes.edit', $note) }}" data-toggle="tooltip" title="Edit Note"
            style="margin-left: 5px">
            <i class="fa fa-edit"></i>
        </a>
    @endif
@elseif($note->payment_voucher_id && auth()->user()->isAdministrator())
    <a href="{{ route('credit-debit-notes.edit', $note) }}" data-toggle="tooltip" title="Edit Note"
        style="margin-left: 5px">
        <i class="fa fa-edit"></i>
    </a>
@endif
