@extends('layouts.admin.admin')
@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Bank Error Logs </h3>
                    <div class="d-flex">
                        
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div>
                            <label for="">Selected Dates</label>
                            <input type="hidden" id="startDate" name="from">
                            <input type="hidden" id="endDate" name="to">
                            <div class="reportRange">
                                <i class="fa fa-calendar" style="padding:8px"></i>
                                <span class="flex-grow-1" style="padding:8px 5px"></span>
                                <i class="fa fa-caret-down" style="padding:8px"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>

                <div class="col-md-12 no-padding-h" id="getintervalview">
                    <table class="table table-bordered" id="statementsDataTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Reference</th>
                                <th>Channel</th>
                                <th>Status</th>
                                <th>Reason</th>
                                <th>Amount</th>
                            </tr>
                        </thead>                        
                    </table>
                </div>
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
        let start = moment().subtract(3, 'days');
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
            $("#statementsDataTable").DataTable().ajax.reload();
        });

        $("#statementsDataTable").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                pageLength: 100,
                ajax: {
                    url: '{!! route('bank-error-logs') !!}',
                    data: function(data) {
                        data.start_date = $("#startDate").val();
                        data.end_date = $("#endDate").val();
                    }
                },
                columns: [
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'statement.reference',
                        name: 'statement.reference'
                    },
                    {
                        data: 'statement.channel',
                        name: 'statement.channel'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'reason',
                        name: 'reason'
                    },
                    {
                        data: 'statement.amount',
                        name: 'statement.amount'
                    },
                ], 
                columnDefs: [
                ],
            });

        });
       
            </script>
@endpush