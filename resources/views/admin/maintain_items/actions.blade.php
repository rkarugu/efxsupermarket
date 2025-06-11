@if (can('edit', 'maintain-items'))
    <span class="span-action">
        <a title="Edit" href="{{ route('maintain-items.edit', $item->slug) }}"><img src="{{ asset('assets/admin/images/edit.png') }}"></a>
    </span>
@endif
@if (can('view', 'maintain-items'))
    <span class="span-action">
        <a title="Item Centre" href="{{ route('item-centre.show', $item) }}">
            <i class="fa fa-store"></i>
        </a>
    </span>
@endif
<span class="span-action"><a href="{{ route('maintain-items.show', $item->slug) }}" title="View">
    <i class="fas fa-eye" aria-hidden="true"></i></a>
</span>
@if ($item->competingBrand)
<span class="span-action">
    <a title="Competing Brands" onclick="fetchCompetingBrands({{ $item->id }})" data-toggle="modal" data-target="#competingBrandsModal">
        <i class="fas fa-fire"></i>
    </a>
</span>
    
@endif
@if (can('clone', 'maintain-items'))
    <span class="span-action">
        <a title="Clone Item" href="javascript:void(0)" onclick="showCloneModal({{ $item->id }})">
            <i class="fas fa-clone"></i>
        </a>
    </span>
@endif
<!-- Competing Brands Modal -->
<div class="modal fade" id="competingBrandsModal" tabindex="-1" aria-labelledby="competingBrandsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="competingBrandsModalLabel">Competing Brands for - <span id="itemName"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Stock Id Code </th>
                            <th>Title</th>
                            <th>Standard Cost</th>
                            <th>Selling Price</th>
                            <th>Qoh</th>
                        </tr>
                    </thead>
                    <tbody id="competingBrandsTableBody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Clone Item Modal -->
<div class="modal fade" id="cloneItemModal" tabindex="-1" role="dialog" aria-labelledby="cloneItemModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="box box-primary">
            <div class="box-header with-border ">
                <h4 class="modal-title pull-left" id="cloneItemModalLabel">Clone Item</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="cloneItemForm" method="POST" action="{{ route('maintain-items.clone') }}">
                    @csrf
                    <input type="hidden" name="item_id" id="cloneItemId">
                    <div class="form-group" style="text-align: left; width:100%;">
                        <label for="newStockIdCode">New Stock ID Code:</label>
                        <br>
                        <input type="text" name="new_stock_id_code" id="newStockIdCode" class="form-control" style="width: 100%" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="pull-left">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
                <div class="pull-right">
                    <button type="submit" form="cloneItemForm" class="btn btn-success btn-sm"><i class="fas fa-paper-plane"></i> Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

  
  
  

{{-- @if (can('route-pricing', 'maintain-items'))
    <span class="span-action">
        <a href="{{route('route.pricing.listing',  $item)}}" title="Route Pricing">
        <i class="fas fa-route" aria-hidden="true"></i></a>
    </span>
@endif --}}
{{-- @if (can('delete', 'maintain-items') && !$item->stock_moves_count)
    <span class='span-action'>
        <a title="Delete" href="#" data-toggle="delete" data-target="#frm{{ $item->id }}">
            <i class="fa fa-trash" style='font-size: 16px;'></i>
        </a>
    </span>
    <form id="frm{{ $item->id }}" action="{{ route('maintain-items.destroy', $item) }}" method="POST"
        style="display: none">
        @csrf
        @method('DELETE')
    </form>
@endif --}}
