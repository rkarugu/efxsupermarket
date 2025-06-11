@extends('layouts.admin.admin')
@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title">Approve Manual Uploads </h3>
                    <div class="d-flex">
                    
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="row" style="display: none">
                    <div class="col-sm-12 d-flex justify-content-end" style="margin: 10px 0">     
                        <div class="form-group" style="margin-left:10px;">
                            <label for="start_date">Start Date</label>
                            <input type="date" id="start_date" name="start_date" class="form-control">
                        </div>
                        <div class="form-group" style="margin-left:10px;">
                            <label for="end_date">End Date</label>
                            <input type="date" id="end_date" name="end_date" class="form-control">
                        </div>                       
                            <div class="form-group" style="margin-left:10px;">
                                <label for="branch">Branch</label>
                                <select class="form-control mtselect" name="branch" id="branch">
                                    <option value="all">Choose Branch</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{$branch->id}}">{{$branch->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" style="margin-left:10px;">
                                <label for="channel">Channel</label>
                                <select class="form-control mtselect" name="channel" id="channel">
                                    <option value="all">Choose Channel</option>
                                    @foreach ($channels as $channel)
                                        <option value="{{$channel}}">{{$channel}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" style="margin-left:10px;">
                                <label for="status">Status</label>
                                <select class="form-control mtselect" name="status" id="status">
                                    <option value="all">Choose Status</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Approved">Approved</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-left:10px;">
                                <label for="route">Route</label>
                                <select class="form-control mtselect" name="route" id="route">
                                    <option value="all">Choose Route</option>
                                    @foreach (getCustomerDropdowns() as $key => $item)
                                        <option value="{{$key}}">{{$item}}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div style="margin-left:10px; margin-top:25px; display:flex;">
                                <button type="button" class="btn btn-primary" name="action" id="generateExcelBtn" style="height: 35px;" title="Print Excel">
                                    <i class="fa fa-file-alt"></i>
                                </button>
                                <button type="button" class="btn btn-primary" name="action" id="generatePDFBtn" style="margin-left:10px;height: 35px;" title="Print Pdf">
                                    <i class="fa fa-file"></i>
                                </button>                                
                            </div>
                    </div>
                </div>
                
                <div class="col-md-12 no-padding-h" id="getintervalview">

                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#pending" data-toggle="tab">Pending</a></li>
                        <li><a href="#approved" data-toggle="tab">Approved</a></li>
                    </ul>
    
                    <div class="tab-content">
                        <div class="tab-pane active" id="pending">
                            <div class="box-body">
                                <table class="table table-bordered" id="pendingDataTable">
                                    <thead>
                                        <tr>
                                            <th>Trans Date</th>
                                            <th>Document No</th>
                                            <th>Channel</th>
                                            <th>Branch</th>
                                            <th>Route</th>
                                            <th>Reference</th>
                                            <th class="">Amount</th>
                                            @if (can('approve-manual-upload', 'reconciliation'))
                                            <th>Action</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $total=0;
                                        @endphp
                                        @foreach ($pendings as $item)
                                            <tr>
                                                <td>{{$item->trans_date}}</td>
                                                <td>{{$item->document_no}}</td>
                                                <td>{{$item->channel}}</td>
                                                <td>{{$item->branch_name}}</td>
                                                <td>{{$item->customer_name}}</td>
                                                <td>{{$item->reference}}</td>
                                                <td>
                                                    @php
                                                        $total+=$item->amount;
                                                    @endphp
                                                    {{manageAmountFormat(abs($item->amount))}}
                                                </td>
                                                @if (can('approve-manual-upload', 'reconciliation'))
                                                <td>
                                                    <button type="button" class="btn btn-success" onclick="approveBtn({{$item->id}})" style="margin-left:10px;border-radius: 0px;padding: 4px 7px;font-size: 12px;font-weight: 600;" ><i class="fa fa-solid fa-thumbs-up"></i></button>
                                                </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4"></th>
                                            <th style="text-align: right;" colspan="2">Total:</th>
                                            <th id=""
                                                style="text-align: left; border-top: 1px solid #000;border-bottom: 1px solid #000;">
                                                {{ manageAmountFormat(abs($total)) }}
                                            </th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                    
                                </table>
                            </div>
                        </div>
    
                        <div class="tab-pane" id="approved">
                            <div class="box-body">
                                <div class="row" style="margin:5px 0;">
                                    <div class="col-sm-5">
                                        <input type="hidden" id="startDate" name="from">
                                        <input type="hidden" id="endDate" name="to">
                                        <div class="row">
                                            <label class="col-sm-4">Select Dates</label>
                                            <div class="reportRange col-sm-8">
                                                <i class="fa fa-calendar" style="padding:8px"></i>
                                                <span class="flex-grow-1" style="padding:8px"></span>
                                                <i class="fa fa-caret-down" style="padding:8px"></i>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- @if (can('print', 'customer-centre'))
                                        <div class="col-sm-4">
                                            <button type="submit" class="btn btn-primary" name="manage-request" value="pdf">Print
                                                Pdf</button>
                                        </div>
                                    @endif --}}
                                </div>
                                <table class="table table-bordered" id="debtorsDataTable">
                                    <thead>
                                        <tr>
                                            <th>Trans Date</th>
                                            <th>Document No</th>
                                            <th>Channel</th>
                                            <th>Branch</th>
                                            <th>Route</th>
                                            <th>Reference</th>
                                            <th>Approved By</th>
                                            <th>Verification</th>
                                            <th class="">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="6"></th>
                                            <th style="text-align: right;" colspan="2">Total:</th>
                                            <th id="debtorsTotal"
                                                style="text-align: left; border-top: 1px solid #000;border-bottom: 1px solid #000;">
                                            </th>
                                        </tr>
                                    </tfoot>
                                    
                                </table>
                            </div>
                        </div>
    
                    </div>
                    
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="confirmApproveModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title" id="approveModalTitle"> Approve Transactions</h3>
    
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form id="updateApproveForm" action="" method="POST">
                    @csrf
                    <div class="box-body">
                        Are you sure You want to Approve this Transaction?
                    </div>
    
                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <input type="hidden" name="" id="">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" id="confirmApproveBtn" class="btn btn-primary" data-id="0" data-dismiss="modal">Approve</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('uniquepagestyle')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
