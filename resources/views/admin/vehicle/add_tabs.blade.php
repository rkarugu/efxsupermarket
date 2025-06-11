<style type="text/css">
    .bor {
        border: 1px solid #ccc;
    }
</style>

<div class="col-md-12">
    <div class="row">
        <div class="col-sm-7">
            <div class="row">
                <div class="col-sm-2 ">
                    <img src="https://fordtrucksglobal.com/Uploads/Page/road-4.png" width="100px" alt="image">
                    {{--                    <img src="{{asset('public/uploads/vehiclelist/'.$row->photo)}}" width="100px" alt="image">--}}
                </div>
                <div class="col-sm-10 ">
                    <span style="font-size: 25px;">{{ @$row->license_plate}}</span><br>
                    <span style="font-size: 12px; color: grey;">{{@$row->vehicle->title}} - {{@$row->vmake->title}} - {{ @$row->models->title }} </span><br>
                    <span style="font-size: 12px;">Status - {{$row->status}} </span><br>

                </div>
            </div>

        </div>
        <div class="col-sm-5 ">
            <div class="row">
                <div class="col-sm-7"></div>
                <div class="col-sm-5">
                    <div class="dropdown text-right">
                        <button class="btn dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-plus"></i> &nbsp;
                            Add <span class="caret"></span>
                        </button>

                        <ul class="dropdown-menu">
                            <li><a href="javascript:void(0)">Add Vehicle Assignment</a></li>
                            <li><a href="{{route('fuelentry.create',['vehicle_id'=>$row->id,'license_plate'=>$row->license_plate])}}">Fuel Entry</a></li>
                            <li><a href="{{route('expensehistory.create',['vehicle_id'=>$row->id,'license_plate'=>$row->license_plate])}}">Expense Entry</a></li>
                            <li><a href="{{route('servicehistory.create',['vehicle_id'=>$row->id,'license_plate'=>$row->license_plate])}}">Service Entry</a></li>
                            <li><a href="{{route('issues.create',['vehicle_id'=>$row->id,'license_plate'=>$row->license_plate])}}">Issues</a></li>
                            <li><a href="{{route('inspection_history.index',['modal'=>true,'vehicle_id'=>$row->id,'license_plate'=>$row->license_plate])}}">Inspection</a></li>
                            <li><a href="{{route('service_remainder.create',['vehicle_id'=>$row->id,'license_plate'=>$row->license_plate])}}">Service Reminder</a></li>

                            {{--
                              <li><a href="{{route('meterhistory.index',['modal'=>true,'vehicle_id'=>$row->id,'license_plate'=>$row->license_plate])}}">Odometer History</a></li>
                            --}}
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
        <a href="{{route('vehicle.show.location',$row->id)}}">
            <button class="tablinks {{ Request::is('admin/vehicle/show/location*')? 'active' : ''}}">Live Location</button>
        </a>

        <a href="{{route('vehicle.show.overview',$row->id)}}">
            <button class="tablinks {{ Request::is('admin/vehicle/show/overview*')? 'active' : ''}}">Overview</button>
        </a>
        <a href="{{route('vehicle.show.fuelentries',$row->id)}}">
            <button class="tablinks {{ Request::is('admin/vehicle/show/fuelentries*')? 'active' : ''}}">Fuel History</button>
        </a>
        <a href="{{route('vehicle.show.expensehistory',$row->id)}}">
            <button class="tablinks {{ Request::is('admin/vehicle/show/expensehistory*')? 'active' : ''}}">Expense History</button>
        </a>
        <a href="{{route('vehicle.show.servicehistory',$row->id)}}">
            <button class="tablinks {{ Request::is('admin/vehicle/show/servicehistory*')? 'active' : ''}}">Service History</button>
        </a>
        <a href="{{route('vehicle.show.issues',$row->id)}}">
            <button class="tablinks {{ Request::is('admin/vehicle/show/issues*')? 'active' : ''}}">Issues</button>
        </a>
        <a href="{{route('vehicle.show.inspection_history',$row->id)}}">
            <button class="tablinks {{ Request::is('admin/vehicle/show/inspection_history*')? 'active' : ''}}">Inspections</button>
        </a>
        <a href="{{route('vehicle.show.service_remainder',$row->id)}}">
            <button class="tablinks {{ Request::is('admin/vehicle/show/service_remainder*')? 'active' : ''}}">Service Remainder</button>
        </a>

        <a href="{{route('vehicle.show.meter_history',$row->id)}}">
            <button class="tablinks {{ Request::is('admin/vehicle/show/meter_history*')? 'active' : ''}}">Odometer History</button>
        </a>

    </div>

</div>