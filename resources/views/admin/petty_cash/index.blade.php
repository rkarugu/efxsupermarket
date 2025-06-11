@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3>
            <a href="{{route($model.'.create')}}"  class="btn btn-primary btn-sm" style="float: right;">Add {{$title}}</a>
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
                         <th>Petty Cash</th>
                         <th>Total Amount</th>
                         <th>Date/Time</th>
                         <th>User</th>
                         <th>Approval Status</th>
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
                         <td>{{$item->petty_cash_no}}</td>
                         <td>{{manageAmountFormat($item->total_amount)}}</td>
                         <td>{{date('d/m/Y H:i A',strtotime($item->created_at))}}</td>
                         <td>{{@$item->user->name}}</td>
                         <td>{{($item->type == 'processed' && count($item->approvals) == 0) ? 'Approved' : 'Pending'}}</td>
                         <td>
                             <a class="btn btn-danger btn-sm" href="{{route('petty-cash.show',base64_encode($item->id))}}" title="Details"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            @if($item->type == 'saved')
                             <a class="btn btn-danger btn-sm" href="{{route('petty-cash.edit',base64_encode($item->id))}}" title="Details"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                            @endif
                             @if($item->type == 'processed' && count($item->approvals) == 0)
                            @if( (isset($permission[$pmodule.'___print']) && isset($permission[$pmodule.'___re-print'])) || $permission == 'superadmin')
                                <a class="btn btn-primary btn-sm printBill" onclick="printBill(this); return false;" href="{{route('petty-cash.print',base64_encode($item->id))}}" title="Print"><i class="fa fa-print" aria-hidden="true"></i></a>
                            @endif 
                            
                            @if((isset($permission[$pmodule.'___print']) && isset($permission[$pmodule.'___re-print'])) || $permission == 'superadmin')
                                <a class="btn btn-warning btn-sm" href="{{route('petty-cash.exportpdf',base64_encode($item->id))}}" title="PDF">
                                    <i class="fa fa-file-pdf" aria-hidden="true"></i>
                                </a>
                            @endif
                            @endif

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
                         <th colspan="4">
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
    function printBill(slug)
    {
        jQuery.ajax({
            url: $(slug).attr('href'),
            type: 'GET',
            async:false,   //NOTE THIS
            headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
            success: function (response) {
            var divContents = response;
            var printWindow = window.open('', '', 'width=600');
            printWindow.document.write(divContents);
            printWindow.document.close();
            printWindow.print();
            printWindow.close();
            }
        });
              
    }
</script>
@endsection