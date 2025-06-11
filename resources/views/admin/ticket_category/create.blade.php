@extends('layouts.admin.admin')
@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title" style="font-weight: 500 !important;"> New {{$title}} </h3>
                    <div class="d-flex">
                        
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="col-md-12 no-padding-h" id="getintervalview">
                    <form class="validate form-horizontal"  role="form" method="POST" action="{{ route($model.'.store') }}" enctype = "multipart/form-data">
                        @csrf
                        <div class="">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-2 text-left">Name</label>
                                <div class="col-sm-10">
                                    {!! Form::text('title', null, ['maxlength'=>'255','placeholder' => 'Name', 'required'=>true, 'class'=>'form-control']) !!}  
                                </div>
                            </div>
                        </div>            
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-solid fa-save"></i> Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('uniquepagestyle')

</style>    
@endsection
@section('uniquepagescript')

<script>
    $(document).ready(function() {
        
    });
    </script>
@endsection