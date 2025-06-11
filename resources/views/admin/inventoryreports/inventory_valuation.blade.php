@extends('layouts.admin.admin')

@section('content')


<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="box box-primary">
        <div class="box-header with-border no-padding-h-b">
            <div  style="height: 150px ! important;"> 
                <div class="card-header">
                    <i class="fa fa-filter"></i> Filter
                </div><br>
                {!! Form::open(['route' => 'inventory-reports.inventory-valuation-report','method'=>'post']) !!}

                <div>
                    <div class="col-md-12 no-padding-h">
                       {{-- <div class="row">
                            <div class="col-sm-3"><label>Select period from  </label> </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::text('start-date', null, [
                                    'class'=>'datepicker form-control',
                                    'placeholder'=>'Select period from' ,'readonly'=>true, 'required'=> true]) !!}
                                </div>
                            </div>
                        </div> --}}
                        <div class="row">
                            <div class="col-sm-3"><label>As At  </label> </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::text('end-date', null, [
                                    'class'=>'datepicker form-control',
                                    'placeholder'=>'As At' ,'readonly'=>true, 'required'=> true]) !!}
                                </div>
                            </div>
                        {{-- </div> --}}
                        {{-- <div class="row"> --}}
                            <div class="col-sm-3"><label>For Inventory in Location </label> </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                {!!Form::select('location', getStoreLocationDropdown(), null, ['placeholder'=>'All', 'class' => 'location form-control'])!!}
                                     
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label> Inventory Categories </label> 
                                    {!! Form::select('wa_inventory_category_id[]', [],null, ['required'=>true, 'class'=>'wa_inventory_category_id form-control mlselec6t', 'multiple'=>'multiple']) !!}
                                </div>
                            </div>
                        {{-- </div> --}}
                        
                        {{-- <div class="row"> --}}
                            <div class="col-sm-4">
                                <?php 
                                $summary_detailed_arr = [
                                    1=> 'Summary Report',  
                                    2=> 'Detailed Report',  
                                ];
                                
                                ?>
                                <div class="form-group">
                                <label> Summary or Detailed Report  </label>
                                    {!!Form::select('show_type', $summary_detailed_arr, null, ['class' => 'form-control'])!!}
                                </div>
                            </div>
                            <div class="form-group col-sm-4">
                                <div class="form-group">
                                    <label for="inputEmail3" class="control-label">Bin Location:</label>
                                    {!!Form::select('wa_unit_of_measure_id', [], null, ['id'=>'wa_unit_of_measure_id','class' => 'form-control wa_unit_of_measure_id','required'=>true,'placeholder' => 'Please select'])!!} 
                                </div>
                            </div>
                        {{-- </div> --}}
                        
                        {{-- <div class="col-md-12 no-padding-h"> --}}
                            <div class="col-sm-12">
                                <button title="Filter" type="submit" class="btn btn-primary" name="filter" value="filter" >
                                    Filter
                                </button>

                                <button title="Export In Excel" type="submit" class="btn btn-warning" name="manage-request" value="xls"  >
                                    <i class="fa fa-file-excel" aria-hidden="true"></i>
                                </button>

                                <button title="PDF" type="submit" class="btn btn-warning" name="manage-request" value="pdf"  >
                                    <i class="fa fa-file-pdf" aria-hidden="true"></i>
                                </button>
                                <a class="btn btn-info" href="{!! route('purchases-by-family-group') !!}"  >Clear</a>

                            </div>
                        </div>


<table class="table table-responsive" style="border:1px solid #ddd; margin-top:40px;">

