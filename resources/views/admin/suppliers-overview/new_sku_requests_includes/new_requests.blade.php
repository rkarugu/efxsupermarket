<div style="padding:10px;">
    <div class="row" style="margin-bottom: 10px">
        <div class="col-sm-4">
            <div class="row">
                @if (can('approve', $pmodule))
                    <div class="col-sm-2">
                        <button id="approve-btn" class="btn btn-success" style="display: none;">Approve</button>
                    </div>
                @endif
                @if (can('reject', $pmodule))
                    <div class="col-sm-2">
                        <button id="reject-btn" class="btn btn-danger" style="display: none;">Reject</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <hr>
    <table class="table table-bordered table-hover table-striped" id="new_requests_table">
        <thead>
            <tr>
                <th><input type="checkbox" id="select-all"></th>
                <th>Date</th>
                <th>Initiated By</th>
                <th>SKU Code</th>
                <th>SKU Name</th>
                <th>Category</th>
                <th>Sub Category</th>
                <th>Pack Size</th>
                <th>Price List Cost</th>
                <th>Gross Weight</th>
                <th>Images</th>
                <th>Discounts</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sku_requests->filter(fn($sku_request) => $sku_request->status === 'Pending') as $sku_request)
                <tr>
                    <td><input type="checkbox" class="select-item" value="{{ $sku_request->id }}"></td>
                    <td>{{ $sku_request->created_at }}</td>
                    <td>{{ $sku_request->supplier?->name }}</td>
                    <td>{{ $sku_request->supplier_sku_code }}</td>
                    <td>{{ $sku_request->supplier_sku_name }}</td>
                    <td>
                        @php
                            $categories = json_decode($sku_request->subcategory, true);
                            $category_description =
                                is_array($categories) && isset($categories['description'])
                                    ? $categories['description']
                                    : 'N/A';
                        @endphp
                        {{ $category_description }}
                    </td>
                    <td>{{ $sku_request->subcategory->title }}</td>
                    <td>{{ $sku_request->packsize->title }}</td>
                    <td>{{ $sku_request->price_list_cost }}</td>
                    <td>{{ $sku_request->gross_weight }}</td>
                    <td><a href="#" class="view-images"
                            data-images="{{ json_encode($sku_request->requestnewskuimages) }}">View</a>
                    </td>
                    <td>
                        {{ implode(', ', json_decode($sku_request->trade_agreement_discount, true) ?? []) }}
                    </td>
                    <td>{{ $sku_request->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">View Images</h5>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row" id="modal-images-container">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default">Cancel</button>
                </div>
            </div>
        </div>
    </div>


</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            var formMessage = new Form()
            let selectedItemIds = [];

            $('#select-all').on('click', function() {
                const isChecked = this.checked;
                $('.select-item').prop('checked', isChecked);

                $('.select-item').each(function() {
                    const itemId = $(this).val();
                    if (isChecked) {
                        if (!selectedItemIds.includes(itemId)) {
                            selectedItemIds.push(itemId);
                        }
                    } else {
                        selectedItemIds = selectedItemIds.filter(id => id !== itemId);
                    }
                });
                toggleActionButtons();
            });

            $('.select-item').on('change', function() {
                const itemId = $(this).val();
                if (this.checked) {
                    if (!selectedItemIds.includes(itemId)) {
                        selectedItemIds.push(itemId);
                    }
                } else {
                    selectedItemIds = selectedItemIds.filter(id => id !== itemId);
                }
                toggleActionButtons();
            });

            function toggleActionButtons() {
                if (selectedItemIds.length > 0) {
                    $('#approve-btn').show();
                    $('#reject-btn').show();
                } else {
                    $('#approve-btn').hide();
                    $('#reject-btn').hide();
                }
            }

            $('#approve-btn').on('click', function() {
                if (selectedItemIds.length > 0) {
                    $.ajax({
                        url: '{{ route('request-new-sku.approve') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ids: selectedItemIds
                        },
                        success: function(response) {
                            formMessage.successMessage('SKU approved successfully')
                            location.reload();
                        },
                        error: function(response) {
                            formMessage.errorMessage('Something went wrong')
                        }
                    });
                }
            });

            $('#reject-btn').on('click', function() {
                if (selectedItemIds.length > 0) {
                    $.ajax({
                        url: '{{ route('request-new-sku.reject') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ids: selectedItemIds
                        },
                        success: function(response) {
                            formMessage.successMessage('SKU rejected successfully')
                            location.reload();
                        },
                        error: function(response) {
                            formMessage.errorMessage('Something went wrong')
                        }
                    });
                }
            });

            $(document).on('click', '.view-images', function(e) {
                e.preventDefault();

                let images = $(this).data('images');
                let modalBody = $('#modal-images-container');
                modalBody.empty();

                if (images && images.length > 0) {
                    images.forEach(function(image) {
                        let imagePath = image.file_path.trim().replace(/^\/|\/$/g, '');
                        let imageUrl = `{{ asset('') }}${imagePath}`;
                        console.log(imageUrl);
                        modalBody.append(`
                <div class="col-md-3 mb-3">
                    <img src="${imageUrl}" class="img-fluid" style="max-width: 100%; height: auto;"/>
                </div>
            `);
                    });
                } else {
                    modalBody.append('<p>No images available.</p>');
                }

                $('#imageModal').modal('show');
            });

            $('#imageModal .btn').on('click', function() {
                $('#imageModal').modal('hide');
                $('#modal-images-container').empty();
            });

        });
    </script>
@endpush
