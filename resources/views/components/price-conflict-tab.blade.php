<div>
    <div class="col-md-12 no-padding-h table-responsive">
        <div class="text-right" style="margin-bottom: 2px;">
            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#reportPriceConflictModal">Report Price Conflict</button>
        </div>
        <table  class="table table-bordered table-hover" id="create_datatable">
            <thead>
                <tr >
                    <th>#</th>
                    <th>Date</th>
                    <th>Reported By</th>
                    <th>Stock Id Code</th>
                    <th>Title</th>
                    <th>As At S.Cost</th>
                    <th>As At Selling Price</th>
                    <th>Reported Price</th>
                    {{-- <th>Supporting Documents</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($reportedConflictItems as $item)
                <tr>
                    <th>{{$loop->index+1}}</th>
                    <td>{{\Carbon\Carbon::parse($item->created_at)->toDateString()}}</td>
                    <td>{{$item->name}}</td>
                    <td>{{$item->stock_id_code}}</td>
                    <td>{{$item->title}}</td>
                    <td style="text-align: right;">{{manageAmountFormat($item->current_standard_cost)}}</td>
                    <td style="text-align: right;">{{manageAmountFormat($item->current_selling_price)}}</td>
                    <td style="text-align: right;">{{manageAmountFormat($item->reported_price)}}</td>
                    {{-- <td>
                        @if ($item->image)
                        <a href="{{ asset('uploads/shift_issues/'.$item->image)}}" target="_blank"><img src="{{ asset('uploads/shift_issues/'.$item->image)}}" alt="" style="width: 30px;"></a>
                            
                        @endif
                    </td> --}}
                </tr>
                    
                @endforeach
       
            </tbody>
            </tfoot>
           
        </table>
    </div>
</div>
 <!-- Modal for Reporting New Items -->
<div class="modal fade" id="reportPriceConflictModal" tabindex="-1" role="dialog" aria-labelledby="reportNewItemModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h4 class="modal-title" id="reportNewItemModalLabel">Report Price Conflict</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="reportPriceConflictForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="productName">Item</label>
                        <select name="item_id" id="item_id" class="form-control item-select" required>
                            <option value="">Search and select an item</option>
                            @foreach ($inventoryItems as $item)
                            <option value="{{$item->id }}">{{ $item->stock_id_code.' - '. $item->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" name="price" id="price" class="form-control" required>
                    </div>
                   
                   
                    <div class="text-right">
                        <input type="submit" class="btn btn-success btn-sm" value="Submit">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
@push('scripts')
<script>
    $(document).ready(function() {
           $('.item-select').select2({
               placeholder: "Search and select an item",
               allowClear: true,
               dropdownParent: $('#reportPriceConflictModal')
           });
       });

   $('#reportPriceConflictForm').on('submit', function(e) {
   e.preventDefault();
   var csrfToken = "{{ csrf_token() }}";

   var formData = new FormData(this);

   $.ajax({
       url: "{{ route('report-price-conflicts.report-from-web') }}",
       method: "POST",
       data: formData,
       processData: false,
       contentType: false,
       headers: {
               'X-CSRF-TOKEN': csrfToken 
           },
       success: function(response) {
           if(response.success) {
                 VForm.successMessage(response.message);
               $('#reportPriceConflictModal').modal('hide');
               location.reload(); 
           }
       },
       error: function(xhr) {
           alert('Something went wrong. Please try again.');
       }
   });
});

</script>
    
@endpush

