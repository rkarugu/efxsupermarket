@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> {!! $title !!} </h3>
            </div>
            <form class="validate form-horizontal" role="form" method="POST" action="{{ route($model.'.store') }}">
                {{ csrf_field() }}

                <div class="box-body">
                    <div class="form-group">
                        <label for="operation" class="control-label col-sm-2"> Operation </label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="operation" placeholder="Operation" id="operation" required>
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="description" class="control-label col-sm-2"> Description </label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="description" placeholder="Description" id="description">
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="notes" class="control-label col-sm-2"> Notes </label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="notes" placeholder="Notes" id="notes">
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="status" class="control-label col-sm-2"> Status </label>
                        <div class="col-sm-10">
                            <div style="margin-bottom: 10px;">
                                {!! Form::radio('status', 'active', true) !!} <span> Active </span>
                            </div>

                            <div>
                                {!! Form::radio('status', 'inactive') !!} <span> Inactive </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary"> Submit </button>
                </div>
            </form>
        </div>
    </section>
@endsection