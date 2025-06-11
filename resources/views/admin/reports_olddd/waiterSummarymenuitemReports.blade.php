
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
                            {!! Form::open(['route' => 'reports.waiter-sales-menu-item-reports','method'=>'get']) !!}

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
                            {!!Form::select('menu_item', getMenuList(), null, ['placeholder'=>'Select Menu Item', 'class' => 'form-control mlselect'  ])!!}
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
                                <a class="btn btn-info" href="{!! route('reports.waiter-sales-menu-item-reports') !!}"  >Clear </a>
                           
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
                                      
                                        <th width="60%"  >EMPLOYEE NAME</th>
                                        <th width="40%"  >Item Count</th>
                                        <th width="40%"  >TOTAL SALES</th>
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                   <?php $total_amount = []; ?>
                                   @foreach($detail as $data)
                                    <tr>     
                                      <td>{{ ucfirst($data['waiter_Name'])}}</td>                                      
                                      <td>{{ $data['item_count'] }}</td>  
                                      <td>{{ manageAmountFormat(array_sum($data['amount'])) }}</td>  
                                    </tr>
                                    <?php $total_amount[] = array_sum($data['amount']); ?>
                                     @endforeach
                                    </tbody>
                                    <tfoot style="font-weight: bold;">
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
  <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />

@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    $(function () {
    $(".mlselect").select2();
});
</script>


<script>
$('.datepicker').datepicker({
format: 'yyyy-mm-dd'
});
</script>


@endsection