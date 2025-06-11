
@extends('layouts.admin.admin')

@section('content')

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">                            
                <div class="col-md-12 no-padding-h">
                    @if(!isset($assets))
                    <form action="" method="get">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                  <label for="">Asset category</label>
                                  <select name="asset_category" id="asset_category" class="form-control" required>
                                    @foreach ($asset_category as $item)
                                        <option value="{{$item->id}}">{{$item->category_code}}</option>
                                    @endforeach
                                  </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                  <label for="">Asset location</label>
                                  <select name="asset_location" id="asset_location" class="form-control">
                                    <option value="" selected>Any Asset Location</option>
                                    @foreach ($asset_location as $item)
                                        <option value="{{$item->id}}">{{$item->location_ID}}</option>
                                    @endforeach
                                  </select>
                                </div>
                            </div>
                            <div class="col-md-12" style="text-align: center">
                                <button type="submit" class="btn btn-sm btn-primary">Search Now</button>
                            </div>
                        </div>
                    </form>
                    @else
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Asset ID</th>
                                    <th>Description</th>
                                    <th>Serial number</th>
                                    <th>Purchase Cost</th>
                                    <th>Total Depreciation</th>
                                    <th>Current Location</th>
                                    <th colspan="2">Move To</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assets as $item)                                    
                                    <tr>
                                        <td style="width: 5%">{{$item->id}}</td>
                                        <td style="width: 10%">{{$item->asset_description_short}}</td>
                                        <td style="width: 10%">{{$item->serial_number}}</td>
                                        <td style="width: 10%">0</td>

                                        <td style="width: 10%">0</td>
                                        <td style="width: 10%">{{$item->location->location_ID}}</td>
                                        <td style="width: 20%">
                                            <select name="asset_location" id="asset_location" class="select2Select form-control">
                                                @foreach ($asset_location as $item2)
                                                    <option value="{{$item2->id}}">{{$item2->location_ID}}</option>
                                                @endforeach
                                              </select>
                                        </td>
                                        <td style="width: 10%">
                                            {{csrf_field()}}
                                            <input type="hidden" name="id" value="{{$item->id}}">
                                            <button type="submit" class="addAssetParts btn btn-sm btn-danger">Move</button>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    @endif
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
$(document).on('click','.addAssetParts',function (e) { 
    e.preventDefault();
   
    var data = $(this).parents('tr').find('input,select').serialize();

        var url = '{{route('changeAssetLocationUpdate')}}';
        var method = 'POST';
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
                            location.href = '{{route('changeAssetLocation')}}';
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
