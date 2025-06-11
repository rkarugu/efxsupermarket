@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> {{ $inventoryItem->title }} Operation Steps </h3>
            </div>

            <div class="box-body">
                @if(count($operationSteps) == 0)
                    <div style="display: flex; flex-direction: column;">
                        <p> No operation steps have been added to this item. </p>

                        <div>
                            <a role="button" href="{{ route("$model.operation-steps.create", $inventoryItem->id) }}" class="btn btn-primary">
                                Add Step</a>
                        </div>
                    </div>
                @else
                    <div class="table-responsive">
                        <div style="margin-bottom: 10px;">
                            @include('message')
                        </div>

                        <div style="display: flex; justify-content: end;">
                            <a role="button" href="{{ route("$model.operation-steps.create", $inventoryItem->id) }}" class="btn btn-primary">
                                Add Step </a>
                        </div>

                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">Step Number</th>
                                <th scope="col"> Operation</th>
                                <th scope="col"> Duration</th>
                                <th scope="col"> Quality Control Check</th>
                                <th scope="col"> Actions</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($operationSteps as $step)
                                <tr>
                                    <th scope="row"> {{ $step['step_number'] }} </th>
                                    <td> {{ $step['name'] }} </td>
                                    <td> {{ $step['duration'] }} </td>
                                    <td> {{ $step['quality_control_check'] }} </td>
                                    <td>
                                        <a href="{{ route("$model.operation-steps.edit", ['itemId' => $inventoryItem->id, 'processId' =>
                                        $step['process_id']]) }}"
                                           style="font-size: 16px; margin-right: 12px;" title="Update Step">
                                            <i class="fa fa-edit"></i>
                                        </a>

                                        <form action="{{ route("$model.operation-steps.destroy", ['itemId' => $inventoryItem->id,'processId' => $step['process_id']]) }}"
                                              method="post" style="display: inline-block;" title="Remove Step">
                                            {{ @csrf_field() }}
                                            <button type="submit">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection