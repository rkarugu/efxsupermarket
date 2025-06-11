
@extends('layouts.admin.admin')
@section('content')
<form method="POST" action="{{route('external-requisitions.store_non_stock')}}" accept-charset="UTF-8" class="submitMe" enctype="multipart/form-data">
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
            {{ csrf_field() }}
             <?php 
             $user = getLoggeduserProfile();
                    /*$purchase_no = getCodeWithNumberSeries('EXTERNAL REQUISITIONS');*/
                    $default_branch_id = $user->restaurant_id;
                    $default_department_id = $user->wa_department_id;
                    $requisition_date = date('Y-m-d');


                    ?>

            <div class = "row">

              <div class = "col-sm-6">
                 <div class = "row">
                    <div class="box-body">
                {{-- <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Purchase Requisition No.</label>
                    <div class="col-sm-7">

                   
                        {!! Form::text('purchase_no',  $purchase_no , ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                    </div>
                </div> --}}
            </div>
                 </div>

                   <div class = "row">
                     <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Employee name</label>
                    <div class="col-sm-7">
                        {!! Form::text('emp_name', $user->name, ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>

                   </div>
                    <div class = "row">
                     <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Requisition Date</label>
                    <div class="col-sm-7">
                        {!! Form::text('purchase_date', $requisition_date, ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>
            </div>
            {{--  <div class = "row">
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-5 control-label">Lead Man</label>
                        <div class="col-sm-7">
                            {!! Form::text('lead_man', null, ['maxlength'=>'255','placeholder' => '', 'required'=>false, 'class'=>'form-control']) !!}  
                        </div>
                    </div>
                </div>
            </div>--}}
            <div class = "row">
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-5 control-label">Note</label>
                        <div class="col-sm-7">
                            {!! Form::text('note_main', null, ['maxlength'=>'255','placeholder' => '', 'required'=>false, 'class'=>'form-control']) !!}  
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
                         {!!Form::select('restaurant_id', getBranchesDropdown(),$default_branch_id, ['class' => 'form-control ','required'=>true,'placeholder' => 'Please select branch','id'=>'branch','disabled'=>true  ])!!} 
                    </div>
                </div>
            </div>
                   </div>

                     <div class = "row">

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Department</label>
                    <div class="col-sm-6">
                         {!!Form::select('wa_department_id',getDepartmentDropdown($user->restaurant_id), $default_department_id, ['class' => 'form-control ','required'=>true,'placeholder' => 'Please select department','id'=>'department'])!!} 
                    </div>
                </div>
            </div>
                     </div>

                     {{--
                    <div class = "row">

                        <div class="box-body">
                           <div class="form-group">
                               <label for="inputEmail3" class="col-sm-5 control-label">Project</label>
                               <div class="col-sm-6">
                                    {!!Form::select('project_id',$projects, NULL, ['class' => 'form-control ','required'=>true,'placeholder' => 'Please select project','id'=>'project' ])!!} 
                               </div>
                           </div>
                       </div>
                    </div>
--}}

                    <div class = "row">

                        <div class="box-body">
                           <div class="form-group">
                               <label for="inputEmail3" class="col-sm-5 control-label">Priority Level</label>
                               <div class="col-sm-6">
                                    {!!Form::select('project_level',$projectLevel, NULL, ['class' => 'form-control ','required'=>true,'placeholder' => 'Please select priority level','id'=>'project' ])!!} 
                               </div>
                           </div>
                       </div>
                    </div>
                    <div class = "row">
                        <div class="box-body">
                          <div class="form-group">
                            <label for="inputEmail3" class="col-sm-5 control-label">Date When item is required</label>
                            <div class="col-sm-6">
                            <input type="date" name="required_date" class="form-control">
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
                             
                          
                            <div class="col-md-12 no-padding-h">
                           <h3 class="box-title"> Requisition Line</h3>
                           <button type="button" class="btn btn-danger btn-sm addNewrow" style="position: fixed;bottom: 30%;left:4%;"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                <table class="table table-bordered table-hover" id="requisitionitemtable">
                                    <thead>
                                    <tr>
                                        {{-- <th>S.No.</th> --}}
                                        {{-- <th>Item Category</th> --}}
                                        <th>Item</th>
                                        <th>UOM</th>
                                        <th >Qty Req</th>
                                        {{--                                     
                                        <th> Cost</th>
                                        <th>Total Cost</th>
                                        <th>VAT Rate</th>
                                        <th> VAT Amount</th>
                                        <th>Total Cost In VAT</th> --}}
                                        <th >Note</th>
                                       

                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr>                                      
                                            <td>
                                              <input type="text" class=" form-control item" name="item[0]">                                              
                                            </td>
                                            <td>
                                                <select name="uom[0]" class="form-control uom"></select>
                                            </td>
                                            <td><input type="text" class=" form-control quantity" name="quantity[0]"></td>
                                             <td><input type="text" class=" form-control note" name="note[0]"></td>
                                            {{--<td><select  class="form-control select2Select"  name="item_gl[]">
                                                <option value="" selected disabled>Select GL Account</option>
                                                @foreach ($chart_of_accounts as $item)
                                                <option value="{{$item->id}}">{{$item->account_code}} - {{$item->account_name}}</option>
                                                @endforeach
                                            </select></td> --}}
                                            <td><button type="button" class="btn btn-danger btn-sm deleteparent"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
                                        </tr>

                                    </tbody>
                                </table>
                                
                            </div>
                       


                              <div class="col-md-12">
                              <div class="col-md-6"><span>
                             

                              {{-- <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#addRequisitionItemModel">Add Item To Requisition</button> --}}
                              <button type="submit" class="btn btn-success btn-sm" >Send Requisition</button>
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
    height: 80px !important;
   }
   .align_float_right
{
  text-align:  right;
}
 </style>
@endsection

@section('uniquepagescript')
{{-- <div id="loader-on" style="
position: absolute;
top: 0;
text-align: center;
display: block;
z-index: 999999;
width: 100%;
height: 100%;
background: #000000b8;
display:none;
" class="loder">
  <div class="loader " id="loader-1"></div>
</div> --}}
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
  <script type="text/javascript">
  var form = new Form();
  var uom = function(){
      $(".uom").select2({
        ajax: {
            url: '{{route("external-requisitions.get_WaUnitOfMeasure")}}',
            dataType: 'json',
            type: "GET",
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                    var res = data.map(function (item) {
                        return {id: item.id, text: item.text};
                    });
                return {
                    results: res
                };
            }
        }
    });
  }
  uom();
$(document).on('keypress',".quantity",function(event) {
    if (event.keyCode === 13) {
      event.preventDefault();
      $(".addNewrow").click();
    }
  });
  
  function makemefocus(){
    if($(".makemefocus")[0]){
        $(".makemefocus")[0].focus();
    }
  }
    $(function () {
      $(".mlselec6t").select2();  
      $('body').addClass('sidebar-mini sidebar-collapse')
});

var newItem = '<tr>'+                                      
                                            '<td>'+
                                              '<input type="text" class=" form-control item" name="item[0]">'+                                              
                                            '</td>'+
                                            '<td>'+
                                                '<select name="uom[0]" class="form-control uom"></select>'+
                                            '</td>'+
                                            '<td><input type="text" class=" form-control quantity" name="quantity[0]"></td>'+
                                            '<td><input type="text" class=" form-control note" name="note[0]"></td>'+
                                            '<td><button type="button" class="btn btn-danger btn-sm deleteparent"><i class="fa fa-trash" aria-hidden="true"></i></button></td>'+
                                        '</tr>';

$(document).on('click','.addNewrow', function () {
    $(".uom").select2("destroy");
    $('#requisitionitemtable tbody').append(newItem);
    updateList().then(
        function(value) { uom(); },
        function(error) { form.errorMessage('Something went wrong'); }
    );
    
});
async function updateList(){
    var list = $('#requisitionitemtable tbody tr');
    var i = 0;
    $.each(list, function (indexInArray, valueOfElement) { 
         $(valueOfElement).find('.item').attr('name','item['+i+']');
         $(valueOfElement).find('.uom').attr('name','uom['+i+']');
         $(valueOfElement).find('.quantity').attr('name','quantity['+i+']');
         $(valueOfElement).find('.note').attr('name','note['+i+']');
         i++;
    });
    return true;
}

$(document).on('click','.deleteparent', function () {
    $(this).parents('tr').remove();
});
</script>
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script>
        $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
        $('.select2Select').parent().css('text-align','left');
    $('.select2Select').select2();
    </script>
@endsection


