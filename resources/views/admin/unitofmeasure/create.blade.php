
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
        <form class="validate form-horizontal"  role="form" method="POST" action="{{ route($model.'.store') }}" enctype = "multipart/form-data">
            {{ csrf_field() }}
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Unit</label>
                    <div class="col-sm-10">
                        {!! Form::text('title', null, ['minlength'=>'2','maxlength'=>'255','placeholder' => 'More than one character', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="chief_storekeeper" class="col-sm-2 control-label">Chief StoreKeeper</label>
                    <div class="col-sm-10">
                        <select name="chief_storekeeper" id="chief_storekeeper" class="select2" required>
                            <option value="" disabled>--Select Chief Storekeeper--</option>
                            @foreach ($users as $user)
                                <option value="{{$user->id}}">{{$user->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Is Display</label>

                    <div class="col-sm-10">

                        <input type="checkbox" name="is_display" id="is_display" style="margin-top: 10px;">

                    </div>
                </div>
            </div>

              


            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</section>
@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>

    </style>
@endsection
@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(function () {
            $(".select2").select2();
        });
    </script>
@endsection


