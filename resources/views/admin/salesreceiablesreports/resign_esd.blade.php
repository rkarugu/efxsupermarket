@extends('layouts.admin.admin')
@section('content')

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Re-sign Invoice </h3>
                    <a href="{{ route('transfers.index') . getReportDefaultFilterForTrialBalance()  }}" class="btn btn-primary">Back</a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form method="POST" action="{{ route('transfers.invoice-resign-esd-post',$data->id) }}">
                    {{csrf_field()}}

                    {{method_field('PATCH')}}

                    <div class="row">
                        <div class="col-sm-3">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="">Invoice Date</label>
                                    <input class="form-control" value="{{ $data->created_at->format('Y-m-d H:is') }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="">Invoice No.</label>
                                    <input class="form-control" value="{{ $data->requisition_no ?? $data->transfer_no }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="">Route</label>
                                    <input class="form-control" value="{{ $data->route }}" readonly>
                                </div>
                            </div>
                        </div>

                    </div>

                    <hr>

                    <h3 class="box-title" style="font-size: 16px;"> Invoice Lines </h3>
                    <hr>

                    <table class="table table-bordered table-hover" id="mainItemTable">
                        <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Amount</th>
                            <th>Disc</th>
                            <th>Vat%</th>
                            <th>Vat Amount</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>

                        @php
                            $TONNAGE = 0;
                            $gross_amount = 0;
                        @endphp
                        @foreach($data->getRelatedItem as $item)
                            <tr class="item">
                                <td>{{ $loop->index + 1 }}</td>
                                <td>
                                    <input type="hidden" name="item_id[{{@$item->wa_inventory_item_id}}]" class="itemid" value="{{@$item->wa_inventory_item_id}}">

                                    {{@$item->getInventoryItemDetail->stock_id_code}}</td>
                                <td>{{@$item->getInventoryItemDetail->description}}</td>
                                <td style="">{{floor($item->quantity)}}</td>
                                <td>{{manageAmountFormat($item->selling_price)}}</td>
                                <td>{{manageAmountFormat($item->quantity*$item->selling_price)}}</td>
                                <td>{{manageAmountFormat($item->discount_amount)}}</td>
                                <td>{{$item->vat_rate}}</td>
                                <td>{{manageAmountFormat($item->vat_amount)}}</td>
                                <td>{{manageAmountFormat($item->total_cost_with_vat)}}</td>
                            </tr>

                        @php
                            $gross_amount += (($item->quantity*$item->selling_price)-$item->discount_amount);

                            $TONNAGE += (($item->getInventoryItemDetail->net_weight ?? 1) * $item->quantity);
                        @endphp
                        @endforeach


                        <tfoot>
                        <tr>
                            <th colspan="8" style="text-align:right">
                                Total Price
                            </th>
                            <td colspan="2">KES <span id="total_exclusive">{{manageAmountFormat($gross_amount)}}</span></td>
                        </tr>
                        <tr>
                            <th colspan="8" style="text-align:right">
                                Discount
                            </th>
                            <td colspan="2">KES <span id="total_discount">{{manageAmountFormat($data->getRelatedItem->sum('discount_amount') ?? 0.00)}}</span></td>
                        </tr>

                        <tr>
                            <th colspan="8" style="text-align:right">
                                Net Amount
                            </th>
                            <td colspan="2">KES <span id="total_vat">{{manageAmountFormat($gross_amount - ($data->getRelatedItem->sum('vat_amount') ?? 0.00))}}</span></td>
                        </tr>

                        <tr>
                            <th colspan="8" style="text-align:right">
                                Total VAT
                            </th>
                            <td colspan="2">KES <span id="total_vat">{{manageAmountFormat($data->getRelatedItem->sum('vat_amount') ?? 0.00)}}</span></td>
                        </tr>
                        <tr>
                            <th colspan="8" style="text-align:right">
                                Total
                            </th>
                            <td colspan="2">KES <span id="total_total">{{manageAmountFormat($gross_amount)}}</span></td>
                        </tr>
                        </tfoot>
                    </table>

                    <div class="d-flex justify-content-end">
                        <input type="submit" value="Confirm Resign" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
    <link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">

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

        #loader-1:before, #loader-1:after {
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
">
        <div class="loader" id="loader-1"></div>
    </div>
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">


        {{--$(document).on('click', '.addExpense', function (e) {--}}
        {{--    e.preventDefault();--}}
        {{--    $('#loader-on').show();--}}
        {{--    var postData = new FormData($(this).parents('form')[0]);--}}
        {{--    var url = $(this).parents('form').attr('action');--}}
        {{--    postData.append('_token', $(document).find('input[name="_token"]').val());--}}
        {{--    postData.append('request_type', $(this).val());--}}

        {{--    $.ajax({--}}
        {{--        url: url,--}}
        {{--        data: postData,--}}
        {{--        contentType: false,--}}
        {{--        cache: false,--}}
        {{--        processData: false,--}}
        {{--        method: 'POST',--}}
        {{--        success: function (out) {--}}
        {{--            $('#loader-on').hide();--}}

        {{--            $(".remove_error").remove();--}}
        {{--            if (out.result == 0) {--}}
        {{--                for (let i in out.errors) {--}}
        {{--                    var id = i.split(".");--}}
        {{--                    if (id && id[1]) {--}}
        {{--                        $("[name='" + id[0] + "[" + id[1] + "]']").parent().append('<label class="error d-block remove_error w-100" id="' + i + '_error">' + out.errors[i][0] + '</label>');--}}
        {{--                    } else {--}}
        {{--                        $("[name='" + i + "']").parent().append('<label class="error d-block remove_error w-100" id="' + i + '_error">' + out.errors[i][0] + '</label>');--}}
        {{--                        $("." + i).parent().append('<label class="error d-block remove_error w-100" id="' + i + '_error">' + out.errors[i][0] + '</label>');--}}
        {{--                    }--}}
        {{--                }--}}
        {{--            }--}}
        {{--            if (out.result === 1) {--}}

        {{--                var api_response = sendInvoiceRequestApi(out.data);--}}

        {{--                form.successMessage(out.message);--}}
        {{--                if (out.location) {--}}
        {{--                    setTimeout(() => {--}}
        {{--                        $('#mainItemTable tbody').html('');--}}
        {{--                        $('#mainItemTable tbody').append('<tr><td><input type="text" class="testIn form-control makemefocus"><div class="textData" style="width: 100%;position: relative;z-index: 99;"></div></td>'--}}
        {{--                            + '<td></td>'--}}
        {{--                            + '<td></td>'--}}
        {{--                            + '<td></td>'--}}
        {{--                            + '<td></td>'--}}
        {{--                            + '<td></td>'--}}
        {{--                            + '<td></td>'--}}
        {{--                            + '<td></td>'--}}
        {{--                            + '<td></td>'--}}
        {{--                            + '<td></td>'--}}
        {{--                            + '<td></td>'--}}
        {{--                            + '<td></td>'--}}
        {{--                            + '<td><button class="btn btn-danger btn-sm deleteparent"><i class="fa fa-trash" aria-hidden="true"></i></button></td>'--}}
        {{--                            + '</tr>');--}}
        {{--                        $('[name="customer_name"]').val('');--}}
        {{--                        $('.entered_cash').val('');--}}
        {{--                        $('.cash_change').html('');--}}
        {{--                        $('#total_exclusive').html('0.00');--}}
        {{--                        $('#total_discount').html('0.00');--}}
        {{--                        $('#total_vat').html('0.00');--}}
        {{--                        $('#total_total').html('0.00');--}}
        {{--                        if (out.requestty == 'save') {--}}
        {{--                            location.href = out.location;--}}
        {{--                        } else {--}}
        {{--                            location.href = out.location;--}}
        {{--                            //printBill(out.location);--}}
        {{--                            //printBill(out.location);--}}
        {{--                        }--}}
        {{--                    }, 8000);--}}
        {{--                }--}}
        {{--            }--}}
        {{--            if (out.result === -1) {--}}
        {{--                form.errorMessage(out.message);--}}
        {{--            }--}}
        {{--        },--}}

        {{--        error: function (err) {--}}
        {{--            $('#loader-on').hide();--}}
        {{--            $(".remove_error").remove();--}}
        {{--            form.errorMessage('Something went wrong');--}}
        {{--        }--}}
        {{--    });--}}

        {{--    function printBill(slug) {--}}
        {{--        jQuery.ajax({--}}
        {{--            url: slug,--}}
        {{--            type: 'GET',--}}
        {{--            async: false,   //NOTE THIS--}}
        {{--            headers: {--}}
        {{--                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')--}}
        {{--            },--}}
        {{--            success: function (response) {--}}
        {{--                var divContents = response;--}}
        {{--                var printWindow = window.open('', '', 'width=600');--}}
        {{--                printWindow.document.write(divContents);--}}
        {{--                printWindow.document.close();--}}
        {{--                printWindow.print();--}}
        {{--                printWindow.close();--}}
        {{--                // location.reload();--}}

        {{--            }--}}
        {{--        });--}}

        {{--    }--}}


        {{--    function sendInvoiceRequestApi(request_json) {--}}
        {{--        var myHeaders = new Headers();--}}
        {{--        myHeaders.append("Accept", "application/json");--}}
        {{--        myHeaders.append("Content-Type", "application/json");--}}
        {{--        myHeaders.append("Authorization", "Basic ZxZoaZMUQbUJDljA7kTExQ==");--}}

        {{--        /*var raw = JSON.stringify({--}}
        {{--          "invoice_date": "25_02_2022",--}}
        {{--          "invoice_number": "2502202211344",--}}
        {{--          "invoice_pin": "P051201909L",--}}
        {{--          "customer_pin": "P051241778C", // optional--}}
        {{--          "customer_exid": "", // tax exception number--}}
        {{--          "grand_total": "219.50",--}}
        {{--          "net_subtotal": "193.92",--}}
        {{--          "tax_total": "25.58",--}}
        {{--          "net_discount_total": "0",--}}
        {{--          "sel_currency": "KSH",--}}
        {{--          "rel_doc_number": "",--}}
        {{--          "items_list": [--}}
        {{--            "FLOURWHEATEXE2KG 1.50 50.00 75.00", // description quantity cost quantity total--}}
        {{--            "SUGARNZOIA50KG 1.00 100.00 100.00",--}}
        {{--            "0001.13.09 SPIRITTYPEJETFUEL 1.00 19.50 19.50", // hscode description quantity cost quantity total--}}
        {{--            "0039.11.16 SOMEZERORATEDITEM 1.00 15.00 15.00",--}}
        {{--            "0001.11.00 EXEMPTITEM 1.00 10.00 10.00"--}}
        {{--          ]--}}
        {{--        });*/--}}


        {{--        var raw = JSON.stringify(request_json);--}}
        {{--        var requestOptions = {--}}
        {{--            method: 'POST',--}}
        {{--            headers: myHeaders,--}}
        {{--            body: raw,--}}
        {{--            redirect: 'follow'--}}
        {{--        };--}}

        {{--        var esd_url = "{{$esd_url}}";--}}

        {{--        console.log(raw);--}}
        {{--        console.log(esd_url + "/api/sign?invoice+1");--}}

        {{--        //var apiUrl="{{url('test/')}}";--}}
        {{--        //console.log(testUrl);--}}

        {{--        // fetch("http://localhost:8089/api/sign?invoice+1", requestOptions) // url stored in db where it can be changed--}}

        {{--        fetch(esd_url + "/api/sign?invoice+1", requestOptions) // url stored in db where it can be changed--}}
        {{--            .then(response => response.text())--}}
        {{--            .then(result => {--}}
        {{--                // response was successful--}}
        {{--                //console.log(result);--}}
        {{--                var successval = 0;--}}
        {{--                var save_esd_url = "{{route('confirm-invoice.save_esd')}}";--}}
        {{--                //console.log('save_esd_url',save_esd_url);--}}

        {{--                $.ajax({--}}
        {{--                    url: save_esd_url,--}}
        {{--                    data: {"apiData": result, status: 1, "_token": "{{csrf_token()}}"},--}}
        {{--                    method: 'POST',--}}
        {{--                    async: false,--}}
        {{--                    success: function (res) {--}}
        {{--                        successval = 1;--}}
        {{--                    }, error: function () {--}}
        {{--                        successval = 0;--}}
        {{--                    }--}}
        {{--                });--}}

        {{--                //console.log(successval)--}}

        {{--                return successval;--}}
        {{--                //new QRCode(document.getElementById("qrcode"), result);--}}

        {{--            })--}}
        {{--            .catch(error => {--}}
        {{--                console.log('error', error)--}}
        {{--                var save_esd_url = "{{route('confirm-invoice.save_esd')}}";--}}
        {{--                $.ajax({--}}
        {{--                    url: save_esd_url,--}}
        {{--                    data: {status: 0, error: error, invoice_number: request_json.invoice_number, "_token": "{{csrf_token()}}"},--}}
        {{--                    method: 'POST',--}}
        {{--                    async: false,--}}
        {{--                    success: function (res) {--}}
        {{--                        successval = 0;--}}
        {{--                    }--}}
        {{--                });--}}


        {{--                return successval;--}}
        {{--            });--}}


        {{--    }--}}
        {{--});--}}
    </script>
@endsection