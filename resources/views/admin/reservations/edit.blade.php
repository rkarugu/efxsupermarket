
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
         {!! Form::model($row, ['method' => 'PATCH','route' => [$model.'.update', $row->id],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}


            <div class="box-body">
                 <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Id</label>
                    <div class="col-sm-10">
                       {!! $row->id !!}
                    </div>
                </div>
            </div>

             <div class="box-body">
                 <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">User</label>
                    <div class="col-sm-10">
                       {!! $row->getAssociateUser->name !!}
                    </div>
                </div>
            </div>

             <div class="box-body">
                 <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Branch</label>
                    <div class="col-sm-10">
                       {!! $row->getAssociateRestro->name !!}
                    </div>
                </div>
            </div>

             <div class="box-body">
                 <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-10">
                       {!! $row->email !!}
                    </div>
                </div>
            </div>

             <div class="box-body">
                 <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Phone Number</label>
                    <div class="col-sm-10">
                       {!! $row->phone_number !!}
                    </div>
                </div>
            </div>

             <div class="box-body">
                 <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Event Type</label>
                    <div class="col-sm-10">
                       {!! $row->event_type !!}
                    </div>
                </div>
            </div>

            <div class="box-body">
                 <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">No Of Person</label>
                    <div class="col-sm-10">
                       {!! $row->no_of_person !!}
                    </div>
                </div>
            </div>

            <div class="box-body">
                 <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Reservation Time</label>
                    <div class="col-sm-10">
                       {!! date('Y-m-d h:i A',strtotime($row->reservation_time)) !!}
                    </div>
                </div>
            </div>

            <div class="box-body">
                 <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Created At</label>
                    <div class="col-sm-10">
                       {!! date('Y-m-d h:i A',strtotime($row->created_at)) !!}
                    </div>
                </div>
            </div>

             <div class="box-body">
                 <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Comment</label>
                    <div class="col-sm-10">
                       {!! $row->comment !!}
                    </div>
                </div>
            </div>





             <div class="box-body">
                 <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Status</label>
                    <div class="col-sm-10">
                         {!!Form::select('status', ['NEW'=>'NEW','CANCLED'=>'CANCLED','CONFIRMED'=>'CONFIRMED'], null, ['class' => 'form-control '  ])!!}  
                    </div>
                </div>
            </div>
           
             


            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</section>
@endsection

@section('uniquepagestyle')

@endsection

@section('uniquepagescript')

@endsection


