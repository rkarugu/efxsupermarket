
@extends('layouts.admin.admin')
@section('content')
<div class=" multistep">
    <div class="container">
        <div class="stepwizard">
            <div class="stepwizard-row setup-panel">
                <div class="stepwizard-step col-xs-3"> 
                    <a href="#step-1" type="button" class="btn btn-success btn-circle step-buttons step-buttons1">1</a>
                    <p><b>Reconcile </b></p>
                </div>
                <div class="stepwizard-step col-xs-3"> 
                    <a href="#step-2" type="button" class="btn btn-default btn-circle step-buttons step-buttons2" disabled="disabled">2</a>
                    <p><b>Verify</b></p>
                </div>
                <div class="stepwizard-step col-xs-3"> 
                    <a href="#step-3" type="button" class="btn btn-default btn-circle step-buttons step-buttons3" disabled="disabled">3</a>
                    <p><b>Approve</b></p>
                </div>
            </div>
        </div>
    </div>
    <form class="validate"  role="form" method="POST" action="{!! route('banking.reconcile.daily.transactions.store') !!}" enctype = "multipart/form-data">
        @csrf
        <section class="content setup-content" id="step-1">    
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"> Initiate Reconciliation  </h3>
                    <div class="col-md-12 no-padding-h table-responsive">                             
                            <div class="row">                                            
                                <div class="col-md-3 form-group">
                                    <label for="">Choose Date</label>
                                    <input type="date" name="date" id="date" class="form-control">
                                </div>
                                <div class="col-md-3 form-group">
                                    <label for="">Channel</label>
                                    <select name="channel" id="channel" class="form-control mlselec6t">
                                        <option value="" selected disabled>--Select Channel--</option>
                                        <option value="Eazzy" {{ request()->channel == 'Eazzy' ? 'selected' : '' }}>
                                            Eazzy
                                        </option>
                                        <option value="Vooma {{ request()->channel == 'Vooma' ? 'selected' : '' }}">
                                            Vooma
                                        </option>
                                        <option value="Mpesa {{ request()->channel == 'Mpesa' ? 'selected' : '' }}">
                                            Mpesa
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label for="">Branch</label>
                                    <select name="branch" id="branch" class="form-control mlselec6t">
                                        <option value="" selected disabled>--Select Branch--</option>
                                        @foreach(getBranchesDropdown() as $key => $branch)
                                        <option value="{{$key}}">{{$branch}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 ">
                                    <br>
                                    <button type="button" class="btn btn-primary" onclick="searchData()">Search</button>
                                </div>
                            </div>
                    </div>
                </div>
                @include('message')
                    {{ csrf_field() }}
                    <div class="box">
                        <div class="box-body">
                            <table class="table table-striped" id="dailyReconDataTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Branch</th>
                                        <th>Document No.</th>
                                        <th>Reference</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th class="text-right" colspan="5">Total</th>
                                        <th class="text-right" id="total"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="box-footer">
                    <button type="submit" class="btn btn-primary submitMe" name="current_step" value="1"  style="float: right;">Next</button>
                    </div>
            </div>
            
        </section>

        <section class="content setup-content" id="step-2">    
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">  Upload File </h3></div>
                <div class="col-md-12 no-padding-h table-responsive">                             
                    <form id="formUpload" enctype="multipart/form-data">
                        @csrf
                        <div class="row">                                            
                            <div class="col-md-3 form-group">
                                <label for="" id="channel-upload">Upload Data</label>
                                <input type="file" name="channel_file_upload" id="fileUpload" class="form-control">
                                <p id="fileName"></p>
                            </div>
                            
                            <div class="col-md-3 d-flex" style="justify-content: space-between;margin-top:25px;">
                                
                                <button type="button" class="btn btn-primary" id="reconcile-upload">Reconcile</button>
                            </div>
                            <div class="col-md-3 ">
                                
                            </div>
                        </div>
                    </form>
            </div>

                    <div class="box-footer">
                    <button type="button" class="btn btn-primary" style="float: left;" onclick="$('.step-buttons1').trigger('click'); return false;">Previous</button>
                    <button type="submit" class="btn btn-primary submitMe stepTwo" name="current_step" value="2" style="float: right;">Next</button>

                    </div>
            </div>
        </section>

        <section class="content setup-content" id="step-3">    
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"> Daily Transaction : Approve </h3>
                    <div>
                        <button type="button" class="btn btn-primary downloadPdf" data-id="downloadMissing"  style="float: right;margin-left:10px;">
                            <svg style="fill: #fff;height: 20px" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                              </svg> Missing
                        </button>
                        <button type="button" class="btn btn-primary downloadPdf" data-id="downloadDoubleEntries" style="float: right;margin-left:10px;">
                            <svg style="fill: #fff;height: 20px" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                              </svg>
                               Double Entries
                        </button>
                        <button type="button" class="btn btn-primary downloadPdf" data-id="downloadUnreconciled" style="float: right;margin-left:10px;">
                            <svg style="fill: #fff;height: 20px" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                              </svg> Unreconciled
                        </button>
                        <button type="button" class="btn btn-primary downloadPdf" data-id="downloadReconciled" style="float: right;margin-left:10px;">
                            <svg style="fill: #fff;height: 20px" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                              </svg> Reconciled
                        </button>
                    </div>
                </div>
                    <div class="col-md-12 no-padding-h">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#summary" data-toggle="tab"> Summary</a></li>
                            <li><a href="#reconciled_trans" data-toggle="tab">Reconciled Transactions</a></li>
                            <li><a href="#missing_trans" data-toggle="tab">Missing In System</a></li>
                            <li><a href="#hanging_trans" data-toggle="tab">Ignored Transactions</a></li>
                            <li><a href="#flagged_trans" data-toggle="tab">Flagged Transactions</a></li>
                        </ul>
    
                        <div class="tab-content">
                            <div class="tab-pane active" id="summary">
                                <div class="table-responsive box-body">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th>Uploaded Transactions</th>
                                            <th>System Transactions</th>
                                            <th>Variance</th>
                                            <th>Reconciled Transactions</th>
                                            <th>Variance (Against System)</th>
                                            <th>Missing Transactions</th>
                                            <th>Ignored Transactions</th>
                                            <th>Flagged Transactions</th>
                                        </tr>
                                        </thead>
    
                                        <tbody>
                                            <tr>
                                                <th scope="row" id="summ_uploaded"></th>
                                                <th scope="row" id="summ_system"></th>
                                                <th scope="row" id="summ_variance"></th>
                                                <th scope="row" id="summ_recon"></th>
                                                <th scope="row" id="summ_variance_ag"></th>
                                                <th scope="row" id="summ_missing"></th>
                                                <th scope="row" id="summ_ignored"></th>
                                                <th scope="row" id="summ_flagged"></th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
    
                            <div class="tab-pane" id="reconciled_trans">
                                <div class="table-responsive box-body">
                                    <div style="margin-bottom: 10px;clear: both;">
                                        <div style="float: right;">
                                            <button type="button" class="btn btn-primary" id="approveRecon"  style="margin-left:10px;">Approve</button> <br>
                                            <span id="approvalCheckError" style="color: red;font-size:12px;"></span>
                                        </div>
                                    </div>
                                    <table class="table table-bordered table-hover" id="reconciled_trans_table">
                                        <thead>
                                        <tr>
                                            <th style="width: 3%;"></th>
                                            <th>System Trans Date</th>
                                            <th>System Input Date</th>
                                            <th>Bank Date</th>
                                            <th>Route</th>
                                            <th>Reference</th>
                                            <th>Document No</th>
                                            <th>Amount</th>
                                            <th><input type="checkbox" id="checkAllRecon"></th>
                                        </tr>
                                        </thead>
    
                                        <tbody>
                                        
                                        </tbody>
    
                                        <tfoot>
                                        <tr>
                                            <th scope="row" colspan="7">RECONCILED TOTAL</th>
                                            <th scope="row" colspan="2" id="reconciled_trans_table_total">0</th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
    
                            <div class="tab-pane" id="missing_trans">
                                <div class="table-responsive box-body">
                                    <table class="table table-bordered table-hover" id="missing_trans_table">
                                        <thead>
                                        <tr>
                                            <th style="width: 3%;"></th>
                                            <th>Bank Date</th>
                                            <th>Reference</th>
                                            <th>Route</th>
                                            <th>Comments</th>
                                            <th>Amount</th>
                                        </tr>
                                        </thead>
    
                                        <tbody>
                                        
                                        </tbody>
    
                                        <tfoot>
                                        <tr>
                                            <th scope="row" colspan="5">MISSING TOTAL</th>
                                            <th scope="row" colspan="1" id="missing_trans_table_total">0</th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
    
                            <div class="tab-pane" id="hanging_trans">
                                <div class="table-responsive box-body">
                                    <table class="table table-bordered table-hover" id="ignored_trans_table">
                                        <thead>
                                        <tr>
                                            <th style="width: 3%;"></th>
                                            <th>Bank Date</th>
                                            <th>Reference</th>
                                            <th>Route</th>
                                            <th>Comments</th>
                                            <th>Amount</th>
                                        </tr>
                                        </thead>
    
                                        <tbody>
                                        
                                        </tbody>
    
                                        <tfoot>
                                        <tr>
                                            <th scope="row" colspan="5">IGNORED TOTAL</th>
                                            <th scope="row" colspan="1" id="ignored_trans_table_total">0</th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
    
                            <div class="tab-pane" id="flagged_trans">
                                <div class="table-responsive box-body">
                                    <table class="table table-bordered table-hover" id="flagged_trans_table">
                                        <thead>
                                        <tr>
                                            <th style="width: 3%;"></th>
                                            <th>Bank Date</th>
                                            <th>Reference</th>
                                            <th>Route</th>
                                            <th>Comments</th>
                                            <th>Amount</th>
                                        </tr>
                                        </thead>
    
                                        <tbody>
                                        
                                        </tbody>
    
                                        <tfoot>
                                        <tr>
                                            <th scope="row" colspan="5">FLAGGED TOTAL</th>
                                            <th scope="row" colspan="1" id="flagged_trans_table_total">0</th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="button" class="btn btn-primary" style="float: left;" onclick="$('.step-buttons2').trigger('click'); return false;">Previous</button>
                    </div>
            </div>
        </section>
</form>
</div>
@endsection

@section('uniquepagestyle')
<link rel="stylesheet" href="{{asset('css/multistep-form.css')}}">
<div id="loader-on" style="
position: fixed;
top: 0;
text-align: center;
display: block;
z-index: 999999;
width: 100%;
height: 100%;
background: #000000b8;
display:none;
" class="loder">
  <div class="loader" id="loader-1"></div>
</div>
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/multistep-form.js')}}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
  <script type="text/javascript">
    $(document).ready(function(e) {
            $('.mtselect').select2();
            var reconciledData;
            var reconciledDataTotal;
            var unreconciledData;
            var missingTransactionsData;
            var doubleEntriesData;
            var flaggedData;
            getData();
            $("#channel").change(function(){
                var selectedValue = $(this).val();
                $("#channel-upload").text('Upload '+selectedValue+' Form');
            });
            $('#fileUpload').change(function(){
                $('#reconcile-upload').prop('disabled', false);
                var fileName = $(this).val().split('\\').pop(); 
                $('#fileName').text(fileName); 
            });
            $('#checkAllRecon').on('click', function() {
                var isChecked = $(this).prop('checked');
                $('#reconciled_trans_table tbody .reconChecked').prop('checked', isChecked);
                $('#reconciled_trans_table').DataTable().rows().nodes().to$().find('.reconChecked').prop('checked', isChecked);
            });

            $('#reconciled_trans_table tbody').on('change', '.reconChecked', function() {
                $('#checkAllRecon').prop('checked', $('.reconChecked:checked').length === $('.reconChecked').length);
            });
            
            $('#reconcile-upload').click(function(){
                var formData = new FormData(); // Create FormData object
                var channelFile = $('input[name=channel_file_upload]').prop('files')[0];
                if (!channelFile) {
                    $('#fileName').text('Choose File'); 
                    return;
                } 
                formData.append('channel_file_upload', channelFile);
                formData.append('channel', $('select[name=channel]').val());
                formData.append('date', $('input[name=date]').val());
                formData.append('branch', $('select[name=branch]').val());

                $(this).prop('disabled', true);
                $(this).removeClass('btn-primary').addClass('btn-secondary');
                
                $.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '{!! route("banking.reconcile.daily.transactions.upload") !!}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response){
                        if (response.dataProcessed) {
                            $('.stepTwo').trigger('click');
                            console.log(response);
                            reconciledData = response.reconciledTransactions;
                            reconciledDataTotal = response.reconciledTotal
                            unreconciledData = response.notReconciled;
                            doubleEntriesData = response.doubles;
                            flaggedData = response.flaggedTransactions;
                            missingTransactionsData = response.missingTransactions

                            $('#summ_uploaded').html(response.bankCount +'('+response.bankTotal+')');                                                
                            $('#summ_system').html(response.systemCount +'('+response.systemTotal+')');
                            $('#summ_variance').html(parseFloat(response.bankCount) - parseFloat(response.systemCount) +'('+ (parseFloat(response.bankTotal) - parseFloat(response.systemTotal)) +')');
                            $('#summ_recon').html(reconciledData.length +'('+reconciledDataTotal +')');
                            $('#summ_variance_ag').html((response.systemCount - reconciledData.length) +'('+ (response.systemTotal - reconciledDataTotal) +')');
                            $('#summ_missing').html(missingTransactionsData.length +'('+response.missingTotal +')');
                            $('#summ_ignored').html(response.ignoredTransactions.length +'('+response.ignoredTotal+')');
                            $('#summ_flagged').html(flaggedData.length +'('+response.flaggedTotal+')');

                            // reconciledDatatable(reconciledData,reconciledDataTotal);
                            $('#reconciled_trans_table').DataTable({
                                autoWidth: false,
                                data: reconciledData, 
                                columns: [ 
                                    { data: null },
                                    { data: 'trans_date' },
                                    { data: 'input_date' },
                                    { data: 'bank_date' },
                                    { data: 'customer_name' },
                                    { data: 'reference' },
                                    { data: 'document_no' },
                                    { data: 'amount' },
                                    { data: null}
                                ],
                                createdRow: function (row, data, dataIndex) {
                                    $('td:eq(0)', row).html(dataIndex + 1);
                                    $(row).attr('data-id', data.id);
                                },
                                columnDefs: [{
                                    targets: -1, // Target the last column
                                    render: function(data, type, row, meta) {
                                        // Render a checkbox
                                        return '<input type="checkbox" class="checkbox reconChecked" value="' + data.id + '">';
                                    }
                                }]
                            });
                            $('#reconciled_trans_table_total').html(reconciledDataTotal);

                            $('#missing_trans_table').DataTable({
                                autoWidth: false,
                                data: response.missingTransactions, 
                                columns: [ 
                                    { data: null },
                                    { data: 'bank_date' },
                                    { data: 'bank_ref' },
                                    { data: 'route' },
                                    { data: 'comments' },
                                    { data: 'amount' }
                                ],
                                createdRow: function (row, data, dataIndex) {
                                    $('td:eq(0)', row).html(dataIndex + 1);
                                }
                            });
                            $('#missing_trans_table_total').html(response.missingTotal);

                            $('#ignored_trans_table').DataTable({
                                autoWidth: false,
                                data: response.ignoredTransactions, 
                                columns: [ 
                                    { data: null },
                                    { data: 'bank_date' },
                                    { data: 'bank_ref' },
                                    { data: 'route' },
                                    { data: 'comments' },
                                    { data: 'amount' }
                                ],
                                createdRow: function (row, data, dataIndex) {
                                    $('td:eq(0)', row).html(dataIndex + 1);
                                }
                            });
                            $('#ignored_trans_table_total').html(response.ignoredTotal);

                            $('#flagged_trans_table').DataTable({
                                autoWidth: false,
                                data: response.flaggedTransactions, 
                                columns: [ 
                                    { data: null },
                                    { data: 'bank_date' },
                                    { data: 'bank_ref' },
                                    { data: 'route' },
                                    { data: 'comments' },
                                    { data: 'amount' }
                                ],
                                createdRow: function (row, data, dataIndex) {
                                    $('td:eq(0)', row).html(dataIndex + 1);
                                }
                            });
                            $('#flagged_trans_table_total').html(response.flaggedTotal);

                        }
                    },
                    error: function(xhr, status, error){
                        console.error(xhr.responseText);
                    }
                });
            });

            $('#approveRecon').click(function(){
                var checkboxValues = [];
                $('#reconciled_trans_table').DataTable().rows().nodes().to$().find('.reconChecked').each(function() {
                    if ($(this).is(":checked")) {
                        checkboxValues.push($(this).val());
                    }
                });
                
                if(!checkboxValues.length){
                    $('#approvalCheckError').html("Select Type Of Approval");
                    return;
                }
                Swal.fire({
                            title: 'Approve Reconciliations?',
                            showCancelButton: true,
                            confirmButtonText: `Approve`,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                var reconJson = [];
                                var newData =[];
                                if (checkboxValues.length == reconciledData.length) {
                                    reconJson = reconciledData;
                                } else {
                                    $.each(reconciledData, function(index, item) {
                                        if (checkboxValues.includes(String(item.id))) {
                                            reconJson.push(item);
                                        } else{
                                            newData.push(item);
                                        }
                                    });
                                }
                                var postData = {
                                    reconJson: reconJson, 
                                    channel: $('select[name=channel]').val(),
                                    date: $('input[name=date]').val(),
                                    branch: $('select[name=branch]').val(),
                                };
                                $.ajax({
                                    url: "{{route('banking.reconcile.daily.transactions.approve')}}", // Replace this with your Laravel route
                                    type: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Add CSRF token header
                                    },
                                    data: postData, 
                                    success: function(response) {
                                        Swal.fire({
                                                    title: "Approved!",
                                                    text: "Posted to GL!",
                                                    icon: "success"
                                        });
                                        
                                        checkboxValues.forEach(function(id) {
                                            var rowToRemove = $('#reconciled_trans_table').find('tr[data-id="' + id + '"]');

                                            if (rowToRemove.length > 0) {
                                                var table = $('#reconciled_trans_table').DataTable();
                                                table.row(rowToRemove).remove().draw(false); 
                                            }
                                        });
                                        console.log(response);
                                    },
                                    error: function(xhr, status, error) {
                                        // Handle error response here
                                        console.error(xhr.responseText);
                                    }
                                });
                            } 
                        }) 
            });

            $('.downloadPdf').click(function(){
                var reconName = '';
                var jsonData = '' 
                if('downloadReconciled' == $(this).data('id')){
                    reconName = 'Reconciliation';
                    jsonData = reconciledData                 
                }

                if('downloadDoubleEntries' == $(this).data('id')){
                    reconName = 'DoubleEntry';
                    jsonData = doubleEntriesData;
                }
                if('downloadFlagged' == $(this).data('id')){
                    reconName = 'Flagged';
                    jsonData = flaggedData;
                }
                if('downloadUnreconciled' == $(this).data('id')){
                    reconName = 'Unreconciled';
                    jsonData = unreconciledData;
                }
                if('downloadMissing' == $(this).data('id')){
                    reconName = 'MissingTransactions';
                    jsonData = missingTransactions;
                }

                if(reconName){
                    $.ajax({
                        url: "{{route('banking.reconcile.daily.transactions.download')}}", // Replace with your endpoint URL
                        type: "POST",
                        contentType: "application/json",
                        data: JSON.stringify({
                            data: jsonData,
                            date: $("#date").val(),
                            branch: $("#branch").val(),
                            channel: $("#channel").val(),
                            type: reconName
                        }), // Convert JSON object to string
                       
                        xhrFields: {
                            responseType: 'blob' 
                        },
                        success: function(response, status, xhr) {
                            var blob = new Blob([response], { type: xhr.getResponseHeader('content-type') });
                            var url = window.URL.createObjectURL(blob);

                            // Create a link element to trigger the download
                            var link = document.createElement('a');
                            link.href = url;
                            link.download = 'daily_reconciliation_'+reconName+'.xlsx'; // Set the filename

                            document.body.appendChild(link);
                            link.click();
                            window.URL.revokeObjectURL(url);
                            link.remove();
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        }                    
                    });
                }
                
            });

    });

    function searchData()
    {
        getData()
    }

    function getData()
    {
        var date = $("#date").val();
        var branch = $("#branch").val();
        var channel = $("#channel").val();
        
        $('#dailyReconDataTable').DataTable().destroy();
        $("#dailyReconDataTable").DataTable({
            processing: true,
            serverSide: true,
            order: [
                [1, "asc"]
            ],
            autoWidth: false,
            pageLength: '<?= Config::get('params.list_limit_admin') ?>',
            ajax: {
                url: '{!! route("banking.reconcile.daily.transactions") !!}',
                data: function(data) {
                    data.date = date;
                    data.branch = branch;
                    data.channel = channel;
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    sortable: false,
                    width: "70px"
                }, {
                    data: 'trans_date',
                    name: 'trans_date',
                }, 
                {
                    data: 'branch',
                    name: 'branch',
                },
                {
                    data: 'document_no',
                    name: 'document_no',
                },
                {
                    data: 'reference',
                    name: 'reference',
                },
                {
                    data: 'amount',
                    name: 'amount',
                    className: 'text-right',
                },
            ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#total").text(json.total);
                },
            "columnDefs": [{
                        "searchable": false,
                        "targets": 0
                    },
                    {
                        className: 'text-center',
                        targets: [1]
                    },
                ],
                language: {
                    searchPlaceholder: "Search"
                },
        });
    }
        
        
</script>
@endsection