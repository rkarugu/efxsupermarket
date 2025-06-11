@extends('layouts.admin.admin')

@section('content')
<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="box-header-flex">
                <h3 class="box-title"> Bulk Device Upload </h3>
                <div>
                    <a href="{{ route($model.'.index') }}" class="btn btn-primary"><i class="fas fa-long-arrow-alt-left"></i> Back</a>
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="session-message-container">
                @include('message')
            </div>

            <form id="topupForm" action="{{ route('device-center.bulk-upload') }}" method="post" enctype="multipart/form-data">
                {{ @csrf_field() }}
                <div class="row">
                    <div class="form-group col-sm-3">
                        <label for="device_upload" class="control-label"> File </label>
                        <input type="file" class="form-control" id="device_upload" name="device_upload">
                        <small class="text-danger" id="device_label" style="height:30px; !important"></small>
                    </div>

                    <div class="form-group col-sm-6">
                        <label style="display: block;">&nbsp;</label>
                        <input type="submit" class="btn btn-success btn-sm" name="intent" id="template" value="Template">
                        <button type="submit" class="fetch btn btn-primary btn-sm confirm">Upload</button>
                    </div>
                </div>
            </form>
            <hr>

            @if($processingUpload)
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#duplicate" data-toggle="tab">Rejected</a></li>
                    <li><a href="#okay" data-toggle="tab">Clean</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="duplicate">
                        <div class="box-body">
                            
                            <div class="table-responsive">
                                <table class="table table-bordered" id="statementDuplicateTable">
                                    <thead>
                                        <tr>
                                            <td>#</td>
                                            <th>Device Type</th>
                                            <th>Model</th>
                                            <th>Serial No.</th>
                                            <th>Device No.</th>
                                            <th>Sim Card Number</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($deviceRejected as $device)
                                            <tr>
                                                <th style="width: 3%;" scope="row">{{ $loop->index + 1 }}</th>
                                                <td>{{ $device['deviceType'] }}</td>
                                                <td>{{ $device['model'] }}</td>
                                                <td>{{ $device['serial'] }}</td>
                                                <td>{{ $device['deviceNo'] }}</td>
                                                <td>{{ $device['simCard'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <input type="hidden" name="deviceRejected" id="deviceRejected" value="{{ json_encode($deviceRejected) }}">
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="okay">
                        <div class="box-body">
                            <form action="{{ route('device-center.bulk-upload-store') }}" method="post">
                                {{ @csrf_field() }}
                                <input type="hidden" name="devices" value="{{ json_encode($devices) }}">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="uploadDeviceTable">
                                        <thead>
                                        <tr>
                                            <td>#</td>
                                            <th>Device Type</th>
                                            <th>Model</th>
                                            <th>Serial No.</th>
                                            <th>Device No.</th>
                                            <th>IMEI Number</th>
                                            <th>Branch</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($devices as $device)
                                            <tr>
                                                <th style="width: 3%;" scope="row">{{ $loop->index + 1 }}</th>
                                                <td>{{ $device['deviceType'] }}</td>
                                                <td>{{ $device['model'] }}</td>
                                                <td>{{ $device['serial'] }}</td>
                                                <td>{{ $device['deviceNo'] }}</td>
                                                <td>{{ $device['simCard'] }}</td>
                                                <td>{{ $device['branch'] }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
        
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary UploadBtn">Upload Devices</button>
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

@endsection

@section('uniquepagescript')
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
   
<script>
    $(document).ready(function () {
        $('#device_upload').change(function () {
            var fileName = $(this).val().split('\\').pop();
            $('#device_label').removeClass('text-danger');
            $('#device_label').text(fileName);
        });
        $('#uploadDeviceTable').DataTable({
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

        $('.UploadBtn').on('click',function(e){
                e.preventDefault();
                
                $('.UploadBtn').prop('disabled', true);
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
