@extends('layouts.admin.admin')

@section('content')
    <section class="content">
       
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Detailed Stock Take Sales Summary Report </h3>
                    <div>
                        {{-- <a href="{{route('detailed-sales-summary-report.excel-download', ['date'=>$date])}}" class="btn btn-primary btn-sm" ><i class="fas fa-file-excel" ></i>  Export</a> --}}
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
                            <th>Document No</th>
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
                                    <td>{{$sale->document_no}}</td>
                                    <td style="text-align:right;">{{manageAmountFormat($sale->total_sales - $sale->total_tax)}}</td>
                                    <td style="text-align:right;">{{manageAmountFormat($sale->total_tax)}}</td>
                                    <td style="text-align:right;">{{manageAmountFormat($sale->total_sales)}}</td>
                                </tr>
                                @php
                                    $grand_total_vatable += ($sale->total_sales - $sale->total_tax);
                                    $grand_total_vat += $sale->total_tax;
                                    $grand_total_sales += $sale->total_sales;
                                @endphp
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2">Total</th>
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
                            <th>Document No</th>
                            <th>Vatable Sale</th>
                            <th>Vat</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                            @php
                                $grand_total_vatable = $grand_total_sales = $grand_total_vat = 0;
                            @endphp
                            @foreach ($returnsData as $sale)
                                <tr>
                                    <th>{{$loop->index+1}}</th>
                                    <td>{{$sale->document_no}}</td>
                                    <td style="text-align:right;">{{manageAmountFormat($sale->total_sales - $sale->total_tax)}}</td>
                                    <td style="text-align:right;">{{manageAmountFormat($sale->total_tax)}}</td>
                                    <td style="text-align:right;">{{manageAmountFormat($sale->total_sales)}}</td>
                                </tr>
                                @php
                                    $grand_total_vatable += ($sale->total_sales - $sale->total_tax);
                                    $grand_total_vat += $sale->total_tax;
                                    $grand_total_sales += $sale->total_sales;
                                @endphp
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2">Total</th>
                                <th style="text-align: right;">{{manageAmountFormat($grand_total_vatable)}}</th>
                                <th style="text-align: right;">{{manageAmountFormat($grand_total_vat)}}</th>
                                <th style="text-align: right;">{{manageAmountFormat($grand_total_sales)}}</th>
                            </tr>
                        </tfoot>
                       
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection


