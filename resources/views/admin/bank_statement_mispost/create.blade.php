@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Update Bank Post Mispost </h3>
                    <a href="{{ route('bank-statement-mispost.index') }}" class="btn btn-primary"><i class="fas fa-long-arrow-alt-left"></i> Back</a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form action="{{ route('bank-statement-mispost.fetch_statement') }}" method="post" enctype="multipart/form-data">
                    {{ @csrf_field() }}
                    <div class="row">
                        <div class="form-group col-sm-3">
                            <label for="reference" class="control-label"> Reference. </label>
                            <input type="text" class="form-control" name="reference" id="reference">
                        </div>

                        <div class="form-group col-sm-9">
                            <label style="display: block;">&nbsp;</label>
                            <button type="submit" name="intent" class="fetch btn btn-primary">Fetch</button>
                        </div>
                    </div>
                </form>

                <hr>

                {{-- @if($processingUpload) --}}
                    <form action="{{ route('bank-statement-mispost.store') }}" method="post">
                        {{ @csrf_field() }}
                        <div class="table-responsive">
                            <table class="table table-bordered" id="transTable">
                                <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th>channel</th>
                                    <th>New Channel</th>
                                    <th>Bank Date</th>
                                    <th>New Bank Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary confirm">Confirm Update</button>
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
        $('body').addClass('sidebar-collapse');
        $('.select2').select2();
        let fetchedItems = [];

        $(document).on('click','.fetch',function(e){
            e.preventDefault();
            var reference = $('#reference').val();
            if (fetchedItems.includes(reference)) {
                form.errorMessage('Reference ' + reference + ' has already been fetched.');
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
                        
                        for (var i=0; i<item.length; i++) {
                            let channelInput = '';
                            let bankDateInput= '';
                            if (item[i].status != 'Pending') {
                                channelInput = `<input type="hidden" name="channel[${item[i].id}]" value="${item[i].channel}" required>`;
                                bankDateInput = `<input type="hidden" name="bank_date[${item[i].id}]" value="${item[i].bank_date}" required>`;
                            } else {
                                channelInput = `
                                    <select class="form-control select2 channels" name="channel[${item[i].id}]" required>
                                        <option value="">Choose Channel</option>
                                        @foreach ($channels as $channel)
                                            <option value="{{ $channel->title }}" {{ request()->channel == $channel->title ? 'selected' : '' }}>
                                                {{ $channel->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="error-message" style="color:red; display:none;"></span>
                                    `;
                                bankDateInput = `<input class="form-control" type="date" name="bank_date[${item[i].id}]" value="${item[i].bank_date}" required>`;
                            }

                            // Append new row to the table
                            $('#transTable tbody').append(`
                                <tr id="item-${item[i].id}" class="${item[i].status === 'Approved' ? 'bg-danger' : ''}">
                                    <td>${item[i].reference}</td>
                                    <td>${item[i].status}</td>
                                    <td>${item[i].amount}</td>
                                    <td>${item[i].channel}</td>
                                    <td>${channelInput}</td>   
                                    <td>${item[i].bank_date}</td>
                                    <td>${bankDateInput}</td>                                 
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm delete-item" data-id="${item[i].id}"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                    </td>
                                </tr>
                            `);
                            fetchedItems.push(item[i].reference);
                        }

                        $('#reference').val('');
                        $('#transTable tbody').find('.select2').last().select2();
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
                        form.errorMessage(errorMessage);
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
            
            let allFilled = true;

            $('.channels').each(function() {
                if ($(this).val().trim() === '') {
                    $(this).next('.select2-container').find('.select2-selection').css('border-color', 'red');
                    $(this).nextAll('.error-message').text('This field is required.').show();
                    allFilled = false;
                    return false; 
                }
            });

            if (!allFilled) {
                e.preventDefault(); 
                form.errorMessage('Please fill in all the Channels before submitting.');
                return;
            }
            $('.confirm').attr('disabled','disabled');
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
                        form.errorMessage(errorMessage);
                },
                    complete: function() {
                        // getDataBtn.prop('disabled', false).text(originalGetDataText);
                        // processDataBtn.prop('disabled', false).text(originalProcessDataText);
                    }
            });
        });
    </script>
@endsection