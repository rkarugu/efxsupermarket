
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
                    <label for="inputEmail3" class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-10">
                        {!! Form::text('name', null, ['maxlength'=>'255','placeholder' => 'Name', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Available From</label>
                    <div class="col-sm-10">
                        {!! Form::text('available_from', null, ['maxlength'=>'255','placeholder' => 'Available From', 'required'=>true, 'class'=>'form-control timepicker','id'=>'timepicker1']) !!}  
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Available To</label>
                    <div class="col-sm-10">
                        {!! Form::text('available_to', null, ['maxlength'=>'255','placeholder' => 'Available To', 'required'=>true, 'class'=>'form-control timepicker']) !!}  
                    </div>
                </div>
            </div>


             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Sub Major Group name</label>
                    <div class="col-sm-10">
                        {!!Form::select('parent_id', $getParentList, null, ['placeholder'=>'Select Sub Major group ', 'class' => 'form-control select2','required'=>true,'title'=>'Please sub major group '  ])!!} 
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Description</label>
                    <div class="col-sm-10">
                        {!! Form::textarea('description', null, ['maxlength'=>'1000','placeholder' => 'Description', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Another layout</label>
                    <div class="col-sm-10">
                        {!! Form::checkbox('is_have_another_layout', null) !!}  
                    </div>
                </div>
            </div>
           
           
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Restaurant</label>
                    <div class="col-sm-10">
                        {!!Form::select('restaurant_id', getBranchesDropdown(), null, ['placeholder'=>'Select Branch', 'class' => 'form-control','title'=>'Please sub major group '  ])!!} 
                    </div>
                </div>
            </div>
            
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Store Location</label>
                    <div class="col-sm-10">
                        {!!Form::select('wa_location_and_store_id', getStoreLocationDropdown(), null, ['placeholder'=>'Select Store Location', 'class' => 'form-control','title'=>'Please sub major group '  ])!!} 
                    </div>
                </div>
            </div>
            
            
            
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Image</label>
                    <div class="col-sm-10">
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
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
<script src="http://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyCGWVuFQaOxDNtbHA6zqeFat4O6pFshURk"></script>



 <script src="{{asset('assets/admin/jquery.timepicker.js')}}"></script>

  <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    $(function () {
    $('.select2').select2();
});
</script>

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


