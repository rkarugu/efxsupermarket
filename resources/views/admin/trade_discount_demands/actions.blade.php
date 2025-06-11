@if (can('view', 'trade-discount-demands'))
    <a href="{{ route('trade-discount-demands.show', $demand) }}" target="_blank" title="View Details"
        data-toggle="tooltip">
        <i class="fa fa-eye"></i>
    </a>
@endif
@if (can('delete', 'trade-discount-demands') && is_null($demand->credit_note_no))
    <a href="#" data-toggle="demands" data-target="#delete{{ $demand->id }}" style="margin-left: 10px">
        <span class="span-action" data-toggle="tooltip" title="Delete Demand">
            <i class="fa fa-trash"></i>
        </span>
    </a>
    <form id="delete{{ $demand->id }}" action="{{ route('trade-discount-demands.destroy', $demand) }}"
        style="display: none" method="post">
        @csrf()
        @method('DELETE')
    </form>
@endif
@if (can('convert', 'trade-discount-demands') && is_null($demand->credit_note_no))
    <a href="{{ route('trade-discount-demands.edit', $demand) }}" target="_blank" title="Convert Demand"
        data-toggle="tooltip" style="margin-left: 10px">
        <i class="fa fa-arrow-right fa-lg"></i>
    </a>
@endif
@if (auth()->user()->isAdministrator() && !is_null($demand->credit_note_no))
    <a href="{{ route('trade-discount-demands.edit', $demand) }}" target="_blank" title="Edit Demand"
        data-toggle="tooltip" style="margin-left: 10px">
        <i class="fa fa-edit"></i>
    </a>
@endif
