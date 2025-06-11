@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Work Order {{ $work_order['order_reference'] }} </h3>

                    <a href="{{ route("$base_route_name.index") }}" role="button" class="btn btn-outline-primary">
                        << Back to Work Orders
                    </a>
                </div>
            </div>

            <div class="box-body">
                <div class="d-flex flex-wrap">
                    <div class="order-parameter d-flex flex-column">
                        <span class="parameter-title"> Date Created</span>
                        <span class="parameter-value"> {{ $work_order['order_date'] }}</span>
                    </div>

                    <div class="order-parameter d-flex flex-column">
                        <span class="parameter-title"> Production Plant</span>
                        <span class="parameter-value"> {{ $work_order['production_plant'] }}</span>
                    </div>

                    <div class="order-parameter d-flex flex-column">
                        <span class="parameter-title"> Production Item</span>
                        <span class="parameter-value"> {{ $work_order['production_item'] }}</span>
                    </div>

                    <div class="order-parameter d-flex flex-column">
                        <span class="parameter-title"> QOH</span>
                        <span class="parameter-value"> {{ $work_order['production_item_qoh'] }}</span>
                    </div>

                    <div class="order-parameter d-flex flex-column">
                        <span class="parameter-title"> BOM Availability</span>
                        <span class="parameter-value"> {{ $work_order['bom_availability'] }}</span>
                    </div>

                    <div class="order-parameter d-flex flex-column">
                        <span class="parameter-title"> Production Status</span>
                        <span class="parameter-value"> {{ $work_order['status'] }}</span>
                    </div>

                    <div class="order-parameter d-flex flex-column">
                        <span class="parameter-title"> Notes</span>
                        <span class="parameter-value"> {{ $work_order['description'] }}</span>
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <div class="box-header with-border">
                        <h3 class="box-title"> Bill of Materials </h3>
                    </div>

                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col"> #</th>
                                    <th scope="col"> Raw Material</th>
                                    <th scope="col"> Base Quantity</th>
                                    <th scope="col"> Required Quantity</th>
                                    <th scope="col"> QOH</th>
                                    <th scope="col"> Availability</th>
                                    <th scope="col"> Unit Cost</th>
                                    <th scope="col"> Total Cost</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($work_order['bom'] as $index => $bomItem)
                                    <tr>
                                        <th scope="row"> {{ $index + 1 }}</th>
                                        <td> {{ $bomItem['raw_material_name'] }}</td>
                                        <td> {{ $bomItem['base_quantity'] }}</td>
                                        <td> {{ $bomItem['required_quantity'] }}</td>
                                        <td> {{ $bomItem['qoh'] }}</td>
                                        <td> {{ $bomItem['availability'] }}</td>
                                        <td> {{ $bomItem['unit_cost'] }}</td>
                                        <td> {{ $bomItem['total_cost'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <div class="box-header with-border">
                        <h3 class="box-title"> Operation Steps </h3>
                    </div>

                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col"> Step Number</th>
                                    <th scope="col"> Operation</th>
                                    <th scope="col"> Duration</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($work_order['operations'] as $index => $operation)
                                    <tr>
                                        <th scope="row"> {{ $operation['step_number'] }}</th>
                                        <td> {{ $operation['operation'] }}</td>
                                        <td> {{ $operation['duration'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <style>
        .order-parameter {
            margin-right: 50px;
            margin-bottom: 20px;
        }

        .parameter-title, .parameter-value {
            font-size: 15px;
        }

        .parameter-title {
            font-weight: 700;
            margin-bottom: 2px;
        }
    </style>
@endsection
