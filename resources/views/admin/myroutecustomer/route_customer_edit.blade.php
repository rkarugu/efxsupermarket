
@extends('layouts.admin.admin')

@section('content')

<form action="{{route('my-route-customers.route_customer_update',$customer->id)}}" method="post" class="submitMe">
    {{csrf_field()}}
    <section class="content">       
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                <h4>{{$title}}</h4>
                <hr>
                <div class="col-md-12 no-padding-h table-responsive">
                    <div>
                        <input type="hidden" name="customer_id" value="{{$customer->customer_id}}">
                        <input type="hidden" name="route_id" value="{{$customer->route_id}}">
                    </div>
                    <div class="form-group">
                      <label for="">Name</label>
                      <input type="text" name="name" id="name" class="form-control" placeholder="Enter Name" aria-describedby="helpId" value="{{@$customer->name}}">
                    </div>
                    <div class="form-group">
                      <label for="">Phone No</label>
                      <input type="text" name="phone_no" id="phone_no" class="form-control" placeholder="Enter Phone No" aria-describedby="helpId" value="{{@$customer->phone}}" >
                    </div>
                    <div class="form-group">
                      <label for="">Business Name</label>
                      <input type="text" name="business_name" id="business_name" class="form-control" placeholder="Enter Business Name" aria-describedby="helpId" value="{{@$customer->bussiness_name}}" >
                    </div>
                    <div class="form-group">
                      <label for="">Town</label>
                      <input type="text" name="town" id="town" class="form-control" placeholder="Enter Town" aria-describedby="helpId" value="{{@$customer->town}}" >
                    </div>
                    <div class="form-group">
                      <label for="">Contact Person</label>
                      <input type="text" name="contact_person" id="contact_person" class="form-control" placeholder="Enter Contact Person" aria-describedby="helpId" value="{{@$customer->contact_person}}" >
                    </div>
                    <button type="submit" class="btn btn-sm btn-danger">Save</button>
                </div>
            </div>
        </div>
    </section>  
</form> 
@endsection
@section('uniquepagescript')
<style type="text/css">
   .select2{
     width: 100% !important;
    }
    #note{
      height: 60px !important;
    }
    .align_float_right
    {
      text-align:  right;
    }
    .textData table tr:hover{
      background:#000 !important;
      color:white !important;
      cursor: pointer !important;
    }


/* ALL LOADERS */

.loader{
  width: 100px;
  height: 100px;
  border-radius: 100%;
  position: relative;
  margin: 0 auto;
  top: 35%;
}

/* LOADER 1 */

#loader-1:before, #loader-1:after{
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 100%;
  border: 10px solid transparent;
  border-top-color: #3498db;
}

#loader-1:before{
  z-index: 100;
  animation: spin 1s infinite;
}

#loader-1:after{
  border: 10px solid #ccc;
}

@keyframes  spin{
  0%{
    -webkit-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }

  100%{
    -webkit-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}

    </style>
<div id="loader-on" class="loder" style="
position: fixed;
top: 0;
text-align: center;
display: block;
z-index: 999999;
width: 100%;
height: 100%;
background: #000000b8;
display:none;
">
  <div class="loader " id="loader-1"></div>
</div>
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
@endsection