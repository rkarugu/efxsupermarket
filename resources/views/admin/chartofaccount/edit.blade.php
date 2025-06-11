
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
                    <label for="inputEmail3" class="col-sm-2 control-label">Account Sub Section</label>
                    <div class="col-sm-10">
                        {!!Form::select('wa_account_sub_section_id', getSubAccountSectionDropdown(), null, ['class' => 'form-control wa_sub_account_section','required'=>true,'placeholder' => 'Please select account' ,'id'=>'selector_selects2' ])!!}
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Account Section</label>
                    <div class="col-sm-10" id="account_section">
                        {{@$row->getSubAccountSection->getAccountSection->section_name}}
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Account Group</label>
                    <div class="col-sm-10" id="account_group">
                        <input type='hidden' name='wa_account_group_id' value='{{$row->wa_account_group_id}}'> {{$row->getRelatedGroup->group_name}}
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Account Name</label>
                    <div class="col-sm-10">
                        {!! Form::text('account_name', null, ['maxlength'=>'255','placeholder' => 'Account Name', 'required'=>true, 'class'=>'form-control']) !!}
                    </div>
                </div>
            </div>


            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Account Code</label>
                    <div class="col-sm-10">
                        {!! Form::text('account_code', null, ['maxlength'=>'255','placeholder' => 'Account Code', 'required'=>true, 'class'=>'form-control numberwithhifun','readonly'=>true]) !!}
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Branches *</label>
                    <div class="col-sm-10">

                        {!!Form::select('branches[]', $branches, $row->branches->pluck('id')->toArray(), ['class' => 'form-control selector_selects2','required'=>true,'multiple'=>true  ])!!}
                    </div>
                </div>
            </div>



            {{-- <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Account Group</label>
                    <div class="col-sm-10">
                         {!!Form::select('wa_account_group_id', getAccountGroupsDropdown(), null, ['class' => 'form-control','required'=>true,'placeholder' => 'Please select account group' ,'id'=>'selector_selects2' ])!!}
                    </div>
                </div>
            </div>
              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">P/L OR B/S</label>
                    <div class="col-sm-10">
                         {!!Form::select('pl_or_bs', ['PROFIT AND LOSS'=>'PROFIT AND LOSS','BALANCE SHEET'=>'BALANCE SHEET'], null, ['class' => 'form-control','required'=>true  ])!!}
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Parent Group</label>
                    <div class="col-sm-10">
                         <input type="checkbox" name="parent_group" id="is_parent" value="1" {{$row->is_parent == 1 ? 'checked' : NULL}}>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Parent</label>
                    <div class="col-sm-10">
                         {!!Form::select('parent_id', ['0'=>'Select Parent']+$lists, null, ['class' => 'form-control selector_selects2 parent_selectable'  ])!!}
                    </div>
                </div>
            </div>
--}}



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
        $(function () {

            $("#selector_selects2").select2();
            $(".selector_selects2").select2();
            if($('#is_parent').is(':checked')){
                $('.parent_selectable').attr('disabled',true);
                $('.parent_selectable').attr('required',false);
            }else{
                $('.parent_selectable').attr('disabled',false);
                $('.parent_selectable').attr('required',true);
            }
        });

        $('#is_parent').change(function(e){
            e.preventDefault();
            if($(this).is(':checked')){
                $('.parent_selectable').attr('disabled',true);
                $('.parent_selectable').attr('required',false);
            }else{
                $('.parent_selectable').attr('disabled',false);
                $('.parent_selectable').attr('required',true);
            }
        });
        $('.wa_sub_account_section').change(function(e){
            $.ajax({
                type:'GET',
                data: {
                    id: $('.wa_sub_account_section').val()
                },
                url: "{{route('sub-account-sections.account-detail')}}",
                success: function(suc){
                    $('#account_section').html(suc.section.name) ;
                    $('#account_group').html(suc.group.name + "<input type='hidden' name='wa_account_group_id' value='"+suc.group.id+"'>");
                }
            })
        });
    </script>

@endsection


