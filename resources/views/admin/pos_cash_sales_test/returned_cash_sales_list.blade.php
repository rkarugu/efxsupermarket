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
             <div class="col-md-4">
                 <div class="form-group">
                   <label for="">From</label>
                   <input type="date" name="start-date" id="start-date" class="form-control" value="{{request()->input('start-date')}}">
                 </div>
             </div>
             <div class="col-md-4">
                 <div class="form-group">
                   <label for="">To</label>
                   <input type="date" name="end-date" id="end-date" class="form-control"  value="{{request()->input('end-date')}}">
                 </div>
             </div>
             <div class="col-md-4">
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
                        <th>Return GRN </th>
                        <th>Cash Sale No.</th>
                        <th>Dated</th>
                        <th>Cashier</th>
                        <th>Total</th>
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
                         <td>{{$item->return_grn}}</td>
                         <td>{{@$item->parent->sales_no}}</td>
                         <td>{{date('d/m/Y H:i',strtotime(@$item->return_date))}}</td>
                         <td>{{@$item->returned_by->name}}</td>
                         <td>{{manageAmountFormat($item->rtn_total)}}</td>
                         <td>
                            <a title="Print" href="javascript:void(0)" onClick="printgrn('{!! $item->return_grn!!}')"><i aria-hidden="true" class="fa fa-print" style="font-size: 20px;" ></i>
                            </a> 
                        </td>
                         @php
                        $total += $item->rtn_total;
                        @endphp
                       
                     </tr>
                     @endforeach
                 </tbody>
                 <tfoot>
                     <tr>
                         <th colspan="5" style="text-align: right">
                             Total
                         </th>
                         <th>
                            {{manageAmountFormat($total)}}
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
    function printgrn(transfer_no)
    {       
        var url = "{{route('pos-cash-sales.returned_cash_sales_print',['id'=>''])}}/"+transfer_no;
        print_this(url);
    }
    
</script>
@endsection