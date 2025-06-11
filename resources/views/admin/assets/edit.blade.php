
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
                            <div class="col-md-12 no-padding-h">
                                <form action="{{route('assets.edit')}}"  method="post" class="editModal addAssetParts">
                                    {{csrf_field()}}
                                    <input type="hidden" name="id" value="{{$data->id}}">
                                <div class="row" style="margin-bottom:10px">
                                    <div class="col-sm-3 " style="padding-top:5px;">
                                        <label>Asset Description (Short):</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="asset_description_short" value="{{$data->asset_description_short}}">
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom:10px">
                                    <div class="col-sm-3 " style="padding-top:5px;">
                                        <label>Asset Description (Long):</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <textarea type="text" class="form-control" name="asset_description_long">{{$data->asset_description_long}}</textarea>
                                    </div>
                                </div>
                
                                <div class="row" style="margin-bottom:10px">
                                    <div class="col-sm-3 " style="padding-top:5px;">
                                        <label>Asset Category:</label>
                                    </div>
                                    <div class="col-sm-5">
                                        <select  class="form-control assetCategory select2Select" name="wa_asset_categorie_id">
                                            @foreach ($asset_category as $item)
                                            <option value="{{$item->id}}" @if($data->wa_asset_categorie_id == $item->id) selected @endif>{{$item->category_code}} - {{$item->category_description}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-4 text-left" style="padding-top:5px;">
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom:10px">
                                    <div class="col-sm-3 " style="padding-top:5px;">
                                        <label>Asset Location:</label>
                                    </div>
                                    <div class="col-sm-5">
                                        <select  class="form-control locationId select2Select" name="wa_asset_location_id">
                                            @foreach ($asset_location as $item)
                                                <option value="{{$item->id}}" @if($data->wa_asset_location_id == $item->id) selected @endif>{{$item->location_ID}} - {{$item->location_description}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-4 text-left" style="padding-top:5px;">
                                    </div>
                                </div>
                
                
                                <div class="row" style="margin-bottom:10px">
                                    <div class="col-sm-3 " style="padding-top:5px;">
                                        <label>Bar Code:</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="bar_code" value="{{$data->bar_code}}">
                                    </div>
                                </div>
                
                                <div class="row" style="margin-bottom:10px">
                                    <div class="col-sm-3 " style="padding-top:5px;">
                                        <label>Serial Number:</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="serial_number" value="{{$data->serial_number}}">
                                    </div>
                                </div>
                
                                <div class="row" style="margin-bottom:10px">
                                    <div class="col-sm-3 " style="padding-top:5px;">
                                        <label>Depreciation Type:</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select  class="form-control select2Select" name="wa_asset_depreciation_id">
                                            @foreach ($asset_depreciation as $item)
                                                <option value="{{$item->id}}" @if($data->wa_asset_depreciation_id == $item->id) selected @endif>{{$item->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom:10px">
                                    <div class="col-sm-3 " style="padding-top:5px;">
                                        <label>Depreciation Rate:</label>
                                    </div>
                                    <div class="col-sm-5">
                                        <input type="number"  class="form-control" name="depreciation_rate" value="{{$data->depreciation_rate}}"> 
                                    </div>
                                    <div class="col-sm-4 text-left" style="padding-top:5px;">
                                        %
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
                                location.href = '{{route('assets.index')}}';
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
function openEditModal(input) {
    url = $(input).attr('href');
    var form = new Form();

    $.ajax({
        type: "GET",
        url: url,
        data: {
            '_token':'{{csrf_token()}}',
        },
        success: function (response) {
            for(let i in response) {     
                $(".editModal [name='"+i+"']").val(response[i]);                    
            }
            $('#editAsset').modal('show');
            $('.select2Select').select2();
        },
        error:function(err)
        {
            $('.loder').css('display','none');
            form.errorMessage('Something went wrong');							
        }
    });
}

</script>
@endsection
