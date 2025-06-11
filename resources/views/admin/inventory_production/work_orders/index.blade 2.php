@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Production Work Orders </h3>

                    @if(count($orders) > 0)
                        <a href="{{ route("$base_route_name.create") }}" role="button" class="btn btn-primary">
                            Add Work Order
                        </a>
                    @endif
                </div>
            </div>

            <div class="box-body">
                <div style="margin-bottom: 10px;">
                    @include('message')
                </div>

                @if(count($orders) == 0)
                    <div class="d-flex flex-column">
                        <p> No work orders have been created yet. </p>
                        <a href="{{ route("$base_route_name.create") }}" role="button" class="btn btn-primary">
                            Add Work Order
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col"> Order Reference</th>
                                <th scope="col"> Production Plant</th>
                                <th scope="col"> Production Item</th>
                                <th scope="col"> Production Quantity</th>
                                <th scope="col"> Order Date</th>
                                <th scope="col"> Description</th>
                                <th scope="col"> Availability of BOM</th>
                                <th scope="col"> Status</th>
                                <th scope="col"> Actions</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($orders as $order) @endforeach
                            <tr>
                                <th scope="row"> {{ $order['order_reference'] }} </th>
                                <td> {{ $order['production_plant']->location_name }}</td>
                                <td> {{ $order['production_item']->title }}</td>
                                <td> {{ $order['production_quantity_with_pack_size'] }}</td>
                                <td> {{ $order['order_date'] }}</td>
                                <td> {{ $order['description'] }}</td>
                                <td>
                                    <span class="label @if($order['bom_is_available']) label-success @else label-danger @endif"
                                          style="font-size: 14px;"> @if($order['bom_is_available'])
                                            In Stock
                                        @else
                                            Unavailable
                                        @endif</span>
                                </td>
                                <td> {{ $order['status'] }}</td>
                                <td>
                                    <a href="{{ route("$base_route_name.edit", $order['id']) }}"
                                       style="font-size: 16px; margin-right: 12px;" title="Edit Order">
                                        <i class="fa fa-edit"></i>
                                    </a>

{{--                                    @if($order['status'] == 'Not Started')--}}
                                    <form action="{{ route("$base_route_name.start", $order['id']) }}" method="post"
                                          style="display: inline-block;" title="Start Work" id="start-work-form">
                                        {{ @csrf_field() }}

                                        <button type="submit"><i class="fa fa-play-circle text-primary"></i></button>
                                    </form>
{{--                                    @endif--}}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@section('uniquepagescript')
    <script type="text/javascript">
        function startWorkOrder() {
            $("#start-work-form").submit();
        }
    </script>
@endsection
