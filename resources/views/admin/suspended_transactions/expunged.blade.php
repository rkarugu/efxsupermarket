@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Expunged Transactions </h3>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="create_datatable">
                        <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Suspension Date</th>
                            <th>Suspended By</th>
                            <th>Expunge Date</th>
                            <th>Expunge By</th>
                            <th>Document No</th>
                            <th>Route</th>
                            <th>Trans Date</th>
                            <th>Input Date</th>
                            <th>Reference</th>
                            <th>Reason</th>
                            <th>Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($expungedTransactions as $trans)
                            <tr>
                                <th style="width: 3%;" scope="row">{{ $loop->index + 1 }}</th>
                                <td>{{ $trans->created_at }}</td>
                                <td>{{ $trans->suspender }}</td>
                                <td>{{ $trans->updated_at }}</td>
                                <td>{{ $trans->resolver }}</td>
                                <td>{{ $trans->document_no }}</td>
                                <td>{{ $trans->route }}</td>
                                <td>{{ $trans->trans_date }}</td>
                                <td>{{ $trans->input_date }}</td>
                                <td>{{ $trans->reference }}</td>
                                <td>{{ $trans->reason }}</td>
                                <td>{{ manageAmountFormat($trans->amount) }}</td>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="11" scope="row" style="text-align: center;"> TOTAL</th>
                            <th colspan="1" scope="row">{{ manageAmountFormat($expungedTransactions->sum('amount')) }}</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('body').addClass('sidebar-collapse');
        });
    </script>
@endsection