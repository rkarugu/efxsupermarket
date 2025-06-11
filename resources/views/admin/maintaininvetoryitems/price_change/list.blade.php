@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">

            </div>

            <div class="box-body">
                <form action="" method="get">
                    <div class="form-group col-sm-4">
                        <select name="item" class="form-control destination_items">
                            <option value="" disabled selected></option>
                            @if(request()->item)
                                <option value="{{request()->item}}" selected>{{@\App\Model\WaInventoryItem::find(request()->item)->title}}</option>
                            @endif
                        </select>
                    </div>

                    <div class="form-group col-sm-4">
                        <select name="supplier" class="form-control" id=supplier-id>
                            <option value="" selected disabled></option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @if(request()->supplier == $supplier->id) selected @endif> {{ $supplier->name }} </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary col-sm-2">Filter</button>
                    {{-- <a class="btn btn-info ml-12" href="{!! route('maintain-items.item_price_pending_list') !!}">Clear </a> --}}

                </form>

                @include('message')
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="create_datatable_25">
                        <thead>
                        <tr>
                            <th style="width: 3%;">S.No.</th>
                            <th>Stock ID Code</th>
                            <th>Title</th>
                            <th>Current Cost</th>
                            <th>New Cost</th>
                            <th>Current Price</th>
                            <th>New Price</th>
                            <th>Initiator</th>
                            <th>Date & Time</th>
                            <th>Block?</th>
                            <th class="noneedtoshort">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(isset($list) && !empty($list))
                                <?php $b = 1; ?>
                            @foreach($list as $item)
                                <tr>
                                    <td style="width: 3%;">{!! $b !!}</td>
                                    <td>{!! @$item->item->stock_id_code !!}</td>
                                    <td>{!! @$item->item->title !!}</td>
                                    <td>{!! @$item->old_standard_cost !!}</td>
                                    <td>{!! @$item->standard_cost !!}</td>
                                    <td>{!! @$item->old_selling_price !!}</td>
                                    <td>{!! @$item->selling_price !!}</td>
                                    <td>{!! @$item->creator->name !!}</td>
                                    <td>{!! \Carbon\Carbon::parse($item->created_at)->toDayDateTimeString() !!}</td>
                                    <td>{!! $item->block_this ? 'Yes' : 'No' !!}</td>
                                    <td class="action_crud">
                                        <a data-toggle="modal" href='#modal-id{!! $b !!}'>
                                            <i class="fa fa-check-circle text-success fa-lg" aria-hidden="true"></i>
                                        </a>

                                        <div class="modal fade" id="modal-id{!! $b !!}" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="box box-primary">
                                                    <div class="box-header with-border">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <h3 class="box-title"> Approve Price Change For {{ $item->item?->title }} ({{ $item->item?->stock_id_code }}) </h3>

                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <div class="box-body">
                                                        <form action="{{route('maintain-items.item_price_pending_verify', ['id' => $item->id])}}" method="post" class="submitMe">
                                                            {{ csrf_field() }}

                                                            <input type="hidden" name="id" value="{{ $item->id }}">
                                                            <input type="hidden" name="standard_cost" value="{{ $item->standard_cost }}">
                                                            <input type="hidden" name="old_standard_cost" value="{{ $item->old_standard_cost }}">
                                                            <input type="hidden" name="selling_price" value="{{ $item->selling_price }}">
                                                            <input type="hidden" name="old_selling_price" value="{{ $item->old_selling_price }}">
                                                            <div class="d-flex">
                                                                <div class="d-flex flex-column param">
                                                                    <span class="param-header">Current Cost</span>
                                                                    <span class="param-value">{{ number_format($item->old_standard_cost ?? 0, 2) }}</span>
                                                                </div>

                                                                <div class="d-flex flex-column param">
                                                                    <span class="param-header">New Cost</span>
                                                                    <span class="param-value">{{ number_format($item->standard_cost, 2) }}</span>
                                                                </div>

                                                                <div class="d-flex flex-column param">
                                                                    <span class="param-header">Current Price</span>
                                                                    <span class="param-value">{{ number_format($item->old_selling_price ?? 0, 2) }}</span>
                                                                </div>

                                                                <div class="d-flex flex-column param">
                                                                    <span class="param-header">New Price</span>
                                                                    <span class="param-value">{{ number_format($item->selling_price, 2) }}</span>
                                                                </div>

                                                                <div class="d-flex flex-column param">
                                                                    <span class="param-header">Initiator</span>
                                                                    <span class="param-value">{{ $item->creator->name }}</span>
                                                                </div>
                                                            </div>

                                                            <div class="form-group" style="margin-top: 20px; width: 100%;">
                                                                <label class="control-label" style="display: block; margin-bottom: 4px;">Supplier</label>
                                                                <select name="supplier_id" id="item-supplier" class="form-control" style="width:100%"
                                                                        @if(count($item->suppliers) == 0) readonly @endif>
                                                                    @foreach($item->suppliers as $supplier)
                                                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="form-group" style="width:100%; margin-top: 20px;">
                                                                <label for="">Action</label>
                                                                <select name="status" class="form-control" style="width:100%" id="request-status">
                                                                    <option value="" selected disabled>Select action</option>
                                                                    <option value="Approved" selected>Approve</option>
                                                                    <option value="Rejected">Reject</option>
                                                                </select>
                                                            </div>

                                                            <table class="table table-hover mt-20">
                                                                <tr>
                                                                    <th>Available Quantity</th>
                                                                    <td>{{ $item->qoh }}</td>
                                                                </tr>

                                                                <tr>
                                                                    <th>Current Cost</th>
                                                                    <td>{{ number_format($item->old_standard_cost, 2) }}</td>
                                                                </tr>

                                                                @php
                                                                    $totalValuation = $item->old_standard_cost * $item->qoh;
                                                                @endphp

                                                                <tr>
                                                                    <th>Total Valuation</th>
                                                                    <td>{{ number_format($totalValuation, 2) }}</td>
                                                                </tr>

                                                                <tr>
                                                                    <th>New Cost</th>
                                                                    <td>{{ number_format($item->standard_cost, 2) }}</td>
                                                                </tr>

                                                                @php
                                                                    $totalValuationOnDrop = $item->standard_cost * $item->qoh;
                                                                @endphp

                                                                <tr>
                                                                    <th>Price @if($totalValuationOnDrop < $totalValuation) Drop @else Increase @endif</th>
                                                                    <td>{{ number_format($item->standard_cost - $item->old_standard_cost, 2) }}</td>
                                                                </tr>

                                                                <tr>
                                                                    <th>Valuation On Price @if($totalValuationOnDrop < $totalValuation) Drop @else Increase @endif</th>
                                                                    <td>{{ number_format($totalValuationOnDrop, 2) }}</td>
                                                                </tr>

                                                                @if(($totalValuation - $totalValuationOnDrop) > 0)
                                                                    <tr style="border-top: 2px solid black !important;">
                                                                        <th>Demand</th>
                                                                        <th>{{ number_format(($totalValuation - $totalValuationOnDrop), 2) }}</th>
                                                                    </tr>
                                                                @endif
                                                            </table>

                                                            <div class="box-footer">
                                                                <div class="d-flex justify-content-between">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                                                                    <div>
                                                                        @if(($totalValuation - $totalValuationOnDrop) > 0)
                                                                            <button type="submit" class="btn btn-primary" id="demand-btn">Save & Create Demand To Supplier</button>
                                                                        @else
                                                                            <button type="submit" class="btn btn-primary" id="save-btn">Save</button>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                    <?php $b++; ?>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagescript')
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

    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>

    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('.mlselec6t').select2();

            $("#supplier-id").select2({
                placeholder: 'Select supplier',
                allowClear: true
            });

            var destinated_item = function () {
                $(".destination_items").select2({
                    placeholder: 'Select item',
                    allowClear: true,
                    ajax: {
                        url: "{{route('maintain-items.inventoryDropdown')}}",
                        dataType: 'json',
                        type: "GET",
                        data: function (term) {
                            return {
                                q: term.term
                            };
                        },
                        processResults: function (response) {
                            return {
                                results: response
                            };
                        },
                        cache: true
                    }
                });
            }
            destinated_item();

            // $("#request-status").change(function () {
            //    if (window.difference > 0) {
            //        let status = $("#request-status").val();
            //        if (status === "Approved") {
            //            $("#save-btn").css('display', 'none');
            //            $("#demand-btn").css('display', 'block');
            //        } else {
            //            $("#save-btn").css('display', 'block');
            //            $("#demand-btn").css('display', 'none');
            //        }
            //    }
            // })
        });
    </script>
@endsection

@section('uniquepagestyle')
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>

    <style>
        .param {
            margin-right: 20px;
            font-size: 16px;
        }

        .param-header {
            font-weight: bold;
        }
    </style>
@endsection
