@extends('layouts.admin.admin')

@section('content')
    <section class="content">
       
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Detailed Sales Summary Report </h3>
                    <div>
                        <a href="{{route('detailed-sales-summary-report.excel-download', ['date'=>$date])}}" class="btn btn-primary btn-sm" ><i class="fas fa-file-excel" ></i>  Export</a>
                    </div>
                </div>
            </div>

            <div class="box-body">
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
                                    <td style="text-align:right;">{{manageAmountFormat($sale->vatable_total_sales - $sale->vat_amount)}}</td>
                                    <td style="text-align:right;">{{manageAmountFormat($sale->vat_amount)}}</td>
                                    <td style="text-align:right;">{{manageAmountFormat($sale->total_sales)}}</td>
                                </tr>
                                @php
                                    $grand_total_vatable += ($sale->vatable_total_sales - $sale->vat_amount);
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
                                    <td style="text-align:right;">{{manageAmountFormat($return->return_value - (($return->vat_rate * $return->return_value ) / (100 + $return->vat_rate)))}}</td>
                                    <td style="text-align:right;">{{manageAmountFormat((($return->vat_rate * $return->return_value ) / (100+ $return->vat_rate)))}}</td>
                                    <td style="text-align:right;">{{manageAmountFormat($return->return_value)}}</td>

                                </tr>
                                @php
                                    $grand_total_return_value_vatable += ($return->return_value - (($return->vat_rate * $return->return_value ) / (100 +$return->vat_rate)));
                                    $grand_total_return_vat += (($return->vat_rate * $return->return_value ) / (100+ $return->vat_rate));
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


