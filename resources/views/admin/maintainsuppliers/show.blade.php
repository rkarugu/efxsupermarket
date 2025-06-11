
@extends('layouts.admin.admin')
@section('content')

<div class=" multistep">
    <div class="container">
    <div class="stepwizard">
        <div class="stepwizard-row setup-panel">
            <div class="stepwizard-step col-xs-3"> 
                <a href="#step-1" type="button" class="btn btn-success btn-circle step-buttons1">1</a>
                <p><small>Supplier Information 1</small></p>
            </div>
            <div class="stepwizard-step col-xs-3"> 
                <a href="#step-2" type="button" class="btn btn-default btn-circle step-buttons2">2</a>
                <p><small>Supplier Information 2</small></p>
            </div>
            <div class="stepwizard-step col-xs-3"> 
                <a href="#step-3" type="button" class="btn btn-default btn-circle step-buttons3">3</a>
                <p><small>Additional Information</small></p>
            </div>
            
        </div>
    </div>
    </div>

    <form class="validate form-horizontal"  role="form" method="post" action="{{ route('maintain-suppliers.updateunverified')}}" enctype = "multipart/form-data">
@csrf
<section class="content setup-content" id="step-1">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> Supplier Information 1 </h3></div>
         @include('message')
            
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Supplier Code</label>
                    <div class="col-sm-10">
                        {!! Form::text('supplier_code',  $row->supplier_code, ['maxlength'=>'255','placeholder' => 'Supplier Code', 'required'=>true, 'class'=>'form-control', 'readonly'=>true]) !!}  
                    </div>
                </div>
            </div>

            
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Supplier Name</label>
                        <div class="col-sm-10">
                            {!! Form::text('name', $row->name, ['maxlength'=>'255','placeholder' => 'Supplier Name', 'required'=>true, 'class'=>'form-control']) !!}  
                        </div>
                    </div>
                </div>
            
            

           

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Address</label>
                    <div class="col-sm-10">
                      {!! Form::text('address', $row->address, ['maxlength'=>'255','placeholder' => 'Address', 'required'=>false, 'class'=>'form-control google_location','id'=>'search_location']) !!}  
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Country</label>
                    <div class="col-sm-10">
                        {!! Form::select('country', getCountryList(),$row->country, ['maxlength'=>'255','placeholder' => 'Please select', 'required'=>false, 'class'=>'form-control mlselect']) !!}  
                    </div>
                </div>
            </div>

                <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Telephone</label>
                    <div class="col-sm-10">
                        {!! Form::text('telephone', $row->telephone, ['maxlength'=>'255','placeholder' => 'Telephone', 'required'=>false, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Facsimile</label>
                    <div class="col-sm-10">
                        {!! Form::text('facsimile', $row->facsimile, ['maxlength'=>'255','placeholder' => 'Facsimile', 'required'=>false, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Email Address</label>
                    <div class="col-sm-10">
                        {!! Form::email('email', $row->email, ['maxlength'=>'255','placeholder' => 'Email Address', 'required'=>false, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">URL</label>
                    <div class="col-sm-10">
                        {!! Form::url('url', $row->url, ['maxlength'=>'255','placeholder' => 'URL', 'required'=>false, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary " name="current_step" value="1"  onclick="$('.step-buttons2').trigger('click'); return false;">Next</button>
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
                        {!! Form::select('supplier_type', ['default'=>'Default','others'=>'Others'],$row->supplier_type, ['maxlength'=>'255','required'=>false, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Supplier Since (d/m/Y)</label>
                    <div class="col-sm-10">
                       

                         {!! Form::text('supplier_since', $row->supplier_since, ['maxlength'=>'255','placeholder' => 'Date', 'required'=>false, 'class'=>'form-control datepicker','readonly'=>true]) !!} 
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Bank Refrence</label>
                    <div class="col-sm-10">
                        {!! Form::text('bank_reference', $row->bank_reference, ['maxlength'=>'255','placeholder' => 'Bank Refrence', 'required'=>false, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label"> Payment Terms</label>
                    <div class="col-sm-10">
                        {!! Form::select('wa_payment_term_id', paymentTermsList(),$row->wa_payment_term_id, ['maxlength'=>'255', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label"> Supplier Currency</label>
                    <div class="col-sm-10">
                        {!! Form::select('wa_currency_manager_id', getAssociatedCurrenyList(),$row->wa_currency_manager_id, ['maxlength'=>'255', 'required'=>false, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">  Remittance Advice</label>
                    <div class="col-sm-10">
                        {!! Form::select('remittance_advice', ['not required'=>'Not Required','required'=>'Required'],$row->remittance_advice, ['maxlength'=>'255', 'required'=>false, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label"> Tax Group</label>
                    <div class="col-sm-10">
                        {!! Form::select('tax_group', ['default'=>'Default','Kenya Revenue Authority'=>'Kenya Revenue Authority'],$row->tax_group, ['maxlength'=>'255', 'required'=>false, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>


            <div class="box-footer">
            <button type="submit" class="btn btn-primary " name="current_step" value="1"  onclick="$('.step-buttons1').trigger('click'); return false;">Previous</button>

                <button type="submit" class="btn btn-primary " name="current_step" value="2" onclick="$('.step-buttons3').trigger('click'); return false;">Next</button>
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
                    {!! Form::select('service_type', ['goods'=>'Goods','services'=>'Services'],$row->service_type, ['maxlength'=>'255','placeholder' => 'Service Type', 'required'=>true, 'class'=>'form-control']) !!}  
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Tax Withhold</label>
                <div class="col-sm-10">
                    {{$row->tax_withhold ? 'Yes' : 'No'}} 
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Transport</label>
                <div class="col-sm-10">
                    {!! Form::select('transport', ['Own Collection'=>'Own Collection','Delivery'=>'Delivery'], $row->transport,['maxlength'=>'255','placeholder' => 'transport', 'required'=>true, 'class'=>'form-control']) !!}  
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Bank Name</label>
                <div class="col-sm-10">
                    {!! Form::text('bank_name', $row->bank_name, ['maxlength'=>'255','placeholder' => 'Bank Name', 'required'=>false, 'class'=>'form-control','readonly'=>true]) !!}  
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Bank Branch</label>
                <div class="col-sm-10">
                    {!! Form::text('bank_branch', $row->bank_branch, ['maxlength'=>'255','placeholder' => 'Bank Branch', 'required'=>false, 'class'=>'form-control']) !!}  
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Bank AC/No</label>
                <div class="col-sm-10">
                    {!! Form::text('bank_account_no', $row->bank_account_no, ['maxlength'=>'255','placeholder' => 'Bank AC/No', 'required'=>false, 'class'=>'form-control']) !!}  
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Bank Swift</label>
                <div class="col-sm-10">
                    {!! Form::text('bank_swift', $row->bank_swift, ['maxlength'=>'255','placeholder' => 'Bank Swift', 'required'=>false, 'class'=>'form-control']) !!}  
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Bank Cheque Payee</label>
                <div class="col-sm-10">
                    {!! Form::text('bank_cheque_payee', $row->bank_cheque_payee, ['maxlength'=>'255','placeholder' => 'Bank Cheque Payee', 'required'=>false, 'class'=>'form-control']) !!}  
                </div>
            </div>
        </div>
      
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Purchase order generation has been blocked</label>
                <div class="col-sm-10">
                    {{$row->purchase_order_blocked ? 'Yes' : 'No'}} 

                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Futher payments have been blocked</label>
                <div class="col-sm-10">
                    {{$row->payments_blocked ? 'Yes' : 'No'}} 

                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Note</label>
                <div class="col-sm-10">
                    {!! Form::text('blocked_note', $row->blocked_note, ['maxlength'=>'255','placeholder' => 'Note', 'required'=>false, 'class'=>'form-control','readonly'=>true]) !!}  
                </div>
            </div>
        </div> 
         <div class="box-footer">

                <button type="submit" class="btn btn-primary " name="current_step" value="2" onclick="$('.step-buttons2').trigger('click'); return false;">Previous</button>
           
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
 <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyBTAqStH1EMi9a-78_BH3ibEQOrJLwSTIo"></script>
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/multistep-form.js')}}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script>

</script>
 

@endsection

