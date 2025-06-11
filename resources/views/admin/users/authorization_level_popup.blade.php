      
<div class="modal-header">
    <button type="button" class="close" 
       data-dismiss="modal">
           <span aria-hidden="true">&times;</span>
           <span class="sr-only">Close</span>
    </button>
    <h4 class="modal-title" id="myModalLabel">
       Internal Authorization Level Assignment
    </h4>
</div>
<div class="modal-body">
 
                  <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Employee Name:</label>
                    <div class="col-sm-10" style="font-weight: bold;margin-top: 2px;">
                         
                    
                         {!! $row->name !!}
                      </div>
                     
                  </div>

                    <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Select Level:</label>
                    <div class="col-sm-10">
                         {!!Form::select('authorization_level', getAuthorizerLevels(), $row->authorization_level, ['class' => 'form-control authorization_level','required'=>false,'placeholder' => 'Please select'  ])!!} 
                    </div>
                </div>
            </div>

                 

                  
               


                 


                  
                 
                 
                 

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

<script type="text/javascript">
 $(".authorization_level").change(function(){
  var emp_level = $(this).val();
  if(emp_level == "")
  {
    emp_level = '0';
  }
     jQuery.ajax({
          url: '{{route('assign-authorization-for-employee')}}',
          type: 'POST',
          data:{emp_level:emp_level,user_slug:'{!! $slug !!}'},
           headers: {
              'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
          },
          success: function (response) 
          {
            
          }
       });




 });
</script>