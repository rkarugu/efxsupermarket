
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                        <tr>
                                            <th width="20%">Sales Date</th>
                                            <th width="20%">Gross Sale Amount</th>
                                            <th width="10%">VAT Amount</th>
                                            <th width="10%">Catering Levy Amount</th>
                                            <th width="20%">Service Tax Amount</th>
                                            <th width="20%">Net Sales Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                        $gross_sale= [];
                                        $vat= [];
                                        $catering_levy= [];
                                        $service_tax= [];
                                        $net_sales= [];

                                    ?>
                                    @if(!empty($data))
                                        <?php $b = 1;?>
                                        @foreach($data as $list)
                                            <tr>
                                                <td>{!! $list->sales_date !!}</td>
                                                <td>{!! manageAmountFormat($list->gross_sale) !!}</td>
                                                <td>{!! manageAmountFormat($list->vat) !!}</td>
                                                <td>{!! manageAmountFormat($list->catering_levy) !!}</td>
                                                <td>{!! manageAmountFormat($list->service_tax) !!}</td>
                                                <td>{!! manageAmountFormat($list->net_sales) !!}</td>
                                            </tr>
                                           <?php $b++; 
                                            $gross_sale[]= $list->gross_sale;
                                            $vat[]= $list->vat;
                                            $catering_levy[]= $list->catering_levy;
                                            $service_tax[]= $list->service_tax;
                                            $net_sales[]= $list->net_sales;

                                           ?>
                                        @endforeach
                                    @endif


                                    </tbody>

                                      <tfoot>
                                        <tr >
                                          
                                            <td></td>
                                            
                                            <td><?= manageAmountFormat(array_sum($gross_sale))?></td>
                                            <td><?= manageAmountFormat(array_sum($vat))?></td>
                                            <td><?= manageAmountFormat(array_sum($catering_levy))?></td>
                                            <td><?= manageAmountFormat(array_sum($service_tax))?></td>
                                            <td><?= manageAmountFormat(array_sum($net_sales))?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>


    </section>
     <style type="text/css">
        tfoot tr td {
            font-weight: bold;
        }
    </style>
   
@endsection
