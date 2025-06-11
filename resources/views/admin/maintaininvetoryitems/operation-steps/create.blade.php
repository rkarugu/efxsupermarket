@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="box-title">
                        {{ $inventoryItem->title }} Operation Step {{ $currentStep }}
                    </h3>

                    <a href="{{ route("$model.operation-steps.index", $inventoryItem->id) }}" class="btn btn-default" role="button">
                        << Back to Operation Steps
                    </a>
                </div>
            </div>

            <div class="box-body">
                <div style="margin-bottom: 10px;">
                    @include('message')
                </div>

                <form action="{{ route("$model.operation-steps.store", $inventoryItem->id) }}" method="post" class="form-horizontal">
                    {{ csrf_field() }}

                    <input type="hidden" value="{{ $currentStep }}" name="step_number">

                    <div class="form-group">
                        <label for="operation-step" class="col-sm-2 control-label"> Operation Step </label>
                        <div class="col-sm-10">
                            <select id="operation-step" class="form-control" name="operation_step_id" required>
                                @foreach($productionProcesses as $process)
                                    <option value="{{ $process->id }}"> {{ $process->operation }} </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="duration" class="col-sm-2 control-label"> Duration (minutes) </label>
                        <div class="col-sm-10">
                            <input type="number" id="duration" name="duration" class="form-control" value="0"
                                   placeholder="Step duration in minutes">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="quality_control_check" class="col-sm-2 control-label"> Quality Control Check </label>
                        <div class="col-sm-10">
                            <div>
                                {!! Form::radio('quality_control_check', true, true) !!} <span> Yes </span>
                            </div>
                            <div>
                                {!! Form::radio('quality_control_check', false) !!} <span> No </span>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
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
        $("#operation-step").select2();
    </script>
@endsection
