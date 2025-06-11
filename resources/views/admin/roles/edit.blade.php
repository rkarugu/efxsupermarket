
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
                    <label for="inputEmail3" class="col-sm-2 control-label">Title</label>
                    <div class="col-sm-10">
                        {!! Form::text('title', null, ['maxlength'=>'255','placeholder' => 'Name', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>

                <div class="form-group">
                    <label for="is_hq_role" class="col-sm-2 control-label" style="padding: 0;">HQ Role</label>
                    <div class="col-sm-10">
                        <input type="checkbox" name="is_hq_role" id="is_hq_role" @if($row->is_hq_role) checked @endif>
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



@section('uniquepagescript')

@endsection


