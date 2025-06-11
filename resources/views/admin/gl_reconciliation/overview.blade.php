@extends('layouts.admin.admin')
@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> GL Reconciliation Overview</h3>
                    <div class="d-flex">
                        
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                <div style="padding:10px">
                        <div class="row">
                            <div class="col-sm-3">
                                <input type="hidden" id="startDate" name="from">
                                <input type="hidden" id="endDate" name="to">
                                    <label class="">Select Dates</label>
                                    <div class="reportRange ">
                                        <i class="fa fa-calendar" style="padding:8px"></i>
                                        <span class="flex-grow-1" style="padding:8px"></span>
                                        <i class="fa fa-caret-down" style="padding:8px"></i>
                                    </div>
                            </div>
                            <div class="col-md-3">
                                @php
                                    $account_codes =  getChartOfAccountsList();
                                @endphp
                                <label class="">Account</label>
                                <select name="account" id="account" class="form-control select2">
                                    <option value="all" selected>Choose Account</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{$account->account_code}}"> {{$account_codes[$account->account_code]}} ({{$account->account_code}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                </div>

                <div class="col-md-12 no-padding-h" id="getintervalview">
                    <table class="table table-bordered" id="overviewDataTable">
                        <thead>
                            <tr>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Account</th>
                                <th>Beginning Balance</th>
                                <th>Ending Balance</th>
                                <th>Matched</th>
                                <th>Missing Trans.</th>
                                <th>Unknown Bankings</th>
                                <th>Variance</th>
                                <th>Action</th>
                            </tr>
                        </thead>                        
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('uniquepagestyle')
<link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
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
@endsection
@section('uniquepagescript')
<script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
    $(document).ready(function() {
        $('body').addClass('sidebar-collapse');
        $('.select2').select2();
        let start = moment().subtract(30, 'days');
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

                    $("#overviewDataTable").DataTable().ajax.reload();
            });

        $('#startDate, #endDate, #account').on('change', function() {
            let start_date = $("#startDate").val();
            let end_date = $("#endDate").val();
            $("#startDate").val(start_date);
            $("#endDate").val(end_date);
            $("#overviewDataTable").DataTable().ajax.reload();
        });
       
        $("#overviewDataTable").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                pageLength: 100,
                ajax: {
                    url: '{!! route('gl-reconciliation.overview') !!}',
                    data: function(data) {
                        data.start_date = $("#startDate").val();
                        data.end_date = $("#endDate").val();
                        data.account = $("#account").val();
                    }
                },
                columns: [
                    {
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        data: 'end_date',
                        name: 'end_date'
                    },
                    {
                        data: 'bank_account.account_name',
                        name: 'bankAccount.account_name',
                    },
                    {
                        data: 'beginning_balance',
                        name: 'beginning_balance'
                    },
                    {
                        data: 'ending_balance',
                        name: 'ending_balance'
                    },
                    {
                        data: 'matched',
                        name: 'matched'
                    },
                    {
                        data: 'missing_trans',
                        name: 'missing_trans'
                    },
                    {
                        data: 'unknown_bankings',
                        name: 'unknown_bankings'
                    },
                    {
                        data: 'variance',
                        name: 'variance'
                    },
                    {
                        data:null,
                        name:null,
                    }
                    
                ],
                columnDefs: [
                    {
                        targets: -1,
                        render: function (data, type, row, meta) {
                            if (type === 'display') {
                                var actions = '';
                                @if (can('show',$model))                                   
                                    var url = "{{ route('gl-reconciliation.view',':id') }}";
                                    url = url.replace(':id', row.id);
                                    actions += `<a href="`+url+`" title="view"><i class="fa fa-solid fa-eye"></i></a>`;
                                @endif
                                return actions;
                            }
                            return data;
                        }
                    }
                ],
            });
        });
            </script>
@endsection