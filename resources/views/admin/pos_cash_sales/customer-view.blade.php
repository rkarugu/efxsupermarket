@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"> {!! $title !!}  </h3>
            </div>
            @include('message')
            <div class="box-body">
                <div class="row pb-4">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Branch</label>
                            <select name="branch" id="branch" class="form-control mlselec6t">
                                <option value="">Select Branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{$branch->id}}" {{ request()->branch == $branch->id? 'selected' : ''}}>{{$branch->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">From</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{request()->input('start-date') ?? date('Y-m-d')}}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">To</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"  value="{{request()->input('end-date') ?? date('Y-m-d')}}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Status</label>
                            <select name="status" id="status" class="form-control mlselec6t">
                                <option value="dispatching" {{request()->status == 'dispatching' ? 'selected' : ''}}>Pending</option>
                                <option value="dispatched" {{request()->status == 'dispatched' ? 'selected' : ''}}>Dispatched</option>
                                <option value="collected" {{request()->status == 'collected' ? 'selected' : ''}}>Collected</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <button type="submit" id="filter" class="btn btn-success btn-sm" style="margin-top: 25px;"><i class="fas fa-filter"></i> Filter</button>
                        </div>
                    </div>
                </div>
                <table id="orderTable" class="table table-striped" style="width:100%">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Date Time</th>
                        <th>Cash Sale</th>
                        <th>Customer</th>
                        <th>Items Count</th>
                        <th>Bin Locations</th>
                        <th>Dispatched Bins</th>
                        <th>Pending Bins</th>
                        <th>Status</th>
                        <th>Completed By</th>
                        <th>Age</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

  <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Confirm Action</h3>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>

            <div class="box-body">
                Are you sure you want to remove this item from the dispatch screen?
            </div>

            <div class="box-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <a id="confirmRemove" href="#" class="btn btn-primary">Confirm</a>
                </div>
            </div>
        </div>
    </div>
</div>
  
    </section>
    


@endsection
@push('scripts')
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script>

        var VForm = new Form();
        function reloadTable() {
            $('#orderTable').DataTable().ajax.reload();
        }
        setInterval(reloadTable, 120000);

        $(document).ready(function() {
            var table = $('#orderTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('pos-cash-sales.customer-view') !!}',
                    data: function(data) {
                        var from = $('#start_date').val();
                        var to = $('#end_date').val();
                        var restaurant_id = $('#restaurant_id').val();
                        var status = $('#status').val();
                        var branch = $('#branch').val();
                        data.restaurant_id = restaurant_id;
                        data.from = from;
                        data.to = to;
                        data.status = status;
                        data.branch = branch;
                    }
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        searchable: false,
                        sortable: false,
                        width: "70px"
                    },
                    {
                        data: "created_at",
                        name: "created_at",
                        searchable: false
                    },
                    {
                        data: "sales_no",
                        name: "sales_no"
                    },
                    {
                        data: "customer",
                        name: "customer"
                    },
                    {
                        data: "items_count",
                        name: "items_count",
                        searchable: false,
                    },
                    {
                        data: "bins_count",
                        name: "bins.bins_count",
                        searchable: false,
                    },
                    {
                        data: "bins_count_dispatched",
                        name: "disp.bins_count_dispatched",
                        searchable: false,
                    },
                    {
                        data: "pending_bins",
                        name: "pending_bins",
                        searchable: false,
                    },
                    {
                        data: "state",
                        name: "state",
                        searchable: false,
                    },
                    {
                        data: "dispatcher",
                        name: "dispatcher",
                        searchable: false,
                    },
                    {
                        data: "age",
                        name: "age",
                        searchable: false,
                    },
                    {
                        data: "action",
                        name: "action",
                        searchable: false,
                    },
                ],
            });
            $('#filter').click(function(e){
                e.preventDefault();
                table.draw();
            });
            $(".mlselec6t").select2();




            $(document).on('click', '.remove-from-screen', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                $('#confirmRemove').data('id', id);
                $('#confirmationModal').modal('show');
            });

            $('#confirmRemove').on('click', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: '/admin/pos-cash-sales/remove-from-screen/' + id,
                    type: 'GET',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content') 
                    },
                    success: function(response) {
                        form.successMessage('Order Removed from Screen successfully.');
                        $('#confirmationModal').modal('hide');
                        // table.refresh()
                            window.location.reload();
                    },
                    error: function(xhr) {
                        console.log(xhr)
                        alert('Failed to remove the item. Please try again.');
                    }
                });
            });


        });
    </script>
@endpush
