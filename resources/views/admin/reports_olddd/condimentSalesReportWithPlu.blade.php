
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
                            {!! Form::open(['route' => 'reports.condiment-sales-report-with-plu','method'=>'get']) !!}

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
                                <a class="btn btn-info" href="{!! route('reports.condiment-sales-report-with-plu').getReportDefaultFilter() !!}"  >Clear</a>
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
                                        <th  >Item</th>
                                        <th   >Sales QTY</th>
                                        
                                        <th   > Plu no</th>
                                        <th  > Plu Name</th>
                                       
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php  $counter = 1;
                                    $total_qty = [];
                                   

                                    ?>
                                    @foreach($detail as $row)
                                    <tr>
                                        <td>{!! $counter !!}</td>
                                        <td>{!! $row['title'] !!}</td>
                                        <td>{!! $row['item_total_quantity'] !!}</td>
                                       
                                         <td>{!! $row['plu_number'] !!}</td>
                                          <td>{!! $row['plu_name'] !!}</td>
                                         


                                    </tr>
                                    <?php 
                                    $total_qty[] = $row['item_total_quantity'];
                                    
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
                                          
                                        <th  >Grand Total</th>
                                      

                                         <th   >{!! array_sum($total_qty)!!}</th>
                                        <th  > </th>
                                        <th ></th>
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

$(document).ready(function(){
var numCols = $(".table-hover").find('tr')[0].cells.length;
var dynamic_width = 100/numCols;
$(".table").find('th').css('width',dynamic_width+'%');
});


            </script>
            


@endsection

