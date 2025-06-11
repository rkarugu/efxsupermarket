
@extends('layouts.admin.admin')

@section('content')
<style>
    .span-action {

    display: inline-block;
    margin: 0 3px;

}
</style>
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                           
                            @include('message')
                            <div class="col-md-12 no-padding-h table-responsive">
                                <h3>{{$title}}</h3>
                            </div>
                            <form action="" method="get">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                          <label for="">From Date</label>
                                          <input type="date" name="from_date" value="{{date('Y-m-d')}}" id="from_date" class="form-control" >
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                          <label for="">To Date</label>
                                          <input type="date" name="to_date" value="{{date('Y-m-d')}}" id="to_date" class="form-control" >
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                          <br>
                                          <button type="submit" class="btn btn-danger">Filter</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="col-md-12 no-padding-h table-responsive">
                                <table class="table table-bordered table-hover">
                                    <tr>
                                        <th>Module</th>
                                        <th>LPO Number</th>
                                        <th>Supplier</th>
                                        <th>Status</th>
                                        <th>Error Message</th>
                                        <th>Timestamp</th>
                                        <th>##</th>
                                    </tr>
                                    @foreach($logs as $log)
                                    <tr>
                                        <td>{{ $log->module }}</td>
                                        <td>{{ $log->lpo_number }}</td>
                                        <td>
                                            @php
                                                $requested_data = json_decode($log->request_data);
                                            @endphp
                                            @if($log->module == 'Send LPO to Supplier' && $requested_data->supplier_name)
                                                {{ $requested_data->supplier_name }}
                                            @endif
                                        </td>
                                        <td>{{ $log->status }}</td>
                                        <td>{{ $log->error_message }}</td>
                                        <td>{{ $log->created_at }}</td>
                                        <td>
                                            <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#logModal" 
                                                data-module="{{ $log->module }}"
                                                data-lpo_number="{{ $log->lpo_number }}"
                                                data-status="{{ $log->status }}"
                                                data-error_message="{{ $log->error_message }}"
                                                data-created_at="{{ $log->created_at }}"
                                                data-request="{{ json_encode($requested_data, JSON_PRETTY_PRINT) }}" 
                                                data-response="{{ json_encode(json_decode($log->response_data), JSON_PRETTY_PRINT) }}">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </td>

                                    </tr>
                                    @endforeach
                                    
                                </table>
                                {{$logs->links()}}
                            </div>
                        </div>
                    </div>


    </section>
  <!-- Modal for Request and Response Data -->
<div class="modal fade" id="logModal" tabindex="-1" role="dialog" aria-labelledby="logModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logModalLabel">Log Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table table-hover">
                            <tr>
                                <th>Module</th>
                                <td id="module"></td>
                                <th>Status</th>
                                <td id="status"> </td>
                                <th>Date</th>
                                <td id="date"> </td>
                            </tr>
                            <tr>
                                <th>Message</th>
                                <td colspan="5" id="error_message"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-6">
                        <h6>Request Data:</h6>
                        <pre id="requestData"></pre>
                    </div>
                    <div class="col-sm-6">
                        <h6>Response Data:</h6>
                        <pre id="responseData"></pre>
                    </div>
                </div>
                
               
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $('#logModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var request = button.data('request'); // Parse request data to JSON
        var response = button.data('response'); // Parse response data to JSON
        $('#module').text(button.data('module'));
        $('#status').text(button.data('status'));
        $('#date').text(button.data('created_at'));
        $('#error_message').text(button.data('error_message'));
        // Update the modal's content with formatted JSON
        var modal = $(this);
        modal.find('#requestData').text(JSON.stringify(request, null, 4)); // Pretty print JSON
        modal.find('#responseData').text(JSON.stringify(response, null, 4)); // Pretty print JSON
    });
</script>
@endpush