
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                                    <div  style="height: 150px ! important;"> 
                            <div class="card-header">
                            <i class="fa fa-filter"></i> Filter
                            </div><br>
                            {!! Form::open(['route' => ['maintain-customers.allocate-receipts',$slug],'method'=>'get']) !!}

                            <div>
                            <div class="col-md-12 no-padding-h">
                           

                            <div class="col-sm-3">
                            <div class="form-group">
                               
                            {!!Form::select('allocation-type', ['only-not-allocated'=>'Only Not Allocated','include-allocated'=>'Include Allocated','allocated-only'=>'Allocated Only'], null, ['placeholder'=>'Select Allocation Type', 'class' => 'form-control'  ])!!}
                            </div>
                            </div>
                            </div>

                            <div class="col-md-12 no-padding-h">
                                 <div class="col-sm-1"><button type="submit" class="btn btn-success" name="manage-request" value="filter"  >Filter</button></div>
                                 


                                <div class="col-sm-1">
                                <a class="btn btn-info" href="{!! route('maintain-customers.allocate-receipts',$slug) !!}"  >Clear</a>
                                </div>
                                

                                
                            </div>
                            </div>

                            </form>
                        </div>
                            
                            <br>
                                <table class="table table-bordered table-hover" id="create_datatable_back">
                                    <thead>
                                    <tr>
                                        <th width="10%">S.No.</th>
                                        <th width="10%">Type</th>
                                        <th width="10%">TXN No</th>
                                        <th width="10%">Date</th>
                                        <th width="15%">Refrence</th>
                                         <th width="15%">Document No</th>
                                         <th width="10%">Settled</th>
                                        <th width="20%">Total</th>
                                       
                                        
                                    </tr>
                                    </thead>
                                    <tbody>
                                   
                                        <?php $b = 1;

                                          $total_amount = [];
                                        ?>
                                        @foreach($data as $list)
                                         
                                            <tr>
                                                <td>{!! $b !!}</td>
                                                <td>{!! isset($number_series_list[$list->type_number])?$number_series_list[$list->type_number] : '' !!}</td>
                                                <td>{!! manageOrderidWithPad($list->id) !!}</td>
                                                <td>{!! $list->trans_date !!}</td>
                                                <td>{!! $list->reference !!}</td>
                                                   <td>{!! $list->document_no !!}</td>
                                                   <td>{!! $list->is_settled=='0'?'No':'Yes' !!}</td>
                                                <td>{!! manageAmountFormat($list->amount) !!}</td>
                                               
                                            </tr>
                                            <?php $b++;
                                             
                                                $total_amount[] = $list->amount;
                                               
                                            ?>
                                        @endforeach
                                  


                                    </tbody>
                                    <tfoot>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        
                                        <td style="font-weight: bold;">Total</td>
                                        <td></td>
                                        <td style="font-weight: bold;">{{ manageAmountFormat(array_sum($total_amount))}}</td>

                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>


    </section>
   
@endsection
