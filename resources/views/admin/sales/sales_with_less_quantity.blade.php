
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
                            {!! Form::open(['route' => 'sales.sales-with-less-quantity','method'=>'get']) !!}

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

                               

                                
                                <div class="col-sm-1">
                                <a class="btn btn-info" href="{!! route('sales.sales-with-less-quantity') !!}"  >Clear</a>
                                </div>
                            </div>
                            </div>

                            </form>
                        </div>
            <br>
            @include('message')
            <div class="col-md-12 no-padding-h">
                <table class="table table-bordered table-hover" id="create_datatable">
                    <thead>
                        <tr>
                            <th width="10%">S NO</th>
                            <th width="10%">Order NO</th>
                            <th width="10%">Date</th>
                            <th width="10%">Recipe No</th>
                            <th width="10%">Ingredient No</th>
                            <th width="10%">Ingredient Name</th>
                            <th width="10%"  >QTBD</th>
                            <th width="10%">QOH(that time)</th>
                            <th width="10%">Store Location</th>
                            <th width="10%">Branch</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $b = 1; ?>
                         @if(isset($data) && count($data)>0)
                        @foreach($data as $row)
                       
                        <tr>
                            <td>{!! $b !!}</td>
                            <td>{!! manageOrderidWithPad($row->getAssociatedOrder->id) !!}</td>
                            <td>{!! getDateTimeFormatted($row->getAssociatedOrder->created_at) !!}</td>
                            <td>{!! isset($row->getAssociatedOrderedItem->getAssociateFooditem->getAssociateRecipe->recipe_number) ? $row->getAssociatedOrderedItem->getAssociateFooditem->getAssociateRecipe->recipe_number : '' !!}</td>
                            <td>{!! $row->getAssociatedInventoryItem->stock_id_code !!}</td>
                            <td>{!! $row->getAssociatedInventoryItem->title !!}</td>
                            <td><?= $row->qoh + $row->deficient_quantity ?></td>
                            <td>{!! $row->qoh !!}</td>
                            <td>{!! isset($row->getAssociatedOrderedItem->getAssociateFooditem->getAssociateRecipe->getAssociateLocation->location_name) ? $row->getAssociatedOrderedItem->getAssociateFooditem->getAssociateRecipe->getAssociateLocation->location_name : '' !!}</td>
                            <td>{!! isset($row->getAssociatedOrderedItem->getAssociateFooditem->getAssociateRecipe->getAssociateLocation->getBranchDetail->name) ? $row->getAssociatedOrderedItem->getAssociateFooditem->getAssociateRecipe->getAssociateLocation->getBranchDetail->name : '' !!}</td>
                            
                            
                            
                        </tr>
                        <?php $b++; ?>
                        
                        @endforeach
                         @else
                        <tr>
                            <td colspan="10">No record found</td>
                        </tr>
                        @endif
                    </tbody>
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