@if (can('edit', 'customer-centre'))
    <a href="{!! route('maintain-customers.route_customer_edit', $customer->id) !!}" class="text-primary">
        <i class="fas fa-edit fa-lg"></i>
    </a>
@endif
@if (can('delete', 'customer-centre'))
    <a href="javascript:void(0);" data-toggle="modal" data-target="#delete-customer-modal" data-backdrop="static"
        data-id="{{ $customer->id }}" data-name="{{ $customer->bussiness_name }}">
        <i class="fas fa-user-times text-danger fa-lg"></i>
    </a>
@endif
