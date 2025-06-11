
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">

                            
                            <br>
                            @include('message')
                              {!! Form::model(null, ['method' => 'get','route' => ['admin.request-transfer-bill-to-order', $bill->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}

                            <div class="col-md-12 no-padding-h table-responsive">
                            <div class="col-md-3">
                                <b>From Bill:#</b> {!! $bill->id !!}
                                </div>
                                <div class="col-md-2">
                               <b> To:</b> 
                                </div>

                                <div class="col-md-6">
                               {!!Form::select('to_bill_id', $all_left_bill, null, ['placeholder'=>'Select Bill', 'class' => 'form-control select2','id'=>'to_bill_id','required'=>true  ])!!} 
                                </div>
                           

                                <div class="col-md-1">
                                <button class="btn btn-success" type ="submit" id="next"> Next <i class="fa fa-angle-double-right" ></i> </button>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>


    </section>
 

 
@endsection

@section('uniquepagestyle')


@endsection



