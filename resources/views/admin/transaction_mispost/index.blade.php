@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Transaction Mispost</h3>
                    <a href="{{ route('transaction-mispost.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add</a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                <table class="table table-bordered table-hover" id="dataTable">
                    <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Date</th>
                            <th>Document_no</th>
                            <th>Original Channel</th>
                            <th>New Channel</th>
                            <th>Updated By</th>
                            <th>Amount</th>
                        </tr>
                    </thead>                  
                </table>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    
@endsection

@section('uniquepagescript')
    <script>
        let table = $("#dataTable").DataTable({
            processing: true,
            serverSide: true,
            order: [
                [0, "asc"]
            ],
            autoWidth: false,
            pageLength: '<?= Config::get('params.list_limit_admin') ?>',
            ajax: {
                url: '{!! route('transaction-mispost.index') !!}',
                data: function(data) {
                    
                }
            },
            columns: [
                { 
                    data: 'DT_RowIndex', 
                    name: 'DT_RowIndex', 
                    orderable: false, 
                    searchable: false 
                },
                {
                    data: "created_at",
                    name: "created_at",
                },
                {
                    data: "debtor_trans.document_no",
                    name: "debtorTrans.document_no",
                },
                {
                    data: "old_channel",
                    name: "old_channel.title",
                },
                {
                    data: "new_channel",
                    name: "new_channel",
                },
                {
                    data: "created_by.name",
                    name: "createdBy.name",
                },
                {
                    data: "debtor_trans.amount",
                    name: "debtorTrans.amount",
                },             
            ],
            
        });
    </script>
@endsection