@extends('layouts.admin.admin')
@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Bank Posting Logs </h3>
                    <div class="d-flex">
                        
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="row">
                    <div class="form-group col-sm-2">
                        <label for="">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control">
                    </div>  
                    <div class="form-group col-sm-2">
                        <label for="">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control">
                    </div>  
                    
                    <div style="margin-left:10px;margin-top:25px; display:flex;">
                        {{-- <button type="button" class="btn btn-primary" name="action" id="generateExcelBtn" style="height: 35px;">
                            <i class="fa fa-file-alt"></i> Excel
                        </button>
                        <button type="button" class="btn btn-primary" name="action" id="generatePDFBtn" style="margin-left:10px;height: 35px;">
                            <i class="fa fa-file"></i> PDF
                        </button> --}}
                        <a href="{{route('bank-posting-logs')}}" class="btn btn-primary" name="action" id="generatePDFBtn" style="margin-left:10px;height: 35px;">
                            Clear
                        </a>
                        
                    </div>
                </div>
                <hr>

                <div class="col-md-12 no-padding-h" id="getintervalview">
                    <table class="table table-bordered" id="statementsDataTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Trans. No</th>
                                <th>Approved</th>
                                <th>Credit</th>
                                <th>Debit</th>
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
        
        $('#start_date, #end_date, #channel, #status').on('change', function() {
            $("#statementsDataTable").DataTable().ajax.reload();
        });
        $('#generateExcelBtn').on('click',function(){
            location.href=`/admin/bank-statements-upload?print=excel&start_date=`+$("#start_date").val()+`&end_date=`+$("#end_date").val()+`&channel=`+$("#channel").val()+`&status=`+$("#status").val();
        });
        $('#generatePDFBtn').on('click',function(){
            location.href=`/admin/bank-statements-upload/?print=pdf&start_date=`+$("#start_date").val()+`&end_date=`+$("#end_date").val()+`&channel=`+$("#channel").val()+`&status=`+$("#status").val();
        });
        $("#statementsDataTable").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                pageLength: 100,
                ajax: {
                    url: '{!! route('bank-posting-logs') !!}',
                    data: function(data) {
                        data.start_date = $("#start_date").val();
                        data.end_date = $("#end_date").val();
                    }
                },
                columns: [
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'transaction_no',
                        name: 'transaction_no'
                    },
                    {
                        data: 'approved',
                        name: 'approved'
                    },
                    {
                        data: 'credit',
                        name: 'credit'
                    },
                    {
                        data: 'debit',
                        name: 'debit'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false
                    } 
                ], columnDefs: [
                    {
                        targets: -1,
                        render: function (data, type, row, meta) {
                            if (type === 'display') {
                                var actions = '';
                                    actions += `
                                    <a title="Excel"
                                                        href="/admin/bank-post-log/excel/`+row.transaction_no+`"
                                                        style="margin-left:5px;">
                                                        <i aria-hidden="true" class="fa fa-file-excel"></i>
                                                    </a>
                                    `;    
                                    
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