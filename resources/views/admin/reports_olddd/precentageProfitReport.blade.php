
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                        <div  style="height: 150px ! important;"> 
                            <div class="card-header">
                            <i class="fa fa-filter"></i> Filter
                            </div><br>
                            {!! Form::open(['route' => 'reports.percentage-profit-report','method'=>'get']) !!}

                            <div>
                            <div class="col-md-12 no-padding-h">
                            <div class="col-sm-3">
                            <div class="form-group">
                            {!! Form::text('start-date', null, [
                            'class'=>'datepicker form-control',
                            'placeholder'=>'Start Date' ,'readonly'=>true]) !!}
                            </div>
                            </div>

                            <div class="col-sm-3">
                            <div class="form-group">
                            {!! Form::text('end-date', null, [
                            'class'=>'datepicker form-control',
                            'placeholder'=>'End Date','readonly'=>true]) !!}
                            </div>
                            </div>

                            <div class="col-sm-3">
                            <div class="form-group">
                            {!!Form::select('restaurant', $restro, null, ['placeholder'=>'Branch', 'class' => 'form-control'  ])!!}
                            </div>
                            </div>
                            </div>

                            <div class="col-md-12 no-padding-h">
                                <div class="col-sm-1"><button type="submit" class="btn btn-success" name="manage-request" value="filter"  >Filter</button></div>

                                <div class="col-sm-1">
                                <button title="Export In Excel" type="submit" class="btn btn-warning" name="manage-request" value="xls"  ><i class="fa fa-file-excel" aria-hidden="true"></i>
                                </button>
                                </div>

                                <div class="col-sm-1">
                                <button title="Export In PDF" type="submit" class="btn btn-warning" name="manage-request" value="pdf"  ><i class="fa fa-file-pdf" aria-hidden="true"></i>
                                </button>
                                </div>
                                <div class="col-sm-1">
                                <a class="btn btn-info" href="{!! route('reports.percentage-profit-report').getReportDefaultFilter() !!}"  >Clear</a>
                                </div>
                            </div>
                            </div>

                            </form>
                        </div>
                            
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th >S.No.</th>
                                        <th   >Item</th>
                                        <th  >Family Group</th>
                                        <th  >Sales Price</th>
                                        <th  >Cost</th>
                                        <th   >Sales QTY</th>
                                        <th   >Total Sales</th>
                                        <th   >Total Cost</th>
                                        <th   >Profit</th>
                                        <th   >Margin(%) </th>



                                       
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php  $counter = 1;
                                    $final_sale = [];
                                    $final_cost = [];
                                     $final_profit = [];
                                  

                                    ?>
                                    @foreach($detail as $row)
                                    <tr>

                                    <?php 
                                    $total_sale = $row['item_total_quantity']*$row['item_price'];
                                    $total_cost = $row['item_total_quantity']*$row['cost'];
                                    $profit = $total_sale-$total_cost;
                                    $profit_percentage = 0;
                                    if($total_cost>0)
                                    {
                                         $profit_percentage = ($profit/$total_cost)*100;
                                    }






                                    $final_sale[] = $total_sale;
                                      $final_cost[] = $total_cost;
                                        $final_profit[] = $profit;


                                   


                                    ?>

                                        <td>{!! $counter !!}</td>
                                        <td>{!! $row['title'] !!}</td>
                                         <td>{!! $row['family_group_name'] !!}</td>


                                          <td>{!! manageAmountFormat($row['item_price']) !!}</td>
                                          <td>{!! manageAmountFormat($row['cost']) !!}</td>
                                           <td>{!! manageAmountFormat($row['item_total_quantity']) !!}</td>
                                           <td>{!! manageAmountFormat($total_sale) !!}</td>
                                          <td>{!! manageAmountFormat($total_cost) !!}</td>
                                          <td>{!! manageAmountFormat($profit) !!}</td>
                                          <td>{!! manageAmountFormat($profit_percentage) !!}</td>






                                    </tr>
                                    <?php 
                                   
                                    $counter++; ?>
                                    @endforeach

                                    
                  
                                    </tbody>
                                    <tfoot>

                                    <?php 
                                    $fianl_margin = 0;
                                     if(array_sum($final_cost)>0)
                                    {
                                         $fianl_margin = (array_sum($final_profit)/array_sum($final_cost))*100;
                                    }
                                    ?>
                                    <tr style=" font-weight: bold;">
                                        
                                        <td></td>
                                        <td></td>
                                        <td>Total</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>{{ manageAmountFormat(array_sum($final_sale))}}</td>
                                        <td>{{ manageAmountFormat(array_sum($final_cost))}}</td>
                                        <td>{{ manageAmountFormat(array_sum($final_profit))}}</td>
                                        <td>{{ manageAmountFormat($fianl_margin)}}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                        </div>
                    </div>
    </section>


  
@endsection


@section('uniquepagestyle')
  <link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
<script>
                 

                  $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
            </script>     


@endsection