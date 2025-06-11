
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
                            <div class="box-header with-border">
                                <div class="box-header-flex">
                                    <h3 class="box-title">Add Asset Category </h3>
                                    <div class="d-flex">
                                        <a class="btn btn-primary mr-xs pull-right ml-2 btn-sm" href="{{route('assets.category.index')}}">Back</a>
                                    </div>
                                </div>
                            </div>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <form action="{{route('assets.category.save')}}"  method="post"  data-modal="newAsset"  class="addAssetParts">
                                    {{csrf_field()}}
                                    <div class="row" style="margin-bottom:10px">
                                        <div class="col-sm-5 " style="padding-top:5px;">
                                            <label>Category Code:</label>
                                        </div>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" name="category_code">
                                        </div>
                                    </div>
                                    <div class="row" style="margin-bottom:10px">
                                        <div class="col-sm-5 " style="padding-top:5px;">
                                            <label>Category Description:</label>
                                        </div>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" name="category_description">
                                        </div>
                                    </div>
                    
                                    <div class="row" style="margin-bottom:10px">
                                        <div class="col-sm-5 " style="padding-top:5px;">
                                            <label>Fixed Asset Cost GL Code:</label>
                                        </div>
                                        <div class="col-sm-7">
                                            <select  name="fixed_asset_id" class="form-control select2Select" >
                                                @foreach ($profit_loss as $item)
                                                    <option value="{{$item->id}}">{{$item->account_code}} - {{$item->account_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-bottom:10px">
                                        <div class="col-sm-5 " style="padding-top:5px;">
                                            <label>Profit and Loss Depreciation GL Code:</label>
                                        </div>
                                        <div class="col-sm-7">
                                            <select  name="profit_loss_depreciation_id" class="form-control select2Select" >
                                                @foreach ($gl as $item)
                                                    <option value="{{$item->id}}">{{$item->account_code}} - {{$item->account_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-bottom:10px">
                                        <div class="col-sm-5 " style="padding-top:5px;">
                                            <label>Profit or Loss on Disposal GL Code:</label>
                                        </div>
                                        <div class="col-sm-7">
                                            <select  name="profit_loss_disposal_id" class="form-control select2Select" >
                                                @foreach ($gl as $item)
                                                    <option value="{{$item->id}}">{{$item->account_code}} - {{$item->account_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-bottom:10px">
                                        <div class="col-sm-5 " style="padding-top:5px;">
                                            <label>Balance Sheet Accumulated Depreciation GL Code:</label>
                                        </div>
                                        <div class="col-sm-7">
                                            <select  name="balance_sheet_id" class="form-control select2Select" >
                                                @foreach ($profit_loss as $item)
                                                    <option value="{{$item->id}}">{{$item->account_code}} - {{$item->account_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                    
                                    <br>
                                    <div class=""><button type="submit" class="btn btn-info float-right">Save</button></div>
                                </form>
                            </div>
                        </div>
                    </div>


    </section>
  
    @endsection
    @section('uniquepagestyle')

<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
 <link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">

 <style type="text/css">
   .select2{
    width: 100% !important;
   }
   #note{
    height: 60px !important;
   }
   .align_float_right
{
  text-align:  right;
}
 </style>
@endsection
    @section('uniquepagescript')
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script src="{{asset('public/js/sweetalert.js')}}"></script>
<script src="{{asset('public/js/form.js')}}"></script>
    <script type="text/javascript">
$(function() {
    var form = new Form();
    $(document).on('submit','.addAssetParts',function (e) { 
        e.preventDefault();
       
        var data = $(this).serialize();
            var url = $(this).attr('action');
            var method = $(this).attr('method');
            var $this = $(this);
            var form = new Form();

            $.ajax({
                url:url,
                method:method,
                data:data,
                success:function(out)
                {
                    $(".remove_error").remove();
                    if(out.result == 0) {
                        console.log(out.errors);
                        for(let i in out.errors) {                        
                            $this.find("[name='"+i+"']").
                            parent().
                            append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                        }
                    }
                    if(out.result === 1) {
                        form.successMessage(out.message);
                        $this.find('input:not(:hidden)').val('');
                        $this.find('textarea').val('');
                       
                        if(out.refresh)
                        {
                            setTimeout(() => {
                                location.href = '{{route('assets.category.index')}}';
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
                    $('.loder').css('display','none');
                    form.errorMessage('Something went wrong');							
                }
            });
    });
    $('.select2Select').parent().css('text-align','left');
    $('.select2Select').select2();
});


</script>
@endsection
