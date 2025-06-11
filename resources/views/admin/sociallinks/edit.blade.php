
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
                    <label for="inputEmail3" class="col-sm-2 control-label">Social Name</label>
                    <div class="col-sm-10">
                        {!! Form::text('slug', null, ['maxlength'=>'255','placeholder' => 'Social Name', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>

            <div class="box-body">
                 <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Link URL</label>
                    <div class="col-sm-10">
                        {!! Form::url('social_link', null, ['maxlength'=>'500','placeholder' => 'Social URL', 'required'=>true, 'class'=>'form-control']) !!}  
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


