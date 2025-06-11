<div>
    <div class="col-md-12 no-padding-h table-responsive">
        <div class="text-right" style="margin-bottom: 2px;">
            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#splitRequestModal">Request Splits</button>
        </div>
        <table class="table table-bordered table-hover" id="create_datatable">
            <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Requested By</th>
                <th>Child Bin</th>
                <th>Child Code</th>
                <th>Child Title</th>
                <th>Mother Bin</th>
                <th>Mother Code</th>
                <th>Mother Title</th>
                <th>Request Quantity</th>
                <th>Mother QOH</th>
            </tr>
            </thead>
            <tbody>
                @foreach ($requests as $request)
                    <tr>
                        <th>{{$loop->index+1}}</th>
                        <td>{{\Carbon\Carbon::parse($request->created_at)->toDateString()}}</td>
                        <td>{{$request->getInitiatingUser?->name}}</td>
                        <td>{{$request->getChildBinDetail->title}}</td>
                        <td>{{$request->getChild->stock_id_code}}</td>
                        <td>{{$request->getChild->title}}</td>
                        <td>{{$request->getMotherBinDetail->title}}</td>
                        <td>{{$request->getMother->stock_id_code}}</td>
                        <td>{{$request->getMother->title}}</td>
                        <td style="text-align: center;">{{$request->requested_quantity}}</td>
                        <td style="text-align: center;">{{$request->mother_qoh}}</td>
                    

                    </tr>
                    
                @endforeach
            </tbody>
        
            
        </table>
       
    </div>
  <!-- Modal for Submitting Split requests -->
<div class="modal fade" id="splitRequestModal" tabindex="-1" role="dialog" aria-labelledby="splitRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h4 class="modal-title" id="splitRequestModalLabel">Request Split</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="splitRequestForm">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Mother Quantity</th>
                                <th>Child Quantity</th>
                                <th>Action</th>
                                
                            </tr>
                        </thead>
                        <tbody id="splitRequestsTableBody">
                            <tr>
                                <td>
                                    <select name="item_name[]" class="form-control item-select-split-requests" required>
                                        <option value="">Search and select an item</option>
                                        @foreach($inventoryItems as $item)
                                            <option value="{{ $item->id }}"  data-conversion-factor="{{ $item->conversion_factor }}">{{ $item->title }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" name="quantity[]" class="form-control quantity-field" required></td>
                                <td><input type="text" name="destinated_quantity[]" class="form-control destinated-quantity-field" required></td>

                                <td><button type="button" class="btn btn-success btn-sm removeItemBtn"><i  class="fas fa-trash"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="form-group d-flex justify-content-between">
                        <button type="button" class="btn btn-success btn-sm" id="addItemBtnSplitRequets"><i class="fas fa-plus"></i> Add Item</button>
                        <input type="submit" class="btn btn-success btn-sm" value="Submit Request">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}

</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            $('.item-select-split-requests').select2({
                placeholder: "Search and select an item",
                allowClear: true,
                dropdownParent: $('#splitRequestModal')
            });

            $('#addItemBtnSplitRequets').on('click', function() {
                var newRow = `
                 <tr>
                     <td>
                         <select name="item_name[]" class="form-control item-select-split-requests" required>
                             <option value="">Search and select an item</option>
                             @foreach($inventoryItems as $item)
                <option value="{{ $item->id }}"  data-conversion-factor="{{ $item->conversion_factor }}">{{ $item->title }}</option>
                             @endforeach
                </select>
            </td>
            <td><input type="number" name="quantity[]" class="form-control quantity-field" required></td>
            <td><input type="text" name="destinated_quantity[]" class="form-control destinated-quantity-field" required></td>
            <td><button type="button" class="btn btn-success btn-sm removeItemBtn"><i  class="fas fa-trash"></i></button></td>
        </tr>`;

                $('#splitRequestsTableBody').append(newRow);

                $('.item-select-split-requests').last().select2({
                    placeholder: "Search and select an item",
                    allowClear: true,
                    dropdownParent: $('#splitRequestModal')

                });
            });
            $(document).on('click', '.removeItemBtn', function() {
                $(this).closest('tr').remove();
            });
            // Handle quantity input change and calculate destinated quantity
            $(document).on('input', '.quantity-field', function() {
                var $row = $(this).closest('tr');
                var quantity = $(this).val();
                var conversionFactor = $row.find('select option:selected').data('conversion-factor');

                if (quantity && conversionFactor) {
                    var destinatedQuantity = quantity * conversionFactor;
                    $row.find('.destinated-quantity-field').val(destinatedQuantity);
                }
            });

            // Handle item change to recalculate the destinated quantity when the item is changed
            $(document).on('change', '.item-select-split-requests', function() {
                var $row = $(this).closest('tr');
                var quantity = $row.find('.quantity-field').val();
                var conversionFactor = $(this).find('option:selected').data('conversion-factor');

                if (quantity && conversionFactor) {
                    var destinatedQuantity = quantity * conversionFactor;
                    $row.find('.destinated-quantity-field').val(destinatedQuantity);
                }
            });

            $('#splitRequestForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('request-splits-from-web') }}",
                    method: "POST",
                    data: $(this).serialize() + '&_token=' + "{{ csrf_token() }}",
                    success: function(response) {
                        if(response.success) {
                            // alert(response.message);
                            VForm.successMessage(response.message);

                            $('#splitRequestModal').modal('hide');
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        var errorMessage = 'Something went wrong. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        VForm.errorMessage(errorMessage);
                                }
                });
            });
        });




    </script>

@endpush
