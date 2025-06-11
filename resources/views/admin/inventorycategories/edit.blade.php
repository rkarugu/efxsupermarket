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
                    <label for="inputEmail3" class="col-sm-2 control-label">Category Code</label>
                    <div class="col-sm-10">
                        {!! Form::text('category_code', null, ['maxlength'=>'255','placeholder' => 'Category Code', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Category Description</label>
                    <div class="col-sm-10">
                        {!! Form::text('category_description', null, ['maxlength'=>'255','placeholder' => 'Category Description', 'required'=>true, 'class'=>'form-control']) !!}
                    </div>
                </div>
            </div>


            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Stock Type</label>
                    <div class="col-sm-10">
                        {!!Form::select('wa_stock_type_category_id', getstockTypeCategory(), null, ['class' => 'form-control mlselect','required'=>true,'placeholder' => 'Please select'  ])!!}
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Stock Family Group</label>
                    <div class="col-sm-10">
                        {!!Form::select('wa_stock_family_group_id', getstockFamilyGroup(), null, ['class' => 'form-control mlselect','required'=>true,'placeholder' => 'Please select'  ])!!}
                    </div>
                </div>
            </div>


            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Inventory Account</label>
                    <div class="col-sm-10">
                        {!!Form::select('stock_gl_code_id', getChartOfAccountsDropdown(), null, ['class' => 'form-control mlselect','required'=>true,'placeholder' => 'Please select'  ])!!}
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">COS GL Account</label>
                    <div class="col-sm-10">
                        {!!Form::select('internal_stock_issues_gl_code_id', getChartOfAccountsDropdown(), null, ['class' => 'form-control mlselect','required'=>true,'placeholder' => 'Please select'  ])!!}
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Sales Account</label>
                    <div class="col-sm-10">
                        {!!Form::select('wip_gl_code_id', getChartOfAccountsDropdown(), null, ['class' => 'form-control mlselect','required'=>true,'placeholder' => 'Please select'  ])!!}
                    </div>
                </div>
            </div>
            {{-- <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">WIP GL Code</label>
                    <div class="col-sm-10">
                         {!!Form::select('wip_gl_code_id', getChartOfAccountsDropdown(), null, ['class' => 'form-control mlselect','required'=>true,'placeholder' => 'Please select'  ])!!} 
                    </div>
                </div>
            </div>
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Inventory Account</label>
                    <div class="col-sm-10">
                         {!!Form::select('stock_adjustments_gl_code_id', getChartOfAccountsDropdown(), null, ['class' => 'form-control mlselect','required'=>true,'placeholder' => 'Please select'  ])!!} 
                    </div>
                </div>
            </div>
            --}}

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Stock Excess Account </label>
                    <div class="col-sm-10">
                        {!!Form::select('price_variance_gl_code_id', getChartOfAccountsDropdown(), null, ['class' => 'form-control  mlselect','required'=>true,'placeholder' => 'Please select'  ])!!}
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Stock Shortage Account </label>
                    <div class="col-sm-10">
                        {!!Form::select('usage_variance_gl_code_id', getChartOfAccountsDropdown(), null, ['class' => 'form-control mlselect','required'=>true,'placeholder' => 'Please select'  ])!!}
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Item Sub Categories</label>
                    <div class="col-sm-10">
                        <select name='item_sub_categories[]' class='form-control item_sub_categories' multiple="multiple">
                            @foreach($row->sub_categories as $cat)
                                <option value="{{$cat->id}}" selected>{{$cat->title}}</optiom>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="image" class="col-sm-2 control-label"> Category Image </label>
                    <div class="col-md-10">
                        <input type="file" name="image" id="image" class="form-control">
                        <img id="preview-image" src="{{ $row->image }}" alt="Category image" style="margin-top: 20px;" width="200" height="200" />
                    </div>
                </div>
            </div>

            {{-- <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Image</label>
                    <div class="col-sm-10">
                      {!! Form::file('image', null, ['required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div> --}}


            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
            </form>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script>
        $('#image').change(function () {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#preview-image').attr('src', e.target.result);
                $('#preview-image').css('display', 'block');
            }

            reader.readAsDataURL(this.files[0]);
        });
    </script>

    <script type="text/javascript">
        $(function () {

            $(".mlselect").select2();
            // $(".item_sub_categories").select2();
            $('.item_sub_categories').select2(
                {
                    placeholder: 'Select Sub Category',
                    ajax: {
                        url: '{{route("item-sub-categories.dropdown_search",['id'=>$row->id])}}',
                        dataType: 'json',
                        type: "GET",
                        delay: 250,
                        data: function (params) {
                            return {
                                search: params.term
                            };
                        },
                        processResults: function (data) {
                            var res = data.map(function (item) {
                                return {id: item.id, text: item.title};
                            });
                            return {
                                results: res
                            };
                        }
                    },
                });
        });
    </script>

@endsection

