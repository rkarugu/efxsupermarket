
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
         @php
       //  echo "<pre>"; print_r($item); die;
         @endphp
        {!! Form::model($row, ['method' => 'PATCH','route' => [$model.'.update', $row->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
          
            {{ csrf_field() }}
          
           
       <input type="hidden" id="type" value="{{$item->entry_type}}">
       <input type="hidden" id="last_etnered" value="{{$lastitem->entry_type}}">

       <input type="hidden" id="debitamnt" value="{{round($item->debit,2)}}">

       <input type="hidden" id="creditamnt" value="{{round($item->credit,2)}}">

       <div class = "row">
          <div class = "col-sm-6">
            @if($item->entry_type == "GL Account")
            @php 
            $type = array(
                "GL Account"=>"GL Account",
                "Bank Account"=>"Bank Account",
                "Customer Account"=>"Customer Account",
                "Supplier Account"=>"Supplier Account"
               );
            @endphp
            @elseif($item->entry_type == "Bank Account")  
            @php 
            $type = array(
                "GL Account"=>"GL Account",
                "Bank Account"=>"Bank Account"
               );
            @endphp
            @elseif($item->entry_type == "Customer Account")  
            @php 
            $type = array(
                "GL Account"=>"GL Account",
                "Customer Account"=>"Customer Account"
               );
            @endphp
            @elseif($item->entry_type == "Supplier Account")  
           
           @php 
            $type = array(
                "GL Account"=>"GL Account",
                "Supplier Account"=>"Supplier Account"
               );
            @endphp
			@else
            @php 
            $type = array(
                "GL Account"=>"GL Account",
                "Bank Account"=>"Bank Account",
                "Customer Account"=>"Customer Account",
                "Supplier Account"=>"Supplier Account"
               );
            @endphp
			
            @endif

                    

               <div class = "row">
               <div class="box-body">
                <div class="form-group">
                   <label for="inputEmail3" class="col-sm-3 control-label">Account Type</label>
                    <div class="col-sm-9">
                        {!!Form::select('entry_type', $type,null, ['class' => 'form-control account_type','required'=>true  ])!!} 
                    </div>
                </div>
              </div>
            </div>


        <div class = "row">
        <div class="box-body">
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-3 control-label">Account Name/No</label>
                <div class="col-sm-9">
                    {!!Form::select('gl_account_id', $accountnolist, null, ['class' => 'form-control mlselect accountno','required'=>true,'placeholder' => 'Please select'  ])!!} 
                </div>
            </div>
        </div>
        </div>            
 
    

                  <div class = "row">
                    <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Debit</label>
                    <div class="col-sm-4">
                        {!! Form::number('debit',  0 , ['min'=>'0', 'class'=>'form-control debit','required'=>true]) !!}  
                    </div>
                    <label for="inputEmail3" class="col-sm-1 control-label">Credit</label>
                    <div class="col-sm-4">
                        {!! Form::number('credit',  0 , [ 'min'=>'0','class'=>'form-control credit','required'=>true]) !!}  
                    </div>
                </div>
            </div>
                 </div>


                   <div class = "row">
                    <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Narrative</label>
                    <div class="col-sm-9">
                        {!! Form::text('narrative', null , ['maxlength'=>'255' ,'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>
                 </div>



                  <div class = "row">
                    <div class="box-body">
                <div class="form-group">
                   
                    <div class="col-sm-12">
                       <button type="submit" class="btn btn-primary">Add</button>
                    </div>
                </div>
            </div>
                 </div>

                   

              </div>
              <div class = "col-sm-6">
                 <div class = "row">
                    <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Entry No.</label>
                    <div class="col-sm-7">

                   
                        {!! Form::text('journal_entry_no',  null , ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>
                 </div>

                  
                    <div class = "row">
                     <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Date to Process Journal</label>
                    <div class="col-sm-7">
                        {!! Form::text('date_to_process',null, ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>
            </div>

            <div class = "row">
               <div class="box-body">
                <div class="form-group">
                   <label for="inputEmail3" class="col-sm-5 control-label">Branch</label>
                    <div class="col-sm-7">
                        {!! Form::select('restaurant',$restroList, null,['placeholder'=>"Select Branch", 'required'=>true,'class'=>'form-control']) !!}  
                    </div>
                </div>
              </div>
            </div>
            <div class = "row">
              <div class="box-body">
                <div class="form-group">
                   <label for="inputEmail3" class="col-sm-5 control-label">Reference</label>
                    <div class="col-sm-7">
                        {!! Form::text('reference', null , ['maxlength'=>'255' ,'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>
           </div>


              </div>




            </div>





          

            


           



            


           

             
        </form>
    </div>
</section>



    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">

                                                            <?php $i =1;

                                    $credit = $row->getRelatedItem?round($row->getRelatedItem->sum('credit'),2):0;
                                    $debit = $row->getRelatedItem?round($row->getRelatedItem->sum('debit'),2):0;
                                    $is_show_process_button = false;

                                    $string = 'Required to balance - ';
                                     if($credit == $debit)
                                    {
                                        $is_show_process_button = true;
                                    }
                                    else
                                    {
                                        if($credit<$debit)
                                        {
                                            $string .= $debit-$credit.' Credit';
                                        }
                                        else
                                        {
                                             $string .= $credit-$debit.' Debit';
                                        }
                                    }
                                    ?>

                                    @if($is_show_process_button==false)

                                   
                                    <span colspan="7" style="color: red;font-weight: bold;">{{ $string }}</span>
                                   


                                    @endif
                             
                          
                            <div class="col-md-12 no-padding-h table-responsive">
                           <h3 class="box-title">Journal Summary</h3>

                            <span id = "requisitionitemtable">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                      <th>S.No.</th>
                                      <th>Account Type</th>
                                      <th>Account</th>
                                      <th>Account Name</th>
                                      <th>Debit</th>
                                       <th>Credit</th>
                                        <th>Narrative</th>
                                      <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>



                                    @foreach($row->getRelatedItem as $item)
                                       <tr>
                                       <td>{{ $i }}</td>
                                       <td>{{ @$item->entry_type }}</td>

                                       @if($item->entry_type == "GL Account")
                                       <td>{{ @$item->getGlDetail->account_code }}</td>
                                       <td>{{ @$item->getGlDetail->account_name }}</td>
                                       @endif
                                       @if($item->entry_type == "Bank Account")
                                       <td>{{ @$item->getGlDetail->account_code }}</td>
                                       <td>{{ @$item->getGlDetail->account_name }}</td>
                                       @endif
                                       @if($item->entry_type == "Customer Account")
                                       <td>{{ @$item->getCustDetail->customer_code }}</td>
                                       <td>{{ @$item->getCustDetail->customer_name }}</td>
                                       @endif
                                       @if($item->entry_type == "Supplier Account")
                                       <td>{{ @$item->getSuppDetail->supplier_code }}</td>
                                       <td>{{ @$item->getSuppDetail->name }}</td>
                                       @endif


                                       <td>{{ $item->debit }}</td>
                                      <td>{{ $item->credit }}</td>
                                      <td>{{ $item->narrative }}</td>
                                       <td><span>
                                           <a href = "{{ route($model.'.deleteItem',[$row->slug,$item->id])}}" title = "Delete"><i class="fa fa-trash"></i></a>
                                       </span></td>
                                      
                                      
                                    </tr>
                                     <?php $i++;?>
                                    @endforeach


                                  


                        
                                   


                                    </tbody>
                                </table>
                                </span>
                            </div>

                              @if($is_show_process_button == true)
                               <div align="right"><a href="{{ route($model.'.process',$row->slug)}}" class="btn btn-success">Process</a></div>
                                    @endif

                       


                            


                               
                        </div>
                    </div>


    </section>
@endsection

@section('uniquepagestyle')

 <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
@endsection

@section('uniquepagescript')


    
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
        var showamntinput1 = $('#debitamnt').val();
        var showamntinput2 = $('#creditamnt').val();
        var selectedtype = $('#type').val();
        var lastselectedtype = $('#last_etnered').val();
        if(selectedtype != $('.account_type').val() && selectedtype!="GL Account"){
            if(lastselectedtype == $('.account_type').val()){
                if(showamntinput1 > 0){
                    $('.debit').attr('disabled','disabled');
                    $('.credit').removeAttr('disabled');
                }else{
                    $('.debit').removeAttr('disabled');
                    $('.credit').attr('disabled','disabled');
                }

                if(showamntinput2 > 0){
                    $('.debit').removeAttr('disabled');
                    $('.credit').attr('disabled','disabled');
                }else{
                    $('.debit').attr('disabled','disabled');
                    $('.credit').removeAttr('disabled');
                }
            }else{
            if(lastselectedtype != selectedtype  && selectedtype!="GL Account"){
                    $('.debit').attr('disabled','disabled');
                    $('.credit').attr('disabled','disabled');
                }else{
                    $('.debit').removeAttr('disabled');
                    $('.credit').removeAttr('disabled');
                }
            }
        }else{
            if(selectedtype!="GL Account"){
                if(showamntinput1 > 0){
                    $('.debit').removeAttr('disabled');
                    $('.credit').attr('disabled','disabled');
                }else{
                    $('.debit').attr('disabled','disabled');
                    $('.credit').removeAttr('disabled');
                }

                if(showamntinput2 > 0){
                    $('.debit').attr('disabled','disabled');
                    $('.credit').removeAttr('disabled');
                }else{
                    $('.debit').removeAttr('disabled');
                    $('.credit').attr('disabled','disabled');
                }
            }
        }


        $('.account_type').on('change',function(){

        console.log(selectedtype+" : "+lastselectedtype);

        if(selectedtype != $(this).val()  && selectedtype!="GL Account"){
            if(lastselectedtype == $(this).val()){
                if(showamntinput1 > 0){
                    $('.debit').attr('disabled','disabled');
                    $('.credit').removeAttr('disabled');
                }else{
                    $('.debit').removeAttr('disabled');
                    $('.credit').attr('disabled','disabled');
                }

                if(showamntinput2 > 0){
                    $('.debit').removeAttr('disabled');
                    $('.credit').attr('disabled','disabled');
                }else{
                    $('.debit').attr('disabled','disabled');
                    $('.credit').removeAttr('disabled');
                }
            }else{
            if(lastselectedtype != selectedtype && selectedtype!="GL Account"){
                    $('.debit').attr('disabled','disabled');
                    $('.credit').attr('disabled','disabled');
                }else{
                if(showamntinput1 > 0){
                    $('.debit').attr('disabled','disabled');
                    $('.credit').removeAttr('disabled');
                }else{
                    $('.debit').removeAttr('disabled');
                    $('.credit').attr('disabled','disabled');
                }

                if(showamntinput2 > 0){
                    $('.debit').removeAttr('disabled');
                    $('.credit').attr('disabled','disabled');
                }else{
                    $('.debit').attr('disabled','disabled');
                    $('.credit').removeAttr('disabled');
                }
                }
            }
        }else{
//          alert($(this).val());
          if($(this).val()!="GL Account"){          
                if(showamntinput1 > 0){
                    $('.debit').removeAttr('disabled');
                    $('.credit').attr('disabled','disabled');
                }else{
                    $('.debit').attr('disabled','disabled');
                    $('.credit').removeAttr('disabled');
                }

                if(showamntinput2 > 0){
                    $('.debit').attr('disabled','disabled');
                    $('.credit').removeAttr('disabled');
                }else{
                    $('.debit').removeAttr('disabled');
                    $('.credit').attr('disabled','disabled');
                }
          }else{
                    $('.debit').removeAttr('disabled');
                    $('.credit').removeAttr('disabled');            
          }
        }

            var account_type = $(this).val();
      //      alert(account_type);
            $.ajax({
                url:"{{route('journal-entries.getAccountNo')}}?type="+account_type,
                method:"GET",
                dataType:"json",
                success:function(response){
                    console.log(JSON.stringify(response));
                    $('.accountno')
                        .empty();
                        $('.accountno')
                        .append($("<option></option>").attr("value","")
                        .text("Please select"));
                    $.each(response.data, function(key, value) {   
                    $('.accountno')
                        .append($("<option></option>")
                        .attr("value",key)
                        .text(value)); 
                    });
                }
            });
            
        });  
    $(function () {
   
    $(".mlselect").select2();
});
</script>

@endsection


