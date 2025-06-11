@extends('layouts.admin.admin')

@section('content')
    <style>
        .span-action {

            display: inline-block;
            margin: 0 3px;

        }
    </style>
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Items With Multiple Suppliers</h3>
                   
                </div>
            </div>
            <div class="box-body">
                @include('message')
                <div class="col-md-12 no-padding-h table-responsive">
                    <form action="{{ route('items-with-multiple-suppliers.index') }}" method="GET">
                        <div class="row">
                            
                            <div class="col-sm-3">
                                <label for="category">Category</label>
                                <select name="category" id="category" class="form-control">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->category_description }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 ">
                                <br>
                                <button type="submit" name="filter" value="Filter" class="btn btn-success"><i class="fas fa-filter"></i> Filter</button>
                                <button type="submit" name="intent" value="Excel" class="btn btn-success"><i class="fas fa-file-excel"></i> Excel</button>
                                <a href="{{route('items-with-multiple-suppliers.index')}}" class="btn btn-success"><i class="fas fa-eraser"></i> Clear</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-12 no-padding-h table-responsive">
                    <table  class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                            <tr >
                                <th>#</th>
                                <th>Stock_id_code</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Pack Size</th>
                                <th class="amount">Price List Cost</th>
                                <th class="amount">Standard Cost</th>
                                <th class="amount">Selling Price</th>
                                <th>QOH</th>
                                <th>Supplier</th>
                                <th>Procurement User</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data_query as $item)
                            <tr>
                                <th>{{$loop->index+1}}</th>
                                <td>{{$item->stock_id_code}}</td>
                                <td>{{$item->title}}</td>
                                <td>{{$item->category}}</td>
                                <td>{{$item->pack_size}}</td>
                                <td class="amount">{{manageAmountFormat($item->price_list_cost)}}</td>
                                <td class="amount">{{manageAmountFormat($item->standard_cost)}}</td>
                                <td class="amount">{{manageAmountFormat($item->selling_price)}}</td>
                                <td class="qty">{{$item->qoh}}</td>
                                <td>{{$item->suppliers}}</td>
                                <td>{{$item->users}}</td>
                            </tr>
                                
                            @endforeach
                        </tbody>
                   
                       
                    </table>
                </div>

            </div>
        </div>


    </section>
@endsection
@section('uniquepagescript')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .select2 {
            width: 100% !important;
        }
        .amount{
            text-align: right;
        }
        .qty{
            text-align: center;
        }
    </style>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
       
        $(function() {
            $(".mlselec6t").select2();
            $("#category, #supplier, #branch").select2();

        });
    </script>
@endsection
