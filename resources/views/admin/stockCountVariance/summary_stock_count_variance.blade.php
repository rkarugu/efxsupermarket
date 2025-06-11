@extends('layouts.admin.admin')

@section('content')
    <?php
    $logged_user_info = getLoggeduserProfile();
    $my_permissions = $logged_user_info->permissions;
    ?>
            <!-- Main content -->
    <section class="content">
     
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Summary Stock Count Variance Report  </h3>
                </div>
            </div>

            <div class="box-body">
                {!! Form::open(['route' => 'admin.stock-count-variance.summary', 'method' => 'get']) !!}
                <div class="row">
                    @if ($logged_user_info->role_id == 1 ||  $logged_user_info->role_id == 147)

                        <div class="col-md-2 form-group">
                            <select name="branch" id="branch" class="mlselect form-control" data-url="{{ route('admin.get-branch-uoms') }}">
                                <option value="" selected disabled>Select branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{$branch->id}}" {{ $branch->id == request()->branch ? 'selected' : '' }}>{{$branch->location_name}}</option>

                                @endforeach
                            </select>

                        </div>
                    @endif
                   @if(isset($user->role_id) && $user->role_id != 152)

                    <div class="col-md-2 form-group">
                        <select name="uom" id="uom" class="mlselect form-control">
                            <option value="" selected disabled>Select Bin</option>
                            @foreach ($uoms as $uom)
                                <option value="{{$uom->id}}" {{ $uom->id == request()->uom ? 'selected' : '' }}>{{$uom->title}}</option>

                            @endforeach
                        </select>

                    </div>
                    @endif
                    <div class="col-md-2 form-group">
                        <select name="storekeeper" id="storekeeper" class="mlselect form-control">
                            <option value="" selected disabled>Select StoreKeeper</option>
                            @foreach ($storeKeepers as $storeKeeper)
                                <option value="{{$storeKeeper->id}}" {{ $storeKeeper->id == request()->storekeeper ? 'selected' : '' }}>{{$storeKeeper->name}}</option>

                            @endforeach
                        </select>

                    </div>
                 
                    <div class="col-md-2 form-group">
                        <input type="date" name="start_date" id="from" class="form-control" value="{{ request()->get('start_date') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-2 form-group">
                        <input type="date" name="end_date" id="to" class="form-control" value="{{ request()->get('end_date') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-3 form-group">
                        <button type="submit" class="btn btn-success btn-sm" name="manage-request" value="filter">Filter</button>
                        <input type="submit" class = "btn btn-success btn-sm" name="type" value="Download">
                        <a class="btn btn-success btn-sm" href="{!! route('admin.stock-count-variance.summary') !!}">Clear </a>
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
                                <th>Stock Id Code</th>
                                <th>Title</th>
                                <th>Bin</th>
                                @foreach ($uniqueDatesArray as $date)
                                <th style="text-align: center;">{{$date}}</th>
                                @endforeach
                            </tr>
                            </thead>
                             <tbody>
                                @foreach ($data as $row)
                                <tr>
                                    <th>{{$loop->index+1}}</th>
                                    <td>{{$row[0]->getInventoryItemDetail->stock_id_code}}</td>
                                    <td>{{$row[0]->getInventoryItemDetail->title}}</td>
                                    <td>{{$row[0]->getUomDetail?->title}}</td>
                                    @foreach ($uniqueDatesArray as $date)
                                    <td style="text-align: center;">
                                        @php
                                            $found = false; 
                                        @endphp
                                        @foreach ($row as $count)
                                            @if (\Carbon\Carbon::parse($count->created_at)->toDateString() == $date)
                                                {{ $count->variation ?? 'NCE'  }}
                                                @php
                                                    $found = true;
                                                    break; 
                                                @endphp

                                            @endif
                                        @endforeach
                                        @if (!$found)
                                            NCE
                                        @endif
                                    </td>
                                @endforeach

                                </tr>
                                    
                                @endforeach
                            

                            </tbody> 
                          
                            <tfoot>
                        
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

            $(".mlselect").select2();
        });
    </script>

    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>



    <script type="text/javascript">
        $(document).ready(function () {
            $('.download-link').on('click', function (event) {
                event.preventDefault();
                var shiftId = $(this).data('shift-id');
                $('#confirmDownloadBtn').attr('href', "{{ url('admin/salesman-shifts') }}/" + shiftId + "/loading-sheet");
                $('#confirmDownloadModal').modal('show');
            });

            //close modal
            $('#confirmDownloadBtn').on('click', function () {
                var downloadLink = $(this).attr('href');
                $('#confirmDownloadModal').modal('hide');
            });

            //shift reopen
            $('.shift-reopen').on('click', function (event) {
                event.preventDefault();
                var shiftId = $(this).data('shift-id');
                $('#confirmShiftReopenBtn').attr('href', "{{ url('admin/salesman-shifts') }}/" + shiftId + "/reopen-from-back-end");
                $('#confirmShiftReopenModal').modal('show');
            });

            //close modal
            $('#confirmShiftReopenBtn').on('click', function () {
                var downloadLink = $(this).attr('href');
                $('#confirmShiftReopenModal').modal('hide');

            });
        });
    </script>
          <script>
            $(document).ready(function() {
                $('#branch').change(function() {
                    var branchId = $(this).val();
                    var url = $(this).data('url');
        
                    $.ajax({
                        url: url,
                        type: 'GET',
                        data: { branch_id: branchId },
                        success: function(data) {
                            console.log(data);
                            $('#uom').empty();
                            $('#uom').append('<option value="" selected disabled>Select Bin</option>');
        
                            $.each(data.uoms, function(key, value) {
                                $('#uom').append('<option value="' + value.id + '">' + value.title + '</option>');
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        }
                    });
                });
            });
        </script>
    
@endsection
