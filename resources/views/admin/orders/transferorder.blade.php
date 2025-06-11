
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">

                            
                            <br>
                            @include('message')
                              {!! Form::model(null, ['method' => 'get','route' => ['admin.request.transfer-order', $order_slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}

                            <div class="col-md-12 no-padding-h table-responsive">
                            <div class="col-md-3">
                                <b>From Order:#</b> {!! $from_order_id !!}
                                </div>
                                <div class="col-md-1">
                               <b> To:</b> 
                                </div>

                                <div class="col-md-3">
                               {!!Form::select('to_order_id', $to_order_ids_arr, null, ['placeholder'=>'Select order', 'class' => 'form-control select2','id'=>'to_order_manager','onchange'=>'manageFields(this);'  ])!!} 
                                </div>
                                 <div class="col-md-1">
                               <b> Or:</b> 
                                </div>
                                <div class="col-md-3">
                                {!!Form::select('to_table_id', $all_tables, null, ['placeholder'=>'Select table', 'class' => 'form-control select2','id'=>'to_table_manager','onchange'=>'manageFields(this);'  ])!!} 
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
  <style type="text/css">
       .table td {
  font-size: 13px;
}
.box-header.with-border.no-padding-h-b {
  min-height: 300px;
  padding-top: 100px;
  font-size: 18px;
}
.select2-container .select2-selection--single .select2-selection__rendered {
  /* padding-left: 0; */
  /* padding-right: 0; */
  /* height: auto; */
  margin-top: -6px;
  height: 100px !important;
}

   </style>
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    $(function () {
      $("#to_order_manager").select2();
      $("#to_table_manager").select2();
});

  function manageFields(data)
  {
    var selected_id = $(data).attr('id');
    var selected_value = $(data).val();
    if(selected_value == '')
    {
      $('#next').prop('disabled', true);
    }
    else
    {
      if(selected_id == 'to_table_manager')
      {
        $('#to_order_manager').val('').trigger("change");
        $('#next').prop('disabled', false);
      }
      if(selected_id == 'to_order_manager')
      {
        $('#to_table_manager').val('').trigger("change");
        $('#next').prop('disabled', false);
      }
    }
  }


    $(document).ready(function() {
     $('#next').prop('disabled', true);
     
 });




</script>

@endsection



