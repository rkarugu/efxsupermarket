@extends('layouts.admin.admin')

@section('content')
<section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title" style="font-weight:500 !important;"> Activity Log </h3>
                    
                </div>
            </div>        
            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div>
                            <input type="hidden" id="startDate" name="from">
                            <input type="hidden" id="endDate" name="to">
                            <div class="reportRange">
                                <i class="fa fa-calendar" style="padding:8px"></i>
                                <span class="flex-grow-1" style="padding:8px 5px"></span>
                                <i class="fa fa-caret-down" style="padding:8px"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="model" id="model" class="form-control mtselect">
                            <option value="all" selected>Choose Model</option>
                            @foreach($models as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="causer" id="causer" class="form-control mtselect">
                            <option value="all" selected>Choose User</option>
                            @foreach ($users as $user)
                                <option value="{{$user->id}}">{{$user->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="description" id="description" class="form-control mtselect">
                            <option value="all" selected>Choose Action</option>
                            @foreach ($descriptions as $description)
                                <option value="{{$description}}">{{$description}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button id="userActivityBtn" class="btn btn-primary">User Activity</button>
                    </div>
                      
                </div>
                <hr>
                <table class="table table-striped" id="activityDataTable">
                    <thead>
                    <tr>
                        <th>Description</th>
                        <th>Model</th>
                        <th>Model ID</th>
                        <th>Subject</th>
                        <th>Causer</th>
                        <th>IP Address</th>
                        <th>User Agent</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
@push('styles')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
<link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
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
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(document).ready(function() {
        $('body').addClass('sidebar-collapse');
        let start = moment();
        let end = moment();

        $('.reportRange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
        $("#startDate").val(start.format('YYYY-MM-DD'));
        $("#endDate").val(end.format('YYYY-MM-DD'));

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
            console.log('change');
            $("#activityDataTable").DataTable().ajax.reload();
        });
        
        $('#userActivityBtn').attr('disabled','disabled');
        $('.mtselect').select2();
        $('#userActivityBtn').on('click', function (e) {
            let id = $("#causer").val();
            var url = "{{ route('activitylogs.user_activity',':id') }}";
            url = url.replace(':id', id);
            console.log(url);
            location.href=url;
        });
        $('#causer').on('change', function() {  
           let causer = $(this).val();
           if (causer == 'all') {
            $('#userActivityBtn').attr('disabled','disabled');
           } else{
            $('#userActivityBtn').removeAttr('disabled');
           }
           
        });
        $('#causer, #date, #model, #description').on('change', function() {  
            $("#activityDataTable").DataTable().ajax.reload();
        });
        $('.completedGrnsReportRange').on('apply.daterangepicker', function(ev, picker) {
            $("#completedGrnsStartDate").val(picker.startDate.format('YYYY-MM-DD'));
            $("#completedGrnsEndDate").val(picker.endDate.format('YYYY-MM-DD'));

            $('.completedGrnsReportRange span').html(picker.startDate.format('MMM D, YYYY') + ' - ' +
                picker.endDate
                .format('MMM D, YYYY'));

                $("#activityDataTable").DataTable().ajax.reload();
        });
        $("#activityDataTable").DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            pageLength: 100,
            ajax: {
                url: '{!! route('activitylogs.datatable') !!}',
                data: function(data) {
                    data.causer = $("#causer").val();
                    data.from = $("#startDate").val();
                    data.to = $("#endDate").val();
                    data.model = $("#model").val();
                    data.description = $("#description").val();
                }
            },
            columns: [{
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'subject_type',
                    name: 'subject_type'
                },
                {
                    data: 'subject_id',
                    name: 'subject_id'
                },
                {
                    data: 'subject_name',
                    name: 'subject_name'
                },
                {
                    data: 'causer.name',
                    name: 'causer.name',
                    searchable: false,
                },
                {
                    data: 'properties.ip',
                    name: 'properties.ip',
                    searchable: false,
                },
                {
                    data: 'properties.user_agent',
                    name: 'properties.user_agent',
                    searchable: false,
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false
                }
                
            ],
            columnDefs: [
                {
                    targets: -1,
                    render: function (data, type, row, meta) {
                        if (type === 'display') {
                                var url = "{{ route('activitylogs.show',':id') }}";
                                url = url.replace(':id', row.id);
                                actions = `<a href="`+url+`" title="view"><i class="fa fa-solid fa-eye"></i></a>`;
                            // var actions = `<a href="/admin/activity-logs/`+row.id+`" class=" text-primary" title="View"><i class="fa fa-eye"></i></a>`;
                            return actions;
                        }
                        return data;
                    }
                }
            ],
        });
        });
    </script>
@endpush
