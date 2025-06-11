@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Suspend Transactions </h3>
                    <a href="{{ route('suspended-transactions.index') }}" class="btn btn-primary"><i class="fas fa-long-arrow-alt-left"></i> Back</a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form action="{{ route('suspended-transactions.fetch_transaction') }}" method="post" enctype="multipart/form-data">
                    {{ @csrf_field() }}

                    {{-- <div class="row">
                        <div class="form-group col-sm-3">
                            <label for="cleanup_list" class="control-label"> Transaction List </label>
                            <input type="file" class="form-control" name="cleanup_list" id="cleanup_list">
                            <label class="custom-file-label" id="cleanup_list_label"></label>
                        </div>

                        <div class="form-group col-sm-9">
                            <label style="display: block;">&nbsp;</label>
                            <input type="submit" name="intent" value="Upload" class="btn btn-primary">
                        </div>
                    </div> --}}
                    <div class="row">
                        <div class="form-group col-sm-3">
                            <label for="document_no" class="control-label"> Document No. </label>
                            <input type="text" class="form-control" name="document_no" id="document_no">
                        </div>

                        <div class="form-group col-sm-9">
                            <label style="display: block;">&nbsp;</label>
                            <button type="submit" name="intent" class="fetch btn btn-primary">Fetch</button>
                        </div>
                    </div>
                </form>

                <hr>

                {{-- @if($processingUpload) --}}
                    <form action="{{ route('suspended-transactions.store') }}" method="post">
                        {{ @csrf_field() }}

                        {{-- <input type="hidden" name="records" value="{{ json_encode($trans) }}"> --}}

                        <div class="table-responsive">
                            <table class="table table-bordered" id="transTable">
                                <thead>
                                <tr>
                                    <th>Document No</th>
                                    <th>Route</th>
                                    <th>Trans Date</th>
                                    <th>Input Date</th>
                                    <th>Reference</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th>Reason</th>
                                </tr>
                                </thead>
                                <tbody>
                                {{-- @foreach($trans as $tran)
                                    <tr>
                                        <th style="width: 3%;" scope="row">{{ $loop->index + 1 }}</th>
                                        <td>{{ $tran->document_no }}</td>
                                        <td>{{ $tran->customer_name }}</td>
                                        <td>{{ $tran->trans_date }}</td>
                                        <td>{{ $tran->created_at }}</td>
                                        <td>{{ $tran->reference }}</td>
                                        <td>{{ ucfirst($tran->verification_status) }}</td>
                                        <td>{{ manageAmountFormat($tran->amount) }}</td>
                                        <td></td>
                                    </tr>
                                @endforeach --}}
                                </tbody>
                                {{-- <tfoot>
                                <tr>
                                    <th colspan="7" scope="row">SUSPEND TOTAL</th>
                                    <th colspan="2" scope="row">{{ manageAmountFormat(collect($trans)->sum('amount')) }}</th>
                                </tr>
                                </tfoot> --}}
                            </table>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary confirm">Confirm Suspension</button>
                        </div>
                    </form>
                {{-- @endif --}}
            </div>
        </div>
    </section>
    <span class="btn-loader" style="display:none;">
        <img src="<?= asset('/assets/admin/images/loader.gif') ?>" alt="Loader"/>
    </span>
@endsection

@section('uniquepagestyle')
    
@endsection

@section('uniquepagescript')
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script>
        $('body').addClass('sidebar-collapse');
        let fetchedItems = [];

        $(document).on('click','.fetch',function(e){
            e.preventDefault();
            var doc_no = $('#document_no').val();
            if (fetchedItems.includes(doc_no)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: 'Document No ' + doc_no + ' has already been fetched.',
                });
                return;
            }
            
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
                        
                        let item = out.data;

                        // Append new row to the table
                        $('#transTable tbody').append(`
                            <tr id="item-${item.id}">
                                <td>${item.document_no}</td>
                                <td>${item.customer_name}</td>
                                <td>${item.trans_date}</td>
                                <td>${item.created_at}</td>
                                <td>${item.reference}</td>
                                <td>${item.verification_status}</td>
                                <td>${item.amount}</td>
                                <td><input type="text" class="form-control reasons" name="reason[${item.id}]"/></td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm delete-item" data-id="${item.id}"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                </td>
                            </tr>
                        `);
                        fetchedItems.push(item.document_no);
                        $('#document_no').val('');
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
                },
                    complete: function() {
                        // getDataBtn.prop('disabled', false).text(originalGetDataText);
                        // processDataBtn.prop('disabled', false).text(originalProcessDataText);
                    }
            });
        });

        $(document).on('click','.delete-item',function(e){
            e.preventDefault();
            let id = $(this).data('id');
            $(`#item-${id}`).remove();
        });

        $(document).on('click','.confirm',function(e){
            e.preventDefault();
            $('.confirm').attr('disabled','disabled');
            $('.btn-loader').show();
            let allFilled = true;

            $('.reasons').each(function() {
                if ($(this).val().trim() === '') {
                    allFilled = false;
                    return false; 
                }
            });

            if (!allFilled) {
                e.preventDefault(); 
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: 'Please fill in all the reasons before submitting.',
                });
                return;
            }

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
                        location.reload();
                    }
                    if(response.result === -1) {
                        form.errorMessage(response.message);
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
                },
                    complete: function() {
                        // getDataBtn.prop('disabled', false).text(originalGetDataText);
                        // processDataBtn.prop('disabled', false).text(originalProcessDataText);
                    }
            });
        });
    </script>
@endsection