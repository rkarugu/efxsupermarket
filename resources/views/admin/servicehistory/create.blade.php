
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
        <div class="box-header with-border  no-padding-h-b">
            <div class="col-sm-10">
                <h3 class="box-title"> {!! $title !!} </h3>
            </div>
            <div class="col-sm-2 text-right">
                <a style="text-align: right;" href="{{ (url()->previous())?url()->previous():route('servicehistory.index') }}" class="btn btn-primary">Back</a> 
            </div>
        </div>
    </div>

	<div class="box box-primary">
		<div class="box-header with-border  no-padding-h-b"><h3 class="box-title"> Details </h3>
			@include('message')

                <div class="container">

	                <form class="validate form-horizontal same-form submitMe"   role="form" method="POST" action="{{ route($model.'.store') }}" enctype = "multipart/form-data">
                    {{ csrf_field() }}
                        <div class="card">
                            <div class="col-md-8 col-sm-offset-2" style="background:#fff;" id="Vehicle" >
    	                        <div class="col-sm-12 form-div" style="padding:50px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03);">
    			                 <!-- <form id="Identification" class="same-form"> -->
    				            
    			                     
                        		  <div class="form-group">
                        		    <label for="exampleInputPassword1">Vehicle</label>
                        		    <select class="form-control category_list m-bot15" name="vehicle" required="true"> 
                        		          @if(request()->vehicle_id && request()->license_plate)
                                            <option value="{{request()->vehicle_id}}">{{request()->license_plate}}</option>
                                           @endif
                        		    </select>
                        		                   
                        		  </div>
                        		  <div class="form-group">
                        		    <label for="exampleInputPassword1">Odometer</label>
                        		    <input type="text" class="form-control" id="exampleInputPassword1"  name="odometer" placeholder="Odometer">
                        		  </div>
    		                      
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Start Date</label><br>
                                                <input class="datebox form-control" type="date" value="date" id="date" name="start_date"/>
                                            </div>
                                        </div>
                                        <div class="col-sm-5 col-sm-offset-2">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Completion Date</label><br>
                                                <input class="datebox form-control" type="date" value="date" id="date" name="complete_date"/>
                                            </div>
                                        </div>
                                    </div>    


                        		  <!-- <div class="form-group">
                        		    <label for="exampleInputEmail1">Start Date</label><br>
                        		    <input class="datebox" type="date" value="date" id="date" name="start_date"/>
                        		    <label for="date">Date</label>
                        		  </div>

                        		   <div class="form-group">
                        		    <label for="exampleInputEmail1">Completion Date</label><br>
                        		    <input class="datebox" type="date" value="date" id="date" name="complete_date"/>
                        		    <label for="date">Date</label>
                        	       </div> -->

                        	      <div class="form-group">
                        		    <label for="exampleInputPassword1">Vendor</label>
                        		    <select class="form-control vendor_list m-bot15" name="vendor" required="true"> 
                        		   
                        		    </select>
                        		  </div>


                        		   <div class="form-group">
                        		    <label for="exampleInputPassword1">Reference</label>
                        		    <input type="textarea" class="form-control" id="exampleInputPassword1"  name="reference" placeholder="Reference">
                        		   </div>


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

                        		  

                        		  <div class="form-group">
                        		    <label for="exampleInputPassword1">Comments</label>
                        		    <textarea class="form-control" id="exampleInputPassword1"  name="comments" placeholder="Comments"></textarea>
                        		  </div>
                                </div>
                                
                                <div style="margin-top: 30px;">&nbsp;</div>
                                <h4 ><b>Issues</b></h4>
                                <div class="col-sm-12 form-div" style="padding:50px;  box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03);">  
    		                        
    	                            <div class="form-group">
                                        <table class="table"> 
                                            <tr>
                                                <th><input type="checkbox" name="all_issue"></th>
                                                <th>Issue</th>
                                                <th>Summary</th>
                                                <th>Status</th>
                                                <th>Assigned</th>
                                                <th>Due Date</th>
                                            </tr>
                                            <tbody id="issues_type">                      
                                            </tbody>

                                        </table>
                            		  	
    		                        </div>

    		                    </div>


                                <div style="margin-top: 30px;">&nbsp;</div>
                                <h4><b>Line Item</b></h4>
                                <div class="col-sm-12 form-div" style="padding:50px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03);">  

             
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Service Task</label>
                                        <select class="form-control service_list   m-bot15"    id="service_task" required="true"></select>
                                    </div> 
        
                                    <table  class="table"  id="service_name">
                                            <thead>
                                            <tr>
                                              <th width="20%" >Task</th>
                                              <th width="20%" >Parts</th>
                                              <th width="20%" >Labor</th>
                                              <th width="20%" >Subtotal</th>
                                            </tr>
                                            </thead> 
                                            <tbody>

                 
                 
                                            </tbody>
                                   </table>   
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="col-md-8 col-sm-offset-2" style="background:#fff; margin-top:30px;" id="Vehicle" >
                                
                                <div class="col-sm-12 form-div" style="padding:50px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03);">
                                    <div class="row">
                                        <div  class="col-md-7 ">
                                            <div class="col-sm-12">
                                                <label for="exampleInputPassword1">GENERAL NOTES</label>
                                                <textarea rows="4" class="form-control" id="exampleInputPassword1"  name="general_notes" placeholder="Enter notes or details(optional)"></textarea>
                                            </div>

                                        </div>   
                                        <div class="col-md-5 ">
                                            

                                                

                                                
                                                <div class=" form-group">
                                                    <input type="number" class="form-control mainsubtotal" placeholder="Subtotal" name="subtotals" readonly>
                                                </div>
                                                <div class=" form-group">
                                                    
                                                    <input type="number" class="form-control mainparts" placeholder="parts" readonly name="partss">
                                                </div>
                                                <div class=" form-group">
                                                    
                                                    <input type="number" class="form-control mainlabor " placeholder="Labor" readonly name="labors">
                                                </div>
                                                <div class=" form-group">
                                                    
                                                    <input type="number" class="form-control maindiscount" placeholder="Discount" onkeyup="totalofAllTotal()" onchange="totalofAllTotal()" name="discount" >
                                                </div>

                                                <div class="form-group">
                                                    
                                                    <input type="number" class="form-control maintax" placeholder="Tax"  onkeyup="totalofAllTotal()" onchange="totalofAllTotal()" name="tax">
                                                    
                                                        
                                                    
                                                </div>  
                                                <div class="form-group">    
                                                    <input type="number" class="form-control maintotal" placeholder="Total" readonly name="total">
                                                </div>
                                                 
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-sm-12 text-right" style="margin: 30px 0px 30px 0px;">
    		                        <button type="submit" class="btn btn-primary">Submit</button>
    		                    </div>

                                
    		                </div>  
                        </div>    
                    </form>
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
            data
      :{

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


          <script type="text/javascript">
                  $(document).ready(function(){
            $(document).on('keyup','.parts',function(r){
                  let a = $(this).val();
                        let b = $(this).parents('tr').find(".labour").val();
                        // alert((+a) + (+b));
                        
                        $(this).parents('tr').find('.subtotal').val((+a) + (+b));
                        totalofAllTotal();
            })

            $(document).on('keyup','.labour',function(r){
                  let a = $(this).val();
                        let b = $(this).parents('tr').find(".parts").val();
                        // alert((+a) + (+b));
                        
                        $(this).parents('tr').find('.subtotal').val((+a) + (+b));
                        totalofAllTotal();
            })
                    //       $(document).find(".parts").on('input',function(){
                    //     let a = $(this).val();
                    //     let b = $(this).parents('tr').find(".labour").val();
                    //     // alert((+a) + (+b));
                        
                    //     $(this).parents('tr').find('.subtotal').text((+a) + (+b));
                    // })


                    //  $(document).find(".labour").on('input',function(){
                    //     let a = $(this).val();
                    //     let b = $(this).parents('tr').find(".parts").val();
                    //     // alert((+a) + (+b));

                    //    $(this).parents('tr').find(".subtotal").text((+a) + (+b));
                    // })
                    $(document).on('click','.deleteTHis',function(r){
                        let b = $(this).parents('tr').remove();
                        totalofAllTotal();
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



        <script type="text/javascript">
            function totalofAllTotal(){
                  var alld = $(document).find('.parts');
                  var allv = $(document).find('.labour');
                  var alls = $(document).find('.subtotal');

                  var mainparts = 0;
                  $.each(alld, function (indexInArray, valueOfElement) { 
                    mainparts = parseFloat(mainparts) + parseFloat($(valueOfElement).val());
                  });
                  $('.mainparts').val((mainparts).toFixed(2));
                  var mainlabor = 0;
                  $.each(allv, function (indexInArray, valueOfElement) { 
                    mainlabor = parseFloat(mainlabor) + parseFloat($(valueOfElement).val());
                  });
                  $('.mainlabor').val((mainlabor).toFixed(2));

                  var mainsubtotal = 0;
                  $.each(alls, function (indexInArray, valueOfElement) { 
                    mainsubtotal = parseFloat(mainsubtotal) + parseFloat($(valueOfElement).val());
                  });
                  $('.mainsubtotal').val((mainsubtotal).toFixed(2));
                    var total = mainsubtotal;
                    var maindiscount = $('.maindiscount').val();
                    if(maindiscount > 0){
                        var afterdis = parseFloat(parseFloat(total)*parseFloat(maindiscount) ) / 100;
                        total = parseFloat(total) - parseFloat(afterdis);
                    }
                    var maintax = $('.maintax').val();

                    if(maintax > 0){
                        var aftertax = parseFloat(parseFloat(total)*parseFloat(maintax) ) / 100;
                        total = parseFloat(total) + parseFloat(aftertax);
                    }
                    $('.maintotal').val((total).toFixed(2));
               
                 
                }
        </script>


              

@endsection
