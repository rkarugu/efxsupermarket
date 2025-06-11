@extends('layouts.admin.admin')
@section('content')
    <script>
        window.supplier = {!! $supplier !!};
        window.terms = {!! $terms !!};
    </script>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> {{ $supplier->name }} ({{ $supplier->supplier_code }}) </h3>
                    <a href="{{ route('maintain-suppliers.index') }}" class="btn btn-primary"> Back to Suppliers </a>
                </div>
            </div>

            <div>
                <div class="box-header with-border">
                    <div class="box-header-flex">
                        <h3 class="box-title"> Summary </h3>

                        <button title="Add Agreement" data-toggle="modal" data-target="#add-agreement-modal" data-backdrop="static" class="btn btn-primary">
                            Add Summary Point
                        </button>
                    </div>
                </div>

                <div class="box-body">
                    <ol id="agreements"></ol>
                </div>
            </div>

            <hr>

            <div>
                <div class="box-header with-border">
                    <div class="box-header-flex">
                        <h3 class="box-title"> Discounts </h3>
                    </div>
                </div>

                <div class="box-body">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Discount Type</th>
                            <th>Target</th>
                            <th>Application Stage</th>
                            <th>Period</th>
                            <th>Discount value</th>
                            <th>Actions</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($discounts as $index => $discount)
                            <tr>
                                <th scope="row" style="width: 3%;">{{ $index + 1 }}</th>
                                <td>{{ $discount['label'] }}</td>
                                <td>{{ $discount['target'] }}</td>
                                <td>{{ $discount['stage'] }}</td>
                                <td>{{ $discount['period'] }}</td>
                                <td>
                                    @if($discount['banded'])
                                        -
                                    @else
                                        {{ $discount['value'] }}
                                    @endif
                                </td>
                                <td>
                                    <div class="action-button-div">
                                        @if(!$discount['banded'])
                                            <a href="javascript:void(0);" title="Update Value"><i class="fas fa-edit"></i></a>
                                        @endif

                                        @if($discount['banded'])
                                            @if($discount['key'] == 'payment_discount')
                                                <a href="javascript:void(0);" title="Discount Bands" data-toggle="modal" data-target="#payment-discount-modal" data-backdrop="static">
                                                    <i class="fas fa-th-list"></i>
                                                </a>
                                            @endif

                                            @if($discount['key'] == 'target_discount_on_qty')
                                                <a href="javascript:void(0);" title="Discount Bands" data-toggle="modal" data-target="#qty-target-discount-modal" data-backdrop="static">
                                                    <i class="fas fa-th-list"></i>
                                                </a>
                                            @endif

                                            @if($discount['key'] == 'target_discount_on_value')
                                                <a href="javascript:void(0);" title="Discount Bands" data-toggle="modal" data-target="#value-target-discount-modal" data-backdrop="static">
                                                    <i class="fas fa-th-list"></i>
                                                </a>
                                            @endif

                                            @if($discount['key'] == 'target_discount_on_total_value')
                                                <a href="javascript:void(0);" title="Discount Bands" data-toggle="modal" data-target="#total-value-target-discount-modal" data-backdrop="static">
                                                    <i class="fas fa-th-list"></i>
                                                </a>
                                            @endif

                                                @if($discount['key'] == 'transport_rebate')
                                                    <a href="javascript:void(0);" title="Discount Bands" data-toggle="modal" data-target="#transport-rebate-discount-modal" data-backdrop="static">
                                                        <i class="fas fa-th-list"></i>
                                                    </a>
                                                @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <hr>

            <div class="box-body">
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Item</th>
                        <th>Cost</th>
                        <th>Base Discount</th>
                        <th>Purchase Qty Offer</th>
                        <th>Qty Discount</th>
                        <th> Rebate Type</th>
                        <th> Rebate Rate</th>
                        <th> Rebate Location</th>
                        <th> QoH</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($supplier_item)>0)
                            <?php $b = 1; ?>
                        @foreach($supplier_item as $list)
                            <tr>
                                <td>{!! $b !!}</td>
                                <td>{!! @$list->inventory_item->title !!}</td>
                                <td>{!! manageAmountFormat($list->price) !!}</td>
                                <td>{!! manageAmountFormat($list->base_amount) !!}</td>
                                <td>20 Get 1</td>
                                <td>60.00</td>
                                <td>{!! $list->rebate_type ?? '-' !!}</td>
                                <td>{!! manageAmountFormat($list->rebate_rate) !!}</td>
                                <td>{!! $list->rebate_location ?? 'N/A' !!}</td>
                                <td>0</td>
                                <td>
                                    @if($list->inventory_item)
                                        <a href="{{route('maintain-items.supplier-stock-movements',$list->inventory_item->stock_id_code)}}">
                                            <i class="fa fa-list" title="Stock Movements" aria-hidden="true"></i>
                                        </a>
                                    @endif

                                    &nbsp;<a data-toggle="modal" href='#modal-id{{$b}}'><i class="fa fa-pen" title="Update" aria-hidden="true"></i></a>
                                    <div class="modal fade" id="modal-id{{$b}}">
                                        <div class="modal-dialog">
                                            <div class="box box-primary">
                                                <div class="box-header with-border">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h3 class="box-title"> Update Trade Agreement For {!! @$list->inventory_item->title !!} </h3>

                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="box-body">
                                                    <form action="{{route($model.'.supplierDataChange')}}" method="post" class="submitMe">
                                                        @csrf
                                                        <input type="hidden" name="data_id" value="{{$list->id}}">

                                                        <div class="form-group">
                                                            <label for="">Current Cost</label>
                                                            <input type="text" class="form-control" value="{{$list->price}}" readonly>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="">New Cost</label>
                                                            <input type="text" class="form-control" value="" name="cost">
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="">Base Discount (%)</label>
                                                            <input type="number" class="form-control" value="{{$list->base_discount}}" name="base_discount">
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="">Invoice Discount (%)</label>
                                                            <input type="number" class="form-control" value="{{$list->invoice_discount}}" name="invoice_discount">
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="">Purchase Quantity</label>
                                                            <input type="number" class="form-control" value="{{$list->purchase_quantity}}" name="purchase_quantity">
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="">Get Offer Quantity</label>
                                                            <input type="number" class="form-control" value="{{$list->offer_quantity}}" name="offer_quantity">
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="">Rebate Type</label>
                                                            <select name="rebate_type" id="rebate_type" class="form-control">
                                                                <option value="" selected disabled> Select rebate type</option>
                                                                @foreach($rebateTypes as $rebateType)
                                                                    <option value="{{ $rebateType['value'] }}"> {{ $rebateType['label'] }} </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="form-group" style="display: none;" id="rebate-location">
                                                            <label for="">Location</label>
                                                            <select name="rebate_location_id" id="rebate_location_id" class="form-control">
                                                                <option value="" selected disabled> Select location</option>
                                                                @foreach($stores as $store)
                                                                    <option value="{{ $store->id}}"> {{ $store->location_name }} </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="">Rebate Value</label>
                                                            <input type="number" class="form-control" value="{{$list->rebate_value}}" name="rebate_value">
                                                        </div>

                                                        <div class="box-footer">
                                                            <div class="d-flex justify-content-between align-content-center">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary"> Update</button>
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

        <div class="modal fade" id="add-agreement-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Add Agreement </h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        <input type="text" class="form-control" id="agreement" placeholder="Trade agreement">
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" onclick="addAgreement();">Add Agreement</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="payment-discount-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Payment Discount Bands </h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-body" id="payment-discount-bands">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="" class="control-label"> Payment period </label>
                                <select name="payment_period" class="form-control payment_period">
                                    @foreach($terms as $term)
                                        <option value="" disabled selected></option>
                                        <option value="{{ $term->id }}"> {{ $term->term_description }} </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="" class="control-label"> Discount (%) </label>
                                <div class="d-flex align-items-center">
                                    <input type="number" name="" id="" class="form-control" value="0">
                                    <button class="btn btn-primary ml-12" title="Remove Band"><strong>-</strong></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary mr-12 mb-12" title="Add Band" onclick="addPaymentDiscountBand()"><strong>+</strong></button>
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary">Update Bands</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="qty-target-discount-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Target Discount On Quantity Bands </h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-body" id="qty-target-discount-bands">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="" class="control-label"> Target Quantity </label>
                                <input type="number" name="" id="" class="form-control">
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="" class="control-label"> Discount (%) </label>
                                <div class="d-flex align-items-center">
                                    <input type="number" name="" id="" class="form-control" value="0">
                                    <button class="btn btn-primary ml-12" title="Remove Band"><strong>-</strong></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary mr-12 mb-12" title="Add Band" onclick="addQtyTargetDiscountBand()"><strong>+</strong></button>
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary">Update Bands</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="value-target-discount-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Target Discount On Product Value Bands </h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-body" id="value-target-discount-bands">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="" class="control-label"> Target Quantity </label>
                                <input type="number" name="" id="" class="form-control">
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="" class="control-label"> Discount (%) </label>
                                <div class="d-flex align-items-center">
                                    <input type="number" name="" id="" class="form-control" value="0">
                                    <button class="btn btn-primary ml-12" title="Remove Band"><strong>-</strong></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary mr-12 mb-12" title="Add Band" onclick="addValueTargetDiscountBand()"><strong>+</strong></button>
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary">Update Bands</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="total-value-target-discount-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Target Discount On Total Value Bands </h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-body" id="total-value-target-discount-bands">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="" class="control-label"> Target Quantity </label>
                                <input type="number" name="" id="" class="form-control">
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="" class="control-label"> Discount (%) </label>
                                <div class="d-flex align-items-center">
                                    <input type="number" name="" id="" class="form-control" value="0">
                                    <button class="btn btn-primary ml-12" title="Remove Band"><strong>-</strong></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary mr-12 mb-12" title="Add Band" onclick="addTotalValueTargetDiscountBand()"><strong>+</strong></button>
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary">Update Bands</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="transport-rebate-discount-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Transport Rebate Discount Bands </h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-body" id="transport-rebate-discount-bands">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="" class="control-label"> Rebate Type </label>
                                <input type="text" name="" id="" class="form-control" value="Per Unit" readonly>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="" class="control-label"> Discount (%) </label>
                                <div class="d-flex align-items-center">
                                    <input type="number" name="" id="" class="form-control" value="0">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="" class="control-label"> Rebate Type </label>
                                <input type="text" name="" id="" class="form-control" value="% of Invoice Amount" readonly>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="" class="control-label"> Discount (%) </label>
                                <div class="d-flex align-items-center">
                                    <input type="number" name="" id="" class="form-control" value="0">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="" class="control-label"> Rebate Type </label>
                                <input type="text" name="" id="" class="form-control" value="Per Tonnage" readonly>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="" class="control-label"> Discount (%) </label>
                                <div class="d-flex align-items-center">
                                    <input type="number" name="" id="" class="form-control" value="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex ml-12">
                        <button class="btn btn-primary mr-12 mb-12">Apply Per Location</button>
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>

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

    <style>
        #agreements {
            list-style-position: inside;
            padding: 0;
            margin: 0;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="{{asset('js/form.js')}}"></script>
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('body').addClass('sidebar-collapse');
            fetchAgreements();

            $("#rebate_type").select2();
            $(".payment_period").select2();

            $("#rebate_type").change(function () {
                $("#rebate-location").css('display', 'none');
                let type = $("#rebate_type").val();
                if (type === 'per_location') {
                    $("#rebate-location").css('display', 'block');
                    $("#rebate_location_id").select2();
                }
            });
        });

        function fetchAgreements() {
            let form = new Form();
            $.ajax({
                url: "/api/trade-agreements",
                type: "get",
                data: {
                    supplier_id: window.supplier.id,
                },
                success: function (response) {
                    if (response.data.length === 0) {
                        return $("#agreements").html('<span> No trade agreements found. </span>');
                    }

                    let string = '';
                    for (let counter = 0; counter < response.data.length; counter++) {
                        string += '<li>' + response.data[counter].agreement + '</li>';
                    }

                    $("#agreements").html(string);
                },
                error: function (response) {
                    form.errorMessage(response.responseJSON?.message ?? response.responseJSON);
                }
            });
        }

        function addAgreement() {
            let agreement = $("#agreement").val();
            let form = new Form();
            if (!agreement) {
                form.errorMessage('Please type a valid agreement');
                return false;
            }

            $.ajax({
                url: "/api/trade-agreements/add",
                type: "post",
                data: {
                    _token: "{{csrf_token()}}",
                    agreement: agreement,
                    supplier_id: window.supplier.id,
                },
                success: function (data) {
                    fetchAgreements();
                    form.successMessage('Agreement added successfully');
                    $("#agreement").val('');
                    $("#add-agreement-modal").modal('hide');
                },
                error: function (response) {
                    form.errorMessage(response.responseJSON?.message ?? response.responseJSON);
                }
            });
        }

        function addPaymentDiscountBand() {
            let band = '<div class="row">' +
                '<div class="col-md-6 form-group">' +
                '<label for="" class="control-label"> Payment period </label>' +
                '<select name="payment_period" class="payment_period form-control">' +
                '<option value="" disabled selected></option>';

            let string = '';
            let terms = window.terms;
            for (let counter = 0; counter < terms.length; counter++) {
                string += '<option value="' + terms[counter].id + '">' + terms[counter].term_description + '</option>';
            }

            band += string;
            band += '</select>' +
                '</div>' +
                '<div class="col-md-6 form-group">' +
                '<label for="" class="control-label"> Discount (%) </label>' +
                '<div class="d-flex align-items-center">' +
                '<input type="number" name="" id="" class="form-control" value="0">' +
                '<button class="btn btn-primary ml-12" title="Remove Band"><strong>-</strong></button>' +
                '</div>' +
                '</div>' +
                '</div>';

            $("#payment-discount-bands").append(band);
            $(".payment_period").select2('destroy');
            $(".payment_period").select2();
        }

        function addQtyTargetDiscountBand() {
            let band = '<div class="row">' +
                '<div class="col-md-6 form-group">' +
                '<label for="" class="control-label"> Target Quantity </label>' +
                '<input type="number" name="" id="" class="form-control">' +
                '</div>' +
                '<div class="col-md-6 form-group">' +
                '<label for="" class="control-label"> Discount (%) </label>' +
                '<div class="d-flex align-items-center">' +
                '<input type="number" name="" id="" class="form-control" value="0">' +
                '<button class="btn btn-primary ml-12" title="Remove Band"><strong>-</strong></button>' +
                '</div>' +
                '</div>' +
                '</div>';

            $("#qty-target-discount-bands").append(band);
        }

        function addValueTargetDiscountBand() {
            let band = '<div class="row">' +
                '<div class="col-md-6 form-group">' +
                '<label for="" class="control-label"> Target Value </label>' +
                '<input type="number" name="" id="" class="form-control">' +
                '</div>' +
                '<div class="col-md-6 form-group">' +
                '<label for="" class="control-label"> Discount (%) </label>' +
                '<div class="d-flex align-items-center">' +
                '<input type="number" name="" id="" class="form-control" value="0">' +
                '<button class="btn btn-primary ml-12" title="Remove Band"><strong>-</strong></button>' +
                '</div>' +
                '</div>' +
                '</div>';

            $("#value-target-discount-bands").append(band);
        }

        function addTotalValueTargetDiscountBand() {
            let band = '<div class="row">' +
                '<div class="col-md-6 form-group">' +
                '<label for="" class="control-label"> Target Value </label>' +
                '<input type="number" name="" id="" class="form-control">' +
                '</div>' +
                '<div class="col-md-6 form-group">' +
                '<label for="" class="control-label"> Discount (%) </label>' +
                '<div class="d-flex align-items-center">' +
                '<input type="number" name="" id="" class="form-control" value="0">' +
                '<button class="btn btn-primary ml-12" title="Remove Band"><strong>-</strong></button>' +
                '</div>' +
                '</div>' +
                '</div>';

            $("#total-value-target-discount-bands").append(band);
        }
    </script>
@endsection

