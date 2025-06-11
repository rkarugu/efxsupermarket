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
                    $purchase_no = $row->purchase_no;
                    $default_branch_id = $row->restaurant_id;
                    $default_department_id = $row->wa_department_id;
                    $purchase_date = $row->purchase_date;


                    ?>
 
 
                {!! Form::model($row, ['method' => 'POST','route' => $form_url,'class'=>'validate2','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}

            
               {!! Form::hidden('purchase_no', $purchase_no, []) !!}  
                {!! Form::hidden('purchase_date', $purchase_date, []) !!} 

               
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Item Category</label>
                    <div class="col-sm-9">
                        {!! Form::select('wa_inventory_category_id', getInventoryCategoryList(),$row->getRelatedItem->find($id)->getInventoryItemDetail->wa_inventory_category_id, ['maxlength'=>'255','placeholder' => 'Please select category', 'required'=>true, 'class'=>'form-control mlselec6t','id'=>'inventory_category']) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Item Name</label>
                    <div class="col-sm-9">
                        {!! Form::select('wa_inventory_item_id', [],$row->getRelatedItem->find($id)->wa_inventory_item_id, ['maxlength'=>'255','placeholder' => 'Please select item', 'required'=>true, 'class'=>'form-control mlselec6t','id'=>'item']) !!}  
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Item No</label>
                    <div class="col-sm-9">
                       
                        {!! Form::text('item_no', $row->getRelatedItem->find($id)->item_no, ['maxlength'=>'255', 'class'=>'form-control','id'=>'item_no','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>

                <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Standard Cost</label>
                    <div class="col-sm-3">
                       
                        {!! Form::text('standard_cost', $row->getRelatedItem->find($id)->standard_cost, ['maxlength'=>'255', 'class'=>'form-control','id'=>'standard_cost','readonly'=>true]) !!}  
                    </div>

                     <label for="inputEmail3" class="col-sm-3 control-label">Last Purchase Price</label>
                    <div class="col-sm-3">
                       
                         {!! Form::text('prev_standard_cost', $row->getRelatedItem->find($id)->prev_standard_cost, ['maxlength'=>'255', 'class'=>'form-control','id'=>'prev_standard_cost','readonly'=>true]) !!}   
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">System UOM</label>
                    <div class="col-sm-3">
                      


                         {!! Form::select('unit_of_measure', getUnitOfMeasureList(),$row->getRelatedItem->find($id)->unit_of_measure, ['maxlength'=>'255','placeholder' => '',  'class'=>'form-control ','id'=>'unit_of_measure','disabled'=>true]) !!}  
                    </div>

                    <label for="inputEmail3" class="col-sm-3 control-label">System Quantity</label>
                    <div class="col-sm-3">
                      


                        {!! Form::number('quantity', $row->getRelatedItem->find($id)->quantity, ['min'=>'1', 'required'=>true, 'class'=>'form-control','step'=>'1','id'=>'quantity','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Order Price</label>
                    <div class="col-sm-9">
                        {!! Form::number('order_price', $row->getRelatedItem->find($id)->order_price, ['min'=>'1', 'required'=>true, 'class'=>'form-control','step'=>'1','id'=>'order_price']) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Supplier UOM</label>
                    <div class="col-sm-9">
                         {!! Form::select('supplier_uom_id', getUnitOfMeasureList(), $row->getRelatedItem->find($id)->supplier_uom_id, ['maxlength'=>'255','placeholder' => 'Please Select UOM',  'class'=>'form-control ','id'=>'supplier_uom_id','required'=>true]) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Supplier Quantity</label>
                    <div class="col-sm-9">
                        {!! Form::number('supplier_quantity', $row->getRelatedItem->find($id)->supplier_quantity, ['min'=>'1', 'required'=>true, 'class'=>'form-control','step'=>'1','id'=>'supplier_quantity']) !!}  
                    </div>
                </div>
            </div>

               <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Unit Conversion</label>
                    <div class="col-sm-9">
                        {!! Form::number('unit_conversion', $row->getRelatedItem->find($id)->unit_conversion, ['min'=>'1', 'required'=>true, 'class'=>'form-control','step'=>'1','id'=>'unit_conversion']) !!}  
                    </div>
                </div>
            </div>


            

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Note</label>
                    <div class="col-sm-9">
                       {!! Form::textarea('note', $row->getRelatedItem->find($id)->note, ['maxlength'=>'1000', 'class'=>'form-control','id'=>'note']) !!}  
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

</style>

<style type="text/css">
   .select2{
    width: 100% !important;
   }
   #last_total_row td {
  border: none !important;
}
 #note{
    height: 60px !important;
   }
   .align_float_right
{
  text-align:  right;
}
 </style>
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
<script type="text/javascript">

 $(function () {
      $(".mlselec6t").select2();
     
     
});
 $(document).ready(function(){
       var selected_inventory_category = $("#inventory_category").val();
        manageitem(selected_inventory_category);
       

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

                    if(('{{$row->getRelatedItem->find($id)->wa_inventory_item_id }}'))
                    {

                      
                        $("#item").val('{{$row->getRelatedItem->find($id)->wa_inventory_item_id}}');

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
       $("#standard_cost").val('');
        $("#prev_standard_cost").val('');
        var selected_item_id = $("#item").val();
       
        getItemDetails(selected_item_id);

    });

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
                        $("#standard_cost").val(obj.standard_cost);
                      $("#prev_standard_cost").val(obj.prev_standard_cost);

                  
                }
            });
        }
       
   }

        $("#inventory_category").change(function(){
      $("#item_no").val('');
        $("#item").val('');
      $("#unit_of_measure").val('');
       $("#standard_cost").val('');
        $("#prev_standard_cost").val('');
       
        var selected_inventory_category = $("#inventory_category").val();
        manageitem(selected_inventory_category);

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


        $(".validate2").validate();

         $("#update_submitform").on("mouseover", function (e) {
          $("#unit_of_measure").attr('disabled',false);
    });

   $("#update_submitform").on("mouseleave", function (e) {
   
        $("#unit_of_measure").attr('disabled',true);
    });

 
</script>

