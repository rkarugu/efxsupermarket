
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
                            {!! Form::open(['route' => 'reports.menu-item-general-sales','method'=>'get']) !!}

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
                                <a class="btn btn-info" href="{!! route('reports.menu-item-general-sales').getReportDefaultFilter() !!}"  >Clear</a>
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
                                        <th>Recipe</th>
                                         <th  >Family Group</th>
                                        <th   >Sales QTY</th>
                                        <th   >Gross Sales</th>
                                        @foreach($charges_names as $charges_name_detail)
                                         <th   >{!! str_replace('_',' ',strtoupper($charges_name_detail))!!}</th>
                                        @endforeach
                                        <th   >Taxes</th>
                                        <th   > Net Sales % Of Ttl</th>
                                       
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php  $counter = 1;
                                    $total_qty = [];
                                    $total_amount = [];
                                    $total_disc = [];
                                    $final_totla_charge= [];

                                    ?>
                                    @foreach($detail as $row)
                                    <tr>
                                        <td>{!! $counter !!}</td>
                                        <td>{!! $row['title'] !!}</td>
                                        <td>{!! $row['recipe_name'] !!}</td>
                                         <td>{!! $row['family_group_name'] !!}</td>
                                        <td>{!! $row['item_total_quantity'] !!}</td>
                                        <td>{!! manageAmountFormat($row['gross_sale']) !!}</td>

                                         @foreach($charges_names as $charges_name_detail)
                                         <td>
                                         <?php 
                                            if(isset($row[$charges_name_detail]))
                                            {
                                                echo  manageAmountFormat($row[$charges_name_detail]);
                                                $final_totla_charge[$charges_name_detail][] = $row[$charges_name_detail]; 
                                            }
                                            else
                                                {
                                                    echo '0.00';
                                                }
                                         ?>
                                             

                                         </td>

                                        @endforeach



                                        <td>-{!! manageAmountFormat($row['total_charges']) !!}</td>
                                         <td>{!! manageAmountFormat($row['gross_sale']-$row['total_charges']) !!}</td>

                                    </tr>
                                    <?php 
                                    $total_qty[] = $row['item_total_quantity'];
                                    $total_amount[] = $row['gross_sale'];
                                    $total_disc[] = $row['total_charges'];
                                    $counter++; ?>
                                    @endforeach

                                    
                  
                                    </tbody>
                                </table>
                            </div>

                             <div class="col-md-12">
                                <table class="table ">
                                    <thead>
                                   <tr>
                                        <th ></th>
                                        <th ></th>
                                        <th >Grand Total</th>
                                        <th   >{!! array_sum($total_qty)!!}</th>
                                        <th   >{!! manageAmountFormat(array_sum($total_amount))!!}</th>

                                         @foreach($charges_names as $charges_name_detail)
                                         <th>
                                            <?php 

                                            if(isset($final_totla_charge[$charges_name_detail]))
                                            {
                                                echo manageAmountFormat(array_sum($final_totla_charge[$charges_name_detail]));
                                            }
                                            else
                                            {
                                                echo '0.00';
                                            }
                                            ?>
                                         </th>
                                          @endforeach


                                        <th   >-{!! manageAmountFormat(array_sum($total_disc))!!}</th>
                                        <th   > {!! manageAmountFormat(array_sum($total_amount)-array_sum($total_disc))!!}</th>
                                    </tr>
                                    </thead>
                                    </table>
                                    </div>
                        </div>
                    </div>
    </section>


  
@endsection


@section('uniquepagestyle')
 <link rel="stylesheet" href="{{asset('assets/admin/dist/bootstrap-datetimepicker.min.css')}}">
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/dist/bootstrap-datetimepicker.js')}}"></script>
<script>
               

                  $('.datepicker').datetimepicker({
                   format: 'yyyy-mm-dd hh:ii:00',
                  minuteStep:1,
                 });




            </script>

            <script>

$(document).ready(function(){
var numCols = $(".table-hover").find('tr')[0].cells.length;
var dynamic_width = 100/numCols;
$(".table").find('th').css('width',dynamic_width+'%');
});


</script>


@endsection