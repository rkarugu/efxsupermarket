
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
                                <form action="{{route('fixed_asset_maintenance_task_update')}}"  method="post"  data-modal="newAsset"  class="addAssetParts">
                                    {{csrf_field()}}
                                    <input type="hidden" name="id" value="{{$task->id}}">
                                    <div class="row" style="margin-bottom:10px">
                                        <div class="col-sm-3 " style="padding-top:5px;">
                                            <label>Asset to Maintain:</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select name="wa_asset_category_id" class="form-control select2Select" >
                                                @foreach ($categories as $item)
                                                    <option value="{{$item->id}}" @if($task->wa_asset_category_id == $item->id) selected @endif>{{$item->asset_description_short}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-bottom:10px">
                                        <div class="col-sm-3 " style="padding-top:5px;">
                                            <label>Description:</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <textarea type="text" name="task_description" class="form-control">{{$task->task_description}}</textarea>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-bottom:10px">
                                        <div class="col-sm-3 " style="padding-top:5px;">
                                            <label>Days Before Due:</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="number" name="days_before_due" class="form-control" value="{{$task->days_before_due}}">
                                        </div>
                                    </div>
                                    <div class="row" style="margin-bottom:10px">
                                        <div class="col-sm-3 " style="padding-top:5px;">
                                            <label>Responsible:</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select name="responsible_id" class="form-control select2Select" >
                                                @foreach ($users as $item)
                                                    <option value="{{$item->id}}" @if($task->responsible_id == $item->id) selected @endif>{{$item->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-bottom:10px">
                                        <div class="col-sm-3 " style="padding-top:5px;">
                                            <label>Manager:</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select name="manager_id" class="form-control select2Select" >
                                                @foreach ($users as $item)
                                                    <option value="{{$item->id}}" @if($task->manager_id == $item->id) selected @endif>{{$item->name}}</option>
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
                                location.href = '{{route('fixed_asset_maintenance_task_list')}}';
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
