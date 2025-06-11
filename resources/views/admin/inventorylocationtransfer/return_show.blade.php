@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> {{ $row->transfer_no }} - Invoice Return </h3>
                    {{--                    <a href="{{ route('transfers.index') }}" class="btn btn-primary"> Back </a> --}}
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="inputEmail3" class="control-label">Requisition No.</label>
                        <input class="form-control" value="{{ $row->transfer_no }}">

                    </div>

                    <div class="form-group col-md-4">
                        <label for="inputEmail3" class="control-label">Salesman</label>
                        <span class="form-control">{{ $row->salesman }}</span>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="inputEmail3" class="control-label">Invoice Date</label>
                        <span class="form-control">{{ \Carbon\Carbon::parse($row->created_at)->toDateString() }}</span>
                    </div>
                </div>

                <section class="content">
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            <h3 class="box-title"> Invoice Items</h3>
                        </div>

                        <div class="box-body">
                            <form action="{{ route('transfers.return_process', $row->slug) }}" method="post"
                                class="addExpense">
                                {{ csrf_field() }}
                                <div>
                                    <input type="hidden" name="id" value="{{ $row->id }}">
                                    <input class="form-control" name="salesman" type="hidden"
                                        value="{{ $row->salesman_id }}">
                                    <input class="form-control" name='route' type="hidden" value="{{ $row->route_id }}">
                                    <input class="form-control" name='customer_id' type="hidden"
                                        value="{{ $row->customer_id }}">
                                    <span class="item"></span>
                                </div>
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Item</th>
                                            <th>Bin</th>
                                            <th>Invoice Qty</th>
                                            <th> Selling Price</th>
                                            <th> Discount</th>
                                            <th>Total Cost Inc</th>
                                            <th>initiated Qty</th>
                                            <th>Already Returned</th>
                                            <th>Received</th>
                                            <th>Total Rejects</th>
                                            <th>Return Qty</th>
                                            <th>Reason</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @if ($invoiceItems && count($invoiceItems) > 0)
                                            @foreach ($invoiceItems as $item)
                                                <input type="hidden" name="transfer_item_id[]"
                                                    value="{{ $item->id }}">
                                                <input type="hidden" name="item_code[{{ $item->id }}]"
                                                    value="{{ $item->item_code }}">
                                                <tr>
                                                    <td>
                                                        <div class="checkbox" style="margin: 0;">
                                                            <label>
                                                                <input type="checkbox" name="item[{{ $item->id }}]"
                                                                    onchange="quantity_updater(this,'.quantity_{{ $item->id }}', '.reason_{{ $item->id }}')"
                                                                    value="{{ $item->id }}">{{ $loop->index + 1 }}
                                                            </label>
                                                        </div>
                                                    </td>

                                                    <td>{{ $item->item_code }} - {{ $item->item_name }}</td>
                                                    <td>{{ $item->bin }}</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>{{ number_format($item->selling_price, 2) }}</td>
                                                    <td>{{ number_format($item->discount, 2) }}</td>
                                                    <td>{{ number_format($item->total_cost_with_vat, 2) }}</td>
                                                    <td>{{ $item->initiated_quantity }}</td>
                                                    <td>{{ $item->returned_quantity }}</td>
                                                    <td>{{ $item->received_quantity }}</td>
                                                    <td>{{ $item->rejected_quantity }}</td>

                                                    <?php
                                                    
                                                    $remainder = $item->quantity - $item->received_quantity - $item->initiated_quantity;
                                                    if ($remainder <= 0) {
                                                        $remainder = 0;
                                                    }
                                                    
                                                    ?>
                                                    <td class="align_float_right">
                                                        <input type="number" id="return-{{ $item->id }}"
                                                            class="form-control quantity_{{ $item->id }}"
                                                            name="quantity[{{ $item->id }}]" value="0"
                                                            aria-describedby='helpId' placeholder="0" style="width: 60px;"
                                                            max="<?php echo (int) $remainder; ?>" min="0" readonly>
                                                        <input type="hidden" name="qty[{{ $item->id }}]"
                                                            value="<?php echo (int) $remainder; ?>">

                                                    </td>

                                                    <td>
                                                        {{-- <input type="text" class="form-control reason_{{$item->id}}"
                                                           name="reason[{{$item->id}}]"

                                                           placeholder="Return Reason"> --}}
                                                        <select name="reason[{{ $item->id }}]"
                                                            id="reason[{{ $item->id }}]"
                                                            class="form-control reason_{{ $item->id }}">
                                                            <option value="" disabled selected>Select Return Reason
                                                            </option>
                                                            @foreach ($returnReasons as $reason)
                                                                <option value="{{ $reason->id }}">{{ $reason->reason }}
                                                                </option>
                                                            @endforeach

                                                        </select>
                                                    </td>
                                                </tr>
                                            @endforeach

                                            <tr>
                                                <td colspan="13" style="text-align: right;">
                                                    <button type="submit" class="btn btn-primary">Process Return</button>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>

