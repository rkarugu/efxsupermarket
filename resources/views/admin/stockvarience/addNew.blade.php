
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
                {!! Form::open(['route' => 'admin.stock-variance.add','method'=>'post', 'id'=>'report-form']) !!}

                <div>
                    <div class="col-md-12 no-padding-h">
                        <div class="col-sm-3"><label>Start Date  </label> </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::text('start_date', null, [
                                'class'=>'datepicker form-control',
                                'placeholder'=>'Start Date' ,'readonly'=>true]) !!}
                            </div>
                        </div>

                        <div class="col-sm-3"><label>End Date  </label> </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::text('end_date', null, [
                                'class'=>'datepicker form-control',
                                'placeholder'=>'End Date','readonly'=>true]) !!}
                            </div>
                        </div>
                    </div>
                        <div class="col-md-12 no-padding-h">
                            <div class="col-sm-3"><label> For Inventory in Location  </label> </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!!Form::select('location', getStoreLocationDropdown(), null, ['placeholder'=>'All', 'class' => 'form-control mlselec6t'])!!}
                                </div>
                            </div>
                        {{-- </div> --}}
                        
                        {{-- <div class="row"> --}}
                            <div class="col-sm-3"><label>Inventory Categories </label> </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::select('wa_inventory_category_id[]', ['-1'=>'All']+getInventoryCategoryList(),null, ['required'=>true, 'class'=>'form-control mlselec6t', 'multiple'=>'multiple']) !!} 
                                </div>
                            </div>
                            </div>
                    <div class="col-md-12 no-padding-h">
                        <div class="col-sm-6">
                                <button title="Filter" type="submit" class="btn btn-primary" name="filter" value="filter" >
                                    Filter
                                </button>

                            {{-- <button title="Export In Excel" type="submit" class="btn btn-warning" name="manage_request" value="xls">
                                <i class="fa fa-file-excel" aria-hidden="true"></i>
                            </button>

                            <button title="PDF" type="submit" class="btn btn-warning" name="manage_request" value="pdf"  >
                                <i class="fa fa-file-pdf" aria-hidden="true"></i>
                            </button>

                            <button title="Print" type="button" class="btn btn-warning" name="manage_request" value="print"  onclick="printFile()">
                                <i class="fa fa-print" aria-hidden="true"></i>
                            </button> --}}

                            {{-- <a class="btn btn-info" href="{!! route('inventory-reports.location-wise-movement') !!}"  >Clear</a> --}}
                        </div>
                    </div>
                </div>

                </form>
            </div>

            <br>
            @include('message')
<form action="{{route('admin.stock-variance.create')}}" method="POST" >
{{csrf_field()}}
@php
    $categories = '';
    if(isset($_POST['wa_inventory_category_id']) && $_POST['wa_inventory_category_id'][0] == '-1'){
        $categories .= 'All';
    }else {
        if(count($data) > 0){
            foreach($data as $key => $val)
            {
                $categories .= $val['category_code'].', ';
            }
        }
    }
@endphp
<input type="hidden" value="{{$categories}}" name="categories">
<input type="hidden" value="{{$start_date ?? NULL}}" name="start_date">
<input type="hidden" value="{{$end_date ?? NULL}}" name="end_date">
<input type="hidden" value="{{$locationname ?? NULL}}" name="location">

<table class="table table-bordered table-responsive" style=" margin-top:40px;">
    
