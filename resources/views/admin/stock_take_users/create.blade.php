
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="box-title"> Assign Stock Take User </h3>            
                <a href="{{  route('admin.stock-counts-users-assingment') }}" class="btn btn-primary"><i class="fas fa-long-arrow-alt-left"></i> Back</a>
            </div>
        </div>
         @include('message')
        <form class="validate form-horizontal"  role="form" method="POST" action="{{route('admin.stock-counts-users-assingment.store')}}" enctype = "multipart/form-data">
            {{ csrf_field() }}
            <div class="box-body">
                <div class="form-group">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Stock Take Date</label>
                        <div class="col-sm-9">
                            {!! Form::date('stock_take_date', old('stock_take_date'), ['maxlength'=>'255','placeholder' => '0', 'required'=>true, 'class'=>'form-control']) !!}  
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">User</label>
                        <div class="col-sm-9">
                            <select name="user[]" id="user[]" class="mlselect form-control" multiple required>
                                <option value="" selected disabled>Select User</option>
                                @foreach ($users as $user )
                                    <option value="{{$user->id}}"> {{ $user->name}} </option>                                
                                @endforeach
                            </select> 
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Category</label>
                        <div class="col-sm-9">
                            <select name="category[]" id="category[]" class="mlselect form-control" multiple required>
                                <option value="" selected disabled>Select Category</option>
                                @foreach ($categories as $category )
                                <option value="{{$category->id}}">
                                    {{ $category->category_description}}</option>                                
                                @endforeach
                            </select> 
                        </div>
                    </div>

                </div>              
            </div>  
            <div class="box-footer align-items-right" >
                <button type="submit" class="btn btn-primary" ><i class="fa fa-solid fa-save"></i> Save</button>
            </div>
        </form>
    </div>
</section>
@endsection
@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(function () {
            $(".mlselect").select2();
        });
    </script>
    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>
    <script type="text/javascript" class="init">
        $(document).ready(function () {
            $('#create_datatable1').DataTable({
                pageLength: "100",
                "order": [
                    [0, "desc"]
                ]
            });
        });
    </script>
@endsection



