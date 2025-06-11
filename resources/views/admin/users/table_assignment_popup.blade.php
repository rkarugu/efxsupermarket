      
<div class="modal-header">
    <button type="button" class="close" 
       data-dismiss="modal">
           <span aria-hidden="true">&times;</span>
           <span class="sr-only">Close</span>
    </button>
    <h4 class="modal-title" id="myModalLabel">
        Table Assignment
    </h4>
</div>
<div class="modal-body">
 
                  <div class="form-group">
                    <label for="exampleInputEmail1">Waiter Name: {!! $row->name !!}</label>
                     
                  </div>

                  <?php 
                  $default_display  = 'none';
                  if(count($all_tables)>0)
                  {
                    $default_display  = '';
                  }
                  ?>

                   <div class="form-group release_all_tables_div" style="display:{!! $default_display !!} ">
                    <label for="exampleInputEmail1"><a href="javascript:void(0)" class="btn btn-success release_all_tables">Release All Tables</a></label>
                     
                  </div>
                  <input type="hidden" name="user_id" value="{!! $row->id !!}">


                 
					<div class="col-md-12 no-padding-h">
          <div class="col-md-6 no-padding-h">
						<table class="table table-bordered table-hover assignmenttable" id="create_datatable">
							<thead>
              <tr>
                  
                  <th >Asigned Table</th>
                </tr>
								<tr>
									
									<td>Table Name</td>
								</tr>
							</thead>
							<tbody id="already_assigned_tr">
              @if(count($all_tables)>0)
							@foreach($all_tables as $table)
								<tr id="tr_{!! $table->id !!}">
									
									<td><input onChange="assigning_uncheck({!! $table->id !!})" class= "already_assigned_checkbox" id="already_assigned_{!! $table->id !!}" type="checkbox" name="tableids[{!! $table->id !!}]" data = "{!! $table->block_section !!}" data-title = "{!! $table->name !!}"
                  @if(in_array($table->id,$already_assigned)) checked @endif
                  > &nbsp;&nbsp;&nbsp;{!! $table->name !!}</td>
								</tr>
								@endforeach
                @endif

							</tbody>
						</table>
            </div>
            <div class="col-md-6 no-padding-h">
            <table class="table table-bordered table-hover assignmenttable" id="create_datatable">
              <thead>
                <tr>
                  <th  >Table To Be Assign</th>
                </tr>
                 <tr>
                  <td >Section:<select class="form-control select_section" style="width: 80%;float: right;">
                    <option value="">Select section</option>
                    <?php
                    $sections = getTableBlockSection();
                    foreach($sections as $section_key=>$section)
                    {
                     ?>
                        <option value="{!! $section_key !!}">{!! $section !!}</option>
                     <?php
                    } ?>

                  </select></td>
                  
                </tr>
              </thead>
              <tbody id="tbody_for_sections">
              
             

              </tbody>
            </table>
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
  $(".select_section").change(function(){
    manageTableBolckSection();
   
  });


  $(".release_all_tables").click(function(){


    var isconfirmed=confirm("Do you want to clear all assigned table?");
    if (isconfirmed)
    {
          jQuery.ajax({
    url: '{{route('assign-or-remove-table-from-waiter')}}',
    type: 'POST',
    data:{table_id:0,user_slug:'{!! $slug !!}',type:'removeall'},
     headers: {
        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
    },
    success: function (response) {


      if(response == 'CANNOTREMOVE')
            {
              alert('Can not remove the table assignment due to some pending order');
              $("#already_assigned_"+table_id).prop('checked',true);
            }
            else
            {
              $(".release_all_tables_div").css('display','none');
             $("#already_assigned_tr").html('');
              manageTableBolckSection(); 
            }
     
    }
    });
    } 

  });



  


  


  function assigning_check(table_id)
  {
    //add data to check start//


    var block_section = $("#assigning_"+table_id).attr('data');
    var table_name = $("#assigning_"+table_id).attr('data-title');
    $("#tr_"+table_id).remove();
    jQuery.ajax({
    url: '{{route('assign-or-remove-table-from-waiter')}}',
    type: 'POST',
    data:{table_id:table_id,user_slug:'{!! $slug !!}',type:'assign'},
     headers: {
        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
    },
    success: function (response) {
      var res = parseInt(response) ;
      if(res == 1)
      {
        var html_obj =  '<tr id="tr_'+table_id+'"><td><input onchange="assigning_uncheck('+table_id+')" class="already_assigned_checkbox" id="already_assigned_'+table_id+'" name="tableids['+table_id+']" data="'+block_section+'" data-title="'+table_name+'" checked="" type="checkbox"> &nbsp;&nbsp;&nbsp;'+table_name+'</td></tr>';
        $("#already_assigned_tr").append(html_obj); 
        $(".release_all_tables_div").css('display','');
      }
      else
      {
        alert('Table already assigned to another waiter');
      } 


    }
    });
    //add data to check end//
  }


  function assigning_uncheck(table_id)
  {
    //add data to uncheck start//
    var block_section = $("#already_assigned_"+table_id).attr('data');
    var table_name = $("#already_assigned_"+table_id).attr('data-title');
    var selected_section = $(".select_section").val();
    jQuery.ajax({
          url: '{{route('assign-or-remove-table-from-waiter')}}',
          type: 'POST',
          data:{table_id:table_id,user_slug:'{!! $slug !!}',type:'remove'},
           headers: {
              'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
          },
          success: function (response) 
          {

            if(response == 'CANNOTREMOVE')
            {
              alert('Can not remove the table assignment due to some pending order');
              $("#already_assigned_"+table_id).prop('checked',true);
            }
            else
            {
              $("#tr_"+table_id).remove();
              if(selected_section == block_section)
              {
                var html_obj = '<tr id="tr_'+table_id+'"><td><input onChange="assigning_check('+table_id+')" class= "assigned_checkbox" id="assigning_'+table_id+'" type="checkbox" data="'+block_section+'" data-title = "'+table_name+'"> &nbsp;&nbsp;&nbsp;'+table_name+'</td></tr>';
                $("#tbody_for_sections").append(html_obj); 
              }

              var total_assigned =  $("#already_assigned_tr tr").length;
             
              if(parseInt(total_assigned) == 0)
              {
                $(".release_all_tables_div").css('display','none');
              }
            }
            
           
           

            
          }
       });
    //add data to uncheck end//
  }

  function manageTableBolckSection()
  {
    var selected_section = $(".select_section").val();
    if(selected_section=='')
    {
      $("#tbody_for_sections").html('');
    }
    else
    {
      jQuery.ajax({
          url: '{{route('admin.table.assignment.with.user.section')}}',
          type: 'POST',
          data:{section:selected_section,user_slug:'{!! $slug !!}'},
           headers: {
              'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
          },
          success: function (response) {
            var data_obj = $.parseJSON(response);
            var html_obj = '';
            $.each(data_obj, function( i, v ) {
              html_obj += '<tr id="tr_'+v.table_id+'"><td><input onChange="assigning_check('+v.table_id+')" class= "assigned_checkbox" id="assigning_'+v.table_id+'" type="checkbox" data="'+v.block_section+'" data-title = "'+v.table_name+'"> &nbsp;&nbsp;&nbsp;'+v.table_name+'</td></tr>';
            });
            $("#tbody_for_sections").html(html_obj); 
          }
       });
    }
  }
</script>