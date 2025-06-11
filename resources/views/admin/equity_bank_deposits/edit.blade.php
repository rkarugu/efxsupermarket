@extends('layouts.admin.admin')
@section('content')

<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"> {!! $title !!} </h3>
        </div>       
        <div class="box-body">
        <form action="{{route('equity-bank-deposits.update',$data->id)}}" method="post" class="submitMe">
            {{csrf_field()}}
            <input type="hidden" name="id" value="{{$data->id}}">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="">Salesman</label>
                        <select name="salesman" id="salesman" class="form-control select_select2">
                            <option value="" selected disabled>Select Salesman</option>
                            @foreach ($locations as $item)
                                <option value="{{@$item->id}}" @if($data->wa_location_and_store_id == $item->id) selected @endif>{{$item->location_name}} ({{$item->location_code}})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                      <label for="">Bill Amount</label>
                      <input type="text" name="billAmount" id="billAmount" class="form-control" placeholder="Enter Bill Amount" value="{{$data->billAmount}}">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                      <label for="">Customer Ref Number</label>
                      <input type="text" name="CustomerRefNumber" id="CustomerRefNumber" class="form-control" placeholder="Enter Ref" value="{{$data->CustomerRefNumber}}">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                      <label for="">Bank Ref</label>
                      <input type="text" name="bankreference" id="bankreference" class="form-control" placeholder="Enter Ref" value="{{$data->bankreference}}">
                    </div>
                </div>
                
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-danger">Process</button>
                </div>
            </div>
        </form>
        </div>
    </div>
</section>


@endsection

@section('uniquepagestyle')
<style>
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

@keyframes spin{
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
@endsection

@section('uniquepagescript')
<div id="loader-on" style="
position: fixed;
top: 0;
text-align: center;
display: block;
z-index: 999999;
width: 100%;
height: 100%;
background: #000000b8;
display:none;
" class="loder">
  <div class="loader" id="loader-1"></div>
</div>
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
<script>
  
</script>
@endsection