@if(count($data) > 0)
    @if($summrytype=="2")        
    <thead>
        <tr>
            <th colspan="6">{{$restuarantname}}</th>
        </tr>
        <tr style="border:1px solid #ddd;">
            <th colspan="6">Inventory Valuation for Categories between and at {{ ($locationname!="") ? $locationname : "All" }}</th>
        </tr>
        <tr>
            <th>Category/Items</th>
            <th></th>
            <th>Quantity</th>
            <th>Unit</th>
            <th>Standard Cost</th>
            <th>Total Value</th>
        </tr>
        </thead>
        <tbody>
             @php 
                $grandtotalcost = 0;
             @endphp
            @foreach($data as $key => $val)
            <tr>
                <th scope="row">{{$val['category_code']}}</th>
                <th>{{$val['category_description']}}</th>
                <th colspan="3"></th>
            </tr>
             @php 
             $totalqty = 0;
             $totalcost = 0;
             @endphp
                @foreach($val['getinventoryitems'] as $key => $value)
             @php 
            $totalqty += $storeBiseQty[$value['getstockmoves']['id']];
             $totalcost += $value['selling_price'] * $storeBiseQty[$value['getstockmoves']['id']];
             @endphp
			 {{--	@if($storeBiseQty[$value['getstockmoves']['id']]!=0) --}}
                <tr>
                    <td scope="row">{{$value['stock_id_code']}}</td>
                    <td>{{$value['title']}}</td>
                    <td>{{$storeBiseQty[$value['getstockmoves']['id']]}}</td>
                    <td>{{$value['unitofmeasures']['title']}}</td>
                    <td>{{$value['selling_price']}}</td>
                    <td>{{ number_format($value['selling_price'] * $storeBiseQty[$value['getstockmoves']['id']],2) }}</td>
                </tr>
                {{-- @endif --}}
                @endforeach
             @php 
             $grandtotalcost += $totalcost;
             @endphp
                <tr>
                <th colspan="2">Total &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; for  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {{$val['category_code']}}  - {{$val['category_description']}}</th>
                <th colspan="3"></th>
                <th>{{number_format($totalcost,2)}}</th>
            </tr>
                <tr>
                    <th colspan="6"></th>
                </tr>

            @endforeach
            <tr>
                <th colspan="6"></th>
            </tr>            

            <tr>
                <th colspan="2">Grand Total &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </th>
                <th colspan="3"></th>
                <th>{{number_format($grandtotalcost,2)}}</th>
            </tr>
        </tbody>
        @else
    <thead>
        <tr>
            <th colspan="6">{{$restuarantname}}</th>
        </tr>
        <tr style="border:1px solid #ddd;">
            <th colspan="6">Inventory Valuation for Categories between and at {{ ($locationname!="") ? $locationname : "All" }}</th>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Total Value</th>
        </tr>
        </thead>
        <tbody>
            @php 
                $grandtotal = 0;
            @endphp
            @foreach($data as $key => $val)
                @php 
                    $totalqty = 0;
                    $totalcost = 0;
                @endphp
            @foreach($val['getinventoryitems'] as $key => $value)
                @php 
                $totalqty += $storeBiseQty[$value['getstockmoves']['id']];
                $totalcost += $value['selling_price'] * $storeBiseQty[$value['getstockmoves']['id']];
                @endphp
            @endforeach
{{--			@if($totalqty!=0) --}}
            <tr>
                <th>{{$val['category_code']}}</th>
                <th>-</th>
                <th>{{$val['category_description']}}</th>
                <th>{{$totalqty}}</th>
                <th>{{number_format($totalcost,2)}}</th>
            @php 
                $grandtotal += $totalcost;
            @endphp                
            </tr>
          {{--  @endif --}}
            @endforeach
            <tr>
                <th></th>
                <th colspan="3">Total Cost</th>
                <th>{{number_format($grandtotal,2)}}</th>
            </tr>

        </tbody>
        @endif
 @else       
<center><h4>Data not found.</h4></center>
        
@endif        
        
</table>        

                            
                        


                    </div>


                </div>

                </form>
            </div>

            <br>



        </div>
    </div>
</section>



@endsection


@section('uniquepagestyle')
 <link rel="stylesheet" href="{{asset('assets/admin/dist/bootstrap-datetimepicker.min.css')}}">
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/dist/bootstrap-datetimepicker.js')}}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    $(function () {
        $(".mlselec6t").select2();
        fun_location();
    });
    function fun_location() { 
        $this = $('.location');
        var rec = '<option value="-1" selected="selected">All</option>';
                   $('.wa_inventory_category_id').html(rec);
        $.ajax({
           type: "GET",
           url: "{{route('admin.stock-takes.getCategories')}}",           
           data: {
            'wa_location_and_store_id':$this.val(),
            'selectedCategory' : '{{(isset($request["wa_inventory_category_id"]) && count($request["wa_inventory_category_id"])>0) ? (implode(',',$request["wa_inventory_category_id"])) : ''}}',
            'selectedUNit' : '{{(isset($request["wa_unit_of_measure_id"])) ? $request["wa_unit_of_measure_id"] : ''}}'
           },
           success: function (response) {
               if(response.result == 1){
                   $('.wa_inventory_category_id').select2('destroy');
                   var rec = '<option value="-1" selected="selected">All</option>';
                   $('.wa_inventory_category_id').html(rec+response.data);
                   $('.wa_inventory_category_id').select2();


                //    $('.wa_unit_of_measure_id').select2('destroy');
                   $('.wa_unit_of_measure_id').html(response.unit);
                //    $('.wa_unit_of_measure_id').select2();
                //updateTableForm();
                }
            // console.log(('<option selected disabled>Please Select</option>').length);
           }
       });
     }
    $('.location').change(function(e){
       e.preventDefault();
       fun_location();
    });
                 $('.datepicker').datetimepicker({
                  format: 'yyyy-mm-dd hh:ii:00',
                  minuteStep:1,
                 });
</script>
@endsection
