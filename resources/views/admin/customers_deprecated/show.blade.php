
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
        <form>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Profile:</label>
                    <div class="col-sm-10">
                    @if($row->image)
                        <img src="{{ asset('uploads/users/thumb/'.$row->image) }}" height="100px" width="100px"/>
                        @else
                         <img src="{{ asset('uploads/nouser.jpg') }}" height="100px" width="100px"/>
                        @endif
                    </div>
                </div>
            </div>

           <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Name:</label>
                    <div class="col-sm-10">
                        {!! $row->name !!}
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Email:</label>
                    <div class="col-sm-10">
                        {!! $row->email !!}
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">DOB:</label>
                    <div class="col-sm-10">
                        {!! convertDMYtoYMD($row->dob) !!}
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Phone Number:</label>
                    <div class="col-sm-10">
                        {!! $row->phone_number !!}
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Gender:</label>
                    <div class="col-sm-10">
                       {!! $row->gender !!}
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Created at:</label>
                    <div class="col-sm-10">
                        {!! date('Y-m-d h:i:s',strtotime($row->created_at)) !!}
                    </div>
                </div>
            </div>
           
        </form>
    </div>
</section>
@endsection

@section('uniquepagestyle')
 
@endsection

@section('uniquepagescript')


@endsection


