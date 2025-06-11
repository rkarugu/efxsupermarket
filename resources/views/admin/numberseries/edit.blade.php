
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
                    <label for="inputEmail3" class="col-sm-2 control-label">Code</label>
                    <div class="col-sm-10">
                        {!! Form::text('code', null, ['maxlength'=>'20','placeholder' => 'Code', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Description</label>
                    <div class="col-sm-10">
                        {!! Form::textarea('description', null, ['maxlength'=>'1000','placeholder' => '', 'required'=>false, 'class'=>'form-control numberwithhifun']) !!}  
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Starting Number</label>
                    <div class="col-sm-10">
                        {!! Form::number('starting_number', null, ['min'=>'1','placeholder' => 'Starting Number', 'required'=>true, 'class'=>'form-control','step'=>'1']) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Type Number</label>
                    <div class="col-sm-10">
                        {!! Form::number('type_number', null, ['min'=>'0','placeholder' => 'Type Number', 'required'=>true, 'class'=>'form-control','step'=>'1']) !!}  
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


