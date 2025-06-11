
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
                    <label for="inputEmail3" class="col-sm-2 control-label">Key</label>
                    <div class="col-sm-10">
                        {!! Form::text('name', null, [
                                                                    'maxlength'=>'191',
                                                                    'class'=>'form-control','title'=>'Please enter key',
                                                                    'placeholder'=>'Key','required'=>true,'readonly'=>true]) !!}
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label"> @if($row->parameter_type=='file')
                                    Upload Document:
                                   @else
                                    Value:
                                   @endif</label>
                    <div class="col-sm-10">
                    @if($row->parameter_type=='number')
                                       {!! Form::number('description', null,['maxlength'=>'500','class'=>'form-control','placeholder'=>'Value','required'=>true,'min'=>0]) !!}

                                       @elseif($row->parameter_type=='email')
                                       {!! Form::email('description', null,['maxlength'=>'500','class'=>'form-control','placeholder'=>'Value','required'=>true,'min'=>0]) !!}

                                       @elseif($row->parameter_type=='url')
                                       {!! Form::url('description', null,['maxlength'=>'500','class'=>'form-control','placeholder'=>'Value','required'=>true]) !!}

                                        @elseif($row->parameter_type=='boolean')
                                       {!! Form::select('description', ['1'=>'Yes','0'=>'No'],null,['class'=>'form-control','required'=>true]) !!}

                                        @elseif($row->parameter_type=='happyhours')

                                          <?php 

                                            if(isset($row->description))
                                            {
                                              $explode = explode('-',$row->description);
                                            }
                                            //dd($explode);

                                          ?>
                                        <div class="col-sm-4">
                                       {!! Form::text('start_from', isset($explode[0])?$explode[0]:null,['class'=>'timepicker form-control','placeholder'=>'Start Time','required'=>true]) !!}
                                       </div>
                                       <div class="col-sm-1">
                                       To
                                       </div>
                                       <div class="col-sm-4">
                                       {!! Form::text('end_from', isset($explode[1])?$explode[1]:null,['class'=>' timepicker form-control','placeholder'=>'End Time','required'=>true]) !!}
                                       </div>

                                       @elseif($row->parameter_type=='file')
                                        <input type = "file" class="form-control" name = "description" >

                                    @else
                                       {!! Form::text('description', null,['maxlength'=>'500','class'=>'form-control','title'=>'Please enter value','placeholder'=>'Value','required'=>true]) !!}
                                    @endif
                        
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


