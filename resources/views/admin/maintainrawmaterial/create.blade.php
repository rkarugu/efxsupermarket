
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
                        {!! Form::text('stock_id_code',null, ['maxlength'=>'255','placeholder' => 'Stock ID Code', 'required'=>true, 'class'=>'form-control']) !!}  
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
                    <label for="inputEmail3" class="col-sm-2 control-label">Inventory Category</label>
                    <div class="col-sm-10">
                        {!! Form::select('wa_inventory_category_id', getInventoryCategoryList(),null, ['maxlength'=>'255','placeholder' => 'Please select', 'required'=>true, 'class'=>'form-control mlselec6t']) !!}  
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Preferred Supplier</label>
                    <div class="col-sm-10">
                        {!!Form::select('suppliers[]',$suppliers,null, [ 'class' => 'form-control selector_selects2','required'=>true,'multiple'=>true  ])!!}  
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Standard Cost</label>
                    <div class="col-sm-10">
                        {!! Form::number('standard_cost', 0, ['min'=>'0', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Minimum Order Quantity</label>
                    <div class="col-sm-10">
                        {!! Form::number('minimum_order_quantity', 0, ['min'=>'0', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

<!--             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Selling Price Inc Vat</label>
                    <div class="col-sm-10">
                        {!! Form::number('selling_price', 0, ['min'=>'0', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>
 -->            
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Bin Location</label>
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
                    <label for="inputEmail3" class="col-sm-2 control-label">Pack Size</label>
                    <div class="col-sm-10">
                        {!! Form::select('pack_size_id',$PackSize ,null, ['maxlength'=>'255','placeholder' => 'Please select', 'required'=>false, 'class'=>'form-control mlselec6t']) !!}  
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Location and Store</label>
                    <div class="col-sm-10">
                        {!! Form::select('store_location_id',$locations ,null, ['maxlength'=>'255','placeholder' => 'Please select', 'required'=>false, 'class'=>'form-control mlselec6t']) !!}  
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Alt Code</label>
                    <div class="col-sm-10">
                        {!! Form::text('alt_code',null, ['maxlength'=>'255','placeholder' => 'Alt Code', 'required'=>false, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Packaged Volume (metres cubed)</label>
                    <div class="col-sm-10">
                        {!! Form::text('packaged_volume',null, ['maxlength'=>'255','placeholder' => 'Packaged Volume (metres cubed)', 'required'=>false, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Gross Weight (KGs)</label>
                    <div class="col-sm-10">
                        {!! Form::text('gross_weight',null, ['maxlength'=>'255','placeholder' => 'Gross Weight (KGs)', 'required'=>false, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Net Weight (KGs)</label>
                    <div class="col-sm-10">
                        {!! Form::text('net_weight',null, ['maxlength'=>'255','placeholder' => 'Net Weight (KGs))', 'required'=>false, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">HS Code</label>
                    <div class="col-sm-10">
                        {!! Form::text('hs_code',null, ['maxlength'=>'100','placeholder' => 'HS Code', 'required'=>false, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

                 {{-- <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Showroom Stock</label>
                    <div class="col-sm-10">
	                    <input type="checkbox" name="showroom_stock" />
                    </div>
                </div>
            </div> 
                 <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">New Stock</label>
                    <div class="col-sm-10">
	                    <input type="checkbox" name="new_stock"  />
                    </div>
                </div>
            </div>  --}}


              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Manufacturing Unit of Measure</label>
                    <div class="col-sm-10">
                        {!! Form::select('wa_unit_of_measure_id', getUnitOfMeasureList(),null, ['maxlength'=>'255','placeholder' => 'Please select', 'required'=>true, 'class'=>'form-control mlselec6t']) !!}  
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Conversion Rate</label>
                    <div class="col-sm-10">
                        {!! Form::number('conversion_rate', 0, ['min'=>'0', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Image</label>
                    <div class="col-sm-10">
                      {!! Form::file('image', null, ['required'=>true, 'class'=>'form-control']) !!}  
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
     $(".selector_selects2").select2();
     
});
</script>
@endsection