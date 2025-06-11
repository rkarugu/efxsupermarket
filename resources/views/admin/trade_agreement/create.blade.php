@extends('layouts.admin.admin')

@section('content')
<form action="{{route('trade-agreement.store')}}" method="post"  class="submitMe">
    @csrf
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Trade Agreement </h3>
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
                                    <select name="wa_supplier_id" class="form-control make_select2">
                                        <option value="" selected disabled>-- Select Supplier --</option>
                                        @foreach($suppliers as $supplier)
                                        <option value="{{$supplier->id}}">{{$supplier->name}} {{$supplier->supplier_code}}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            
                            <tr>
                                <th>Date</th>
                                <td>{{date('d M Y')}}</td>
                            </tr>
                        </table>                        
                    </div>
                    <div class="col-sm-6">
                        
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                            <span name="summary"></span>
                            <table id="add_summary" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            Agreement Summary
                                        </th>
                                        <th>
                                            <button class="btn btn-primary" type="button" onclick="addMoreRows(); return false;">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                            <button type="submit" class="btn btn-danger">
                                <i class="fa fa-save"></i> &nbsp; Create Agreement</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</form>
@endsection

@section('uniquepagescript')
<link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
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
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        function addMoreRows(){
            row = `<tr><td><input type="text" class="form-control" value="" name="summary[]"></td>`+
                        `<td><button class="btn btn-primary" type="button" onclick="$(this).parents('tr').remove(); return false;">`+
                        `<i class="fa fa-trash"></i></button></td></tr>`;
            $('#add_summary tbody').append(row);
        }
        $(document).ready(function () {
            $(".make_select2").select2();
        });

    </script>
@endsection
