@extends('layouts.admin.admin')
@section('content')

<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3>
           
        </div>
         @include('message')
         <div class="box-body" style="padding-bottom:15px">
            <form action="" method="get">
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                    <label for="">From</label>
                    <input type="date" name="start_date" id="start-date" class="form-control" value="{{request()->input('start_date') ?? date('Y-m-d')}}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                    <label for="">To</label>
                    <input type="date" name="to_date" id="end-date" class="form-control"  value="{{request()->input('to_date') ?? date('Y-m-d')}}">
                    </div>
                </div>
             
                
                <div class="col-md-2">
                    <label>For Inventory in Location </label>
                        <select name="location" id="location_id" class='form-control' value="{{request()->input('location')}}">
                            <option value="-1" selected>Show All</option>
                            @php
                                $collection = getStoreLocationDropdownLimitByIds([6,20,21,22]);
                            @endphp
                            @foreach ($collection as $key => $item)
                            <option value="{{$key}}" {{isset($_GET['location']) && $_GET['location'] == $key ? 'selected' : ''}}>{{$item}}</option>
                            @endforeach
                        </select>
                </div>


                <div class="col-md-2">
                    <label>INV/RTN filter </label>
                        <select name="invoice"  class='form-control'>
                            <option value="CS-" {{isset($_GET['invoice']) && $_GET['invoice'] == 'CS-' ? 'selected' : ''}}>Cash Sale</option>
                            <option value="INV-" {{isset($_GET['invoice']) && $_GET['invoice'] == 'INV-' ? 'selected' : ''}}>Invoice</option>
                            <option value="RTN-" {{isset($_GET['invoice']) && $_GET['invoice'] == 'RTN-' ? 'selected' : ''}}>Return</option>
                           
                        </select>
                </div>
                
                <div class="col-md-2">  
                    <div class="form-group">
                    <br>                   
                    <button type="submit" name="manage" value="pdf" ><i class="fa fa-file-pdf" style="font-size:30px;color:red"></i></i></button>
                    </div>                           
                </div>
                
                <div class="col-md-2">
                    <div class="form-group">
                    <button type="submit" id="filter" class="btn btn-primary btn-sm" style="margin-top: 25px;">Filter</button>
                    </div>
                </div>
            </div>
            </form>
         <div class="table-responsive">
            <table class="table table-responsive" style="border:1px solid #ddd; margin-top:40px;">

    @if(count($data) > 0)
    
    <thead>
        <tr>
            <th colspan="6"></th>
        </tr>
        
        <tr>
            
            <th>Category</th>
            
            <th>Total Sale</th>
            <th>Total Cost</th>
            <th>Gross Profit</th>

        </tr>
        </thead>
        <tbody>
            @php 
                $grandtotal = 0;
                $grandprice = 0;
                $grandprofit =0;
            @endphp
            @foreach($data as $key => $val)
            <tr>
                
                <th>{{$val->category_description}}</th>
               @php
                $total_cost = $val->standard_cost_sum;
                $grandtotal  += $total_cost;
                $grandprice  += $val->price_sum;
                $grandprofit += $val->price_sum + $total_cost;
               @endphp
                <th>{{number_format($val->price_sum,2)}}</th>
                <th>{{number_format(abs($total_cost),2)}}</th>
                <th>{{number_format($val->price_sum + $total_cost,2)}}</th>
            </tr>

                @foreach($val['sub_items'] as $item)
                    <tr>
                
                        <td>{{@$item['title'] .'-'. @$item['stock_id_code'] }}</td>
                       
                        <td>{{number_format(@$item['price'],2)}}</td>
                        <td>{{number_format(abs($item['standard_cost']),2)}}</td>
                        <td>{{number_format(@$item['price'] - $item['standard_cost'],2)}}</td>
                    </tr>
                @endforeach
            @endforeach
            <tr>
                <th></th>
              
                <th>{{number_format($grandprice,2)}}</th>
                <th>{{number_format(abs($grandtotal),2)}}</th>
                <th>{{number_format($grandprofit,2)}}</th>
            </tr>

        </tbody>
        @endif
       
        
</table> 
           
             
         </div>
         </div>
    </div>
</section>
@endsection

@section('uniquepagescript')
<script type="text/javascript">
    var location_id = function(){
            $("#location_id").select2();
        };
        location_id();
</script>
@endsection

