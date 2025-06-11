
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
                            {!! Form::open(['route' => 'sales.sales-deductions','method'=>'get']) !!}

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
                                <a class="btn btn-info" href="{!! route('sales.sales-deductions') !!}"  >Clear</a>
                                </div>
                            </div>
                            </div>

                            </form>
                        </div>
            <br>
            @include('message')
            <div class="col-md-12 no-padding-h" style="overflow-x: scroll;">
                <table class="table" id="create_datatable">
                    <thead>
                        <tr>
                            <th width="10%">S NO</th>
                            <th width="10%">Order NO</th>
                            <th width="10%">Menu Item</th>
                            <th width="10%">Date</th>
                            <th width="10%">Recipe No</th>
                            <th width="10%">Recipe Name</th>
                            <th width="10%">Recipe Cost</th>
                            <th width="10%">Family Group</th>
                            <th width="10%">Ingredient Details</th>
                            <th width="10%">Qty Deducted</th>
                            <th width="10%">Weight</th>
                            <th width="10%">Store Location</th>
                            <th width="10%">Branch</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $b = 1; 
                        ?>
                        @if(isset($data) && count($data)>0)
                        @foreach($data as $row)
                        @if(isset($row->getAssociateFooditem->getAssociateRecipe->getAssociateIngredient))
                        @foreach($row->getAssociateFooditem->getAssociateRecipe->getAssociateIngredient  as $recipe_ingredient_key => $recipe_ingredient_row)
                        
                            <?php $item_row = isset($recipe_ingredient_row->getAssociateItemDetail) ? $recipe_ingredient_row->getAssociateItemDetail : []; 
                              $qty_deducted = getinventoryItemDeductedQuantity($row->id, $item_row->id);
                               $item_detail_str = !empty($inventory_item_detail_arr) ? '<br/>' : '';
                                    $item_detail_str .= "$item_row->title ($item_row->stock_id_code)";
								//$recipe_ingredient_row->weight
//								echo "<pre>"; print_r($recipe_ingredient_row->cost); die;
 
                            ?>
                        <tr>
                           <td>{{ $b }}</td>
                             <td>{!! manageOrderidWithPad($row->getrelatedOrderForItem->id) !!}</td>
                            <td>
                                {!! isset($row->getAssociateFooditem->name) ? $row->getAssociateFooditem->name : '' !!}
                            </td>
                            <td>{!! getDateTimeFormatted($row->getrelatedOrderForItem->created_at) !!}</td>
                            <td>
                                {!! isset($row->getAssociateFooditem->getAssociateRecipe->recipe_number) ? $row->getAssociateFooditem->getAssociateRecipe->recipe_number : '' !!}
                            </td>
                            <td>{!! $row->getAssociateFooditem->getAssociateRecipe->title !!}</td>
                            <td>{{$recipe_ingredient_row->cost}}</td>
                            <td>{!! $row->getAssociateFooditem->getItemCategoryRelation->getRelativecategoryDetail->name !!}</td>
                            <td>{!! $item_detail_str !!}</td>
                            <td>{!! $qty_deducted !!}</td>
                            <td>{!! $recipe_ingredient_row->weight * $row->item_quantity !!}</td>
                            <td>{!! isset($row->getAssociateFooditem->getAssociateRecipe->getAssociateLocation->location_name) ? $row->getAssociateFooditem->getAssociateRecipe->getAssociateLocation->location_name : '' !!}</td>
                            <td>{!! isset($row->getAssociateFooditem->getAssociateRecipe->getAssociateLocation->getBranchDetail->name) ? $row->getAssociateFooditem->getAssociateRecipe->getAssociateLocation->getBranchDetail->name : '' !!}</td>
                            
                            
                            
                        </tr>

                        <?php $b++; ?>
                        @endforeach
                        @endif
                        @endforeach
                        @else
                        <tr>
                            <td colspan="7">No record found</td>
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