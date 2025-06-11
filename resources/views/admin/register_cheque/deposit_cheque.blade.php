@extends('layouts.admin.admin')
@section('content')
    <form method="POST" action="{{ route($model.'.deposit_cheque_update',$data->id) }}" accept-charset="UTF-8"
          class="submitMe" enctype="multipart/form-data">
        <input type="hidden" name="id" value="{{$data->id}}">
        <input type="hidden" name="source" value="register-cheque">
        {{method_field('PUT')}}
        <a href="{{ route($model.'.index') }}?source=register-cheque" class="btn btn-primary">Back</a>
        <br>
        <section class="content">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
                @include('message')

                {{ csrf_field() }}

                <div class="box-body">
                    <div class="row">

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="">Deposit Date</label>
                                <input type="date" name="deposited_date" id="deposited_date"
                                       value="{{$data->deposited_date}}" class="form-control" placeholder="Deposit Date"
                                       max="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                        {{--            <div class="col-sm-4">--}}
                        {{--                <div class="form-group">--}}
                        {{--                  <label for="">Bank Deposited</label>--}}
                        {{--                  <input type="text" name="bank_deposited" id="bank_deposited" value="{{$data->bank_deposited}}" class="form-control" placeholder="Bank Deposited" >--}}
                        {{--                </div>--}}
                        {{--            </div>--}}
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="">Bank Deposited</label>
                                <select name="bank_deposited" id="bank_deposited" class="form-group">
                                    <option value="" selected disabled>Select Bank</option>
                                    @foreach ($banks as $item)
                                        <option value="{{$item->id}}">{{$item->bank}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="">Employee</label>
                                <select name="deposited_by" id="deposited_by" class="form-group">
                                    <option value="" selected disabled>Select Employee</option>
                                    @foreach ($users as $item)
                                        <option value="{{$item->id}}" {{$data->deposited_by == $item->id ? 'selected' : null}}>{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-danger">Submit</button>
                        </div>
                        <div class="col-sm-6 mt-5" style="margin-top: 2%">
                            <img src="{{asset($data->cheque_image)}}" width="100%">
                        </div>

                    </div>
                </div>
            </div>
        </section>


    </form>

@endsection

@section('uniquepagestyle')
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>

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
    <div id="loader-on" class="loder" style="
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
    <script>
        $('#deposited_by').select2();
        $('#bank_deposited').select2();
    </script>
@endsection


