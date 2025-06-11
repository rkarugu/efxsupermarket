
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
         {!! Form::model($row, ['method' => 'PATCH','route' => [$model.'.update', $row->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}
                      
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Account Group</label>
                    <div class="col-sm-10">
                         {!!Form::select('wa_account_section_id', getAccountSectionDropdown(), null, ['class' => 'form-control wa_account_section_id','required'=>true,'placeholder' => 'Please select group' ,'id'=>'selector_selects' ])!!} 
                    </div>
                </div>
            </div>

                <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Account Section</label>
                    <div class="col-sm-10">
                         {!!Form::select('wa_account_group_id', getParentAccountGroupsDropdown($row->id), null, ['class' => 'form-control wa_account_group_id','required'=>false,'placeholder' => 'Please select section' ,'id'=>'selector_selects2' ])!!} 
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Sub Section Name</label>
                    <div class="col-sm-10">
                        {!! Form::text('section_name', null, ['maxlength'=>'255','placeholder' => 'Sub Section Name', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Sub Section Code</label>
                    <div class="col-sm-10">
                        {!! Form::text('section_code', null, ['maxlength'=>'255','placeholder' => 'Sub Section Code', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</section>
@endsection

@section('uniquepagestyle')

<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
  <script type="text/javascript">
     var wa_account_group_id = function(){
            $(".wa_account_group_id").select2(
            {
                placeholder:'Select Account Sub Section',
                ajax: {
                    url: '{{route("sub-account-sections.account-sections")}}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            id: $('.wa_account_section_id').val()
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
    
    $(function () {
      $("#selector_selects").select2();
    //   $("#selector_selects2").select2();
    wa_account_group_id();
      $("#selector_selects").change(function(e){
        wa_account_group_id();
    })
});
</script>
@endsection


