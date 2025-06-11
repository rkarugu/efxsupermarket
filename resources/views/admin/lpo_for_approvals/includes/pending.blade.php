@include('message')
<div class="col-md-12 no-padding-h table-responsive">
    <table class="table table-bordered table-hover" id="pending_table">
        <thead>
            <tr>
                <th>S.No.</th>
                <th>Order No</th>
                <th>LPO Date</th>
                <th>Change Requested date</th>
                <th>Supplier</th>
                <th>Store Location</th>
                {{-- <th>Department</th> --}}
                <th>LPO Amount</th>
                <th>Status</th>
                <th class="noneedtoshort">Action</th>

                <!--th style = "width:10%" class = "noneedtoshort">Date</th-->

            </tr>
        </thead>
        <tbody>
            @if (isset($lists) && !empty($lists))
                @foreach ($lists as $key => $list)
                    @if ($list->status == 'Pending')
                        <tr>
                            <td>{!! $key + 1 !!}</td>
                            <td>{!! $list->purchaseOrder->purchase_no !!}</td>
                            <td>{!! date('d M Y', strtotime($list->purchaseOrder->created_at)) !!}</td>
                            <td>{!! date('d M Y', strtotime($list->created_at)) !!}</td>
                            <td>{!! @$list->purchaseOrder->getSupplier->name !!}</td>
                            <td>{{ isset($list->purchaseOrder->getBranch) ? $list->purchaseOrder->getBranch->name : '' }}
                            </td>
                            {{-- <td>{{ isset($list->purchaseOrder->getDepartment) ? $list->purchaseOrder->getDepartment->department_name : '' }}
                            </td> --}}
                            <td>{!! @manageAmountFormat($list->purchaseOrder->getRelatedItem->sum('total_cost_with_vat')) !!}</td>
                            <td>{!! $list->status !!}</td>
                            <td class="action_crud">
                                <a href="{{ route($model . '.show', $list->id) }}"><i class="fa fa-eye"></i></a>
                            </td>
                        </tr>
                    @endif
                @endforeach
            @endif
        </tbody>
    </table>
</div>
