
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
         {!! Form::model($row, ['method' => 'PATCH','route' => [$model.'.update', $row->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}
                <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Title</label>
                    <div class="col-sm-10">
                        {!! Form::text('title', null, ['maxlength'=>'255','placeholder' => 'Title', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Display Order</label>
                    <div class="col-sm-10">
                    {!!Form::select('display_order',array_combine(range(1,100), range(1,100)), null, ['placeholder'=>'Select display order ', 'class' => 'form-control','required'=>true,'title'=>'Please display order'  ])!!}
                        
                    </div>
                </div>
            </div>
           
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Image</label>
                    <div class="col-sm-10">
                       <input type = "file" name = "image_update" title = "Please select image"  accept="image/*">
                        <img width="100px" height="100px;"src="{{ asset('uploads/advertisements/thumb/'.$row->image) }}">
                    </div>
                </div>
            </div>
             
             


            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</section>
@endsection

@section('uniquepagestyle')
<link href="{{asset('assets/admin/jquery.timepicker.css')}}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
<script src="http://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyCGWVuFQaOxDNtbHA6zqeFat4O6pFshURk"></script>



 <script src="{{asset('assets/admin/jquery.timepicker.js')}}"></script>

  

 

<script type="text/javascript">
  
 $(function () {
   $('.timepicker').timepicker({ 'timeFormat': 'H:i' });
});



 function initialize() {
    var input = document.getElementById('search_location');
    var options = {};
                 
   var autocomplete = new google.maps.places.Autocomplete(input, options);
    google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();
           /* var lat = place.geometry.location.lat();
            var lng = place.geometry.location.lng();
            $("#latitude").val(lat);
            $("#longitude").val(lng);*/
        });
}
             
google.maps.event.addDomListener(window, 'load', initialize);
</script>


@endsection


