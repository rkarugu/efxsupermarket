@extends('layouts.admin.admin')

@section('content')
    <section class="content" id="bom-container">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Bill of Materials For {{ $inventoryItem->title }} </h3>
            </div>

            <div class="alert-message">
                @include('message')
            </div>

            <div class="box-body">
                @if(count($bomPayload) == 0)
                    <p> This item has no bill of materials. Click on the button below to add.</p>
                    <a class="btn btn-primary" href="{{ route($model.'.add-bom-item', $inventoryItem->id) }}">Create BOM</a>
                @else
                    <div style="display: flex; justify-content: end; margin-bottom: 10px;">
                        <a class="btn btn-primary" href="{{ route($model.'.add-bom-item', $inventoryItem->id) }}">Add BOM Item</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead>
                            <tr>
                                <th scope="col"> #</th>
                                <th scope="col"> Raw Material</th>
                                <th scope="col"> Quantity</th>
                                <th scope="col"> Unit Cost</th>
                                <th scope="col"> Stock Cost</th>
                                <th scope="col"> Notes</th>
                                <th scope="col"> Actions</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($bomPayload as $index => $bomMaterial)
                                <tr>
                                    <th scope="row"> {{ $index + 1 }} </th>
                                    <td> {{ $bomMaterial['title'] }} </td>
                                    <td> {{ $bomMaterial['quantity'] }} </td>
                                    <td> {{ $bomMaterial['unit_cost'] }} </td>
                                    <td> {{ $bomMaterial['stock_cost'] }} </td>
                                    <td> {{ $bomMaterial['notes'] ?? '-' }} </td>
                                    <td>
                                        <form action="{{ route($model.'.remove-bom-item', $bomMaterial['id']) }}" method="POST">
                                            {{ csrf_field() }}
                                            <button role="button" class="text-danger"><i class="fa fa-trash text-danger"></i></button>
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
