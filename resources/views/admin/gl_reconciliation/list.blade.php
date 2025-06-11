@extends('layouts.admin.admin')
@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> GL Reconciliation </h3>
                    <div class="d-flex">
                        @if (can('create',$model))
                        <a href="{{ route('gl-reconciliation.create') }}" class="btn btn-primary">Reconcile</a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#pending" data-toggle="tab"> Pending</a></li>
                    <li><a href="#closed" data-toggle="tab">Closed</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="pending">
                        <div class="table-responsive box-body">
                            <div class="col-md-12 no-padding-h" id="getintervalview">
                                <table class="table table-bordered" id="reconDataTable">
                                    <thead>
                                        <tr>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Account</th>
                                            <th>Beginning Balance</th>
                                            <th>Ending Balance</th>
                                            <th>Variance</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>                        
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="closed">
                        <div class="table-responsive box-body">
                            <div class="col-md-12 no-padding-h" id="getintervalview">
                                <table class="table table-bordered" id="closedDataTable">
                                    <thead>
                                        <tr>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Account</th>
                                            <th>Beginning Balance</th>
                                            <th>Ending Balance</th>
                                            <th>Variance</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>                        
                                </table>
                            </div>
                        </div>
                    </div>
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
            let start_date = $("#start_date").val();
            let end_date = $("#end_date").val();
            $("#startDate").val(start_date);
            $("#endDate").val(end_date);
            $("#reconDataTable").DataTable().ajax.reload();
        });
       
        $("#reconDataTable").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                pageLength: 100,
                ajax: {
                    url: '{!! route('gl-reconciliation.list') !!}',
                    data: function(data) {
                        data.start_date = $("#start_date").val();
                        data.end_date = $("#end_date").val();
                        data.channel = $("#channel").val();
                        data.status = 'pending'
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
        

        $("#closedDataTable").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                pageLength: 100,
                ajax: {
                    url: '{!! route('gl-reconciliation.list') !!}',
                    data: function(data) {
                        data.start_date = $("#start_date").val();
                        data.end_date = $("#end_date").val();
                        data.channel = $("#channel").val();
                        data.status = 'closed'
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