
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
                            {!! Form::open(['route' => 'reports.detailed-payment-methods-reports','method'=>'get']) !!}

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
                        {!! Form::select('restaurant',$restroList, null,['placeholder'=>"Select Branch",'class'=>'form-control']) !!}  

                            </div>
                            </div>

                            <div class="col-sm-3">
                            <div class="form-group">
                                @php
                                    $paymentmethodlist = $paymentmethods;
                                @endphp
                        {!! Form::select('payment_method',$paymentmethodlist, null,['placeholder'=>"Select Payment Method",'class'=>'form-control']) !!}  

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
                                <a class="btn btn-info" href="{!! route('reports.detailed-payment-methods-reports') !!}"  >Clear </a>
                           
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
                                        <th width="10%"  >Receipt ID</th>
                                        <th width="10%"  >Datetime</th>
                                        <th width="10%"  >Waiter Name</th>
                                        <th width="15%"  >Cashier Name</th>
                                        <th width="10%"  >Number of Orders</th>
                                       
                                        <th width="10%"  >Payment Method</th>
                                        <th width="15%"  >Amount</th>
                                        <th width="20%"  >Narration</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                    $total_amount = [];
                                    ?>
                                  
                                   @foreach($detail as $data)
                                   <?php 
                                   $rendered = [];

                                   $sub_total = [];
                                   ?>
                                   @if(isset($data['payments']))
                                    @foreach($data['payments'] as $pData)
                                        <?php 
                                        $is_render = false;
                                        if(!in_array($data['receipt_id'], $rendered))
                                        {
                                            $rendered[] = $data['receipt_id'];
                                             $is_render = true;
                                        }
                                        $sub_total[] = $pData['amount'];
                                         $total_amount[] = $pData['amount'];

                                        ?>
                                    <tr>   
                                      <td>{{ $is_render==true?$data['receipt_id']:''}}</td>
                                      <td>{{ $is_render==true?$data['created_at']:''}}</td>  
                                      <td>{{ $is_render==true?ucfirst($data['waiter_name']):''}}</td>
                                      <td>{{ $is_render==true?$data['cashier_name']:''}}</td> 
                                      <td>{{ $is_render==true?$data['no_of_orders']:''}}</td>
                                      <td>{{ $pData['payment_mode']}}</td> 
                                      <td>{{ manageAmountFormat($pData['amount'])}}</td>
                                     
                                      <td>{{ $pData['narration']}}</td>
                                    </tr>
                                     @endforeach
                                    @endif 

                                      {{-- <tr style="font-weight: bold;">   
                                      <td></td>
                                      <td></td>  
                                      <td></td>
                                      <td></td> 
                                      <td></td>
                                      <td>Sub Total</td> 
                                      <td>{{ manageAmountFormat(array_sum($sub_total))}}</td>
                                     
                                      <td></td>
                                    </tr> --}}
                                   
                                     @endforeach
                                    </tbody>



                                    <tfoot style="font-weight: bold;">
                                    <td></td>
                                      <td></td> 
                                      <td></td>
                                      <td></td> 
                                      <td></td>
                                     
                                      
                                     
                                      <td>Grand Total</td>
                                      <td>{{ manageAmountFormat(array_sum($total_amount)) }}</td>
                                       <td></td> 

                                    </tfoot>

                                </table>
                            </div>

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