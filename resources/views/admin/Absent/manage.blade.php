@extends('layouts.admin.admin')
@section('content')
<style>
.dataTables_wrapper table th, td {
    border-right: 0px solid #337ab7 !important;
}	
/* Style the tab */
.tab {
  overflow: hidden;
/*  border: 1px solid #ccc;
*/  background-color: #c1ccd1;
}

/* Style the buttons inside the tab */
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 7px 16px;
  transition: 0.3s;
  font-size: 14px;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: white;
  border: 1px solid gainsboro;
}

/* Style the tab content */
.tabcontent {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
}
.ctable {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

.ctable,td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}
    .onoffswitch {
    position: relative; width: 90px;
    -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
}
.onoffswitch-checkbox {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
.onoffswitch-label {
    display: block; overflow: hidden; cursor: pointer;
   /* border: 2px solid #999999; 
    border-radius: 20px;*/
}
.onoffswitch-inner {
    display: block; width: 200%; margin-left: -100%;
    transition: margin 0.3s ease-in 0s;
}
.onoffswitch-inner:before, .onoffswitch-inner:after {
    display: block; float: left; width: 50%; height: 30px; padding: 0; line-height: 30px;
    font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
    box-sizing: border-box;
}
.onoffswitch-inner:before {
    content: "ON";
    padding-left: 10px;
    background-color: #34A7C1; color: #FFFFFF;
}
.onoffswitch-inner:after {
    content: "OFF";
    padding-right: 10px;
    background-color: red; color: #fff;
    text-align: right;
}
.onoffswitch-switch {
    display: block; width: 18px; margin: 6px;
    background: #FFFFFF;
    position: absolute; top: 0; bottom: 0;
    right: 56px;
    border: 2px solid #999999; border-radius: 20px;
    transition: all 0.3s ease-in 0s; 
}
.onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner {
    margin-left: 0;
}
.onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-switch {
    right: 0px; 
}

 .onoffswitch1 {
    position: relative; width: 90px;
    -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
}
.onoffswitch1-checkbox {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
.onoffswitch1-label {
    display: block; overflow: hidden; cursor: pointer;
   /* border: 2px solid #999999; 
    border-radius: 20px;*/
}
.onoffswitch1-inner {
    display: block; width: 200%; margin-left: -100%;
    transition: margin 0.3s ease-in 0s;
}
.onoffswitch1-inner:before, .onoffswitch1-inner:after {
    display: block; float: left; width: 50%; height: 30px; padding: 0; line-height: 30px;
    font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
    box-sizing: border-box;
}
.onoffswitch1-inner:before {
    content: "ON";
    padding-left: 10px;
    background-color: #34A7C1; color: #FFFFFF;
}
.onoffswitch1-inner:after {
    content: "OFF";
    padding-right: 10px;
    background-color: red; color: #fff;
    text-align: right;
}
.onoffswitch1-switch {
    display: block; width: 18px; margin: 6px;
    background: #FFFFFF;
    position: absolute; top: 0; bottom: 0;
    right: 56px;
    border: 2px solid #999999; border-radius: 20px;
    transition: all 0.3s ease-in 0s; 
}
.onoffswitch1-checkbox:checked + .onoffswitch1-label .onoffswitch1-inner {
    margin-left: 0;
}
.onoffswitch1-checkbox:checked + .onoffswitch1-label .onoffswitch1-switch {
    right: 0px; 
}

/* Style the close button */
</style>

    <!-- Main content -->
    <section class="content">
                                  @include('message')<br>

        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                       <div class="box-header with-border no-padding-h-b">  <h4>Employee Profile</h4>
                        	<hr>	
                        	<div class="row">
                        <div class="col-md-3 col-sm-3">
                            <a href="#">
                        <img id="MainContent_imgPassPort" class="img-thumbnail img-circle img-responsive" src="{{  asset('public/uploads/EmpImage/'.$empData->emp_image) }}" align="middle" style="height:200px;width:200px;">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </a>
                        
                        </div>
                       <div class="col-md-9 col-sm-9">
                          <table class="table">
                            <tbody>
                            	<tr>
                                <td><strong>Staff No</strong></td>
                                <td>:</td>
                                <td><b>{{$empData->staff_number}}</b></td>
                            </tr>
                            <tr>
                                <td><strong>Employee Name</strong></td>
                                <td>:</td>
                                <td><b>{{$empData->first_name}} {{$empData->last_name}}</b></td>
                            </tr>
                            <tr>
                               <td><strong>PIN Number</strong></td>
                               <td>:</td>
                               <td><b>{{$empData->pin_number}}</b></td>
                            </tr>
                            <tr>
                                <td><strong>ID Number</strong></td>
                                <td>:</td>
                                <td><b>{{$empData->Id_number}}</b></td>
                            </tr>
                            <tr>
                            	<td><strong>NSSF Number</strong></td>
                                <td>:</td>
                                <td><b>{{$empData->nssf_no}}</b></td>
                            </tr>
                            <tr>
                                <td><strong>NHIF Number</strong></td>
                                <td>:</td>
                                <td><b>{{$empData->nhif_no}}</b></td>
                            </tr>
                            </tbody>
                        </table>
                       </div>
                        </div>
                    </div>
                </div>
                    <div class="box box-primary" style="margin-top: 20px;">
                       <div class="box-header with-border no-padding-h-b">
                         <h4>Employee Profile</h4><hr>
                         <form @if(!empty($absentEdit)) action="{{route('PayrollAbsend.edit',['id'=>$absentEdit->id])}}"
                         @else() action="{{route('PayrollAbsend.CreateAbsent')}}" @endif  method="post">      {{ csrf_field() }}

                        <div class="col-lg-12 col-md-12">
                        <div class="col-lg-4">
                          <input type="hidden" name="emp_id" value="{{$empData->id}}">
                          <div class="form-group">
                            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Month</label>
                              <div class="col-lg-9">
                            <select name="month" class="form-control input-sm">
                            <option value="1"{{@$absentEdit->month =='1' ?  'selected="selected"' : ''}}>JANUARY</option>
                            <option value="2"{{@$absentEdit->month =='2' ?  'selected="selected"' : ''}}>FEBRUARY</option>
                            <option value="3"{{@$absentEdit->month =='3' ?  'selected="selected"' : ''}}>MARCH</option>
                            <option value="4"{{@$absentEdit->month =='4' ?  'selected="selected"' : ''}}>APRIL</option>
                            <option value="5"{{@$absentEdit->month =='5' ?  'selected="selected"' : ''}}>MAY</option>
                            <option value="6"{{@$absentEdit->month =='6' ?  'selected="selected"' : ''}}>JUNE</option>
                            <option value="7"{{@$absentEdit->month =='7' ?  'selected="selected"' : ''}}>JULY</option>
                            <option value="8"{{@$absentEdit->month =='8' ?  'selected="selected"' : ''}}>AUGUST</option>
                            <option value="9"{{@$absentEdit->month =='9' ?  'selected="selected"' : ''}}>SEPTEMBER</option>
                            <option value="10"{{@$absentEdit->month =='10' ?  'selected="selected"' : ''}}>OCTOBER</option>
                            <option value="11"{{@$absentEdit->month =='11' ?  'selected="selected"' : ''}}>NOVEMBER</option>
                            <option value="12"{{@$absentEdit->month =='12' ?  'selected="selected"' : ''}}>DECEMBER</option>
                          </select>
                          </div>
                        </div>
                        </div>
                        <div class="col-lg-4">
                          <div class="form-group">
                          <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Year</label>
                            <div class="col-lg-9">
                            <select name="year" id="" class="form-control input-sm">
                          <option value="2013"{{@$absentEdit->year =='2013' ?
                          'selected="selected"' : ''}}>2013</option>
                          <option value="2014"{{@$absentEdit->year =='2014' ?  'selected="selected"' : ''}}>2014</option>
                          <option value="2015"{{@$absentEdit->year =='2015' ?  'selected="selected"' : ''}}>2015</option>
                          <option value="2016"{{@$absentEdit->year =='2016' ?  'selected="selected"' : ''}}>2016</option>
                          <option value="2017"{{@$absentEdit->year =='2017' ?  'selected="selected"' : ''}}>2017</option>
                          <option value="2018"{{@$absentEdit->year =='2018' ?  'selected="selected"' : ''}}>2018</option>
                          <option value="2019"{{@$absentEdit->year =='2019' ?  'selected="selected"' : ''}}>2019</option>
                          <option value="2020"{{@$absentEdit->year =='2020' ?  'selected="selected"' : ''}}>2020</option>
                          <option selected="selected" value="2021">2021</option>
                          <option value="2022"{{@$absentEdit->year =='2021' ? 
                             'selected="selected"' : ''}}>2022</option>
                          <option value="2023"{{@$absentEdit->year =='2023' ?  'selected="selected"' : ''}}>2023</option>
                          <option value="2024"{{@$absentEdit->year =='2024' ?  'selected="selected"' : ''}}>2024</option>
                          <option value="2025"{{@$absentEdit->year =='2025' ?  'selected="selected"' : ''}}>2025</option>
                          <option value="2026"{{@$absentEdit->year =='2026' ?  'selected="selected"' : ''}}>2026</option>
                          <option value="2027"{{@$absentEdit->year =='2027' ?  'selected="selected"' : ''}}>2027</option>
                          <option value="2028"{{@$absentEdit->year =='2028' ?  'selected="selected"' : ''}}>2028</option>
                          <option value="2029"{{@$absentEdit->year =='2029' ?  'selected="selected"' : ''}}>2029</option>
                          <option value="2030"{{@$absentEdit->year =='2030' ?  'selected="selected"' : ''}}>2030</option>
                          <option value="2031"{{@$absentEdit->year =='2031' ?  'selected="selected"' : ''}}>2031</option>
                          <option value="2032"{{@$absentEdit->year =='2032' ?  'selected="selected"' : ''}}>2032</option>
                          <option value="2033"{{@$absentEdit->year =='2033' ?  'selected="selected"' : ''}}>2033</option>
                          <option value="2034"{{@$absentEdit->year =='2034' ?  'selected="selected"' : ''}}>2034</option>
                          <option value="2035"{{@$absentEdit->year =='2035' ?  'selected="selected"' : ''}}>2035</option>
                        </select>
                        </div>
                      </div> 
                        </div>
                        <div class="col-lg-4">
                             <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Absent Days</label>
                             <div class="col-lg-9">
                          <input type="number" name="absent_days" placeholder="Absent Days" class="form-control" value="{{@$absentEdit->absent_days}}"> 
                        </div>
                        </div>
                        <div class="col-lg-12">
                         <input type="submit" name="" value="Save" class="btn btn-success btn-sm">
                        </div>
                      </div>
                    </form>
                      <hr>
                      <div class="col-lg-12">
                        <table class="table table-condensed table-bordered table-hover table-striped" cellspacing="0" rules="all" border="1"  style="border-collapse:collapse;margin-top: 20px;">
                        <tbody>
                          <tr style="color:White;background-color:#3AC0F2;">
                            <th scope="col">#</th>
                            <th scope="col">Year</th>
                            <th scope="col">Month</th>
                            <th scope="col">Month Days</th>
                            <th scope="col">Days Worked</th>
                            <th scope="col">Absent Days</th>
<!--                             <th scope="col">AbsentPay</th>
 -->                            <th align="left" scope="col" style="width:70px;">&nbsp;</th>
                            <th scope="col">&nbsp;</th>
              </tr>
               @foreach($absentData as $key => $absentDataVal)
              <tr>
                <td>{{$key+1}}</td>
                <td>{{$absentDataVal->year}}</td>
                <td>{{$absentDataVal->month}}</td>
                <?php  $d=cal_days_in_month(CAL_GREGORIAN,$absentDataVal->month,$absentDataVal->year);
 ?>
                <td>{{$d}}</td>
                <td>{{$d - $absentDataVal->absent_days}}</td>
                <td>{{$absentDataVal->absent_days}}</td>
                <td><a href="?Edit={{$absentDataVal->id}}"><input type="submit" value="Edit"  class="btn btn-success btn-xs"></a></td>
           <td><a href="{{route('PayrollAbsend.delete',['id'=>$absentDataVal->id])}}"><input type="submit" value="Delete" onclick="javascript:__doPostBack('','DeleteWE$0')" class="btn btn-warning btn-xs"></a></td>
            </tr>@endforeach
        </tbody>
      </table>
      </div>
    </div>
    </section>
    @endsection
    @section('uniquepagescript')
 <script>
function openCity(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}

// Get the element with id="defaultOpen" and click on it
document.getElementById("defaultOpen").click();
</script>
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script type="text/javascript">
   $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
</script>	
@endsection
