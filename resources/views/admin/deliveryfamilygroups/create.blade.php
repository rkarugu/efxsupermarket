
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
        <form class="validate form-horizontal"  role="form" method="POST" action="{{ route($model.'.store') }}" enctype = "multipart/form-data">
            {{ csrf_field() }}
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Name</label>
                    <div class="col-sm-9">
                        {!! Form::text('name', null, ['maxlength'=>'255','placeholder' => 'Name', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>
          


             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Delivery Sub Major Group name</label>
                    <div class="col-sm-9">
                        {!!Form::select('parent_id', $getParentList, null, ['placeholder'=>'Select Sub Major group ', 'class' => 'form-control','required'=>true,'title'=>'Please sub major group '  ])!!} 
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Description</label>
                    <div class="col-sm-9">
                        {!! Form::textarea('description', null, ['maxlength'=>'1000','placeholder' => 'Description', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

           
           
           
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Image</label>
                    <div class="col-sm-9">
                        <input type = "file" name = "image" title = "Please select image" required accept="image/*" >
                    </div>
                </div>
            </div>
             
             


            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
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


