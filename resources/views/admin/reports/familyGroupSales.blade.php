
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
                            {!! Form::open(['route' => 'reports.family-group-sales','method'=>'get']) !!}

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
                                <a class="btn btn-info" href="{!! route('reports.family-group-sales').getReportDefaultFilter() !!}"  >Clear</a>
                                </div>

                                  <div class="col-sm-2">


                            
                                <!--button type="submit" class="btn btn-success" name="manage-request" value="export"  >Export</button-->
                           
                        </div>
                                 <!--div class="col-sm-2"><button class="btn btn-success">Export</button></div-->
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
                                        <th width="10%">S.No.</th>
                                        <th width="20%"  >Title</th>
                                        <th width="10%"  >Sales QTY</th>
                                        <th width="20%"  >Gross Sales</th>
                                        <th width="20%"  >Taxes</th>
                                        <th width="20%"  > Net Sales % Of Ttl</th>
                                       
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php  $counter = 1;
                                    $total_qty = [];
                                    $total_amount = [];
                                    $total_disc = [];

                                    ?>
                                    @foreach($detail as $row)
                                    <tr>
                                        <td>{!! $counter !!}</td>
                                        <td>{!! $row['title'] !!}</td>
                                        <td>{!! $row['item_total_quantity'] !!}</td>
                                        <td>{!! manageAmountFormat($row['gross_sale']) !!}</td>
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
                                        <th width="20%"></th>
                                        <th width="10%"  >Grand Total</th>
                                        <th width="10%"  >{!! array_sum($total_qty)!!}</th>
                                        <th width="20%"  >{!! manageAmountFormat(array_sum($total_amount))!!}</th>
                                        <th width="20%"  >-{!! manageAmountFormat(array_sum($total_disc))!!}</th>
                                        <th width="20%"  > {!! manageAmountFormat(array_sum($total_amount)-array_sum($total_disc))!!}</th>
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


@endsection