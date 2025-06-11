
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> Edit Geomapping Schedules - {{$branch->name.' - '}}  {{$route->route_name}}</h3></div>
         @include('message')
        <form class="validate form-horizontal"  role="form" method="POST" action="{{ route('geomapping-schedules.update', $schedule->id) }}" enctype = "multipart/form-data">
            @method('PUT')
            {{ csrf_field() }}
            <div class="box-body">
                <div class="form-group">
                    <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Date</label>
                    <div class="col-sm-9" >
                        {!! Form::date('date', $schedule->date, ['required'=>true, 'class'=>'form-control' ]) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Supervisor</label>
                    <div class="col-sm-9">
                        {!! Form::text('supervisor', $schedule->route_manager, ['maxlength'=>'255', 'required'=>true, 'class'=>'form-control' ]) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Supervisor Contact</label>
                    <div class="col-sm-9">
                        {!! Form::text('supervisor_contact', $schedule->route_manager_contact, ['maxlength'=>'255', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Route Manager</label>
                    <div class="col-sm-9">
                        {!! Form::text('supervisor2', $schedule->supervisor, ['maxlength'=>'255', 'class'=>'form-control' ]) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Route Manager Contact</label>
                    <div class="col-sm-9">
                        {!! Form::text('supervisor_contact2', $schedule->supervisor_contact, ['maxlength'=>'255', 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Golden  Africa Rep</label>
                    <div class="col-sm-9">
                        {!! Form::text('ga_rep', $schedule->golden_africa_rep, ['maxlength'=>'255', 'class'=>'form-control' ]) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">GA Rep Contact</label>
                    <div class="col-sm-9">
                        {!! Form::text('ga_rep_contact', $schedule->golden_africar_rep_contact, ['maxlength'=>'255', 'class'=>'form-control']) !!}  
                    </div>
                </div>
                </div>
               
            </div>
          
             
             


            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</section>
@endsection



