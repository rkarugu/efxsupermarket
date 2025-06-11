
@extends('layouts.admin.admin')
@section('content')
{{-- <form method="POST" action="{{ route($model.'.store') }}" accept-charset="UTF-8" class="submitMe" enctype="multipart/form-data" novalidate="novalidate"> --}}
<section class="content">    
    <div class="box box-primary">
      <div class="box-header with-border">
        <div class="box-header-flex">
            <h3 class="box-title"> Receive Transfers </h3>
            <a href="{{route('n-transfers.indexProcessed')}}" class="btn btn-primary">{{'<< '}} Back</a>
        </div>
    </div> 
         @include('message')
            {{ csrf_field() }}
             <?php 
                    $transfer_no = getCodeWithNumberSeries('TRAN');
                    $transfer_date = date('Y-m-d');
                    $user = getLoggeduserProfile();

                    ?>

            <div class = "row">

              <div class = "col-sm-6">
                 <div class = "row">
                    <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Transfer No.</label>
                    <div class="col-sm-7">

                   
                        {!! Form::text('transfer_no',  $interBranchTransfer->transfer_no , ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>
                 </div>
                 <div class = "row">
                  <div class="box-body">
             <div class="form-group">
                 <label for="inputEmail3" class="col-sm-5 control-label">Manual Document No.</label>
                 <div class="col-sm-7">
                     {!! Form::text('emp_name', $interBranchTransfer->manual_doc_number ?? '-', ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
    
                 </div>
             </div>
         </div>
         </div>

                   <div class = "row">
                     <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Employee name</label>
                    <div class="col-sm-7">
                        {!! Form::text('emp_name', $user?->name, ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>

                   </div>
                    <div class = "row">
                     <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Transfer Date</label>
                    <div class="col-sm-7">
                        {!! Form::text('emp_name', \Carbon\Carbon::parse($interBranchTransfer->created_at)->toFormattedDayDateString() , ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  

                    </div>
                </div>
            </div>
            </div>



              </div>
              <div class = "col-sm-6">
                   <div class = "row">
                     <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Branch</label>
                    <div class="col-sm-6">
                        {!! Form::text('transfer_no',  getlocationRowById($interBranchTransfer->from_store_location_id)->location_name , ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>
                   </div>


                        <div class = "row">
                        @php
            $allToStore = [];
            $tostore = \DB::table('wa_location_and_stores')->get();
            @endphp
            @foreach($tostore as $t)
            @php
            $allToStore[$t->id] = $t->location_name .' ( '.$t->location_code.')';
            @endphp
            @endforeach
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">From Store</label>
                    <div class="col-sm-6">
                        {!! Form::text('transfer_no',  getlocationRowById($interBranchTransfer->from_store_location_id)->location_name , ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>
                     </div>

                        <div class = "row">

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">To Store</label>
                    <div class="col-sm-6">
                        {!! Form::text('transfer_no',  getlocationRowById($interBranchTransfer->to_store_location_id)->location_name , ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>
                     </div>

              </div>


            </div>
        
    </div>
</section>

       <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                             
                          
                            <div class="col-md-12 no-padding-h">
                           <h3 class="box-title"> Items </h3>
                           @php
                           $totalItems = $interBranchTransferItems->count() ?? 0 ;
                           $totalQty = 0 ;
                           $totalWeight = 0 ;
                           $totalCost = 0 ;
                           @endphp

                                <table class="table table-bordered table-hover" id="mainItemTable">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                      <th>Item Code</th>
                                      <th>Title</th>
                                      <th>UOM</th>
                                      <th>Bin</th>
                                      <th>Qty Rec</th>
                                      <th> Cost</th>
                                      <th>Total Cost</th>
                                    
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($interBranchTransferItems as $item)
                                        <tr>                                      
                                            <th>
                                                {{$loop->index  + 1}}
                                           
                                            </th>
                                            <td>{{ $item->getInventoryItemDetail?->stock_id_code }}</td>
                                            <td>{{ $item->getInventoryItemDetail?->title }}</td>
                                            <td>{{$item->getInventoryItemDetail?->pack_size->title}}</td>
                                            <td>{{$item->getInventoryItemDetail?->unitofmeasures->title ?? '-'}}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ $item->standard_cost }}</td>
                                            <td>{{ $item->total_cost}}</td>
                                           
                                            {{-- <td><button type="button" class="btn btn-danger btn-sm deleteparent" style="background-color: transparent !important; border:none; color:red !important;"><i class="fas fa-trash" style="color:red;" aria-hidden="true"></i></button></td> --}}
                                            </tr>
                                            @php
                                                $totalQty += $item->quantity;
                                                $totalWeight += ($item->getInventoryItemDetail->gross_weight ?? 0) * $item->quantity;
                                                $totalCost += $item->total_cost;
                                            @endphp
                                        @endforeach

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <tr>
                                                <td colspan="5">
                                                <strong>Grand Total</strong>     
                                                </td>
                                                <td colspan="2">
                                                <strong>{{ $totalQty }}</strong>  
                                                </td>
                                                <td >
                                                <strong> {{manageAmountFormat($totalCost) }}</strong>  
                                            </td>
                                        </tr>
                                    </tfoot>                                  
                                </table>
                                <table>
                                    <tr>
                                        <td width="39%" >
                                            <strong> Total Line Items: {{ $totalItems }}  </strong>     
                                        </td>
                                        <td width="39%" >
                                            <strong>  Total Weight: {{ $totalWeight }}</strong>  
                                        </td>
                                    </tr>
                                </table>
                            </div>

                              <div class="col-md-12">
                              <div class="col-md-6"><span>
                             
                                {{-- @if ($interBranchTransfer->status == "PENDING")
                                <a href="{{route('n-transfers.processReceiveInterBranchTransfer', $interBranchTransfer->transfer_no)}}" class="btn btn-primary btn-sm">Process Receive</a>
                                    
                                @endif --}}
                              </span></div>
                              <div class="col-md-3"></div>
                              <div class="col-md-3"></div>
                              </div>
  
                        </div>
                    </div>

    </section>
  {{-- </form> --}}


    <!-- Modal -->


@endsection

@section('uniquepagestyle')

<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
 <link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">

 <style type="text/css">
   .select2{
    width: 100% !important;
   }
   #note{
    height: 80px !important;
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
position: absolute;
top: 0;
text-align: center;
display: block;
z-index: 999999;
width: 100%;
height: 100%;
background: #000000b8;
display:none;
">
  <div class="loader" id="loader-1"></div>
</div>
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
  <script type="text/javascript">

    $(function () {
      $(".mlselec6t").select2();
     
     
});

</script>
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script>
        $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
    </script>
@endsection


