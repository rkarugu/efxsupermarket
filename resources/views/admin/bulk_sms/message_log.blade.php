@extends('layouts.admin.admin')
@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Message Log </h3>
                    <div class="d-flex">
                    
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="row">
                    <div class="col-sm-12 d-flex" style="margin: 10px 0">     
                        <div class="form-group" style="margin-left:10px;">
                            <label for="start_date"> Date</label>
                            <input type="hidden" id="startDate" name="start_date">
                            <input type="hidden" id="endDate" name="end_date">
                            <div class="reportRange">
                                <i class="fa fa-calendar" style="padding:8px"></i>
                                <span class="flex-grow-1" style="padding:8px"></span>
                                <i class="fa fa-caret-down" style="padding:8px"></i>
                            </div>
                        </div>
                                           
                            <div class="form-group" style="margin-left:10px;">
                                <label for="delivery_status">Delivery Status</label>
                                <select class="form-control mtselect" name="delivery_status" id="delivery_status">
                                    <option value="">Choose Delivery Status</option>
                                    <option value="1">Sent</option>
                                    <option value="0">Fail</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-left:10px;">
                                <label for="issn">ISSN</label>
                                <select class="form-control mtselect" name="issn" id="issn">
                                    <option value="">Choose ISSN</option>
                                    <option value="{{ env("KANINI_SMS_SENDER_ID_2") }}">{{ env("KANINI_SMS_SENDER_ID_2") }}</option>
                                    <option value="{{ env("AIRTOUCH_ISSN") }}">{{ env("AIRTOUCH_ISSN") }}</option>
                                    <option value="{{ env("KANINI_SMS_SENDER_ID") }}">{{ env("KANINI_SMS_SENDER_ID") }}</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-left:10px;">
                                <label for="category">Category</label>
                                <select class="form-control mtselect" name="category" id="category">
                                    <option value="">Choose Category</option>
                                    <option value="Test SMS">Test SMS</option>
                                    <option value="Bulk SMS">Bulk SMS</option>
                                </select>
                            </div>
                            <div style="margin-left:10px; margin-top:25px; display:flex;">
                                {{-- <button type="button" class="btn btn-primary" name="action" id="generateExcelBtn" style="height: 35px;" title="Print Excel">
                                    <i class="fa fa-file-alt"></i>
                                </button> --}}
                                <button type="button" class="btn btn-primary" name="action" id="generatePDFBtn" style="margin-left:10px;height: 35px;" title="Print Pdf">
                                    <i class="fa fa-file"></i>
                                </button>                                
                            </div>
                            <div></div>
                    </div>
                </div>
                <hr>
                
                <div class="col-md-12 no-padding-h" id="getintervalview">
                    <table class="table table-bordered" id="logDataTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Title</th>
                                <th>Branch</th>
                                <th>Send Group</th>
                                <th>Recipients</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('uniquepagestyle')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />
<style>
    .select2.select2-container.select2-container--default
    {
        width: 100% !important;
    }
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
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
    $(document).ready(function() {
        $('body').addClass('sidebar-collapse');
        $('.mtselect').select2();
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

                refreshStatementTable();
            });

        $('#delivery_status, #issn, #category, #startDate, #end_date').on('change', function() {
            $("#logDataTable").DataTable().ajax.reload();
        });
        $('#generateExcelBtn').on('click',function(){
            location.href=`/admin/bulk-sms/message-log?type=excel&delivery_status=`+$("#delivery_status").val()+`&issn=`+$("#issn").val()+`&category=`+$("#category").val()+`&start_date=`+$("#startDate").val()+`&end_date=`+$("#endDate").val();
        });
        $('#generatePDFBtn').on('click',function(){
            location.href=`/admin/bulk-sms/message-log?type=pdf&delivery_status=`+$("#delivery_status").val()+`&issn=`+$("#issn").val()+`&category=`+$("#category").val()+`&start_date=`+$("#startDate").val()+`&end_date=`+$("#endDate").val();
        });
        $("#logDataTable").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                pageLength: 100,
                ajax: {
                    url: '{!! route('bulk-sms.message-log') !!}',
                    data: function(data) {
                        data.delivery_status = $("#delivery_status").val();
                        data.issn = $("#issn").val();
                        data.category = $("#category").val();
                        data.start_date = $("#startDate").val();
                        data.end_date = $("#endDate").val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'date',
                        name: 'date',
                    },
                    {
                        data: 'time',
                        name: 'time',
                    },                    {
                        data: 'title',
                        name: 'title',
                    },
                    {
                        data: 'branch.name',
                        name: 'branch.name',
                    },
                    {
                        data: 'send_group',
                        name: 'send_group'
                    },
                    {
                        data: 'recipients',
                        name: 'recipients'
                    },
                    {
                        data: null,
                        name: null
                    }
                ],
                columnDefs: [
                    {
                        targets: -1,
                        render: function (data, type, row, meta) {
                            if (type === 'display') {
                                var actions = '';
                                var actionUrl = "{{ route('bulk-sms.message-log.view', ['id']) }}";
                                actionUrl = actionUrl.replace('id', row.id);
                                actions += `<a href="`+actionUrl+`" role="button" title="Edit"><i class="fa fa-solid fa-eye"></i></a>`;
                                    
                                return actions;
                            }
                            return data;
                        }
                    }
                ],
            });

           
        });
        function refreshStatementTable() {
            console.log('clicked');
            $("#logDataTable").DataTable().ajax.reload();
        }
    </script>
@endsection
