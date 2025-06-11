
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
                        <div class="box-header with-border">
                            <div class="box-header-flex">
                                <h3 class="box-title">Edit Asset Location </h3>
                                <div class="d-flex">
                                    <a class="btn btn-primary mr-xs pull-right ml-2 btn-sm" href="{{route('assets.location.index')}}">Back</a>
                                </div>
                            </div>
                        </div>
                        <div class="box-header with-border no-padding-h-b">
                            
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <form action="{{route('assets.location.update')}}"  method="post"  data-modal="newAsset"  class="addAssetParts">
                                    {{csrf_field()}}
                                    <input type="hidden" name="id" value="{{$data->id}}">
                                    <div class="row" style="margin-bottom:10px">
                                        <div class="col-sm-3 " style="padding-top:5px;">
                                            <label>Location ID:</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" name="location_id" class="form-control" value="{{$data->location_ID}}">
                                        </div>
                                    </div>
                                    <div class="row" style="margin-bottom:10px">
                                        <div class="col-sm-3 " style="padding-top:5px;">
                                            <label>Location Description:</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" name="location_description" class="form-control" value="{{$data->location_description}}">
                                        </div>
                                    </div>
                                    <div class="row" style="margin-bottom:10px">
                                        <div class="col-sm-3 " style="padding-top:5px;">
                                            <label>Parent Location:</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select name="location_parent" class="form-control select2Select" >
                                                <option value="">Select Parent</option>
                                                @foreach ($asset_location as $item)
                                                    <option value="{{$item->id}}" @if ($data->wa_asset_locations_id  == $item->id)
                                                        selected
                                                    @endif>{{$item->location_ID}} - {{$item->location_description}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-bottom:10px">
                                        <div class="col-sm-3 " style="padding-top:5px;">
                                            <label for="branch">Branch</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="form-control select2" name="branch" id="branch">
                                                <option value="all">Choose Branch</option>
                                                @foreach ($branches as $branch)
                                                    <option value="{{$branch->id}}" @if ($data->restaurant_id  == $branch->id)
                                                        selected
                                                    @endif>{{$branch->name}}</option>
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
                                location.href = '{{route('assets.location.index')}}';
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
