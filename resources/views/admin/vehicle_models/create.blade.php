@extends('layouts.admin.admin')

@section('content')
@php
$user = getLoggeduserProfile();
@endphp
<script>
window.user = {!! $user !!}
</script>
    <section class="content">
        <div class="box box-primary">
            <div class="session-message-container">
                @include('message')
            </div>

            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Vehicle Models | Add Model </h3>

                    <a href="{{ route("$base_route.index") }}" role="button" class="btn btn-primary"> Back </a>
                </div>
            </div>

            <div class="box-body">
                <form method="post" action="{{ route("$base_route.store") }}" class="form-horizontal">
                    {{ @csrf_field() }}
                    <div class="form-group">
                        <label for="name" class="col-md-2 control-label"> Model Name </label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="name" name="name" required placeholder="Vehicle model name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-md-2 control-label"> Vehicle Type </label>
                        <div class="col-md-10">
                                <select name="vehicle_type_id" id="vehicle_type_id" class="form-control vehicle_type_id" >
                                    <option value="">Select Vehicle Type</option>
                                    @foreach ($vehicleTypes as $vehicleType)
                                    <option value="{{$vehicleType->id}}">{{$vehicleType->name}}</option>
                                        
                                    @endforeach
                                </select>


                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-md-2 control-label"> Suppliers </label>
                        <div class="col-md-10">
                                <select name="supplier" id="supplier" class="form-control vehicle_type_id" >
                                    <option value="" disabled selected>Select supplier</option>
                                    @foreach ($vehicleSuppliers as $vehicleSupplier)
                                    <option value="{{$vehicleSupplier->id}}">{{$vehicleSupplier->name}}</option>
                                        
                                    @endforeach
                                </select>


                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-md-2 control-label"> Unladed Weight </label>
                        <div class="col-md-10">
                            <input type="number" class="form-control" id="unladed_weight" name="unladed_weight" required placeholder="weight in tonnes">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-md-2 control-label"> Max Load Capacity </label>
                        <div class="col-md-10">
                            <input type="number" class="form-control" id="max_load_capacity" name="max_load_capacity" required placeholder="weight in tonnes">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-md-2 control-label"> Fuel Tank Capacity </label>
                        <div class="col-md-10">
                            <input type="number" class="form-control" id="fuel_tank_capacity" name="fuel_tank_capacity" required placeholder="capacity in litres">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-md-2 control-label"> Tyre Count </label>
                        <div class="col-md-10">
                            <input type="number" class="form-control" id="tyre_count" name="tyre_count" required placeholder="inc of spare">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-md-2 control-label"> Axle Count </label>
                        <div class="col-md-10">
                            <input type="number" class="form-control" id="axle_count" name="axle_count" required placeholder="axle count">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-md-2 control-label"> Travel Expense </label>
                        <div class="col-md-10">
                            <input type="number" class="form-control" id="travel_expense" name="travel_expense" required placeholder="Travel Expense">
                        </div>
                    </div>
                  


                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary"> Add Model </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
@section('uniquepagestyle')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
         $(document).ready(function(){
            $('#vehicle_type_id').select2();
            $('#supplier').select2();

            
         });
         
    </script>
  
@endsection


