@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Route Listing </h3>

                    <a href="{{ route("$base_route.create") }}" role="button" class="btn btn-primary"> Add Route </a>
                </div>
            </div>

            <div class="box-body">
                {{-- {!! Form::open(['route' => 'manage-routes.datatable', 'method' => 'get']) !!} --}}
                <div class="row">

                    <div class="col-md-3 form-group">
                        <select name="branch" id="branch" class="mlselect form-control">
                            <option value="" selected disabled>Select branch</option>
                            {{-- @foreach ($branches as $branch)
                                    <option value="{{$branch->id}}" {{ $branch->id == request()->branch ? 'selected' : '' }}>{{$branch->name}}</option>
                                @endforeach --}}
                            {{-- @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ $branch->id == request()->branch ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach --}}
                            @foreach ($branches as $index => $branch)
                                <option value="{{ $branch->id }}" {{ $index == 0 && !$isAdmin ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <button type="submit" class="btn btn-success" name="manage-request" id="filter"
                            value="filter">Filter</button>
                        <a class="btn btn-success ml-12" href="{!! route('manage-routes.listing') !!}">Clear </a>
                    </div>
                </div>
                {{-- {!! Form::close(); !!} --}}

                <hr>

                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="table-responsive">
                    <table class="table" id="routes-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th scope="col"> Route Name</th>
                                <th scope="col"> Branch</th>
                                <th scope="col"> Route Manager</th>
                                <th scope="col"> Sales Man</th>
                                <th scope="col"> Order Taking Days</th>
                                <th scope="col"> Salesman Expense</th>
                                <th scope="col">Actions</th>
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
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        function confirmRouteDeletion() {
            let userHasConfirmed = confirm(`Are you sure you want to remove this route?`);
            if (userHasConfirmed) {
                $("#delete-route-form").submit();
            }
        }

        $(function() {
            $(".mlselect").select2();
        });

        $(document).ready(function() {
            function loadTable(branch = '') {
                $('#routes-table').DataTable({
                    "paging": true,
                    "lengthChange": true,
                    "processing": true,
                    // "serverSide": true,
                    'searching': true,
                    "order": [
                        [0, "asc"]
                    ],
                    "pageLength": 50,
                    "destroy": true,
                    "ajax": {
                        "url": '{!! route("$base_route.datatable") !!}',
                        "dataType": "json",
                        "type": "GET",
                        "data": {
                            _token: "{{ csrf_token() }}",
                            branch: branch
                        }
                    },
                    "columns": [{
                            data: 'row_number',
                            name: 'row_number'
                        },
                        {
                            data: 'route_name',
                            name: 'route_name',
                            orderable: true
                        },
                        {
                            data: 'branch',
                            name: 'branch',
                            orderable: false
                        },
                        {
                            data: 'route_manager',
                            name: 'route_manager',
                            orderable: false
                        },
                        {
                            data: 'salesman',
                            name: 'salesman',
                            orderable: false
                        },
                        {
                            data: 'order_taking_days',
                            name: 'order_taking_days',
                            orderable: false
                        },
                        {
                            data: 'travel_expense',
                            name: 'travel_expense',
                            orderable: true
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false
                        }
                    ],
                    "columnDefs": [{
                        "searchable": false,
                        "targets": 0
                    }]
                });
            }

            loadTable();

            $('#filter').on('click', function(e) {
                e.preventDefault();
                let branch = $('#branch').val();
                loadTable(branch);
            });
        });
    </script>
@endsection
