
@extends('layouts.admin.admin')

@section('content')


<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="box box-primary">
        <div class="box-header with-border no-padding-h-b">
            <div  style="height: 150px ! important;"> 
                <div class="card-header">
                    <i class="fa fa-filter"></i> Filter
                </div><br>
<form action="" method="get">
                <div>
                    <div class="col-md-12 no-padding-h">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="start-date" style="width:100% !important">Start Date:</label>
								<input type="text" id="start-date" style="width:100% !important" class="datepicker" name="start-date" autocomplete="off" value="{{@request()->get('start-date')}}">
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="end-date" style="width:100% !important">End Date:</label>
								<input type="text" id="end-date" style="width:100% !important" class="datepicker" name="end-date" autocomplete="off" value="{{@request()->get('end-date')}}">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="end-date">Project</label>
                                <select name="project" id="project" class="mlselec6t form-control">
                                    <option value="" selected disabled>Select Project</option>
                                    @foreach ($projects as $item)
                                        <option value="{{$item->id}}" {{request()->project == $item->id ? 'selected' : NULL}}>{{$item->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 no-padding-h">
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-success" name="manage-request" value="filter"  >Filter</button>
                            <button type="submit" class="btn btn-success" name="manage-request" value="export"  >PDF</button>
                            <button type="submit" class="btn btn-success" name="manage-request" value="excel"  >Excel</button>
                           
                        </div>





                    </div>
                </div>

                </form>
            </div>

            <br>
            @include('message')

            <div class="col-md-12 no-padding-h">

                @if($monthRange<=12)

                <table class="table table-bordered table-hover">
             <?php 
                $logged_user_info = getLoggeduserProfile();
             ?>

        <tr style="text-align: left;">
            <th  colspan="8">Monthly Project Summary</th>
        </tr>

        <tr style="text-align: left;">
            <th  colspan="2"><b>-</b></th>
        </tr>
 

        <tr>
            <td style="text-align: left;"><b></b></td>
            @if(isset($selectedMonthArr['m']) && !is_null($selectedMonthArr['m']))

            @foreach($selectedMonthArr['m'] as $key => $month)

                <td style="text-align: right;"><b>{{getMonthsNameToNumber($month)}}</b></td>
            @endforeach
            @endif
        </tr>
    <!-- Dynamic code start -->

                 <?php 
                $main_qty = [];
                $main_vat = [];
                $main_net = [];
                $main_total = [];

                $new_final_arr=[];
                ?>


                @foreach($gl_tags as $gl_tag)
                 
                        @php $total_stock_arr=[]; @endphp 
                        <tr style="text-align: right;">
                            <td style="text-align: left;">{{ $gl_tag->title }}</td>
            			@if(isset($selectedMonthArr['m']) && !is_null($selectedMonthArr['m']))
                            @foreach($selectedMonthArr['m'] as $key => $month)
                                @php
                                $year=$selectedMonthArr['y'][$key]; 
                                    $created_from=date($year.'-'.$month.'-01');
                                    $created_to=date($year.'-'.$month.'-t');
                                    
                                    $monthlyStock=0;
                                    $monthlyStock=\App\Model\WaGlTran::where('gl_tag',$gl_tag->id)->where(function($e){
                                        if(request()->project){
                                            $e->where('project_id',request()->project);
                                        }
                                    })->whereRaw(\DB::RAW("(CASE WHEN wa_gl_trans.transaction_type = 'Journal' THEN amount > 0 ELSE amount >= 0 OR amount <= 0 END)"))->whereYear('trans_date', $year)->whereMonth('trans_date', $month)->sum('amount'); 
                                    
                                    
                                    $total_stock_arr[]=$monthlyStock;
                                    $new_final_arr[$month.'-'.$year][]=$monthlyStock;
                                    
                                @endphp
                                <td style="text-align: right;">{{manageAmountFormat(abs($monthlyStock))}}</td>
                            @endforeach
                            @endif


                        </tr>
                    @endforeach


                <tr style="text-align: right;">
                    <td colspan="" style="text-align: left;">
                        <b>Total: </b>
                    </td>
                    @if(isset($selectedMonthArr['m']) && !is_null($selectedMonthArr['m']))
                            @foreach($selectedMonthArr['m'] as $key => $month)
                                @php
                                $year=$selectedMonthArr['y'][$key]; 
                                @endphp
                    <td>
                            {{manageAmountFormat(array_sum($new_final_arr[$month.'-'.$year] ?? [0.00]))}}
                    </td>
                    @endforeach
                    @endif
                </tr>
            </table>

            @endif
            </div>

        </div>
    </div>
</section>



@endsection



@section('uniquepagestyle')
<link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.0/themes/smoothness/jquery-ui.css">
@endsection

@section('uniquepagescript')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script>
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
<!-- <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script> -->
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>


<script>
	
    $(".mlselec6t").select2();


// $('#start_date').datepicker({
//     format: 'yyyy-mm-dd',
// });

// $('#end_date').datepicker({
//     format: 'yyyy-mm-dd',
// });
  

//  $(window).on('load',function(){
//     var today = new Date().toISOString().split('T')[0];
//     document.getElementById("end_date").setAttribute('min', today); 
//  });  

	
// 	$(document).ready(function() {
//   $(".datepicker").datepicker({
//     changeMonth: true,
//     changeYear: true,
//     dateFormat: "dd/mm/yy",
//     minDate: 0, // Disable previous dates
//     maxDate: "+1y",
//     onSelect: function(selectedDate) {
//       if (this.id === "start-date") {
//         var endDate = $("#end-date").datepicker("getDate");
//         if (endDate && endDate <= new Date(selectedDate)) {
//           endDate.setDate(endDate.getDate() + 1);
//           $("#end-date").datepicker("setDate", endDate);
//         }
//       } else {
//         var startDate = $("#start-date").datepicker("getDate");
//         if (startDate && startDate >= new Date(selectedDate)) {
//           startDate.setDate(startDate.getDate() - 1);
//           $("#start-date").datepicker("setDate", startDate);
//         }
//       }
//     }
//   });
// });


$(document).ready(function() {
  $(".datepicker").datepicker({
    changeMonth: true,
    changeYear: true,
    dateFormat: "yy-mm-dd",
    // minDate: 0, // Disable previous dates
    // maxDate: "+1y",
    onSelect: function(selectedDate) {
      if (this.id === "start-date") {
        var endDate = $("#end-date").datepicker("getDate");
        if (endDate && endDate <= new Date(selectedDate)) {
          endDate.setDate(endDate.getDate() + 1);
          $("#end-date").datepicker("setDate", endDate);
        }
      } else {
        var startDate = $("#start-date").datepicker("getDate");
        if (startDate && startDate >= new Date(selectedDate)) {
          startDate.setDate(startDate.getDate() - 1);
          $("#start-date").datepicker("setDate", startDate);
        }
      }
      
      // Check if the difference between start and end dates is not more than 12 months
      var startDate = $("#start-date").datepicker("getDate");
      var endDate = $("#end-date").datepicker("getDate");
      if (startDate && endDate) {
        var diffMonths = (endDate.getFullYear() - startDate.getFullYear()) * 12;
        diffMonths -= startDate.getMonth();
        diffMonths += endDate.getMonth();
        if (diffMonths > 12) {
          alert("The difference between start and end dates cannot be more than 12 months.");
          $("#end-date").val("");
        }
      }
    }
  });
});



</script>
@endsection


