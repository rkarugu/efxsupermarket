
@extends('layouts.admin.admin')
@section('content')
<?php 
$logged_user_info = getLoggeduserProfile();
 ?>
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> Order ID: {!! manageOrderidWithPad($row->id) !!} </h3></div>
         @include('message')
         {!! Form::model($row, ['method' => 'PATCH','route' => ['admin.update.postpad.orders', $row->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}
              

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Order ID</label>
                    <div class="col-sm-10">
                  {!! manageOrderidWithPad($row->id) !!}
                       
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Total Amount</label>
                    <div class="col-sm-10">
                    {!! manageAmountFormat($row->order_final_price) !!}
                       
                    </div>
                </div>
            </div>

               <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Waiter Name</label>
                    <div class="col-sm-10">
                   {!! getAssociateWaiteWithOrder($row) !!}
                       
                    </div>
                </div>
            </div>


           

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Discount(%)</label>
                    <div class="col-sm-10">
                    
                        {!! Form::number('admin_discount_in_percent', null, ['placeholder' => 'Discount', 'required'=>true, 'class'=>'form-control','min'=>0,'max'=>$logged_user_info->max_discount_percent]) !!}  
                    </div>
                </div>
            </div>


             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Discount Reason</label>
                    <div class="col-sm-10">
                    
                        {!! Form::text('discount_reason', null, ['placeholder' => 'Discount Reason', 'class'=>'form-control','maxlength'=>'255']) !!}  
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

@endsection

@section('uniquepagescript')


@endsection


