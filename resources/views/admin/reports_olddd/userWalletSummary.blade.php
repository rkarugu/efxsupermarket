
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
                            {!! Form::open(['route' => 'reports.wallet-ledger-entries','method'=>'get']) !!}

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
                            {!!Form::select('phone_number', $userList, null, ['placeholder'=>'Select Phone Number', 'class' => 'form-control'  ])!!}
                            </div>
                            </div>


                            </div>

                            <div class="col-md-12 no-padding-h">
                                 <div class="col-sm-1"><button type="submit" class="btn btn-success" name="manage-request" value="filter"  >Filter</button></div>

                                     <!--div class="col-sm-1">
                                <button title="Export In Excel" type="submit" class="btn btn-warning" name="manage-request" value="xls"  ><i class="fa fa-file-excel" aria-hidden="true"></i>
                                </button>
                                </div-->

                                <!--div class="col-sm-1">
                                <button title="Export In PDF" type="submit" class="btn btn-warning" name="manage-request" value="pdf"  ><i class="fa fa-file-pdf" aria-hidden="true"></i>
                                </button>
                                </div-->
                                 <div class="col-sm-1">


                            
                                <a class="btn btn-info" href="{!! route('reports.wallet-ledger-entries').getReportDefaultFilter() !!}"  >Clear </a>
                           
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
                                        <th width="10%">Date</th>
                                        <th width="20%"  >Phone Number</th>
                                        <th width="10%"  >Entry Type</th>
                                        <th width="20%"  >Amount</th>
                                        <th width="20%"  >Narration</th>
                                        <th width="20%"  > Remark</th>
                                       
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php  $counter = 1;
                                   
                                      $total_cr = [];
                                     $total_dr = [];

                                    ?>
                                    @foreach($detail as $row)
                                    <tr>
                                        <td>{!!  $row['created_date'] !!}</td>
                                        <td>{!! $row['phone_number'] !!}</td>
                                        <td>{!! $row['entry_type'] !!}</td>
                                        <td>{!! $row['transaction_type']=='DR'?'-':''!!}{!! manageAmountFormat($row['amount']) !!}</td>
                                        <td>{!! $row['narration'] !!}</td>
                                         <td>{!! $row['remark'] !!}</td>

                                    </tr>

                                    <?php 

                                   

                                     if($row['transaction_type']=='CR')
                                     {
                                        $total_cr[] = $row['amount'];
                                     }
                                     else
                                     {
                                        $total_dr[] = $row['amount'];
                                     }
                                    ?>
                                   
                                    @endforeach

                                    
                  
                                    </tbody>
                                </table>
                            </div>


                             <div class="col-md-12">
                                <table class="table ">
                                    <thead>
                                   <tr>
                                   <th width="20%"  > </th>
                                        <th width="20%">Grand Total</th>
                                        <th width="10%">Top Up</th>
                                        <th width="10%">{!! manageAmountFormat(array_sum($total_cr)) !!}</th>
                                        <th width="20%"  >Purchase</th>
                                        <th width="20%"  >{!! manageAmountFormat(array_sum($total_dr)) !!}</th>
                                        
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