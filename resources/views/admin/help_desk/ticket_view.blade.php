@extends('layouts.admin.admin')
@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title" style="font-weight: 500 !important;"> Ticket Info ({{$ticket->code}}) </h3>
                    <div class="d-flex">
                        @if (can('update-status', 'help-desk-tickets'))
                            <a data-toggle="modal" data-target="#updateStatusModal" class="btn btn-primary" style="margin-top:0px;margin-right:5px;"><i class="fas fa-pen"></i> Update Status</a>
                        @endif 
                        @if ($ticket->current_assignee && can('assign', 'help-desk-tickets') && $ticket->created_by != Auth::user()->id && $ticket->current_status->status != 'Closed')
                            <a data-toggle="modal" data-target="#reassignModal" class="btn btn-primary" style="margin-top:0px;margin-right:5px;"><i class="fas fa-redo-alt"></i> Re-assign</a>
                        @endif
                        @if ($ticket->created_by == Auth::user()->id)
                            <a href="{{ route('help-desk.my.tickets') }}" class="btn btn-primary" style="margin-top:0px;"><i class="fas fa-long-arrow-alt-left"></i> Back</a>
                        @else
                            <a href="{{ route('help-desk.tickets.index',['status' => $ticket->current_status->status]) }}" class="btn btn-primary" style="margin-top:0px;"><i class="fas fa-long-arrow-alt-left"></i> Back</a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="container chat-container">
                            <div class="chat-box" id="chat-box">
                                @if (!count($ticket->responses))
                                    <div class="date-group">
                                        <div class="chat-date">{{ $ticket->created_at->toDateString() }}</div>
                                        <div class="chat-message {{ $ticket->created_by == auth()->id() ? 'user' : 'other' }}">
                                            <strong>{{ $ticket->creator->name }}</strong>: {{ $ticket->message }}
                                            <span class="center-block">{{date('H:i',strtotime($ticket->created_at))}}</span> 
                                        </div>
                                    </div>
                                @else
                                    @foreach ($groupedResponses as $date => $responses)
                                    <div class="date-group">
                                        <div class="chat-date">{{ date('d-m-Y', strtotime($date)) }}</div>
                                        @if ($date == $ticket->created_at->toDateString())
                                            <div class="chat-message {{ $ticket->created_by == auth()->id() ? 'user' : 'other' }}">
                                                <strong>{{ $ticket->creator->name }}</strong>: {{ $ticket->message }}
                                                <span class="center-block">{{date('H:i',strtotime($ticket->created_at))}}</span> 
                                            </div>
                                        @endif
                                        @foreach($responses as $message)
                                            <div class="chat-message {{ $message['created_by'] == auth()->id() ? 'user' : 'other' }}">
                                                <strong>{{ $message['creator'] }}</strong>: {{ $message['message'] }}
                                                <span class="center-block">{{$message['time']}}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                                @endif
                                
                            </div>
                           
                            @if (
                                (isset($ticket->current_assignee) && 
                                ((can('', 'help-desk-tickets') && $ticket->current_assignee->assignee->id == Auth::user()->id)) || Auth::user()->role_id == 1  || Auth::user()->id == $ticket->created_by)
                            )
                             <form action="{{ route('help-desk.tickets.respond') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <textarea name="message" id="message" class="form-control" placeholder="Type a message..."></textarea>
                                    </div>
                                    <input type="hidden" name="ticket" value="{{$ticket->id}}">
                                    <button type="submit" id="respondBtn" class="btn btn-primary pull-right"><i class="fa fa-solid fa-reply"></i> Respond</button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="box-body">
                            <div class="form-group" style="margin-bottom: 0px">
                                <label for="inputEmail3" class="col-sm-5 text-left" style="padding: 0px;">Subject</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" class="form-control" value="{{$ticket->subject}}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group" style="margin-bottom: 0px">
                                <label for="inputEmail3" class="col-sm-5 text-left" style="padding: 0px;">Assignee</label>
                                <div class="col-sm-7">
                                    @if ($ticket->current_assignee)
                                        <input type="text" class="form-control" class="form-control" value="{{$ticket->current_assignee->assignee->name}}" disabled>
                                    @else
                                        @if (can('assign', 'help-desk-tickets'))
                                            <button type="submit" class="btn btn-primary btn-small" data-toggle="modal" data-target="#assignModal" style="margin-top:0px;"><i class="fas fa-plus"></i> Assign</button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if ($ticket->current_assignee)
                            <div class="box-body">
                                <div class="form-group" style="margin-bottom: 0px">
                                    <label for="inputEmail3" class="col-sm-5 text-left" style="padding: 0px;">Assigned Date</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" class="form-control" value="{{$ticket->current_assignee->created_at->format('d-m-Y H:i')}}" disabled>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if ($ticket->attachment)
                            <div class="box-body">
                                <div class="form-group" style="margin-bottom: 0px">
                                    <label for="inputEmail3" class="col-sm-5 text-left" style="padding: 0px;">Attachment</label>
                                    <div class="col-sm-7">
                                        @php
                                            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif','pdf'];
                                            $extension = pathinfo($ticket->attachment, PATHINFO_EXTENSION);
                                        @endphp
                                       
                                        @if (in_array(strtolower($extension), $imageExtensions))
                                            <a data-toggle="modal" data-target="#viewAttachmentModal" style="margin-top:0px;cursor:pointer;"><u>View Attachment</u></a>
                                        @else
                                            <a href="{{ asset('storage/' . $ticket->attachment) }}" download><u>Download Attachment</u></a>
                                        @endif
                                       
                                    </div>
                                </div>
                            </div>
                        @endif
                       
                        {{-- <div class="box-body">
                            <div class="form-group" style="margin-bottom: 0px">
                                <label for="code" class="col-sm-5 text-left" style="padding: 0px;">Date</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" class="form-control" value="{{date('d-m-Y H:i',strtotime($ticket->created_at))}}" disabled>
                                </div>
                            </div>
                        </div>  --}}
                        <div class="box-body">
                            <div class="form-group" style="margin-bottom: 0px">
                                <label for="inputEmail3" class="col-sm-5 text-left" style="padding: 0px;">Priority</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" class="form-control" value="{{$ticket->priority}}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group" style="margin-bottom: 0px">
                                <label for="inputEmail3" class="col-sm-5 text-left" style="padding: 0px;">Status</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" class="form-control" value="{{$ticket->current_status->status}}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group" style="margin-bottom: 0px">
                                <label for="inputEmail3" class="col-sm-5 text-left" style="padding: 0px;">Branch</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" class="form-control" value="{{$ticket->branch->name}}" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="viewAttachmentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document" style="height: 80%;width: 80%;">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title"> Ticket Attachment</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                    @php
                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                        $extension = pathinfo($ticket->attachment, PATHINFO_EXTENSION);
                    @endphp
                    @if (in_array(strtolower($extension), $imageExtensions))
                        <div class="box-body zoom-container">
                            <small style="display: block;"><i>Hover to zoom</i></small>
                            <img src="{{ asset('storage/' . $ticket->attachment) }}" style="width: 80%;" class="zoom-image">
                        </div>
                    @else
                        <iframe src="{{ asset('storage/' . $ticket->attachment) }}#toolbar=0" frameborder="0" width="100%" height="500px"></iframe>
                        {{-- <embed src="{{ asset('storage/' . $ticket->attachment) }}" type="application/pdf" width="100%" height="500px"> --}}

                    @endif
                    
    
                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="respondModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title"> Respond to Ticket</h3>
    
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form id="newTicketForm" action="{{ route('help-desk.tickets.respond') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <label for="message" class="control-label"> Message </label>
                                <textarea name="message" id="message" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
    
                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            
                            
                            <button type="submit" id="respondBtn" class="btn btn-primary">Respond</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reassignModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title"> Re-Assign Ticket</h3>
    
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form id="newTicketForm" action="{{ route('help-desk.tickets.assign') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="assignee" class="control-label"> Assignees </label>
                                <select name="assignee" id="assignee" class="form-control mtselect">
                                    <option value="">Choose Assignee</option>
                                    @foreach ($assignees as $assignee)
                                        <option value="{{$assignee['id']}}">{{$assignee['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                        </div>
                    </div>
    
                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <input type="hidden" class="form-control" name="ticket" value="{{$ticket->id}}">
                            <button type="submit" id="reassignBtn" class="btn btn-primary assignBtn">Assign</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="assignModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title"> Assign Ticket</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form id="newTicketForm" action="{{ route('help-desk.tickets.assign') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="assignee" class="control-label"> Assignees </label>
                                <select name="assignee" id="assignee" class="form-control mtselect">
                                    <option value="">Choose Assignee</option>
                                    <option value="{{Auth::user()->id}}">Me ({{Auth::user()->name}})</option>
                                    @foreach ($assignees as $assignee)
                                        <option value="{{$assignee['id']}}">{{$assignee['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                        </div>
                    </div>
    
                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <input type="hidden" class="form-control" name="ticket" value="{{$ticket->id}}">
                            <button type="submit" id="assignBtn" class="btn btn-primary assignBtn">Assign</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title"> Update Status</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form id="newTicketForm" action="{{ route('help-desk.tickets.status') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="status" class="control-label"> Status </label>
                                <select name="status" id="status" class="form-control mtselect">
                                    <option value="">Choose Status</option>
                                    
                                    @if ($ticket->current_status->status == 'Open' || $ticket->current_status->status == 'Re-Open')
                                        <option value="Development">Development</option>
                                    @endif
                                    @if ($ticket->current_status->status == 'Development')
                                        <option value="Completed">Complete</option>
                                    @endif
                                    @if ($ticket->current_status->status == 'Completed' )
                                        <option value="Closed">Close</option>
                                    @endif
                                    @if ($ticket->current_status->status == 'Closed' )
                                        <option value="Re-Open">Re-Open</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
    
                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <input type="hidden" class="form-control" name="ticket" value="{{$ticket->id}}">
                            <button type="submit" id="updateStatusBtn" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('uniquepagestyle')

<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
<style>
    .select2.select2-container.select2-container--default
    {
        width: 100% !important;
    }
    .chat-container {
        width: 100%;
        margin: 0 auto;
        padding: 20px;
    }
    .chat-box {
        border: 1px solid #ccc;
        height: auto;
        overflow-y: scroll;
        padding: 10px;
    }
    .chat-message {
        display: inline-block;
        padding: 10px;
        padding-bottom: 5px;
        margin-bottom: 10px;
        border-radius: 10px;
        max-width: 70%;
    }
    .chat-message span {
        font-size: 12px;
        display: block;
    }
    .chat-message.user {
        background-color: #d9edf7;
        float: right;
        text-align: right;
        clear: both;
    }
    .chat-message.other {
        background-color: #f2dede;
        float: left;
        clear: both;
    }
    .clearfix::after {
        content: "";
        clear: both;
        display: table;
    }
    .date-group{
        display: block;
        clear: both;
        position: relative;
        padding-top: 40px;
    }
    .chat-date{
        position: absolute;
        top: 0;
        left: 45%;
        background-color: #e1e1e1;
        padding: 5px 10px;
        border-radius: 11px;
    }

    .loader{
        width: 100px;
        height: 100px;
        border-radius: 100%;
        position: relative;
        margin: 0 auto;
        top: 35%;
    }
    
    /* LOADER 1 */
    
    #loader-1:before, #loader-1:after{
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 100%;
        border: 10px solid transparent;
        border-top-color: #3498db;
    }
    
    #loader-1:before{
        z-index: 100;
        animation: spin 1s infinite;
    }
    
    #loader-1:after{
        border: 10px solid #ccc;
    }
    
    @keyframes spin{
        0%{
        -webkit-transform: rotate(0deg);
        -ms-transform: rotate(0deg);
        -o-transform: rotate(0deg);
        transform: rotate(0deg);
        }
    
        100%{
        -webkit-transform: rotate(360deg);
        -ms-transform: rotate(360deg);
        -o-transform: rotate(360deg);
        transform: rotate(360deg);
        }
    }

    .zoom-container {
    position: relative;
    overflow: hidden;
    width: 100%; /* Set the desired width */
    height: 100%; /* Set the desired height */
    }

    .zoom-image {
        width: 100%;
        height: 100%;
        transition: transform 0.1s ease-out;
        transform-origin: 0 0;
    }
    .zoom-image:hover{
        cursor: zoom-in;
    }
</style>
@endsection
@section('uniquepagescript')
    <div id="loader-on" style="
    position: fixed;
    top: 0;
    text-align: center;
    display: block;
    z-index: 999999;
    width: 100%;
    height: 100%;
    background: #000000b8;
    display:none;
">
  <div class="loader" id="loader-1"></div>
</div>
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

<script>
    var form = new Form();
    $(document).ready(function() {
        $('body').addClass('sidebar-collapse');
        $('.select2').select2();
        
        $('.mtselect').each(function() {
            $(this).select2({
                dropdownParent: $(this).closest('.modal')
            });
        });
        const zoomLevel = 1.5; // Adjust the zoom level as needed
        const $zoomContainer = $('.zoom-container');
        const $zoomImage = $zoomContainer.find('.zoom-image');

        $zoomContainer.on('mousemove', function(e) {
            const offsetX = e.offsetX;
            const offsetY = e.offsetY;
            const width = $zoomContainer.width();
            const height = $zoomContainer.height();

            const moveX = (offsetX / width) * 100;
            const moveY = (offsetY / height) * 100;

            $zoomImage.css({
                'transform': `scale(${zoomLevel})`,
                'transform-origin': `${moveX}% ${moveY}%`
            });
        });

        $zoomContainer.on('mouseleave', function() {
            $zoomImage.css('transform', 'scale(1)');
        });
        $('#start_date, #end_date, #channel, #status').on('change', function() {
            let start_date = $("#start_date").val();
            let end_date = $("#end_date").val();
            $("#startDate").val(start_date);
            $("#endDate").val(end_date);
            $("#ticketsDataTable").DataTable().ajax.reload();
        });
        $('#generateExcelBtn').on('click',function(){
            location.href=`/admin/bank-statements-upload?print=excel&start_date=`+$("#start_date").val()+`&end_date=`+$("#end_date").val()+`&channel=`+$("#channel").val()+`&status=`+$("#status").val();
        });
        $('#generatePDFBtn').on('click',function(){
            location.href=`/admin/bank-statements-upload/?print=pdf&start_date=`+$("#start_date").val()+`&end_date=`+$("#end_date").val()+`&channel=`+$("#channel").val()+`&status=`+$("#status").val();
        });
        $("#ticketsDataTable").DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            pageLength: 100,
            ajax: {
                url: '{!! route('help-desk.tickets.index') !!}',
                data: function(data) {
                    data.start_date = $("#start_date").val();
                    data.end_date = $("#end_date").val();
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
                    data: 'subject', 
                    name: 'subject', 
                },
                { 
                    data: 'priority', 
                    name: 'priority', 
                },
                { 
                    data: 'branch.name', 
                    name: 'branch.name', 
                },
                { 
                    data: 'creator.name', 
                    name: 'creator.name', 
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false
                }
            ], columnDefs: [

                {
                    targets: -1,
                    render: function (data, type, row, meta) {
                        if (type === 'display') {
                            var actions = '';
                            @if (can('show', 'help-desk-tickets'))
                                var url = "{{ route('help-desk.tickets.show',':id') }}";
                                url = url.replace(':id', row.id);
                                actions += `<a href="`+url+`" title="view"><i class="fa fa-solid fa-eye"></i></a>`;
                            @endif
                            return actions;
                        }
                        return data;
                    }
                }
            ],
        });

        $('.assignBtn').on('click', function (e) {
            e.preventDefault();
            $('#loader-on').show();
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

                    $('#loader-on').hide();
                    $(".remove_error").remove();
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
                        location.reload();
                        $('#assignModal').modal('hide');
                        form.successMessage(out.message);            
                    }
                    if(out.result === -1) {
                        form.errorMessage(out.message);
                    }
                },
                
                error:function(err)
                {
                    console.log(err);
                $('#loader-on').hide();
                    $(".remove_error").remove();
                    form.errorMessage('Something went wrong');							
                }
            });
        });

        $('#respondBtn').on('click', function (e) {
            e.preventDefault();
            $('#loader-on').show();
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

                    $('#loader-on').hide();
                    $(".remove_error").remove();
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
                        location.reload();
                        $('#repondModal').modal('hide');
                        form.successMessage(out.message);            
                    }
                    if(out.result === -1) {
                        form.errorMessage(out.message);
                    }
                },
                
                error:function(err)
                {
                    console.log(err);
                $('#loader-on').hide();
                    $(".remove_error").remove();
                    form.errorMessage('Something went wrong');							
                }
            });
        });

        $('#updateStatusBtn').on('click', function (e) {
            e.preventDefault();
            $('#loader-on').show();
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

                    $('#loader-on').hide();
                    $(".remove_error").remove();
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
                        location.reload();
                        $('#updateStatusModal').modal('hide');
                        form.successMessage(out.message);            
                    }
                    if(out.result === -1) {
                        form.errorMessage(out.message);
                    }
                },
                
                error:function(err)
                {
                    console.log(err);
                $('#loader-on').hide();
                    $(".remove_error").remove();
                    form.errorMessage('Something went wrong');							
                }
            });
        });
    });

        
    </script>
@endsection