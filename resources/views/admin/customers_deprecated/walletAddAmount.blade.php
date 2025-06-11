
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
         {!! Form::model($row, ['method' => 'post','route' => [$model.'.post.add.amount.to.wallet', $row->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}
                <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Phone Number</label>
                    <div class="col-sm-10">
                       {!! $row->phone_number !!}
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Amount</label>
                    <div class="col-sm-10">
                        {!! Form::number('amount', null, ['maxlength'=>'255','placeholder' => 'Amount', 'required'=>true, 'class'=>'form-control','min'=>'0','max'=>'10000000']) !!}  
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Payment Mode</label>
                    <div class="col-sm-10">
                    {!!Form::select('entry_type',$payment_mode, null, ['placeholder'=>'Select payment mode', 'class' => 'form-control','required'=>true ])!!}
                        
                    </div>
                </div>
            </div>
           
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Refrence</label>
                    <div class="col-sm-10">
                        {!! Form::text('refrence_description', null, ['maxlength'=>'255','placeholder' => 'Refrence',  'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>
             
             


            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Add</button>
            </div>
        </form>
    </div>
</section>
@endsection

@section('uniquepagestyle')

@endsection

@section('uniquepagescript')


@endsection


