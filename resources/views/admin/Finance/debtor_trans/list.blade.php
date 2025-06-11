@extends('layouts.admin.admin')
@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Debtor Transactions </h3>
                    <div class="d-flex">
                    
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="row">
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
                                    <option value="{{ $channel->title }}" {{ request()->channel == $channel->title ? 'selected' : '' }}>
                                        {{ $channel->title }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" style="margin-left:10px;">
                                <label for="status">Status</label>
                                <select class="form-control mtselect" name="status" id="status">
                                    <option value="all">Choose Status</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Verified">Verified</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Duplicate">Duplicate</option>
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
                <hr>
                
                <div class="col-md-12 no-padding-h" id="getintervalview">
                    <table class="table table-bordered" id="debtorsDataTable">
                        <thead>
                            <tr>
                                <th>Trans Date</th>
                                <th>Document No</th>
                                <th>Channel</th>
                                <th>Branch</th>
                                <th>Route</th>
                                <th>Reference</th>
                                <th>Verification</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5"></th>
                                <th style="text-align: right;" colspan="2">Total:</th>
                                <th id="debtorsTotal"
                                    style="text-align: left; border-top: 1px solid #000;border-bottom: 1px solid #000;">
                                </th>
                                <th></th>
                            </tr>
                        </tfoot>
                        
                    </table>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="editReferenceModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title" id="editReferenceModalTitle"> Edit Reference</h3>
    
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form id="updateEditReferenceForm" action="" method="POST">
                    @csrf
                    <div class="box-body">
                        <div class="row">
                            {{-- <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="channel" class="control-label"> Channel </label>
                                    <select name="channel" id="editChannelVal" class="form-control mtselect" required>
                                        <option value="" selected disabled>--Select Channel--</option>
                                        @foreach ($channels as $channel)
                                            <option value="{{$channel}}" {{ request()->channel == $channel ? 'selected' : '' }}>{{$channel}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="editReferenceVal">Reference</label>
                                    <textarea name="reference" id="editReferenceVal" class="form-control" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" id="confirmEditReferenceBtn" class="btn btn-primary" data-dismiss="modal">Edit Reference</button>
    
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateMispostModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title" id="updateMisposteModalTitle"> Edit Channel</h3>
    
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form id="updateupdateMispostForm" action="" method="POST">
                    @csrf
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group row">
                                    <div class="col-sm-4"><label for="channel" class="control-label">Current Channel </label></div>
                                    <div class="col-sm-8"><input type="text" id="current_channel" class="form-control" disabled></div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group row">
                                    <div class="col-sm-4"><label for="channel" class="control-label"> Channel </label></div>
                                    <div class="col-sm-8">
                                        <select name="channel" id="editChannelVal" class="form-control mtselect" required>
                                            <option value="" selected disabled>--Select Channel--</option>
                                            @foreach ($channels as $channel)
                                                <option value="{{$channel->title}}" {{ request()->channel == $channel->title ? 'selected' : '' }}>{{$channel->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <input type="hidden" name="transaction" id="update_channel_id" >
                            <button type="submit" id="confirmUpdateMispostBtn" class="btn btn-primary">Edit Channel</button>
    
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
@endsection
@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

<script>
    $(document).ready(function() {
        $('body').addClass('sidebar-collapse');
        $('.mtselect').select2();
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
                    url: '{!! route('debtor-trans.datatable') !!}',
                    data: function(data) {
                        data.status = $("#status").val();
                        data.channel = $("#channel").val();
                        data.branch = $("#branch").val();
                        data.route = $("#route").val();
                        data.start_date = $("#start_date").val();
                        data.end_date = $("#end_date").val();
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
                        data: 'verification_status',
                        name: 'verification_status'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: null,
                        orderable: false
                    },
                ],
                columnDefs: [
                    {
                        targets: -1,
                        render: function (data, type, row, meta) {
                            if (type === 'display') {
                                var actions = '';
                                @if (can('edit-debtor-reference', 'reconciliation'))
                                    if (row.verification_status == 'Pending') {
                                        actions += `<a onclick="editReferenceModal('`+row.id+`','`+row.reference+`','`+row.channel+`')" role="button" title="Edit"><i class="fa fa-solid fa-edit"></i></a>`;
                                    }
                                @endif
                                @if (can('update-mispost-channel', 'reconciliation'))
                                    if (row.verification_status != 'Approved') {
                                        actions += `<a onclick="updateMispostModal('`+row.id+`','`+row.channel+`')" role="button" title="Mispost Channel" style="margin-left:10px;"><i class="fa fa-solid fa-signs-post"></i></a>`;
                                    }
                                @endif
                                return actions;
                            }
                            return data;
                        }
                    }
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#debtorsTotal").text(json.total_amount);
                }
            });

            $('#confirmEditReferenceBtn').on('click', function (e) {
                e.preventDefault();
                $('#updateEditReferenceForm').get(0).submit();
                
            });
            $('#confirmUpdateMispostBtn').on('click', function (e) {
                e.preventDefault();
                $('#updateupdateMispostForm').get(0).submit();
                
            });
        });

        function editReferenceModal(id,reference,channel)
        {
            $('#editReferenceModalTitle').html('Edit Reference \''+reference+' \'.');
            var actionUrl = "{{ route('payment-reconciliation.verification.edit-reference', ['trans_id']) }}";
            actionUrl = actionUrl.replace('trans_id', id);
            $('#editReferenceVal').val(reference);
            $('#editChannelVal').val(channel).change();
            $('#updateEditReferenceForm').attr('action', actionUrl);
            $('#editReferenceModal').modal();
        }
        
        function updateMispostModal(id,channel)
        {
            $('#updateMispostModalTitle').html('Edit Miispost Channel');
            var actionUrl = "{{ route('transaction-mispost.store-single') }}";
            $('#editChannelVal').val(channel).change();
            $('#current_channel').val(channel);
            $('#update_channel_id').val(id);
            $('#updateupdateMispostForm').attr('action', actionUrl);
            $('#updateMispostModal').modal();
        }
        </script>
@endsection
