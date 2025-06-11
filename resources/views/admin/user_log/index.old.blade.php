@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> User Logs </h3>
            </div>
            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                <form method="GET" action="{{ route("userlogs.index") }}">
                    <input type="hidden" id="startDate" name="from">
                    <input type="hidden" id="endDate" name="to">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="row">
                                    <label for="reportRange" class="col-sm-4">Select Dates</label>
                                    <div id="reportRange" class="col-sm-8 reportRange">
                                        <i class="fa fa-calendar" style="padding:8px"></i>
                                        <span class="flex-grow-1" style="padding:8px">Select Dates</span>
                                        <i class="fa fa-caret-down" style="padding:8px"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-body">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover" id="userLogsDataTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Timestamp</th>
                            <th>IP Address</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Branch</th>
                            <th>Department</th>
                            <th>Activity</th>
                        </tr>
                        </thead>                        
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />
    <style>
        .reportRange {
            display: flex;
            align-content: center;
            justify-content: stretch;
            border: 1px solid #eee;
            cursor: pointer;
            height: 35px;
        }
    </style>
@endpush
@push('scripts')
    <script type="text/javascript" src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}">
    </script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            let start = moment();
            let end = moment();

            $('.reportRange').daterangepicker({
                startDate: start,
                endDate: end,
                alwaysShowCalendars: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(7, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            });
            
            $('.reportRange').on('apply.daterangepicker', function(ev, picker) {
                $("#startDate").val(picker.startDate.format('YYYY-MM-DD'));
                $("#endDate").val(picker.endDate.format('YYYY-MM-DD'));

                $('.reportRange span').html(picker.startDate.format('MMM D, YYYY') + ' - ' + picker.endDate
                    .format('MMM D, YYYY'));

                refreshTable();
            });
        })

        $("#userLogsDataTable").DataTable({
            processing: true,
            serverSide: true,
            order: [
                [1, "asc"]
            ],
            autoWidth: false,
            pageLength: '<?= Config::get('params.list_limit_admin') ?>',
            ajax: {
                url: '{!! route("userlogs.index") !!}',
                data: function(data) {
                    data.from = $("#startDate").val();
                    data.to = $("#endDate").val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    sortable: false,
                    width: "70px"
                },
                {
                    data: 'created_at',
                    name: 'user_logs.created_at',
                },
                {
                    data: 'ip_address',
                    name: 'user_logs.user_ip',
                },
                {
                    data: 'user',
                    name: 'users.name',
                },
                {
                    data: 'role',
                    name: 'roles.title',
                },
                {
                    data: 'restaurant',
                    name: 'restaurants.name',
                },
                {
                    data: 'department_name',
                    name: 'wa_departments.department_name',
                },
                {
                    data: 'activity',
                    name: 'activity',
                }
            ],
        });

        function refreshTable() {
            $("#userLogsDataTable").DataTable().ajax.reload();
        }
    </script>
@endpush

