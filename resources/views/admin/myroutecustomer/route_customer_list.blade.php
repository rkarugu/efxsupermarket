@extends('layouts.admin.admin')
@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                         
                            @include('message')
                            <div class="col-md-12 no-padding-h table-responsive">
                                <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                        <tr>
                                            <th>Dated</th>
                                            <th>Name</th>
                                            <th>Phone No.</th>
                                            <th>Business</th>
                                            <th>Town</th>
                                            <th>Contact Person</th>
                                            <th>Total Sales</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      @php $totalCashSales= 0;@endphp
                                        @foreach ($lists as $key => $item)
                                          @php $totalCashSales+=$item->total_sales; @endphp
                                            <tr>
                                                <td>{{date('d/M/Y',strtotime($item->created_at))}}</td>
                                                <td>{{$item->name}}</td>
                                                <td>{{$item->phone}}</td>
                                                <td>{{$item->bussiness_name}}</td>
                                                <td>{{$item->town}}</td>
                                                <td>{{$item->contact_person}}</td>
                                                <td>{{manageAmountFormat(@$item->total_sales)}}</td>
                                                <td><a href="{!! route('my-route-customers.edit',$item->id)!!}" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> </a></td>
                                            </tr>
                                        @endforeach
                                        
                                    </tbody>
                                    <tfoot>
                                      <tr>
                                        <th colspan="7" class="text-right">Total :- {{manageAmountFormat($totalCashSales)}}</th>
                                      </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>


    </section>
   
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