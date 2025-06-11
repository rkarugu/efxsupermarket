@extends('layouts.admin.admin')
@section('content')
    <a href="{{ route('pos-cash-sales.returned_cash_sales_list_dispatcher') }}" class="btn btn-primary">Back</a>
    <br>
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
            @include('message')
            <div class = "row">
                <div class = "col-sm-4">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="">Date</label>
                            <span class="form-control">{{@$cash_sale->created_at ->format('d-m-Y')}}</span>
                        </div>
                    </div>
                </div>

                <div class = "col-sm-4">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="">Time</label>
                            <span class="form-control">{{@$cash_sale->created_at -> format('H:m:s')}}</span>
                        </div>
                    </div>
                </div>

                <div class = "col-sm-4">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="">Cashier</label>
                            <span class="form-control">{{@$cash_sale->user->name}}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class = "row">
                <div class = "col-sm-3">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="">Customer</label>
                            <span class="form-control">{{@$cash_sale->customer}}</span>
                        </div>
                    </div>
                </div>
                <div class = "col-sm-3">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="">Sale No</label>
                            <span class="form-control">{{@$cash_sale->sales_no}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">


                <div class="col-md-12 no-padding-h ">
                    <h3 class="box-title"> Cash Sales Return</h3>

                    <form action="{{route('pos-cash-sales.accept_return',base64_encode(@$cash_sale->id))}}" method="POST" class = "addExpense" id="return_form">
                        {{ csrf_field() }}
                        <input type="hidden" name="is_late" value="1">
                        <table class="table table-bordered table-hover" id="mainItemTable">
                            <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Return Time</th>
                                <th>Item Title</th>
                                <th>Return QTY</th>
                                <th>Return Reason</th>
                                <th>Status</th>
                                <th>Comment</th>
                                <th>Reject</th>

{{--                                @foreach($data as $item)--}}
{{--                                    @if($loop->first && $item->accepted_at)--}}
{{--                                        <th>Time</th>--}}
{{--                                        @break--}}
{{--                                    @else--}}
{{--                                        <th>Reject</th>--}}
{{--                                    @endif--}}

{{--                                @endforeach--}}
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $completed =  false;
                            @endphp
                            @if($data)
                                @foreach ($data as $return)
                                    <tr>
                                        <td>
                                            @if($return -> accepted_by != null)
                                                {{@$return->saleItem->item->stock_id_code}}
                                                @php
                                                    $completed =  true;
                                                @endphp
                                            @else
                                                <div class="checkbox" style="margin-top: 0;margin-bottom: 0;">
                                                    <label>
                                                        <input class="checked_return_items" type="checkbox" name="item[{{$return->id}}]" value="{{$return->id}}">{{@$return->saleItem->item->stock_id_code}}
                                                    </label>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{@$return->created_at}}</td>
                                        <td>{{@$return->saleItem->item->description}}</td>
                                        <td>
                                            {{ $return->return_quantity }}
                                        </td>
                                        <td>
                                            {{ @$return->reasons ->reason  }}
                                        </td>
                                        <td>
                                           @if($return->accepted_at == null)
                                               pending
                                            @else
                                               {{ $return->accepted ? 'Accepted':'Rejected' }}
                                           @endif
                                        </td>
                                        <td>

                                            <input type="text" class="form-control" name="comment[{{$return->id}}]" value="{{ $return->comment }}" placeholder="Write your comment" style="height: 60px" @if($return->accepted_at != null)readonly disabled @endif>
                                        </td>
                                        <td style="padding: 2%">
                                            @if($return->accepted_at != null)
                                                {{ $return->accepted_at  }}
                                            @else
                                                <input type="checkbox" class="form-check" name="reject[{{$return->id}}]" @if($return->accepted_at != null && !$return -> accepted)checked  @endif @if($return -> accepted_by != null)disabled @endif>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        Empty Table
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                        <button type="submit" class="btn btn-primary align_float_right addExpense">Process</button>

                    </form>
                </div>



            </div>
        </div>


    </section>

    <div id="otpModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">OTP Verification</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Please enter the OTP to Reject Returns:</p>
                    <input type="text" id="otpInput" class="form-control" placeholder="Enter OTP">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="verifyOtpBtn">Verify OTP</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('uniquepagestyle')
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
    <link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">
    <style type="text/css">
        .select2{
            width: 100% !important;
        }
        #note{
            height: 60px !important;
        }
        .align_float_right
        {
            text-align:  right;
        }
        .textData table tr:hover{
            background:#000 !important;
            color:white !important;
            cursor: pointer !important;
        }


        /* ALL LOADERS */

        .loader{
            width: 100px;
            height: 100px;
            border-radius: 100%;
            position: relative;
            margin: 0 auto;
            top: 35%;
        }

        /* LOADER 1 */

        #loader-1:before, #loader-1:after{
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

        #loader-1:before{
            z-index: 100;
            animation: spin 1s infinite;
        }

        #loader-1:after{
            border: 10px solid #ccc;
        }

        @keyframes spin{
            0%{
                -webkit-transform: rotate(0deg);
                -ms-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100%{
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
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">
        function enableQuantity(pro,param) {
            if($(pro).is(':checked')){
                $(param).removeAttr('readonly');
            }else{
                $(param).attr('readonly',true);
            }
        }
        var form = new Form();
        $(document).on('submit','.addExpense',function(e){
            e.preventDefault();
            var is_checked = 0;
            $('.checked_return_items').each(function () {
                if($(this).prop('checked')==true){
                    is_checked=1;
                }
            });

            if(is_checked==true){

                // $('.return_btn').attr('disabled',true);

                $('#loader-on').show();
                var postData = new FormData($(this)[0]);
                var url = $(this).attr('action');
                postData.append('_token',$(document).find('input[name="_token"]').val());
                $.ajax({
                    url:url,
                    data:postData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    method:'POST',
                    success:function(out){
                        $('#loader-on').hide();
                        console.log(out.result)
                        $(".remove_error").remove();
                        if(out.result == 0) {
                            for(let i in out.errors) {
                                var id = i.split(".");
                                if(id && id[1]){
                                    $("[name='"+id[0]+"["+id[1]+"]']").parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                                }else
                                {
                                    $("[name='"+i+"']").parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                                    $("."+i).parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                                }
                            }
                        }
                        if(out.result === 1) {
                            form.successMessage(out.message);
                            if(out.location)
                            {
                                setTimeout(() => {
                                    location.href = out.location;
                                }, 1000);
                            }
                        }
                        if(out.result === -1) {
                            form.errorMessage(out.message);
                        }
                        if(out.result === 2) {
                            $('#otpModal').modal('show');
                        }
                    },

                    error:function(err)
                    {
                        $('#loader-on').hide();
                        $(".remove_error").remove();
                        form.errorMessage('Something went wrong');
                    }
                });
            }else{
                alert('Please select an item first!')
            }



        });
        $('#verifyOtpBtn').click(function() {
            const otp = $('#otpInput').val();

            $.post("{{ route('pos-cash-sales.verify-return.otp') }}", {
                _token: '{{ csrf_token() }}',
                otp: otp,
                sales_no: '{{ $cash_sale->id }}'
            }, function(response) {
                if (response.success) {
                    let fom = document.getElementById('return_form');
                    var postData = new FormData(fom);
                    var url = fom.getAttribute('action');
                    postData.append('_token',$(document).find('input[name="_token"]').val());
                    postData.append('otp_verified','true');
                    $.ajax({
                        url:url,
                        data:postData,
                        contentType: false,
                        cache: false,
                        processData: false,
                        method:'POST',
                        success:function(out){
                            $('#loader-on').hide();
                            console.log(out.result)
                            $(".remove_error").remove();
                            if(out.result == 0) {
                                for(let i in out.errors) {
                                    var id = i.split(".");
                                    if(id && id[1]){
                                        $("[name='"+id[0]+"["+id[1]+"]']").parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                                    }else
                                    {
                                        $("[name='"+i+"']").parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                                        $("."+i).parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                                    }
                                }
                            }
                            if(out.result === 1) {
                                form.successMessage(out.message);
                                if(out.location)
                                {
                                    setTimeout(() => {
                                        location.href = out.location;
                                    }, 1000);
                                }
                            }
                            if(out.result === -1) {
                                form.errorMessage(out.message);
                            }
                            if(out.result === 2) {
                                $('#otpModal').modal('show');
                            }
                        },

                        error:function(err)
                        {
                            $('#loader-on').hide();
                            $(".remove_error").remove();
                            form.errorMessage('Something went wrong');
                        }
                    });
                } else {
                    alert(response.message);
                }
            });
        });


    </script>
    <script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>
@endsection


