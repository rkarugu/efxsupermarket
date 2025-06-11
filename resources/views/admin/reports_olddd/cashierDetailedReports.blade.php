
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
                            {!! Form::open(['route' => 'reports.get-cashier-detailed-reports','method'=>'get']) !!}

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

                            

                           


                            </div>

                            <div class="col-md-12 no-padding-h">
                                 <div class="col-sm-1"><button type="submit" class="btn btn-success" name="manage-request" value="filter"  >Filter</button></div>

                                     <div class="col-sm-1">
                                <button title="Export In Excel" type="submit" class="btn btn-warning" name="manage-request" value="xls"  ><i class="fa fa-file-excel" aria-hidden="true"></i>
                                </button>
                                </div>

                                <!--div class="col-sm-1">
                                <button title="Export In PDF" type="submit" class="btn btn-warning" name="manage-request" value="pdf"  ><i class="fa fa-file-pdf" aria-hidden="true"></i>
                                </button>
                                </div-->
                                 <div class="col-sm-1">
                                <a class="btn btn-info" href="{!! route('reports.get-cashier-detailed-reports') !!}"  >Clear </a>
                           
                        </div>
                             <div class="col-sm-2">
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
                                      
                                        <th width="25%"  >Cashier</th>
                                        <th width="25%"  >Waiter Name</th>
                                         <th width="25%"  >Pay Method</th>
                                          <th width="25%"  >Sales Receipt</th>
                                        
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                   <?php 
                                  
                                   $total_amount = [];


                                   ?>
                                   @foreach($detail as $data)
                                   
                                    <tr style="font-weight: bold;">
                                        <td colspan="4" style="padding-left: 50px;">{{ $data['cashier_name']}}</td>
                                    </tr>

                                    @foreach($data['payments'] as $payment)

                                    <tr>
                                               <td></td>
                                           <td>{{ ucfirst($payment['waiter_name'])}}</td>
                                          <td>{{ $payment['payment_mode']}}</td>
                                             <td>{{ manageAmountFormat($payment['amount']) }}</td>
                                    </tr>

                                    <?php 
                                    $total_amount[] = $payment['amount'];

                                    ?>


                                    @endforeach






                                   
                                     @endforeach
                                   
                                   
                                   

                                    
                  
                                    </tbody>

                                    <tfoot style="font-weight: bold;">
                                       
                                          <td></td>
                                           <td></td>
                                            <td>Grand Total</td>
                                             <td>{{ manageAmountFormat(array_sum($total_amount)) }}</td>

                                    </tfoot>

                                </table>
                            </div>

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