@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Debtor Trans Upload</h3>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form action="{{ route('maintain-customers.real_recon.upload') }}" method="post" enctype='multipart/form-data'>
                    {{ @csrf_field() }}
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="bank">Select bank</label>
                                <select name="bank" id="bank" class="form-control">
                                    <option value="" disabled selected>Select Bank</option>
                                    @foreach(['EQUITY BANK', 'VOOMA'] as $bank)
                                        <option value="{{ $bank }}">{{ $bank }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="upload_file">Select upload file</label>
                                <input type="file" class="form-control" name="upload_file" id="upload_file" required>
                                <label class="custom-file-label" for="customFile" id="upload_file_label"></label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="bank_upload_file">Select bank statement</label>
                                <input type="file" class="form-control" name="bank_upload_file" id="bank_upload_file" required>
                                <label class="custom-file-label" for="customFile" id="bank_upload_file_label"></label>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label style="display: block;">&nbsp;</label>
                                <input type="submit" class="btn btn-primary" name="intent" value="UPLOAD">
                            </div>
                        </div>
                    </div>
                </form>

                <hr>

                @if($uploadedData)
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Upload Preview</h3>
                        </div>

                        <div class="box-body">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#matching_trans" data-toggle="tab">Matching Transactions</a></li>
                                <li><a href="#rejected_trans" data-toggle="tab">Rejected Transactions</a></li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane active" id="matching_trans">
                                    <div class="box-body">
                                        <form action="{{ route('maintain-customers.real_recon.confirm') }}" method="post">
                                            {{ @csrf_field() }}

                                            <input type="hidden" value="{{ json_encode($matchingTrans) }}" name="records">

                                            <div class="table-responsive">
                                                <table class="table table-hover table-bordered" id="matching_trans_table">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 3%;">#</th>
                                                        <th>CUSTOMER CODE</th>
                                                        <th>ROUTE</th>
                                                        <th>TRANS DATE</th>
                                                        <th>AMOUNT</th>
                                                        <th>PAID BY</th>
                                                        <th>CHANNEL</th>
                                                        <th>REFERENCE</th>
                                                        <th>BANK REFERENCE</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($matchingTrans as $index => $row)
                                                        <tr>
                                                            <th style="width: 3%" scope="row">{{ $index + 1 }}</th>
                                                            <td>{{ $row[0] }}</td>
                                                            <td>{{ $row[1] }}</td>
                                                            <td>{{ $row[2] }}</td>
                                                            <td>{{ $row[3] }}</td>
                                                            <td>{{ $row[4] }}</td>
                                                            <td>{{ $row[5] }}</td>
                                                            <td>{{ $row[6] }}</td>
                                                            <td>{{ $row[7] ?? $row[8] }}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>

                                                <div class="d-flex justify-content-end">
                                                    <input type="submit" value="Confirm Upload" class="btn btn-primary">
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="tab-pane" id="rejected_trans">
                                    <div class="box-body">
                                        <div class="table-responsive">
                                            <div class="d-flex justify-content-end" style="margin-bottom: 10px;">
                                                <form action="{{ route('maintain-customers.real_recon.download-rejected') }}" method="post">
                                                    {{ @csrf_field() }}

                                                    <input type="hidden" name="records" value="{{ json_encode($rejectedTrans) }}">
                                                    <input type="submit" value="Download Rejected" class="btn btn-primary">
                                                </form>
                                            </div>

                                            <table class="table table-hover table-bordered" id="rejected_trans_table">
                                                <thead>
                                                <tr>
                                                    <th style="width: 3%;">#</th>
                                                    <th>CUSTOMER CODE</th>
                                                    <th>ROUTE</th>
                                                    <th>TRANS DATE</th>
                                                    <th>AMOUNT</th>
                                                    <th>PAID BY</th>
                                                    <th>CHANNEL</th>
                                                    <th>REFERENCE</th>
                                                    <th>REMARKS</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($rejectedTrans as $index => $row)
                                                    <tr>
                                                        <th style="width: 3%" scope="row">{{ $index + 1 }}</th>
                                                        <td>{{ $row[0] }}</td>
                                                        <td>{{ $row[1] }}</td>
                                                        <td>{{ $row[2] }}</td>
                                                        <td>{{ $row[3] }}</td>
                                                        <td>{{ $row[4] }}</td>
                                                        <td>{{ $row[5] }}</td>
                                                        <td>{{ $row[6] }}</td>
                                                        <td>{{ $row[7] ?? $row[8] }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@section('uniquepagescript')
    <script>
        $(document).ready(function () {
            $('body').addClass('sidebar-collapse');
            // $("#bank").select2();

            $('#matching_trans_table').DataTable({
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

            $('#rejected_trans_table').DataTable({
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

        $('#upload_file').on('change', function () {
            let fileName = $(this).val();
            $(this).next('#upload_file_label').text(fileName);
        });

        $('#bank_upload_file').on('change', function () {
            let fileName = $(this).val();
            $(this).next('#bank_upload_file_label').text(fileName);
        });


    </script>
@endsection