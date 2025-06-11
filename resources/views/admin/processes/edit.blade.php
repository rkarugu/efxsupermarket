@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> {!! $title !!} </h3>
            </div>

            <br>
            @include('message')

            {!! Form::model($process, ['method' => 'PATCH','route' => [$model.'.update', $process->id],'class'=>'validate']) !!}
            {{ csrf_field() }}

            <div class="box-body">
                <div class="form-group">
                    <label for="operation" class="control-label col-sm-2"> Operation </label>
                    <div class="col-sm-10">
                        {!! Form::text('operation', null, ['placeholder' => 'Operation', 'required' => true, 'class' => 'form-control']) !!}
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="description" class="control-label col-sm-2"> Description </label>
                    <div class="col-sm-10">
                        {!! Form::text('description', null, ['placeholder' => 'Description', 'class' => 'form-control']) !!}
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="notes" class="control-label col-sm-2"> Notes </label>
                    <div class="col-sm-10">
                        {!! Form::text('notes', null, ['placeholder' => 'Notes', 'class' => 'form-control']) !!}
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="status" class="control-label col-sm-2"> Status </label>
                    <div class="col-sm-10">
                        <div style="margin-bottom: 10px;">
                            {!! Form::radio('status', 'active') !!} <span> Active </span>
                        </div>

                        <div>
                            {!! Form::radio('status', 'inactive') !!} <span> Inactive </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary"> Submit</button>
            </div>
            {!! Form::close() !!}
        </div>
    </section>
@endsection