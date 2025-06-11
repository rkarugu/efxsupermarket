
@extends('layouts.admin.admin')

@section('content')

<style type="text/css">
    .category{
        display: flex;
        align-items: center;
    }
    .category input{
        width: 50%;
    }
     .category label{
        margin-left: 5px;
     }
     .category label > div{
        order: -2;
     }
       
</style>

<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border  no-padding-h-b"><h3 class="box-title"> {!! $title !!} </h3>
            @include('message')
        </div>
    </div>

	<div class="box box-primary">
		<div class="box-header with-border  no-padding-h-b"><h3 class="box-title"> Details </h3>
			@include('message')

                <div class="container">

	               
                        <div class="card">
                            <div class="col-md-8 col-sm-offset-2" style="background:#fff;" id="Vehicle" >
    	                        <div class="col-sm-12 form-div" style="padding:50px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03);">
    			                 <!-- <form id="Identification" class="same-form"> -->
    				            
    			                     
                        		  <div class="form-group">
                        		    <label for="exampleInputPassword1">Vehicle</label>
                        		    <select disabled class="form-control category_list m-bot15" name="vehicle_id" required="true"> 
                        		          <option value="{{$row->vehicle_id}}">{{@$row->vehicle->license_plate}}</option>
                        		    </select>
                        		                   
                        		  </div>

                                  <div class="form-group">
                                    <label for="exampleInputPassword1">Service Task</label>
                                    <select disabled class="form-control service_task_list m-bot15" name="service_task_id" required="true"> 
                                            <option value="{{$row->service_task_id}}">{{@$row->service_task->name}}</option>
                                    </select>
                                                   
                                  </div>  
                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    <label for="exampleInputEmail1">Time Interval</label><br>
                                                    <input disabled class="form-control" type="number"  id="time_enterval" value="{{$row->time_enterval}}" name="time_enterval"/>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label>&nbsp;</label>
                                                    <select disabled class="form-control" name="time_enterval_type">
                                                        <option {{$row->time_enterval_type == "day"?'selected':''}} value="day">Day(s)</option>  
                                                        <option {{$row->time_enterval_type == "week"?'selected':''}} value="week">Week(s)</option>  
                                                        <option {{$row->time_enterval_type == "month"?'selected':''}} value="month">Month(s)</option>  
                                                        <option {{$row->time_enterval_type == "year"?'selected':''}} value="year">Year(s)</option>  
                                                    </select>
                                                </div>    
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    <label for="exampleInputEmail1">Time Due Soon Threshold</label><br>
                                                    <input disabled class="form-control" type="number"  id="time_duesoon_threshold" value="{{$row->time_duesoon_threshold}}" name="time_duesoon_threshold"/>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label>&nbsp;</label>
                                                    <select disabled class="form-control" name="time_duesoon_threshold_type">

                                                        <option {{$row->time_duesoon_threshold_type == "day"?'selected':''}} value="day">Day(s)</option>  
                                                        <option {{$row->time_duesoon_threshold_type == "week"?'selected':''}} value="week">Week(s)</option>  
                                                        <option {{$row->time_duesoon_threshold_type == "month"?'selected':''}} value="month">Month(s)</option>  
                                                        <option {{$row->time_duesoon_threshold_type == "year"?'selected':''}} value="year">Year(s)</option> 

                                                    </select>
                                                </div>    
                                            </div>
                                        </div>

                                    </div>    

                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <label for="exampleInputEmail1">Primary Meter Interval</label><br>
                                            <input disabled class="form-control" type="number" id="primary_meter_interval" value="{{$row->primary_meter_interval}}" placeholder="Every" name="primary_meter_interval"/>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="exampleInputEmail1">Primary Meter Due Soon Threshold</label><br>
                                            <input disabled class="form-control" placeholder="Every" type="number" id="  primary_meter_duesoon_threshold" value="{{$row->primary_meter_duesoon_threshold}}" name="     primary_meter_duesoon_threshold"/>
                                        </div>
                                    </div>
                                </div>
                                
                                
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
                placeholder:'Select Vehicle',
                ajax: {
                    url: '{{route('vehicle.list')}}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
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



        var service_task_list = function(){
            $(".service_task_list").select2(
            {
                placeholder:'Select Service Task',
                ajax: {
                    url: '{{route('servicetask.list')}}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
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
        service_task_list();

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


        </script>

         <script type="text/javascript">
         var service_list = function(){
            $(".service_list").select2(
            {
                placeholder:'Select service Task',
                ajax: {
                    url: '{{route('service.list')}}',
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
        service_list();
        </script>



    <script type="text/javascript">
    $(".category_list").change(function() {
        $.ajax({
            method:'get',
            data:{
                id: $(".category_list").val(),
                "_token": "{{ csrf_token() }}",
            },
            url: "{{route("servicehistory.issues")}}",
            success: function(result){
                $("#issues_type").html(result.html)
            }});
      })

   </script>

  <script type="text/javascript">
    $(".category_list").change(function() {
        $.ajax({
            method:'get',
            data:{ id: $(".category_list").val(), "_token": "{{ csrf_token() }}",},
            url: "{{route("servicehistory.servicetask")}}",
            success: function(result){
                $("#service_task").html(result.html)
            }});
    })

   </script>

    <script>
        $(document).ready(function(){
            $("#service_task").change(function(){
                 $.ajax({
                method:'get',
                data
                        :{

                    id: $("#service_task").val(),
                    "_token": "{{ csrf_token() }}",
                },
                  
                url: "{{route("service.work")}}",
                success: function(result){
                    $("#service_name tbody").append(result.html)
                }});
            })

        })
    </script>


          

    <script>
        $(document).ready(function(){
          $(document).find("button").click(function(){
            $(document).find(".tabel").remove();
          });
        });
    </script>     



        

              

@endsection
