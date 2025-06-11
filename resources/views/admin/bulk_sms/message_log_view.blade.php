@extends('layouts.admin.admin')
@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> View Message Log </h3>
                    <div class="d-flex">
                    <a href="{{ route('bulk-sms.message-log') }}" class="btn btn-primary"><i class="fas fa-long-arrow-alt-left"></i> Back</a>
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="row">
                    <div class="col-sm-12 d-flex" style="margin: 10px 0">  
                        <div class="form-group" style="margin-left:10px;">
                            <label for="delivery_status">Title</label>
                            <input type="text" class="form-control" value="{{$logs->title}}" disabled>
                        </div>
                        <div class="form-group" style="margin-left:10px;">
                            <label for="delivery_status">Branch</label>
                            <input type="text" class="form-control" value="{{$logs->branch?  $logs->branch->name: 'All' }}" disabled>
                        </div>
                        <div class="form-group" style="margin-left:10px;">
                            <label for="start_date"> Message</label>
                            <textarea  class="form-control" id="" disabled>{!! $logs->messages[0]->message !!}</textarea>                            
                        </div>
                    </div>
                </div>
                <hr>
                
                <div class="col-md-12 no-padding-h" id="getintervalview">
                    <table class="table table-bordered" id="logDataTable">
                        <thead>
                            <tr>
                                <th style="width: 3%;">#</th>
                                <th>Phone</th>
                                <th>Sms Length</th>
                                <th>issn</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logs->messages as $log)
                                <tr>
                                    <th style="width: 3%;" scope="row"> {{ $loop->index + 1 }} </th>
                                    <td>{{$log->phone_number}}</td>
                                    <td>{{$log->sms_length}}</td>
                                    <td>{{ $log->issn }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('uniquepagestyle')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />
<style>
    .select2.select2-container.select2-container--default
    {
        width: 100% !important;
    }
    .reportRange {
        display: flex;
        align-content: center;
        justify-content: stretch;
        border: 1px solid #eee;
        cursor: pointer;
        height: 35px;
    }

</style>
@endsection
@section('uniquepagescript')

<script>
    $(document).ready(function() {
       
        $("#logDataTable").DataTable({
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
