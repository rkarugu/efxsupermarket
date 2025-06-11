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
                    <h3 class="box-title">Stock Take User Assignments </h3>
                    <div>
                        @if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
                            <a href="{!! route('admin.stock-counts-users-assingment.create')!!}" class="btn btn-success btn-sm">Create New Assignment</a>    
                        @endif
                        {{-- @if(isset($permission[$pmodule.'___upload']) || $permission == 'superadmin')
                            <a href="{!! route('admin.stock-counts.user-items-upload')!!}" class="btn btn-success btn-sm">Upload User Items</a>    
                        @endif --}}
                        @if(isset($permission[$pmodule.'___view']) || $permission == 'superadmin')
                        <a href="{!! route('admin.stock-count.user-item-assignments.all')!!}" class="btn btn-success btn-sm">User Items</a>    
                        @endif
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'admin.stock-counts-users-assingment', 'method' => 'get']) !!}
                    <div class="row" style="margin-left: 2px; padding-left:2px;" >
    
                        <div class="col-md-2 form-group">
                            <input type="date" name="start_date" id="from" class="form-control" value="{{ request()->get('start_date') ?? \Carbon\Carbon::now()->toDateString() }}">
                        </div>
    
                        <div class="col-md-2 form-group">
                            <input type="date" name="end_date" id="to" class="form-control" value="{{ request()->get('end_date') ?? \Carbon\Carbon::now()->toDateString() }}">
                        </div>
    
                        <div class="col-md-2 form-group">
                            <button type="submit" class="btn btn-success btn-sm" name="manage-request" value="filter">Filter</button>
                            <a class="btn btn-success btn-sm" href="{!! route('admin.stock-counts-users-assingment') !!}">Clear </a>
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
                            <th>Stock Take Date</th>
                            <th>Created By</th>
                            <th>Assignee</th>
                            <th>Bin</th>
                            <th>Categories</th>
                            <th>Action</th>                           
                            
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($stockTakeAssignments as $record)
                            <tr>
                                <th>{{$loop->index+1}}</th>
                                <td>{{\Carbon\Carbon::parse($record->stock_take_date)->toDateString()}}</td>
                                <td>{{$record->storeKeeper?->name}}</td>
                                <td>
                                    @foreach ($record->assistant as $assistant)
                                        {{$assistant->assistant?->name}},  
                                    @endforeach
                                </td>
                                <td>{{$record->uom?->title}}</td>
                                <td>{{$record->assignedCategories}}</td>
                                <td>
                                    <div class="action-button-div">
                                        @if(isset($permission[$pmodule.'___edit']) || $permission =='superadmin')
                                        <a href="{!! route('admin.stock-counts-users-assingment.edit', $record->id)!!}" class=""><i class="fa fa-pen"></i></a>
                                        @endif
                                    </div>
                                    
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
            <h4 class="modal-title" id="staticBackdropLabel">Are you sure you want to Delete this promotion?</h4>
           
        </div>
        <form method="POST" id="confirmationForm2" action="">
            @csrf
            @method("DELETE")
            
            <input name="user_requested_access2" type="hidden" id="user_requested_access2"
                    value="{{ old('user_requested_access2') }}" required />
           
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary btn-submit-updated-center2">Yes, Delete Promotion</button>
            </div>
        </form>
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
<script type="text/javascript">
    $(function () {

        $(".mlselect").select2();
    });
</script>
<script>
    $(document).ready(function() {
        $('.btn-decline').click(function() {
            var promotionId = $(this).data('promotion-id');
            $('#confirmationModal').find('#promotion_id').val(promotionId);
            console.log(promotionId);
            $('#confirmationForm').attr('action', '{{ route('promotions-bands.block', ['promotionId' => ':promotionId']) }}'.replace(':promotionId', promotionId));
            console.log("Form action:", $('#confirmationForm').attr('action')); // Check if action attribute is set correctly

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
            $('#confirmationModal2').find('#promotion_id').val(promotionId);
            $('#confirmationForm2').attr('action', '{{ route('promotions-bands.delete', ['promotionId' => ':promotionId']) }}'.replace(':promotionId', promotionId));
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
@endsection



