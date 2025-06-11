
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
                            {!! Form::open(['route' => 'reports.payment-sales-summary','method'=>'get']) !!}

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
                                <a class="btn btn-info" href="{!! route('reports.payment-sales-summary').getReportDefaultFilter() !!}"  >Clear</a>
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
                                        <th width="10%">S.No.</th>
                                        <th width="30%"  >Payment Name</th>
                                        <th width="30%"  >No Of Transactions</th>
                                        <th width="30%"  >Total Payments</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php  $counter = 1;
                                    $total_qty = [];
                                    $total_amount = [];

                                    ?>
                                    @foreach($detail as $row)
                                    <tr>
                                        <td>{!! $counter !!}</td>
                                        <td>{!! $row->payment_mode !!}</td>
                                        <td>{!! $row->number_of_transaction !!}</td>
                                        <td>{!! manageAmountFormat($row->amount) !!}</td>
                                    </tr>
                                    <?php 
                                    $total_qty[] = $row->number_of_transaction;
                                     $total_amount[] = $row->amount;
                                    $counter++; ?>
                                    @endforeach

                                    
                  
                                    </tbody>
                                </table>
                            </div>

                             <div class="col-md-12">
                                <table class="table ">
                                    <thead>
                                   <tr>
                                        <th width="30%"></th>
                                        <th width="10%"  >Grand Total</th>
                                        <th width="30%"  >{!! array_sum($total_qty)!!}</th>
                                        <th width="30%"  >{!! manageAmountFormat(array_sum($total_amount))!!}</th>
                                    </tr>
                                    </thead>
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