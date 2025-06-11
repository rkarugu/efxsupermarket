
@extends('layouts.admin.admin')
@section('content')
<style type="text/css">
    .onoffswitch {
    position: relative; width: 90px;
    -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
}
.onoffswitch-checkbox {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
.onoffswitch-label {
    display: block; overflow: hidden; cursor: pointer;
   /* border: 2px solid #999999; 
    border-radius: 20px;*/
}
.onoffswitch-inner {
    display: block; width: 200%; margin-left: -100%;
    transition: margin 0.3s ease-in 0s;
}
.onoffswitch-inner:before, .onoffswitch-inner:after {
    display: block; float: left; width: 50%; height: 30px; padding: 0; line-height: 30px;
    font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
    box-sizing: border-box;
}
.onoffswitch-inner:before {
    content: "ON";
    padding-left: 10px;
    background-color: #34A7C1; color: #FFFFFF;
}
.onoffswitch-inner:after {
    content: "OFF";
    padding-right: 10px;
    background-color: red; color: #fff;
    text-align: right;
}
.onoffswitch-switch {
    display: block; width: 18px; margin: 6px;
    background: #FFFFFF;
    position: absolute; top: 0; bottom: 0;
    right: 56px;
    border: 2px solid #999999; border-radius: 20px;
    transition: all 0.3s ease-in 0s; 
}
.onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner {
    margin-left: 0;
}
.onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-switch {
    right: 0px; 
}

 .onoffswitch1 {
    position: relative; width: 90px;
    -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
}
.onoffswitch1-checkbox {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
.onoffswitch1-label {
    display: block; overflow: hidden; cursor: pointer;
   /* border: 2px solid #999999; 
    border-radius: 20px;*/
}
.onoffswitch1-inner {
    display: block; width: 200%; margin-left: -100%;
    transition: margin 0.3s ease-in 0s;
}
.onoffswitch1-inner:before, .onoffswitch1-inner:after {
    display: block; float: left; width: 50%; height: 30px; padding: 0; line-height: 30px;
    font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
    box-sizing: border-box;
}
.onoffswitch1-inner:before {
    content: "ON";
    padding-left: 10px;
    background-color: #34A7C1; color: #FFFFFF;
}
.onoffswitch1-inner:after {
    content: "OFF";
    padding-right: 10px;
    background-color: red; color: #fff;
    text-align: right;
}
.onoffswitch1-switch {
    display: block; width: 18px; margin: 6px;
    background: #FFFFFF;
    position: absolute; top: 0; bottom: 0;
    right: 56px;
    border: 2px solid #999999; border-radius: 20px;
    transition: all 0.3s ease-in 0s; 
}
.onoffswitch1-checkbox:checked + .onoffswitch1-label .onoffswitch1-inner {
    margin-left: 0;
}
.onoffswitch1-checkbox:checked + .onoffswitch1-label .onoffswitch1-switch {
    right: 0px; 
}


.onoffswitch3 {
    position: relative; width: 90px;
    -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
}
.onoffswitch3-checkbox {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
.onoffswitch3-label {
    display: block; overflow: hidden; cursor: pointer;
   /* border: 2px solid #999999; 
    border-radius: 20px;*/
}
.onoffswitch3-inner {
    display: block; width: 200%; margin-left: -100%;
    transition: margin 0.3s ease-in 0s;
}
.onoffswitch3-inner:before, .onoffswitch3-inner:after {
    display: block; float: left; width: 50%; height: 30px; padding: 0; line-height: 30px;
    font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
    box-sizing: border-box;
}
.onoffswitch3-inner:before {
    content: "ON";
    padding-left: 10px;
    background-color: #34A7C1; color: #FFFFFF;
}
.onoffswitch3-inner:after {
    content: "OFF";
    padding-right: 10px;
    background-color: red; color: #fff;
    text-align: right;
}
.onoffswitch3-switch {
    display: block; width: 18px; margin: 6px;
    background: #FFFFFF;
    position: absolute; top: 0; bottom: 0;
    right: 56px;
    border: 2px solid #999999; border-radius: 20px;
    transition: all 0.3s ease-in 0s; 
}
.onoffswitch3-checkbox:checked + .onoffswitch3-label .onoffswitch3-inner {
    margin-left: 0;
}
.onoffswitch3-checkbox:checked + .onoffswitch3-label .onoffswitch3-switch {
    right: 0px; 
}

</style>
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
        <form class="validate form-horizontal"  role="form" method="POST" action="{{ route($model.'.store') }}" enctype = "multipart/form-data">
            {{ csrf_field() }}
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Non Cash Benefit</label>
                    <div class="col-sm-10">
                        {!! Form::text('non_cash_benefit', null, ['maxlength'=>'255','placeholder' => 'Non Cash Benefit', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                   <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Code</label>
                    <div class="col-sm-10">
                      {!! Form::text('code', null, ['maxlength'=>'255','placeholder' => 'Code', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Rate (%)</label>
                    <div class="col-sm-10">
                      {!! Form::text('rate', null, ['maxlength'=>'255','placeholder' => 'Rate', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Use Rate</label>
                    <div class="col-sm-10">
                    <div class="onoffswitch1">
                    <input type="checkbox" name="use_rate" class="onoffswitch1-checkbox" value="On" id="myonoffswitch1" tabindex="0" checked>
                    <label class="onoffswitch1-label" for="myonoffswitch1">
                        <span class="onoffswitch1-inner"></span>
                        <span class="onoffswitch1-switch"></span>
                    </label>
                      </div>
                </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Recurring</label>
                    <div class="col-sm-10">
                    <div class="onoffswitch">
                    <input type="checkbox" name="recurring" class="onoffswitch-checkbox" value="On" id="myonoffswitch" tabindex="0" checked>
                    <label class="onoffswitch-label" for="myonoffswitch">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                      </div>
                   </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Taxable</label>
                    <div class="col-sm-10">
                    <div class="onoffswitch3">
                    <input type="checkbox" name="tax_able" class="onoffswitch3-checkbox" value="On" id="myonoffswitch3" tabindex="0" checked>
                    <label class="onoffswitch3-label" for="myonoffswitch3">
                        <span class="onoffswitch3-inner"></span>
                        <span class="onoffswitch3-switch"></span>
                    </label>
                      </div>
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



    