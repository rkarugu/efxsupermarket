@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title" style="font-weight:500 !important;"> Return (Excess) Info </h3>
                    <a href="{{ route("stock-processing.return") }}" role="button" class="btn btn-primary"> Back </a>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="inputEmail3" class="col-sm-5 text-left" style="padding: 0px;">Name</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" class="form-control" value="{{$debtor->debtor->employee->name}}" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="inputEmail3" class="col-sm-5 text-left" style="padding: 0px;">Phone</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" class="form-control" value="{{$debtor->debtor->employee->phone_number}}" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="inputEmail3" class="col-sm-5 text-left" style="padding: 0px;">Role</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" class="form-control" value="{{$debtor->debtor->employee->userRole->title}}" disabled>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="inputEmail3" class="col-sm-5 text-left" style="padding: 0px;">Location</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" class="form-control" value="{{$debtor->debtor->employee->location_stores->location_name}}" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="inputEmail3" class="col-sm-5 text-left" style="padding: 0px;">Branch</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" class="form-control" value="{{$debtor->debtor->employee->userRestaurent->name}}" disabled>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <table class="table table-bordered" id="datatable">
                    <thead>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Document No</th>
                        <th>Quantity</th>
                        <th class="text-right">Price</th>
                        <th>VAT%</th>
                        <th>VAT Amount</th>
                        <th class="text-right">Total</th>
                    </thead>
                    <tbody>
                        @php
                            $total=0;
                            $vatTotal= 0;
                        @endphp
                        @foreach ($debtor->items as $item)
                        <tr>
                            <td>{{$item->inventoryItem->stock_id_code}}</td>
                            <td>{{$item->inventoryItem->title}}</td>
                            <td>{{$item->document_no}}</td>
                            <td>{{$item->quantity}}</td>
                            <td class="text-right">{{ manageAmountFormat(abs($item->price))}}</td>
                            <td>{{$item->inventoryItem->taxManager->tax_value}}</td>
                            <td class="text-right">{{ manageAmountFormat(abs($item->vat))}}</td>
                            <td class="text-right">{{ manageAmountFormat(abs($item->total))}}</td>
                            @php
                                $total += abs($item->total);
                                $vatTotal += $item->vat;
                            @endphp
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                                <td colspan="7" class="text-right"><b>Net Amount</b></td>
                                <td class="text-right"><b>{{ manageAmountFormat($total-$vatTotal) }}</b></td>
                        </tr>
                        <tr>
                                <td colspan="7" class="text-right"><b>Vat</b></td>
                                <td class="text-right"><b>{{ manageAmountFormat($vatTotal) }}</b></td>
                        </tr>
                        <tr>
                                <td colspan="7" class="text-right"><b>Total</b></td>
                                <td class="text-right"><b>{{ manageAmountFormat($total) }}</b></td>
                        </tr>
                        @if (can('resign_esd', 'stock-processing-return') && $esd_status != 'Signed successfully.')
                            <tr>
                                <td colspan="7" class="text-right"><b></b></td>
                                <td class="text-right">
                                    <form action="{{route('stock-processing.sales.resign_esd',$debtor->id)}}" method="POST">
                                        @csrf
                                        <button class="btn btn-primary" type="submit" title="Re-Sign ESD"> Re-Sign ESD </button>
                                    </form> 
                                </td>
                            </tr>
                        @endif
                    </tfoot>
                </table>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

    <script type="text/javascript">
        $(function () {
            $(".select2").select2();
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#datatable').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                'autoWidth': false,
                'pageLength': 100,
                'aoColumnDefs': [{
                    'bSortable': false,
                    'aTargets': 'noneedtoshort'
                }],
            });

        });

    </script>
@endsection
