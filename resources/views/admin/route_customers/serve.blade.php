@extends('layouts.admin.admin')

@section('content')
    <section class="content">

        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"> {!! $title !!}</h3>
            </div>
            @include('message')
            <div class="box-body">
                <div class="row pb-4">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">From</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{request()->input('start-date') ?? date('Y-m-d')}}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">To</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"  value="{{request()->input('end-date') ?? date('Y-m-d')}}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Route</label>
                            <select name="route_id" id="route_id" class="form-control">
                                <option value="">Select Route</option>
                                @foreach($routes as $route)
                                    <option value="{{ $route->id }}" {{ $route->route_name == request()->route_id ? 'selected' : '' }}> {{ $route->route_name }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <button type="submit" id="filter" class="btn btn-primary btn-sm" style="margin-top: 25px;">Filter</button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table" id="customerTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th> Date Onboarded</th>
                            <th> Route</th>
                            <th> Center</th>
                            <th> Business Name</th>
                            <th> Customer Name</th>
                            <th> Phone Number</th>
                            <th> Status</th>
                            <th> Comment</th>
                            <th> Actions</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('uniquepagestyle')
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
@endsection
@section('uniquepagescript')
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script>
        // var VForm = new Form();
        $(document).ready(function() {
            var table = $("#customerTable").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('route-customers.time-served') !!}',
                    data: function(data) {
                        var route_id = $('#route_id').val();
                        var from = $('#start_date').val();
                        var to = $('#end_date').val();
                        data.from = from;
                        data.to = to;
                        data.route_id = route_id;
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    sortable: false,
                    width: "70px"
                },
                    {
                        data: "created_at",
                        name: "created_at"
                    },
                    {
                        data: "route.route_name",
                        name: "route.route_name"
                    },
                    {
                        data: "location_name",
                        name: "location_name"
                    },
                    {
                        data: "bussiness_name",
                        name: "bussiness_name"
                    },
                    {
                        data: "name",
                        name: "name"
                    },
                    {
                        data: "phone",
                        name: "phone"
                    },
                    {
                        data: "status",
                        name: "status"
                    },
                    {
                        data: "time_taken",
                        name: "time_taken"
                    },
                    {
                        data: "action",
                        name: "action",
                        searchable: false
                    }
                ],

            });

            $('#filter').click(function(e){
                e.preventDefault();
                table.draw();
            });
            $("#route_id").select2();
            $(".mlselec6t").select2();
        })

    </script>
    <script>
        $(document).ready(function() {
            $('#show-export-excel-modal').click(function() {
                $('#export-customer-modal').modal('show');
            });
        });
    </script>
    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>
    <script>
        function submitExportForm() {
            // Submit the form
            document.getElementById('export-customer-form').submit();
            $('#export-customer-modal').modal('hide');
            form.reset();


        }
    </script>
@endsection