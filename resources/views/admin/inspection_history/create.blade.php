
@extends('layouts.admin.admin')

@section('content')

<section class="content">


	<div class="box box-primary">
        <div class="box-header with-border  no-padding-h-b"><h3 class="box-title"> {!! $title !!} </h3>
           
        </div>
    </div>

    @include('message')
	<div class="box box-primary">
		<div class="box-header with-border  no-padding-h-b"><h3 class="box-title"> Inspection Details </h3>
			

			<div class="container">

				<div class="card">
	
					<div class="col-md-10 col-sm-offset-1" style="background:#fff;" id="Vehicle" >
						<form class="validate form-horizontal same-form"   role="form" method="POST" action="{{ route($model.'.store') }}" enctype = "multipart/form-data">
	            		{{ csrf_field() }}
	       					
	       					<!-- <div class="col-sm-12 form-div">
					  			
					  			<div class="row ">
					  				<div class="col-sm-3">
					  					<input type="number" class="form-control">
					  				</div>
					  				<div class="col-sm-3">
					  					<input type="number" class="form-control">
					  				</div>
					  				<div class="col-sm-3">
					  					<input type="number" class="form-control">
					  				</div>
					  				<div class="col-sm-3">
					  					<input type="button" class="btn btn-primary add_more" value="+Add More">
					  				</div>
					  			</div>

					  			<div id="append_div"></div>
					  			<br>
					  		</div> -->

	       					<div class="col-sm-12 form-div" style="padding:50px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03); ">

				  				<div class="form-group">
				    				<label for="exampleInputPassword1">Vehicle <span style="color:red;">*</span></label>
				    				{!! Form::select('vehicle_id', [] ,null, ['maxlength'=>'255' ,'class'=>'form-control vehicle_dropdown','required'=>true]) !!} 
				  				</div>
				  			</div>


				  			<div class="col-sm-12 form-div items_div" style="margin-top: 30px; padding:50px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03); display: none; ">

				  				<div class="row">
				  					<h3>Item Checklist</h3>
				  				</div> 
				  				<hr>

				  				@foreach($form_items as $item)
				  					<div class="form-group">
					    				<label class="col-sm-5" for="exampleInputPassword1">{{$item->title}} <span style="color:red;">*</span><br>
					    					<span style="color:#ccc;">{{ $item->short_description }}</span>
					    				</label>

					    				<div class="col-sm-7">
					    					

						    				@if($item->inspection_from_type_id==1)
						    					
						    					<label class="col-sm-6"><input value="pass" type="radio" name="items[{{$item->id}}]"> Pass</label>
						    					<label class="col-sm-6"><input value="fail" type="radio" name="items[{{$item->id}}]"> Fail</label>
							    				
							    				<input type="hidden" class="form-control" name="inspection_from_type_id[{{$item->id}}]" value="{{$item->inspection_from_type_id}}">	
							    				
						    				@elseif($item->inspection_from_type_id==2)
						    					<input type="text" class="form-control" placeholder="Enter Meter" name="items[{{$item->id}}]">
						    					<input type="hidden" class="form-control" name="inspection_from_type_id[{{$item->id}}]" value="{{$item->inspection_from_type_id}}">
						    				@elseif($item->inspection_from_type_id==3)
						    					<input type="text" placeholder="Enter Your Sign" class="form-control" name="items[{{$item->id}}]">
						    					<input type="hidden" class="form-control" name="inspection_from_type_id[{{$item->id}}]" value="{{$item->inspection_from_type_id}}">
						    				<!-- @elseif($item->inspection_from_type_id==6)

						    					Pick File
						    					<input type="file" name="items[{{$item->id}}]">	 -->
						    				@elseif($item->inspection_from_type_id==7)
						    					<input type="number" placeholder="Enter Number" class="form-control" name="items[{{$item->id}}]">
						    					<input type="hidden" class="form-control" name="inspection_from_type_id[{{$item->id}}]" value="{{$item->inspection_from_type_id}}">
						    				@elseif($item->inspection_from_type_id==8)
						    					<input type="date" class="form-control" name="items[{{$item->id}}]">	
						    					<input type="hidden" class="form-control" name="inspection_from_type_id[{{$item->id}}]" value="{{$item->inspection_from_type_id}}">
						    				@else
						    					<input type="text" placeholder="Enter Text" class="form-control" name="items[{{$item->id}}]">	
						    					<input type="hidden" class="form-control" name="inspection_from_type_id[{{$item->id}}]" value="{{$item->inspection_from_type_id}}">
						    				@endif


						    				
						    				

					    				</div>	

					  				</div>

					  				<hr>
				  				@endforeach
				  				

				  				

				  			</div>

				  			<div class="col-sm-12 form-div" style="margin-top: 30px; padding:50px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03); display: none;">

				  				<div class="row">
				  					<h3>Sign Off</h3>
				  				</div> 
				  				<hr>
				  				

				  				<div class="form-group">
				    				<label class="col-sm-5" for="exampleInputPassword1">Vehicle Condition OK <span style="color:red;">*</span></label>
				    				
				    				
				    				<div class="col-sm-7">
				    					
				    					<input type="text" class="form-control">
				    				</div>

				  				</div>

				  				<hr>

				  				<div class="form-group">
				    				<label class="col-sm-5" for="exampleInputPassword1">Reviewing Driver's Signature <span style="color:red;">*</span></label>
				    				
				    				
				    				<div class="col-sm-7">
				    					
				    					<input type="text" class="form-control">
				    				</div>

				  				</div>

				  			</div>
				  			


				  			<div class="col-sm-12 text-right">
				  				<br>
				  				<input type="hidden" class="form-control" name="inspection_form_id" value="{{base64_encode($form_id)}}">
				  				<button type="submit" class="btn btn-primary">Save Inspection</button>
			  					
			    			</div>
	 					</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

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
	
	var child_html='<div class="row child_row"><br>\n\
  				<div class="col-sm-1">\n\
  					&nbsp;\n\
  				</div>\n\
  				<div class="col-sm-3">\n\
  					<input type="number" class="form-control">\n\
  				</div>\n\
  				<div class="col-sm-3">\n\
  					<input type="number" class="form-control">\n\
  				</div>\n\
  				<div class="col-sm-3">\n\
  					<input type="number" class="form-control">\n\
  				</div>\n\
  				<div class="col-sm-2">\n\
  					<i class="fa fa-times remove_child"></i>\n\
  				</div>\n\
  			</div>';

  	var html='<div class="row parrent_row"><br>\n\
  				<div class="col-sm-3">\n\
  					<input type="number" class="form-control">\n\
  				</div>\n\
  				<div class="col-sm-3">\n\
  					<input type="number" class="form-control">\n\
  				</div>\n\
  				<div class="col-sm-3">\n\
  					<input type="number" class="form-control">\n\
  				</div>\n\
  				<div class="col-sm-3">\n\
  					<input type="button" class="btn btn-primary add_more_child" value="+Add Child">\n\
  					<i class="fa fa-times remove_parent"></i> \n\
  				</div>\n\
  				<div id="append_child_div"></div>\n\
  			</div>';		
  	// Append Parent		
	$('.add_more').on('click',function(){
		$('#append_div').append(html);
	});	

	// Append Child
	$('#append_div').on('click','.parrent_row  .add_more_child',function(){
		$(this).parent('div').parent('.parrent_row').find('#append_child_div').append(child_html);	
	});


	// Remove Parent
	$('#append_div').on('click','.parrent_row  .remove_parent',function(){
		$(this).parent('div').parent('.parrent_row').remove();	
	});


	// Remove Child
	$('#append_div').on('click','.parrent_row  #append_child_div .child_row .remove_child',function(){
		$(this).parent('div').parent('.child_row').remove();	
	});



	var vehicles = function(){
        $(".vehicle_dropdown").select2(
        {
            placeholder:'Select Vehicle',
            ajax: {
                url: '{{route("vehicle_dropdown")}}',
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
    vehicles();

    $(".vehicle_dropdown").on('select2:select',function(){
    	$('.items_div').show();
    });


</script>
@endsection
