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
                    <h3 class="box-title">Item Margins Report</h3>
                   
                </div>
            </div>
            <div class="box-body">
                @include('message')
                <div class="col-md-12 no-padding-h table-responsive">
                    <form action="{{ route('item-margins-report.index') }}" method="GET">
                        <div class="row">
                            
                            <div class="col-md-3 form-group">
                                <label for="">Supplier</label>
                                <select name="supplier" id="supplier" class="form-control mlselec6t" >
                                    <option value="" selected disabled>--Select Supplier--</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{request()->supplier ==  $supplier->id ? 'selected' : ''}}>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="">Category</label>
                                <select name="category" id="category" class="form-control mlselec6t" >
                                    <option value="" selected disabled>--Select Category--</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{request()->category ==  $category->id ? 'selected' : ''}}>{{ $category->category_description }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="">Competing Brand</label>
                                <select name="brand" id="brand" class="form-control mlselec6t" >
                                    <option value="" selected disabled>--Select Competing Brand--</option>
                                    @foreach ($competingBrands as $brand)
                                        <option value="{{ $brand->id }}" {{request()->brand ==  $brand->id ? 'selected' : ''}}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                
                            <div class="col-md-3 ">
                                <br>
                                <button type="submit" name="filter" value="Filter" class="btn btn-success "><i class="fas fa-filter"></i> Filter</button>
                                <button type="submit" name="intent" value="Excel" class="btn btn-success "><i class="fas fa-file-excel"></i> Excel</button>
                                <a href="{{route('item-margins-report.index')}}" class="btn btn-success "><i class="fas fa-eraser"></i> Clear</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-12 no-padding-h table-responsive">
                    <table  class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item Code</th>
                                <th>Title</th>
                                <th>Supplier</th>
                                <th>Users</th>
                                <th>Qoh</th>
                                <th class="amount">Actual Margin</th>
                                <th class="amount">Discount On Delivery</th>
                                <th class="amount">End Month Discount</th>
                                <th class="amount">Quartely Discount</th>
                                <th class="amount">Total Margin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $row)
                                <tr>
                                    <th>{{$loop->index+1}}</th>
                                    <td>{{$row->stock_id_code}}</td>
                                    <td>{{$row->title}}</td> 
                                    <td>{{$row->suppliers}}</td>
                                    <td>{{$row->procurement_users}}</td>
                                    <td style="text-align: center;">{{$row->qoh}}</td>
                                    <td class="amount">{{$row->actual_margin. ($row->margin_type == 1 ? ' %' : ' KES')}}</td>
                                    <td>{{ $row->delivery_discount ? $row->delivery_discount . ($row->delivery_discount_type == 'Percentage' ? ' %': ' KES') : '-' }}</td>
                                    <td>{{ $row->monthly_discount ? $row->monthly_discount . ($row->monthly_discount_type == 'Percentage' ? ' %': ' KES') : '-' }}</td>
                                    <td>{{ $row->quarterly_discount ? $row->quarterly_discount . ($row->quarterly_discount_type == ' Percentage' ? ' %': ' KES') : '-' }}</td>
                                    <td>{{($row->actual_margin + $row->delivery_discount + $row->monthly_discount + $row->quarterly_discount) . ($row->margin_type == 1 ? ' %' : ' KES')}}</td>
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
    </style>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
       
        $(function() {
            $(".mlselec6t").select2();
        });
    </script>
@endsection
