  <script src="{{asset('assets/admin/jquery.validate.min.js')}}"></script>    
<div class="modal-header">
    <button type="button" class="close" 
       data-dismiss="modal">
           <span aria-hidden="true">&times;</span>
           <span class="sr-only">Close</span>
    </button>
    <h4 class="modal-title" id="myModalLabel">
       Edit Item To Purchase Order
    </h4>
</div>
<div class="modal-body">
  <?php 
                    $purchase_no = @$row->purchase_no;
                    $default_branch_id = @$row->restaurant_id;
                    $default_department_id = @$row->wa_department_id;
                    $purchase_date = @$row->purchase_date;


                    ?>
 
                {!! Form::model(@$row, ['method' => 'POST','route' => [$model.'.updatePurchaseItem', @$row->getRelatedItem->find($id)->id],'class'=>'validate2','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}

            
               {!! Form::hidden('purchase_no', $purchase_no, []) !!}  
                {!! Form::hidden('purchase_date', $purchase_date, []) !!} 

               
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Item Category</label>
                    <div class="col-sm-9">
                        {!! Form::select('wa_inventory_category_id', getInventoryCategoryList(),@$row->getRelatedItem->find($id)->getInventoryItemDetail->wa_inventory_category_id, ['maxlength'=>'255','placeholder' => 'Please select category', 'required'=>true, 'class'=>'form-control mlselec6t','id'=>'inventory_category']) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Item Name</label>
                    <div class="col-sm-9">
                        {!! Form::select('wa_inventory_item_id', [],@$row->getRelatedItem->find($id)->wa_inventory_item_id, ['maxlength'=>'255','placeholder' => 'Please select item', 'required'=>true, 'class'=>'form-control mlselec6t','id'=>'item']) !!}  
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Item No</label>
                    <div class="col-sm-9">
                       
                        {!! Form::text('item_no', @$row->getRelatedItem->find($id)->item_no, ['maxlength'=>'255', 'class'=>'form-control','id'=>'item_no','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>

                <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Standard Cost</label>
                    <div class="col-sm-3">
                       
                        {!! Form::text('standard_cost', @$row->getRelatedItem->find($id)->standard_cost, ['maxlength'=>'255', 'class'=>'form-control','id'=>'standard_cost','readonly'=>true]) !!}  
                    </div>

                     <label for="inputEmail3" class="col-sm-3 control-label">Last Purchase Price</label>
                    <div class="col-sm-3">
                       
                         {!! Form::text('prev_standard_cost', @$row->getRelatedItem->find($id)->prev_standard_cost, ['maxlength'=>'255', 'class'=>'form-control','id'=>'prev_standard_cost','readonly'=>true]) !!}   
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">System UOM</label>
                    <div class="col-sm-3">
                      


                         {!! Form::select('unit_of_measure', getUnitOfMeasureList(),@$row->getRelatedItem->find($id)->unit_of_measure, ['maxlength'=>'255','placeholder' => '',  'class'=>'form-control ','id'=>'unit_of_measure','disabled'=>true]) !!}  
                    </div>

                    <label for="inputEmail3" class="col-sm-3 control-label">System Quantity</label>
                    <div class="col-sm-3">
                      


                        {!! Form::number('quantity', @$row->getRelatedItem->find($id)->quantity, ['min'=>'1', 'required'=>true, 'class'=>'form-control','id'=>'quantity','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">VAT Category</label>
                    <div class="col-sm-9">
                       
                    {!! Form::text('tax_value',  @$row->getRelatedItem->find($id)->vat_rate, ['maxlength'=>'255', 'class'=>'form-control','id'=>'tax_value','readonly'=>true]) !!}   
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Order Price</label>
                    <div class="col-sm-4">
                        {!! Form::number('order_price',  @$row->getRelatedItem->find($id)->order_price, ['min'=>'0', 'required'=>true, 'class'=>'form-control','id'=>'order_price']) !!}  
                    </div>
                    
                    <div class="col-sm-4">
                        <a href="javascript:void(0);" class="btn btn-primary" id ="view_last_price_button">View last Purchases Price</a>
                    </div>
                </div>
            </div>
            
            <div class="box-body" id="price-box-sec">
                
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Price Exclusive of VAT?</label>
                    <div class="col-sm-9">
                    {{@$row->getRelatedItem->find($id)->is_exclusive_vat}}
                    {{-- @php 
                        $yeschecked = (@$row->getRelatedItem->find($id)->is_exclusive_vat=="Yes") ? true : false;
                        $nochecked = (@$row->getRelatedItem->find($id)->is_exclusive_vat=="No") ? true : false;
                    @endphp
                    Yes
                    {!! Form::radio('is_exclusive_vat', 'Yes', ['class'=>'form-control','id'=>'is_exclusive_vat','checked'=> $yeschecked]) !!}   
                    No
                    {!! Form::radio('is_exclusive_vat', 'No', ['class'=>'form-control','id'=>'is_exclusive_vat','checked'=> $nochecked]) !!}   
                       --}}
                   </div>
                </div>
            </div>  
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Supplier UOM</label>
                    <div class="col-sm-9">
                         {!! Form::select('supplier_uom_id', getUnitOfMeasureList(), @$row->getRelatedItem->find($id)->supplier_uom_id, ['maxlength'=>'255','placeholder' => 'Please Select UOM',  'class'=>'form-control ','id'=>'supplier_uom_id','required'=>true]) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Supplier Quantity</label>
                    <div class="col-sm-9">
                        {!! Form::number('supplier_quantity', @$row->getRelatedItem->find($id)->supplier_quantity, ['min'=>'0', 'required'=>true, 'class'=>'form-control','id'=>'supplier_quantity']) !!}  
                    </div>
                </div>
            </div>

               <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Unit Conversion</label>
                    <div class="col-sm-9">
                        {!! Form::number('unit_conversion', @$row->getRelatedItem->find($id)->unit_conversion, ['min'=>'0', 'required'=>true, 'class'=>'form-control','id'=>'unit_conversion']) !!}  
                    </div>
                </div>
            </div>


            

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Note</label>
                    <div class="col-sm-9">
                       {!! Form::textarea('note', @$row->getRelatedItem->find($id)->note, ['maxlength'=>'1000', 'class'=>'form-control','id'=>'note_1']) !!}  
                    </div>
                </div>
            </div>
            

              <div class="box-footer">
                <button type="submit" class="btn btn-primary" id="update_submitform">Update</button>
            </div>
            </form>         

                  
               


                 


                  
                 
                 
                 

</div>

<div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">
                            Close
                </button>
               
            </div>
            
            
<style type="text/css">
  .assignmenttable{
    overflow: scroll;
  }
  #note_1{
    height: 60px;
  }

</style>

<script type="text/javascript">
 $(document).ready(function(){
       var selected_inventory_category = $("#inventory_category").val();
        manageitem(selected_inventory_category);
        setTimeout(() => {
            $( "#view_last_price_button" ).trigger( "click" );
           
        }, 100);


    });

    function manageitem(selected_inventory_category)
   {
   
        if(selected_inventory_category != "")
        {
            jQuery.ajax({
                url: '{{route('purchase-orders.items')}}',
                type: 'POST',
                data:{selected_inventory_category:selected_inventory_category},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) 
                {
                    $("#item").val('');
                    $("#item").html(response);

                    if(('{{@$row->getRelatedItem->find($id)->wa_inventory_item_id }}'))
                    {

                      
                        $("#item").val('{{@$row->getRelatedItem->find($id)->wa_inventory_item_id}}');

                       // $("#item").trigger('change');
                    
                    }
                    
                   

                }
            });
        }
        else
        {
           $("#item").val('');
           $("#item").html('<option selected="selected" value="">Please select item</option>');
        }
   }

    $("#item").change(function(){
      $("#item_no").val('');
      $("#unit_of_measure").val('');
        var selected_item_id = $("#item").val();
        $( "#view_last_price_button" ).trigger( "click" );

        getItemDetails(selected_item_id);

    });


      $("#unit_conversion,#supplier_quantity").on("keyup", function (e) {
      manageSystemQuantity();
    });

   $("#unit_conversion,#supplier_quantity").change(function (e) {
      manageSystemQuantity();
    });

         function manageSystemQuantity()
   {  
      var unit_conversion =   $("#unit_conversion").val();
      var supplier_quantity =   $("#supplier_quantity").val();
      var quantity = null;
      if(unit_conversion && supplier_quantity)
      {
        quantity = unit_conversion*supplier_quantity;
      }

      $("#quantity").val(quantity);
   }

    function getItemDetails(selected_item_id)
   {
   
    
        if(selected_item_id != "")
        {
            jQuery.ajax({
                url: '{{route('purchase-orders.items.detail')}}',
                type: 'POST',
                data:{selected_item_id:selected_item_id},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) 
                {

                  var obj = jQuery.parseJSON(response);
                    $("#item_no").val(obj.stock_id_code);
                     $("#unit_of_measure").val(obj.unit_of_measure);

                  
                }
            });
        }
       
   }

        $("#inventory_category").change(function(){
      $("#item_no").val('');
      $("#unit_of_measure").val('');
       
        var selected_inventory_category = $("#inventory_category").val();
        manageitem(selected_inventory_category);

    });

       
          $(".validate2").validate();

         $("#update_submitform").on("mouseover", function (e) {
          $("#unit_of_measure").attr('disabled',false);
    });

   $("#update_submitform").on("mouseleave", function (e) {
   
        $("#unit_of_measure").attr('disabled',true);
    });
 
</script>
    
    <script>
        $('#view_last_price_button').click(function(){
            item_id = $('#item').val();
            jQuery.ajax({
                url: '{{route('admin.purchase-orders.view-last-purchases-price')}}',
                type: 'POST',
                //dataType: "json",
                data:{'item_id':item_id},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('#price-box-sec').html(response);
                }
            });
        });
    </script>
