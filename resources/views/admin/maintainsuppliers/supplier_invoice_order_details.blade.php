@extends('layouts.admin.admin')
@section('content')
    <form action="{{ route('maintain-suppliers.supplier_invoice_process') }}" method="post" class="addExpense">
        <section class="content">
            <div class="box box-primary">
                <div class="box-header with-border  no-padding-h-b">
                    <h3 class="box-title"> {!! $title !!} </h3>
                    @include('message')

                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{ $invoices->id }}">
                    <input type="hidden" name="supplier" value="{{ $supplierList->id }}">
                    <div class="row">
                        <div class="col-md-3 no-padding-h ">
                            <div class="form-group">
                                <label for="">Supplier</label>
                                <span class="form-control">{{ $supplierList->name }} -
                                    {{ $supplierList->supplier_code }}</span>
                            </div>
                        </div>
                        <div class="col-md-3 no-padding-h ">
                            <div class="form-group">
                                <label for="">Order No</label>
                                <span class="form-control">{{ $invoices->purchase_no }}</span>
                            </div>
                        </div>
                        <div class="col-md-3 no-padding-h ">
                            <div class="form-group">
                                <label for="">Order Date</label>
                                <span class="form-control">{{ $invoices->purchase_date }}</span>
                            </div>
                        </div>
                        <div class="col-md-3 no-padding-h ">
                            <div class="form-group">
                                <label for="">GRN Number</label>
                                <span class="form-control">{{ $invoices->grn_number }}</span>
                            </div>
                        </div>
                        <div class="col-md-3 no-padding-h ">
                            <div class="form-group">
                                <label for="">Delivery Note</label>
                                <span class="form-control">{{ $invoices->receive_note_doc_no }}</span>
                            </div>
                        </div>
                        <div class="col-md-3 no-padding-h ">
                            <div class="form-group">
                                <label for="">CU Invoice No</label>
                                <input type="text" class="form-control" name="cu_invoice_number"
                                    value="{{ $invoices->cu_invoice_number }}">
                            </div>
                        </div>

                        <div class="col-md-3 no-padding-h ">
                            <div class="form-group">
                                <label for="">Store Location</label>
                                <span class="form-control">{{ @$invoices->getStoreLocation->location_name }}</span>
                            </div>
                        </div>
                        <div class="col-md-3 no-padding-h ">
                            <div class="form-group">
                                <label for="">Branch</label>
                                <span class="form-control">{{ @$invoices->getBranch->name }}</span>
                            </div>
                        </div>

                        <div class="col-md-3 no-padding-h ">
                            <div class="form-group">
                                <label for="">Supplier Invoice Number *</label>
                                <input type="text" class="form-control" name="supplier_invoice_number"
                                    value="{{ $invoices->supplier_invoice_no }}">
                            </div>
                        </div>
                        <div class="col-md-3 no-padding-h ">
                            <div class="form-group">
                                <label for="">Supplier Invoice Date *</label>
                                <input type="date" class="form-control" name="supplier_invoice_date" min="{{ now()->subYear()->format('Y-m-d') }}">
                            </div>
                        </div>
                        @php
                            $files = (array) json_decode($invoices->documents);
                        @endphp
                        @if (count($files) > 0)
                            <div class="col-md-3 no-padding-h ">
                                <div class="form-group">
                                    <a data-toggle="modal" href='#modal-id{{ $invoices->id }}' style="margin-top: 23px;"
                                        class="btn btn-biz-pinkish">Download Documents</a>

                                    <div class="modal fade" id="modal-id{{ $invoices->id }}">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-hidden="true">&times;</button>
                                                    <h4 class="modal-title">Download Documents</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <table class="table table-hover">


                                                        @foreach ($files as $key => $val)
                                                            <tr>
                                                                <th>
                                                                    {{ strtoupper(str_replace('_', ' ', $key)) }}
                                                                </th>
                                                                <td>
                                                                    <a target="_blank"
                                                                        href="{{ asset('uploads/purchases_docs/' . @$val) }}">Download</a>
                                                                </td>
                                                            </tr>
                                                        @endforeach

                                                    </table>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default"
                                                        data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>

                </div>
            </div>

            <!-- Small boxes (Stat box) -->
            @if (count($invoices->getRelatedItem) > 0)
                <div class="box box-primary" id="invoices">
                    <div class="box-header with-border">
                        <h3 class="box-title">Items </h3>
                    </div>
                    <div class="box-body">
                        <input type="hidden" name="grn_number" value="{{ request()->grn }}">
                        @error('grn_number')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                        <table class="table table-bordered table-hover" id="mainItemTable">
                            <tr>
                                <th>Item</th>
                                <th>Description</th>
                                <th style="width: 140px;">QTY</th>
                                <th>Incl. Price</th>
                                <th>Location</th>
                                <th>VAT%</th>
                                {{-- <th style="width: 90px;">Disc%</th> --}}
                                {{-- <th style="width: 90px;">Discount</th> --}}
                                <th>Exclusive</th>
                                <th>VAT</th>
                                <th>Total</th>
                            </tr>
                            @php
                                $total_total = $total_exclusive = $total_vat = $total_discount = 0;
                            @endphp
                            @foreach ($invoices->getRelatedItem as $b => $item)
                                @if ($item->qty_received > 0)
                                    <tr>

                                        <td>{{ $item->item_no }}</td>
                                        <td>{{ @$item->getInventoryItemDetail->description }}</td>
                                        <td>
                                            @if (!isset($permission['suppliers-invoice___edit']) && $permission != 'superadmin')
                                                <input type="hidden" value="{{ $item->qty_received }}"
                                                    name="quantity[{{ $item->id }}]">
                                                {{ $item->qty_received }}
                                            @else
                                                <input type="number" onkeyup="getTotal(this)"
                                                    name="quantity[{{ $item->id }}]" step="any"
                                                    value="{{ $item->qty_received }}" class="form-control quantity">
                                            @endif
                                        </td>
                                        <td>
                                            @if (!isset($permission['suppliers-invoice___edit']) && $permission != 'superadmin')
                                                {{-- {{$item->order_price}} --}}
                                                {{ json_decode($item->invoice_info)->order_price }}
                                                {{-- <input type="hidden" value="{{$item->order_price}}" name="price[{{$item->id}}]"> --}}
                                                <input type="hidden"
                                                    value="{{ json_decode($item->invoice_info)->order_price }}"
                                                    name="price[{{ $item->id }}]">
                                            @else
                                                {{-- <input type="text" onkeyup="getTotal(this)" name="price[{{$item->id}}]" value="{{$item->order_price}}" class="form-control standard_cost"></td> --}}
                                                <input type="number" onkeyup="getTotal(this)"
                                                    name="price[{{ $item->id }}]" step="any"
                                                    value="{{ json_decode($item->invoice_info)->order_price }}"
                                                    class="form-control standard_cost">
                                            @endif
                                        </td>
                                        {{-- <td>{{@$item->location->location_name}}</td> --}}
                                        <td>{{ @$invoices->getStoreLocation->location_name }}</td>
                                        <td>
                                            <select class="form-control vat_taxes">
                                                <option value="">Select Option</option>
                                                @foreach ($vatTaxes as $vatTax)
                                                    <option value="{{ $vatTax->tax_value }}"
                                                        {{ $vatTax->tax_value == $item->vat_rate ? 'selected' : '' }}>
                                                        {{ $vatTax->text }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" value="{{ $item->vat_rate }}"
                                                name="vat_rate[{{ $item->id }}]" class="vat_percentage">
                                        </td>
                                        {{-- <td class="">
                                            @if (!isset($permission['suppliers-invoice___edit']) && $permission != 'superadmin')
                                                <span class="show_discount_per">{{ $item->discount_percentage }}</span>
                                                <input type="hidden" value="{{ $item->discount_percentage }}"
                                                    name="discount[{{ $item->id }}]" class="discount_per">
                                            @else
                                                <input type="number" name="discount[{{ $item->id }}]"
                                                    value="{{ $item->discount_percentage }}"
                                                    class="form-control discount_per" onkeyup="getTotal(this);"
                                                    step="any">
                                            @endif
                                        </td>
                                        </td> --}}
                                        {{-- <td>
                                            @if (!isset($permission['suppliers-invoice___edit']) && $permission != 'superadmin')
                                                <span class="show_discount">{{ $item->discount_amount }}</span>
                                                <input type="hidden" value="{{ $item->discount_amount }}"
                                                    name="discount[{{ $item->id }}]" class="discount">
                                            @else
                                                <input type="number" name="discount[{{ $item->id }}]"
                                                    value="{{ $item->discount_amount }}" class="form-control discount"
                                                    onhange="discount();" step="any">
                                            @endif --}}
                                        @php
                                            // $total = round((($item->qty_received * $item->order_price) - $item->discount_amount),2);
                                            $invoice_info = json_decode($item->invoice_info);
                                            $discount =  isset($invoice_info->total_discount) ?  $invoice_info->total_discount : 0;
                                            $total = round($item->qty_received * (float) $invoice_info->order_price - $discount, 2);

                                            $vat = round($total - ($total * 100) / ($item->vat_rate + 100), 2);
                                            $exclusive = round($total - $vat, 2);

                                        @endphp
                                        <td class="exclusive">{{ manageAmountFormat($exclusive) }}</td>
                                        <td class="vat">{{ manageAmountFormat($vat) }}</td>
                                        <td class="total">
                                            <div class="form-group">
                                                <input type="number" class="form-control"
                                                    name="total[{{ $item->id }}]" onkeyup="reverseCalculate(this)"
                                                    onchange="reverseCalculate(this)" value="{{ $total }}"
                                                    step="any">
                                            </div>
                                        </td>
                                        @php
                                            $total_total += $total;
                                            $total_exclusive += $exclusive;
                                            $total_vat += $vat;
                                            $total_discount += $discount;
                                        @endphp
                                    </tr>
                                @endif
                            @endforeach
                            <tr>
                                <th colspan="3">GRN Details</th>
                                <th colspan="5" style="text-align:right">
                                    Total Exclusive
                                </th>
                                <td colspan="2">KES <span
                                        id="total_exclusive">{{ manageAmountFormat($total_exclusive) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th colspan="2">VAT</th>
                                <td>KES {{ manageAmountFormat($grn->vat_amount) }}</td>
                                <th colspan="5" style="text-align:right">
                                    Total VAT
                                </th>
                                <td colspan="2">KES <span
                                        id="total_vat">{{ manageAmountFormat($total_vat) }}</span>
                                </td>
                            </tr>
                            @php
                                $roundOff = fmod($total_total, 1); //0.25
                                if ($roundOff != 0) {
                                    if ($roundOff > '0.50') {
                                        $roundOff = '+' . round(1 - $roundOff, 2);
                                    } else {
                                        $roundOff = '-' . round($roundOff, 2);
                                    }
                                    //$total_total += $roundOff;
                                }
                            @endphp
                            {{-- <tr>
                                <th colspan="10" style="text-align:right">
                                    Round Off
                                </th>
                                <td colspan="2">KES <span id="round_off_total">{{ $roundOff }}</span></td>
                            </tr> --}}
                            <tr>
                                <th colspan="2">Total</th>
                                <td>KES {{ manageAmountFormat($grn->total_amount) }}</td>
                                <th colspan="5" style="text-align:right">
                                    Total Discount
                                </th>
                                <td colspan="2">KES <span
                                        id="total_discount">{{ manageAmountFormat($total_discount) }}</span></td>
                            </tr>
                            <tr>
                                <th colspan="2"></th>
                                <td></td>
                                <th colspan="5" style="text-align:right">
                                    Total
                                </th>
                                <td colspan="2">KES <span
                                        id="total_total">{{ manageAmountFormat($total_total) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th colspan="12" style="text-align:center">
                                    <button type="submit" class="btn btn-primary btn-sm">Process Invoice</button>
                                </th>
                            </tr>
                        </table>
                    </div>
                </div>
            @endif
            <div class="box box-primary" id="invoices">
                <div class="box-header with-border">
                    <h3 class="box-title"> Stock Movements </h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-hover" id="">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Desc</th>
                                <th>Date</th>
                                <th>User Name</th>
                                <th>Store Location</th>
                                <th>Qty In</th>
                                <th>Qty Out</th>
                                <th>QOH</th>
                                <th>Selling Price</th>
                                <th>Reference</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($wa_stock_moves) && !empty($wa_stock_moves))
                                <?php $b = 1; ?>
                                @foreach ($wa_stock_moves as $list)
                                    <tr>
                                        <td>{!! ucfirst(@$list->getInventoryItemDetail->stock_id_code) !!}</td>
                                        <td>{!! ucfirst(@$list->getInventoryItemDetail->title) !!}</td>
                                        <td>{!! date('d/M/Y', strtotime(@$list->created_at)) !!}</td>
                                        <td>{!! ucfirst(@$list->getRelatedUser->name) !!}</td>
                                        <td>{!! isset($list->getLocationOfStore->location_name) ? ucfirst($list->getLocationOfStore->location_name) : '' !!}</td>
                                        <td>{!! $list->qauntity >= 0 ? +$list->qauntity : null !!}</td>
                                        <td>{!! $list->qauntity < 0 ? -$list->qauntity : null !!}</td>
                                        <td>{!! @$list->new_qoh !!}</td>
                                        <td>{!! manageAmountFormat(@$list->selling_price) !!}</td>
                                        <td>{!! @$list->refrence !!}</td>
                                    </tr>
                                    <?php $b++; ?>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </form>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">

    <style type="text/css">
        .select2 {
            width: 100% !important;
        }

        #note {
            height: 60px !important;
        }

        .align_float_right {
            text-align: right;
        }

        .textData table tr:hover {
            background: #000 !important;
            color: white !important;
            cursor: pointer !important;
        }
    </style>
@endsection

@section('uniquepagescript')
    <div id="loader-on"
        style="
position: absolute;
top: 0;
text-align: center;
display: block;
z-index: 999999;
width: 100%;
height: 100%;
background: #000000b8;
display:none;
">
        <div class="loader" id="loader-1"></div>
    </div>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        var form = new Form();

        $('.addExpense').on('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault(); // Prevent the form from submitting
            }
        });

        $(document).on('submit', '.addExpense', function(e) {
            e.preventDefault();

            $('#loader-on').show();
            var postData = new FormData($(this)[0]);
            var url = $(this).attr('action');
            postData.append('_token', $(document).find('input[name="_token"]').val());
            $.ajax({
                url: url,
                data: postData,
                contentType: false,
                cache: false,
                processData: false,
                method: 'POST',
                success: function(out) {
                    $('#loader-on').hide();

                    $(".remove_error").remove();
                    if (out.result == 0) {
                        for (let i in out.errors) {
                            var id = i.split(".");
                            if (id && id[1]) {
                                $("[name='" + id[0] + "[" + id[1] + "]']").parent().append(
                                    '<label class="error d-block remove_error w-100" id="' + i +
                                    '_error">' + out.errors[i][0] + '</label>');
                            } else {
                                $("[name='" + i + "']").parent().append(
                                    '<label class="error d-block remove_error w-100" id="' + i +
                                    '_error">' + out.errors[i][0] + '</label>');
                                $("." + i).parent().append(
                                    '<label class="error d-block remove_error w-100" id="' + i +
                                    '_error">' + out.errors[i][0] + '</label>');
                            }
                        }
                    }
                    if (out.result === 1) {
                        form.successMessage(out.message);
                        if (out.location) {
                            setTimeout(() => {
                                location.href = out.location;
                            }, 1000);
                        }
                    }
                    if (out.result === -1) {
                        form.errorMessage(out.message);
                    }
                },

                error: function(err) {
                    $('#loader-on').hide();
                    $(".remove_error").remove();
                    form.errorMessage('Something went wrong');
                }
            });
        });

        $(document).ready(function() {
            $('body').addClass('sidebar-collapse');
        });
        $(function() {
            $(".mlselec6t, .vat_taxes").select2();

            $(".vat_taxes").on('select2:select', function(e) {
                $(this).parents('tr').find('.vat_percentage').val($(e.currentTarget).val())
                getTotal($(this));
            })
        });
    </script>
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });

        function getTotal(vara) {
            var price = $(vara).parents('tr').find('.standard_cost').val();
            var quantity = $(vara).parents('tr').find('.quantity').val();
            // var discount_per = $(vara).parents('tr').find('.discount_per').val();
            var vat_percentage = $(vara).parents('tr').find('.vat_percentage').val();
            // var discount = ((parseFloat(price) * parseFloat(quantity)) * parseFloat(discount_per)) / 100;
            // var exclusive = ((parseFloat(price) * parseFloat(quantity)) - parseFloat(discount));
            var total = parseFloat(price) * parseFloat(quantity);
            var vat = parseFloat(total) - ((parseFloat(total) * 100) / (parseFloat(vat_percentage) + 100));
            exclusive = parseFloat(parseFloat(exclusive) - parseFloat(vat));

            // $(vara).parents('tr').find('.discount').val((discount).toFixed(2));
            $(vara).parents('tr').find('.exclusive').html((exclusive).toFixed(2));
            $(vara).parents('tr').find('.vat').html((vat).toFixed(2));
            $(vara).parents('tr').find('.total input').val((total).toFixed(2));

            totalofAllTotal();
        }

        $(document).on('keyup, change', '.discount', function(e) {
            // var discount = parseFloat($(this).val());
            var price = parseFloat($(this).parents('tr').find('.standard_cost').val());
            var quantity = parseFloat($(this).parents('tr').find('.quantity').val());
            var vat_percentage = parseFloat($(this).parents('tr').find('.vat_percentage').val());

            var totalOriginalPrice = price * quantity; // Calculate the total original price

            // Calculate the discount percentage
            // var discountPercentage = (discount / totalOriginalPrice) * 100;

            var total = parseFloat(price) * parseFloat(quantity);
            var vat = parseFloat(total) - ((parseFloat(total) * 100) / (parseFloat(vat_percentage) + 100));
            exclusive = parseFloat(parseFloat(exclusive) - parseFloat(vat));

            //    $(this).parents('tr').find('.discount_per').val((discountPercentage).toFixed(2));
            $(this).parents('tr').find('.exclusive').html((exclusive).toFixed(2));
            $(this).parents('tr').find('.vat').html((vat).toFixed(2));
            $(this).parents('tr').find('.total input').val((total).toFixed(2));

            totalofAllTotal();
        });

        function reverseCalculate(vara) {
            var total = $(vara).parents('tr').find('.total input').val();
            var quantity = $(vara).parents('tr').find('.quantity').val();

            var price = (total / quantity);

            $(vara).parents('tr').find('.standard_cost').val(price.toFixed(2));
            //  var discount_per = $(vara).parents('tr').find('.discount_per').val();
            var vat_percentage = $(vara).parents('tr').find('.vat_percentage').val();
            //   var discount = ((parseFloat(price) * parseFloat(quantity)) * parseFloat(discount_per)) / 100;
            //  var exclusive = ((parseFloat(price) * parseFloat(quantity)) - parseFloat(discount));
            var total = (parseFloat(price) * parseFloat(quantity));
            var vat = parseFloat(total) - ((parseFloat(total) * 100) / (parseFloat(vat_percentage) + 100));
            exclusive = parseFloat(parseFloat(total) - parseFloat(vat));

            //   $(vara).parents('tr').find('.discount').val((discount).toFixed(2));
            $(vara).parents('tr').find('.exclusive').html((exclusive).toFixed(2));
            $(vara).parents('tr').find('.vat').html((vat).toFixed(2));

            totalofAllTotal();
        }

        function totalofAllTotal() {
            var alle = $(document).find('.exclusive');
            var allv = $(document).find('.vat');
            var allt = $(document).find('.total input');

            var exclusive = 0;
            var vat = 0;
            var total = 0;

            $.each(alle, function(indexInArray, valueOfElement) {
                exclusive = parseFloat(exclusive) + parseFloat($(valueOfElement).text().replaceAll(',', ''));
            });
            $.each(allv, function(indexInArray, valueOfElement) {
                vat = parseFloat(vat) + parseFloat($(valueOfElement).text().replaceAll(',', ''));
            });
            $.each(allt, function(indexInArray, valueOfElement) {
                total = parseFloat(total) + parseFloat($(valueOfElement).val().replaceAll(',', ''));
            });

            $('#total_exclusive').html((exclusive).formatMoney());
            $('#total_vat').html((vat).formatMoney());

            var roundoff = total % 1;
            if (roundoff != 0) {
                if (roundoff >= 0.5) {
                    roundoff = roundoff;
                } else {
                    roundoff = -roundoff;
                }
                //total = parseFloat(total) + parseFloat(roundoff);
            }

            // $('#round_off_total').html((roundoff).formatMoney());
            $('#total_total').html((total).formatMoney());
        }
    </script>
@endsection
