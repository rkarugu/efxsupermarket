 
@extends('layouts.admin.admin')

@section('content')

<section class="content">
	<div class="box box-primary">
		<div class="box-header with-border  no-padding-h-b"><h3 class="box-title"> {!! $title !!} </h3>
			@include('message')

            <div class="container">

                <div class="card">
            	
                    <div class="col-md-10 col-sm-offset-1" id="Vehicle" >
            	        <form class="validate form-horizontal same-form"   role="form" method="POST" action="{{ route($model.'.update',$row->id) }}" enctype = "multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
                            <div class="form-div" style="padding:50px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03); ">
            			     <!-- <form id="Identification" class="same-form"> -->
            				
                			   
                		  
                                  <div class="form-group">
                                    <label for="exampleInputPassword1">Name</label>
                                    <input type="text" class="form-control" id="exampleInputPassword1"  name="name" placeholder="Name" value="{{$row->name}}" required>
                                  </div>
                        		  <div class="form-group">
                        		    <label for="exampleInputPassword1">Description</label>
                        		    <textarea class="form-control" id="exampleInputPassword1"  name="description" placeholder="Description">{{$row->description}}</textarea>
                        		  </div>

                                   <!--   <div class="form-group">
                                               <strong>Name:</strong>
                                            <select id='myselect' multiple name="subtype[]">
                                               <option value="">Select An Option</option>
                                               @foreach($subtype as $tag)
                                               <option value="{{ $tag->title }}">{{ $tag->title }}</option>
                                               @endforeach
                                            </select>
                                     </div> -->

                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Sub Type</label>
                                        <select class="form-control category_list  m-bot15" id='myselect' multiple name="subtype[]" required="true"> 
                                        </select>
                                    </div>

                            		<div class="btn-block text-right">
                            			  <div class="btn-group">
                            		          <button type="submit" class="btn btn-primary">Submit</button>
                            		      </div>
                            	    </div>
                            
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>

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
                placeholder:'Select Subtype',
                ajax: {
                    url: '{{route('subtype.lists')}}',
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

         // $(".category_list").select2();


 

  
  var subtype="{{$row->subtype}}";
  subtype_arr=subtype.split(",");
  

  $(".category_list").select2('val',subtype_arr); 
 </script>
       

@endsection