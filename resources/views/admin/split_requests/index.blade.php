@extends('layouts.admin.admin')

@section('content')

    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title">Split Requests</h3>
                    
                </div>
            </div>

            <div class="box-body">
                {!! Form::open(['route' => 'stock-breaking.split-requests', 'method' => 'get']) !!}
                <div class="row">
                    <div class="col-md-2 form-group">
                        <select name="store" id="store" class="form-control mlselec6t">
                           <option value="" selected disabled>Select Branch</option>
                          @foreach ($stores as $store)
                          <option value="{{$store->id}}" @if (request()->store == $store->id)
                            selected
                          @endif >{{ $store->location_name }}</option>
                              
                          @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <select name="status" id="status" class="form-control mlselec6t">
                            <option value="pending" {{request()->status == 'pending' ? 'selected' : ''}}>Pending</option>
                            <option value="rejected" {{request()->status == 'rejected' ? 'selected' : ''}}>Rejected</option>
                            <option value="approved" {{request()->status == 'rejected' ? 'selected' : ''}}>Approved</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <input type="date" name="start_date" id="from" class="form-control" value="{{ request()->get('start_date') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-2 form-group">
                        <input type="date" name="end_date" id="to" class="form-control" value="{{ request()->get('end_date') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-3 form-group">
                        <button type="submit" class="btn btn-success btn-sm" name="manage-request" value="filter"><i class="fas fa-filter" ></i>Filter</button>
                        {{-- <button type="submit" class="btn btn-success" name="type" value="Download">Download</button> --}}
                        <a class="btn btn-success btn-sm" href="{!! route('stock-breaking.split-requests') !!}"><i class="fa-solid fa-eraser"></i>Clear </a>
                    </div>
                </div>

                {!! Form::close(); !!}

                <hr>

                @include('message')
                <div class="col-md-12">
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
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($requests as $request)
                                <tr>
                                    <th>{{$loop->index+1}}</th>
                                    <td>{{\Carbon\Carbon::parse($request->created_at)->toDateString()}}</td>
                                    <td>{{$request->getInitiatingUser->name}}</td>
                                    <td>{{$request->getChildBinDetail->title}}</td>
                                    <td>{{$request->getChild->stock_id_code}}</td>
                                    <td>{{$request->getChild->title}}</td>
                                    <td>{{$request->getMotherBinDetail->title}}</td>
                                    <td>{{$request->getMother->stock_id_code}}</td>
                                    <td>{{$request->getMother->title}}</td>
                                    <td style="text-align: center;">{{$request->requested_quantity}}</td>
                                    <td style="text-align: center;">{{$request->mother_qoh}}</td>
                                    <td>
                                        
                                        <input type="checkbox" name="selected_requests[]" value="{{$request->id}}" class="request-checkbox">
                                    </td>

                                </tr>
                                
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="11" class="text-right" class="border-right:none;">
                                    <form id="unblock_selected_form_reject" action="{{ route('admin.reject-stock-split-requests') }}" method="POST">
                                        @csrf
                                        <button type="submit" id="reject_button" class="btn btn-success btn-sm" disabled><i class="fas fa-thumbs-down"></i> Reject Selected</button>
                                    </form>
                               
                                </td>
                                <td colspan="1" class="text-right" style="border-left: none;">
                                    <form id="unblock_selected_form" action="{{ route('admin.approve-stock-split-requests') }}" method="POST">
                                        @csrf
                                        <button type="submit" id="approve_button" class="btn btn-success btn-sm" disabled><i class="fas fa-thumbs-up"></i> Approve Selected</button>
                                    </form>
                               
                                </td>
                            </tr>
                        </tfoot>
                        
                    </table>
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
            $(".mlselec6t").select2();
        });
    </script>

    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>
    <script type="text/javascript">
    $(document).ready(function() {
        $('#approve_button').prop('disabled', true);

        $('input[name="selected_requests[]"]').on('change', function() {
            if ($('input[name="selected_requests[]"]:checked').length > 0) {
                $('#approve_button').prop('disabled', false);
            } else {
                $('#approve_button').prop('disabled', true);
            }
        });
        $('#reject_button').prop('disabled', true);

        $('input[name="selected_requests[]"]').on('change', function() {
            if ($('input[name="selected_requests[]"]:checked').length > 0) {
                $('#reject_button').prop('disabled', false);
            } else {
                $('#reject_button').prop('disabled', true);
            }
        });
        $('#unblock_selected_form, #unblock_selected_form_reject').on('submit', function(e) {
            e.preventDefault();
            var selectedForm = $(this);
            var selectedRequests = $('.request-checkbox:checked').clone();

            selectedForm.append(selectedRequests);
            selectedForm.off('submit').submit(); 
        });
    });

    </script>
@endsection
