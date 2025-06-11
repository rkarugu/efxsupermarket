@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Top Up Debit Statements </h3>
                    <a href="{{ route('bank-statements') }}" class="btn btn-primary"><i class="fas fa-long-arrow-alt-left"></i> Back</a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form id="topupForm" action="{{ route('bank-statements.upload-debit') }}" method="post" enctype="multipart/form-data">
                    {{ @csrf_field() }}
                    <div class="row">
                        <div class="form-group col-sm-3">
                            <label for="channel" class="control-label"> Channel </label>
                            <select name="channel" id="channel" class="form-control select2" required>
                                <option value="">Choose Channel</option>
                                @foreach ($channels as $channel)
                                    <option value="{{$channel->title}}">{{$channel->title}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-sm-3">
                            <label for="topup_list" class="control-label"> File </label>
                            <input type="file" class="form-control" id="topup_list" name="topup_list">
                            <small class="text-danger" id="topup_label" style="height:30px; !important"></small>
                        </div>

                        <div class="form-group col-sm-6">
                            <label style="display: block;">&nbsp;</label>
                            <button type="submit" class="fetch btn btn-primary confirm">Upload</button>
                        </div>
                    </div>
                </form>
                <hr>

                @if($processingUpload)
                    
                    <div class="box-body">
                        <form action="{{ route('bank-statements.store-debit') }}" method="post">
                            {{ @csrf_field() }}

                            <input type="hidden" name="statements" value="{{ json_encode($statements) }}">

                            <div class="table-responsive">
                                <table class="table table-bordered" id="statementTable">
                                    <thead>
                                    <tr>
                                        <td>#</td>
                                        <th>Bank Date</th>
                                        <th>Reference</th>
                                        <th>Channel</th>
                                        <th>Original Channel</th>
                                        <th>Amount</th>
                                        <th>Original Amount</th>
                                        <th>Status</th>
                                        <th>Type</th>
                                        <th>Sys. Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($statements as $statement)
                                        <tr>
                                            <th style="width: 3%;" scope="row">{{ $loop->index + 1 }}</th>
                                            <td>{{ $statement['bank_date'] }}</td>
                                            <td>{{ $statement['reference'] }}</td>
                                            <td>{{ $statement['channel'] }}</td>
                                            <td>{{ $statement['original_channel'] }}</td>
                                            <td class="text-right">{{ manageAmountFormat($statement['amount']) }}</td>  
                                            <td class="text-right">{{ manageAmountFormat($statement['original_amount']) }}</td>
                                            <td>
                                                @if ($statement['status'])
                                                    Found
                                                @else
                                                    New                                                    
                                                @endif    
                                            </td>  
                                            <td>{{ $statement['type']}}</td>
                                            <td>{{ $statement['verification_status'] }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Top Up</button>
                            </div>
                        </form>
                    </div>
                    
                @endif
            </div>
        </div>
    </section>
    <span class="btn-loader" style="display:none;">
        <img src="<?= asset('/assets/admin/images/loader.gif') ?>" alt="Loader" />
    </span>
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
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script>
        $('.select2').select2();
        $(document).ready(function () {
            $('.confirm').on('click',function(e){
                e.preventDefault();
                $('.btn-loader').show();
                $("#topupForm").submit();
            });

            $('#generateExcelDuplicate').on('click',function(){
                $.ajax({
                    url: `{{ route('bank-statements-export-duplicate') }}`, // your route here
                    method: 'POST',
                    data: {
                        // any data you want to send to the server
                        duplicates: $('#statementsDuplicate').val(),
                        _token: '{{ csrf_token() }}' // for Laravel CSRF protection
                    },
                    xhrFields: {
                        responseType: 'blob' // very important to handle binary data
                    },
                    success: function(blob, status, xhr) {
                        // Create a link element
                        var link = document.createElement('a');
                        var url = window.URL.createObjectURL(blob);
                        link.href = url;
                        link.download = 'export_duplicate.xlsx'; // the filename for the downloaded file
                        document.body.append(link);
                        link.click();
                        link.remove();

                        // Release memory
                        window.URL.revokeObjectURL(url);
                    },
                    error: function(xhr, status, error) {
                        alert('Failed to download file: ' + error);
                    }
                });
            });
            
            $('#topup_list').change(function () {
                var fileName = $(this).val().split('\\').pop();
                $('#topup_label').removeClass('text-danger');
                $('#topup_label').text(fileName);
            });
            
            $('#statementDuplicateTable').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                'autoWidth': false,
                'pageLength': 100,
                'aoColumnDefs': [{
                    'bSortable': false,
                    'aTargets': 'noneedtoshort'
                }],
            });
            $('#statementTable').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                'autoWidth': false,
                'pageLength': 100,
                'aoColumnDefs': [{
                    'bSortable': false,
                    'aTargets': 'noneedtoshort'
                }],
            });
        });
    </script>
@endsection