<style>
    .select2.select2-container.select2-container--default
    {
        width: 100% !important;
    }

</style>
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
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $(document).ready(function() {
        $('body').addClass('sidebar-collapse');
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
        
        $('.mtselect').select2();
        $('#pendingDataTable').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                'autoWidth': false,
                'pageLength': 100,
                'aoColumnDefs': [{
                    'bSortable': false,
                    'aTargets': 'noneedtoshort'
                }],
            });
        $('#status, #channel, #route, #branch, #start_date, #end_date').on('change', function() {
            $("#debtorsDataTable").DataTable().ajax.reload();
        });
        $('#generateExcelBtn').on('click',function(){
            location.href=`/admin/debtor-trans/datatable?type=excel&status=`+$("#status").val()+`&channel=`+$("#channel").val()+`&branch=`+$("#branch").val()+`&route=`+$("#route").val()+`&start_date=`+$("#start_date").val()+`&end_date=`+$("#end_date").val();
        });
        $('#generatePDFBtn').on('click',function(){
            location.href=`/admin/debtor-trans/datatable?type=pdf&status=`+$("#status").val()+`&channel=`+$("#channel").val()+`&branch=`+$("#branch").val()+`&route=`+$("#route").val()+`&start_date=`+$("#start_date").val()+`&end_date=`+$("#end_date").val();
        });
        $("#debtorsDataTable").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                pageLength: 100,
                ajax: {
                    url: '{!! route('manual-upload-list') !!}',
                    data: function(data) {
                        data.start_date = $("#startDate").val();
                        data.end_date = $("#endDate").val();
                    }
                },
                columns: [{
                        data: 'trans_date',
                        name: 'trans_date'
                    },
                    {
                        data: 'document_no',
                        name: 'document_no'
                    },
                    {
                        data: 'channel',
                        name: 'channel'
                    },
                    {
                        data: 'branch_name',
                        name: 'restaurants.name'
                    },
                    {
                        data: 'customer_name',
                        name: 'wa_customers.customer_name'
                    },
                    {
                        data: 'reference',
                        name: 'reference'
                    },
                    {
                        data: 'approved_by',
                        name: 'users.name'
                    },
                    {
                        data: 'verification_status',
                        name: 'verification_status'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    }
                ],
                
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#debtorsTotal").text(json.total_amount);
                }
            });

            $('#confirmApproveBtn').on('click', function (e) {
                e.preventDefault();           
                var postData = { 
                    transaction: $(this).data('id')
                };

                $.ajax({
                    url: "{{route('manual-update-status')}}", 
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                    },
                    data: postData, 
                    success: function(response) {
                        location.reload(true);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });

            });

            $('#confirmEditReferenceBtn').on('click', function (e) {
                e.preventDefault();
                $('#updateEditReferenceForm').get(0).submit();
                
            });
        });
        function approveBtn(id)
        {
                $('#confirmApproveBtn').data('id',id);
                $('#confirmApproveModal').modal();
        }
        function refreshStatementTable() {
            $("#debtorsDataTable").DataTable().ajax.reload();
        }
            </script>
@endsection
