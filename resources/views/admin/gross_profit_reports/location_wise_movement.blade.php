
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
                {!! Form::open(['route' => 'inventory-reports.location-wise-movement','method'=>'post', 'id'=>'report-form']) !!}

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
                                    {!!Form::select('location', getStoreLocationDropdown(), null, ['placeholder'=>'All', 'class' => 'form-control'])!!}
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

                            <button title="Export In Excel" type="submit" class="btn btn-warning" name="manage_request" value="xls">
                                <i class="fa fa-file-excel" aria-hidden="true"></i>
                            </button>

                            <button title="PDF" type="submit" class="btn btn-warning" name="manage_request" value="pdf"  >
                                <i class="fa fa-file-pdf" aria-hidden="true"></i>
                            </button>

                            <button title="Print" type="button" class="btn btn-warning" name="manage_request" value="print"  onclick="printFile()">
                                <i class="fa fa-print" aria-hidden="true"></i>
                            </button>

                            <a class="btn btn-info" href="{!! route('inventory-reports.location-wise-movement') !!}"  >Clear</a>
                        </div>
                    </div>
                </div>

                </form>
            </div>

            <br>
            @include('message')

<table class="table table-responsive" style="border:1px solid #ddd; margin-top:40px;">

@if(count($data) > 0)
      
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
            <th>UOM</th>
            <th>Opening Stock</th>
            <th>Purchases</th>
            <th>Transfers</th>
            <th>Issues</th>
            <th>Sales</th>
            <th>Closing Balance</th>
            <th>Standard Cost</th>
            <th>Closing Value</th>
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
                $cbtotal = 0;
                $sctotal = 0;
                $cvtotal = 0;


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
             $totalcost += $value['getstockmoves']['price'] * $storeBiseQty[$value['getstockmoves']['id']];
             @endphp

                <tr>
                    <td scope="row">{{$value['stock_id_code']}}</td>
                    <td>{{$value['title']}}</td>
                    <td>{{$value['unitofmeasures']['title']}}</td>
                   
                    <td>{{abs($storeBiseQty[$value['getstockmoves']['id']])}}</td>
                    <td>{{abs($purchaseBiseQty[$value['getstockmoves']['id']])}}</td>
                    <td>{{$transfersBiseQty[$value['getstockmoves']['id']]}}</td>
                    <td>{{$issuesBiseQty[$value['getstockmoves']['id']]}}</td>
                    <td>{{$salesBiseQty[$value['getstockmoves']['id']]}}</td>
                    @php
					  $cs = abs($storeBiseQty[$value['getstockmoves']['id']] + $purchaseBiseQty[$value['getstockmoves']['id']] + ($transfersBiseQty[$value['getstockmoves']['id']] + $issuesBiseQty[$value['getstockmoves']['id']] ) - $salesBiseQty[$value['getstockmoves']['id']]);
                      $closing_stock = number_format($cs,2);
                    @endphp
                  
                  <td>{{ $closing_stock }}</td>
                  <td>{{number_format($value['getstockmoves']['standard_cost'],2)}}</td>
                    <td>{{ (number_format($value['getstockmoves']['standard_cost'] * $cs,2)) }}</td>

                </tr>
                @php
                     $ostotal += $storeBiseQty[$value['getstockmoves']['id']];                

                     $ptotal += $purchaseBiseQty[$value['getstockmoves']['id']];
                     $ttotal += $transfersBiseQty[$value['getstockmoves']['id']]; 
                     $itotal += $issuesBiseQty[$value['getstockmoves']['id']]; 
                     $stotal += $salesBiseQty[$value['getstockmoves']['id']];      
                     $cbtotal += $cs; 
                     $sctotal += $value['getstockmoves']['standard_cost']; 
                     $cvtotal += ($value['getstockmoves']['standard_cost'] * $cs); 
                                                                                                                    
                     


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
                <th>{{number_format(abs($ostotal),2)}}</th>
                <th>{{number_format(abs($ptotal),2)}}</th>
                <th>{{number_format($ttotal,2)}}</th>
                <th>{{number_format($itotal,2)}}</th>
                <th>{{number_format($stotal,2)}}</th>
                <th>{{number_format(abs($cbtotal),2)}}</th>
                <th>{{number_format(abs($sctotal),2)}}</th>
                <th>{{number_format(abs($cvtotal),2)}}</th>
            </tr>
        </tbody>
       
@endif        
        
</table>        

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
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></
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
</script>
@endsection


