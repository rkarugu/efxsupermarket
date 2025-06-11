
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
        <form class="validate form-horizontal"  role="form" method="POST" action="{{ route($model.'.store') }}" enctype = "multipart/form-data">
            {{ csrf_field() }}
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Stock ID Code</label>
                    <div class="col-sm-10">
                        {!! Form::text('stock_id_code',getCodeWithNumberSeries('TYRE INVENTORY ITEM'), ['maxlength'=>'255','placeholder' => 'Tyre ID Code', 'required'=>true, 'class'=>'form-control','readony'=>true]) !!}  
                    </div>
                </div>
            </div>


            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Item Title</label>
                    <div class="col-sm-10">
                        {!! Form::text('title', null, ['maxlength'=>'255','placeholder' => 'Item Title', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>


             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Description</label>
                    <div class="col-sm-10">
                      {!! Form::text('description', null, ['maxlength'=>'255','placeholder' => 'Description', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Tyre Size</label>
                    <div class="col-sm-10">
                        {!! Form::text('tyre_size', null, ['maxlength'=>'255','placeholder' => 'Item Title', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>


            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Tyre Make</label>
                    <div class="col-sm-10">
                      {!! Form::text('tyre_make', null, ['maxlength'=>'255','placeholder' => 'Description', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Type</label>
                    <div class="col-sm-10">
                      <select class="form-control" name="inventory_item_type" >
                          <option value="new">New</option>
                          <option value="retread">Retread</option>
                      </select>
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Inventory Category</label>
                    <div class="col-sm-10">
                        {!! Form::select('wa_inventory_category_id', getInventoryCategoryList(),null, ['maxlength'=>'255','placeholder' => 'Please select', 'required'=>true, 'class'=>'form-control mlselec6t']) !!}  
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Pattern</label>
                    <div class="col-sm-10">
                        {!! Form::text('pattern', null, ['maxlength'=>'255','placeholder' => 'Item Title', 'required'=>true, 'class'=>'form-control']) !!} 
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Tyre Cost</label>
                    <div class="col-sm-10">
                        {!! Form::number('standard_cost', 0, ['min'=>'0', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

            {{--<div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Status</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="status" >
                            <option value="waiting">Waiting</option>
                            <option value="in_transit">In Transit</option>
                            <option value="transit_to_stock">Transit to Stock</option>
                            <option value="emergency">Emergency</option>
                            <option value="damaged">Damaged</option>
                            <option value="new_tyre_in_stock">New Tyre in Stock</option>
                            <option value="new_but_used">New But Used</option>
                            <option value="retread_but_used">Retread But Used</option>
                        </select> 
                    </div>
                </div>
            </div>--}}

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Unit Of Measure</label>
                    <div class="col-sm-10">
                        {!! Form::select('wa_unit_of_measure_id', getUnitOfMeasureList(),null, ['maxlength'=>'255','placeholder' => 'Please select', 'required'=>true, 'class'=>'form-control mlselec6t']) !!}  
                    </div>
                </div>
            </div> 
          
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Tax Category</label>
                    <div class="col-sm-10">
                        {!! Form::select('tax_manager_id',$all_taxes ,null, ['maxlength'=>'255','placeholder' => 'Please select', 'required'=>false, 'class'=>'form-control mlselec6t']) !!}  
                    </div>
                </div>
            </div>
            
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Serialised</label>
                    <div class="col-sm-10">
                        {!! Form::select('serialised',['Yes'=>'Yes','No'=>"No"] ,null, ['maxlength'=>'255','placeholder' => 'Please select', 'required'=>true, 'class'=>'form-control mlselec6t']) !!}  
                    </div>
                </div>
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</section>
@endsection

@section('uniquepagestyle')

<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
  <script type="text/javascript">
    $(function () {
      $(".mlselec6t").select2();
     
     
});
</script>
@endsection