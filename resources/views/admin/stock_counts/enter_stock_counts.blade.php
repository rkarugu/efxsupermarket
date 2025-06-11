
@extends('layouts.admin.admin')

@section('content')


<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="box box-primary">
         {!! Form::model('WaStockCount', ['method' => 'POST','route' => ['admin.stock-counts.enter-update-stock-counts'],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
        <div class="box-header with-border no-padding-h-b">
            @include('message')
            <div align = "center">
                <div class="form-group col-sm-4">
                    <label for="inputEmail3" class="col-sm-4 control-label">Store Location:</label>
                    <div class="col-sm-8">
                        {!!Form::select('wa_location_and_store_id', $location_list, ($user->role_id  == 152 ? $user->wa_location_and_store_id : null), ['id'=>'location-input','class' => 'form-control authorization_level mlselec6t','required'=>true,'placeholder' => 'Please select'])!!} 
                    </div>
                </div>
                <div class="form-group col-sm-4">
                    <label for="inputEmail3" class="col-sm-4 control-label">Bin Location:</label>
                    <div class="col-sm-8">
                        {!!Form::select('wa_unit_of_measure_id', [], ($user->role_id  == 152 ? $user->wa_unit_of_measures_id : null), ['id'=>'wa_unit_of_measure_id','class' => 'form-control wa_unit_of_measure_id mlselec6t','required'=>true,'placeholder' => 'Please select'])!!} 
                    </div>
                </div>
                <div class="form-group col-sm-4">
                    <label for="inputEmail3" class="col-sm-4 control-label">Inventory Category:</label>
                    <div class="col-sm-8">
                        {!!Form::select('wa_inventory_category_id', [], null, ['id'=>'category-input','class' => 'form-control authorization_level mlselec6t','required'=>true,'placeholder' => 'Please select'])!!} 
                    </div>
                </div>
            </div>
            <br>
            {{ csrf_field() }}
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="create_item_stock_form">
                        <thead>
                            <tr>
                                <th width="10%">Stock ID Code</th>
                                <th width="20%">Title</th>
                                <th width="10%">Pack Size</th>
                                <th width="30%"  >Quantity</th>
                                <th width="30%"  >Reference</th>
                            </tr>
                        </thead>
                        <tbody id="table-form-data">
                        </tbody>  
                    </table>
                </div>
        </div>
          {{ Form::close() }}
    </div>
</section>
@endsection
@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
  <style>
      /* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
}
  </style>
@endsection
@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
 $(function () {
            $(".mlselec6t").select2();
        });


    $('document').ready(function(){
        var user = @json($user);
        //check if user is store keeper
        if(user.role_id == 152){
            $.ajax({
           type: "GET",
           url: "{{route('admin.stock-takes.getCategories')}}",
           data: {
            'wa_location_and_store_id':user.wa_location_and_store_id,
            'wa_unit_of_measure_id': user.wa_unit_of_measures_id,
           },
           success: function (response) {
               if(response.result == 1){
                   $('#category-input').html(response.data);
                   $('.wa_unit_of_measure_id').html(response.unit);
                $('#table-form-data').html('');
                console.log(response);
            }
           }
       });
            // updateTableForm();
            jQuery.ajax({
            url: '{{route('admin.stock-counts.enter-stock-counts-form-list')}}',
            type: 'POST',
            //dataType: "json",
            data:{location_id:user.wa_location_and_store_id, category_id:0,  wa_unit_of_measure_id:user.wa_unit_of_measures_id},
            headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                $('#table-form-data').html(response);
            }
        });
        }      
    });

    $('#location-input, #category-input, #wa_unit_of_measure_id').change(function(){
        location_selected_value = $('#location-input').val();
        category = $('#category-input').val();
        wa_unit_of_measure_id = $('#wa_unit_of_measure_id').val();
        if(location_selected_value != '' && category != '' && wa_unit_of_measure_id != null){
            updateTableForm();
        }
    });

    $('#location-input').change(function(e){
       e.preventDefault();
       $this = $(this);
       $.ajax({
           type: "GET",
           url: "{{route('admin.stock-takes.getCategories')}}",
           data: {
            'wa_location_and_store_id':$this.val()
           },
           success: function (response) {
               if(response.result == 1){
                   $('#category-input').html('<option value="All">All</option>' +  response.data);
                   $('.wa_unit_of_measure_id').html(response.unit);
                $('#table-form-data').html('');
                console.log(response);
            }
           }
       });

    });
    function updateTableForm(){
        var user = @json($user);
        //check if user is store keeper
        if(user.role_id == 152){
            
        location_selected_value = user.wa_location_and_store_id;
        wa_unit_of_measure_id = user.wa_unit_of_measures_id;
        category_selected_value =  $('#category-input').val();
        }else{
            
        location_selected_value = $('#location-input').val();
        category_selected_value = $('#category-input').val();
        wa_unit_of_measure_id = $('#wa_unit_of_measure_id').val();
        

        }
        

        jQuery.ajax({
            url: '{{route('admin.stock-counts.enter-stock-counts-form-list')}}',
            type: 'POST',
            //dataType: "json",
            data:{location_id:location_selected_value, category_id:category_selected_value,wa_unit_of_measure_id:wa_unit_of_measure_id},
            headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                $('#table-form-data').html(response);
            }
        });
    }
</script>
@endsection
