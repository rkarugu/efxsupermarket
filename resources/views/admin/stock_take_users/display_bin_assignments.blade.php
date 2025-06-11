@extends('layouts.admin.admin')

@section('content')
    <style>
        .span-action {

            display: inline-block;
            margin: 0 3px;

        }
    </style>
    <!-- Main content -->
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title">User  Item Assignments </h3>
                    <div>
                        <a href="{{route('admin.stock-count.user-item-assignments.add-allocation')}}" class="btn btn-success btn-sm">Add</a>
                        @if(isset($permission[$pmodule.'___upload']) || $permission == 'superadmin')
                            <a href="{!! route('admin.stock-counts.user-items-upload')!!}" class="btn btn-success btn-sm">Upload User Items</a>    
                        @endif
                        @if(isset($permission[$pmodule.'___transfer']) || $permission == 'superadmin')
                            <button class="btn btn-success btn-sm" id="transfer">Transfer</button>    
                        @endif
                        @if(isset($permission[$pmodule.'___batch-upload']) || $permission == 'superadmin')
                            <a href="{!! route('admin.stock-counts.batch-upload')!!}" class="btn btn-success btn-sm">Batch Upload</a>    
                        @endif
                        <a href="{{route('admin.stock-counts-users-assingment')}}" class="btn btn-success btn-sm">Back</a>

                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'admin.stock-count.user-item-assignments.all', 'method' => 'get']) !!}
                    <div class="row" style="margin-left: 2px; padding-left:2px;" >
    
                        <div class="col-md-2 form-group">
                            <select name="branch" id="branch" class="mlselect form-control" >
                                <option value="" selected disabled>Select branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{$branch->id}}" {{ $branch->id == request()->branch? 'selected' : '' }}>{{$branch->location_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 form-group">
                            <select name="user" id="user" class="mlselect form-control" >
                                <option value="" selected disabled>Select User</option>
                                @foreach ($users as $user)
                                    <option value="{{$user->id}}" {{ $user->id == request()->user? 'selected' : '' }}>{{$user->name}}</option>
                                @endforeach
                            </select>
                        </div>
    
                        <div class="col-md-2 form-group">
                            <button type="submit" class="btn btn-success btn-sm" name="manage-request" value="filter">Filter</button>
                            <button type="submit" name='intent' value="excel" class="btn btn-success btn-sm">Download</button>
                            <a class="btn btn-success btn-sm" href="{!! route('admin.stock-count.user-item-assignments.all') !!}">Clear </a>
                        </div>
                    </div>
    
                    {!! Form::close(); !!}
                </div>
                <hr>
                @include('message')
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="create_datatable_10">
                        <thead>
                        <tr>
                            <th width="3%">#</th>
                            {{-- <th>Branch</th> --}}
                            <th>Bin</th>
                            <th>User</th>
                            <th>Stock Id Code</th>
                            <th>Title</th>
                            <th>Action</th>                           
                            
                        </tr>
                        </thead>
                       <tbody>
                        @foreach ($allocations as $allocation)
                            <tr>
                                <th>{{$loop->index+1}}</th>
                                {{-- <td>{{$allocation->getRelatedStore?->location_name}}</td> --}}
                                <td>{{$allocation->getRelatedBin?->title}}</td>
                                <td>{{$allocation->getRelatedUser?->name}}</td>
                                <td>{{$allocation->getRelatedItem?->stock_id_code }}</td>
                                <td>{{$allocation->getRelatedItem?->title}}</td>
                                <td>
                                    @if ($permission == 'superadmin' || isset($permission['stock-take-user-assignment___delete']))
                                        <button class="delete-allocation" data-allocation-id="{{$allocation->id}}" style="border:none; background:transparent;"><i class="fas fa-trash" style="color: red; "></i></button>
                                    @endif
                                </td>
                            </tr>       
                        @endforeach
                       </tbody>
                    </table>
                </div>

            </div>

               
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
            {{-- @method('PUT') --}}
            
            <input name="user_requested_access" type="hidden" id="user_requested_access"
                    value="{{ old('user_requested_access') }}" required />
           
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary btn-submit-updated-center">Yes, Block Promotion</button>
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
            <h4 class="modal-title" id="staticBackdropLabel">Are you sure you want to Delete this allocation?</h4>
           
        </div>
        <form method="POST" id="confirmationForm2" action="">
            @csrf
            @method("DELETE")
            
            <input name="user_requested_access2" type="hidden" id="user_requested_access2"
                    value="{{ old('user_requested_access2') }}" required />
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a class="btn btn-success btn-submit-updated-center2"  id="delete-action" href="">Yes, Delete</a>
            </div>
        </form>
    </div>
</div>
</div>
{{-- Transfer Modal --}}
<div class="modal fade" id="transferModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
aria-labelledby="staticBackdropLabel" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="staticBackdropLabel">Transfer Item  Allocations</h4>
           
        </div>
        <div class="box-body">
            <div class=" form-group">
                <label for="from_user">From</label>
                <select name="from_user" id="from_user" class="mlselect modal-select form-control" >
                    <option value="" selected disabled>Select User</option>
                    @foreach ($users as $user)
                        <option value="{{$user->id}}" {{ $user->id == request()->user? 'selected' : '' }}>{{$user->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class=" form-group">
                <label for="from_user">To</label>
                <select name="to_user" id="to_user" class="mlselect modal-select form-control" >
                    <option value="" selected disabled>Select User</option>
                    @foreach ($users as $user)
                        <option value="{{$user->id}}" {{ $user->id == request()->user? 'selected' : '' }}>{{$user->name}}</option>
                    @endforeach
                </select>
            </div>

        </div>
           
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id='transferConfirmation'>Transfer</button>
            </div>
    </div>
</div>
</div>
   </section> 
@endsection
@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection
@section('uniquepagescript')
<script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
<script type="text/javascript">
    $(function () {

        $(".mlselect").select2();
    });
</script>
    <script>
        $(document).ready(function() {
            $('#create_datatable_10 tbody').on('click', '.delete-allocation', function() {
                var id = $(this).data('allocation-id');
                $('#delete-action').attr('href', '{{ route('admin.stock-count.user-item-assignments.destroy', ['id' => ':id']) }}'.replace(':id', id));
                $('#confirmationModal2').modal('show');
            });
            $('#transfer').on('click', function() {
                var id = $(this).data('allocation-id');
                $('#transferModal').modal('show');
            });
           
            $('#transferConfirmation').on('click', function () {
            const from_user = $('#from_user').val();
            const to_user = $('#to_user').val();
            let form = new Form();

            $.ajax({
                url: '{{ route("admin.stock-count.user-item-assignments.transfer-allocation") }}',
                method: 'POST',
                data: {
                    from_user: from_user,
                    to_user: to_user,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {

                    form.successMessage('Items Transferred Successfully.');

                    $('#transferModal').modal('hide');
                    
                    window.location.reload();
                },
                error: function (error) {
                    form.errorMessage(error.response.data.message);  
                }
            });

         
        });
        });
    </script>
    
@endsection



