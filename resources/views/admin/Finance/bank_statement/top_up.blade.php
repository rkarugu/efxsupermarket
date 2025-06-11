@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Top Up Statements </h3>
                    <a href="{{ route('bank-statements') }}" class="btn btn-primary"><i class="fas fa-long-arrow-alt-left"></i> Back</a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form id="topupForm" action="{{ route('bank-statements.upload') }}" method="post" enctype="multipart/form-data">
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
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#duplicate" data-toggle="tab">Duplicate</a></li>
                        <li><a href="#okay" data-toggle="tab">Clean</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="duplicate">
                            <div class="box-body">
                                @if (count($statementsDuplicate))
                                    <div style="display: block;clear:both;width:100%;position:relative;text-align:right;">
                                        <button type="button" class="btn btn-primary" name="action" id="generateExcelDuplicate" style="height: 35px;text-aligh:right;margin:10px 0px;">
                                            <i class="fa fa-file-alt"></i> Excel
                                        </button>
                                    </div>
                                @endif
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="statementDuplicateTable">
                                        <thead>
                                        <tr>
                                            <td>#</td>
                                            <th>Bank Date</th>
                                            <th>Reference</th>
                                            <th>Trans Ref.</th>
                                            <th>Amount</th>
                                            <th>Channel</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $duplicateArray=[];
                                            @endphp
                                        @foreach($statementsDuplicate as $statement)
                                            <tr>
                                                <th style="width: 3%;" scope="row">{{ $loop->index + 1 }}</th>
                                                <td>{{ $statement['bank_date'] }}</td>
                                                <td>{{ $statement['reference'] }}</td>
                                                <td>{{ $statement['reference_2'] }}</td>
                                                <td>{{ $statement['amount'] }}</td>
                                                <td>{{ $statement['channel'] }}</td>                                                
                                            </tr>
                                            @php
                                                $duplicateArray[]=[
                                                    $statement['bank_date'],
                                                    $statement['reference'],
                                                    $statement['reference_2'],
                                                    $statement['amount'],
                                                    $statement['channel']
                                                ];
                                            @endphp
                                        @endforeach
                                        </tbody>
                                    </table>
                                    <input type="hidden" name="statementsDuplicate" id="statementsDuplicate" value="{{ json_encode($duplicateArray) }}">
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="okay">
                            <div class="box-body">
                                <form action="{{ route('bank-statements.store') }}" method="post">
                                    {{ @csrf_field() }}
            
                                    <input type="hidden" name="statements" value="{{ json_encode($statementsUnique) }}">
            
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="statementTable">
                                            <thead>
                                            <tr>
                                                <td>#</td>
                                                <th>Bank Date</th>
                                                <th>Reference</th>
                                                <th>Trans Ref.</th>
                                                <th>Channel</th>
                                                <th>Debit</th>
                                                <th>Credit</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($statementsUnique as $statement)
                                                <tr>
                                                    <th style="width: 3%;" scope="row">{{ $loop->index + 1 }}</th>
                                                    <td>{{ $statement['bank_date'] }}</td>
                                                    <td>{{ $statement['reference'] }}</td>
                                                    <td>{{ $statement['reference_2'] }}</td>
                                                    <td>{{ $statement['channel'] }}</td>
                                                    @if ($statement['amountStatus'] == 'debit')
                                                        <td class="text-right">{{ manageAmountFormat($statement['amount']) }}</td>    
                                                        <td class="text-right">0</td>
                                                    @else
                                                        <td class="text-right">0</td>    
                                                        <td class="text-right">{{ manageAmountFormat($statement['amount']) }}</td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
            
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary topUpBtn">Top Up</button>
                                    </div>
                                </form>
                            </div>
                        </div>
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

            $('.topUpBtn').on('click',function(e){
                e.preventDefault();
                
                $('.topUpBtn').prop('disabled', true);
                $('.btn-loader').show();
                

                var postData = new FormData($(this).parents('form')[0]);
                var url = $(this).parents('form').attr('action');
                postData.append('_token',$(document).find('input[name="_token"]').val());

                $.ajax({
                url:url,
                data:postData,
                contentType: false,
                cache: false,
                processData: false,
                method:'POST',
                success:function(out){
                    $('.btn-loader').hide();
                    if(out.result == 0) {
                        for(let i in out.errors) {
                            var id = i.split(".");
                            if(id && id[1]){
                                $("[name='"+id[0]+"["+id[1]+"]']").parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                            }else
                            {
                                $("[name='"+i+"']").parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                                $("."+i).parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                            }
                        }
                    }
                    if(out.result === 1) {
                        form.successMessage(out.message);    
                        setTimeout(
                        function() 
                        {                        
                            location.reload();
                        }, 3000);         
                    }
                    if(out.result === -1) {
                        form.errorMessage(out.message);
                    }
                },
                
                error:function(err)
                {
                    console.log(err);
                    $('.btn-loader').hide();
                    form.errorMessage('Something went wrong');							
                }
            });

            });
        });
    </script>
@endsection