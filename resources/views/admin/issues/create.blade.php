
@extends('layouts.admin.admin')

@section('content')

<section class="content">


	<div class="box box-primary">
        <div class="box-header with-border  no-padding-h-b">
            <div class="col-sm-10">
                <h3 class="box-title"> {!! $title !!} </h3>
            </div>
            <div class="col-sm-2 text-right">
                <a style="text-align: right;" href="{{ (url()->previous())?url()->previous():route('issues.index') }}" class="btn btn-primary">Back</a> 
            </div>
        </div>
    </div>


	<div class="box box-primary">
		<div class="box-header with-border  no-padding-h-b"><h3 class="box-title"> Details </h3>
			@include('message')

			<div class="container">

				<div class="card">
	
					<div class="col-md-8 col-sm-offset-2" style="background:#fff;" id="Vehicle" >
						<form class="validate form-horizontal same-form"   role="form" method="POST" action="{{ route($model.'.store') }}" enctype = "multipart/form-data">
	            		{{ csrf_field() }}
	       					<div class="col-sm-12 form-div" style="padding:50px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03); ">
								

				  				<div class="form-group">
				    				<label for="exampleInputPassword1">Asset</label>
				    				<select class="form-control category_list m-bot15" name="asset" required="true">  
                                      @if(request()->vehicle_id && request()->license_plate)
                                        <option value="{{request()->vehicle_id}}">{{request()->license_plate}}</option>
                                       @endif
                                    </select>
                                </div>
					
								<div class="row">
                                    <div class="col-sm-5">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Reported Date</label><br>

				    						<input class="datebox form-control" type="date" value="date" id="date" name="date"/>
                                        </div>
                                    </div>
                                    <div class="col-sm-5 col-sm-offset-2">
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">Time</label>
                                            <input class="timeBox form-control" type="time" value="13:30"/ name="time">
                                        </div>  
                                    </div>
                                </div>
				   				
				   				


				 				<div class="form-group">
				    				<label for="exampleInputPassword1">Summary</label>
				    				<textarea class="form-control" id="exampleInputPassword1"  name="summary" placeholder="Summary"></textarea>
				  				</div>

				  				<div class="form-group">
				    				<label for="exampleInputPassword1">Description</label>
				    				<textarea class="form-control" id="exampleInputPassword1"  name="description" placeholder="Description"></textarea>
				  				</div>

				   				<div class="form-group">
				    				<label for="exampleInputPassword1">Reported By</label>
				    				<input type="text" class="form-control" id="exampleInputPassword1"  name="reported_by" placeholder="Reported By">
				  				</div>

				   				<div class="form-group">
				    				<label for="exampleInputPassword1">Assigned To</label>
				    				<select class="form-control user_list m-bot15" name="assigned" required="true"> 
				   
				    				</select>
				  				</div>   	 

				  			</div>
				  			
				  			<div style="margin-top: 30px;">&nbsp;</div>
				  			<h4><b>Overdue Settings</b></h4>
				  			<div class="col-sm-12 form-div" style="padding:50px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03); ">
								<div class="form-group">
				    				<label for="exampleInputEmail1">Due Date</label><br>

				    				<input class="datebox form-control" type="date" value="date" id="date" name="due_date"/>
				    				<span style="color:grey; font-size:11px;">(optional) Considered overdue after this date</span>
				  				</div>
				  			</div>
				  			


				  			<div class="col-sm-12 form-div" style="padding:50px; margin-top: 30px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03); ">

				  				<div class="row">
                                    <div class="col-sm-5">
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">Photo</label>
                                            <input type="file" class="form-control" id="exampleInputPassword1" name="photos">
                                        </div>
                                    </div>
                                    <div class="col-sm-5 col-sm-offset-2">
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">Document</label>
                                            <input type="file" class="form-control" id="exampleInputPassword1" name="documents">
                                        </div>  
                                    </div>
                                </div>

							</div>
				  			<div class="col-sm-12 text-right">
				  				<br>
				  				<button type="submit" class="btn btn-primary">Submit</button>
			  					
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
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}">
</script>



        <script type="text/javascript">
 var category_list = function(){
            $(".category_list").select2(
            {
                placeholder:'Select Asset',
                ajax: {
                    url: '{{route('vehicle.lists')}}',
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
 var user_list = function(){
            $(".user_list").select2(
            {
                placeholder:'Select Assigned',
                ajax: {
                    url: '{{route('user.list')}}',
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
        user_list();

        </script>


@endsection
