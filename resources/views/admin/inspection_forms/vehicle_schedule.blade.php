
@extends('layouts.admin.admin')

@section('content')
<section class="content">
    @include('message')
    <div class="box box-primary">
        <div class="box-header with-border  no-padding-h-b"><h3 class="box-title"> {!! $title !!} </h3>
        <div>&nbsp;</div>
        <div class="card tabbable">
                    <form class="validate form-horizontal"   role="form" method="POST" action="{{ route($model.'.store_items') }}" enctype = "multipart/form-data">
                            {{ csrf_field() }}
                      <div class="col-md-4 " style="padding:20px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03); ">
                            <ul class="nav nav-pills nav-stacked">
                                <li ><a href="{{route($model.'.edit.items',base64_encode($form_id))}}"> Inspection Items</a></li>
                                <li class="active "><a href="{{route($model.'.edit.vehicle_schedule',base64_encode($form_id))}}" data-bs-target="#v-pills-home1" data-toggle="tab">Vehicle & Schedule</a></li>
                                <li><a href="#Lifecycle" data-toggle="tab">Workflow</a></li>
                                
                            </ul>
                             <!-- <div class="tab-content col-md-9">
                                <div class="tab-pane active" id="a">Lorem ipsum dolor sit amet, charetra varius rci quis tortor imperdiet venenatis quam sit amet vulputate. Quisque mauris augue, molestie tincidunt condimentum vitae, gravida a libero.</div>
                                <div class="tab-pane" id="b">Secondo sed ac orci quis tortor imperdiet venenatis. Duis elementum auctor accumsan. Aliquam in felis sit amet augue.</div>
                                <div class="tab-pane" id="c">Thirdamuno, ipsum dolor sit amet, consectetur adipiscing elit. Duis elementum auctor accumsan. Duis pharetra
                                varius quam sit amet vulputate. Quisque mauris augue, molestie tincidunt condimentum vitae. </div>
                            </div>  -->
                      </div>
                      <div class="col-md-8 tab-content">

                        <div class="col-sm-12 form-div" style="padding:30px; box-shadow:0 5px 20px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06),0 2px 4px rgba(0,0,0,.03);" id="maintenance">
                            <h3>Vehicle & Schedule</h3>
                        </div>

                        
                       

                        
                      </div>
                    </form>
        </div>
    </div>
  </div>
</section>


<style>


    .same-btn{
    margin-right: 10px !important;
    border-radius: 3px !important; 
    border: 1px solid #c7c7c7;
    color: #000;
    }

    .btn-block{
        display: flex;
        justify-content: end;
    }
    .main-box-ul{
        border-radius: 4px;
        background-color: #c7c7c7;
        padding: 10px;
        background-color: #fff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 10%)
    }
    .form-div .same-form{
        background-color: #fff !important;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 10%) !important;
        padding: 10px 12px !important;
        margin: 10px 0;
    }
    .btn-group .green-btn{
        background-color: #44ace9 !important;
    }
</style>
    
   
@endsection

@section('uniquepagescript')
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}">
</script>


@endsection