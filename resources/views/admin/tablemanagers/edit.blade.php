
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
         {!! Form::model($row, ['method' => 'PATCH','route' => [$model.'.update', $row->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}
           <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-10">
                        {!! Form::text('name', null, ['maxlength'=>'255','placeholder' => 'Name', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Branch</label>
                    <div class="col-sm-10">
                        {!!Form::select('restaurant_id', $restroList, null, ['placeholder'=>'Select Branch ', 'class' => 'form-control','required'=>true,'title'=>'Please select Branch'  ])!!}
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Section</label>
                    <div class="col-sm-10">
                        {!!Form::select('block_section', $tableBlockSection, null, ['placeholder'=>'Select Section ', 'class' => 'form-control','required'=>true,'title'=>'Please select section'  ])!!}
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Capacity</label>
                    <div class="col-sm-10">

                    {!!Form::select('capacity',array_combine(range(1,50), range(1,50)), null, ['placeholder'=>'Select Capacity ', 'class' => 'form-control','required'=>true,'title'=>'Please select capacity'  ])!!}
                        
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Booking Status</label>
                    <div class="col-sm-10">

                    {!!Form::select('booking_status',$booking_status_arr, null, ['placeholder'=>'Select Status ', 'class' => 'form-control','required'=>true  ])!!}
                        
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


