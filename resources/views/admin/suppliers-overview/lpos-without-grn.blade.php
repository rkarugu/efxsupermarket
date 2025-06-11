@extends('layouts.admin.admin')

@php
    $user = Auth::user();
@endphp

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> LPOs Older Than 7 Days Without GRNs </h3>
                </div>
            </div>

            <div class="box-body">
                <div>
                    <table class="table table-bordered" id="create_datatable_25">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Order No</th>
                                <th>Order Date</th>
                                <th>Initiated By</th>
                                <th>Supplier</th>
                                <th>Branch</th>
                                <th>Department</th>
                                <th>Total Amount</th>
                                <th>Ageing</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchaseOrders as $i => $purchaseOrder)
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $purchaseOrder->purchase_no }}</td>
                                    <td>{{ $purchaseOrder->purchase_date }}</td>
                                    <td>{{ $purchaseOrder->user->name }}</td>
                                    <td>{{ $purchaseOrder->supplier->name }}</td>
                                    <td>{{ $purchaseOrder->branch->name }}</td>
                                    <td>{{ $purchaseOrder->department->department_name }}</td>
                                    <td>{{ number_format($purchaseOrder->purchase_order_items_sum_total_cost_with_vat) }}</td>
                                    <td>{{ $purchaseOrder->ageing }} days</td>
                                    <td class="action_crud">
                                        @if (isset($user->permissions['purchase-orders___hide']) || $user->role_id == 1)
                                            <span>
                                                <a title="Archive" href="{{ route('purchase-orders.hidepurchaseorder', $purchaseOrder->slug) }}" >
                                                    <i class="fa fa-eye-slash" style="color: red;" aria-hidden="true"></i>
                                                </a>
                                            </span> 
                                        @endif
                                         
            
                                        @if($purchaseOrder->type == 'stock')
                                            <span>
                                                <a title="View" target="_blank" href="{{ route('purchase-orders.show', $purchaseOrder->slug) }}">
                                                    <i class="fa fa-eye" style="font-size: 20px;" aria-hidden="true"></i>
                                                </a>
                                            </span>
                                        @else
                                            <span>
                                                <a title="View" target="_blank" href="{{ route('non-stock-purchase-orders.show', $purchaseOrder->slug) }}">
                                                    <i class="fa fa-eye" style="font-size: 20px;" aria-hidden="true"></i>
                                                </a>
                                            </span>
                                        @endif
            
            
                                        @if($purchaseOrder->status == 'APPROVED')
                                            @if(!$purchaseOrder->sent_to_supplier)
                                                <span>
                                                    <button title="Send To Supplier" data-toggle="modal" data-target="#send-to-supplier-modal" data-backdrop="static"
                                                        data-id="{{ $purchaseOrder->supplier->id }}" data-name="{{ $purchaseOrder->supplier->name }}" data-lpo="{{ $purchaseOrder->purchase_no }}"
                                                        data-slug="{{ $purchaseOrder->slug }}" style="background-color: transparent !important; border: none !important;">
                                                        <i class="fa fa-envelope text-primary"></i>
                                                    </button>
                                                </span>
                                            @endif
            
                                            <span>
                                                <a title="Print" href="javascript:void(0)" onclick="printBill('{!! $purchaseOrder->slug!!}')">
                                                    <i aria-hidden="true" class="fa fa-print" style="font-size: 20px;"></i>
                                                </a>
                                            </span>
            
                                            <span>
                                                <a title="Export To Pdf" target="_blank" href="{{ route('purchase-orders.exportToPdf', $purchaseOrder->slug)}}">
                                                    <i aria-hidden="true" class="fa fa-file-pdf" style="font-size: 20px;"></i>
                                                </a>
                                            </span>
                                        @endif
                                    </td>
                                </tr>                    
                            @endforeach
                        </tbody>
                    </table>
                </div>
            
                <div class="modal fade" id="send-to-supplier-modal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h3 class="box-title"> Send LPO To Supplier </h3>
            
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
            
                            <div class="box-body">
                                <p style="font-size: 16px;"> Are you sure you want to send <span id="lpo-number"></span> to <span id="supplier-name"></span>? </p>
                                <form action="{!! route("purchase-orders.send-to-supplier")  !!} " method="post" id="send-to-supplier-form">
                                    {{ csrf_field() }}
            
                                    <input type="hidden" id="supplier-id" name="supplier_id">
                                    <input type="hidden" id="lpo-slug" name="lpo_slug">
                                </form>
                            </div>
            
                            <div class="box-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" onclick="confirmSendToSupplier();">Yes, Send</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $("body").addClass('sidebar-collapse');

        function confirmSendToSupplier() {
            $("#send-to-supplier-form").submit();
        }

        $('#send-to-supplier-modal').on('show.bs.modal', function (event) {
            let triggeringButton = $(event.relatedTarget);
            let idValue = triggeringButton.data('id');
            let nameValue = triggeringButton.data('name');
            let lpoValue = triggeringButton.data('lpo');
            let lpoSlugValue = triggeringButton.data('slug');

            $("#supplier-id").val(idValue);
            $("#supplier-name").text(nameValue);
            $("#lpo-number").text(lpoValue);
            $("#lpo-slug").val(lpoSlugValue);
        })
    </script>
@endpush
