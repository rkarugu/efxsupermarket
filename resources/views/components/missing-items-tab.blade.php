<div>
    <div class="col-md-12 no-padding-h table-responsive">
        <div class="text-right" style="margin-bottom: 2px;">
            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#reportMissingModal">Report Missing</button>
        </div>
        <table  class="table table-bordered table-hover" id="create_datatable_10">
            <thead>
                <tr >
                    <th>#</th>
                    <th>Date</th>
                    <th>Reported By</th>
                    <th>Stock Id Code</th>
                    <th>Item</th>
                    <th>Last Purchase Date</th>
                    <th>Last Sale Date</th>
                    <th>Supplier</th>
                    <th>Qoh As At</th>
                    <th>Order Qty</th>
                    <th>Amount</th>
                </tr>
            </thead>
            @php
                $total = 0;
            @endphp
            <tbody>
                @foreach ($missingItems as $item)
                    <tr>
                        <th>{{$loop->index+1}}</th>
                        <td>{{$item->created_at}}</td>
                        <td>{{$item->name}}</td>
                        <td>{{$item->stock_id_code}}</td>
                        <td>{{$item->title}}</td>
                        <td>{{$item->last_purchase_date}}</td>
                        <td>{{$item->last_sale_date}}</td>
                        <td>{{$item->supplier}}</td>
                        <td style="text-align: center;">{{$item->as_at_quantity}}</td>
                        <td style="text-align: center;">{{$item->quantity}}</td>
                        <td style="text-align: right;">{{manageAmountFormat(($item->quantity ?? 0) * $item->selling_price)}}</td>
                    </tr>
                    @php
                        $total += ($item->quantity ?? 0) * $item->selling_price;
                    @endphp
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="10">Total</th>
                    <th  style="text-align: right;">{{manageAmountFormat($total)}}</th>
                </tr>
            </tfoot>
           
        </table>
    </div>
  <!-- Modal for Reporting Missing Items -->
<div class="modal fade" id="reportMissingModal" tabindex="-1" role="dialog" aria-labelledby="reportMissingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h4 class="modal-title" id="reportMissingModalLabel">Report Missing Items</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="reportMissingForm">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="missingItemsTableBody">
                            <tr>
                                <td>
                                    <select name="item_name[]" class="form-control item-select-missing" required>
                                        <option value="">Search and select an item</option>
                                        @foreach($inventoryItemsMissing as $item)
                                            <option value="{{ $item->id }}">{{ $item->title }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" name="quantity[]" class="form-control" required></td>
                                <td><button type="button" class="btn btn-success btn-sm removeItemBtn"><i  class="fas fa-trash"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="form-group d-flex justify-content-between">
                        <button type="button" class="btn btn-success btn-sm" id="addItemBtn"><i class="fas fa-plus"></i> Add Item</button>
                        <input type="submit" class="btn btn-success btn-sm" value="Submit Report">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


</div>
@push('scripts')
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}

    <script>
        $(document).ready(function() {
            $('.item-select-missing').select2({
                placeholder: "Search and select an item",
                allowClear: true,
                dropdownParent: $('#reportMissingModal')
            });

            $('#addItemBtn').on('click', function() {
                var newRow = `
                    <tr>
                        <td>
                            <select name="item_name[]" class="form-control item-select-missing" required>
                                <option value="">Search and select an item</option>
                                @foreach($inventoryItemsMissing as $item)
                <option value="{{ $item->id }}">{{ $item->title }}</option>
                                @endforeach
                </select>
            </td>
            <td><input type="number" name="quantity[]" class="form-control" required></td>
            <td><button type="button" class="btn btn-success btn-sm removeItemBtn"><i  class="fas fa-trash"></i></button></td>
        </tr>`;

                $('#missingItemsTableBody').append(newRow);

                $('.item-select-missing').last().select2({
                    placeholder: "Search and select an item",
                    allowClear: true,
                    dropdownParent: $('#reportMissingModal')

                });
            });
            $(document).on('click', '.removeItemBtn', function() {
                $(this).closest('tr').remove();
            });
            $('#reportMissingForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('report-missing-items.report-from-web') }}",
                    method: "POST",
                    data: $(this).serialize() + '&_token=' + "{{ csrf_token() }}",
                    success: function(response) {
                        if(response.success) {
                            // alert(response.message);
                            VForm.successMessage(response.message);

                            $('#reportMissingModal').modal('hide');
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        alert('Something went wrong. Please try again.');
                    }
                });
            });
        });

    </script>

@endpush