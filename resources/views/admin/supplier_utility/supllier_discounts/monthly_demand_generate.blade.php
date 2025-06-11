@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header">
                <div class="box-header-flex">
                    <div class="d-flex flex-column">
                        <h3 class="box-title"> Supplier Monthly Demand </h3>
                    </div>
                    
                </div>
                
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form action="" method="get">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                              <label for="supplier">Supplier</label>
                              <select name="supplier" class="form-control search_select">
                                <option value="" selected disabled>-- Select Supplier -- </option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{$supplier->id}}" {{(request()->supplier ?? '') == $supplier->id ? 'selected' : ""}}>{{$supplier->supplier_code}} - {{$supplier->name}}</option>
                                @endforeach
                              </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="month">Month</label>
                                <select name="month" class="form-control">
                                  <option value="" selected disabled>-- Select Month -- </option>
                                  <option value="1" {{(request()->month ?? '') == 1 ? 'selected' : ""}}>January</option>
                                  <option value="2" {{(request()->month ?? '') == 2 ? 'selected' : ""}}>Febuary</option>
                                  <option value="3" {{(request()->month ?? '') == 3 ? 'selected' : ""}}>March</option>
                                  <option value="4" {{(request()->month ?? '') == 4 ? 'selected' : ""}}>April</option>
                                  <option value="5" {{(request()->month ?? '') == 5 ? 'selected' : ""}}>May</option>
                                  <option value="6" {{(request()->month ?? '') == 6 ? 'selected' : ""}}>June</option>
                                  <option value="7" {{(request()->month ?? '') == 7 ? 'selected' : ""}}>July</option>
                                  <option value="8" {{(request()->month ?? '') == 8 ? 'selected' : ""}}>August</option>
                                  <option value="9" {{(request()->month ?? '') == 9 ? 'selected' : ""}}>September</option>
                                  <option value="10" {{(request()->month ?? '') == 10 ? 'selected' : ""}}>October</option>
                                  <option value="11" {{(request()->month ?? '') == 11 ? 'selected' : ""}}>November</option>
                                  <option value="12" {{(request()->month ?? '') == 12 ? 'selected' : ""}}>December</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="year">Year</label>
                                <select name="year" class="form-control">
                                    <option value="" selected disabled>-- Select Year -- </option>
                                    @for($i=2023;$i<=date('Y');$i++)
                                        <option value="{{$i}}" {{(request()->year ?? '') == $i ? 'selected' : ""}}>{{$i}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <br>
                            <button type="submit" class="btn btn-danger">Proceed</button>
                        </div>
                    </div>
                </form>
                @if(count($data)>0)
                <div class="table-responsive">
                    <table class="table" id="create_datatable_10">
                        <thead>
                            <tr>
                                <th>Inventory Item</th>
                                <th>Sold Qty</th>
                                <th>Total Cost</th>
                                <th>Discount Demand</th>
                                <th>Demand In</th>
                                <th>Final Demand</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    <td>{{$item['inventory']->title}}</td>
                                    <td>{{manageAmountFormat($item['total_quantity'])}}</td>
                                    <td>{{manageAmountFormat($item['total_cost'])}}</td>
                                    <td>{{manageAmountFormat($item['discount_value'])}} {{$item['type'] == 'Value' ? ' KSH' : '%' }}</td>
                                    <td>{{$item['type'] == 'Value' ? 'Sold Qty * '.$item['discount_value'] : '% of Total Cost' }}</td>
                                    <td>{{manageAmountFormat($item['discount'])}}</td>
                                </tr>
                            @endforeach
                            
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

    </section>
@endsection

@section('uniquepagescript')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />

 <style type="text/css">
 .select2{
     width: 100% !important;
    }
    </style>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    $('.search_select').select2();
</script>
@endsection
