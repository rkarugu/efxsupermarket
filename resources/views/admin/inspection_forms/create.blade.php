
@extends('layouts.admin.admin')

@section('content')

<section class="content">


	<div class="box box-primary">
        <div class="box-header with-border  no-padding-h-b"><h3 class="box-title"> {!! $title !!} </h3>
            @include('message')
        </div>
    </div>

    @include('message')
	<div class="box box-primary">
		<div class="box-header with-border  no-padding-h-b"><h3 class="box-title"> Details </h3>
			

			<div class="container">

				<div class="card">
	
					<div class="col-md-8 col-sm-offset-2" style="background:#fff;" id="Vehicle" >
						<form class="validate form-horizontal same-form"   role="form" method="POST" action="{{ route($model.'.store') }}" enctype = "multipart/form-data">
	            		{{ csrf_field() }}
	       					<div class="col-sm-12 form-div" style="padding:50px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03); ">

				  				<div class="form-group">
				    				<label for="exampleInputPassword1">Title</label>
				    				<input type="text" class="form-control category_list m-bot15" name="title" required="true">
				  				</div>

				  				<div class="form-group">
				    				<label for="exampleInputPassword1">Description</label>
				    				<textarea class="form-control" id="exampleInputPassword1"  name="description" placeholder="Description"></textarea>
				  				</div>
				  			</div>
				  			<div class="col-sm-12 text-right">
				  				<br>
				  				<button type="submit" class="btn btn-primary">Save Inspection Form</button>
			  					
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
@endsection
