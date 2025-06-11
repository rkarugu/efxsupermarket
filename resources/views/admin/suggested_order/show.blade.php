@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                @include('message')
                <div class="d-flex justify-content-between">
                    <h4 class="box-title">{{$title}}</h4>
                    
                </div>
            </div>
            <div class="box-header with-border">
               <br>
                    <div class="row">
                        <div class="col-sm-10">
                            <div class="form-group col-sm-12">
                                <label>Order No: </label>
                                <span >{{$order->order_number}}</span>
                            </div>
                            <div class="form-group col-sm-12">
                                <label>Order Date: </label>
                                <span >{{date('d M Y',strtotime($order->order_date))}}</span>
                            </div>

                            <div class="form-group col-sm-12">
                                <label>Supplier: </label>
                                <span >{{$order->getSupplier->name}}</span>
                            </div>
                            
                        </div>
                        @if ($order->status == 'Pending')
                        <div class="col-sm-2">
                            <button data-toggle="modal" data-target="#modelId" class="btn btn-primary" value="Filter">
                                Process
                            </button>
                        </div>
                        @endif
                    </div>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                <form action="{{route('suggested-order.update',$order->id)}}" method="post" class="submitMe">
                    @csrf
                    @method('PUT')
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Process the Suggested Order</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="form-group col-sm-4">
                                        <label>Order No: </label>
                                        <span >{{$order->order_number}}</span>
                                    </div>
                                    <div class="form-group col-sm-4">
                                        <label>Order Date: </label>
                                        <span >{{date('d M Y',strtotime($order->order_date))}}</span>
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label>Supplier: </label>
                                        <span >{{$order->getSupplier->name}}</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                <label for="">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="" selected disabled>-- Select Reason --</option>
                                    <option value="Accepted">Accept</option>
                                    <option value="Rejected">Reject</option>
                                </select>
                                </div>
                                <div class="form-group" id="reject_reason" style="display: none">
                                <label for="">Reject Reason</label>
                                <textarea name="reject_reason"  cols="30" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" >
                        <thead>
                            <tr>
                                <th >Item</th>
                                <th >Unit Price</th>
                                <th >Quantity</th>
                                <th >QOO</th>
                                <th >Max Stock</th>
                                <th >Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($order->items) && !empty($order->items))
                                @foreach ($order->items as $item)
                                    <tr >
                                        <td>{!! $item->inventory_item->title !!}</td>
                                        <td>{!! $item->inventory_item->standard_cost !!}</td>
                                        <td>{!! manageAmountFormat($item->quantity) !!}</td>
                                        <td>{!! manageAmountFormat($item->qoo) !!}</td>
                                        <td>{{ manageAmountFormat($item->inventory_item->max_stock) }}
                                        </td>
                                        <td>
                                            {{ manageAmountFormat($item->inventory_item->standard_cost * $item->quantity) }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection


@push('scripts')
<div id="loader-on"
style="
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
<script src="{{ asset('js/sweetalert.js') }}"></script>
<script src="{{ asset('js/form.js') }}"></script>
<script>
    $('#status').change(function(e){
        if($(this).val() == 'Rejected'){
            $('#reject_reason').show();
        }else{
            $('#reject_reason').hide();
        }
    })
</script>
@endpush

