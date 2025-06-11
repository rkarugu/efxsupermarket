@extends('layouts.admin.admin')

@section('content')
    <style>
        .span-action {

            display: inline-block;
            margin: 0 3px;
        }
    </style>
    @include('message')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Item Data </h3>
                    
                    <div>
                        @if(isset($permission[$pmodule.'___edit']) || $permission == 'superadmin')
                            <a href = "{!! route($model.'.edit',$row->slug)!!}" class = "btn btn-success">Edit Item</a>
                        @endif
                    </div>                       
                </div>
            </div>
            
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4">
                        <label for="">Title</label>
                        <p>{{$row->title}}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Category</label>
                        <p>{{$row->category->category_description}}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Sub Category</label>
                        <p>{{$row->sub_category->title}}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Status</label>
                        <p>{{$row->status==1?'Active':'Retired'}}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Approval Status</label>
                        <p>{{$row->approval_status}}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Description</label>
                        <p>{{$row->description}}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Standard Cost</label>
                        <p>{{$row->standard_cost}}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Prev Standard Cost</label>
                        <p>{{$row->prev_standard_cost}}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Selling Price</label>
                        <p>{{$row->selling_price}}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Pack Size</label>
                        <p>
                            @if ($row->pack_size)
                            {{$row->pack_size->title}}
                            @else
                            -
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Stock Id Code</label>
                        <p>{{$row->stock_id_code}}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Gross Weight</label>
                        <p>{{$row->gross_weight}}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Net Weight</label>
                        <p>{{$row->net_weight}}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Tax</label>
                        <p>
                            @if ($row->getTaxesOfItem)
                                {{$row->getTaxesOfItem->title}}  
                            @else 
                            -     
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Percentage Margin</label>
                        <p>{{$row->percentage_margin}} %</p>
                    </div>
                   
                    <div class="col-md-4">
                        <label for="">Restocking Method</label>
                        <p>{{$row->restocking_method}}</p>
                    </div>
                    <div class="col-md-4">
                        <label for="">Supplier(s)</label>
                        <p>
                            @foreach ($row->suppliers as $supplier)
                                {{$supplier->name}} ,
                            @endforeach
                        </p>
                    </div>
                </div>                
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('css/multistep-form.css') }}">
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
        "
        class="loder">
        <div class="loader" id="loader-1"></div>
    </div>
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    {{-- <script src="{{ asset('js/multistep-form.js') }}"></script> --}}
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            
            $('.wa_inventory_category_id').select2();
            $('#supplier-id').select2();
        });
    </script>
    <script type="text/javascript">
        
    </script>
@endsection
