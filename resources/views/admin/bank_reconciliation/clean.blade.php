@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Clean Transactions </h3>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form action="{{ route('bank-reconciliation.cleanup.upload') }}" method="post" enctype="multipart/form-data">
                    {{ @csrf_field() }}

                    <div class="row">
                        <div class="form-group col-sm-3">
                            <label for="cleanup_list" class="control-label"> Cleanup List </label>
                            <input type="file" class="form-control" name="cleanup_list" id="cleanup_list">
                            <label class="custom-file-label" id="cleanup_list_label"></label>
                        </div>

                        <div class="form-group col-sm-9">
                            <label style="display: block;">&nbsp;</label>
                            <input type="submit" name="intent" value="Upload" class="btn btn-primary">
                        </div>
                    </div>
                </form>

                <hr>

                @if($processingUpload)
                    <form action="{{ route('bank-reconciliation.cleanup.confirm') }}" method="post">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th style="width: 3%;">#</th>
                                    <th>Document No</th>
                                    <th>Route</th>
                                    <th>Trans Date</th>
                                    <th>Input Date</th>
                                    <th>Reference</th>
                                    <th>Amount</th>
                                    <th>Reason</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($trans as $tran)
                                    <tr>
                                        <th style="width: 3%;" scope="row">{{ $loop->index + 1 }}</th>
                                        <td>{{ $tran['document_no'] }}</td>
                                        <td>{{ $tran['route'] }}</td>
                                        <td>{{ $tran['trans_date'] }}</td>
                                        <td>{{ $tran['input_date'] }}</td>
                                        <td>{{ $tran['reference'] }}</td>
                                        <td>{{ manageAmountFormat($tran['amount']) }}</td>
                                        <td>{{ $tran['reason'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="6" scope="row">CLEANUP TOTAL</th>
                                    <th colspan="2" scope="row">{{ manageAmountFormat(collect($trans)->sum('amount')) }}</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <input type="submit" value="Confirm Deletion" class="btn btn-primary">
                        </div>
                    </form>
                @endif
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
            $('#reconciled_trans_table').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                'autoWidth': false,
                'pageLength': 100,
                'initComplete': function (settings, json) {
                    let info = this.api().page.info();
                    let total_record = info.recordsTotal;
                    if (total_record < 101) {
                        // $('.dataTables_paginate').hide();
                    }
                },
                'aoColumnDefs': [{
                    'bSortable': false,
                    'aTargets': 'noneedtoshort'
                }],
            });
        });

        $('#cleanup_list').on('change', function () {
            let fileName = $(this).val();
            $(this).next('#cleanup_list_label').text(fileName);
        })
    </script>
@endsection