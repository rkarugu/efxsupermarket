
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} - {{$row->title}} </h3></div>
         @include('message')
         {!! Form::model($row, ['method' => 'PATCH','route' => [$model.'.update.standard.cost', $row->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}
  <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Stock ID Code</label>
                    <div class="col-sm-10">
                        {!! Form::text('stock_id_code', null, ['maxlength'=>'255','placeholder' => 'Stock ID Code', 'required'=>true,'readonly'=>true, 'class'=>'form-control']) !!}  

                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Prev. Standard Cost</label>
                    <div class="col-sm-10">
                        {!! Form::number('prev_standard_cost', null, ['min'=>'0', 'required'=>true, 'readonly'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Standard Cost</label>
                    <div class="col-sm-10">
                        {!! Form::number('standard_cost', null, ['min'=>'0', 'pattern'=>"\d*", 'maxlength'=>'8', 'required'=>true, 'class'=>'form-control']) !!}  
                         {!! Form::hidden('old_standard_cost', $row->standard_cost, ['min'=>'0', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Recent Cost Updated Time</label>
                    <div class="col-sm-10">
                        {!! Form::text('cost_update_time', date('d-m-Y H:i:s',strtotime($row->cost_update_time)), ['readonly'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Block</label>
                    <div class="col-sm-10">
                        <div class="form-check form-check-inline">
                            <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" name="block_this" id="block_this" value="True" {{$row->block_this ? 'checked' : ''}}>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Selling Price</label>
                    <div class="col-sm-10">
                        {!! Form::number('selling_price', $row->selling_price, ['min'=>'0', 'pattern'=>"\d*", 'maxlength'=>'8', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
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