
@extends('layouts.admin.admin')

@section('content')

<section class="content">
	<div class="box box-primary">
		<div class="box-header with-border  no-padding-h-b">
			<div class="col-sm-10">
				<h3 class="box-title"> {!! $title !!} </h3>
			</div>
			<div class="col-sm-2 text-right">
				<a style="text-align: right;" href="{{ (url()->previous())?url()->previous():route('fuelentry.index') }}" class="btn btn-primary">Back</a> 
			</div>
		</div>
	</div>	

		<div class="box">
			<div class="box-header with-border  no-padding-h-b"><h3 class="box-title"> Details </h3>
				@include('message')

				<div class="container">

					<div class="card">
		
						<div class="col-md-8 col-sm-offset-2" style="background:#fff;" id="Vehicle" >
		       				
							<form class="validate form-horizontal same-form submitMe"   role="form" method="POST" action="{{ route($model.'.update',$row->id) }}"  enctype = "multipart/form-data">
	            					{{ csrf_field() }}
		       					{{ method_field('PUT') }}
		       					<div class="col-sm-12 form-div" style="padding:50px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03); ">
								
									
									<div class="form-group">
									    <label for="exampleInputPassword1">Vehicle</label>
									    
									     {!!Form::select('vehicle', getVehicleListWithAll(),$row->vehicle, ['class' => 'form-control mlselec6t vehicle_list','required'=>true,'id'=>'vehicle' ])!!} 


									                   
									</div>
									

									<div class="row">
										<div class="col-sm-6">
											<div class="form-group">
												<label for="exampleInputEmail1">Fuel Entry Date</label><br>
									    		<input class="datebox form-control" type="date" value="{{$row->date}}" id="date" name="date"/>
									    	</div>
										</div>
										<div class="col-sm-6">
											<div class="form-group">
												<label for="date">Time</label>
									    		<input class="timeBox form-control" type="time" value="{{$row->time}}" value="13:30"/ name="time">
									    	</div>
										</div>
									</div>

			   						<div class="form-group">
									    <label for="exampleInputPassword1">Previous Odometer Reading</label>
									    <input type="text" value="{{$row->previous_odometer_reading}}" class="form-control" id="previous_odometer_reading" name="previous_odometer_reading" readonly value="0">
									</div>


									<div class="form-group">
									    <label for="exampleInputPassword1">Odometer</label>
									    <input type="text" value="{{$row->odometer}}" class="form-control" id="exampleInputPassword1"  name="odometer" placeholder="Odometer">
									</div>

									<div class="form-group">
									    <label for="exampleInputPassword1">Litres</label>
									    <input type="number" value="{{$row->gallons}}" class="form-control" id="exampleInputPassword1"  name="gallons" placeholder="Litres">
									</div>

									<div class="form-group">
									    <label for="exampleInputPassword1">Price</label>
									    <input type="text" value="{{$row->price}}" class="form-control" id="exampleInputPassword1"  name="price" placeholder="Price">
									</div>



								   	<div class="form-group">
								    	<label for="exampleInputPassword1">Fuel Type/Grade</label>
								    	<select class="form-control m-bot15" name="fuel_type" required="true">         
					                        <option value="" selected disabled>Please Select</option>
					                        <option {{ ($row->fuel_type=="compressed natural gas")?'selected':'' }} value="compressed natural gas">Compressed Natural Gas</option>
					                        <option {{ ($row->fuel_type=="petrol")?'selected':'' }} value="petrol">Petrol</option>
					                        <option {{ ($row->fuel_type=="diesel")?'selected':'' }} value="diesel">Diesel</option>
					                        <option {{ ($row->fuel_type=="propane")?'selected':'' }} value="propane">Propane</option>
								        </select>
								    </div>      

									<div class="form-group">
									    <label for="exampleInputPassword1">Vendor</label>
									    {!!Form::select('vendor', getVendorList(),$row->vendor, ['class' => 'form-control mlselec6t','required'=>true,'id'=>'vendor' ])!!} 


									</div>
			  
									<div class="form-group">
									    <label for="exampleInputPassword1">Reference</label>
									    <input type="text" class="form-control" id="exampleInputPassword1" value="{{$row->reference}}"  name="reference" placeholder="Reference">
									</div>


									<div class="form-group">
									  	<b><p>Flag</p></b>
									    <input type="checkbox" {{($row->flag=="personal")?'checked':''}} id="vehicle1" name="flag[]" value="personal">&nbsp;
							        	<label for="vehicle1">Personal</label>
									    <br>
							        
											<input type="checkbox" {{($row->flag=="Partial_fuel_up")?'checked':''}} id="vehicle2" name="flag[]" value="Partial_fuel_up">&nbsp;
							        		<label for="vehicle2">Partial fuel-up</label>
											<br>
									</div>

									
								    
	 							
								</div>
								<br>

								{{-- 
								<div class="col-sm-12 form-div" style="padding:50px; margin-top: 20px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03); ">
									
									

									<div class="row">
										<div class="col-sm-6">
											<div class="form-group">
												<label for="exampleInputPassword1">Photo</label>
									    		<input  type="file" class="form-control" id="exampleInputPassword1" name="photos">
									    	</div>
									    </div>
										<div class="col-sm-6">
											<div class="form-group">
												<label for="exampleInputPassword1">Document</label>
									    		<input type="file" class="form-control" id="exampleInputPassword1" name="documents">
									    	</div>	
										</div>
									</div> 
								</div>
								--}}

								<div class="col-sm-12 form-div" style="padding:50px; margin-top: 20px; margin-bottom: 30px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03); ">	
									

								   	<div class="form-group">
								    	<label for="exampleInputPassword1">Comments</label>
								    	<textarea rows="5" class="form-control" id="exampleInputPassword1"  name="comments">
								    		{{ $row->comments }}
								    	</textarea>
								  	</div>

									
										

								</div>
								<div class="col-md-12 text-right">
							  		<button type="submit" class="btn btn-primary">Submit</button>
							  	</div>
							</form>
						</div>
					</div>

				</div>
			</div>
		</div>	
	
	
