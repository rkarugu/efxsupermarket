
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
                            {!! Form::open(['route' => 'reports.family-group-menu-item-general-sales','method'=>'get']) !!}

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
                                <button title="Export In PDF" type="submit" class="btn btn-warning" name="manage-request" value="pdf"  ><i class="fa fa-file-pdf" aria-hidden="true"></i>
                                </button>
                                </div>
                                <div class="col-sm-1">
                                <a class="btn btn-info" href="{!! route('reports.family-group-menu-item-general-sales').getReportDefaultFilter() !!}"  >Clear</a>
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
                                        <th   >Item</th>
                                        <th   >Price</th>
                                         <th   >Sales QTY</th>
                                         <th   >% for SalesQty</th>
                                        <th   >Gross Sales</th>
                                        <th   >% for Gross Sales</th>
                                       
                                    </tr>
                                    </thead>
                                    <tbody>
	                                    <?php
                                   $final_totla_charge= [];
		                                    
	                                    ?>
                                    @foreach($detail as $key=> $rows)
                                    <?php  $counter = 1;
                                    $total_qty = [];
                                    $total_amount = [];
                                    $total_qty_per = [];
                                    $total_gross_per = [];
                                    $total_disc = [];
 
                                    ?>

                                    @foreach($rows as $row)
                                     @if($row['title']!="")
                                    <tr>
                                        <td>{!! ucfirst(strtolower($row['title'])) !!} </td>
                                        <td>{!! $row['price'] !!}</td>
                                         <td>{!! $row['item_total_quantity'] !!}</td>
                                         @if($row['item_total_quantity'] > 0)
                                         <td>{!! round(($row['item_total_quantity']/$rows['total_qty_total'])*100,2) !!}%</td>
	                                    <?php 
	                                    $total_qty_per[] = (($row['item_total_quantity']/$rows['total_qty_total'])*100);
	                                    ?>
                                         @else
                                         <td>0%</td>
                                         @endif
                                        <td>{!! manageAmountFormat($row['gross_sale']) !!}</td>
                                         @if($row['gross_sale'] > 0)
                                         <td>{!! round(($row['gross_sale']/$rows['gross_sales_total'])*100,2) !!}%</td>
	                                    <?php 
	                                    $total_gross_per[] = (($row['gross_sale']/$rows['gross_sales_total'])*100);
	                                    ?>
                                         @else
                                         <td>0%</td>
                                         @endif
                                    </tr>
                                    <?php 
                                    $total_qty[] = $row['item_total_quantity'];
                                    $total_amount[] = $row['gross_sale'];
                                    $total_disc[] = $row['total_charges'];
                                    $final_totla_charge[] = $row['gross_sale'];
                                    $counter++; ?>
                                    @endif
                                    @endforeach
                                    <thead>
                                    <tr>
                                        <th>{!! ucfirst(strtolower($key)) !!}</th>
                                        <th></th>
                                        <th>{!! array_sum($total_qty) !!}</th>
                                        <th>{!! array_sum($total_qty_per) !!}%</th>
                                        <th>{!! manageAmountFormat(array_sum($total_amount)) !!}</th>
                                        <th>{!! array_sum($total_gross_per) !!}%</th>
                                    </tr>
                                    </thead>

                                    @endforeach

                                    
                  
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>Gross Sale Total</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th>{!! manageAmountFormat(array_sum($final_totla_charge)) !!}</th>
                                        <th></th>
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

            <script>

$(document).ready(function(){
var numCols = $(".table-hover").find('tr')[0].cells.length;
var dynamic_width = 100/numCols;
$(".table").find('th').css('width',dynamic_width+'%');
});


</script>


@endsection