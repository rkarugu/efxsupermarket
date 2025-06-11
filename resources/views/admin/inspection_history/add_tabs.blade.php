<style type="text/css">
    .bor{
        border: 1px solid #ccc;
    }
</style>

<div class="col-md-12">
    <div class="row">
        <div class="col-sm-7">
            <div class="row">
                <div class="col-sm-2 ">
                    <img src="{{asset('public/uploads/vehiclelist/'.$row->photo)}}" width="100px"  alt="image">
                </div>
                <div class="col-sm-10 ">
                 <span style="font-size: 25px;">{{ @$row->license_plate}}</span><br>
                 <span style="font-size: 12px; color: grey;">{{@$row->vehicle->title}} - {{@$row->vehicle->title}} - {{ @$row->models->title }} </span><br>
                 <span style="font-size: 12px;">Status - {{$row->status}} </span><br>

                </div>
            </div>
            
        </div>
        <div class="col-sm-5 ">
            <div class="row">
                <div class="col-sm-7"></div>
                <div class="col-sm-5">
                     
                     <div class="dropdown text-right">
                        <button class="btn dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-plus"></i> &nbsp; Add
                        <span class="caret"></span></button>
                        <ul class="dropdown-menu">
                          <li><a href="javascript:void(0)">Add Vehicle Assignment</a></li>
                          <li><a href="{{route('fuelentry.create')}}">Fuel Entry</a></li>
                          <li><a href="{{route('expensehistory.create')}}">Expense Entry</a></li>
                          <li><a href="{{route('servicehistory.create')}}">Service Entry</a></li>
                          <li><a href="{{route('issues.create')}}">Issues</a></li>
                          <li><a href="#">Inspection</a></li>
                          <li><a href="#">Service Reminder</a></li>
                          <li><a href="#">Vehicle Renewal Reminder</a></li>
                          <li><a href="#">Meter Entry</a></li>
                        </ul>
                    </div>   

                </div>
            </div>
            
        </div>
    </div>
</div>

<div class="col-md-12" style="height:30px;"></div>

<div class="col-md-12">
    <div class="tab">
        <a href="{{route('vehicle.show.overview',$row->id)}}"><button class="tablinks {{ Request::is('admin/vehicle/show/overview*')? 'active' : ''}}">Overview</button></a>
        <a href="{{route('vehicle.show.specs',$row->id)}}"><button class="tablinks {{ Request::is('admin/vehicle/show/specs*')? 'active' : ''}}" >Specs</button></a>    
        <a href="{{route('vehicle.show.financial',$row->id)}}"><button class="tablinks {{ Request::is('admin/vehicle/show/financial*')? 'active' : ''}}" >Financial</button></a>    
    </div>
     
</div>