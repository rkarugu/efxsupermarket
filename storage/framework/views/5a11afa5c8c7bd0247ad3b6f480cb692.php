<?php $__env->startSection('content'); ?>

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
<form class="validate form-horizontal"  role="form" method="POST" action="<?php echo e(route($model.'.store')); ?>" enctype = "multipart/form-data">

<section class="content setup-content" id="step-1">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> Supplier Information 1 </h3></div>
         <?php echo $__env->make('message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo e(csrf_field()); ?>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Supplier Code</label>
                    <div class="col-sm-10">
                        <?php echo Form::text('supplier_code',  getCodeWithNumberSeries('SUPPLIER'), ['maxlength'=>'255','placeholder' => 'Supplier Code', 'required'=>true, 'class'=>'form-control','readonly'=>true]); ?>  
                    </div>
                </div>
            </div>

            
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Supplier Name</label>
                        <div class="col-sm-10">
                            <?php echo Form::text('name', null, ['maxlength'=>'255','placeholder' => 'Supplier Name', 'required'=>true, 'class'=>'form-control']); ?>  
                        </div>
                    </div>
                </div>

                 <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Procurement User</label>
                        <div class="col-sm-10">
                           
                             <select name="procument_user" id="procument_user" class="form-control mlselect">
                             <option value="" >Select User</option>
                          <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <option value="<?php echo e($user->id); ?>" <?php if(request()->user ==  $user->id): echo 'selected'; endif; ?> }}><?php echo e($user->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                     </select>
                        </div>
                    </div>
                </div>
            
            
            

           

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Address</label>
                    <div class="col-sm-10">
                      <?php echo Form::text('address', null, ['maxlength'=>'255','placeholder' => 'Address', 'required'=>false, 'class'=>'form-control google_location','id'=>'search_location']); ?>  
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Country</label>
                    <div class="col-sm-10">
                        <?php echo Form::select('country', getCountryList(),null, ['maxlength'=>'255','placeholder' => 'Please select', 'required'=>false, 'class'=>'form-control mlselect']); ?>  
                    </div>
                </div>
            </div>

                <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Telephone</label>
                    <div class="col-sm-10">
                        <?php echo Form::text('telephone', null, ['maxlength'=>'255','placeholder' => 'Telephone', 'required'=>false, 'class'=>'form-control']); ?>  
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Facsimile</label>
                    <div class="col-sm-10">
                        <?php echo Form::text('facsimile', null, ['maxlength'=>'255','placeholder' => 'Facsimile', 'required'=>false, 'class'=>'form-control']); ?>  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Email Address</label>
                    <div class="col-sm-10">
                        <?php echo Form::email('email', null, ['maxlength'=>'255','placeholder' => 'Email Address', 'required'=>false, 'class'=>'form-control']); ?>  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">URL</label>
                    <div class="col-sm-10">
                        <?php echo Form::url('url', null, ['maxlength'=>'255','placeholder' => 'URL', 'required'=>false, 'class'=>'form-control']); ?>  
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
                        <?php echo Form::select('supplier_type', ['default'=>'Default','others'=>'Others'],null, ['maxlength'=>'255','required'=>false, 'class'=>'form-control']); ?>  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Supplier Since (d/m/Y)</label>
                    <div class="col-sm-10">
                       

                         <?php echo Form::text('supplier_since', null, ['maxlength'=>'255','placeholder' => 'Date', 'required'=>false, 'class'=>'form-control datepicker','readonly'=>true]); ?> 
                    </div>
                </div>
            </div>

         
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label"> Payment Terms</label>
                    <div class="col-sm-10">
                        <?php echo Form::select('wa_payment_term_id', paymentTermsList(),null, ['maxlength'=>'255', 'required'=>true, 'class'=>'form-control']); ?>  
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label"> Supplier Currency</label>
                    <div class="col-sm-10">
                        <?php echo Form::select('wa_currency_manager_id', getAssociatedCurrenyList(),null, ['maxlength'=>'255', 'required'=>false, 'class'=>'form-control']); ?>  
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">  Remittance Advice</label>
                    <div class="col-sm-10">
                        <?php echo Form::select('remittance_advice', ['not required'=>'Not Required','required'=>'Required'],null, ['maxlength'=>'255', 'required'=>false, 'class'=>'form-control']); ?>  
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label"> Tax Group</label>
                    <div class="col-sm-10">
                        <?php echo Form::select('tax_group', ['default'=>'Default','Kenya Revenue Authority'=>'Kenya Revenue Authority'],null, ['maxlength'=>'255', 'required'=>false, 'class'=>'form-control']); ?>  
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Credit Limit</label>
                    <div class="col-sm-10">
                        <?php echo Form::number('credit_limit', null, [
                            'min' => '0',
                            'placeholder' => 'Credit Limit',
                            'required' => true,
                            'class' => 'form-control',
                            'readonly' => false,
                        ]); ?>

                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Monthly Target</label>
                    <div class="col-sm-10">
                        <?php echo Form::number('monthly_target', null, [
                            'min' => '0',
                            'placeholder' => 'Monthly Target',
                            'required' => true,
                            'class' => 'form-control',
                            'readonly' => false,
                        ]); ?>

                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Quarterly Target</label>
                    <div class="col-sm-10">
                        <?php echo Form::number('quarterly_target', null, [
                            'min' => '0',
                            'placeholder' => 'Quarterly Target',
                            'required' => true,
                            'class' => 'form-control',
                            'readonly' => false,
                        ]); ?>

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
                    <?php echo Form::select('service_type', ['goods'=>'Goods','services'=>'Services','dormant'=>'Dormant'],'goods', ['maxlength'=>'255','placeholder' => 'Select Service Type', 'required'=>true, 'class'=>'form-control']); ?>  
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Tax Withhold</label>
                <div class="col-sm-10">
                    <?php echo Form::checkbox('tax_withhold'); ?>  
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">KRA PIN</label>
                <div class="col-sm-10">
                    <?php echo Form::text('kra_pin', null, ['maxlength'=>'255','placeholder' => 'KRA PIN', 'required'=>false, 'class'=>'form-control']); ?>  
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Transport</label>
                <div class="col-sm-10">
                    <?php echo Form::select('transport', ['Own Collection'=>'Own Collection','Delivery'=>'Delivery'], 'Own Collection',['maxlength'=>'255','placeholder' => 'Select Transport', 'required'=>true, 'class'=>'form-control']); ?>  
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Bank Name</label>
                <div class="col-sm-10">
                    <?php echo Form::text('bank_name', null, ['maxlength'=>'255','placeholder' => 'Bank Name', 'required'=>false, 'class'=>'form-control']); ?>  
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Bank Branch</label>
                <div class="col-sm-10">
                    <?php echo Form::text('bank_branch', null, ['maxlength'=>'255','placeholder' => 'Bank Branch', 'required'=>false, 'class'=>'form-control']); ?>  
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Bank AC/No</label>
                <div class="col-sm-10">
                    <?php echo Form::text('bank_account_no', null, ['maxlength'=>'255','placeholder' => 'Bank AC/No', 'required'=>false, 'class'=>'form-control']); ?>  
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Bank Swift</label>
                <div class="col-sm-10">
                    <?php echo Form::text('bank_swift', null, ['maxlength'=>'255','placeholder' => 'Bank Swift', 'required'=>false, 'class'=>'form-control']); ?>  
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Bank Cheque Payee</label>
                <div class="col-sm-10">
                    <?php echo Form::text('bank_cheque_payee', null, ['maxlength'=>'255','placeholder' => 'Bank Cheque Payee', 'required'=>false, 'class'=>'form-control']); ?>  
                </div>
            </div>
        </div>
      
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Purchase order generation has been blocked</label>
                <div class="col-sm-10">
                    <?php echo Form::checkbox('purchase_order_blocked'); ?>  
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Futher payments have been blocked</label>
                <div class="col-sm-10">
                    <?php echo Form::checkbox('payments_blocked'); ?>  
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Note</label>
                <div class="col-sm-10">
                    <?php echo Form::text('blocked_note', null, ['maxlength'=>'255','placeholder' => 'Note', 'required'=>false, 'class'=>'form-control']); ?>  
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

<?php $__env->stopSection(); ?>

<?php $__env->startSection('uniquepagestyle'); ?>
 <link rel="stylesheet" href="<?php echo e(asset('css/multistep-form.css')); ?>">
 <link rel="stylesheet" href="<?php echo e(asset('assets/admin/dist/datepicker.css')); ?>">
 <link href="<?php echo e(asset('assets/admin/bower_components/select2/dist/css/select2.min.css')); ?>" rel="stylesheet" />
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('uniquepagescript'); ?>
 <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=<?php echo e($googleMapsApiKey); ?>"></script>
<script src="<?php echo e(asset('assets/admin/dist/bootstrap-datepicker.js')); ?>"></script>
<script src="<?php echo e(asset('js/sweetalert.js')); ?>"></script>
<script src="<?php echo e(asset('js/multistep-form.js')); ?>"></script>
<script src="<?php echo e(asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')); ?>"></script>
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
 

<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/maintainsuppliers/create.blade.php ENDPATH**/ ?>