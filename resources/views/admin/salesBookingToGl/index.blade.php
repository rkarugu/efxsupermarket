
@extends('layouts.admin.admin')

@section('content')

    <?php
    $total_amonts = [
        'gross_sale'=>0,
        'vat'=>0,
        'catering_levy'=>0,
        'service_tax'=>0,
        'net_sales'=>0
    ];
    ?>
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
                                        <th >Date</th>
                                        
                                        <!--th width="10%">Family Group</th-->
                                         <th width="10%">Branch</th>
                                         <th width="10%">GL Code</th>
                                        <th width="20%">GL Name</th>
                                        <th width="10%">Sales Qty</th>
                                       
                                        <th width="10%">Gross Sale</th>
                                        <th width="10%">VAT</th>
                                        <th width="10%">Catering Levy</th>
                                        <th width="10%">Service Tax</th>
                                        <th width="10%">Net Sales</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php 

                                    $gross_sale = [];
                                         $vat = [];
                                          $catering_levy = [];
                                           $service_tax = [];
                                              $net_sales = [];
                                    ?>
                                    @if(isset($data) && !empty($data))
                                        <?php $b = 1;

                                         //   echo "<pre>"; print_r($data); die;

                                        ?>
                                        @foreach($data as $list)

                                        <?php 
                                            $gross_sale[]  = $list['gross_sale'] ;
                                            $vat[]  = $list['vat'] ;
                                            $catering_levy[]  = $list['catering_levy'];
                                            $service_tax[]  = $list['service_tax'];
                                            $net_sales[]  = $list['net_sales'];
                                        ?>
                                         
                                            <tr>
                                               
                                                
                                                <td>{!! $list['sale_date'] !!}</td>
                                                <td>{!! @$list['restaurant_name'] !!}</td>
                                                 <td>{!! $list['account_code'] !!}</td>
                                                <td>{!! $list['account_name'] !!}</td>
                                                <td>{!! $list['quantity'] !!}</td>
                                               

                                                <td>

                                                {!! manageAmountFormat($list['gross_sale']) !!}
                                                    
                                                </td>
                                                <td>
                                                   {!! manageAmountFormat($list['vat']) !!}
                                                </td>
                                                <td>
                                                   {!! manageAmountFormat($list['catering_levy']) !!}
                                                </td>
                                                <td>
                                                    {!! manageAmountFormat($list['service_tax']) !!}
                                                </td>
                                                <td>
                                                   {!! manageAmountFormat($list['net_sales']) !!}
                                                </td>
                                            </tr>
                                           <?php $b++; ?>
                                        @endforeach
                                    @endif


                                    </tbody>
                                    
                                    <tfoot>
                                        <tr >
                                            <td></td>
                                          
                                            <!--td></td-->
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            
                                            <td><?= manageAmountFormat(array_sum($gross_sale))?></td>
                                            <td><?= manageAmountFormat(array_sum($vat))?></td>
                                            <td><?= manageAmountFormat(array_sum($catering_levy))?></td>
                                            <td><?= manageAmountFormat(array_sum($service_tax))?></td>
                                            <td><?= manageAmountFormat(array_sum($net_sales))?></td>
                                        </tr>
                                    </tfoot>
                                    
                                </table>
                                <div align="right">
                                <a class= "btn btn-primary" align="right" href="{{ route('sales-booking-to-gl.post-sales-to-general-ledger')}}" onclick="return shoLoader(this);">Post Sales to General Ledger</span></a>
                                
                            </div>
                        </div>
                    </div>

                    
    </section>
    <span class="btn-loader" style="display:none;">
        <img src="<?= asset('public/assets/admin/images/loader.gif') ?>" alt="Loader" />
    </span>
    <style type="text/css">
        tfoot tr td {
            font-weight: bold;
        }
    </style>

   
@endsection


@section('uniquepagescript')
<script>
function shoLoader(obj){
    if (confirm('Are You Sure?')){
        $('.btn-loader').show();
    }else{
        return false;
    }
}
</script>
@endsection