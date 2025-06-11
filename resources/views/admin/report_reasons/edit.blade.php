
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                               <form action="{!! route($model.'.update',$row->id)!!}" method="post" class="submitMe">
                                    {{ method_field('PUT') }}
                                    {{ csrf_field() }}
                                    <div class="form-group">
                                        <label for="title">Name</label>
                                        <input type="text" name="name" id="name" value="{{$row->name}}" class="form-control" placeholder="Name" >
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                               </form>
                            </div>
                        </div>
                    </div>


    </section>
   
@endsection
@section('uniquepagescript')
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
@endsection
