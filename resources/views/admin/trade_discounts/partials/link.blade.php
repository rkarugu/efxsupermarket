@if (can('view', 'trade-discounts'))
    <a href="{{ route('trade-discounts.show', $discount->id) }}" target="_blank" title="View Details" data-toggle="tooltip">
        <i class="fa fa-eye"></i>
    </a>
@endif