
@extends('layouts.admin.admin')
@section('content')
 <form method="POST" action="{{ route($model.'.store') }}" accept-charset="UTF-8" class="validate" enctype="multipart/form-data" novalidate="novalidate" id="resolve-form">

<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
     
            {{ csrf_field() }}
             <?php 
                    $purchase_no = getCodeWithNumberSeries('PURCHASE ORDERS');
                    $purchase_date = date('Y-m-d');
              ?>

            <div class = "row">

              <div class = "col-sm-6">
                 <div class = "row">
                    <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Purchase Order No.</label>
                    <div class="col-sm-7">

                   
                        {!! Form::text('purchase_no',  $purchase_no , ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>
                 </div>

                   <div class = "row">
                     <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Employee name</label>
                    <div class="col-sm-7">
                      

                          {!!Form::select('user_id',getAllReusitionUsers(),null, ['class' => 'form-control  mlselec6t','required'=>true,'id'=>'user_id','placeholder'=>'Please select'  ])!!} 
                    </div>
                </div>
            </div>

                   </div>
                    <div class = "row">
                     <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Purchase Date</label>
                    <div class="col-sm-7">
                        {!! Form::text('purchase_date', $purchase_date, ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>
            </div>



              </div>
              <div class = "col-sm-6">
                   <div class = "row">
                     <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Branch</label>
                    <div class="col-sm-6">
                         

                         {!! Form::text('restaurant_name', null, ['maxlength'=>'255','placeholder' => '', 'required'=>false, 'class'=>'form-control','readonly'=>true,'id'=>'restaurant_name']) !!}  

                          {!! Form::hidden('restaurant_id', null, ['id'=>'restaurant_id']) !!}

                    </div>
                </div>
            </div>
                   </div>

                     <div class = "row">

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Department</label>
                    <div class="col-sm-6">
                        

                          {!! Form::text('department_name', null, ['maxlength'=>'255','placeholder' => '', 'required'=>false, 'class'=>'form-control','readonly'=>true,'id'=>'department_name']) !!}  

                          {!! Form::hidden('wa_department_id', null, ['id'=>'wa_department_id']) !!}
                    </div>
                </div>
            </div>
                     </div>

                      <div class = "row">

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Supplier Name</label>
                    <div class="col-sm-6">
                         {!!Form::select('wa_supplier_id',getSupplierDropdown(),null, ['class' => 'form-control  mlselec6t','id'=>'wa_supplier_id','placeholder'=>'Please select'  ])!!} 
                    </div>
                </div>
            </div>
                     </div>

                      <div class = "row">

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Store Location</label>
                    <div class="col-sm-6">
                         {!!Form::select('wa_location_and_store_id',getStoreLocationDropdown(), null, ['class' => 'form-control mlselec6t','id'=>'wa_location_and_store_id','placeholder'=>'Please select'  ])!!} 
                    </div>
                </div>
            </div>
                     </div>

              </div>
            </div>
    </div>
</section>

       <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                             
                          
                            <div class="col-md-12 no-padding-h table-responsive">
                           <h3 class="box-title"> Purchase Order Line</h3>

                            <span id = "requisitionitemtable">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                      <th>S.No.</th>
                                      <th>Item Category</th>
                                      <th>Item No</th>
                                      <th>Description</th>
                                       <th>Supplier UOM</th>
                                        <th>Supplier QTY</th>



                                      <th>System UOM</th>
                                       <th>unit Conversion</th>


                                      <th>System Qty</th>
                                      <th> Price</th>
                                      <th>Total Price</th>
                                      <th>VAT Rate</th>
                                      <th> VAT Amount</th>
                                      <th>Total Cost In VAT</th>
                                      <th>Note</th>
                                      <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                       <tr>
                                      <td colspan="16">Do not have any item in list.</td>
                                      
                                    </tr>
                        
                                   


                                    </tbody>
                                </table>
                                </span>
                            </div>
                       


                              <div class="col-md-12">
                              <div class="col-md-6"><span>
                             

                              <button type="submit" class="btn btn-success btn-lg" id="addItemForm" >Resolve Requistions</button>
                              </span></div>
                              <div class="col-md-3"></div>
                              <div class="col-md-3"></div>
                              </div>


                               
                        </div>
                    </div>


    </section>

   </form>

@endsection

@section('uniquepagestyle')

<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
 <link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">

 <style type="text/css">
   .select2{
    width: 100% !important;
   }
   #note{
    height: 60px !important;
   }
   .align_float_right
{
  text-align:  right;
}
 </style>
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
  <script type="text/javascript">
  $(document).ready(function(){
    var wa_supplier_id = $("#wa_supplier_id").val();
    var wa_location_and_store_id = $("#wa_location_and_store_id").val();
    if(wa_supplier_id && wa_location_and_store_id)
    {
      $("#addItemForm").css('display','');
    }
  });
    $(function () {
      $(".mlselec6t").select2();
  });

    $('#resolve-form').submit(function(){
        valid = $(this).valid();
        if(valid){
          $('#addItemForm').attr('disabled', true);
        }
    });



    $("#user_id").change(function(){
      var user_id = $("#user_id").val();
      $("#restaurant_name").val('');
      $("#department_name").val('');
      if(user_id && user_id != '')
      {
                 jQuery.ajax({
                url: '{{route('resolve-requisition-to-lpo.get.userDetail')}}',
                type: 'POST',
                data:{user_id:user_id},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) 
                {

                  var obj = jQuery.parseJSON(response);

                  $("#restaurant_name").val(obj.restaurant_name);
                  $("#restaurant_id").val(obj.restaurant_id);
                  $("#department_name").val(obj.department_name);
                  $("#wa_department_id").val(obj.wa_department_id);
                  /*  $("#item_no").val(obj.stock_id_code);
                     $("#unit_of_measure").val(obj.unit_of_measure).change();
                     $("#standard_cost").val(obj.standard_cost);
                      $("#prev_standard_cost").val(obj.prev_standard_cost);
                      */

                     

                     

                  
                }
            });
      }

     
    });

</script>
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script>
        $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
    </script>
@endsection


