
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
        
           

          
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Tyre ID Code</label>
                    <div class="col-sm-10">
                        {!! Form::text('code', $row->code, ['maxlength'=>'255','placeholder' => 'Stock ID Code', 'required'=>true, 'class'=>'form-control','disabled'=>true]) !!}  
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Tyre Size</label>
                    <div class="col-sm-10">
                        {!! Form::text('tyre_size', $row->tyre_size, ['maxlength'=>'255','placeholder' => 'Item Title', 'required'=>true, 'class'=>'form-control','disabled'=>true]) !!}  
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Tyre Make</label>
                    <div class="col-sm-10">
                      {!! Form::text('tyre_make', $row->tyre_make, ['maxlength'=>'255','placeholder' => 'Description', 'required'=>true, 'class'=>'form-control','disabled'=>true]) !!}  
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Type</label>
                    <div class="col-sm-10">
                      <select class="form-control" name="type" disabled >
                          <option {{($row->type=="new")?'selected':''}} value="new">New</option>
                          <option {{($row->type=="retread")?'selected':''}} value="retread">Retread</option>
                      </select>
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Pattern</label>
                    <div class="col-sm-10">
                        {!! Form::text('pattern', $row->pattern, ['maxlength'=>'255','placeholder' => 'Item Title', 'required'=>true, 'class'=>'form-control','disabled'=>true]) !!} 
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Tyre Cost</label>
                    <div class="col-sm-10">
                        {!! Form::number('cost', $row->cost, ['min'=>'0', 'required'=>true, 'class'=>'form-control','disabled'=>true]) !!}  
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Status</label>
                    <div class="col-sm-10">
                        <select class="form-control" disabled name="status" >
                            <option {{($row->status=="waiting")?'selected':''}} value="waiting">Waiting</option>
                            <option {{($row->status=="in_transit")?'selected':''}} value="in_transit">In Transit</option>
                            <option {{($row->status=="transit_to_stock")?'selected':''}} value="transit_to_stock">Transit to Stock</option>
                            <option {{($row->status=="emergency")?'selected':''}} value="emergency">Emergency</option>
                            <option {{($row->status=="damaged")?'selected':''}} value="damaged">Damaged</option>
                            <option {{($row->status=="new_tyre_in_stock")?'selected':''}} value="new_tyre_in_stock">New Tyre in Stock</option>
                            <option {{($row->status=="new_but_used")?'selected':''}} value="new_but_used">New But Used</option>
                            <option {{($row->status=="retread_but_used")?'selected':''}} value="retread_but_used">Retread But Used</option>
                        </select> 
                    </div>
                </div>
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