@endsection


@section('uniquepagestyle')
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


        /* ALL LOADERS */

        .loader {
            width: 100px;
            height: 100px;
            border-radius: 100%;
            position: relative;
            margin: 0 auto;
            top: 35%;
        }

        /* LOADER 1 */

        #loader-1:before,
        #loader-1:after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 100%;
            border: 10px solid transparent;
            border-top-color: #3498db;
        }

        #loader-1:before {
            z-index: 100;
            animation: spin 1s infinite;
        }

        #loader-1:after {
            border: 10px solid #ccc;
        }

        @keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
                -ms-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
                -ms-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
    </style>
@endsection


@section('uniquepagescript')
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
    ">

        <div class="loader" id="loader-1"></div>
    </div>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>

    <script type="text/javascript">
        // let form = new Form();
        $(function() {
            $(".mlselect").select2();
        });

        function quantity_updater(param, output, reason) {
            if ($(param).is(':checked')) {
                $(output).removeAttr('readonly');
                $(reason).attr('required', true);
                $(reason).removeAttr('readonly');


            } else {
                $(output).attr('readonly', true);
                $(reason).removeAttr('required');
                $(reason).attr('readonly', true);



            }
        }

        // $(document).on('submit', '.addExpense', function (e) {
        //     e.preventDefault();
        //     $('#loader-on').show();
        //     var postData = new FormData($(this)[0]);
        //     var url = $(this).attr('action');
        //     postData.append('_token', $(document).find('input[name="_token"]').val());
        //     $.ajax({
        //         url: url,
        //         data: postData,
        //         contentType: false,
        //         cache: false,
        //         processData: false,
        //         method: 'POST',
        //         success: function (out) {
        //             $('#loader-on').hide();
        //
        //             $(".remove_error").remove();
        //             if (out.result == 0) {
        //                 for (let i in out.errors) {
        //                     var id = i.split(".");
        //                     if (id && id[1]) {
        //                         $("[name='" + id[0] + "[" + id[1] + "]']").parent().append('<label class="error d-block remove_error w-100" id="' + i + '_error">' + out.errors[i][0] + '</label>');
        //                     } else {
        //                         $("[name='" + i + "']").parent().append('<label class="error d-block remove_error w-100" id="' + i + '_error">' + out.errors[i][0] + '</label>');
        //                         $("." + i).parent().append('<label class="error d-block remove_error w-100" id="' + i + '_error">' + out.errors[i][0] + '</label>');
        //                     }
        //                 }
        //             }
        //             if (out.result === 1) {
        //                 form.successMessage(out.message);
        //                 if (out.location) {
        //                     setTimeout(() => {
        //                         location.href = out.location;
        //                     }, 1000);
        //                 }
        //             }
        //             if (out.result === -1) {
        //                 form.errorMessage(out.message);
        //             }
        //         },
        //
        //         error: function (err) {
        //             $('#loader-on').hide();
        //             $(".remove_error").remove();
        //             form.errorMessage('Something went wrong');
        //         }
        //     });
        // });
        function check_allowed(val, qty) {
            console.log(val, qty);

        }

        $(document).ready(function() {
            $('body').addClass('sidebar-collapse');
        });

        function int(value) {
            return parseInt(value);
        }

        // this checks the value and updates it on the control, if needed
        function checkValue(sender) {
            let min = sender.min;
            let max = sender.max;
            let value = int(sender.value);
            if (value > max) {
                sender.value = min;
            } else if (value < min) {
                sender.value = max;
            }
        }
    </script>
@endsection
