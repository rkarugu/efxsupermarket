
@extends('layouts.admin.admin')
@section('content')

<div class=" multistep">
    <div class="container">
    <div class="stepwizard">
        <div class="stepwizard-row setup-panel">
            <div class="stepwizard-step col-xs-3"> 
                <a href="#step-1" type="button" class="btn btn-success btn-circle step-buttons step-buttons1">1</a>
                <p><b>Supplier Information 1</b></p>
            </div>
            <div class="stepwizard-step col-xs-3"> 
                <a href="#step-2" type="button" class="btn btn-default btn-circle step-buttons step-buttons2" disabled="disabled">2</a>
                <p><b>Supplier Information 2</b></p>
            </div>
            <div class="stepwizard-step col-xs-3"> 
                <a href="#step-3" type="button" class="btn btn-default btn-circle step-buttons step-buttons3" disabled="disabled">3</a>
                <p><b>Additional Information</b></p>
            </div>
            
        </div>
    </div>
    </div>
<form class="validate form-horizontal"  role="form" method="POST" action="{{ route($model.'.store') }}" enctype = "multipart/form-data">

<section class="content setup-content" id="step-1">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> Supplier Information 1 </h3></div>
         @include('message')
            {{ csrf_field() }}
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Supplier Code</label>
                    <div class="col-sm-10">
                        {!! Form::text('supplier_code',  getCodeWithNumberSeries('SUPPLIER'), ['maxlength'=>'255','placeholder' => 'Supplier Code', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>

            
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Supplier Name</label>
                        <div class="col-sm-10">
                            {!! Form::text('name', null, ['maxlength'=>'255','placeholder' => 'Supplier Name', 'required'=>true, 'class'=>'form-control']) !!}  
                        </div>
                    </div>
                </div>

                 <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Procurement User</label>
                        <div class="col-sm-10">
                           
                             <select name="procument_user" id="procument_user" class="form-control mlselect">
                             <option value="" >Select User</option>
                          @foreach ($users as $user)
                          <option value="{{ $user->id }}" @selected(request()->user ==  $user->id) }}>{{ $user->name }}</option>
                        @endforeach
                     </select>
                        </div>
                    </div>
                </div>
            
            
            

           

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Address</label>
                    <div class="col-sm-10">
                      {!! Form::text('address', null, ['maxlength'=>'255','placeholder' => 'Address', 'required'=>false, 'class'=>'form-control google_location','id'=>'search_location']) !!}  
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Country</label>
                    <div class="col-sm-10">
                        {!! Form::select('country', getCountryList(),null, ['maxlength'=>'255','placeholder' => 'Please select', 'required'=>false, 'class'=>'form-control mlselect']) !!}  
                    </div>
                </div>
            </div>

                <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Telephone</label>
                    <div class="col-sm-10">
                        {!! Form::text('telephone', null, ['maxlength'=>'255','placeholder' => 'Telephone', 'required'=>false, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Facsimile</label>
                    <div class="col-sm-10">
                        {!! Form::text('facsimile', null, ['maxlength'=>'255','placeholder' => 'Facsimile', 'required'=>false, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Email Address</label>
                    <div class="col-sm-10">
                        {!! Form::email('email', null, ['maxlength'=>'255','placeholder' => 'Email Address', 'required'=>false, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">URL</label>
                    <div class="col-sm-10">
                        {!! Form::url('url', null, ['maxlength'=>'255','placeholder' => 'URL', 'required'=>false, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary submitMe" name="current_step" style="float: right;" value="1">Next</button>
            </div>
       
    </div>
</section>
<section class="content setup-content setup-content" id="step-2">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> Supplier Information 2 </h3></div>
              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Supplier Type</label>
                    <div class="col-sm-10">
                        {!! Form::select('supplier_type', ['default'=>'Default','others'=>'Others'],null, ['maxlength'=>'255','required'=>false, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Supplier Since (d/m/Y)</label>
                    <div class="col-sm-10">
                       

                         {!! Form::text('supplier_since', null, ['maxlength'=>'255','placeholder' => 'Date', 'required'=>false, 'class'=>'form-control datepicker','readonly'=>true]) !!} 
                    </div>
                </div>
            </div>

         {{--   <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Bank Refrence</label>
                    <div class="col-sm-10">
                        {!! Form::text('bank_reference', null, ['maxlength'=>'255','placeholder' => 'Bank Refrence', 'required'=>false, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>
--}}
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label"> Payment Terms</label>
                    <div class="col-sm-10">
                        {!! Form::select('wa_payment_term_id', paymentTermsList(),null, ['maxlength'=>'255', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label"> Supplier Currency</label>
                    <div class="col-sm-10">
                        {!! Form::select('wa_currency_manager_id', getAssociatedCurrenyList(),null, ['maxlength'=>'255', 'required'=>false, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">  Remittance Advice</label>
                    <div class="col-sm-10">
                        {!! Form::select('remittance_advice', ['not required'=>'Not Required','required'=>'Required'],null, ['maxlength'=>'255', 'required'=>false, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label"> Tax Group</label>
                    <div class="col-sm-10">
                        {!! Form::select('tax_group', ['default'=>'Default','Kenya Revenue Authority'=>'Kenya Revenue Authority'],null, ['maxlength'=>'255', 'required'=>false, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Credit Limit</label>
                    <div class="col-sm-10">
                        {!! Form::number('credit_limit', null, [
                            'min' => '0',
                            'placeholder' => 'Credit Limit',
                            'required' => true,
                            'class' => 'form-control',
                            'readonly' => false,
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Monthly Target</label>
                    <div class="col-sm-10">
                        {!! Form::number('monthly_target', null, [
                            'min' => '0',
                            'placeholder' => 'Monthly Target',
                            'required' => true,
                            'class' => 'form-control',
                            'readonly' => false,
                        ]) !!}
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Quarterly Target</label>
                    <div class="col-sm-10">
                        {!! Form::number('quarterly_target', null, [
                            'min' => '0',
                            'placeholder' => 'Quarterly Target',
                            'required' => true,
                            'class' => 'form-control',
                            'readonly' => false,
                        ]) !!}
                    </div>
                </div>
            </div>


            <div class="box-footer">
            <button type="button" class="btn btn-primary" style="float: left;" onclick="$('.step-buttons1').trigger('click'); return false;">Previous</button>

                <button type="submit" class="btn btn-primary submitMe" name="current_step" style="float: right;" value="2">Next</button>
            </div>
       
    </div>
</section>
<section class="content setup-content" id="step-3">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> Additional Information </h3></div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Type of Service</label>
                <div class="col-sm-10">
                    {!! Form::select('service_type', ['goods'=>'Goods','services'=>'Services','dormant'=>'Dormant'],'goods', ['maxlength'=>'255','placeholder' => 'Select Service Type', 'required'=>true, 'class'=>'form-control']) !!}  
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Tax Withhold</label>
                <div class="col-sm-10">
                    {!! Form::checkbox('tax_withhold') !!}  
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">KRA PIN</label>
                <div class="col-sm-10">
                    {!! Form::text('kra_pin', null, ['maxlength'=>'255','placeholder' => 'KRA PIN', 'required'=>false, 'class'=>'form-control']) !!}  
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Transport</label>
                <div class="col-sm-10">
                    {!! Form::select('transport', ['Own Collection'=>'Own Collection','Delivery'=>'Delivery'], 'Own Collection',['maxlength'=>'255','placeholder' => 'Select Transport', 'required'=>true, 'class'=>'form-control']) !!}  
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Bank Name</label>
                <div class="col-sm-10">
                    {!! Form::text('bank_name', null, ['maxlength'=>'255','placeholder' => 'Bank Name', 'required'=>false, 'class'=>'form-control']) !!}  
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Bank Branch</label>
                <div class="col-sm-10">
                    {!! Form::text('bank_branch', null, ['maxlength'=>'255','placeholder' => 'Bank Branch', 'required'=>false, 'class'=>'form-control']) !!}  
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Bank AC/No</label>
                <div class="col-sm-10">
                    {!! Form::text('bank_account_no', null, ['maxlength'=>'255','placeholder' => 'Bank AC/No', 'required'=>false, 'class'=>'form-control']) !!}  
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Bank Swift</label>
                <div class="col-sm-10">
                    {!! Form::text('bank_swift', null, ['maxlength'=>'255','placeholder' => 'Bank Swift', 'required'=>false, 'class'=>'form-control']) !!}  
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Bank Cheque Payee</label>
                <div class="col-sm-10">
                    {!! Form::text('bank_cheque_payee', null, ['maxlength'=>'255','placeholder' => 'Bank Cheque Payee', 'required'=>false, 'class'=>'form-control']) !!}  
                </div>
            </div>
        </div>
      
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Purchase order generation has been blocked</label>
                <div class="col-sm-10">
                    {!! Form::checkbox('purchase_order_blocked') !!}  
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Futher payments have been blocked</label>
                <div class="col-sm-10">
                    {!! Form::checkbox('payments_blocked') !!}  
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Note</label>
                <div class="col-sm-10">
                    {!! Form::text('blocked_note', null, ['maxlength'=>'255','placeholder' => 'Note', 'required'=>false, 'class'=>'form-control']) !!}  
                </div>
            </div>
        </div>
        <div class="box-footer">
        <button type="button" class="btn btn-primary" style="float: left;" onclick="$('.step-buttons2').trigger('click'); return false;">Previous</button>

            <button type="submit" class="btn btn-primary submitMe" name="current_step" style="float: right;" value="3">Save & Finish</button>
        </div>
    </div>
</section>
</form>
</div>

@endsection

@section('uniquepagestyle')
 <link rel="stylesheet" href="{{asset('css/multistep-form.css')}}">
 <link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">
 <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
 <div id="loader-on" style="
position: fixed;
top: 0;
text-align: center;
display: block;
z-index: 999999;
width: 100%;
height: 100%;
background: #000000b8;
display:none;
" class="loder">
  <div class="loader" id="loader-1"></div>
</div>
@endsection

@section('uniquepagescript')
 <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key={{ $googleMapsApiKey }}"></script>
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/multistep-form.js')}}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script>




        $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });


         function initialize() {
    var input = document.getElementById('search_location');
    var options = {};
                 
   var autocomplete = new google.maps.places.Autocomplete(input, options);
    google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();
           /* var lat = place.geometry.location.lat();
            var lng = place.geometry.location.lng();
            $("#latitude").val(lat);
            $("#longitude").val(lng);*/
        });
}
             
google.maps.event.addDomListener(window, 'load', initialize);
    </script>

  <script type="text/javascript">
    $(function () {
   
    $(".mlselect").select2();
});
</script>
 

@endsection


