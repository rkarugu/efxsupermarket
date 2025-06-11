<div>
    <div class="col-md-12 no-padding-h table-responsive">
        <div class="text-right" style="margin-bottom: 2px;">
            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#reportNewItemModal">Report New Item</button>
        </div>
        <table  class="table table-bordered table-hover" id="create_datatable">
            <thead>
                <tr >
                    <th>#</th>
                    <th>Date</th>
                    <th>Reported By</th>
                    <th>Item Name</th>
                    <th>Comment</th>
                    {{-- <th>Supporting Documents</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($reportedItems as $item)
                <tr>
                    <th>{{$loop->index+1}}</th>
                    <td>{{\Carbon\Carbon::parse($item->created_at)->toDateString()}}</td>
                    <td>{{$item->name}}</td>
                    <td>{{$item->product_name}}</td>
                    <td>{{$item->comment}}</td>
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
<div class="modal fade" id="reportNewItemModal" tabindex="-1" role="dialog" aria-labelledby="reportNewItemModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h4 class="modal-title" id="reportNewItemModalLabel">Report New Item</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="reportNewItemForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="productName">Product Name</label>
                        <input type="text" name="product_name" id="productName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="comment">Comment</label>
                        <textarea name="comment" id="comment" class="form-control" rows="3"></textarea>
                    </div>
                    {{-- <div class="form-group">
                        <label for="image">Receipt</label>
                        <input type="file" name="image" id="image" class="form-control-file">
                    </div> --}}
                    <div class="text-right">
                        <input type="submit" class="btn btn-success btn-sm" value="Submit Item">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $('#reportNewItemForm').on('submit', function(e) {
    e.preventDefault();
    var csrfToken = "{{ csrf_token() }}";

    var formData = new FormData(this);

    $.ajax({
        url: "{{ route('report-new-items.report-from-web') }}",
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

                $('#reportNewIemModal').modal('hide');
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
