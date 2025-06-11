@if ($order->is_hide == 'No')
    @if (can('hide', $model) && $order->status !== 'COMPLETED')
        <span>
            <a title="Archive" href="{{ route($model . '.hidepurchaseorder', $order->slug) }}">
                <i class="fa fa-eye-slash" style="color: red;" aria-hidden="true"></i>
            </a>
        </span>
    @endif
    @if (can('edit', $model))
        @if ($order->status == 'APPROVED')
            <!-- Disabled edit button for approved LPOs -->
            <span>
                <i class="fa fa-edit" style="color: #ccc; cursor: not-allowed;" aria-hidden="true" title="Cannot edit approved LPO"></i>
            </span>
        @else
            @if ($order->type == 'stock')
                @if (!$order->supplier_accepted || auth()->user()->isAdministrator())
                    <span>
                        <a title="Edit" href="{{ route($model . '.edit', $order->slug) }}">
                            <i class="fa fa-edit" aria-hidden="true"></i>
                        </a>
                    </span>
                @endif
            @else
                <span>
                    <a title="Edit" href="{{ route('non-stock-purchase-orders.edit', $order->slug) }}">
                        <i class="fa fa-edit" aria-hidden="true"></i>
                    </a>
                </span>
            @endif
        @endif
    @endif
    @if (
        (can('delete', $model) && ($order->status == 'DRAFT' || $order->status == 'PRELPO')) ||
            auth()->user()->isAdministrator())
        <span>
            <form title="Trash" style="display: inline-block"
                action="{{ URL::route($model . '.destroy', $order->slug) }}" method="POST">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button style="background: none; border:none;" class="text-danger">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </button>
            </form>
        </span>
    @endif
    @if ($order->status == 'APPROVED' || $order->status == 'COMPLETED')
        <!-- Always show PDF download and print buttons for approved and completed LPOs -->
        <span>
            <a title="Print LPO" href="{{ url('admin/purchase-orders/print?slug=' . $order->slug) }}" target="_blank">
                <i aria-hidden="true" class="fa fa-print"></i>
            </a>
        </span>
        <span>
            <a title="Export To PDF" href="{{ url('admin/purchase-orders/exportToPdf/' . $order->slug) }}" target="_blank">
                <i aria-hidden="true" class="fa fa-file-pdf-o"></i>
            </a>
        </span>
    @elseif ($order->status != 'DRAFT' && $order->status != 'PRELPO')
        @if (can('export-pdf', $model) && $order->supplier_accepted)
            <span>
                <a title="Export To Pdf" href="{{ route($model . '.exportToPdf', $order->slug) }}" target="_blank">
                    <i aria-hidden="true" class="fa fa-file-pdf-o"></i>
                </a>
            </span>
        @endif
    @endif
@else
    @if ($order->status == 'APPROVED' && $order->supplier_accepted)
        <span>
            <a title="Export To Pdf" style="margin: 0 5px;" href="{{ route($model . '.exportToPdf', $order->slug) }}"><i
                    aria-hidden="true" class="fa fa-file-pdf" style="font-size: 20px;"></i>
            </a>
        </span>
        <span>
            <a href="{{ route($model . '.unarchive_lpo', $order->slug) }}" class=" delete-confirm" title="unarchive"><i
                    class="fa fa-exchange" aria-hidden="true"></i></i>
            </a>
        </span>
    @else
        <span>
            <a href="{{ route($model . '.unarchive_lpo', $order->slug) }}" class=" delete-confirm" title="unarchive"><i
                    class="fa fa-exchange" aria-hidden="true"></i></i>
            </a>
        </span>
    @endif
@endif
