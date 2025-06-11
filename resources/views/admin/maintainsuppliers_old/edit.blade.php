
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
         {!! Form::model($row, ['method' => 'PATCH','route' => [$model.'.update', $row->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}
  <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Supplier Code</label>
                    <div class="col-sm-10">
                        {!! Form::text('supplier_code', null, ['maxlength'=>'255','placeholder' => 'Supplier Code', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
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

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Bank Refrence</label>
                    <div class="col-sm-10">
                        {!! Form::text('bank_reference', null, ['maxlength'=>'255','placeholder' => 'Bank Refrence', 'required'=>false, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

           

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
           
             


            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</section>
@endsection

@section('uniquepagestyle')
 <link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">
 <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
 <script src="http://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyBTAqStH1EMi9a-78_BH3ibEQOrJLwSTIo"></script>
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
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