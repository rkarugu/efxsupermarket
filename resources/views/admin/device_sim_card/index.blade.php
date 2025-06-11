@extends('layouts.admin.admin')

@section('content')
<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="box-header-flex">
                <h3 class="box-title"> {{ $title }} </h3>
                <div>
                    @if (can('add', $model))
                        <a href="{{ route($model.'.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> {{ $title }}</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="session-message-container">
                @include('message')
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="deviceSimCardDataTable">
                    <thead>
                    <tr>
                        <th style="width: 3%;">#</th>
                        <th>IMEI</th>
                        <th>Device</th>
                        <th class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($simCards as $sim)
                            <tr>
                                <th style="width: 3%;" scope="row"> {{ $loop->index + 1 }} </th>
                                <td> {{ $sim->phone_number }} </td>
                                <td> {{ $sim->device?->device_no }} </td>
                                <td class="text-right">
                                    @if (can('edit', $model))
                                        <a href="{{ route($model .'.edit',$sim->id) }}" class="" style="margin-left: 10px;"><i class="fas fa-pen"></i></a>
                                    @endif
                                    @if (can('delete', $model))
                                        <button  onclick="deletePop({{$sim->id}},'{{$sim->phone_number}}')" class="" data-id="{{ $sim->id }}" style="border:none;background-color:#fff;color:red;"><i class="fa fa-trash"
                                            aria-hidden="true"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Delete Device Sim Card</h3>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <form id="manualUploadForm" action="" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="box-body">
                    <form id="fetchPaymentForm" action="" method="post">
                        <p>Are you sure you want to Delete <b id="deleteTitle"></b></p>
                </div>

                <div class="box-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <input type="hidden" value="" id="deleteId" name="deleteId">
                        <button type="button" id="confirmDeleteBtn" class="btn btn-primary">Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('uniquepagestyle')
    <style type="text/css">
            
     /* ALL LOADERS */
     
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

@push('scripts')
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
    <script>
        $(document).ready(function () {
            $('#deviceSimCardDataTable').DataTable({
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

        var form = new Form();

    $(document).on('click','#confirmDeleteBtn',function(e){
        e.preventDefault();
        
        $('#deleteModal').modal('hide');
        var postData = new FormData($(this).parents('form')[0]);
        var url = "{{ route($model.'.delete',':id') }}";
        var id = $('#deleteId').val();
            url = url.replace(':id', id);
            console.log(url);
        postData.append('_token',$(document).find('input[name="_token"]').val());
        
        $.ajax({
            url:url,
            data:postData,
            contentType: false,
            cache: false,
            processData: false,
            method:'GET',
            success:function(out){

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
                    form.successMessage(out.message);
                    setTimeout(
                    function() 
                    {                        
                        location.href = `{{route($model.'.index')}}`;
                    }, 3000);                    
                    
                }
                if(out.result === -1) {
                    let errorMessage = '';
                        if (out.message) {
                            errorMessage = out.message
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            html: errorMessage,
                        });
                }
            },
            
            error:function(err)
            {
                $(".remove_error").remove();
                
                let errorMessage = '';
                    if (err?.responseJSON?.errors) {
                        for (let key in err.responseJSON.errors) {
                            if (err.responseJSON.errors.hasOwnProperty(key)) {
                                errorMessage += err.responseJSON.errors[key].join('<br>') + '<br>';
                            }
                        }
                    } else if (err?.responseJSON?.error) {
                        errorMessage = err.responseJSON.error
                    }else{
                        errorMessage = 'Something went wrong.'
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        html: errorMessage,
                    });
            }
        });
    }); 

    function deletePop(id,title) {
            $('#deleteId').val(id);
            $('#deleteTitle').text(title);
            $('#deleteModal').modal('show');
        }
    </script>
@endpush