<div style="padding: 10px">
    <div class="row">
        <div class="col-sm-9">
        </div>
        <div class="col-sm-3">
            @if (can('manage-discount', 'maintain-items'))
                <div align="right" class="form-group">
                    <a href="{{ route('discount-bands.create', $itemId) }}" class="btn btn-success">
                        <i class="fa fa-plus"></i>
                        Add Discount Band</a>
                </div>
            @endif
        </div>
    </div>
    <table class="table table-bordered table-hover" id="create_datatable_10">
        <thead>
            <tr>
                <th width="3%">#</th>
                <th width="10%">From Quantity</th>
                <th width="10%">To Quantity</th>
                <th width="10%">Discount Amount</th>
                <th width="20%">Initiated By</th>
                <th width="10%">Status</th>
                <th width="10%">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($discountBands as $discountBand)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $discountBand->from_quantity }}</td>
                    <td>{{ $discountBand->to_quantity ?? '-' }}</td>
                    <td>{{ $discountBand->discount_amount }}</td>
                    <td>{{ getUserData($discountBand->initiated_by)?->name }}</td>
                    <td>{{ $discountBand->status }}</td>
                    <td>
                        <div class="action-button-div">
                            @if (can('manage-discount', 'maintain-items'))
                                <a href="{{ route('discount-bands.edit', $discountBand->id) }}">
                                    <i class="fas fa-pen" title="edit"></i></a>
                            @endif
                            @if (can('approve-discount', 'maintain-items'))
                                @if ($discountBand->status != 'APPROVED')
                                    <button type="button" class="text-primary mr-2 btn-decline transparent-btn"
                                        data-toggle="modal" title="Approve" data-target="#confirmationModal"
                                        data-discount-band-id="{{ $discountBand->id }}">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                                <button type="button" class="text-primary mr-2 btn-decline2 transparent-btn"
                                    data-toggle="modal" title="Delete" data-target="#confirmationModal2"
                                    data-discount-band-id="{{ $discountBand->id }}">
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
                <h4 class="modal-title" id="staticBackdropLabel">Are you sure you want to approve this discount?</h4>

            </div>
            <form method="POST" id="confirmationForm" action="">
                @csrf

                <input name="user_requested_access" type="hidden" id="user_requested_access"
                    value="{{ old('user_requested_access') }}" required />

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-submit-updated-center">Yes, Approve
                        Discount</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- Delete discounts --}}
<div class="modal fade" id="confirmationModal2" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="staticBackdropLabel">Are you sure you want to Delete this discount band?
                </h4>

            </div>
            <form method="POST" id="confirmationForm2" action="">
                @csrf
                @method('DELETE')

                <input name="user_requested_access2" type="hidden" id="user_requested_access2"
                    value="{{ old('user_requested_access2') }}" required />

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-submit-updated-center2">Yes, Delete
                        Discount</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            $('.btn-decline').click(function() {
                var discountBandId = $(this).data('discount-band-id');
                $('#confirmationModal').find('#discount_band_id').val(discountBandId);
                $('#confirmationForm').attr('action',
                    '{{ route('discount-bands.approve', ['discountBandId' => ':discountId']) }}'
                    .replace(':discountId', discountBandId));
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
                var discountBandId = $(this).data('discount-band-id');
                $('#confirmationModal2').find('#discount_band_id').val(discountBandId);
                $('#confirmationForm2').attr('action',
                    '{{ route('discount-bands.delete', ['discountBandId' => ':discountId']) }}'
                    .replace(':discountId', discountBandId));
            });

            $('#confirmationModal2').on('show.bs.modal', function(event) {
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
