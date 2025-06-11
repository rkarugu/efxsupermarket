@extends('layouts.admin.admin')

@section('content')
    <?php
    $logged_user_info = getLoggeduserProfile();
    $my_permissions = $logged_user_info->permissions;
    ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title">Summary Stock Break Report </h3>
                </div>
            </div>
            <div class="box-body">
                {!! Form::open(['route' => 'stock-breaking.summary', 'method' => 'get']) !!}
                <div class="row">
                    @if($permission == 'superadmin')
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
                        <input type="date" name="start_date" id="from" class="form-control" value="{{ request()->get('start_date') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-3 form-group">
                        <button type="submit" class="btn btn-success" name="manage-request" value="filter"><i class="fas fa-filter"></i> Filter</button>
                        {{-- <input type="submit" class = "btn btn-success" name="type" value="Download"> --}}
                        <button type="submit" class="btn btn-success" name="type" value="Download"> <i class="fas fa-download"></i> Download</button>
                        <a class="btn btn-success" href="{!! route('stock-breaking.summary') !!}"><i class="fas fa-eraser"></i> Clear</a>
                    </div>
                </div>
                {!! Form::close(); !!}
                <hr>
                @include('message')
                <div class="col-md-12">
                    <h4>Auto Breaks</h4>
                        <table class="table table-bordered table-hover" id="create_datatable_50">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Document No.</th>
                                <th>Mother Bin</th>
                                <th>Mother Item</th>
                                <th>Mother Qty</th>
                                <th>Child Bin</th>
                                <th>Child Item</th>
                                <th>Child Qty</th>
                            </tr>
                            </thead>
                           <tbody>
                            @foreach ($records as $break)
                            <tr>
                                <th>{{$loop->index+1}}</th>
                                <td>{{\Carbon\Carbon::parse($break->created_at)->toDateString()}}</td>
                                <td>{{$break->break_number}}</td>
                                <td>{{$break->mother_bin}}</td>
                                <td>{{$break->mother_code .' - '. $break->mother_name}}</td>
                                <td style="text-align: center;">{{$break->total_mother_quantity}}</td>
                                <td>{{$break->child_bin}}</td>
                                <td>{{$break->child_code .' - '. $break->child_name}}</td>
                                <td style="text-align: center;">{{$break->total_child_quantity}}</td>
                            </tr>
                                
                            @endforeach

                           </tbody>
                        
                        </table>
                </div>
                <div class="col-md-12">
                    <h4>Manual Breaks</h4>
                        <table class="table table-bordered table-hover" id="create_datatable_25">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Document No.</th>
                                <th>Mother Bin</th>
                                <th>Mother Item</th>
                                <th>Mother Qty</th>
                                <th>Child Bin</th>
                                <th>Child Item</th>
                                <th>Child Qty</th>
                            </tr>
                            </thead>
                           <tbody>
                            @foreach ($manualBreaks as $break)
                            <tr>
                                <th>{{$loop->index+1}}</th>
                                <td>{{\Carbon\Carbon::parse($break->created_at)->toDateString()}}</td>
                                <td>{{$break->break_number}}</td>
                                <td>{{$break->mother_bin_location}}</td>
                                <td>{{$break->mother_code .' - '. $break->mother_name}}</td>
                                <td style="text-align: center;">{{$break->mother_quantity}}</td>
                                <td>{{$break->child_bin_location}}</td>
                                <td>{{$break->child_code .' - '. $break->child_name}}</td>
                                <td style="text-align: center;">{{$break->child_quantity}}</td>
                            </tr>
                                
                            @endforeach

                           </tbody>
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
        function printgrn() {
                var confirm_text = 'report';
                var isconfirmed = confirm("Do you want to print " + confirm_text + "?");
            let branch = $('#branch').val();
            let uom = $('#uom').val();
            let start_date = $('#from').val();
            if (isconfirmed) {
                jQuery.ajax({
                    url: '{{ route('admin.stock-count-variance.print') }}',
                    // async: true, //NOTE THIS
                    type: 'GET',
                    data: {
                        branch: branch,
                        uom: uom,
                        start_date: start_date,
                    },
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('response');
                        var divContents = response;
                        var printWindow = window.open('', '', 'width=600');
                        printWindow.document.write(divContents);
                        printWindow.document.close();
                        printWindow.print();
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', status, error);
                    }
                
                });
            }
        }
    </script>
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
