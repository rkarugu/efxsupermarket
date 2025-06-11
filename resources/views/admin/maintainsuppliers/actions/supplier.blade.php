@if ($supplier->is_verified)
    @if (can('edit', 'maintain-suppliers'))
        <span class='span-action'> <a title='Edit'
                href="{{ route('maintain-suppliers.edit', $supplier->supplier_code) }}">
                <img src="{{ asset('assets/admin/images/edit.png') }}"></a></span>
    @endif
    @if (can('vendor-centre', 'maintain-suppliers'))
        <span class='span-action'>
            <a title="Vendor centre" href="{{ route('maintain-suppliers.vendor_centre', $supplier->supplier_code) }}"><i
                    class="fa fa-store"></i></a>
        </span>
    @endif    
    @if (can('delete', 'maintain-suppliers') && $supplier->canBeDeleted())
        <x-actions.delete-record identifier="supp{{ $supplier->id }}" action="{{ route('maintain-suppliers.destroy', $supplier->id) }}" />
    @endif
@else
    @if (can('show', 'maintain-suppliers'))
        <span class='span-action'>
            <a title='Show' href="{{ route('maintain-suppliers.show', $supplier) }}">
                <i class='fa fa-eye'></i></a>
        </span>
    @endif
    <span class='span-action'>
        <a data-toggle="modal" href="#modal-id{{ $supplier->id }}">
            <i class="fa fa-check-circle text-success fa-lg" aria-hidden="true"></i>
        </a>
    </span>
    <div class="modal fade" id="modal-id{{ $supplier->id }}">
        <div class="modal-dialog">
            <form class="submitMe"
                action="{{ route('maintain-suppliers.supplier_unverified_process', ['id' => $supplier->id]) }}"
                method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Verify Supplier : {{ $supplier->supplier_code }} -
                            {{ $supplier->name }}
                        </h4>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to verify supplier account : {{ $supplier->supplier_code }} -
                        {{ $supplier->name }}?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Verify</button>
                    </div>
                </div>
        </div>
    </div>
@endif
