@extends('layouts.admin.admin')

@section('content')
<?php 
    $logged_user_info = getLoggeduserProfile();
    $my_permissions = $logged_user_info->permissions;

?>


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                        <div  style="height: 150px ! important;"> 
                            <div class="card-header">
                            <i class="fa fa-filter"></i> Filter
                            </div><br>
                            {!! Form::open(['route' => 'merge_payment_report.index','method'=>'POST']) !!}

                            <div>
                            <div class="col-md-12 no-padding-h">


                            <div class="col-sm-3">
                              <div class="form-group">
                              {!!Form::select('salesman_id', getAllsalesmanList(), null, ['placeholder'=>'Select Salesman', 'class' => 'form-control mlselect getshiftdata allsalesman_id'  ])!!}
                              </div>
                              </div>
                           
                              <div class="col-sm-3">
                                <div class="form-group">
                                {!!Form::select('shift_id[]', getAllShiftList(), null, ['placeholder'=>'Select Shift', 'class' => 'form-control  mlselect shiftList', 'multiple'=>'multiple'  ])!!}
                                </div>
                                </div> 

                            </div>

                            <div class="col-md-12 no-padding-h">
                                 <div class="col-sm-1"><button type="submit" class="btn btn-success" name="manage-request" value="filter"  >Filter</button></div>
                                 <!--div class="col-sm-1">
                                <button title="Export In PDF" type="submit" class="btn btn-warning" name="manage-request" value="pdf"  ><i class="fa fa-file-pdf" aria-hidden="true"></i>
                                </button>
                                </div-->
                                 <div class="col-sm-1">
                                <a class="btn btn-info" href="{!! route('payment-reconcilliation.index') !!}"  >Clear </a>
                           
                        </div>
                             <div class="col-sm-2">
                        </div>
                                
                            </div>
                            </div>

                            </form>
                        </div>

                             <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h table-responsive">
                                <table class="table table-bordered table-hover" id="create_datatable_50">
                                    <thead>
                                    <tr>
                                        <th width="10%">S.No.</th>                                       
                                        <th width="10%"  >Date</th>
                                        <th width="10%"  >Transaction No.</th>
                                        <th width="10%"  >Shift Id</th>
                                        <th width="10%"  >Salesman Name</th>
                                        <th width="10%"  >Account</th>
                                        <th width="10%"  >Description</th>
                                        <th width="10%"  >Narrative</th>    
                                        <th width="10%"  >Amount</th>
                                        <th width="10%" class="noneedtoshort" >Action</th>                              
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($lists) && !empty($lists))
                                            <?php $b = 1; ?>
                                            @foreach($lists as $list)
                                                <tr>
                                                    <td>{!! $b !!}</td>
                                                    <td>{!! date('d/M/Y H:i A',strtotime($list->created_at)) !!}</td>
                                                    <td>{!! $list->transaction_no !!}</td>
                                                    <td>{!! $list->shift !!}</td>
                                                    <td>{!! @$list->getShiftDetail->getSalesManDetail->name !!}</td>
                                                    <td>{!! $list->payment_account !!}</td>
                                                    <td>{!! $list->description !!}</td>
                                                    <td>{!! $list->narration !!}</td>
                                                    <td>{!! manageAmountFormat( $list->amount) !!}</td>
                                                    <td class = "action_crud">
                                                        <div style="display:flex">
                                                        <button type="button" class="btn btn-success btn-sm" style="margin-right:5px" data-toggle="modal" data-target="#addRequisitionItemModel{{$b}}">Update Shift</button>	                                                
                                                         <form action="{{route('merge_payment_report.reverse_transactions',['id'=>base64_encode($list->id)])}}" method="POST" class="submitMe">
                                                           {{csrf_field()}}
                                                           <input type="hidden" name="id" value="{{$list->id}}" class="form-control" required />
                                                           <button type="submit" class="btn btn-warning" title="Reverse"><i class="fa fa-undo" aria-hidden="true"></i></button>
                                                         </form>        
                                                         </div>
                                                      
                                                       
       
                                                       
                                                       <div id="addRequisitionItemModel{{$b}}" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                         <div class="modal-dialog" style="width: 40%;">
                                                       
                                                           <!-- Modal content-->
                                                           <div class="modal-content">
                                                             <div class="modal-header">
                                                               <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                             <h4 class="modal-title">Update Shift ID</h4>
                                                             </div>
                                                             <div class="modal-body">
                                                               {!! Form::model(null, ['method' => 'POST','route' => ['payment-reconcilliation.update-shift-id', $list->id],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
                                                                   {{ csrf_field() }}
                                                                           
                                                                    <div class="box-body">
                                                                        <div class="row">
                                                                            <div class=" form-group" style="width:100%">
                                                                                <label for="inputEmail3" class="col-sm-3 control-label">Shift Id</label>
                                                                                <div class="col-sm-9">
                                                                                        <input type="text" style="width:100%" name="new_shift_id" value="{{$list->shift_id}}" class="form-control" required />
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                   
                                                                            <br>
                                                                    
                                                                       <div class="row form-group" style="width:100%">
                                                                           <label for="inputEmail3" class="col-sm-3 control-label"></label>
                                                                           <div class="col-sm-9">
                                                                                <div class="box-footer">
                                                                                    <button type="submit" class="btn btn-primary" id="add_submitform">update</button>
                                                                                </div>
                                                                           </div>
                                                                       </div>
                                                                   </div>
                                                                </form>    
                                                            </div> 
                                                            </div> 
                                                            </div> 
                                                        </div> 
                                                                </td>
                                                </tr>
                                            <?php $b++; ?>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="8" style="text-align: right">
                                                Grand Total : 
                                            </td>
                                            <td colspan="2">
                                                {{manageAmountFormat(@$lists->sum('amount'))}}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
    </section>

@endsection
@section('uniquepagestyle')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
<link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    $(function () {
   $('body').addClass('sidebar-collapse');
    $(".mlselect").select2();
});
                 

                  $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });

        $(document).ready(function(){
  $(".getshiftdata").change(function(){
	  var salesmanId = $(this).val();
    $.ajax({
	    url: "{{route('sales-and-receivables-reports.getShiftBySalesman')}}",
	    dataType:"JSON", 
	    data:{'_token':"{{csrf_token()}}",salesman_id:salesmanId,'shift_summary':'1'},
	    success: function(result){
		    $('.shiftList').html('');
			$.each(result, function (key, val) {
		    $('.shiftList').append('<option value="'+key+'">'+val+'</option>');
			});
//			$("#div1").html(result);
    	}});
  });
});
$('.submitMe').submit(function(input){
    var a = confirm('Are you sure you want to reverse the merged payment?');
    if(!a){
        input.preventDefault();
        return false;
    }
})
            </script>

@endsection

