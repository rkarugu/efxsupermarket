@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3>
        </div>
         @include('message')
         <div class="box-body" style="padding:15px">
         <br>
         <form action="" method="get">
         <div class="row">
             <div class="col-md-3">
                 <div class="form-group">
                   <label for="">From</label>
                   <input type="date" name="start-date" id="start-date" class="form-control" value="{{request()->input('start-date')}}">
                 </div>
             </div>
             <div class="col-md-3">
                 <div class="form-group">
                   <label for="">To</label>
                   <input type="date" name="end-date" id="end-date" class="form-control"  value="{{request()->input('end-date')}}">
                 </div>
             </div>
             <div class="col-md-3">
                 <div class="form-group">
                   <label for="">Stage</label>
                   
                   <select name="stage" class="form-control" id="stage">
                    <option value="1" {{request()->stage == 1 ? 'selected' : ''}}>Approval Stage 1</option>
                    <option value="2" {{request()->stage == 2 ? 'selected' : ''}}>Approval Stage 2</option>
                   </select>
                 </div>
             </div>
             <div class="col-md-3">
                 <div class="form-group">
                   <button type="submit" class="btn btn-primary btn-sm" style="margin-top: 25px;">Filter</button>
                 </div>
             </div>
         </div>
         </form>
         <div class="table-responsive">
             <br>
             <table class="table table-hover table-bordered table-invert">
                 <thead>
                     <tr>
                         <th>Sr. No.</th>
                         <th>Petty Cash</th>
                         <th>Total Amount</th>
                         <th>Requested At</th>
                         <th>Requested User</th>
                         <th>Stage</th>
                         <th>Action</th>
                     </tr>
                 </thead>
                 <tbody>
                     @php
                     $total = 0;
                     @endphp
                     @foreach($data as $key => $item)
                     <tr>
                         <td>{{ $key+1}}</td>
                         <td>{{@$item->petty_cash->petty_cash_no}}</td>
                         <td>{{manageAmountFormat(@$item->petty_cash->total_amount)}}</td>
                         <td>{{date('d/m/Y H:i A',strtotime(@$item->created_at))}}</td>
                         <td>{{@$item->petty_cash->user->name}}</td>
                         <td>Approval Stage {{@$item->stage}}</td>
                         <td>
                             <a href="{{route('petty-cash.pending_approval_show',['id'=>base64_encode($item->id)])}}"><i class="fa fa-eye"></i></a>

                         </td>
                         @php
                        $total += @$item->total_amount;
                        @endphp
                     </tr>
                     @endforeach
                 </tbody>
                 <tfoot>
                     <tr>
                         <th colspan="2" style="text-align: right">
                             Total
                         </th>
                         <th colspan="3">
                            {{manageAmountFormat($total)}}
                         </th>
                         <th colspan="2" style="text-align: right">
                             
                         </th>
                     </tr>
                 </tfoot>
             </table>
             <div>
                 {{$data->appends($_GET)->links()}}
             </div>
         </div>
         </div>
    </div>
</section>
@endsection
@section('uniquepagescript')
<script>
   
</script>
@endsection