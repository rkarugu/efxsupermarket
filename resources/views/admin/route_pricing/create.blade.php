
    @extends('layouts.admin.admin')
    @section('content')
    <section class="content">    
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> {{$inventoryItem->title}} | Create Route Pricing  </h3>            
                    <a href="{{  route('item-centre.show', $inventoryItem->id) }}" class="btn btn-success">Back</a>
                
                </div>

            </div>
        
            {{-- @include('message') --}}
            <form class="validate form-horizontal"  role="form" method="POST" action="{{ route('route.pricing.store', $inventoryItem->id) }}" enctype = "multipart/form-data">
                {{ csrf_field() }}
                <div class="box-body">
                    <div class="form-group">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Standard Cost : {{$inventoryItem->standard_cost}}</label>
                        
                            <label for="inputEmail3" class="col-sm-3 control-label">Selling Price : {{$inventoryItem->selling_price}}</label>
                        
                            <label for="inputEmail3" class="col-sm-3 control-label">Min Margin : {{$inventoryItem->percentage_margin}} {{($inventoryItem->margin_type == 1) ? '%': 'Kes'}}</label>
                            
                        </div>  
            
                    
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Branch</label>
                        <div class="col-sm-9">
                            <select name="branch" id="branch" class="mlselect form-select" required>
                                <option value="" selected disabled>Select Branch</option>
                                @foreach ($branches as $branch )
                                <option value="{{$branch->id}}" {{ old('branch') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    
                                @endforeach
                            </select> 

                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Routes</label>
                        <div class="col-sm-9">
                            <select name="routes[]" id="routes" class="mlselect form-select" multiple required>
                                @foreach ($routes as $route )
                                <option value="{{$route->id}}" {{ in_array($route->id, old('routes', [])) ? 'selected' : '' }}>{{ $route->route_name }}</option>
                                    
                                @endforeach
                            </select> 
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Price</label>
                        <div class="col-sm-9">
                            {!! Form::number('price', null, ['maxlength'=>'255','placeholder' => '0', 'required'=>true, 'class'=>'form-control']) !!}  
                            @if ($errors->has('price'))
                            <span class="text-danger">
                                <strong>{{ $errors->first('price') }}</strong>
                            </span>
                        @endif
                        </div>
                        
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Type</label>
                            <div class="col-sm-10">
                                <div class="d-flex">
                                    <div class="form-check form-check-inline" style="margin-right:10px;">
                                        <input class="form-check-input" type="radio" name="type" id="is_flash" value="1"  required>
                                        <label class="form-check-label" for="is_flash">Flash</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="type" id="not_flash" value="0"  checked   >
                                        <label class="form-check-label" for="not_flash">Non Flash</label>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>
                
                    </div>              
                </div>  
                <div class="box-footer" >
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
            $(document).ready(function() {
        $('#branch').change(function() {
            var branchId = $(this).val(); 

            $.ajax({
                url: '{{ route("get.routes.by.branch") }}', 
                type: 'GET',
                data: {branch_id: branchId},
                success: function(response) {
                    $('#routes').empty();
                    $.each(response.routes, function(key, value) {
                        $('#routes').append('<option value="' + value.id + '">' + value.route_name + '</option>');
                    });
                }
            });
        });
    });
        </script>
      
        
    @endsection



