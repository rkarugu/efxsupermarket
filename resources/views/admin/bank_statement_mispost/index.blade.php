@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Bank Statement Mispost</h3>
                    @if (can('add', 'bank-statement-mispost'))
                        <a href="{{ route('bank-statement-mispost.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add</a>
                    @endif
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
                            <th>Old Date</th>
                            <th>New Date</th>
                            <th>Original Channel</th>
                            <th>New Channel</th>
                            <th>Updated By</th>
                            <th>Reference</th>
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
                url: '{!! route('bank-statement-mispost.index') !!}',
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
                    data: "old_bank_date",
                    name: "old_bank_date",
                },
                {
                    data: "new_bank_date",
                    name: "new_bank_date",
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
                    data:'bank_statement.reference',
                    name:'bankStatement.reference'
                },
                {
                    data: "bank_statement.amount",
                    name: "bankStatement.amount",
                },             
            ],
            
        });
    </script>
@endsection