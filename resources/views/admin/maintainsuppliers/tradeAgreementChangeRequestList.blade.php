
@extends('layouts.admin.admin')
@section('content')

<style>
    .span-action {

    display: inline-block;
    margin: 0 3px;

}
</style>

<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"> Trade Agreements Price Change Requets</h3>
            
        </div>
        <div class="box-body">
        @include('message')
        <form action="" method="GET" role="form">
            <div class="row">
                <div class="col-md-3 ">
                    <div class="form-group">
                        <select class="form-control mlselec6t supplier" name="supplier" id="supplier">
                            <option selected value="" disabled> - Select Supplier -</option>
                            @foreach ($supplierList as $item)
                            <option value="{{$item->id}}" @if(isset(request()->supplier) && request()->supplier==$item->id) selected @endif>{{$item->name}}({{$item->supplier_code}})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <select name="status" id="inputstatus" class="form-control" required="required">
                            <option value="Pending" {{(request()->status ?? 'Pending') == 'Pending' ? 'selected' : ''}}>Pending</option>
                            <option value="Approved" {{(request()->status ?? 'Pending') == 'Approved' ? 'selected' : ''}}>Approved</option>
                            <option value="Rejected" {{(request()->status ?? 'Pending') == 'Rejected' ? 'selected' : ''}}>Rejected</option>
                        </select>
                        
                    </div>
                </div>
                <div class="col-sm-3">
                <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
            <div class="col-md-12 no-padding-h">
        <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th >S.No.</th>                                       
                            <th   >Item</th>
                            <th   >Supplier</th>
                            <th   >Current Cost</th>
                            <th   >New Cost</th>
                            <th   >Discount Amount</th>
                            <th   >Transport Rebate Amount</th>
                            <th   >Initiator</th>
                            <th   >AT</th>
                            @if(in_array((request()->status ?? 'Pending'),['Approved','Rejected']))
                            <th   >Approver</th>
                            <th   >AT</th>
                            @endif
                            <th   >Status</th>
                            <th   >Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            @if(count($supplier_change_requests_items)>0)
                                <?php $b = 1;?>
                                @foreach($supplier_change_requests_items as $list)                                         
                                    <tr>
                                        <td>{!! $b !!}</td>
                                        <td>{!! @$list->item_data->inventory_item->title !!}</td>
                                        <td>{!! @$list->item_data->supplier->name !!}</td>
                                        <td>{!! manageAmountFormat(@$list->item_data->price ?? 0) !!}</td>
                                        <td>{!! manageAmountFormat($list->price) !!}</td>
                                        <td>{!! $list->discount_amount !!}</td>
                                        <td>{!! manageAmountFormat($list->transport_rebate_amount) !!}</td>
                                        <td>{!! @$list->initiator->name !!}</td>
                                        <td>{!! $list->initiated_at !!}</td>
                                        @if(in_array((request()->status ?? 'Pending'),['Approved','Rejected']))
                                        <td>{!! @$list->approver->name !!}</td>
                                        <td>{!! $list->approved_at !!}</td>
                                        @endif
                                        <td>{{ $list->status }}</td>
                                        <td>
                                            <a data-toggle="modal" href='#modal-id{{$b}}'><i class="fa fa-eye" title="Edit Price" aria-hidden="true"></i></a>
                                            <div class="modal fade" id="modal-id{{$b}}">
                                                <div class="modal-dialog">
                                                    <form action="{{route('maintain-suppliers.supplierRequestDataApprove')}}" method="post" class="submitMe">
                                                        @csrf
                                                        <input type="hidden" name="data_id" value="{{$list->id}}">
                                                        <input type="hidden" name="approval_check" class="approval_check{{$b}}">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Request of Trade Agreement For Item: {!! @$list->item_data->inventory_item->title !!}</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="">Current Cost</label>
                                                                <input type="text" class="form-control" value="{{@$list->item_data->price}}" readonly>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="">New Cost</label>
                                                                <input type="text" class="form-control" value="{{$list->price}}" name="cost" readonly>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="">Discount Amount</label>
                                                                <input type="text" class="form-control" value="{{$list->discount_amount}}" name="discount_amount" readonly>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="">Transport Rebate Amount</label>
                                                                <input type="text" class="form-control" value="{{$list->transport_rebate_amount}}" readonly name="transport_rebate_amount">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <input type="submit" value="Reject" class="btn btn-danger" onclick="$('.approval_check{{$b}}').val('Reject')">
                                                            <input type="submit" value="Approve" class="btn btn-danger" onclick="$('.approval_check{{$b}}').val('Approve')">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>
                                            
                                        </td>
                                        
                                    </tr>
                                <?php $b++; ?>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
        
    </div></div>
    </div>
</section>

@endsection

@section('uniquepagestyle')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
<style type="text/css">
   .select2{
     width: 100% !important;
    }
    </style>
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
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
<script src="{{asset('js/sweetalert.js')}}"></script>
<script>
    $(function () {
        $(".mlselec6t").select2();
    });
  
</script>

@endsection

