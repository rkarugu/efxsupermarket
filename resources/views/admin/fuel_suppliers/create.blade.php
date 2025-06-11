
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="box-title"> Add Fuel Supplier </h3>            
                <a href="{{  route('fuel-suppliers.index') }}" class="btn btn-success">Back</a>
            </div>
        </div>
         @include('message')
        <form class="validate form-horizontal"  role="form" method="POST" action="{{route('fuel-suppliers.store')}}" enctype = "multipart/form-data">
            {{ csrf_field() }}
            <div class="box-body">
                <div class="form-group">
                   
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Supplier</label>
                        <div class="col-sm-9">
                            <select name="supplier" id="supplier" class="mlselect form-control" required>
                                <option value="" selected disabled>Select Supplier</option>
                                @foreach ($suppliers as $supplier )
                                <option value="{{$supplier->id}}">
                                    {{ $supplier->name}}</option>                                
                                @endforeach
                            </select> 
                        </div>
                    </div>

                </div>              
            </div>  
            <div class="box-footer text-right" >
                <button type="submit" class="btn btn-success" >Submit</button>
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

   
@endsection



