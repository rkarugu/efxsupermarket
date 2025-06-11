@extends('layouts.admin.admin')
@section('content')
    <!-- Main content -->
    <section class="content">
        <form id="newTicketForm" action="{{ route('help-desk.tickets.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="box-header-flex">
                        <h3 class="box-title" style="font-weight: 500 !important;"> New Ticket </h3>
                        <div class="d-flex">
                        
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="session-message-container">
                        @include('message')
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="module" class="col-sm-2 control-label">Module</label>
                        <div class="col-sm-10">
                            <select name="module" id="module" class="form-control select2">
                                <option value="">Choose Module</option>
                                @if (can('view', 'sales-and-receivables'))
                                    <option value="Sales & Receivables">Sales & Receivables</option>
                                @endif
                                @if (can('view', 'purchases'))
                                    <option value="Purchases">Purchases</option>
                                @endif
                                @if (can('view', 'account-payables'))
                                    <option value="Account Payables">Account Payables</option>
                                @endif
                                @if (can('view', 'inventory'))
                                    <option value="Inventory">Inventory</option>
                                @endif
                                @if (can('view', 'genralledger'))
                                    <option value="General Ledger">General Ledger</option>
                                @endif
                                @if (can('view', 'fleet-management-module'))
                                    <option value="Fleet Management">Fleet Management</option>
                                @endif
                                @if (can('view', 'communication-center'))
                                    <option value="Communications Center">Communications Center</option>
                                @endif
                                @if (can('view', 'financial-management'))
                                    <option value="System Administration">System Administration</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="priority" class="col-sm-2 control-label">Priority</label>
                        <div class="col-sm-10">
                            <select name="priority" id="priority" class="form-control select2">
                                <option value="">Choose Priority</option>
                                <option value="High">High</option>
                                <option value="Medium">Medium</option>
                                <option value="Low">Low</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Category</label>
                        <div class="col-sm-10">
                            <select name="category" id="category" class="form-control select2">
                                <option value="">Choose Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{$category->id}}">{{ $category->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="subject" class="col-sm-2 control-label">Subject</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="subject" id="subject">
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="subject" class="col-sm-2 control-label">Message</label>
                        <div class="col-sm-10">
                            <textarea name="message" id="message" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="attachment" class="col-sm-2 control-label">Attachment</label>
                        <div class="col-sm-10">
                            <input type="file" id="attachment" name="attachment">
                            <p id="attachmentLabel" class="help-block"></p>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit" id="newTicketBtn" class="btn btn-primary"><i class="fa fa-solid fa-save"></i> Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>

    <div class="modal fade" id="newTicketModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title"> New Ticket</h3>
    
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                
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
        $('.select2').select2();
        $('#attachment').change(function () {
            var fileName = $(this).val().split('\\').pop();
            $('#attachmentLabel').text(fileName);
        });
        $('#newTicketBtn').on('click', function (e) {
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
                        $("#module").val('');
                        $("#priority").val('');
                        $('category').val('');
                        $('#attachmentLabel').text('');
                        $('#newTicketForm').trigger("reset");
                        form.successMessage(out.message);    
                        setTimeout(
                        function() 
                        {                        
                            location.href = `{{route('help-desk.my.tickets')}}`;
                        }, 3000);         
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