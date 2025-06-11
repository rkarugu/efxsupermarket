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
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Reverse Splits</h3>
                    <div>
                        @if (isset($permission[$pmodule . '___add']) || $permission == 'superadmin')
                            @if ($is_display)
                                <a href="{{route('reverse-splitting.create')}}" class="btn btn-success btn-sm"><i class="fas fa-add"></i> Add Reverse Split</a>  
                            @endif
                        @endif
                    </div>
                   
                </div>
            </div>
            <div class="box-body">
                @include('message')
                <div class="col-md-12 no-padding-h table-responsive">
                    <form action="{{ route('reverse-splitting.index') }}" method="GET">
                        <div class="row">
                            
                            <div class="col-md-3 form-group">
                                <label for="">Branch</label>
                                <select name="branch" id="mlselec6t" class="form-control mlselec6t" >
                                    <option value="" selected disabled>--Select Branch--</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" {{request()->branch ==  $branch->id ? 'selected' : ''}}>{{ $branch->location_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="">Start</label>
                                <input type="date" name="date" id="date" class="form-control" value="{{request()->date ? request()->date : \Carbon\Carbon::now()->toDateString()}}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="">End</label>
                                <input type="date" name="todate" id="todate" class="form-control" value="{{request()->todate ? request()->todate : \Carbon\Carbon::now()->toDateString()}}">
                            </div>
                            <div class="col-md-3 ">
                                <label for="">&nbsp;</label>
                                <br>
                                <button type="submit" name="filter" value="Filter" class="btn btn-success"><i class="fas fa-filter"></i> Filter</button>
                                <button type="submit" name="intent" value="Excel" class="btn btn-success"><i class="fas fa-file-excel"></i> Excel</button>
                                <a href="{{route('reported-missing-items.index')}}" class="btn btn-success"><i class="fas fa-eraser"></i> Clear</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-12 no-padding-h table-responsive">
                    <table  class="table table-bordered table-hover" id="create_datatable">    
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Document No</th>
                                <th>Child Bin</th>
                                <th>Child Code</th>
                                <th>Child</th>
                                <th>Child Qty</th>
                                <th>Mother Bin</th>
                                <th>Mother Code</th>
                                <th>Mother</th>
                                <th>Mother Qty</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reverseSplits as $split)
                            <tr>
                                <th>{{$loop->index+1}}</th>
                                <th>{{$split->document_no}}</th>
                                <td>{{$split->getChildBin?->title}}</td>
                                <td>{{$split->getChildItem?->stock_id_code}}</td>
                                <td>{{$split->getChildItem?->title}}</td>
                                <td>{{$split->requested_child_quantity}}</td>
                                <td>{{$split->getMotherBin?->title}}</td>
                                <td>{{$split->getMotherItem?->stock_id_code}}</td>
                                <td>{{$split->getMotherItem?->title}}</td>
                                <td>{{$split->expected_mother_quantity}}</td>
                                <td>{{$split->status}}</td>
                                <td>
                                    @if ($split->status == 'pending')
                                        @if ($permission == 'superadmin' || $is_mother_bin )
                                            <a href="" class="approve-split" title="approve" data-split-id = {{$split->id}}><i class="fas fa-thumbs-up"></i></a>                  
                                            <a href="" style="margin-left: 5px;" class="reject-split" title="reject" data-split-id = {{$split->id}} ><i class="fas fa-thumbs-down" style="color: red;"></i></a>                  
                                        @endif
                                    @endif
                                </td>
                            </tr>
                                
                            @endforeach
                        
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal fade" id="confirmDownloadModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title">Confirm Reverse Split</h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        Are you sure you want to approve this reverse split?
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <a id="confirmDownloadBtn" href="#" class="btn btn-primary">Confirm</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="confirmRejectModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title">Confirm Reject Reverse Split</h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        Are you sure you want to reject this reverse split?
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <a id="confirmRejectBtn" href="#" class="btn btn-primary">Reject</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('uniquepagescript')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .select2 {
            width: 100% !important;
        }
    </style>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
       
        $(function() {
            $(".mlselec6t").select2();
            $('.approve-split').on('click', function (event) {
                event.preventDefault();
                var splitId = $(this).data('split-id');
                $('#confirmDownloadBtn').attr('href', "{{ url('admin/reverse-splitting/approve/') }}/" + splitId );
                $('#confirmDownloadModal').modal('show');
            });

            //close modal
            $('#confirmDownloadBtn').on('click', function () {
                var downloadLink = $(this).attr('href');
                $('#confirmDownloadModal').modal('hide');
            });
            $('.reject-split').on('click', function (event) {
                event.preventDefault();
                var splitId = $(this).data('split-id');
                $('#confirmRejectBtn').attr('href', "{{ url('admin/reverse-splitting/reject/') }}/" + splitId );
                $('#confirmRejectModal').modal('show');
            });

            //close modal
            $('#confirmRejectBtn').on('click', function () {
                var downloadLink = $(this).attr('href');
                $('#confirmRejectModal').modal('hide');
            });
        });

    </script>
@endsection
