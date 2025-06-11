
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
         {!! Form::model($row, ['method' => 'PATCH','route' => ['admin.update.profile', $row->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}
           <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-10">
                        {!! Form::text('name', null, ['maxlength'=>'255','placeholder' => 'Name', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>
           
           

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-10">
                        {!! Form::email('email', null, ['maxlength'=>'255','placeholder' => 'Email', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Phone Number</label>
                    <div class="col-sm-10">
                        {!! Form::text('phone_number', null, ['maxlength'=>'255','placeholder' => 'Phone Number', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

            @if($row->role_id == '1')
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Max Discount For Pay Later Order(%)</label>
                    <div class="col-sm-10">
                       {!! Form::number('max_discount_percent', null,['class'=>'form-control','placeholder'=>'Value','min'=>0,'max'=>100]) !!}
                    </div>
                </div>
            </div>
                @else
                    {!! Form::hidden('max_discount_percent',$row->max_discount_percent) !!}
            @endif

                        <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Branch</label>
                    <div class="col-sm-10">
                        {!!Form::select('restaurant_id', $restroList, null, ['placeholder'=>'Select Branch ', 'class' => 'form-control','required'=>true,'title'=>'Please select Branch','id'=>'branch'  ])!!}
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Department</label>
                    <div class="col-sm-10">
                         {!!Form::select('wa_department_id', getDepartmentDropdown($row->restaurant_id), null, ['class' => 'form-control mlselec6t','required'=>true,'placeholder' => 'Please select department','id'=>'department' ])!!} 
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Store Location</label>
                    <div class="col-sm-10">
                        
                         {!!Form::select('wa_location_and_store_id', getStoreLocationDropdownByBranch($row->restaurant_id), null, ['class' => 'form-control mlselec6t','required'=>true,'placeholder' => 'Please select store location','id'=>'wa_location_and_store_id' ])!!} 
                    </div>
                </div>
            </div>
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Image</label>
                    <div class="col-sm-10">
                        <input type = "file" name = "image_update" title = "Please select image"  accept="image/*">
                        <img width="100px" height="100px;" src="{{ asset('uploads/users/thumb/'.$row->image) }}">
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</section>
@endsection


@section('uniquepagestyle')
 <link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script>
        $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });

            $("#branch").change(function(){
        var selected_branch_id = $("#branch").val();
        managedepartment(selected_branch_id);
        manageStoreLocation(selected_branch_id);

    });

                function managedepartment(branch_id)
   {
        if(branch_id != "")
        {
            jQuery.ajax({
                url: '{{route('external-requisitions.get-departments')}}',
                type: 'POST',
                data:{branch_id:branch_id},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) 
                {
                    $("#department").val('');
                    $("#department").html(response);

                    manageTotalCost();

                }
            });
        }
        else
        {
           $("#department").val('');
           $("#department").html('<option selected="selected" value="">Please select department</option>');
        }
   }


function manageStoreLocation(branch_id)
   {
        if(branch_id != "")
        {
            jQuery.ajax({
                url: '{{route('locations.get-location-by_branch')}}',
                type: 'POST',
                data:{branch_id:branch_id},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) 
                {
                    $("#wa_location_and_store_id").val('');
                    $("#wa_location_and_store_id").html(response);

                    //manageTotalCost();
                    
                }
            });
        }
        else
        {
           $("#wa_location_and_store_id").val('');
           $("#wa_location_and_store_id").html('<option selected="selected" value="">Please select store location</option>');
        }
   }
    </script>
@endsection