@if(count($data) > 0)
    
      
    <thead>
        <tr>
            <th colspan="6">{{$restuarantname}}</th>
        </tr>
        <tr style="border:1px solid #ddd;">
            <th colspan="6">Inventory Valuation for Categories between and at {{ ($locationname!="") ? $locationname : "All" }}</th>
        </tr>
        <tr>
            <th colspan="2">Category/Items</th>
            <th>UOM</th>
            <th>Opening Stock</th>
            <th>Purchases</th>
            <th>Transfers</th>
            <th>Issues</th>
            <th>Total</th>
            <th>Closing Stock</th>
            <th width='80px' style="width:80px">Potential Sales</th>
            <th>Actual Sales</th>
            <th width='50px' style="width:50px">Variance</th>
            
        </tr>
        </thead>
        <tbody>
             @php 
                $grandtotalcost = 0;

                $ostotal = 0;
                $ptotal = 0;
                $ttotal = 0;
                $itotal = 0;
                $stotal = 0;
                $totalOftotal = 0;
             


            @endphp
            @foreach($data as $key => $val)
            <tr>
                
                <th scope="row">{{$val['category_code']}}
                <input type="hidden" name="category_code[]" value="{{$val['category_code']}}">
                </th>
                <th>
                    {{$val['category_description']}}
                    <input type="hidden" name="category_description[]" value="{{$val['category_description']}}">
                </th>
                <th colspan="3"></th>
            </tr>
             @php 
             $totalqty = 0;
             $totalcost = 0;
             $parentKey = $key;
             @endphp
                @foreach($val['getinventoryitems'] as $key => $value)
             @php 
            $totalqty += $storeBiseQty[$value['getstockmoves']['id']];
             $totalcost += $value['getstockmoves']['price'] * $storeBiseQty[$value['getstockmoves']['id']];
             @endphp

                <tr>
                    <td scope="row">
                        {{$value['stock_id_code']}}
                        <input type="hidden" name="stock_id_code[{{$parentKey}}][]" value="{{$value['stock_id_code']}}">
                    </td>
                    <td>
                        {{$value['title']}}
                        <input type="hidden" name="title[{{$parentKey}}][]" value="{{$value['title']}}">
                    </td>
                    <td>
                        {{$value['unitofmeasures']['title']}}
                        <input type="hidden" name="unitofmeasures[{{$parentKey}}][]" value="{{$value['unitofmeasures']['title']}}">
                        </td>
                   
                    <td><input type="hidden" name="storeBiseQty[{{$parentKey}}][]" class="storeBiseQty" value="{{($storeBiseQty[$value['getstockmoves']['id']])}}">
                        {{($storeBiseQty[$value['getstockmoves']['id']])}}</td>
                    <td>
                        <input type="hidden" name="purchaseBiseQty[{{$parentKey}}][]" class="purchaseBiseQty" value="{{($purchaseBiseQty[$value['getstockmoves']['id']])}}">
                        {{($purchaseBiseQty[$value['getstockmoves']['id']])}}</td>
                    <td>
                        <input type="hidden" name="transfersBiseQty[{{$parentKey}}][]" class="transfersBiseQty" value="{{($transfersBiseQty[$value['getstockmoves']['id']])}}"> 
                        {{($transfersBiseQty[$value['getstockmoves']['id']])}}</td>
                    <td>
                        <input type="hidden" name="issuesBiseQty[{{$parentKey}}][]" class="issuesBiseQty" value="{{($issuesBiseQty[$value['getstockmoves']['id']])}}"> 
                        {{($issuesBiseQty[$value['getstockmoves']['id']])}}</td>
                    @php
                        $total = 0;
                        $total = ($storeBiseQty[$value['getstockmoves']['id']]) + ($purchaseBiseQty[$value['getstockmoves']['id']])
                        + ($transfersBiseQty[$value['getstockmoves']['id']]) - ($issuesBiseQty[$value['getstockmoves']['id']]);
                    @endphp
                    <td>
                        <input type="hidden" name="total[{{$parentKey}}][]" class="total" value="{{$total}}"> 
                        {{$total}}</td>
                    <td><input type="numeric" name="closing_stock[{{$parentKey}}][]" class="closing_stock" value="0"></td>
                    <td><input type="numeric" style="border: 0px solid" name="potential_stock[{{$parentKey}}][]" class="potential_stock" value="0" readonly></td>
                    <td>
                        <input type="hidden" name="salesBiseQty[{{$parentKey}}][]" class="salesBiseQty" value="{{abs($salesBiseQty[$value['getstockmoves']['id']])}}"> 
                        {{abs($salesBiseQty[$value['getstockmoves']['id']])}}</td>
               
                    <td><input type="numeric" style="border: 0px solid" name="variance[{{$parentKey}}][]" class="variance" value="0" readonly></td>
               

                </tr>
                @php
                     $ostotal += ($storeBiseQty[$value['getstockmoves']['id']]);                

                     $ptotal += ($purchaseBiseQty[$value['getstockmoves']['id']]);
                     $ttotal += (($transfersBiseQty[$value['getstockmoves']['id']])); 
                     $itotal += ($issuesBiseQty[$value['getstockmoves']['id']]); 
                     $stotal += abs($salesBiseQty[$value['getstockmoves']['id']]);      
                 
                     $totalOftotal += $total;                                                                              
                     


                @endphp
                @endforeach
             @php 
             $grandtotalcost += $totalcost;
             @endphp
            @endforeach
            <tr>
                <th colspan="6"></th>
            </tr>            

            <tr>
                <th colspan="3">Grand Total </th>
                <th>{{number_format(($ostotal),2)}}</th>
                <th>{{number_format(($ptotal),2)}}</th>
                <th>{{number_format($ttotal,2)}}</th>
                <th>{{number_format($itotal,2)}}</th>
                <th>{{number_format($totalOftotal,2)}}</th>
                <th id="closingstock">0.00</th>
                <th id="potentialstock">0.00</th>
                <th>{{number_format($stotal,2)}}</th>
                <th id="variance">0.00</th>
               
            </tr>

            <tr>
                <th colspan="10"></th>
                
                <th>
                    <button type="button" id="calculateAll" class="btn btn-danger">Calculate</button>
                </th>
                <th>
                    <button type="submit" class="btn btn-danger" id="save_report" >Save Report</button>
                </th>
               
            </tr>
        </tbody>
       
