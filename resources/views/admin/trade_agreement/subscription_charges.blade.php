@extends('layouts.admin.admin')

@section('content')
<form action="{{route('trade-agreement.subscription_charges',$trade->id)}}" method="post"  class="submitMe">
    @csrf
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Supplier Subscription </h3>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table">
                            <tr>
                                <th>Supplier</th>
                                <td>
                                    {{$trade->supplier->name}}
                                </td>
                            </tr>
                            
                            <tr>
                                <th>Trade Reference</th>
                                <td> {{$trade->reference}} </td>
                            </tr>
                        </table>                        
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        @php
                            $plan = $plans->first();   
                        @endphp
                        <div class="form-group">
                            <label for="">Currency</label>
                            <select name="currency_id" class="form-control open-select2">
                                <option value="" selected disabled>-- Select Currency --</option>
                                @foreach ($currencies as $currency)
                                    <option value="{{$currency->id}}" {{@$plan->wa_currency_manager_id == $currency->id ? 'selected' : ''}}>{!! $currency->ISO4217  !!}</option>
                                @endforeach
                            </select>
                        </div>
                        @foreach ($plan_types as $key => $type)
                            @php
                                $plan = $plans->where('billing_period',$type)->first();   
                            @endphp
                            <div class="form-group">
                                <label for="">{{$type}} Charges</label>
                                <input type="text" name="{{$key}}_charge" id="{{$key}}_charge" value="{{@$plan->charges ?? ''}}" class="form-control" placeholder="Enter Subscription Charges" aria-describedby="helpId">
                            </div>
                        @endforeach
                        
                        <button type="submit" class="btn btn-danger">Save Subscription Charges</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</form>
@endsection

@section('uniquepagescript')
<link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
<script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <style type="text/css">
        .select2 {
            width: 100% !important;
        }
        .loader {
            width: 100px;
            height: 100px;
            border-radius: 100%;
            position: relative;
            margin: 0 auto;
            top: 35%;
        }
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
        .badge-bizwiz{
            background-color: #0086ff21;
            color: black;
            font-weight: 500;
            padding: 5px 12px;
            border: 1px solid #0074ff;
            margin: 2px;
        }
        .badge-bizwiz a{
            margin-left: 4px;
            font-size: 16px;
            font-weight: 900;
        }
    </style>
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
        "
        class="loder">
        <div class="loader" id="loader-1"></div>
    </div> 
 
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script>
        $('.open-select2').select2({
            templateResult: formatState,
            templateSelection: formatState
        });
        function formatState (opt) {
            if (!opt.id) {
                return opt.text;
            } 

            var $opt = $(
                `<span><img src="{!! asset('assets/admin/flags')!!}/${opt.text}.gif" style="width:50px; margin-right:5px" /> ${opt.text}</span>`
            );
            return $opt;
        }
    </script>
@endsection
