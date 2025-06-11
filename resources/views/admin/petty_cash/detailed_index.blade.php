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
         <form action="{{route('petty-cash-detailed')}}" method="get">
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
             @if (isset($permission[$pmodule . '___view-all']) || $permission == 'superadmin')
             <div class="col-md-3">
                 <div class="form-group">
                   <label for="">Salesman</label>
                   <select name="location" class="form-control mlselect">
                       <option value="-1" selected>Show All</option>
                    @foreach($location_data as $key => $row)
                        <option value="{{$row->id}}" @if(request()->location && request()->location == $row->id) selected @endif>{{$row->location_name.' ('.$row->location_code.')'}}</option>
                    @endforeach
                   </select>
                 </div>
             </div>
             @endif
             <div class="col-md-3">
                 <div class="form-group">
                   <button type="submit" class="btn btn-primary btn-sm" style="margin-top: 25px;">Filter</button>
                   <!-- <button type="submit" class="btn btn-primary btn-sm" name="pdf" value="1" style="margin-top: 25px;">PDF</button> -->
                   <button type="submit" class="btn btn-primary btn-sm" name="excel" value="1" style="margin-top: 25px;">Excel</button>
                 </div>
             </div>
         </div>
         </form>
         <div class="table-responsive">
             <br>
             <table class="table table-hover table-bordered table-invert">
                 <thead>
                     <tr>
                         <th>Petty Cash</th>
                         <th>Date/Time</th>
                         <th>Posted By</th>
                         <th>Account No.</th>
                         <th>Payment For</th>
                         <th>Collected By</th>
                         <th>Amount</th>
                     </tr>
                 </thead>
                 <tbody>
                     @php
                     $total = 0;
                     @endphp
                     @foreach($data as $key => $item)
                     <tr>
                         <td>{{@$item->parent->petty_cash_no}}</td>
                         <td>{{date('d/m/Y H:i A',strtotime($item->parent->created_at))}}</td>
                         <td>{{@$item->parent->user->name}}</td>

                         <td>{{@$item->chart_of_account->account_name}}</td>
                         <td>{{$item->payment_for}}</td>
                         <td>{{$item->collected_by}}</td>

                         <td>{{manageAmountFormat($item->amount)}}</td>
                         
                        @php
                        $total += @$item->amount;
                        @endphp
                     </tr>
                     @endforeach
                 </tbody>
                 <tfoot>
                     <tr>
                         <th colspan="3" style="text-align: right">
                             
                         </th>
                         <th colspan="4" style="text-align: right">
                            Total : {{manageAmountFormat($total)}}
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
@section('uniquepagestyle')

 <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
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
<script type="text/javascript">
    $(function () {   
        $(".mlselect").select2();
    });
</script>
 
@endsection