@extends('layouts.admin.admin')
@section('content')

    <style type="text/css">
        /* Style the tab */
        .tab {
            overflow: hidden;
            /*  border: 1px solid #ccc;
            */
            background-color: #c1ccd1;
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

        .ctable, td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
    </style>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header"></div>
            @include('message')
            <div class="data-description">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12" style="margin-bottom:2rem">
                            @include('admin/vehicle/add_tabs')

                            <div class="col-md-6">
                                <div class="col-md-12" style="box-shadow: 0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03); margin-top: 2rem;">

                                    <h4 style="padding: 26px 20px 0;">Details - </h4>
                                    <hr>

                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-md-4 control-label">Vehicle</label>
                                            <div class="col-md-8">
                                                <p>{{ @$row->license_plate}}  </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-md-4 control-label">Acquisition Date</label>
                                            <div class="col-md-8">
                                                <p>{{$row->acquisition_date}}</p>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-md-4 control-label">Vin Sn</label>
                                            <div class="col-md-8">
                                                <p>{{@$row->vin_sn }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-md-4 control-label">Type</label>
                                            <div class="col-md-8">
                                                <p>{{@$row->vehicle->title}}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-md-4 control-label">Year</label>
                                            <div class="col-md-8">
                                                <p>{{ $row->year }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-md-4 control-label">Make</label>
                                            <div class="col-md-8">
                                                <p>{{ @$row->make->title??'-' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-md-4 control-label">Model</label>
                                            <div class="col-md-8">
                                                <p>{{ @$row->models->title??'-' }}</p>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="box-body">
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-md-4 control-label">Trim</label>
                                            <div class="col-md-8">
                                                <p>{{ $row->trim }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="row">

                                    <div class="col-md-12" style="box-shadow: 0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03); margin-top: 2rem; ">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <h4 style="padding: 26px 20px 0;">Open Issues - </h4>
                                                <hr>
                                            </div>
                                            <div class="col-md-4" style="padding: 35px 20px 0; text-align: right;">
                                                <span>
                                                    <a href="{{route('issues.create',['vehicle_id'=>$row->id,'license_plate'=>$row->license_plate])}}">+ Add Issue</a> &nbsp; | &nbsp; <a
                                                            href="{{route('vehicle.show.issues',$row->id)}}">View All</a>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="row" style="border:1px solid #ccc; padding: 10px; border-radius: 10px; margin-bottom: 5%;">
                                                <div class="col-md-6">
                                                    <span>Overdue</span><br>
                                                    <span style="font-size:20px"><b>{{$issueOverDueCount}}</b></span>
                                                </div>
                                                <div class="col-md-6" style="border-left:1px solid #ccc;">
                                                    <span>Open</span><br>
                                                    <span style="font-size:20px; color:orange;"><b>{{$issueOpenCount}}</b></span>
                                                </div>

                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-md-12" style="box-shadow: 0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03); margin-top: 2rem; ">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h4 style="padding: 26px 20px 0;">Service Remainder - </h4>
                                                <hr>
                                            </div>
                                            <div class="col-md-6" style="padding: 35px 20px 0; text-align: right;">
                                               <span>
                                                <a href="{{route('service_remainder.create',['vehicle_id'=>$row->id,'license_plate'=>$row->license_plate])}}">+ Add Service Remainder</a> &nbsp; | &nbsp; <a
                                                           href="{{route('vehicle.show.service_remainder',$row->id)}}">View All</a>

                                               </span>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="row" style="border:1px solid #ccc; padding: 10px; border-radius: 10px; margin-bottom: 5%;">
                                                <div class="col-md-4">
                                                    <span>Overdue</span><br>
                                                    <span style="font-size:20px"><b>{{$remaindersOverdueCount}}</b></span>
                                                </div>
                                                <div class="col-md-4" style="border-left:1px solid #ccc;">
                                                    <span>Due Soon</span><br>
                                                    <span style="font-size:20px; color:orange;"><b>{{$remaindersDueCount}}</b></span>
                                                </div>

                                                <div class="col-md-4" style="border-left:1px solid #ccc;">
                                                    <span>Snoozed</span><br>
                                                    <span style="font-size:20px;"><b>{{$remaindersSnoozedCount}}</b></span>
                                                </div>

                                            </div>
                                        </div>

                                    </div>


                                    <div class="col-md-12" style="box-shadow: 0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03); margin-top: 2rem; ">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h4 style="padding: 26px 20px 0;">Inspections - </h4>
                                                <hr>
                                            </div>
                                            <div class="col-md-6" style="padding: 35px 20px 0; text-align: right;">
                                                <span>
                                                    <a href="{{route('inspection_history.create',['vehicle_id'=>$row->id,'license_plate'=>$row->license_plate])}}">+ Add Inspection</a> &nbsp; | &nbsp; <a
                                                            href="{{route('vehicle.show.inspection_history',$row->id)}}">View All</a>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="row" style="border:1px solid #ccc; padding: 10px; border-radius: 10px; margin-bottom: 5%;">
                                                <div class="col-md-6">
                                                    <span>Pass</span><br>
                                                    <span style="font-size:20px"><b>{{$inspectionPassCount}}</b></span>
                                                </div>
                                                <div class="col-md-6" style="border-left:1px solid #ccc;">
                                                    <span>Fail</span><br>
                                                    <span style="font-size:20px; color:orange;"><b>{{$inspectionFailCount}}</b></span>
                                                </div>

                                            </div>
                                        </div>
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
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">
        $(function () {
            $('.select2').select2();
            $("#selector_selects2").select2();
        });
    </script>

    <style type="text/css">
        .cont {
            text-align: justify;
            text-justify: inter-word;;
        }
    </style>

@endsection