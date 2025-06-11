
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
                            {!! Form::open(['route' => 'reports.get-cashier-reports','method'=>'get']) !!}

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
                            {!!Form::select('user_id', $cashierList, null, ['placeholder'=>'Cashier Name', 'class' => 'form-control'  ])!!}
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
                                <a class="btn btn-info" href="{!! route('reports.get-cashier-reports') !!}"  >Clear </a>
                           
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
                                        <th width="10%">S.No.</th>
                                        <th width="45%"  >Payment Name</th>
                                        <th width="45%"  >Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                   <?php 
                                   $counter = 1;
                                   $total_amount = [];
                                   ?>
                                   @foreach($detail as $data)
                                    <tr>
                                        <td>{!! $counter !!}</td>
                                        <td>{!! $data['payment_mode'] !!}</td>
                                        <td>{!! manageAmountFormat($data['amount']) !!}</td>
                                    </tr>
                                    <?php 

                                    $total_amount[] = $data['amount'];
                                    $counter++; ?>
                                    @endforeach
                                   

                                    
                  
                                    </tbody>
                                </table>
                            </div>

                             <div class="col-md-12">
                                <table class="table ">
                                    <thead>
                                   <tr>
                                        <th width="10%"></th>
                                        <th width="45%"  >Grand Total</th>
                                        <th width="45%"  >{!! manageAmountFormat(array_sum($total_amount)) !!}</th>  
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