
@extends('layouts.admin.admin')

@section('content')
<style>
    .span-action {

    display: inline-block;
    margin: 0 3px;

}
</style>
<div class="row ">
    <div class="col-md-12" style="padding-left: 29px;">
        <a class="btn btn-danger remove-btn mr-xs  ml-2 btn-sm" style="margin-right:51px" href="{{route('banking.transfer.list')}}"><i class="fa fa-arrow-left" aria-hidden="true"></i>   Back</a>
    </div>
</div>
    <!-- Main content -->
    <form action="{{route('banking.transfer.store')}}" method="post" class="addbills">
        {{csrf_field()}}
        <section class="content" style="min-height: 0;">
            <div class="box box-primary">
                <div class="box-header with-border no-padding-h-b">                
                    @include('message')
                    <div class="col-md-12 no-padding-h">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                  <label for="">Transfer Funds From</label>                                  
                                  <select class="form-control category_list" name="transfer_from" id="selectsupplier">
                                  </select>
                                </div>
                            </div>
                            <div class="col-sm-4 ">
                                <br>
                                <div class="form-group">
                                    <h5>Balance: <span class="balance">0</span></h5>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                  <label for="">Transfer Funds To</label>                                  
                                  <select class="form-control category_list" name="transfer_to" >
                                  </select>
                                </div>
                            </div>
                            <div class="col-sm-4 ">
                                <br>
                                <div class="form-group">
                                    <h5>Balance: <span class="balance">0</span></h5>
                                </div>
                            </div>
                        </div>
                       
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                  <label for="">Transfer Amount</label>                                  
                                  <input type="number" name="amount" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-4 ">
                                <div class="form-group">
                                    <label for="">Date</label>                                  
                                    <input type="date" name="date" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-8">
                                <div class="form-group">
                                  <label for="">Memo</label>                                  
                                  <textarea name="memo" class="form-control" cols="30" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-sm-8">
                               <button type="submit" class="btn btn-danger">Submit</button>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </section>

    </form>
   
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
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">
    $('.category_list').change(function (e) {
        e.preventDefault();
        var val = $(this).val();
        var $this = $(this);
        $.ajax({
            type: "POST",
            url: "{{route('banking.transfer.fetch')}}",
            data: {
                '_token':'{{csrf_token()}}',
                'id':val
            },
            success: function (response) {
                $this.parents('.row').find('.balance').html(response.amount);
            }
        });
    });
        var category_list = function(){
            $(".category_list").select2(
            {
                placeholder:'Select Fund Account',
                ajax: {
                    url: '{{route("expense.paymentAccount")}}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                            var res = data.map(function (item) {
                                return {id: item.id, text: item.text};
                            });
                        return {
                            results: res
                        };
                    }
                },
            });
        }
        $(document).ready(function () {
            category_list();
        });
    var form = new Form();

        $(document).on('submit','.addbills',function(e){
            e.preventDefault();
            // form.successMessage('Bill Added');
            // return true;
            var postData = new FormData($(this)[0]);
			var url = $(this).attr('action');
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
                        if(out.location)
                        {
                            setTimeout(() => {
                                location.href = out.location;
                            }, 1000);
                        }
                    }
                    if(out.result === -1) {
                        form.errorMessage(out.message);
                    }
                },
                
                error:function(err)
                {
                    $(".remove_error").remove();
                    form.errorMessage('Something went wrong');							
                }
            });
        });
    </script>
@endsection
