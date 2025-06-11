<div style="padding: 10px">
    <div class="row">
        <div class="col-md-9">
        </div>
        <div class="col-sm-3">
            @if (can('manage-promotions', 'maintain-items'))
                @if ($can_create)
                    <div align="right" class="form-group">
                        <a href="{!! route('promotions-bands.create', $itemId) !!}" class="btn btn-success">
                            <i class="fa fa-plus"></i>
                            Add Promotion</a>
                    </div>
                @endif
            @endif
        </div>
    </div>
    <table class="table table-bordered table-hover" id="create_datatable_25">
        <thead>
            <tr>
                <th width="3%">#</th>
                <th>Type</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Original Price</th>
                <th>Promotion Price</th>
                <th>Sale Quantity</th>
                <th>Promotion Item</th>
                <th>Promotion Item Quantity</th>
                <th>Created By</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($promotions as $promotion)
                <tr>
                    <th>{{ $loop->iteration }}</th>
                    <td>{{$promotion->promotionType->name}}</td>
                    <td>{{ \Carbon\Carbon::parse($promotion->from_date)->toDateString() ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($promotion->to_date)->toDateString() }}</td>
                    <td>{{ $promotion->status ?? '-' }}</td>
                    <td>{{ $promotion->current_price ?? '-' }}</td>
                    <td>{{ $promotion->promotion_price ?? '-' }}</td>
                    <td>{{ $promotion->sale_quantity }}</td>
                    <td>{{ $promotion->promotionItem?->stock_id_code . ' ' . $promotion->promotionItem?->title }}</td>
                    <td>{{ $promotion->promotion_quantity }}</td>
                    <td>{{ $promotion->initiatedBy?->name }}</td>
                    <td>
                        <div class="action-button-div">
                            @if (can('manage-promotions', 'maintain-items'))
                                <a href="{{ route('promotions-bands.edit', $promotion->id) }}">
                                    <i class="fas fa-pen" title="edit"></i></a>
                                @if ($promotion->status != 'blocked')
                                    <a href="{{ route('promotions-bands.block', $promotion->id) }}">
                                        <i class="fa fa-lock fa-lg" title="Block Promotion"></i></a>
                                @endif
                                @if ($promotion->status == 'blocked')
                                    <a href="{{ route('promotions-bands.unblock', $promotion->id) }}">
                                        <i class="fa fa-lock-open fa-lg" title="unblock  prommotion"></i></a>
                                @endif
                                <button type="button" class="text-primary mr-2 btn-decline2 transparent-btn"
                                    data-toggle="modal" title="Delete" data-target="#confirmationModal3"
                                    data-promotion-id="{{ $promotion->id }}">
                                    <i class="fas fa-trash-alt" style="color: red;"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{-- approve discounts modal --}}
<div class="modal fade" id="confirmationModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="staticBackdropLabel">Are you sure you want to block this promotion?</h4>

            </div>
            <form method="post" id="confirmationForm" action="">
                @csrf
                <input name="user_requested_access" type="hidden" id="user_requested_access"
                    value="{{ old('user_requested_access') }}" required />
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-submit-updated-center">Yes, Block
                        Promotion</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- Delete discounts --}}
<div class="modal fade" id="confirmationModal3" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="staticBackdropLabel">Are you sure you want to Delete this promotion?</h4>
            </div>
            <form method="POST" id="confirmationForm3">
                @csrf
                @method('DELETE')
                <input name="user_requested_access2" type="hidden" id="user_requested_access2"
                    value="{{ old('user_requested_access2') }}" required />
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-submit-updated-center2">Yes, Delete
                        Promotion</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            $('.btn-decline').click(function() {
                var promotionId = $(this).data('promotion-id');
                $('#confirmationModal').find('#promotion_id').val(promotionId);
                console.log(promotionId);
                $('#confirmationForm').attr('action',
                    '{{ route('promotions-bands.block', ['promotionId' => ':promotionId']) }}'.replace(
                        ':promotionId', promotionId));
                console.log("Form action:", $('#confirmationForm').attr(
                'action')); 
            });

            $('#confirmationModal').on('show.bs.modal', function(event) {
                var modal = $(this);
                modal.find('.btn-submit-updated-center').off('click').on('click', function() {
                    // Here you can submit the form
                    modal.find('form').submit();
                    // Close the modal
                    modal.modal('hide');
                });
            });

            $('.btn-decline2').click(function() {
                var promotionId = $(this).data('promotion-id');
                $('#confirmationModal3').find('#promotion_id').val(promotionId);
                $('#confirmationForm3').attr('action',
                    '{{ route('promotions-bands.delete', ['promotionId' => ':promotionId']) }}'
                    .replace(':promotionId', promotionId));
            });

            $('#confirmationModal3').on('show.bs.modal', function(event) {
                var modal = $(this);
                modal.find('.btn-submit-updated-center2').off('click').on('click', function() {
                    // Here you can submit the form
                    modal.find('form').submit();
                    // Close the modal
                    modal.modal('hide');
                });
            });
        });
    </script>
@endpush