@endif        
        
</table>        
</form>
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
<script>
function printFile() {
    
    var isconfirmed=confirm("Do you want to print?");
    if (isconfirmed) 
    {
        var frm = $('#report-form');
        data_form = frm.serialize();
        jQuery.ajax({
          url: '{{route('inventory-reports.location-wise-movement')}}',
          type: 'POST',
          async:false,
          data:data_form,
          headers: {
             'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            },
        success: function (response) {
            var divContents = response;
            var printWindow = window.open('', '', 'width=600');
            printWindow.document.write('<html><head><title>Receipt</title>');
            printWindow.document.write('</head><body >');
            printWindow.document.write(divContents);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }
      });
    }
}

</script>
<script type="text/javascript">
    $(function () {
        $(".mlselec6t").select2();
    });

    $('.datepicker').datetimepicker({
    format: 'yyyy-mm-dd hh:ii:00',
    minuteStep:1,
    });

    $('#calculateAll').click(function(e){
        e.preventDefault();
        var closingstock = 0;
        var potentialstock = 0;
        var variance = 0;
        $('.closing_stock').each(function(key,val){
            var myval = $(val).val();
            var total = $(val).parents('tr').find('.total').val();
            $(val).parents('tr').find('.potential_stock').val((parseFloat(total) - parseFloat(myval)).toFixed(2));

            var salesBiseQty = $(val).parents('tr').find('.salesBiseQty').val();
            var potential_stock = $(val).parents('tr').find('.potential_stock').val();
            $(val).parents('tr').find('.variance').val((parseFloat(potential_stock) - parseFloat(salesBiseQty)).toFixed(2));

            closingstock = parseFloat(closingstock) + parseFloat(myval);
            potentialstock = parseFloat(potentialstock) + (parseFloat(total) - parseFloat(myval));
            variance = parseFloat(variance) + (parseFloat(potential_stock) - parseFloat(salesBiseQty));
        });
        $('#closingstock').html(parseFloat(closingstock).toFixed(2));
        $('#potentialstock').html(parseFloat(potentialstock).toFixed(2));
        $('#variance').html(parseFloat(variance).toFixed(2));
    });
    $('#save_report').click(function(e){
        e.preventDefault();
        var closingstock = 0;
        var potentialstock = 0;
        var variance = 0;
        $('.closing_stock').each(function(key,val){
            var myval = $(val).val();
            var total = $(val).parents('tr').find('.total').val();
           
            $(val).parents('tr').find('.potential_stock').val((parseFloat(total) - parseFloat(myval)).toFixed(2));

            var salesBiseQty = $(val).parents('tr').find('.salesBiseQty').val();
            var potential_stock = $(val).parents('tr').find('.potential_stock').val();
            $(val).parents('tr').find('.variance').val((parseFloat(potential_stock) - parseFloat(salesBiseQty)).toFixed(2));

            closingstock = parseFloat(closingstock) + parseFloat(myval);
            potentialstock = parseFloat(potentialstock) + (parseFloat(total) - parseFloat(myval));
            variance = parseFloat(variance) + (parseFloat(potential_stock) - parseFloat(salesBiseQty));
            // console.log(key+' = '+myval);
           
        });
        $('#closingstock').html(parseFloat(closingstock).toFixed(2));
        $('#potentialstock').html(parseFloat(potentialstock).toFixed(2));
        $('#variance').html(parseFloat(variance).toFixed(2));
        setTimeout(() => {
            $(this).parents('form').submit();
        }, 500);
    });
</script>
@endsection
