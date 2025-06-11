@extends('layouts.admin.admin')
@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Add Customer to GL </h3>
                    <div class="d-flex">
                        
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form action="{{ route('update-customer-to-gl.process') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="form-group col-sm-2">
                            <label for="">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" required>
                        </div>  
                        <div class="form-group col-sm-2">
                            <label for="">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control">
                        </div>  
                        <div style="margin-left:10px;margin-top:25px; display:flex;">
                            <button type="submit" class="btn btn-primary" name="action" id="process" style="height: 35px;margin-right:10px;">
                                Process
                            </button>
                            <button type="button" class="btn btn-primary" name="action" id="generateExcelBtn" style="height: 35px;">
                                <i class="fa fa-file-alt"></i> Excel
                            </button>
                        </div>
                    </div>
                </form>
                <hr>

                <div class="col-md-12 no-padding-h" id="getintervalview">
                    <table class="table table-bordered" id="statementsDataTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Transaction Date</th>
                                <th>Transaction No</th>
                                <th>Transaction Type</th>
                                <th>Account</th>
                                <th>Narrative</th>
                                <th>Branch</th>
                                <th>Debit</th>
                                <th>Credit</th>
                            </tr>
                        </thead>                        
                    </table>
                </div>
            </div>
        </div>
    </section>

    @include('admin.Finance.bank_statement.partials.topup_modal')

    <div class="modal fade" id="debtorUploadModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title"> Allocate Statement to Debtors</h3>
    
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form id="manualUploadForm" action="{{ route('manual-upload-transaction') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <form id="fetchPaymentForm" action="" method="post">
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label for="route" class="control-label"> Route </label>
                                    <select name="route" id="route" class="form-control mtselect" required>
                                        <option value="">Choose Route</option>
                                        
                                    </select>
                                </div>
                                
                            </div>
                    </div>
    
                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <input type="hidden" name="" id="">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <input type="hidden" value="" id="bankId" name="bankId">
                            <button type="submit" id="confirmManualUploadBtn" class="btn btn-primary" data-id="0" data-dismiss="modal">Allocate</button>
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
        $('.select2').select2();
        $('.mtselect').select2({
            dropdownParent: $('#debtorUploadModal')
        });
        $('#start_date, #end_date').on('change', function() {
            $("#statementsDataTable").DataTable().ajax.reload();
        });
        $('#generateExcelBtn').on('click',function(){
            location.href=`/admin/update-customer-to-gl/?print=excel&start_date=`+$("#start_date").val()+`,&end_date=`+$("#end_date").val()
        });
        $("#statementsDataTable").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                pageLength: 100,
                ajax: {
                    url: '{!! route('update-customer-to-gl.index') !!}',
                    data: function(data) {
                        data.start_date = $("#start_date").val();
                        data.end_date = $("#end_date").val();
                    }
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        mname: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'trans_date',
                        name: 'trans_date'
                    },                    
                    {
                        data: 'transaction_no',
                        name: 'transaction_no'
                    },                    
                    {
                        data: 'transaction_type',
                        name: 'transaction_type'
                    },
                    {
                        data: 'account',
                        name: 'account'
                    },
                    {
                        data: 'narrative',
                        name: 'narrative'
                    },
                    {
                        data: 'branch_name',
                        name: 'restaurants.name'
                    },
                    {
                        data: 'debit',
                        name: 'debit'
                    },
                    {
                        data: 'credit',
                        name: 'credit'
                    },
                ],
            });
            $('#confirmManualUploadBtn').on('click', function (e) {
                e.preventDefault();
                var errors = 0;

                if (errors == 0) {
                    $(this).prop("disabled", true);
                    $('#manualUploadForm').get(0).submit();
                }
            });

        });

        function uploadPop(id)
        {
            // $('.mtselect').trigger('change.select2');
            $('#bankId').val(id);
            $('#debtorUploadModal').modal('show');
        }

       
            </script>
@endsection