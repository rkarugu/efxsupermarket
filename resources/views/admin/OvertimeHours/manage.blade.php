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
                         <form action="{{route('OvertimeHours.Create')}}" method="post">      {{ csrf_field() }}

                        <div class="col-lg-12 col-md-12">
                        <div class="col-lg-6">
                          <input type="hidden" name="emp_id" value="{{$empData->id}}">
                          <div class="form-group">
                            <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Month</label>
                              <div class="col-lg-9">
                            <select name="month" class="form-control input-sm">
                            <option value="JANUARY">JANUARY</option>
                            <option value="FEBRUARY">FEBRUARY</option>
                            <option value="MARCH">MARCH</option>
                            <option selected="selected" value="APRIL">APRIL</option>
                            <option value="MAY">MAY</option>
                            <option value="JUNE">JUNE</option>
                            <option value="JULY">JULY</option>
                            <option value="AUGUST">AUGUST</option>
                            <option value="SEPTEMBER">SEPTEMBER</option>
                            <option value="OCTOBER">OCTOBER</option>
                            <option value="NOVEMBER">NOVEMBER</option>
                            <option value="DECEMBER">DECEMBER</option>
                          </select>
                          </div>
                        </div>
                        </div>
                        <div class="col-lg-6">
                          <div class="form-group">
                          <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Year</label>
                            <div class="col-lg-9">
                            <select name="year" id="" class="form-control input-sm">
                          <option value="2013">2013</option>
                          <option value="2014">2014</option>
                          <option value="2015">2015</option>
                          <option value="2016">2016</option>
                          <option value="2017">2017</option>
                          <option value="2018">2018</option>
                          <option value="2019">2019</option>
                          <option value="2020">2020</option>
                          <option selected="selected" value="2021">2021</option>
                          <option value="2022">2022</option>
                          <option value="2023">2023</option>
                          <option value="2024">2024</option>
                          <option value="2025">2025</option>
                          <option value="2026">2026</option>
                          <option value="2027">2027</option>
                          <option value="2028">2028</option>
                          <option value="2029">2029</option>
                          <option value="2030">2030</option>
                          <option value="2031">2031</option>
                          <option value="2032">2032</option>
                          <option value="2033">2033</option>
                          <option value="2034">2034</option>
                          <option value="2035">2035</option>
                        </select>
                        </div>
                      </div>
                        </div>
                        <div class="col-lg-6" style="margin-top: 15px;">
                             <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Hours Worked</label>
                             <div class="col-lg-9">
                          <input type="text" name="hours_worked" placeholder="Hours Worked" class="form-control"> 
                        </div>
                        </div>
                        <div class="col-lg-6" style="margin-top: 15px;">
                              <div class="form-group">
                          <label for="inputEmail3" class="col-lg-3 control-label" style="margin-top: 5px;">Overtime Type</label>
                            <div class="col-lg-9">
                            <select name="overtime_type" id="" class="form-control input-sm">
                          <option value="Overtime 1.5">Overtime 1.5</option>
                          <option value="Overtime 2.0">Overtime 2.0</option>
                        </select>
                        </div>
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
                            <th scope="col">Hours Worked</th>
                            <th scope="col">Overtime Type</th>
                            <th scope="col">&nbsp;</th>
              </tr>
              @foreach($overtimeData as $key => $avle)
              <tr>
                <td>{{$key+1}}</td>
                <td>{{$avle->year}}</td>
                <td>{{$avle->month}}</td>
                <td>{{$avle->hours_worked}}</td>
                <td>{{$avle->overtime_type}}</td>
               <td><a href="{{route('OvertimeHours.delete',['id'=>$avle->id])}}"><input type="button" value="Delete" onclick="javascript:__doPostBack('ctl00$MainContent$gridTimesheet','DeleteWE$0')" class="btn btn-warning btn-xs"></td>
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