</section>



<!-- <style>
	/*.col-md-9{
    overflow-y: scroll;
    height: 1000px;

	}*/

	.same-btn{
    margin-right: 10px !important;
    border-radius: 3px !important; 
    border: 1px solid #c7c7c7;
    color: #000;
	}

	.btn-block{
		display: flex;
		justify-content: end;
	}
	.main-box-ul{
		border-radius: 4px;
		background-color: #c7c7c7;
		padding: 10px;
		background-color: #fff;
		box-shadow: 0 1px 3px rgba(0, 0, 0, 10%)
	}
	.form-div .same-form{
		background-color: #fff !important;
		box-shadow: 0 5px 10px rgba(0, 0, 0, 10%) !important;
		padding: 10px 12px !important;
		margin: 10px 0;
	}
	.btn-group .green-btn{
		background-color: #44ace9 !important;
	}
</style>
     -->
   
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
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}">
</script>

<script type="text/javascript">

	$(function () {
        $(".mlselec6t").select2();
    });


	$(document).ready(function(){
		$('.vehicle_list').on('change',function(){
			var vehicle_id =$(this).val();
			$.ajax({
				url:'{{route('fuelentry.get_previous_odometer')}}',
				type:'GET',
				data:{vehicle_id:vehicle_id},
				success:function(response){
					$('#previous_odometer_reading').val(response.previous_odometer_reading);
				}

			});
		});
	});
 var category_list = function(){
            $(".category_list").select2(
            {
                placeholder:'Select vehicle',
                ajax: {
                    url: '{{route('vehicle.list')}}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                    	console.log(data);
                            var res = data.map(function (item) {
                                return {id: item.id, text: item.text};
                            });
                        return {
                            results: res
                        };
                    }
                },
            });
        };
        category_list();

         // $(".category_list").select2();

        </script>

        <script type="text/javascript">
 var category_list = function(){
            $(".category_list").select2(
            {
                placeholder:'Select vehicle',
                ajax: {
                    url: '{{route('vehicle.list')}}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                    	console.log(data);
                            var res = data.map(function (item) {
                                return {id: item.id, text: item.text};
                            });
                        return {
                            results: res
                        };
                    }
                },
            });
        };
        category_list();

         // $(".category_list").select2();

        </script>

        <script type="text/javascript">
 var vendor_list = function(){
            $(".vendor_list").select2(
            {
                placeholder:'Select Vendor',
                ajax: {
                    url: '{{route('vendor.list')}}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                    	console.log(data);
                            var res = data.map(function (item) {
                                return {id: item.id, text: item.text};
                            });
                        return {
                            results: res
                        };
                    }
                },
            });
        };
        vendor_list();

         // $(".category_list").select2();

        </script>
@endsection
