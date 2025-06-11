
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
        <form class="validate"  role="form" method="POST" action="{{ route($model.'.postJournal') }}" enctype = "multipart/form-data">
            {{ csrf_field() }}
          
             <?php 
                   
                    $date_to_process = date('Y-m-d');


                    ?>

            <div class = "row">
                              <div class = "col-sm-6">
                  
            @php 
            $type = array(
                "GL Account"=>"GL Account",
                "Bank Account"=>"Bank Account",
                "Customer Account"=>"Customer Account",
                "Supplier Account"=>"Supplier Account"
               );
            @endphp
                    
            <div class = "row">
               <div class="box-body">
                <div class="form-group">
                   <label for="inputEmail3" class="col-sm-3 control-label">Account Type</label>
                    <div class="col-sm-9">
                        {!!Form::select('entry_type', $type,'Supplier Account', ['class' => 'form-control account_type','required'=>true  ])!!} 
                    </div>
                </div>
              </div>
            </div>
                
            <div class = "row">
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">Account Name/No</label>
                        <div class="col-sm-9">
                            {!!Form::select('gl_account_id', getChartOfAccountsDropdown(), null, ['class' => 'form-control mlselect accountno','required'=>true,'placeholder' => 'Please select'  ])!!} 
                        </div>
                    </div>
                </div>
            </div>     


            <div class = "row">
                <div class="box-body">
                    <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Debit</label>
                            <div class="col-sm-4">

                   
                                {!! Form::number('debit',  0 , ['min'=>'0', 'class'=>'form-control','required'=>true]) !!}  
                            </div>
                            {{--
                                <label for="inputEmail3" class="col-sm-1 control-label">Credit</label>
                                <div class="col-sm-4">
                                    {!! Form::number('credit',  0 , [ 'min'=>'0','class'=>'form-control','required'=>true]) !!}  
                                </div>
                            --}}
                    </div>
                </div>
            </div>

            <div class = "row">
              <div class="box-body">
                <div class="form-group">
                   <label for="inputEmail3" class="col-sm-3 control-label">Narrative</label>
                    <div class="col-sm-9">
                        {!! Form::text('narrative', null , ['maxlength'=>'255' ,'class'=>'form-control','required'=>true]) !!}  
                    </div>
                </div>
            </div>
           </div>
           
            <div class = "row" id="open_for_inter_branch_transaction" >
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-4 control-label">Inter Branch GL/Account</label>
                        <div class="col-sm-8">
                            {!!Form::select('inter_branch_transaction_id', getChartOfAccountsInterBranchDropdown(), null, ['class' => 'form-control mlselect','required'=>true,'placeholder' => 'Please select'  ])!!} 
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class = "col-sm-6">
            <div class = "row">
               <div class="box-body">
                <div class="form-group">
                   <label for="inputEmail3" class="col-sm-3 control-label">Account Type</label>
                    <div class="col-sm-9">
                        {!!Form::select('entry_type', $type,'Bank Account', ['class' => 'form-control account_type','required'=>true  ])!!} 
                    </div>
                </div>
              </div>
            </div>
                
            <div class = "row">
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-3 control-label">Account Name/No</label>
                        <div class="col-sm-9">
                            {!!Form::select('gl_account_id', getChartOfAccountsDropdown(), null, ['class' => 'form-control mlselect accountno','required'=>true,'placeholder' => 'Please select'  ])!!} 
                        </div>
                    </div>
                </div>
            </div>     


            <div class = "row">
                <div class="box-body">
                    <div class="form-group">



                        <label for="inputEmail3" class="col-sm-3 control-label">Credit</label>
                        <div class="col-sm-4">
                            {!! Form::number('credit',  0 , [ 'min'=>'0','class'=>'form-control','required'=>true]) !!}  
                        </div>
                            
                    </div>
                </div>
            </div>    

            

              </div>

            





            </div>

            <div class = "row">
                <div class="box-body">
                    <div class="form-group">
                       
                        <div class="col-sm-12">
                           <button type="submit" class="btn btn-primary">Process</button>
                        </div>
                    </div>
                </div>
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
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script>
        $('#is_inter_branch_transaction').change(function(e){
            $('#open_for_inter_branch_transaction').hide();
            if($(this).is(':checked')){
                $('#open_for_inter_branch_transaction').show();
            }
        })
        $('.account_type').on('change',function(){
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
        $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
        var projects = function(){
            $(".projects").select2(
            {
                placeholder:'Select projects',
                ajax: {
                    url: '{{route("expense.projects")}}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
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
                },
            });
        }
        projects();
        var gl_tags = function(){
            $(".gl_tags").select2(
            {
                placeholder:'Select Gl tags',
                ajax: {
                    url: '{{route("expense.gl_tags")}}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
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
                },
            });
        }
        gl_tags();
    </script>
<script type="text/javascript">
    $(function () {

        $(".mlselect").select2();
        $('#open_for_inter_branch_transaction').hide();

});
</script>

@endsection


