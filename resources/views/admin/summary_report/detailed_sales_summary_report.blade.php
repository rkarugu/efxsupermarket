@extends('layouts.admin.admin')

@section('content')
    <section class="content">
       
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Detailed Sales Summary Report </h3>
                </div>
            </div>

            <div class="box-body">
                {{-- {!! Form::open(['route' => 'salesman-shifts.index', 'method' => 'get']) !!}
                <div class="row">
                        <div class="col-md-3 form-group">
                            <select name="branch" id="branch" class="form-control mlselect"  data-url="{{ route('admin.get-branch-routes') }}">
                                <option value="" selected disabled>Select branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{$branch->id}}" 
                                        {{ request()->has('branch') ? ($branch->id == request()->branch ? 'selected' : '') : ($branch->id == $authuser->restaurant_id ? 'selected' : '') }}>
                                        {{$branch->name}}
                                    </option>

                                @endforeach
                            </select>

                        </div>
                    <div class="col-md-2 form-group">
                        <select name="route" id="route" class="mlselect form-control">
                            <option value="" selected disabled>Select Route</option>
                            @foreach ($routes as $route )
                                <option value="{{$route->id}}" {{ $route->id == request()->route ? 'selected' : '' }}>{{$route->route_name}}</option>

                            @endforeach
                        </select>

                    </div>

                    <div class="col-md-2 form-group">
                        <input type="date" name="start-date" id="from" class="form-control" value="{{ request()->get('start-date') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-2 form-group">
                        <input type="date" name="end-date" id="to" class="form-control" value="{{ request()->get('end-date') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-3 form-group">
                        <button type="submit" class="btn btn-success" name="manage-request" value="filter">Filter</button>
                        <input type="submit" class="btn btn-success" name="type" value="Download">
                        <a class="btn btn-success ml-12" href="{!! route('salesman-shifts.index') !!}">Clear </a>
                    </div>
                </div>

                {!! Form::close(); !!} --}}
                <hr>

                @include('message')
                <div class="col-md-12">
                    <h4 class="box-title"> Sales </h4>


                    <table class="table table-bordered table-hover" id="create_datatable_25">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Route</th>
                            <th>Invoice</th>
                            <th>Vatable Sale</th>
                            <th>Vat</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                            @php
                                $grand_total_vatable = $grand_total_sales = $grand_total_vat = 0;
                            @endphp
                            @foreach ($salesData as $sale)
                                <tr>
                                    <th>{{$loop->index+1}}</th>
                                    <td>{{$sale->route}}</td>
                                    <td>{{$sale->invoice_no}}</td>
                                    <td style="text-align:right;">{{manageAmountFormat($sale->total_sales - $sale->vat_amount)}}</td>
                                    <td style="text-align:right;">{{manageAmountFormat($sale->vat_amount)}}</td>
                                    <td style="text-align:right;">{{manageAmountFormat($sale->total_sales)}}</td>

                                </tr>
                                @php
                                    $grand_total_vatable += ($sale->total_sales - $sale->vat_amount);
                                    $grand_total_vat += $sale->vat_amount;
                                    $grand_total_sales += $sale->total_sales;
                                @endphp
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3">Total</th>
                                <th style="text-align: right;">{{manageAmountFormat($grand_total_vatable)}}</th>
                                <th style="text-align: right;">{{manageAmountFormat($grand_total_vat)}}</th>
                                <th style="text-align: right;">{{manageAmountFormat($grand_total_sales)}}</th>


                            </tr>
                        </tfoot>
                       
                    </table>
                </div>
               
                <div class="col-md-12">
                    <h4 class="box-title"> Returns</h4>

                    <table class="table table-bordered table-hover" id="create_datatable_50">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Route</th>
                            <th>Return</th>
                            <th>Invoice</th>
                            <th>Vatable Return</th>
                            <th>Vat</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                            @php
                                $grand_total_return_value_vatable = $grand_total_return_value = $grand_total_return_vat = 0;
                            @endphp
                            @foreach ($returnsData as $return)
                                <tr>
                                    <th>{{$loop->index+1}}</th>
                                    <td>{{$return->route}}</td>
                                    <td>{{$return->return_no}}</td>
                                    <td>{{$return->invoice_no}}</td>
                                    <td style="text-align:right;">{{manageAmountFormat($return->return_value - (($vat_rate * $return->return_value ) / (100 + $vat_rate)))}}</td>
                                    <td style="text-align:right;">{{manageAmountFormat((($vat_rate * $return->return_value ) / (100+ $vat_rate)))}}</td>
                                    <td style="text-align:right;">{{manageAmountFormat($return->return_value)}}</td>

                                </tr>
                                @php
                                    $grand_total_return_value_vatable += ($return->return_value - (($vat_rate * $return->return_value ) / (100 +$vat_rate)));
                                    $grand_total_return_vat += (($vat_rate * $return->return_value ) / (100+ $vat_rate));
                                    $grand_total_return_value += $return->return_value;
                                @endphp
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4">Total</th>
                                <th style="text-align: right;">{{manageAmountFormat($grand_total_return_value_vatable)}}</th>
                                <th style="text-align: right;">{{manageAmountFormat($grand_total_return_vat)}}</th>
                                <th style="text-align: right;">{{manageAmountFormat($grand_total_return_value)}}</th>


                            </tr>
                        </tfoot>
                       
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection


