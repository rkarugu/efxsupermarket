@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title" style="width: 30%">Route Customers</h3>
                    <div class="d-flex justify-content-between flex-grow-1">
                        <form action="{{ route('route-customers.index') }}" method="GET"
                            class="d-flex justify-content-between flex-grow-1">
                            <select name="branch" id="branch" class="form-control flex-grow-1 select2"
                                style="margin-left:10px">
                                <option value="">Choose Branch</option>
                                @foreach ($branch as $item)
                                    <option value="{{ $item->id }}"
                                        {{ request()->branch == $item->id ? 'selected' : '' }}>{{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            <select name="route" id="route" class="form-control flex-grow-1 select2"
                                style="margin-left:10px">
                                <option value="">Choose Route</option>
                            </select>
                            <select name="center" id="center" class="form-control flex-grow-1 select2"
                                style="margin-left:10px">
                                <option value="">Choose Centers</option>
                            </select>
                        </form>
                        <a href="{!! route('route-customers.export-all-route-customers') !!}" class="btn btn-primary" style="margin-left:10px"> Export All
                            to Excel</a>
                        <button type="button" class="btn btn-primary" id="show-export-excel-modal"
                            style="margin-left:10px">Export New to Excel</button>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table" id="routeCustomersDataTable">
                        <thead>
                            <tr>
                                <th> Date Onboarded</th>
                                <th> Route</th>
                                <th> Center</th>
                                <th> Business Name</th>
                                <th> Customer Name</th>
                                <th> Phone Number</th>
                                <th> Status</th>
                                <th> Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
    </section>
    {{-- export all modal --}}
    <div class="modal fade" id="export-customer-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title"> Select Onboarding Range </h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="modal-body">
                    <form action="{{ route('route-customers.export-new-customers') }}" method="post"
                        id="export-customer-form">
                        @csrf
                        <div class="form-group">
                            <label for="from">From:</label>
                            <input type="date" name="from" id="from" class="form-control datepicker"
                                placeholder="from">
                        </div>
                        <div class="form-group">
                            <label for="from">To:</label>
                            <input type="date" name="to" id="to" class="form-control datepicker"
                                placeholder="to">
                        </div>
                    </form>
                </div>
                <div class="box-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="submitExportForm()">Download</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.css') }}"
        rel="stylesheet" />
@endpush
@push('scripts')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>
    <script>
        $('body').addClass('sidebar-collapse');

        $(document).ready(function() {
            $(".select2").select2();

            $('#show-export-excel-modal').click(function() {
                $('#export-customer-modal').modal('show');
            });

            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd'
            });

            $("#branch").change(function() {
                let branch = $(this).val();
                $.ajax({
                    url: "{{ route('route-customers.routes') }}",
                    data: {
                        branch: branch,
                    },
                    success: function(data) {
                        $("#route").html(new Option('Please Select', '', false, false));
                        var res = data.routes.map(function(item) {
                            let option = new Option(item.route_name, item.id, false,
                                false)
                            $("#route").append(option)
                        });
                    }
                });

                refreshTable();
            })

            $("#route").change(function() {
                let route = $(this).val();
                $.ajax({
                    url: "{{ route('route-customers.centers') }}",
                    data: {
                        route: route,
                    },
                    success: function(data) {
                        $("#center").html(new Option('Please Select', '', false, false));
                        var res = data.centers.map(function(item) {
                            let option = new Option(item.name, item.id, false, false)
                            $("#center").append(option)
                        });
                    }
                });

                refreshTable();
            });

            $("#center").change(function() {
                refreshTable();
            });

            $("#routeCustomersDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('route-customers.index') !!}',
                    data: function(data) {
                        data.branch = $("#branch").val();
                        data.route = $("#route").val();
                        data.center = $("#center").val();
                    }
                },
                columns: [{
                    data: 'created_at',
                    name: 'created_at'
                }, {
                    data: 'route.route_name',
                    name: 'route.route_name'
                }, {
                    data: 'center.name',
                    name: 'center.name'
                }, {
                    data: 'bussiness_name',
                    name: 'bussiness_name'
                }, {
                    data: 'name',
                    name: 'name'
                }, {
                    data: 'phone',
                    name: 'phone'
                }, {
                    data: 'status',
                    name: 'status'
                }, {
                    data: 'actions',
                    name: 'actions',
                    className: 'text-center',
                    searchable: false,
                    orderable: false
                }, ],
            });
        });


        function refreshTable() {
            $("#routeCustomersDataTable").DataTable().ajax.reload();
        }

        function submitExportForm() {
            document.getElementById('export-customer-form').submit();
            $('#export-customer-modal').modal('hide');
            form.reset();
        }
    </script>
@endpush
