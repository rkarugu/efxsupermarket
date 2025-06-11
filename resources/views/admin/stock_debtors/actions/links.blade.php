{{-- @if (can('edit', 'customer-centre')) --}}
    {{-- <a href="{!! route('stock-debtors.debtor_trans', $customer->id) !!}" class="text-primary">
        <i class="fas fa-edit fa-lg"></i>
    </a> --}}
{{-- @endif --}}

<a href="{!! route('stock-debtors.view', $data->id) !!}" class="text-primary">
    <i class="fas fa-eye fa-lg"></i>
</a>